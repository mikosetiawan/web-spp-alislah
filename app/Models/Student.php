<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'name',
        'email',
        'phone',
        'address',
        'class_id',
        'gender',
        'birth_date',
        'birth_place',
        'photo',
        'status',
        'parent_name',
        'parent_phone'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $appends = ['unpaid_months', 'full_name', 'initials'];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activePayments()
    {
        return $this->payments()->where('status', 'paid');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ($this->gender === 'L' ? ' Sdn' : ' Sdri');
    }

    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
            if (strlen($initials) >= 2)
                break;
        }

        return $initials;
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        return $query->where('name', 'like', $term)
            ->orWhere('nis', 'like', $term)
            ->orWhere('email', 'like', $term)
            ->orWhereHas('class', function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('major', 'like', $term);
            });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getPaymentStatusAttribute()
    {
        $currentMonth = now()->format('Y-m');
        $payment = $this->payments()->where('month', $currentMonth)->first();

        return $payment ? $payment->status : 'unpaid';
    }

    public function scopeWithPaymentStatus($query, $status)
    {
        return $query->whereHas('payments', function ($q) use ($status) {
            $q->where('status', $status)
                ->where('month', now()->format('Y-m'));
        }, $status === 'unpaid' ? '=' : '>', 0);
    }

    public function getUnpaidMonthsAttribute()
    {
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->format('m');

        // Get active academic year
        $academicYear = request()->input('academic_year') ?? $this->getCurrentAcademicYear();
        Log::info("Checking unpaid months for student {$this->id} in academic year {$academicYear}");

        if (!$this->class) {
            Log::warning("Student {$this->id} has no class assigned");
            return collect();
        }

        // Get all paid months
        $paidMonths = $this->payments()
            ->where('status', 'paid')
            ->get()
            ->map(function ($payment) {
                return Carbon::parse($payment->month)->format('Y-m');
            })
            ->toArray();

        // Get SPP costs for this academic year
        $sppCosts = SppCost::getCostForAcademicYear($this->class_id, $academicYear);

        if ($sppCosts->isEmpty()) {
            Log::warning("No SPP costs found for class {$this->class_id} in academic year {$academicYear}");
            return collect();
        }

        $unpaidMonths = collect();
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Split academic year into start and end years
        [$startYear, $endYear] = explode('/', $academicYear);

        // Academic year months: July (start year) to June (end year)
        $academicMonths = [
            ['year' => $startYear, 'months' => ['07', '08', '09', '10', '11', '12']],
            ['year' => $endYear, 'months' => ['01', '02', '03', '04', '05', '06']]
        ];

        foreach ($academicMonths as $period) {
            $year = $period['year'];
            $monthList = $period['months'];

            foreach ($monthList as $month) {
                // Skip future months in current academic year
                if ($year == $currentYear && $month > $currentMonth) {
                    continue;
                }

                $monthKey = $month;
                $monthValue = $months[$monthKey] ?? 'Unknown';
                $fullMonth = "$year-$monthKey";

                // Check if this month is unpaid
                if (!in_array($fullMonth, $paidMonths)) {
                    $sppCost = $sppCosts->firstWhere('year', $year);
                    if (!$sppCost) {
                        Log::warning("No SPP cost found for year {$year} in academic year {$academicYear}");
                        continue;
                    }

                    $dueDate = Carbon::create($year, $month, 10); // Due date is 10th of each month
                    $isOverdue = $currentDate->greaterThan($dueDate);

                    $unpaidMonths->push([
                        'month' => $fullMonth,
                        'month_name' => "$monthValue $year",
                        'amount' => $sppCost->amount,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'is_overdue' => $isOverdue,
                        'spp_cost_id' => $sppCost->id,
                        'year' => $year,
                        'academic_year' => $academicYear
                    ]);
                }
            }
        }

        // Sort by month (oldest first)
        return $unpaidMonths->sortBy('month')->values();
    }

    /**
     * Get current academic year based on system date
     * Format: YYYY/YYYY (e.g. 2023/2024)
     */
    public static function getCurrentAcademicYear()
    {
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Academic year runs from July to June
        if ($currentMonth >= 7) { // July or later
            return $currentYear . '/' . ($currentYear + 1);
        } else { // January to June
            return ($currentYear - 1) . '/' . $currentYear;
        }
    }

    /**
     * Get list of academic years for selection
     */
    public static function getAcademicYearOptions($yearsBack = 5, $yearsForward = 1)
    {
        $currentAcademicYear = self::getCurrentAcademicYear();
        [$startYear, $endYear] = explode('/', $currentAcademicYear);

        $options = [];

        // Previous years
        for ($i = $yearsBack; $i >= 1; $i--) {
            $options[] = ($startYear - $i) . '/' . ($endYear - $i);
        }

        // Current and future years
        for ($i = 0; $i <= $yearsForward; $i++) {
            $options[] = ($startYear + $i) . '/' . ($endYear + $i);
        }

        return $options;
    }

    public function sppBills()
    {
        return $this->hasMany(SppBill::class);
    }
}
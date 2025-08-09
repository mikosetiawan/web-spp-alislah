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

    public function unpaid_months()
    {
        return $this->sppBills()
            ->where('status', 'unpaid') // Hanya tagihan yang belum dibayar
            ->where('academic_year', $this->getCurrentAcademicYear())
            ->get()
            ->map(function ($bill) {
                return [
                    'month' => $bill->month,
                    'year' => $bill->year,
                    'month_name' => Carbon::create($bill->year, $bill->month, 1)->translatedFormat('F'),
                    'amount' => $bill->amount,
                    'spp_cost_id' => $bill->spp_cost_id,
                    'academic_year' => $bill->academic_year,
                    'is_overdue' => Carbon::now()->year > $bill->year ||
                        (Carbon::now()->year == $bill->year && Carbon::now()->month > $bill->month)
                ];
            });
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
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $unpaidBills = $this->sppBills()
            ->where('academic_year', $currentAcademicYear)
            ->where('status', 'unpaid')
            ->exists();

        return $unpaidBills ? 'unpaid' : 'paid';
    }

    public function scopeWithPaymentStatus($query, $status)
    {
        return $query->whereHas('payments', function ($q) use ($status) {
            $q->where('status', $status)
                ->where('month', now()->format('Y-m'));
        }, $status === 'unpaid' ? '=' : '>', 0);
    }


    // public function getUnpaidMonthsAttribute()
    // {
    //     try {
    //         $academicYear = request()->input('academic_year') ?? $this->getCurrentAcademicYear();

    //         if (!preg_match('/^\d{4}\/\d{4}$/', $academicYear)) {
    //             $academicYear = $this->getCurrentAcademicYear();
    //         }

    //         [$startYear, $endYear] = explode('/', $academicYear);

    //         // Query untuk bulan yang belum dibayar
    //         $paidMonths = $this->payments()
    //             ->where(function ($q) use ($startYear, $endYear) {
    //                 $q->whereYear('month', $startYear)->whereMonth('month', '>=', 7)
    //                     ->orWhereYear('month', $endYear)->whereMonth('month', '<=', 6);
    //             })
    //             ->pluck('month')
    //             ->map(function ($date) {
    //                 return Carbon::parse($date)->format('Y-m');
    //             })
    //             ->toArray();

    //         // Generate semua bulan dalam tahun ajaran
    //         $allMonths = [];
    //         for ($year = $startYear; $year <= $endYear; $year++) {
    //             $startMonth = ($year == $startYear) ? 7 : 1;
    //             $endMonth = ($year == $endYear) ? 6 : 12;

    //             for ($month = $startMonth; $month <= $endMonth; $month++) {
    //                 $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
    //                 $allMonths[] = "$year-$monthKey";
    //             }
    //         }

    //         // Filter bulan yang belum dibayar
    //         $unpaidMonths = array_diff($allMonths, $paidMonths);

    //         // Format output
    //         $monthNames = [
    //             '01' => 'Januari',
    //             '02' => 'Februari',
    //             '03' => 'Maret',
    //             '04' => 'April',
    //             '05' => 'Mei',
    //             '06' => 'Juni',
    //             '07' => 'Juli',
    //             '08' => 'Agustus',
    //             '09' => 'September',
    //             '10' => 'Oktober',
    //             '11' => 'November',
    //             '12' => 'Desember'
    //         ];

    //         $result = collect();
    //         foreach ($unpaidMonths as $month) {
    //             [$year, $monthNum] = explode('-', $month);
    //             $result->push([
    //                 'month' => $month,
    //                 'month_name' => $monthNames[$monthNum] . ' ' . $year,
    //                 'amount' => $this->class->sppCosts->where('year', $year)->first()->amount ?? 0,
    //                 'due_date' => Carbon::create($year, $monthNum, 10)->format('Y-m-d'),
    //                 'is_overdue' => now()->greaterThan(Carbon::create($year, $monthNum, 10))
    //             ]);
    //         }

    //         return $result->sortBy('month');

    //     } catch (\Exception $e) {
    //         logger()->error("Error in getUnpaidMonthsAttribute: " . $e->getMessage());
    //         return collect();
    //     }
    // }




    public function getUnpaidMonthsAttribute()
{
    try {
        $academicYear = $this->getCurrentAcademicYear();
        [$startYear, $endYear] = explode('/', $academicYear);

        // Ambil semua bulan yang sudah dibayar dari kedua sistem
        $paidMonths = $this->payments()
            ->where(function($q) use ($startYear, $endYear) {
                $q->whereYear('month', $startYear)->whereMonth('month', '>=', 7)
                  ->orWhereYear('month', $endYear)->whereMonth('month', '<=', 6);
            })
            ->get()
            ->map(function($payment) {
                return [
                    'month' => Carbon::parse($payment->month)->month,
                    'year' => Carbon::parse($payment->month)->year
                ];
            });

        $paidBills = $this->sppBills()
            ->where('status', 'paid')
            ->where(function($q) use ($startYear, $endYear) {
                $q->whereYear('year', $startYear)->where('month', '>=', 7)
                  ->orWhereYear('year', $endYear)->where('month', '<=', 6);
            })
            ->get()
            ->map(function($bill) {
                return [
                    'month' => $bill->month,
                    'year' => $bill->year
                ];
            });

        $allPaid = $paidMonths->merge($paidBills)->unique();

        // Generate semua bulan dalam tahun akademik
        $allMonths = collect();
        for ($year = (int)$startYear; $year <= (int)$endYear; $year++) {
            $startMonth = $year == (int)$startYear ? 7 : 1;
            $endMonth = $year == (int)$endYear ? 6 : 12;

            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $allMonths->push([
                    'month' => $month,
                    'year' => $year
                ]);
            }
        }

        // Filter bulan yang belum dibayar
        $unpaidMonths = $allMonths->reject(function($month) use ($allPaid) {
            return $allPaid->contains(function($paid) use ($month) {
                return $paid['month'] == $month['month'] && $paid['year'] == $month['year'];
            });
        });

        // Format output
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $unpaidMonths->map(function($month) use ($monthNames) {
            $isOverdue = now()->greaterThan(Carbon::create($month['year'], $month['month'], 10));
            $sppCost = $this->class->sppCosts->where('year', $month['year'])->first();
            
            return [
                'month' => $month['month'],
                'year' => $month['year'],
                'month_name' => $monthNames[$month['month']] . ' ' . $month['year'],
                'amount' => $sppCost->amount ?? 0,
                'due_date' => Carbon::create($month['year'], $month['month'], 10)->format('Y-m-d'),
                'is_overdue' => $isOverdue,
                'spp_cost_id' => $sppCost->id ?? null,
                'academic_year' => $this->getCurrentAcademicYear()
            ];
        })->sortBy(['year', 'month']);

    } catch (\Exception $e) {
        logger()->error("Error in getUnpaidMonthsAttribute: " . $e->getMessage());
        return collect();
    }
}



    protected function getCurrentAcademicYear()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year; // 2025
        return $currentMonth >= 7 ? $currentYear . '/' . ($currentYear + 1) : ($currentYear - 1) . '/' . $currentYear;
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
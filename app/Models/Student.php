<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
        'parent_name',  // Sudah ada
        'parent_phone'  // Sudah ada
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $appends = ['unpaid_months', 'full_name', 'initials'];

    // Relasi ke Class (perbaikan nama model Classes menjadi Class)
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id'); // Diasumsikan nama model Class diganti menjadi ClassModel
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activePayments()
    {
        return $this->payments()->where('status', 'paid');
    }

    // Accessor untuk nama lengkap dengan gelar
    public function getFullNameAttribute()
    {
        return $this->name . ($this->gender === 'L' ? ' Sdn' : ' Sdri');
    }

    // Accessor untuk inisial nama
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

    // Scope untuk siswa aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk siswa tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Di dalam App\Models\Student

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

    // Di dalam model Student, tambahkan method berikut:

    public function getUnpaidMonthsAttribute()
    {
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->format('m');

        if (!$this->class) {
            return collect();
        }

        // Ambil semua pembayaran yang sudah dibayar
        $paidMonths = $this->payments()
            ->where('status', 'paid')
            ->get()
            ->map(function ($payment) {
                return Carbon::parse($payment->month)->format('Y-m');
            })
            ->toArray();

        // Dapatkan biaya SPP untuk kelas ini
        $sppCosts = SppCost::where('class_id', $this->class_id)
            ->orderBy('year', 'desc')
            ->get();

        if ($sppCosts->isEmpty()) {
            return collect();
        }

        $unpaidMonths = collect();
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        // Periksa untuk setiap tahun yang memiliki biaya SPP
        foreach ($sppCosts as $sppCost) {
            $year = $sppCost->year;

            // Jika tahun ini, hanya periksa sampai bulan saat ini
            $maxMonth = ($year == $currentYear) ? $currentMonth : '12';

            for ($month = 1; $month <= $maxMonth; $month++) {
                $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                $monthValue = $months[$monthKey] ?? 'Unknown';
                $fullMonth = "$year-$monthKey";

                // Skip jika bulan ini di masa depan (untuk tahun berjalan)
                if ($year == $currentYear && $month > $currentMonth) {
                    continue;
                }

                // Cek apakah bulan ini belum dibayar
                if (!in_array($fullMonth, $paidMonths)) {
                    $dueDate = Carbon::create($year, $month, 10); // Jatuh tempo tgl 10
                    $isOverdue = $currentDate->greaterThan($dueDate);

                    $unpaidMonths->push([
                        'month' => $fullMonth,
                        'month_name' => "$monthValue $year",
                        'amount' => $sppCost->amount,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'is_overdue' => $isOverdue,
                        'spp_cost_id' => $sppCost->id,
                        'year' => $year
                    ]);
                }
            }
        }

        // Urutkan dari bulan terlama ke terbaru
        return $unpaidMonths->sortBy('month')->values();
    }

}
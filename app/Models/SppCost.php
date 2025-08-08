<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SppCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'year',
        'amount',
        'note'
    ];

    /**
     * Relasi ke ClassModel
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Relasi ke Payment
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'spp_cost_id');
    }

    /**
     * Relasi ke SppBill
     */
    public function sppBills()
    {
        return $this->hasMany(SppBill::class, 'spp_cost_id');
    }

    /**
     * Scope untuk memfilter biaya SPP berdasarkan tahun pelajaran
     * Tahun pelajaran (misalnya, 2024/2025) mencakup Juli-Desember (tahun awal) dan Januari-Juni (tahun akhir)
     */
    public function scopeByAcademicYear($query, $academicYear)
    {
        [$startYear, $endYear] = explode('/', $academicYear);

        return $query->whereIn('year', [$startYear, $endYear]);
    }

    /**
     * Mendapatkan biaya SPP untuk bulan tertentu dalam tahun pelajaran
     */
    public static function getCostForMonth($classId, $month)
    {
        $date = Carbon::parse($month);
        $year = $date->year;

        return self::where('class_id', $classId)
            ->where('year', $year)
            ->first();
    }

    /**
     * Mendapatkan biaya SPP untuk tahun pelajaran tertentu
     */
    public static function getCostForAcademicYear($classId, $academicYear)
    {
        [$startYear, $endYear] = explode('/', $academicYear);

        return self::where('class_id', $classId)
            ->whereIn('year', [$startYear, $endYear])
            ->get()
            ->keyBy('year');
    }


    // In SppCost.php
    protected static function booted()
    {
        static::created(function ($sppCost) {
            $sppCost->generateBillsForClassStudents();
        });
    }

    public function generateBillsForClassStudents()
    {
        $students = $this->class->students()->active()->get();

        foreach ($students as $student) {
            SppBill::firstOrCreate([
                'student_id' => $student->id,
                'spp_cost_id' => $this->id,
                'academic_year' => $this->getAcademicYearFromYear(),
                'amount' => $this->amount,
                'status' => 'unpaid'
            ]);
        }
    }

    protected function getAcademicYearFromYear()
    {
        $year = (int) $this->year; // Explicitly cast to integer
        $currentDate = now();
        $currentMonth = $currentDate->month;

        if ($currentMonth >= 7) { // July-Dec
            return $year . '/' . ($year + 1);
        } else { // Jan-Jun
            return ($year - 1) . '/' . $year;
        }
    }

}
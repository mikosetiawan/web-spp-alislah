<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SppBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'spp_cost_id',
        'academic_year',
        'month',
        'year',
        'amount',
        'status',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'amount' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function sppCost()
    {
        return $this->belongsTo(SppCost::class);
    }


    public function generateBillsForClassStudents()
    {
        $students = $this->class->students()->active()->get();
        $academicYear = $this->getAcademicYearFromYear();
        [$startYear, $endYear] = explode('/', $academicYear);

        // Define months for academic year (July to June)
        $academicMonths = [
            ['year' => $startYear, 'months' => [7, 8, 9, 10, 11, 12]],
            ['year' => $endYear, 'months' => [1, 2, 3, 4, 5, 6]]
        ];

        foreach ($students as $student) {
            foreach ($academicMonths as $period) {
                foreach ($period['months'] as $month) {
                    SppBill::firstOrCreate([
                        'student_id' => $student->id,
                        'spp_cost_id' => $this->id,
                        'academic_year' => $academicYear,
                        'month' => $month,
                        'year' => $period['year'],
                    ], [
                        'amount' => $this->amount,
                        'status' => 'unpaid'
                    ]);
                }
            }
        }
    }
}
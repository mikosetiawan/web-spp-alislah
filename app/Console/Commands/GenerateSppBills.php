<?php 


namespace App\Console\Commands;

use App\Models\SppCost;
use App\Models\Student;
use Illuminate\Console\Command;

class GenerateSppBills extends Command
{
    protected $signature = 'spp:generate-bills {academicYear}';
    protected $description = 'Generate SPP bills for all active students in the given academic year';

    public function handle()
    {
        $academicYear = $this->argument('academicYear');
        [$startYear, $endYear] = explode('/', $academicYear);

        $students = Student::active()->get();
        $this->info("Generating SPP bills for {$students->count()} students in academic year {$academicYear}");

        foreach ($students as $student) {
            $sppCosts = SppCost::where('class_id', $student->class_id)
                ->whereIn('year', [$startYear, $endYear])
                ->get()
                ->keyBy('year');

            if ($sppCosts->isEmpty()) {
                $this->warn("No SPP costs found for student {$student->id} in class {$student->class_id}");
                continue;
            }

            $academicMonths = [
                ['year' => $startYear, 'months' => [7, 8, 9, 10, 11, 12]],
                ['year' => $endYear, 'months' => [1, 2, 3, 4, 5, 6]]
            ];

            foreach ($academicMonths as $period) {
                $year = $period['year'];
                $sppCost = $sppCosts->get($year);

                if (!$sppCost) {
                    $this->warn("No SPP cost found for year {$year} for student {$student->id}");
                    continue;
                }

                foreach ($period['months'] as $month) {
                    $bill = $student->sppBills()->firstOrCreate([
                        'spp_cost_id' => $sppCost->id,
                        'academic_year' => $academicYear,
                        'month' => $month,
                        'year' => $year,
                    ], [
                        'amount' => $sppCost->amount,
                        'status' => 'unpaid'
                    ]);

                    $this->info("Generated bill for student {$student->id}, month {$month}/{$year}");
                }
            }
        }

        $this->info('SPP bills generated successfully.');
    }
}
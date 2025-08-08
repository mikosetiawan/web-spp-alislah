<?php

namespace App\Http\Controllers;

use App\Exports\PaymentsExport;
use App\Models\ClassModel;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SppCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Student::with([
    //         'class',
    //         'payments' => function ($q) {
    //             $q->whereYear('month', now()->year);
    //         }
    //     ])->active();

    //     // Filter berdasarkan kelas
    //     if ($request->has('class_id') && $request->class_id != '') {
    //         $query->where('class_id', $request->class_id);
    //     }

    //     // Filter berdasarkan status pembayaran
    //     if ($request->has('payment_status') && $request->payment_status != 'all') {
    //         if ($request->payment_status == 'paid') {
    //             $query->whereHas('payments', function ($q) {
    //                 $q->whereYear('month', now()->year)
    //                     ->where('status', 'paid');
    //             });
    //         } else {
    //             $query->whereDoesntHave('payments', function ($q) {
    //                 $q->whereYear('month', now()->year)
    //                     ->where('status', 'paid');
    //             });
    //         }
    //     }

    //     $students = $query->orderBy('name')->paginate(20);
    //     $classes = ClassModel::all();

    //     // Tambahkan informasi status pembayaran untuk setiap siswa
    //     $students->each(function ($student) {
    //         $student->payment_status = $student->getPaymentStatusAttribute();
    //         $student->unpaid_count = $student->unpaid_months->count();
    //     });

    //     return view('payments.index', compact('students', 'classes'));
    // }




    // public function index(Request $request)
    // {
    //     $query = Student::with([
    //         'class',
    //         'payments' => function ($q) use ($request) {
    //             // Filter pembayaran berdasarkan tahun pelajaran
    //             if ($request->has('academic_year') && $request->academic_year != '') {
    //                 [$startYear, $endYear] = explode('/', $request->academic_year);
    //                 $q->where(function ($query) use ($startYear, $endYear) {
    //                     $query->whereYear('month', $startYear)
    //                         ->whereMonth('month', '>=', 7)
    //                         ->orWhereYear('month', $endYear)
    //                         ->whereMonth('month', '<=', 6);
    //                 });
    //             } else {
    //                 $q->whereYear('month', now()->year);
    //             }
    //         }
    //     ])->active();

    //     // Filter berdasarkan kelas
    //     if ($request->has('class_id') && $request->class_id != '') {
    //         $query->where('class_id', $request->class_id);
    //     }

    //     // Filter berdasarkan status pembayaran
    //     if ($request->has('payment_status') && $request->payment_status != 'all') {
    //         if ($request->payment_status == 'paid') {
    //             $query->whereHas('payments', function ($q) use ($request) {
    //                 if ($request->has('academic_year') && $request->academic_year != '') {
    //                     [$startYear, $endYear] = explode('/', $request->academic_year);
    //                     $q->where(function ($query) use ($startYear, $endYear) {
    //                         $query->whereYear('month', $startYear)
    //                             ->whereMonth('month', '>=', 7)
    //                             ->orWhereYear('month', $endYear)
    //                             ->whereMonth('month', '<=', 6);
    //                     });
    //                 } else {
    //                     $q->whereYear('month', now()->year);
    //                 }
    //                 $q->where('status', 'paid');
    //             });
    //         } else {
    //             $query->whereDoesntHave('payments', function ($q) use ($request) {
    //                 if ($request->has('academic_year') && $request->academic_year != '') {
    //                     [$startYear, $endYear] = explode('/', $request->academic_year);
    //                     $q->where(function ($query) use ($startYear, $endYear) {
    //                         $query->whereYear('month', $startYear)
    //                             ->whereMonth('month', '>=', 7)
    //                             ->orWhereYear('month', $endYear)
    //                             ->whereMonth('month', '<=', 6);
    //                     });
    //                 } else {
    //                     $q->whereYear('month', now()->year);
    //                 }
    //                 $q->where('status', 'paid');
    //             });
    //         }
    //     }

    //     $students = $query->orderBy('name')->paginate(20);
    //     $classes = ClassModel::all();

    //     // Daftar tahun pelajaran untuk filter
    //     $academicYears = $this->getAcademicYears();

    //     // Tambahkan informasi status pembayaran untuk setiap siswa
    //     $students->each(function ($student) use ($request) {
    //         $student->payment_status = $student->getPaymentStatusAttribute();
    //         $student->unpaid_count = $student->unpaid_months->count();
    //     });

    //     return view('payments.index', compact('students', 'classes', 'academicYears'));
    // }

    // /**
    //  * Helper method untuk mendapatkan daftar tahun pelajaran
    //  */
    // protected function getAcademicYears()
    // {
    //     $currentYear = now()->year;
    //     $years = Payment::select(DB::raw("DISTINCT YEAR(month) as year"))
    //         ->orderBy('year', 'desc')
    //         ->pluck('year')
    //         ->toArray();

    //     $academicYears = [];
    //     foreach ($years as $year) {
    //         $academicYears[] = ($year - 1) . '/' . $year;
    //         $academicYears[] = $year . '/' . ($year + 1);
    //     }

    //     // Tambahkan tahun pelajaran saat ini jika belum ada
    //     $currentAcademicYear = (now()->month < 7 ? ($currentYear - 1) : $currentYear) . '/' . (now()->month < 7 ? $currentYear : ($currentYear + 1));
    //     if (!in_array($currentAcademicYear, $academicYears)) {
    //         $academicYears[] = $currentAcademicYear;
    //     }

    //     return array_unique($academicYears);
    // }


    /**
     * Helper method untuk mendapatkan daftar tahun pelajaran
     */
    protected function getAcademicYears()
    {
        $currentYear = now()->year;
        $years = Payment::select(DB::raw("DISTINCT YEAR(month) as year"))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Generate academic years format: 2023/2024
        $academicYears = [];
        foreach ($years as $year) {
            // Tambahkan format tahun ajaran (July-June)
            $academicYear = $year . '/' . ($year + 1);
            if (!in_array($academicYear, $academicYears)) {
                $academicYears[] = $academicYear;
            }
        }

        // Tambahkan tahun ajaran saat ini jika belum ada
        $currentAcademicYear = $this->getCurrentAcademicYear();
        if (!in_array($currentAcademicYear, $academicYears)) {
            array_unshift($academicYears, $currentAcademicYear);
        }

        return array_unique($academicYears);
    }



    public function index(Request $request)
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $academicYear = $request->input('academic_year', $currentAcademicYear);

        // Validasi format tahun ajaran
        if (!preg_match('/^\d{4}\/\d{4}$/', $academicYear)) {
            $academicYear = $currentAcademicYear;
        }

        try {
            [$startYear, $endYear] = explode('/', $academicYear);
        } catch (\Exception $e) {
            // Fallback ke tahun ajaran saat ini jika parsing gagal
            [$startYear, $endYear] = explode('/', $currentAcademicYear);
        }

        $query = Student::with([
            'class',
            'payments' => function ($q) use ($startYear, $endYear) {
                $q->where(function ($query) use ($startYear, $endYear) {
                    $query->whereYear('month', $startYear)->whereMonth('month', '>=', 7)
                        ->orWhereYear('month', $endYear)->whereMonth('month', '<=', 6);
                });
            }
        ])->active();


        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != 'all') {
            if ($request->payment_status == 'paid') {
                $query->whereHas('payments', function ($q) use ($academicYear) {
                    try {
                        [$startYear, $endYear] = explode('/', $academicYear);
                        $q->where(function ($query) use ($startYear, $endYear) {
                            $query->whereYear('month', $startYear)->whereMonth('month', '>=', 7)
                                ->orWhereYear('month', $endYear)->whereMonth('month', '<=', 6);
                        })->where('status', 'paid');
                    } catch (\Exception $e) {
                        Log::error("Error parsing academic year in payment filter: {$e->getMessage()}");
                    }
                });
            } else {
                $query->whereDoesntHave('payments', function ($q) use ($academicYear) {
                    try {
                        [$startYear, $endYear] = explode('/', $academicYear);
                        $q->where(function ($query) use ($startYear, $endYear) {
                            $query->whereYear('month', $startYear)->whereMonth('month', '>=', 7)
                                ->orWhereYear('month', $endYear)->whereMonth('month', '<=', 6);
                        })->where('status', 'paid');
                    } catch (\Exception $e) {
                        Log::error("Error parsing academic year in unpaid filter: {$e->getMessage()}");
                    }
                });
            }
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = ClassModel::all();
        $academicYears = $this->getAcademicYears();

        // Add payment status for each student
        $students->each(function ($student) use ($academicYear) {
            request()->merge(['academic_year' => $academicYear]);
            $student->payment_status = $student->getPaymentStatusAttribute();
            $student->setRelation('unpaid_months', $student->unpaid_months);
        });

        return view('payments.index', compact(
            'students',
            'classes',
            'academicYears',
            'currentAcademicYear'
        ));
    }


    /**
     * Get current academic year
     */
    protected function getCurrentAcademicYear()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Tahun ajaran berjalan dari Juli 2023 - Juni 2024 (2023/2024)
        return $currentMonth >= 7 ? $currentYear . '/' . ($currentYear + 1)
            : ($currentYear - 1) . '/' . $currentYear;
    }



    public function show(Student $student)
    {
        $student->load([
            'payments' => function ($q) {
                $q->orderBy('month', 'desc');
            },
            'class.sppCosts'
        ]);

        $currentYear = now()->year;
        $sppCost = SppCost::where('class_id', $student->class_id)
            ->where('year', $currentYear)
            ->first();

        // Hitung total tunggakan
        $totalUnpaid = $student->unpaid_months->sum('amount');

        // Kelompokkan pembayaran per tahun untuk tampilan yang lebih rapi
        $paymentsByYear = $student->payments->groupBy(function ($payment) {
            return Carbon::parse($payment->month)->format('Y');
        });

        return view('payments.show', compact('student', 'sppCost', 'totalUnpaid', 'paymentsByYear'));
    }

    public function create(Student $student)
    {
        $unpaidMonths = $student->unpaid_months->map(function ($month) use ($student) {
            // Tambahkan spp_cost_id ke setiap bulan
            $month['spp_cost_id'] = SppCost::where('class_id', $student->class_id)
                ->where('year', explode('-', $month['month'])[0])
                ->first()->id ?? null;
            return $month;
        });

        if ($unpaidMonths->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Siswa ini tidak memiliki tunggakan SPP');
        }

        return view('payments.create', [
            'student' => $student,
            'unpaidMonths' => $unpaidMonths,
            'sppCost' => $unpaidMonths->first()['amount'] ?? 0
        ]);
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date|before_or_equal:today',
            'spp_cost_id' => 'required|exists:spp_costs,id',
            'payment_method' => 'required|string|in:cash,transfer,qris',
            'note' => 'nullable|string|max:500'
        ]);

        // Tambahkan tanggal lengkap untuk bulan
        $validated['month'] = $validated['month'] . '-01';

        DB::transaction(function () use ($validated, $student) {
            $payment = new Payment($validated + [
                'status' => 'paid',
                'admin_id' => auth()->id(),
                'student_id' => $student->id
            ]);

            $payment->save();
        });

        return redirect()
            ->route('students.payments.show', $student)
            ->with('success', 'Pembayaran SPP berhasil dicatat');
    }

    public function printReceipt(Payment $payment)
    {
        $payment->load('student.class');

        return view('payments.receipt', compact('payment'));
    }

    public function getUnpaidMonths(Student $student)
    {
        return response()->json($student->unpaid_months);
    }


    public function report(Request $request)
    {
        $query = Payment::with('student.class')
            ->orderBy('payment_date', 'desc');

        // Filter by year
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('month', $request->year);
        }

        // Filter by month range
        if ($request->has('start_month') && $request->start_month != '') {
            $startDate = Carbon::parse($request->start_month)->startOfMonth();
            $query->where('month', '>=', $startDate);
        }

        if ($request->has('end_month') && $request->end_month != '') {
            $endDate = Carbon::parse($request->end_month)->endOfMonth();
            $query->where('month', '<=', $endDate);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // For Excel export
        if ($request->has('export')) {
            return $this->exportExcel($query->get());
        }

        $payments = $query->paginate(50);
        $classes = ClassModel::all();

        $years = Payment::select(DB::raw("DISTINCT YEAR(month) as year"))
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('payments.report', compact('payments', 'classes', 'years'));
    }

    private function exportExcel($payments)
    {
        return Excel::download(new PaymentsExport($payments), 'laporan-pembayaran-spp.xlsx');
    }

    public function printReport(Request $request)
    {
        $query = Payment::with('student.class')
            ->orderBy('payment_date', 'desc');

        // Same filters as report method
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('month', $request->year);
        }

        if ($request->has('start_month') && $request->start_month != '') {
            $startDate = Carbon::parse($request->start_month)->startOfMonth();
            $query->where('month', '>=', $startDate);
        }

        if ($request->has('end_month') && $request->end_month != '') {
            $endDate = Carbon::parse($request->end_month)->endOfMonth();
            $query->where('month', '<=', $endDate);
        }

        if ($request->has('class_id') && $request->class_id != '') {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $payments = $query->get();
        $totalAmount = $payments->sum('amount');

        return view('payments.print', compact('payments', 'totalAmount'));
    }
}
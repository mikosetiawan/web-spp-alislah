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
    public function index(Request $request)
    {
        $query = Student::with([
            'class',
            'payments' => function ($q) {
                $q->whereYear('month', now()->year);
            }
        ])->active();

        // Filter berdasarkan kelas
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Filter berdasarkan status pembayaran
        if ($request->has('payment_status') && $request->payment_status != 'all') {
            if ($request->payment_status == 'paid') {
                $query->whereHas('payments', function ($q) {
                    $q->whereYear('month', now()->year)
                        ->where('status', 'paid');
                });
            } else {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->whereYear('month', now()->year)
                        ->where('status', 'paid');
                });
            }
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = ClassModel::all();

        // Tambahkan informasi status pembayaran untuk setiap siswa
        $students->each(function ($student) {
            $student->payment_status = $student->getPaymentStatusAttribute();
            $student->unpaid_count = $student->unpaid_months->count();
        });

        return view('payments.index', compact('students', 'classes'));
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
        $unpaidMonths = $student->unpaid_months;

        if ($unpaidMonths->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Siswa ini tidak memiliki tunggakan SPP');
        }

        $currentYear = now()->year;
        $sppCost = SppCost::where('class_id', $student->class_id)
            ->where('year', $currentYear)
            ->first();

        return view('payments.create', compact('student', 'unpaidMonths', 'sppCost'));
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

    // app/Http/Controllers/PaymentController.php

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
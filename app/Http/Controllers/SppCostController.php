<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassModel;
use App\Models\SppCost;
use App\Models\SppBill;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SppCostController extends Controller
{
    public function index()
    {
        $sppCosts = SppCost::with('class')->latest()->paginate(10);
        return view('spp-costs.index', compact('sppCosts'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        return view('spp-costs.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $currentYear = Carbon::now()->year; // 2025
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:10000',
            'year' => [
                'required',
                'regex:/^([0-9]{4})\/([0-9]{4})$/',
                function ($attribute, $value, $fail) use ($currentYear) {
                    [$startYear, $endYear] = explode('/', $value);
                    if ($startYear < $currentYear || $endYear != $startYear + 1) {
                        $fail('Tahun ajaran harus dimulai dari ' . $currentYear . ' atau setelahnya dan dalam format YYYY/YYYY+1.');
                    }
                },
            ],
        ]);

        // Check if spp cost already exists for this class and year
        $exists = SppCost::where('class_id', $request->class_id)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Biaya SPP untuk kelas dan tahun ajaran ini sudah ada.');
        }

        // Create SPP Cost
        $sppCost = SppCost::create($request->all());

        // Generate SPP Bills for students in the selected class
        $students = Student::where('class_id', $request->class_id)->get();
        foreach ($students as $student) {
            // Create 12 monthly bills for the academic year
            [$startYear] = explode('/', $request->year);
            for ($month = 7; $month <= 12; $month++) { // July to December
                SppBill::create([
                    'student_id' => $student->id,
                    'spp_cost_id' => $sppCost->id,
                    'month' => $month,
                    'year' => $startYear,
                    'amount' => $request->amount,
                    'status' => 'unpaid',
                ]);
            }
            for ($month = 1; $month <= 6; $month++) { // January to June (next year)
                SppBill::create([
                    'student_id' => $student->id,
                    'spp_cost_id' => $sppCost->id,
                    'month' => $month,
                    'year' => $startYear + 1,
                    'amount' => $request->amount,
                    'status' => 'unpaid',
                ]);
            }
        }

        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP dan tagihan berhasil ditambahkan.');
    }

    public function edit(SppCost $sppCost)
    {
        $classes = ClassModel::all();
        return view('spp-costs.edit', compact('sppCost', 'classes'));
    }

    public function update(Request $request, SppCost $sppCost)
    {
        $currentYear = Carbon::now()->year; // 2025
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:10000',
            'year' => [
                'required',
                'regex:/^([0-9]{4})\/([0-9]{4})$/',
                function ($attribute, $value, $fail) use ($currentYear) {
                    [$startYear, $endYear] = explode('/', $value);
                    if ($startYear < $currentYear || $endYear != $startYear + 1) {
                        $fail('Tahun ajaran harus dimulai dari ' . $currentYear . ' atau setelahnya dan dalam format YYYY/YYYY+1.');
                    }
                },
            ],
        ]);

        // Check if another SPP cost exists for this class and year
        $exists = SppCost::where('class_id', $request->class_id)
            ->where('year', $request->year)
            ->where('id', '!=', $sppCost->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Biaya SPP untuk kelas dan tahun ajaran ini sudah ada.');
        }

        $sppCost->update($request->all());

        // Update existing SPP bills for this SPP cost
        SppBill::where('spp_cost_id', $sppCost->id)->update(['amount' => $request->amount]);

        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP dan tagihan terkait berhasil diperbarui.');
    }

    public function destroy(SppCost $sppCost)
    {
        // Delete related SPP bills
        SppBill::where('spp_cost_id', $sppCost->id)->delete();
        $sppCost->delete();
        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP dan tagihan terkait berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassModel;
use App\Models\SppCost;
use Illuminate\Http\Request;

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
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:10000',
            'year' => 'required|digits:4|integer|min:2020|max:' . (date('Y') + 5),
        ]);

        // Check if spp cost already exists for this class and year
        $exists = SppCost::where('class_id', $request->class_id)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Biaya SPP untuk kelas dan tahun ini sudah ada.');
        }

        SppCost::create($request->all());

        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP berhasil ditambahkan.');
    }

    public function edit(SppCost $sppCost)
    {
        $classes = ClassModel::all();
        return view('spp-costs.edit', compact('sppCost', 'classes'));
    }

    public function update(Request $request, SppCost $sppCost)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'amount' => 'required|numeric|min:10000',
            'year' => 'required|digits:4|integer|min:2020|max:' . (date('Y') + 5),
        ]);

        $sppCost->update($request->all());

        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP berhasil diperbarui.');
    }

    public function destroy(SppCost $sppCost)
    {
        $sppCost->delete();
        return redirect()->route('spp-costs.index')
            ->with('success', 'Biaya SPP berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $classes = ClassModel::withCount('students')
            ->when($search, function($query, $search) {
                return $query->search($search);
            })
            ->latest()
            ->paginate(10);

        return view('classes.index', compact('classes', 'search'));
    }

    public function create()
    {
        $majors = [
            'TKJ' => 'Teknik Komputer dan Jaringan',
            'RPL' => 'Rekayasa Perangkat Lunak',
            'MM' => 'Multimedia'
        ];

        return view('classes.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:10',
            'major' => 'required|in:TKJ,RPL,MM',
            'grade' => 'required|integer|between:10,12',
            'teacher_name' => 'required|max:100',
            'max_students' => 'required|integer|min:1',
        ]);

        ClassModel::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(ClassModel $class)
    {
        $class->load('students', 'sppCosts');
        return view('classes.show', compact('class'));
    }

    public function edit(ClassModel $class)
    {
        $majors = [
            'TKJ' => 'Teknik Komputer dan Jaringan',
            'RPL' => 'Rekayasa Perangkat Lunak',
            'MM' => 'Multimedia'
        ];

        return view('classes.edit', compact('class', 'majors'));
    }

    public function update(Request $request, ClassModel $class)
    {
        $request->validate([
            'name' => 'required|max:10',
            'major' => 'required|in:TKJ,RPL,MM',
            'grade' => 'required|integer|between:10,12',
            'teacher_name' => 'required|max:100',
            'max_students' => 'required|integer|min:1',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(ClassModel $class)
    {
        // Cek apakah kelas memiliki siswa
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
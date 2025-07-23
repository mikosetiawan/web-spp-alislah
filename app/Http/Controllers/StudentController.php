<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel; // Diasumsikan nama model Class diganti menjadi ClassModel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'active');
        
        $students = Student::with('class')
            ->when($search, function($query, $search) {
                return $query->search($search);
            })
            ->when($status, function($query, $status) {
                if ($status === 'active') {
                    return $query->where('status', 'active');
                } elseif ($status === 'inactive') {
                    return $query->where('status', 'inactive');
                }
                return $query;
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('students.index', compact('students', 'search', 'status'));
    }

    public function create()
    {
        $classes = ClassModel::active()->get(); // Hanya kelas aktif
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students|max:20',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:students',
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'required|max:100',
            'address' => 'required',
            'phone' => 'required|max:15',
            'parent_name' => 'required|max:100',
            'parent_phone' => 'required|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('photo');
        $data['status'] = 'active'; // Default status aktif

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storePhoto($request->file('photo'));
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['class', 'payments' => function($query) {
            $query->latest()->limit(12);
        }, 'payments.sppCost']);
        
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassModel::active()->get();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|max:20|unique:students,nis,'.$student->id,
            'name' => 'required|max:100',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'required|max:100',
            'address' => 'required',
            'phone' => 'required|max:15',
            'parent_name' => 'required|max:100',
            'parent_phone' => 'required|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'sometimes|in:active,inactive'
        ]);

        $data = $request->except('photo', 'remove_photo');

        // Handle photo removal
        if ($request->has('remove_photo') && $student->photo) {
            Storage::disk('public')->delete($student->photo);
            $data['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $this->storePhoto($request->file('photo'));
        }

        $student->update($data);

        return redirect()->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        // Soft delete, jadi foto tidak dihapus dulu
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function restore($id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->forceDelete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus permanen.');
    }

    protected function storePhoto($photo)
    {
        $filename = Str::random(20) . '.' . $photo->getClientOriginalExtension();
        return $photo->storeAs('student-photos', $filename, 'public');
    }
}
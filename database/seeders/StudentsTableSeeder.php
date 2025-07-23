<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Classes;
use App\Models\ClassModel;

class StudentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ClassModel::all();

        foreach ($classes as $class) {
            for ($i = 1; $i <= 10; $i++) {
                Student::create([
                    'nis' => $class->id . '00' . $i,
                    'name' => 'Siswa ' . $i . ' ' . $class->name,
                    'email' => 'siswa' . $class->id . $i . '@example.com',
                    'phone' => '0812345678' . $i,
                    'address' => 'Jl. Contoh Alamat ' . $i,
                    'class_id' => $class->id,
                    'gender' => $i % 2 == 0 ? 'L' : 'P',
                    'birth_date' => now()->subYears(16)->subDays(rand(0, 365)),
                    'birth_place' => 'Kota Contoh',
                    'photo' => null,
                    'status' => 'active',
                    'parent_name' => 'Orang Tua ' . $i,
                    'parent_phone' => '0812987654' . $i,
                ]);
            }
        }
    }
}

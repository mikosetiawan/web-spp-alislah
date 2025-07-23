<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\ClassModel;

class ClassesTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'X-A',
                'major' => 'TKJ',
                'grade' => 10,
                'teacher_name' => 'Budi Santoso',
                'max_students' => 30,
            ],
            [
                'name' => 'XI-RPL',
                'major' => 'RPL',
                'grade' => 11,
                'teacher_name' => 'Siti Aminah',
                'max_students' => 32,
            ],
            [
                'name' => 'XII-MM',
                'major' => 'MM',
                'grade' => 12,
                'teacher_name' => 'Agus Wijaya',
                'max_students' => 28,
            ],
        ];

        foreach ($data as $class) {
            ClassModel::create($class);
        }
    }
}

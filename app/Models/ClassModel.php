<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;

    // Tambahkan ini untuk menentukan nama tabel
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'major',
        'grade',
        'teacher_name',
        'max_students'
    ];

    // Relasi ke siswa dengan spesifikasi foreign key
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // Relasi ke SPP Costs
    public function sppCosts()
    {
        return $this->hasMany(SppCost::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
        // Jika tabel classes tidak memiliki kolom status, gunakan kondisi lain yang sesuai
        // Contoh: return $query; // Jika semua kelas dianggap aktif
    }

    // Accessor untuk nama lengkap kelas
    public function getFullNameAttribute(): string
    {
        $major = [
            'TKJ' => 'Teknik Komputer dan Jaringan',
            'RPL' => 'Rekayasa Perangkat Lunak',
            'MM' => 'Multimedia',
        ];

        $majorName = $major[$this->major] ?? $this->major;

        return "Kelas {$this->grade} {$majorName} {$this->name}";
    }

    // Scope pencarian
    public function scopeSearch($query, string $term)
    {
        $term = "%$term%";
        return $query->where('name', 'like', $term)
            ->orWhere('major', 'like', $term)
            ->orWhere('teacher_name', 'like', $term);
    }
}
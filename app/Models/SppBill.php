<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SppBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'spp_cost_id',
        'month',
        'year',
        'amount',
        'status',
    ];



    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function bills()
    {
        return $this->hasMany(SppBill::class);
    }


    /**
     * Relasi ke model Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke model SppCost
     */
    public function sppCost()
    {
        return $this->belongsTo(SppCost::class);
    }
}

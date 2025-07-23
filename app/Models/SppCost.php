<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'year',
        'amount',
        'note'
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
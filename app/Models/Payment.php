<?php

namespace App\Models;

use App\Traits\GeneratesReceiptNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, GeneratesReceiptNumber;

    protected $fillable = [
        'student_id',
        'month',
        'amount',
        'payment_date',
        'payment_method',
        'receipt_number',
        'status',
        'note',
        'admin_id',
        'spp_cost_id'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'month' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function sppCost()
    {
        return $this->belongsTo(SppCost::class);
    }
}
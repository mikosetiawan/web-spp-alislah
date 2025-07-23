<?php

namespace App\Traits;

trait GeneratesReceiptNumber
{
    protected static function bootGeneratesReceiptNumber()
    {
        static::creating(function ($model) {
            if (empty($model->receipt_number)) {
                $model->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber()
    {
        $prefix = 'SPP-' . now()->format('Ymd') . '-';
        $lastPayment = static::where('receipt_number', 'like', $prefix . '%')
                           ->orderBy('receipt_number', 'desc')
                           ->first();

        if ($lastPayment) {
            $lastNumber = (int) str_replace($prefix, '', $lastPayment->receipt_number);
            $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '00001';
        }

        return $prefix . $nextNumber;
    }
}
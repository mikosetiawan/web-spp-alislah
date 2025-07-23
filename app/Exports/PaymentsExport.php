<?php 


// app/Exports/PaymentsExport.php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Tanggal Bayar',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Bulan',
            'Jumlah',
            'Metode Pembayaran',
            'Admin'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->payment_date->format('d/m/Y'),
            $payment->student->nis,
            $payment->student->name,
            $payment->student->class->name ?? '-',
            $payment->month->format('F Y'),
            $payment->amount,
            ucfirst($payment->payment_method),
            $payment->admin->name ?? '-'
        ];
    }
}
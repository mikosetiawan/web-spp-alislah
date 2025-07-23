
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembayaran SPP</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMBAYARAN SPP</h2>
        <p>Sekolah XYZ - {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Bayar</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Bulan</th>
                <th class="text-right">Jumlah</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                <td>{{ $payment->student->nis }}</td>
                <td>{{ $payment->student->name }}</td>
                <td>{{ $payment->student->class->name ?? '-' }}</td>
                <td>{{ $payment->month->format('F Y') }}</td>
                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Bendahara</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
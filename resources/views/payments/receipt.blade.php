<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto bg-white shadow-md my-8 p-8">
        <div class="text-center border-b-2 border-black pb-4 mb-6">
            <div class="text-2xl font-bold">NAMA SEKOLAH ANDA</div>
            <div class="text-sm">Alamat Sekolah, Kota, Kode Pos</div>
            <div class="text-sm">Telp: (021) 12345678 | Email: sekolah@example.com</div>
        </div>

        <div class="text-center text-xl font-bold my-4">KWITANSI PEMBAYARAN SPP</div>
        
        <div class="text-right mb-6">
            <strong>No. Kwitansi:</strong> {{ $payment->receipt_number }}
        </div>

        <table class="w-full border-collapse border border-gray-300 mb-6">
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Nama Siswa</td>
                <td class="p-2 border border-gray-300">{{ $payment->student->name }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">NIS</td>
                <td class="p-2 border border-gray-300">{{ $payment->student->nis }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Kelas</td>
                <td class="p-2 border border-gray-300">{{ $payment->student->class->full_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Bulan Pembayaran</td>
                <td class="p-2 border border-gray-300">{{ \Carbon\Carbon::parse($payment->month)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Tanggal Pembayaran</td>
                <td class="p-2 border border-gray-300">{{ $payment->payment_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Jumlah Pembayaran</td>
                <td class="p-2 border border-gray-300">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Metode Pembayaran</td>
                <td class="p-2 border border-gray-300">{{ ucfirst($payment->payment_method) }}</td>
            </tr>
            @if($payment->note)
            <tr>
                <td class="p-2 border border-gray-300 bg-gray-100 font-medium">Catatan</td>
                <td class="p-2 border border-gray-300">{{ $payment->note }}</td>
            </tr>
            @endif
        </table>

        <div class="flex justify-between mt-10">
            <div>
                <strong>Petugas:</strong> {{ $payment->admin->name ?? '-' }}
            </div>
            <div class="text-center">
                <div>Hormat Kami,</div>
                <div class="border-t border-black mt-12 pt-1 w-48 mx-auto">Bendahara Sekolah</div>
            </div>
        </div>

        <div class="text-center mt-8 print:hidden">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Cetak Kwitansi</button>
            <button onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded">Tutup</button>
        </div>
    </div>

    <style>
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }
            .print\:hidden {
                display: none;
            }
        }
    </style>
</body>
</html>
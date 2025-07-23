<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Pembayaran SPP - {{ $student->name }}</h2>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('payments.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200 text-center">
                    Kembali
                </a>
                @if ($student->unpaid_months->count() > 0)
                    <a href="{{ route('students.payments.create', $student) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-center">
                        Tambah Pembayaran
                    </a>
                @endif
            </div>
        </div>

         {{-- Alert Message --}}
        <x-alert-message type="success" />
        <x-alert-message type="error" />
        <x-alert-message type="warning" />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h5 class="text-lg font-medium text-gray-900 mb-3">Informasi Siswa</h5>
                <p class="mb-1"><strong>NIS:</strong> {{ $student->nis }}</p>
                <p class="mb-1"><strong>Kelas:</strong> {{ $student->class->full_name ?? '-' }}</p>
                <p class="mb-0"><strong>Status:</strong>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $student->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h5 class="text-lg font-medium text-gray-900 mb-3">SPP Tahun Ini</h5>
                <p class="mb-1"><strong>Biaya per Bulan:</strong>
                    {{ $sppCost ? 'Rp ' . number_format($sppCost->amount, 0, ',', '.') : '-' }}
                </p>
                <p class="mb-0"><strong>Status Bulan Ini:</strong>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $student->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $student->payment_status == 'paid' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h5 class="text-lg font-medium text-gray-900 mb-3">Tunggakan</h5>
                <p class="mb-1"><strong>Jumlah Bulan:</strong> {{ $student->unpaid_months->count() }}</p>
                <p class="mb-3"><strong>Total:</strong>
                    Rp {{ number_format($student->unpaid_months->sum('amount'), 0, ',', '.') }}
                </p>
                @if ($student->unpaid_months->count() > 0)
                    <a href="{{ route('students.payments.create', $student) }}"
                        class="w-full sm:w-auto px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition duration-200 text-center block sm:inline-block text-center">
                        Bayar Sekarang
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-medium text-gray-900">Riwayat Pembayaran</h4>
            </div>

            <div class="p-6">
                @if ($student->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kwitansi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($student->payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Carbon\Carbon::parse($payment->month)->translatedFormat('F Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->payment_method) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $payment->receipt_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $payment->status == 'paid' ? 'Lunas' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('payments.receipt.print', $payment) }}" class="text-blue-600 hover:text-blue-900" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Belum ada riwayat pembayaran untuk siswa ini.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
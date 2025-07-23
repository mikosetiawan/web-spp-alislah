<x-app-layout title="Detail Siswa">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom kiri -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profil Siswa -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 mb-4">
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-4xl text-gray-600 font-medium">{{ $student->initials }}</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $student->full_name }}</h3>
                    <p class="text-gray-600">{{ $student->nis }}</p>
                    
                    <div class="mt-4 w-full space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Kelas</span>
                            <span class="font-medium">{{ $student->class->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Jurusan</span>
                            <span class="font-medium">{{ $student->class->major ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $student->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Jenis Kelamin</span>
                            <span class="font-medium">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">TTL</span>
                            <span class="font-medium text-right">{{ $student->birth_place }}, {{ $student->birth_date->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Usia</span>
                            <span class="font-medium">{{ $student->birth_date->age }} tahun</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">No. HP</span>
                            <span class="font-medium">{{ $student->phone }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Email</span>
                            <span class="font-medium">{{ $student->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Orang Tua -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-900 mb-4">Orang Tua/Wali</h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-medium">{{ $student->parent_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">No. HP</p>
                        <p class="font-medium">{{ $student->parent_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kolom kanan -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Alamat -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Alamat Lengkap</h3>
                </div>
                <div class="prose prose-sm max-w-none">
                    {!! nl2br(e($student->address)) !!}
                </div>
            </div>
            
            <!-- Riwayat Pembayaran -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Pembayaran SPP</h3>
                    <div class="flex space-x-3">
                        <a href="{{ route('students.payments.create', $student) }}" class="btn-primary-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Pembayaran
                        </a>
                        <a href="{{ route('students.unpaid-months', $student->id) }}" class="btn-secondary-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Bulan Belum Bayar
                        </a>
                    </div>
                </div>
                
                @if($student->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($student->payments as $payment)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $payment->month->translatedFormat('F Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $payment->sppCost->year ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $payment->status === 'paid' ? 'Lunas' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->payment_date->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('payments.receipt', $payment->id) }}" 
                                            class="text-green-600 hover:text-green-900" title="Kwitansi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat pembayaran</h3>
                    <p class="mt-1 text-sm text-gray-500">Siswa ini belum memiliki riwayat pembayaran SPP.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
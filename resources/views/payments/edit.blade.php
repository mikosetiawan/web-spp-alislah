<x-app-layout title="Edit Pembayaran SPP">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Pembayaran SPP</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('payments.update', $payment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="">Pilih Siswa</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" 
                                    {{ $payment->student_id == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->nis }}) - {{ $student->class->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Bulan Tagihan</label>
                        <select name="month" id="month" class="form-select" required>
                            <option value="">Pilih Bulan</option>
                            @php
                                $currentYear = now()->year;
                                $months = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                            @endphp
                            @foreach($months as $key => $month)
                                <option value="{{ $currentYear }}-{{ $key }}"
                                    {{ $payment->month == $currentYear.'-'.$key ? 'selected' : '' }}>
                                    {{ $month }} {{ $currentYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-input" 
                            value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-span-1">
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Dibayar (Rp)</label>
                        <input type="number" name="amount_paid" id="amount_paid" class="form-input" 
                            value="{{ old('amount_paid', $payment->amount_paid) }}" required>
                    </div>

                    <div class="col-span-1">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ $payment->payment_method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="qris" {{ $payment->payment_method == 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="notes" id="notes" class="form-input" 
                            value="{{ old('notes', $payment->notes) }}">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('payments.index') }}" class="btn-secondary mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
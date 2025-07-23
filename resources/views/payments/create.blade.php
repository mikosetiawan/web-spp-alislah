<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <h4 class="text-xl font-semibold text-white">Pembayaran SPP - {{ $student->name }}</h4>
                </div>

                <div class="p-6">
                    <form action="{{ route('students.payments.store', $student) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Bulan yang Dibayar</label>
                            <select name="month" id="month" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Bulan</option>
                                @foreach ($unpaidMonths as $unpaid)
                                    <option value="{{ $unpaid['month'] }}">
                                        {{ $unpaid['month_name'] }}
                                        (Rp {{ number_format($unpaid['amount'], 0, ',', '.') }})
                                        @if ($unpaid['is_overdue'])
                                            - Terlambat
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                            <input type="number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="amount" name="amount" required>
                        </div>

                        <div class="mb-4">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <input type="date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="note" id="note" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <input type="hidden" name="spp_cost_id" value="{{ $unpaidMonths->first()['spp_cost_id'] }}">

                        <div class="flex flex-col sm:flex-row justify-end gap-3">
                            <a href="{{ route('students.payments.show', $student) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200 text-center">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('month').addEventListener('change', function() {
            const selectedMonth = this.value;
            const unpaidMonths = @json($unpaidMonths);

            const selectedData = unpaidMonths.find(month => month.month === selectedMonth);
            if (selectedData) {
                document.getElementById('amount').value = selectedData.amount;
            }
        });
    </script>
</x-app-layout>
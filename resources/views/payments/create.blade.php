<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <h4 class="text-xl font-semibold text-white">Pembayaran SPP - {{ $student->name }}</h4>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="p-6">
                    <form action="{{ route('students.payments.store', $student) }}" method="POST" id="paymentForm">
                        @csrf

                        <div class="mb-4">
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Bulan yang Dibayar</label>
                            <select name="month" id="month" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Bulan</option>
                                @php
                                    $currentMonth = now()->month; // 8 for August
                                    $currentYear = now()->year; // 2025
                                @endphp
                                @foreach ($unpaidMonths as $unpaid)
                                    <?php
                                        $monthValue = $unpaid['month'] . '-' . $unpaid['year'];
                                        $displayName = \Carbon\Carbon::create($unpaid['year'], $unpaid['month'], 1)->translatedFormat('F Y');
                                        $isSelected = ($unpaid['month'] == $currentMonth && $unpaid['year'] == $currentYear) ? 'selected' : '';
                                    ?>
                                    <option value="{{ $monthValue }}" {{ $isSelected }}>
                                        {{ $displayName }} (Rp {{ number_format($unpaid['amount'], 0, ',', '.') }})
                                        @if ($unpaid['is_overdue'])
                                            - Terlambat
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                            <input type="number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="amount" name="amount" required readonly>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <input type="date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="other">Lainnya</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="note" id="note" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="spp_cost_id" value="{{ $unpaidMonths->first()['spp_cost_id'] ?? '' }}">

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
        document.addEventListener('DOMContentLoaded', function () {
            const monthSelect = document.getElementById('month');
            const amountInput = document.getElementById('amount');
            const unpaidMonths = @json($unpaidMonths);

            // Set default value and amount based on current month
            const currentMonth = {{ now()->month }}; // 8 for August
            const currentYear = {{ now()->year }}; // 2025
            if (unpaidMonths.length > 0) {
                const defaultValue = unpaidMonths.find(item => item.month == currentMonth && item.year == currentYear);
                const selectedValue = defaultValue ? `${defaultValue.month}-${defaultValue.year}` : `${unpaidMonths[0].month}-${unpaidMonths[0].year}`;
                monthSelect.value = selectedValue;
                amountInput.value = defaultValue ? defaultValue.amount : unpaidMonths[0].amount;
            }

            // Update amount when month changes
            monthSelect.addEventListener('change', function () {
                const selectedValue = this.value;
                if (selectedValue) {
                    const [month, year] = selectedValue.split('-');
                    const selectedData = unpaidMonths.find(item => item.month == month && item.year == year);
                    if (selectedData) {
                        amountInput.value = selectedData.amount;
                    } else {
                        amountInput.value = '';
                    }
                }
            });

            // Form submission validation
            document.getElementById('paymentForm').addEventListener('submit', function (event) {
                const selectedMonth = monthSelect.value;
                const amount = amountInput.value;
                if (!selectedMonth || !amount) {
                    event.preventDefault();
                    alert('Silakan pilih bulan dan pastikan jumlah pembayaran terisi.');
                }
            });
        });
    </script>
</x-app-layout>
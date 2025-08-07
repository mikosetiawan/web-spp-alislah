<x-app-layout title="Tambah Biaya SPP">
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Tambah Biaya SPP</h2>
                <a href="{{ route('spp-costs.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </div>

            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('spp-costs.store') }}" method="POST" id="sppForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select name="class_id" id="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input type="number" name="year" id="year" min="2025" max="2099" step="1" value="{{ old('year', now()->year) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <p id="year_error" class="mt-1 text-sm text-red-600 hidden">Tahun harus tahun berjalan (2025) atau tahun ke depan.</p>
                        @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Biaya SPP</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" 
                                   class="block w-full pl-10 pr-12 py-2 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="0" required>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg transition duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('year').addEventListener('input', function() {
            const yearInput = this.value;
            const currentYear = new Date().getFullYear(); // 2025
            const errorElement = document.getElementById('year_error');

            // Validasi tahun harus 2025 atau lebih
            if (yearInput < currentYear) {
                errorElement.classList.remove('hidden');
                this.setCustomValidity('Tahun harus tahun berjalan (2025) atau tahun ke depan.');
            } else {
                errorElement.classList.add('hidden');
                this.setCustomValidity('');
            }
        });

        // Validasi saat form disubmit
        document.getElementById('sppForm').addEventListener('submit', function(event) {
            const yearInput = document.getElementById('year').value;
            const currentYear = new Date().getFullYear(); // 2025

            if (yearInput < currentYear) {
                event.preventDefault();
                document.getElementById('year_error').classList.remove('hidden');
                document.getElementById('year').setCustomValidity('Tahun harus tahun berjalan (2025) atau tahun ke depan.');
            }
        });
    </script>
</x-app-layout>
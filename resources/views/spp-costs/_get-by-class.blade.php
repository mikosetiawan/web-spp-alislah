<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-800 mb-4">Cari Biaya SPP Berdasarkan Kelas</h3>
    
    <form id="searchSppCostForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf
        <div>
            <label for="search_class_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select name="class_id" id="search_class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Kelas</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="search_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" name="year" id="search_year" min="2000" max="2099" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-200 h-10 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                Cari
            </button>
        </div>
    </form>

    <div id="sppCostResult" class="mt-6 hidden">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-2">Hasil Pencarian:</h4>
            <div class="flex justify-between items-center">
                <p class="text-gray-600">Biaya SPP: <span id="sppCostAmount" class="font-semibold">Rp 0</span></p>
                <button id="useThisCostBtn" class="text-sm bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded transition duration-200">
                    Gunakan Biaya Ini
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchSppCostForm');
    const resultDiv = document.getElementById('sppCostResult');
    const sppCostAmount = document.getElementById('sppCostAmount');
    const useThisCostBtn = document.getElementById('useThisCostBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const classId = document.getElementById('search_class_id').value;
        const year = document.getElementById('search_year').value;
        
        fetch("{{ route('spp-costs.get-by-class') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                class_id: classId,
                year: year
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success && data.amount) {
                sppCostAmount.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.amount);
                useThisCostBtn.dataset.amount = data.amount;
                resultDiv.classList.remove('hidden');
            } else {
                alert('Biaya SPP tidak ditemukan untuk kelas dan tahun tersebut');
                resultDiv.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mencari biaya SPP');
            resultDiv.classList.add('hidden');
        });
    });

    useThisCostBtn.addEventListener('click', function() {
        const amount = this.dataset.amount;
        document.getElementById('amount').value = amount;
        resultDiv.classList.add('hidden');
    });
});
</script>
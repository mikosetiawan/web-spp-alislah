<x-app-layout title="Tambah Kelas Baru">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900">Tambah Kelas Baru</h2>
        </div>
        
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Class Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kelas</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="grade" class="block text-sm font-medium text-gray-700">Tingkat</label>
                                <select name="grade" id="grade" class="form-select mt-1 block w-full" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>Kelas 10</option>
                                    <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>Kelas 11</option>
                                    <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>Kelas 12</option>
                                </select>
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="major" class="block text-sm font-medium text-gray-700">Jurusan</label>
                                <select name="major" id="major" class="form-select mt-1 block w-full" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($majors as $key => $value)
                                        <option value="{{ $key }}" {{ old('major') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('major')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Teacher Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Wali Kelas</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="teacher_name" class="block text-sm font-medium text-gray-700">Nama Wali Kelas</label>
                                <input type="text" name="teacher_name" id="teacher_name" value="{{ old('teacher_name') }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('teacher_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="max_students" class="block text-sm font-medium text-gray-700">Maksimal Jumlah Siswa</label>
                                <input type="number" name="max_students" id="max_students" value="{{ old('max_students') }}" 
                                    class="form-input mt-1 block w-full" required min="1">
                                @error('max_students')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('classes.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Kelas
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
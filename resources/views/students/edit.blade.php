<x-app-layout title="Edit Data Siswa">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900">Edit Data Siswa</h2>
            <p class="text-sm text-gray-500">NIS: {{ $student->nis }}</p>
        </div>
        
        <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pribadi</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                                <input type="text" name="nis" id="nis" value="{{ old('nis', $student->nis) }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('nis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <div class="mt-2 space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="L" 
                                            {{ old('gender', $student->gender) == 'L' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-blue-600" required>
                                        <span class="ml-2">Laki-laki</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="P" 
                                            {{ old('gender', $student->gender) == 'P' ? 'checked' : '' }} 
                                            class="form-radio h-4 w-4 text-pink-600">
                                        <span class="ml-2">Perempuan</span>
                                    </label>
                                </div>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $student->birth_place) }}" 
                                        class="form-input mt-1 block w-full" required>
                                    @error('birth_place')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}" 
                                        class="form-input mt-1 block w-full" required>
                                    @error('birth_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="photo" class="block text-sm font-medium text-gray-700">Foto</label>
                                @if($student->photo)
                                    <div class="flex items-center space-x-4 mb-2">
                                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                                            <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="remove_photo" class="form-checkbox h-4 w-4 text-red-600">
                                            <span class="ml-2 text-sm text-gray-600">Hapus foto</span>
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="photo" id="photo" class="form-input mt-1 block w-full">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact & Class Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontak & Kelas</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                                <select name="class_id" id="class_id" class="form-select mt-1 block w-full" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $student->phone) }}" 
                                    class="form-input mt-1 block w-full" required>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea name="address" id="address" rows="3" 
                                    class="form-textarea mt-1 block w-full" required>{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-2">Informasi Orang Tua</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="parent_name" class="block text-sm font-medium text-gray-700">Nama Orang Tua</label>
                                        <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name', $student->parent_name) }}" 
                                            class="form-input mt-1 block w-full" required>
                                        @error('parent_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="parent_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon Orang Tua</label>
                                        <input type="text" name="parent_phone" id="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" 
                                            class="form-input mt-1 block w-full" required>
                                        @error('parent_phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('students.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Perbarui Data Siswa
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
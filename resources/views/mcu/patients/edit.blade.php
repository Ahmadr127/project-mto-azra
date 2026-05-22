@extends('layouts.app')
@section('title', 'Edit Pasien')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Data Pasien MCU</h2>

            <form action="{{ route('patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">No. Rekam Medis</label>
                        <input type="text" name="patient_code" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('patient_code', $patient->patient_code) }}" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">NIK</label>
                        <input type="text" name="nik" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('nik', $patient->nik) }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pasien</label>
                        <input type="text" name="name" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('name', $patient->name) }}" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                        <select name="gender" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="">Pilih</option>
                            <option value="L" {{ old('gender', $patient->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $patient->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('birth_date', $patient->birth_date) }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Umur</label>
                        <input type="number" name="age" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('age', $patient->age) }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">No. HP</label>
                        <input type="text" name="phone" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('phone', $patient->phone) }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Perusahaan</label>
                        <input type="text" name="company" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('company', $patient->company) }}">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                        <textarea name="address" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('address', $patient->address) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('patients.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
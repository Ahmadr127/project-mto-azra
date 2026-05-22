@extends('layouts.app')
@section('title', 'Pendaftaran MCU')

@section('content')
<div class="w-full mx-auto max-w-3xl">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Pendaftaran MCU Baru</h2>

            <form action="{{ route('mcu-registrations.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pasien</label>
                    <select name="patient_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Paket MCU</label>
                    <select name="mcu_package_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} - Rp {{ number_format($package->base_price) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pendaftaran</label>
                    <input type="date" name="registration_date" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Daftar & Generate Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
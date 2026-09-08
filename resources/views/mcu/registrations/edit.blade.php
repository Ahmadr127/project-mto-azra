@extends('layouts.app')
@section('title', 'Edit Registrasi MCU')

@section('content')
<div class="w-full">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Registrasi MCU</h2>
            <form action="{{ route('mcu-registrations.update', $mcuRegistration) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pasien</label>
                    <select name="patient_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected($mcuRegistration->patient_id == $patient->id)>{{ $patient->name }} ({{ $patient->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Paket MCU</label>
                    <select name="mcu_package_id" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" @selected($mcuRegistration->mcu_package_id == $package->id)>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Registrasi</label>
                    <input type="date" name="registration_date" value="{{ old('registration_date', $mcuRegistration->registration_date ? \Carbon\Carbon::parse($mcuRegistration->registration_date)->format('Y-m-d') : '') }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="registered" @selected($mcuRegistration->status=='registered')>Registered</option>
                        <option value="in_progress" @selected($mcuRegistration->status=='in_progress')>In Progress</option>
                        <option value="completed" @selected($mcuRegistration->status=='completed')>Completed</option>
                        <option value="cancelled" @selected($mcuRegistration->status=='cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('mcu-registrations.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

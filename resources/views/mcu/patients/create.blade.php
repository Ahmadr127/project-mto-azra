@extends('layouts.app')
@section('title', 'Tambah Pasien')

@section('content')
<div class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tambah Data Pasien MCU</h2>
            <p class="text-sm text-gray-500 mt-1">Isi data pasien dengan lengkap. Umur akan terhitung otomatis dari tanggal lahir.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('mcu.patients._form', ['patient' => new \App\Models\Patient()])
        <div class="flex items-center justify-end gap-3 bg-white p-4 rounded-xl border border-gray-200 shadow-sm sticky bottom-4">
            <a href="{{ route('patients.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm">
                Simpan Pasien
            </button>
        </div>
    </form>
</div>
@endsection

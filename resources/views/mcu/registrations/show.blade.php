@extends('layouts.app')
@section('title', 'Detail Registrasi MCU')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Detail Pemeriksaan MCU: {{ $mcuRegistration->patient->name }}</h2>
        <div class="mb-6 border-b pb-4">
            <p><strong>Paket:</strong> {{ $mcuRegistration->package->name }}</p>
            <p><strong>Status:</strong> {{ $mcuRegistration->status }}</p>
        </div>

        <h3 class="font-bold text-lg mb-2">Item Pemeriksaan:</h3>
        <ul class="list-disc pl-5">
            @foreach($mcuRegistration->examLabs as $lab)
                <li>Lab: {{ $lab->lab->name }} - <span class="font-bold text-blue-600">{{ $lab->status }}</span> (Hasil: {{ $lab->result_value }})</li>
            @endforeach
            @foreach($mcuRegistration->examPhysicalExams as $fisik)
                <li>Fisik: {{ $fisik->physicalExam->name }} - <span class="font-bold text-blue-600">{{ $fisik->status }}</span></li>
            @endforeach
        </ul>
        <div class="mt-6">
            <a href="{{ route('mcu-registrations.index') }}" class="text-gray-600">Kembali</a>
        </div>
    </div>
</div>
@endsection
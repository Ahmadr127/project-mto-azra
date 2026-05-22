@extends('layouts.app')
@section('title', 'Input Hasil Radiologi')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-2">Input Hasil Radiologi</h2>
    <p class="text-gray-600 mb-6">Pasien: <span class="font-bold">{{ $mcuRegistration->patient->name }}</span> ({{ $mcuRegistration->patient->patient_code }})</p>

    <form action="{{ route("mcu-examinations.radiology.store", $mcuRegistration) }}" method="POST">
        @csrf
        <table class="min-w-full divide-y divide-gray-200 mb-6">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Pemeriksaan</th>
                    <th class="px-6 py-3 text-left">Nilai Normal</th>
                    <th class="px-6 py-3 text-left">Hasil</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mcuRegistration->examRadiologies as $exam)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $exam->radiology->name }}</td>
                    <td class="px-6 py-4 text-gray-500">
                        @if('radiology' === 'lab')
                            {{ $exam->normal_value }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="results[{{ $exam->id }}]" value="{{ $exam->result }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Masukkan hasil...">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <a href="{{ route("mcu-examinations.radiology.index") }}" class="text-gray-600 mr-4 mt-2">Batal</a>
            <button type="submit" class="bg-green-600 text-white font-bold py-2 px-4 rounded">Simpan Hasil</button>
        </div>
    </form>
</div>
@endsection
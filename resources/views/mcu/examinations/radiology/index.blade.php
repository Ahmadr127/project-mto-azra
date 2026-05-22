@extends('layouts.app')
@section('title', 'Antrian Radiologi')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-6">Antrian Radiologi</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Tgl</th>
                <th class="px-6 py-3 text-left">Pasien</th>
                <th class="px-6 py-3 text-left">Paket</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($registrations as $reg)
            <tr>
                <td class="px-6 py-4">{{ $reg->registration_date }}</td>
                <td class="px-6 py-4 font-bold">{{ $reg->patient->name }}</td>
                <td class="px-6 py-4">{{ $reg->package->name }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route("mcu-examinations.radiology.form", $reg) }}" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">Isi Hasil</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Resume Medis Akhir')

@section('content')
@php
    $columns = [
        ['key' => 'filter_patient', 'label' => 'No RM', 'type' => 'text', 'placeholder' => 'RM...', 'searchable' => true],
        ['key' => 'filter_patient', 'label' => 'Nama Pasien', 'type' => 'text', 'placeholder' => 'Nama...', 'searchable' => true],
        ['key' => 'paket', 'label' => 'Paket', 'searchable' => false],
        ['key' => 'tanggal', 'label' => 'Tanggal', 'searchable' => false],
        ['key' => 'status', 'label' => 'Status', 'searchable' => false],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Antrean Resume & Cetak Hasil</h2>
            </div>
            <p class="text-sm text-gray-500 mt-1">Filter tanggal di bawah, atau ketik No RM / Nama di kolom.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-6 mt-4 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Date Range di luar table --}}
        <x-date-range-filter from-key="filter_date_from" to-key="filter_date_to" label="Filter Tgl Registrasi (Rentang)" />

        <x-data-table :paginator="$registrations" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($registrations as $i => $reg)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $registrations->firstItem() + $i }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $reg->patient->patient_code }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $reg->patient->name }}</div>
                                <div class="text-xs text-gray-500">{{ $reg->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $reg->patient->age }} Thn</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $reg->package->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ date('d M Y', strtotime($reg->registration_date)) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($reg->status == 'completed')
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative">Selesai</span>
                                    </span>
                                @else
                                    <span class="relative inline-block px-3 py-1 font-semibold text-yellow-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                        <span class="relative">Proses</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <a href="{{ route('mcu-resumes.show', $reg->id) }}" class="text-white bg-blue-500 hover:bg-blue-700 font-bold py-1.5 px-3 rounded text-xs mr-1 inline-block shadow-sm" style="background-color: #2563eb; color: #ffffff !important;">
                                    Review
                                </a>
                                @if($reg->status == 'completed')
                                <a href="{{ route('mcu-resumes.pdf', $reg->id) }}" target="_blank" class="text-white bg-red-500 hover:bg-red-700 font-bold py-1.5 px-3 rounded text-xs inline-block shadow-sm" style="background-color: #dc2626; color: #ffffff !important;">
                                    PDF
                                </a>
                                @endif
                            </td>
                        </tr>
            @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                <p class="text-sm">Tidak ada antrean resume.</p>
                            </td>
                        </tr>
            @endforelse
        </x-data-table>
    </div>
</div>
@endsection

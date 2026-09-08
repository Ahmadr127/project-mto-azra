@extends('layouts.app')
@section('title', 'Data Pasien')

@section('content')
@php
    $columns = [
        ['key' => 'filter_patient_code', 'label' => 'No. RM', 'type' => 'text', 'placeholder' => 'Cari RM...', 'searchable' => true],
        ['key' => 'filter_name', 'label' => 'Pasien', 'type' => 'text', 'placeholder' => 'Cari nama...', 'searchable' => true],
        ['key' => 'filter_gender', 'label' => 'JK', 'type' => 'select', 'searchable' => true, 'options' => ['' => 'Semua', 'L' => 'L', 'P' => 'P']],
        ['key' => 'filter_phone', 'label' => 'Kontak', 'type' => 'text', 'placeholder' => 'Cari HP...', 'searchable' => true],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-green-600 text-white flex items-center justify-center"><i class="fas fa-hospital-user text-sm"></i></span>
                        Data Pasien MCU
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Tekan Enter di tiap kolom untuk filter. Nomor otomatis, tanpa kolom Wilayah.</p>
                </div>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm">
                    <i class="fas fa-plus text-xs"></i> Tambah Pasien
                </a>
            </div>
        </div>

        <x-data-table :paginator="$patients" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($patients as $i => $item)
                <tr class="hover:bg-green-50/40 transition">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $patients->firstItem() + $i }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <a href="{{ route('patients.show', $item) }}" class="inline-flex items-center gap-1.5 font-bold text-green-700 hover:text-green-800 text-sm">
                            <i class="fas fa-file-medical text-[11px]"></i>{{ $item->patient_code }}
                        </a>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0">
                                @if($item->photo_url)
                                    <img src="{{ $item->photo_url }}" alt="foto" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold text-gray-600">{{ strtoupper(substr($item->name,0,2)) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $item->nik ?? '-' }} @if($item->birth_place) • {{ $item->birth_place }} @endif</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $item->gender=='L' ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($item->gender=='P' ? 'bg-pink-100 text-pink-800 border border-pink-200' : 'bg-gray-100 text-gray-600 border border-gray-200') }}">
                            {{ $item->gender ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm text-gray-700">{{ $item->phone ?? '-' }}</div>
                        @if($item->emergency_phone)
                            <div class="text-xs text-orange-600">{{ $item->emergency_phone }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('patients.show', $item) }}" class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold border border-blue-200">
                                <i class="fas fa-eye mr-1 text-[10px]"></i> Detail
                            </a>
                            <a href="{{ route('patients.edit', $item) }}" class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-semibold border border-amber-200">
                                <i class="fas fa-pen mr-1 text-[10px]"></i> Edit
                            </a>
                            <form action="{{ route('patients.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pasien ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold border border-red-200">
                                    <i class="fas fa-trash mr-1 text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    <p class="text-sm">Belum ada data pasien</p>
                </td></tr>
            @endforelse
        </x-data-table>
    </div>
</div>
@endsection

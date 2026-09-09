@extends('layouts.app')
@section('title', 'Antrian Anamnesis')

@section('content')
@php
    $columns = [
        ['key' => 'filter_date', 'label' => 'Tgl Registrasi', 'searchable' => false],
        ['key' => 'filter_patient', 'label' => 'Pasien', 'type' => 'text', 'placeholder' => 'Nama / RM...', 'searchable' => true],
        ['key' => 'filter_package', 'label' => 'Paket MCU', 'type' => 'text', 'placeholder' => 'Paket...', 'searchable' => true],
        ['key' => 'status', 'label' => 'Status', 'searchable' => false],
        ['key' => 'filter_update', 'label' => 'Tgl Update', 'searchable' => false],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Modul Pemeriksaan</span>
                    <h2 class="text-xl font-bold mt-1 text-white">Antrian Anamnesis Clinical</h2>
                    <p class="text-xs mt-0.5 text-slate-400">Daftar pasien untuk wawancara anamnesis (keluhan & riwayat).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 bg-slate-800 border border-slate-700 rounded px-3 py-1.5 font-semibold">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-teal-400 mr-1.5 animate-pulse"></span>
                    {{ $registrations->total() }} Pasien Aktif
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list-check text-teal-600"></i> Daftar Antrian MCU Pasien
            </h3>
            </div>

        <x-date-range-filter from-key="filter_date_from" to-key="filter_date_to" label="" />

        <x-data-table :paginator="$registrations" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($registrations as $i => $reg)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $registrations->firstItem() + $i }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-slate-500 font-medium text-sm">
                            <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>
                            {{ \Carbon\Carbon::parse($reg->registration_date)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700 border border-slate-200 shadow-sm">
                                    {{ substr($reg->patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block tracking-tight">{{ $reg->patient->name }}</span>
                                    <span class="text-[10px] text-slate-500">RM: <span class="font-semibold text-slate-700">{{ $reg->patient->patient_code }}</span> | {{ $reg->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $reg->patient->age }} Thn</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <span class="font-semibold text-slate-700 block text-xs">{{ $reg->package->name }}</span>
                                <span class="text-[10px] text-teal-600 font-medium flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-layer-group text-[9px]"></i>
                                    {{ $reg->examAnamneses->count() }} Parameter Anamnesis
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($reg->status === 'registered')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">Terdaftar</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200 shadow-sm animate-pulse">Pemeriksaan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-slate-500 font-medium text-xs">
                            <i class="far fa-clock text-slate-400 mr-1.5"></i>
                            {{ $reg->updated_at && $reg->created_at && !$reg->updated_at->equalTo($reg->created_at) ? \Carbon\Carbon::parse($reg->updated_at)->translatedFormat('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('mcu-examinations.anamnesis.form', $reg) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-bold text-white shadow-sm hover:shadow transition-all" style="background-color: #0f766e !important;">
                                <i class="fas fa-comments-medical text-[10px]"></i> Isi Hasil Anamnesis
                            </a>
                        </td>
                    </tr>
            @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            <p class="text-sm">Tidak ada antrian pasien</p>
                        </td>
                    </tr>
            @endforelse
        </x-data-table>
    </div>
</div>
@endsection

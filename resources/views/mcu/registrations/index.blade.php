@extends('layouts.app')
@section('title', 'Registrasi MCU')

@section('content')
@php
    $columns = [
        ['key' => 'filter_registration_date', 'label' => 'Tgl Registrasi', 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'searchable' => false],
        ['key' => 'filter_patient', 'label' => 'Pasien', 'type' => 'text', 'placeholder' => 'Nama / RM...', 'searchable' => true],
        ['key' => 'filter_package', 'label' => 'Paket MCU', 'type' => 'text', 'placeholder' => 'Paket...', 'searchable' => true],
        ['key' => 'filter_status', 'label' => 'Status', 'type' => 'select', 'searchable' => true, 'options' => ['registered'=>'Registered','in_progress'=>'In Progress','completed'=>'Completed']],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Registrasi MCU</h2>
                <button type="button" @click="$dispatch('open-modal', 'mcuRegistrationModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Daftar Baru
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">Tekan Enter di tiap kolom untuk filter. Gunakan rentang tanggal di bawah untuk filter tanggal.</p>
        </div>

        {{-- Modal Form Pendaftaran --}}
        @include('mcu.registrations._modal', ['patients' => $patients ?? collect(), 'packages' => $packages ?? collect(), 'selectedPatientId' => null, 'modalId' => 'mcuRegistrationModal'])

        @if($errors->any())
            <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                Terdapat kesalahan pada form pendaftaran. Modal akan terbuka otomatis.
            </div>
        @endif

        {{-- Date Range Filter di luar table --}}
        <x-date-range-filter from-key="filter_date_from" to-key="filter_date_to" label="Filter Tgl Registrasi (Rentang)" />

        <x-data-table :paginator="$registrations" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($registrations as $i => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $registrations->firstItem() + $i }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->registration_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->patient->name }}<br><span class="text-xs text-gray-500">{{ $item->patient->patient_code }}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->package->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold @if($item->status==='completed') bg-green-100 text-green-800 @elseif($item->status==='in_progress') bg-blue-100 text-blue-800 @else bg-yellow-100 text-yellow-800 @endif">{{ $item->status }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('mcu-registrations.show', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                            <form action="{{ route('mcu-registrations.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
            @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            <p class="text-sm">Tidak ada registrasi</p>
                        </td>
                    </tr>
            @endforelse
        </x-data-table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if($errors->any())
        setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'mcuRegistrationModal' })), 400);
    @endif
});
</script>
@endpush
@endsection
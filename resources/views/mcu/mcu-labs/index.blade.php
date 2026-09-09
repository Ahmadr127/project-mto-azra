@extends('layouts.app')

@section('title', 'Kelola Master Lab')

@section('content')
@php
    $columns = [
        ['key' => 'filter_code', 'label' => 'Kode', 'type' => 'text', 'placeholder' => 'Kode...', 'searchable' => true],
        ['key' => 'filter_name', 'label' => 'Nama', 'type' => 'text', 'placeholder' => 'Nama...', 'searchable' => true],
        ['key' => 'filter_category', 'label' => 'Kategori', 'type' => 'text', 'placeholder' => 'Kategori...', 'searchable' => true],
        ['key' => 'harga', 'label' => 'Harga', 'searchable' => false],
        ['key' => 'urutan', 'label' => 'Urutan', 'searchable' => false],
        ['key' => 'filter_status', 'label' => 'Status', 'type' => 'select', 'searchable' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Kelola Master Lab</h2>
                <a href="{{ route('mcu-labs.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Tambah Master Lab
                </a>
            </div>
            <p class="text-sm text-gray-500 mt-1">Tekan Enter di tiap kolom untuk filter.</p>
        </div>

        <x-data-table :paginator="$mcu_labs" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($mcu_labs as $i => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $mcu_labs->firstItem() + $i }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->code }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->category ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->display_order }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item->status)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('mcu-labs.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('mcu-labs.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
            @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            <p class="text-sm">Tidak ada data</p>
                        </td>
                    </tr>
            @endforelse
        </x-data-table>
    </div>
</div>
@endsection

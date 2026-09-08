@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
@php
    $columns = [
        ['key' => 'filter_name', 'label' => 'Nama', 'type' => 'text', 'placeholder' => 'Cari nama...', 'searchable' => true],
        ['key' => 'filter_nik', 'label' => 'NIK', 'type' => 'text', 'placeholder' => 'NIK...', 'searchable' => true],
        ['key' => 'filter_username', 'label' => 'Username', 'type' => 'text', 'placeholder' => 'Username...', 'searchable' => true],
        ['key' => 'filter_email', 'label' => 'Email', 'type' => 'text', 'placeholder' => 'Email...', 'searchable' => true],
        ['key' => 'filter_role', 'label' => 'Role', 'type' => 'text', 'placeholder' => 'Role...', 'searchable' => true],
        ['key' => 'filter_created_at', 'label' => 'Tanggal Dibuat', 'type' => 'date', 'placeholder' => '', 'searchable' => true],
        ['key' => 'aksi', 'label' => 'Aksi', 'searchable' => false],
    ];
@endphp

<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        {{-- Header --}}
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-600 text-white">
                            <i class="fas fa-users text-sm"></i>
                        </span>
                        Kelola Users
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Tekan Enter di tiap kolom untuk filter. Gunakan Filter untuk cari manual.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search','filter_name','filter_nik','filter_username','filter_email','filter_role','filter_created_at','date_from','date_to']))
                        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-eraser text-xs"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm transition">
                        <i class="fas fa-plus text-xs"></i> Tambah User
                    </a>
                </div>
            </div>
        </div>

        <x-data-table :paginator="$users" :columns="$columns" :showNumber="true" searchMode="manual">
            @forelse($users as $i => $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $users->firstItem() + $i }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-9 w-9">
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center shadow-sm">
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 hidden sm:block">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $user->nik ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->username }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($user->role)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($user->role->name === 'admin') bg-red-100 text-red-800 border border-red-200
                                @else bg-emerald-100 text-emerald-800 border border-emerald-200 @endif">
                                <i class="fas fa-shield-halved mr-1 text-[10px]"></i>{{ $user->role->display_name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Tidak ada role</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                        {{ $user->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold border border-indigo-200">
                                <i class="fas fa-pen mr-1 text-[10px]"></i> Edit
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold border border-red-200">
                                        <i class="fas fa-trash mr-1 text-[10px]"></i> Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 px-2">(Anda)</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p class="text-sm font-medium">Tidak ada data ditemukan</p>
                            <p class="text-xs mt-1">Coba ubah filter atau tambah user baru.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-data-table>
    </div>
</div>
@endsection

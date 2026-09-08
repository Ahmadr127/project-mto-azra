@extends('layouts.app')
@section('title', 'Data Pasien')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-green-600 text-white flex items-center justify-center"><i class="fas fa-hospital-user text-sm"></i></span>
                        Data Pasien MCU
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Daftar pasien terdaftar. Klik No. RM untuk melihat detail.</p>
                </div>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm">
                    <i class="fas fa-plus text-xs"></i> Tambah Pasien
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('patients.index') }}" class="p-4 border-b border-gray-200 bg-gray-50">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau No. RM..."
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('patients.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. RM</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Umur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">JK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kontak</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $item)
                    <tr class="hover:bg-green-50/40 transition">
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
                            <div class="text-sm text-gray-900">{{ $item->age_formatted }}</div>
                            <div class="text-xs text-gray-500">{{ $item->birth_date ? $item->birth_date->format('d/m/Y') : '-' }}</div>
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
                </tbody>
            </table>
        </div>

        <x-data-table :paginator="$patients" />
    </div>
</div>
@endsection

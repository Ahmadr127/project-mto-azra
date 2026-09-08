@extends('layouts.app')
@section('title', 'Detail Pasien')

@section('content')
<div class="w-full space-y-6" x-data="{ photoOpen:false }">
    {{-- Breadcrumb & Actions - tanpa icon --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('patients.index') }}" class="hover:text-green-700">Data Pasien</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-900 font-medium">{{ $patient->patient_code }}</span>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">Detail Pasien</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('patients.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
            <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold">
                Edit
            </a>
            @if(auth()->user()->hasPermission('manage_mcu_registrations'))
                <button type="button" @click="$dispatch('open-modal', 'mcuRegistrationModalPatient')" class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold shadow-sm">
                    Daftarkan MCU
                </button>
            @else
                <span class="inline-flex items-center gap-2 px-5 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm font-semibold border border-gray-200 cursor-not-allowed" title="Butuh permission manage_mcu_registrations">
                    Daftarkan MCU
                </span>
            @endif
        </div>
    </div>

    {{-- Biodata menyatu dengan foto (lebih lebar), Alamat lebih sempit di kiri --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Alamat Lengkap kiri - lebih sempit --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900">Alamat Lengkap</h3>
                    <p class="text-xs text-gray-500">Alamat utama dan wilayah administratif.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alamat</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 leading-relaxed">{{ $patient->address ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500">Kelurahan / Desa</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $patient->kelurahan ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500">Kecamatan</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $patient->kecamatan ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500">Kabupaten / Kota</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $patient->kabupaten_kota ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500">Provinsi</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $patient->provinsi ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500">Kode Pos</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $patient->kode_pos ?? '-' }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <p class="text-xs text-green-700 font-semibold">Alamat Lengkap (gabungan)</p>
                            <p class="text-sm font-medium text-gray-900">{{ $patient->full_address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biodata Lengkap menyatu dengan foto - lebih lebar --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900">Biodata Lengkap</h3>
                    <p class="text-xs text-gray-500">Informasi utama pasien — foto menyatu di kanan.</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6">
                        {{-- Fields --}}
                        <div class="flex-1">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Lengkap</dt>
                                    <dd class="mt-1 text-base font-bold text-gray-900">{{ $patient->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">No. RM</dt>
                                    <dd class="mt-1 font-mono font-semibold text-green-700 bg-green-50 border border-green-200 inline-flex items-center px-2.5 py-1 rounded-full text-sm">{{ $patient->patient_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">NIK</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->nik ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Jenis Kelamin</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $patient->gender=='L' ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($patient->gender=='P' ? 'bg-pink-100 text-pink-800 border border-pink-200' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $patient->gender=='L' ? 'Laki-laki' : ($patient->gender=='P' ? 'Perempuan' : '-') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Tempat, Tanggal Lahir</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->birth_place ? $patient->birth_place . ', ' : '' }}{{ $patient->birth_date ? $patient->birth_date->format('d F Y') : '-' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs text-gray-500">Umur</dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        <span class="inline-flex items-center bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-semibold">{{ $patient->age_formatted }}</span>
                                        @if($patient->age_detailed)
                                            <span class="ml-2 text-xs text-gray-500">({{ $patient->age_detailed['years'] }} th, {{ $patient->age_detailed['months'] }} bln, {{ $patient->age_detailed['days'] }} hr)</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">No. HP / Telepon</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Nomor Darurat</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->emergency_phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">BPJS</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->bpjs ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Departemen</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->department ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Status Kepegawaian</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->employee_status ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Terdaftar</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $patient->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                        {{-- Foto menyatu di kanan --}}
                        <div class="flex flex-col items-center lg:w-40 flex-shrink-0">
                            @if($patient->photo_url)
                                <button type="button" @click="photoOpen=true" class="group relative w-32 h-32 rounded-xl overflow-hidden border-2 border-gray-200 shadow-sm bg-gray-50 flex items-center justify-center hover:border-green-300 transition cursor-pointer">
                                    <img src="{{ $patient->photo_url }}" alt="Foto {{ $patient->name }}" class="w-full h-full object-cover group-hover:opacity-90 transition">
                                    <span class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="bg-white/90 text-gray-700 text-xs font-semibold px-2 py-1 rounded-full">Klik perbesar</span>
                                    </span>
                                </button>
                            @else
                                <div class="w-32 h-32 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                                    <span class="text-2xl">—</span>
                                    <span class="text-xs mt-1">Tidak ada foto</span>
                                </div>
                            @endif
                            <p class="mt-3 text-xs text-gray-400 text-center">Klik foto untuk memperbesar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal foto --}}
    <div x-show="photoOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70" @click="photoOpen=false">
        <div class="relative max-w-2xl w-full" @click.stop>
            <button @click="photoOpen=false" class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 shadow">×</button>
            @if($patient->photo_url)
                <img src="{{ $patient->photo_url }}" alt="Foto {{ $patient->name }}" class="w-full h-auto max-h-[85vh] object-contain rounded-xl shadow-2xl bg-white">
            @endif
        </div>
    </div>

    {{-- Riwayat MCU --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Riwayat Pendaftaran MCU</h3>
                <p class="text-xs text-gray-500">{{ $patient->mcuRegistrations->count() }} riwayat ditemukan</p>
            </div>
            @if(auth()->user()->hasPermission('manage_mcu_registrations'))
                <button type="button" @click="$dispatch('open-modal', 'mcuRegistrationModalPatient')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-xs font-semibold">
                    Baru
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            @if($patient->mcuRegistrations->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm font-medium text-gray-500">Belum ada pendaftaran MCU</p>
                    <p class="text-xs text-gray-400 mt-1">Klik "Daftarkan MCU" untuk membuat pendaftaran baru.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Paket</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($patient->mcuRegistrations->sortByDesc('registration_date') as $reg)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-xs font-mono text-gray-600">{{ $reg->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($reg->registration_date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $reg->package->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($reg->status=='registered') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($reg->status=='in_progress') bg-amber-100 text-amber-800 border border-amber-200
                                    @elseif($reg->status=='completed') bg-green-100 text-green-800 border border-green-200
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($reg->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('mcu-registrations.show', $reg) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Modal Pendaftaran MCU --}}
    @include('mcu.registrations._modal', ['patients' => $patients ?? collect(), 'packages' => $packages ?? collect(), 'selectedPatientId' => $patient->id, 'modalId' => 'mcuRegistrationModalPatient'])
</div>
@endsection

@extends('layouts.app')
@section('title', 'Antrian Anamnesis')

@section('content')
<div class="w-full mx-auto max-w-7xl px-2">
    <!-- Modern Clinical Header Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-comments-medical text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Modul Pemeriksaan</span>
                    <h2 class="text-xl font-bold mt-1 text-white">Antrian Anamnesis Clinical</h2>
                    <p class="text-xs mt-0.5 text-slate-400">Daftar pasien aktif yang dijadwalkan untuk wawancara anamnesis (keluhan & riwayat penyakit).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 bg-slate-800 border border-slate-700 rounded px-3 py-1.5 font-semibold">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-teal-400 mr-1.5 animate-pulse"></span>
                    {{ $registrations->count() }} Pasien Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Main Queue Card -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list-check text-teal-600"></i>
                Daftar Antrian MCU Pasien
            </h3>
            
            <!-- Quick Search Filter -->
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                </span>
                <input type="text" id="queueSearch" onkeyup="filterQueue()" class="w-full bg-white border border-slate-300 rounded-md pl-9 pr-3 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors" placeholder="Cari nama pasien atau No RM...">
            </div>
        </div>

        @if($registrations->isEmpty())
        <div class="p-12 text-center text-slate-500 flex flex-col items-center justify-center bg-white">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-slate-200 shadow-inner">
                <i class="fas fa-users-slash text-2xl text-slate-400"></i>
            </div>
            <h4 class="text-slate-800 font-bold text-base mb-1">Tidak Ada Antrian Pasien</h4>
            <p class="text-slate-500 text-xs max-w-md leading-relaxed">Saat ini tidak ada pasien dalam antrian terdaftar atau sedang dalam proses pemeriksaan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs text-slate-700" id="queueTable">
                <thead class="bg-slate-100 text-slate-600 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 w-2/12">Tanggal Registrasi</th>
                        <th class="px-6 py-3.5 w-4/12">Informasi Pasien</th>
                        <th class="px-6 py-3.5 w-3/12">Paket MCU</th>
                        <th class="px-6 py-3.5 w-1.5/12 text-center">Status Reg</th>
                        <th class="px-6 py-3.5 w-1.5/12 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($registrations as $reg)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Tanggal -->
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-medium">
                            <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>
                            {{ \Carbon\Carbon::parse($reg->registration_date)->translatedFormat('d M Y') }}
                        </td>
                        
                        <!-- Pasien -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700 border border-slate-200 shadow-sm">
                                    {{ substr($reg->patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block tracking-tight search-target">{{ $reg->patient->name }}</span>
                                    <span class="text-[10px] text-slate-500 search-target">RM: <span class="font-semibold text-slate-700">{{ $reg->patient->patient_code }}</span> | {{ $reg->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $reg->patient->age }} Thn</span>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Paket -->
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-semibold text-slate-700 block text-xs">{{ $reg->package->name }}</span>
                                <span class="text-[10px] text-teal-600 font-medium flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-layer-group text-[9px]"></i>
                                    {{ $reg->examAnamneses->count() }} Parameter Anamnesis
                                </span>
                            </div>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($reg->status === 'registered')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                                    Terdaftar
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-50 text-teal-700 border border-teal-200 shadow-sm animate-pulse">
                                    Pemeriksaan
                                </span>
                            @endif
                        </td>
                        
                        <!-- Aksi -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('mcu-examinations.anamnesis.form', $reg) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-bold text-white shadow-sm hover:shadow transition-all" style="background-color: #0f766e !important; hover:background-color: #0d9488 !important;">
                                <i class="fas fa-comments-medical text-[10px]"></i>
                                Isi Hasil Anamnesis
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<script>
    function filterQueue() {
        const input = document.getElementById('queueSearch');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('queueTable');
        if (!table) return;
        const trs = table.getElementsByTagName('tr');

        // Loop through all table rows, and hide those who don't match the search query
        for (let i = 1; i < trs.length; i++) {
            const tr = trs[i];
            const targets = tr.getElementsByClassName('search-target');
            let match = false;
            for (let j = 0; j < targets.length; j++) {
                if (targets[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
            if (match) {
                tr.style.display = "";
            } else {
                tr.style.display = "none";
            }
        }
    }
</script>
@endsection
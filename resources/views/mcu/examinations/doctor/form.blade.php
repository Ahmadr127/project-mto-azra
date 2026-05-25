@extends('layouts.app')
@section('title', 'Input Hasil Konsultasi Dokter')

@section('content')
<div class="w-full mx-auto">
    <!-- Modern Clinical Patient Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-user-md text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Proses MCU Konsultasi Dokter</span>
                    <h2 class="text-xl font-bold mt-1 text-white">{{ $mcuRegistration->patient->name }}</h2>
                    <p class="text-xs mt-0.5 text-slate-300">
                        No RM: <span class="font-semibold text-white">{{ $mcuRegistration->patient->patient_code }}</span> | 
                        NIK: <span class="font-semibold text-white">{{ $mcuRegistration->patient->nik ?? '-' }}</span> | 
                        <span class="text-slate-200">{{ $mcuRegistration->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $mcuRegistration->patient->age }} Tahun</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <div class="rounded px-4 py-2 text-xs min-w-[150px] border" style="background-color: #0f172a !important; border-color: #334155 !important;">
                    <span class="text-[10px] uppercase block font-bold text-slate-500">Kd. Booking</span>
                    <span class="font-extrabold text-sm block mt-0.5" style="color: #2dd4bf !important;">{{ $mcuRegistration->registration_code ?? 'R' . str_pad($mcuRegistration->id, 8, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="rounded px-4 py-2 text-xs min-w-[150px] border" style="background-color: #0f172a !important; border-color: #334155 !important;">
                    <span class="text-[10px] uppercase block font-bold text-slate-500">Paket & RS Unit</span>
                    <span class="font-bold block mt-0.5 text-xs truncate max-w-[140px] text-slate-300">{{ $mcuRegistration->package->name }}</span>
                    <span class="text-[10px] text-slate-400">RS AZRA Bogor</span>
                </div>
            </div>
        </div>
    </div>

    @if($mcuRegistration->examMedicalActions->isEmpty())
    <div class="bg-white rounded-lg border border-slate-200 shadow p-12 text-center text-slate-500 mb-6 flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-slate-200 shadow-inner">
            <i class="fas fa-clipboard-list text-2xl text-slate-400"></i>
        </div>
        <h4 class="text-slate-800 font-bold text-lg mb-1">Konsultasi / Tindakan Dokter Belum Dikonfigurasi</h4>
        <p class="text-slate-500 text-sm max-w-md leading-relaxed">
            Tidak ada komponen konsultasi dokter dalam paket yang dipilih. Silakan edit **Master Paket MCU**, centang item konsultasi dokter yang diinginkan, lalu lakukan registrasi ulang pasien.
        </p>
    </div>
    @else

    <!-- Main Entry Form -->
    <form action="{{ route('mcu-examinations.doctor.store', $mcuRegistration) }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg border border-slate-300 shadow-sm mb-6 overflow-hidden">
            <!-- Category Header -->
            <div class="px-4 py-3 font-bold text-xs uppercase tracking-wider flex items-center justify-between text-white" style="background-color: #334155 !important;">
                <span class="flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-user-md" style="color: #2dd4bf !important;"></i>
                    Daftar Pemeriksaan & Konsultasi Dokter
                </span>
                <span class="text-[10px] font-semibold text-slate-300 tracking-wide bg-slate-700/60 px-2.5 py-0.5 rounded border border-slate-600/50">
                    {{ $mcuRegistration->examMedicalActions->count() }} Tindakan
                </span>
            </div>

            <!-- Parameters Input Fields -->
            <div class="p-4 bg-slate-50">
                <div class="space-y-4">
                    @foreach($mcuRegistration->examMedicalActions as $exam)
                        <div class="bg-white border border-slate-200 rounded-lg p-4 hover:border-teal-400 transition-all shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="w-full md:w-4/12 flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-invoice-dollar text-teal-600 text-xs"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-xs uppercase tracking-tight block">
                                        {{ $exam->medicalAction->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block mt-0.5">
                                        Kode: {{ $exam->medicalAction->code }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Input box -->
                            <div class="w-full md:w-8/12">
                                <input type="hidden" name="exam_ids[]" value="{{ $exam->id }}">
                                <textarea 
                                    name="results[{{ $exam->id }}]" 
                                    rows="2"
                                    placeholder="Masukkan hasil pemeriksaan konsultasi dokter atau resep..."
                                    class="w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-xs text-slate-800 font-semibold focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors shadow-inner"
                                >{{ old('results.'.$exam->id, $exam->result ?: 'Dalam Batas Normal') }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form Action Footer -->
        <div class="mt-8 bg-white border border-slate-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm mb-12">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-halved text-teal-600 text-lg"></i>
                <div class="text-left">
                    <span class="text-xs font-bold text-slate-700 block">Validasi Pengisian Hasil</span>
                    <span class="text-[10px] text-slate-500 block">Pastikan semua data konsultasi dokter telah diinputkan dengan benar sebelum disimpan.</span>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('mcu-examinations.doctor.index') }}" class="w-full sm:w-auto text-center px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-md text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-md text-xs font-extrabold shadow transition-all flex items-center justify-center gap-1.5">
                    <i class="fas fa-circle-check"></i>
                    Simpan Hasil Konsultasi
                </button>
            </div>
        </div>

    </form>
    @endif
</div>
@endsection
@extends('layouts.app')
@section('title', 'Input Hasil Laboratorium')

@section('content')
@php
    // Index category styling and metadata
    $categoryOrder = [
        'Hematologi Lengkap' => [
            'icon' => 'fa-droplet', 
            'color' => 'border-rose-500 text-rose-500 bg-rose-50',
            'desc' => 'Darah Lengkap & Hitung Jenis'
        ],
        'Urine' => [
            'icon' => 'fa-prescription-bottle', 
            'color' => 'border-amber-500 text-amber-500 bg-amber-50',
            'desc' => 'Karakteristik & Kimia Urine'
        ],
        'Urine Sedimen' => [
            'icon' => 'fa-microscope', 
            'color' => 'border-emerald-500 text-emerald-500 bg-emerald-50',
            'desc' => 'Mikroskopis Urine Sedimen'
        ],
        'Fungsi Liver' => [
            'icon' => 'fa-lungs', 
            'color' => 'border-pink-500 text-pink-500 bg-pink-50',
            'desc' => 'Fungsi Hati / Liver Panel'
        ],
        'Lipid Profil' => [
            'icon' => 'fa-circle-half-stroke', 
            'color' => 'border-violet-500 text-violet-500 bg-violet-50',
            'desc' => 'Profil Kolesterol & Lemak Darah'
        ],
        'Fungsi Ginjal' => [
            'icon' => 'fa-shield-halved', 
            'color' => 'border-blue-500 text-blue-500 bg-blue-50',
            'desc' => 'Laju Filtrasi & Fungsi Ginjal'
        ],
        'Glukosa Darah' => [
            'icon' => 'fa-dna', 
            'color' => 'border-cyan-500 text-cyan-500 bg-cyan-50',
            'desc' => 'Kadar Gula & Manajemen Diabetes'
        ],
        'Imunologi' => [
            'icon' => 'fa-shield-virus', 
            'color' => 'border-orange-500 text-orange-500 bg-orange-50',
            'desc' => 'Penanda Tumor & Imunologis'
        ],
        'Lainnya' => [
            'icon' => 'fa-circle-nodes', 
            'color' => 'border-slate-500 text-slate-500 bg-slate-50',
            'desc' => 'Pemeriksaan Tambahan Lainnya'
        ],
    ];

    // Grouping the patient's exam labs
    $groupedExams = $mcuRegistration->examLabs->groupBy(function($exam) {
        return $exam->lab->category ?? 'Lainnya';
    });
@endphp

<div class="w-full mx-auto">
    <!-- Modern Clinical Patient Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-flask text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Proses MCU Laboratorium</span>
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

    @if($mcuRegistration->examLabs->isEmpty())
    <div class="bg-white rounded-lg border border-slate-200 shadow p-12 text-center text-slate-500 mb-6 flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-slate-200 shadow-inner">
            <i class="fas fa-clipboard-list text-2xl text-slate-400"></i>
        </div>
        <h4 class="text-slate-800 font-bold text-lg mb-1">Pemeriksaan Lab Belum Dikonfigurasi</h4>
        <p class="text-slate-500 text-sm max-w-md leading-relaxed">
            Tidak ada komponen laboratorium dalam paket yang dipilih. Silakan edit **Master Paket MCU**, centang item laboratorium yang diinginkan, lalu lakukan registrasi ulang pasien.
        </p>
    </div>
    @else

    <!-- Main Entry Form -->
    <form action="{{ route('mcu-examinations.lab.store', $mcuRegistration) }}" method="POST">
        @csrf

        @foreach($categoryOrder as $categoryName => $meta)
            @php
                $exams = $groupedExams->get($categoryName);
            @endphp

            @if($exams && $exams->count() > 0)
                @php
                    // Separate normal parameters and the category summary/rekapitulasi
                    $normalExams = $exams->filter(function($exam) {
                        return !str_ends_with($exam->lab->code, '-KES');
                    })->sortBy('lab.display_order');

                    $summaryExam = $exams->first(function($exam) {
                        return str_ends_with($exam->lab->code, '-KES');
                    });
                @endphp

                <div class="bg-white rounded-lg border border-slate-300 shadow-sm mb-6 overflow-hidden">
                    <!-- Category Header -->
                    <div class="px-4 py-3 font-bold text-xs uppercase tracking-wider flex items-center justify-between text-white" style="background-color: #334155 !important;">
                        <span class="flex items-center gap-2 text-sm font-bold">
                            <i class="fas {{ $meta['icon'] }}" style="color: #2dd4bf !important;"></i>
                            {{ $categoryName }}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-300 tracking-wide bg-slate-700/60 px-2.5 py-0.5 rounded border border-slate-600/50">
                            {{ $meta['desc'] }}
                        </span>
                    </div>

                    <!-- Parameters Input Grid -->
                    <div class="p-4">
                        @if($normalExams->isEmpty())
                            <p class="text-xs text-slate-400 italic">Hanya memiliki parameter rekapitulasi.</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($normalExams as $exam)
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 hover:border-teal-400 transition-all flex flex-col justify-between shadow-xs">
                                        <div class="flex justify-between items-start gap-1 mb-2">
                                            <span class="font-bold text-slate-700 text-[11px] tracking-tight leading-tight" title="{{ $exam->lab->name }}">
                                                {{ $exam->lab->name }}
                                            </span>
                                            <span class="text-[8px] font-bold text-slate-400 px-1 py-0.5 bg-slate-200/50 rounded border border-slate-300/40 uppercase tracking-wider">
                                                {{ $exam->lab->code }}
                                            </span>
                                        </div>
                                        
                                        <!-- Input box -->
                                        <div class="relative">
                                            <input type="hidden" name="exam_ids[]" value="{{ $exam->id }}">
                                            <input type="text" 
                                                   name="results[{{ $exam->id }}]" 
                                                   value="{{ old('results.'.$exam->id, $exam->result_value) }}"
                                                   placeholder="Nilai..."
                                                   class="w-full bg-white border border-slate-300 rounded px-2.5 py-1.5 text-xs text-slate-800 font-bold focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors shadow-inner"
                                            >
                                        </div>

                                        <!-- Range Info & Unit -->
                                        <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex flex-col gap-0.5 text-[10px] text-slate-500">
                                            <span class="block truncate" title="Nilai Rujukan: {{ $exam->normal_value }}">
                                                <i class="fas fa-circle-info text-[9px] mr-1 text-slate-400"></i>
                                                Rujukan: <span class="font-medium text-slate-700">{{ $exam->normal_value ?: '-' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Category Rekapitulasi / Summary -->
                        @if($summaryExam)
                            <div class="mt-5 pt-4 border-t border-slate-200/80 bg-teal-50/20 -mx-4 -mb-4 p-4 rounded-b-lg">
                                <label class="block text-slate-800 text-[11px] font-bold uppercase mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-square-poll-horizontal text-teal-600 text-sm"></i>
                                    {{ $summaryExam->lab->name }}
                                    <span class="bg-teal-100 text-teal-800 px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold">REKAPITULASI SUMMARY</span>
                                </label>
                                <input type="hidden" name="exam_ids[]" value="{{ $summaryExam->id }}">
                                <textarea 
                                    name="results[{{ $summaryExam->id }}]" 
                                    rows="2"
                                    placeholder="Tuliskan temuan abnormal atau 'Dalam Batas Normal'..."
                                    class="w-full bg-white border border-slate-300 rounded px-3 py-2 text-xs text-slate-800 font-semibold focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors shadow-inner"
                                >{{ old('results.'.$summaryExam->id, $summaryExam->result_value ?: 'Dalam Batas Normal') }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Form Action Footer -->
        <div class="mt-8 bg-white border border-slate-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm mb-12">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-halved text-teal-600 text-lg"></i>
                <div class="text-left">
                    <span class="text-xs font-bold text-slate-700 block">Validasi Pengisian Hasil</span>
                    <span class="text-[10px] text-slate-500 block">Pastikan semua data laboratorium telah diinputkan dengan benar sebelum disimpan.</span>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('mcu-examinations.lab.index') }}" class="w-full sm:w-auto text-center px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-md text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-md text-xs font-extrabold shadow transition-all flex items-center justify-center gap-1.5">
                    <i class="fas fa-circle-check"></i>
                    Simpan Hasil Lab
                </button>
            </div>
        </div>

    </form>
    @endif
</div>
@endsection
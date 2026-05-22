@extends('layouts.app')
@section('title', 'Input Hasil Anamnesis')

@section('content')
@php
    // Index the exams by their master anamnesis code so we can fetch them instantly by code
    $examsByCode = $mcuRegistration->examAnamneses->keyBy(function($exam) {
        return trim($exam->anamnesis->code);
    });

    // Helper function to safely output input fields
    $getInputHtml = function($code, $placeholder = 'Ketik hasil...') use ($examsByCode) {
        $exam = $examsByCode->get($code);
        if (!$exam) return '<span class="text-xs text-gray-400 italic">Komponen tidak aktif</span>';
        
        $value = old('results.'.$exam->id, $exam->result);
        return '
            <input type="hidden" name="exam_ids[]" value="' . $exam->id . '">
            <input type="text" 
                   name="results[' . $exam->id . ']" 
                   value="' . htmlspecialchars($value) . '" 
                   class="w-full bg-white border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors" 
                   placeholder="' . htmlspecialchars($placeholder) . '">';
    };

    // Helper function to render family dropdown (1-5)
    $getFamilyDropdownHtml = function($code) use ($examsByCode) {
        $exam = $examsByCode->get($code);
        if (!$exam) return '<span class="text-xs text-gray-400 italic">Komponen tidak aktif</span>';
        
        $value = old('results.'.$exam->id, $exam->result ?: 'Tidak');
        $options = ['Tidak', 'Tidak Tahu', 'Ya (Ayah)', 'Ya (Ibu)', 'Ya (Ayah & Ibu)'];
        
        $selectHtml = '<input type="hidden" name="exam_ids[]" value="' . $exam->id . '">';
        $selectHtml .= '<select name="results[' . $exam->id . ']" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors">';
        foreach ($options as $option) {
            $selected = ($value === $option) ? 'selected' : '';
            $selectHtml .= '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
        }
        $selectHtml .= '</select>';
        return $selectHtml;
    };

    // Helper function to render personal dropdown (Tidak/Ya)
    $getPersonalDropdownHtml = function($code) use ($examsByCode) {
        $exam = $examsByCode->get($code);
        if (!$exam) return '<span class="text-xs text-gray-400 italic">Komponen tidak aktif</span>';
        
        $value = old('results.'.$exam->id, $exam->result ?: 'Tidak');
        $options = ['Tidak', 'Ya'];
        
        $selectHtml = '<input type="hidden" name="exam_ids[]" value="' . $exam->id . '">';
        $selectHtml .= '<select name="results[' . $exam->id . ']" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors">';
        foreach ($options as $option) {
            $selected = ($value === $option) ? 'selected' : '';
            $selectHtml .= '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
        }
        $selectHtml .= '</select>';
        return $selectHtml;
    };

    // Helper function to render death dropdown (No 7)
    $getDeathDropdownHtml = function($code) use ($examsByCode) {
        $exam = $examsByCode->get($code);
        if (!$exam) return '<span class="text-xs text-gray-400 italic">Komponen tidak aktif</span>';
        
        $value = old('results.'.$exam->id, $exam->result ?: 'Tidak');
        $options = ['Tidak', 'Ya (Ayah)', 'Ya (Ibu)', 'Ya (Ayah & Ibu)'];
        
        $selectHtml = '<input type="hidden" name="exam_ids[]" value="' . $exam->id . '">';
        $selectHtml .= '<select name="results[' . $exam->id . ']" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors">';
        foreach ($options as $option) {
            $selected = ($value === $option) ? 'selected' : '';
            $selectHtml .= '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
        }
        $selectHtml .= '</select>';
        return $selectHtml;
    };
@endphp

<div class="w-full mx-auto max-w-7xl px-2">
    
    <!-- Modern Clinical Patient Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-user-md text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Proses MCU Anamnesis</span>
                    <h2 class="text-xl font-bold mt-1" style="color: #ffffff !important;">{{ $mcuRegistration->patient->name }}</h2>
                    <p class="text-xs mt-0.5" style="color: #94a3b8 !important;">
                        No RM: <span class="font-semibold" style="color: #cbd5e1 !important;">{{ $mcuRegistration->patient->patient_code }}</span> | 
                        NIK: <span class="font-semibold" style="color: #cbd5e1 !important;">{{ $mcuRegistration->patient->nik ?? '-' }}</span> | 
                        <span style="color: #cbd5e1 !important;">{{ $mcuRegistration->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $mcuRegistration->patient->age }} Tahun</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <div class="rounded px-4 py-2 text-xs min-w-[150px] border" style="background-color: #0f172a !important; border-color: #334155 !important;">
                    <span class="text-[10px] uppercase block font-bold" style="color: #64748b !important;">Kd. Booking</span>
                    <span class="font-extrabold text-sm block mt-0.5" style="color: #2dd4bf !important;">{{ $mcuRegistration->registration_code ?? 'R' . str_pad($mcuRegistration->id, 8, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="rounded px-4 py-2 text-xs min-w-[150px] border" style="background-color: #0f172a !important; border-color: #334155 !important;">
                    <span class="text-[10px] uppercase block font-bold" style="color: #64748b !important;">Paket & RS Unit</span>
                    <span class="font-bold block mt-0.5 text-xs truncate max-w-[140px]" style="color: #cbd5e1 !important;">{{ $mcuRegistration->package->name }}</span>
                    <span class="text-[10px]" style="color: #94a3b8 !important;">RS AZRA Bogor</span>
                </div>
            </div>
        </div>
    </div>

    @if($mcuRegistration->examAnamneses->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 shadow p-12 text-center text-gray-500 mb-6 flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-gray-200 shadow-inner">
            <i class="fas fa-clipboard-list text-2xl text-slate-400"></i>
        </div>
        <h4 class="text-slate-800 font-bold text-lg mb-1">Pemeriksaan Anamnesis Belum Dikonfigurasi</h4>
        <p class="text-gray-500 text-sm max-w-md leading-relaxed">
            Tidak ada komponen pertanyaan anamnesis dalam paket yang dipilih. Silakan edit **Master Paket MCU**, centang item anamnesis yang diinginkan, lalu lakukan registrasi ulang pasien.
        </p>
    </div>
    @else

    <!-- Anamnesis Form -->
    <form action="{{ route('mcu-examinations.anamnesis.store', $mcuRegistration) }}" method="POST">
        @csrf

        <!-- 1. KOREKSI DOKTER -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-user-doctor" style="color: #2dd4bf !important;"></i>
                Koreksi Anamnesa / Keluhan dari Pasien Oleh Dokter
            </div>
            <div class="p-3 bg-slate-50">
                {!! $getInputHtml('ANM-KD-001', 'Ketik koreksi anamnesa atau keluhan di sini...') !!}
            </div>
        </div>

        <!-- 2. RIWAYAT KESEHATAN (KELUHAN KERJA) -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-heart-pulse" style="color: #2dd4bf !important;"></i>
                Riwayat Kesehatan / Keluhan Pasien Saat Ini
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 text-[11px] font-bold uppercase mb-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Keluhan Pasien saat ini <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[9px] lowercase font-bold ml-1">Dalam Pekerjaan</span>
                    </label>
                    {!! $getInputHtml('ANM-KP-001', 'Tidak ada keluhan') !!}
                </div>
                <div>
                    <label class="block text-slate-700 text-[11px] font-bold uppercase mb-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Keluhan Pasien saat ini <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[9px] lowercase font-bold ml-1">Di Luar Pekerjaan</span>
                    </label>
                    {!! $getInputHtml('ANM-KP-002', 'Tidak ada keluhan') !!}
                </div>
            </div>
        </div>

        <!-- 3. DOUBLE COLUMN: RIWAYAT PENYAKIT KELUARGA & PRIBADI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
            
            <!-- LEFT COLUMN: RIWAYAT PENYAKIT KELUARGA -->
            <div class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center justify-between text-white" style="background-color: #334155 !important;">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-users-medical" style="color: #2dd4bf !important;"></i>
                        1. Riwayat Penyakit Keluarga
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded text-slate-300" style="background-color: #475569 !important;">Dropdown 1-5</span>
                </div>
                
                <div class="p-3 flex-grow bg-slate-50 text-xs">
                    <table class="w-full text-left border-collapse">
                        <tbody>
                            <!-- General Status row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 font-semibold text-slate-700 w-7/12">1. Riwayat Penyakit Keluarga</td>
                                <td class="py-2 px-1 text-slate-400 w-1/12 text-center text-gray-400 w-12">:</td>
                                <td class="py-2 pl-2 w-4/12">{!! $getInputHtml('ANM-RK-001', 'Tidak') !!}</td>
                            </tr>
                            <!-- Hipertensi row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4 w-7/12">1. Tekanan Darah Tinggi (Hipertensi)</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2 w-4/12">{!! $getFamilyDropdownHtml('ANM-RK-002') !!}</td>
                            </tr>
                            <!-- Kencing Manis row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">2. Kencing Manis (Diabetes Melitus)</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getFamilyDropdownHtml('ANM-RK-003') !!}</td>
                            </tr>
                            <!-- Jantung row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">3. Penyakit Jantung dan Pembuluh Darah</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getFamilyDropdownHtml('ANM-RK-004') !!}</td>
                            </tr>
                            <!-- Kanker row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">4. Kanker</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getFamilyDropdownHtml('ANM-RK-005') !!}</td>
                            </tr>
                            <!-- Asthma row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">5. Asthma</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getFamilyDropdownHtml('ANM-RK-006') !!}</td>
                            </tr>
                            <!-- Penyakit Lainnya row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">6. Penyakit Lainnya</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RK-007', '-') !!}</td>
                            </tr>
                            <!-- Lainnya Ayah row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-500 pl-8">- Penyakit Lainnya Ayah</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RK-008', '-') !!}</td>
                            </tr>
                            <!-- Lainnya Ibu row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-500 pl-8">- Penyakit Lainnya Ibu</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RK-009', '-') !!}</td>
                            </tr>
                            <!-- Meninggal row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">7. Apakah sudah meninggal dunia</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getDeathDropdownHtml('ANM-RK-010') !!}</td>
                            </tr>
                            <!-- Meninggal Ayah row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-500 pl-8">- Penyebab ayah meninggal</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RK-011', '-') !!}</td>
                            </tr>
                            <!-- Meninggal Ibu row -->
                            <tr>
                                <td class="py-2 pr-2 text-slate-500 pl-8">- Penyebab ibu meninggal</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RK-012', '-') !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT COLUMN: RIWAYAT PENYAKIT PRIBADI -->
            <div class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center justify-between text-white" style="background-color: #334155 !important;">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-user-shield" style="color: #2dd4bf !important;"></i>
                        2. Riwayat Penyakit Pribadi
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded text-slate-300" style="background-color: #475569 !important;">Dropdown Ya/Tidak</span>
                </div>
                
                <div class="p-3 flex-grow bg-slate-50 text-xs">
                    <table class="w-full text-left border-collapse">
                        <tbody>
                            <!-- General Status row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 font-semibold text-slate-700 w-7/12">2. Riwayat Penyakit Pribadi</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2 w-4/12">{!! $getInputHtml('ANM-RP-001', 'Tidak') !!}</td>
                            </tr>
                            <!-- Hipertensi row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">1. Tekanan Darah Tinggi (Hipertensi)</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-002') !!}</td>
                            </tr>
                            <!-- Kencing Manis row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">2. Kencing manis</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-003') !!}</td>
                            </tr>
                            <!-- Jantung row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">3. Penyakit jantung dan pembuluh darah</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-004') !!}</td>
                            </tr>
                            <!-- Kanker row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">4. Kanker</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-005') !!}</td>
                            </tr>
                            <!-- Asthma row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4">5. Asthma</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-006') !!}</td>
                            </tr>
                            <!-- Penyakit Lainnya label row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-4 font-semibold">6. Penyakit Lainnya</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RP-007', '-') !!}</td>
                            </tr>
                            <!-- Ambeien row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-8">6. Ambeien</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-008') !!}</td>
                            </tr>
                            <!-- Alergi row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-8">7. Alergi</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-009') !!}</td>
                            </tr>
                            <!-- Alergi Lainnya row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-500 pl-12">- Alergi Lainya</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RP-010', '-') !!}</td>
                            </tr>
                            <!-- Paru-paru row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-8">8. Penyakit Paru paru</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-011') !!}</td>
                            </tr>
                            <!-- Haid row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-700 pl-8">9. Riwayat Haid (khusus wanita)</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getPersonalDropdownHtml('ANM-RP-012') !!}</td>
                            </tr>
                            <!-- Haid lainnya row -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 pr-2 text-slate-500 pl-12">- Riwayat haid lainya</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RP-013', '-') !!}</td>
                            </tr>
                            <!-- Penyakit lainnya tambahan row -->
                            <tr>
                                <td class="py-2 pr-2 text-slate-700 pl-8">10. Penyakit lainnya</td>
                                <td class="py-2 px-1 text-slate-400 text-center w-12">:</td>
                                <td class="py-2 pl-2">{!! $getInputHtml('ANM-RP-014', '-') !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- 4. RIWAYAT RAWAT INAP RS (3 TAHUN TERAKHIR) -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-bed" style="color: #2dd4bf !important;"></i>
                3. Riwayat Rawat Inap RS (3 Tahun Terakhir)
            </div>
            <div class="p-3 bg-slate-50 text-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-300" style="background-color: #e2e8f0 !important;">
                            <th class="py-2 px-3 font-semibold w-1/12 text-center" style="color: #334155 !important;">#.</th>
                            <th class="py-2 px-2 font-semibold w-11/12" style="color: #334155 !important;">Penyebab dirawat | Tahun Rawat | Lama dirawat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 font-bold text-center border-r border-gray-200" style="background-color: #f1f5f9 !important; color: #334155 !important;">1</td>
                            <td class="py-2 px-2">{!! $getInputHtml('ANM-RI-001', 'Contoh: Demam Berdarah | 2024 | 5 Hari') !!}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 font-bold text-center border-r border-gray-200" style="background-color: #f1f5f9 !important; color: #334155 !important;">2</td>
                            <td class="py-2 px-2">{!! $getInputHtml('ANM-RI-002', '-') !!}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-bold text-center border-r border-gray-200" style="background-color: #f1f5f9 !important; color: #334155 !important;">3</td>
                            <td class="py-2 px-2">{!! $getInputHtml('ANM-RI-003', '-') !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. KEBIASAAN, RIWAYAT OPERASI, PENGGUNAAN OBAT RUTIN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
            
            <!-- COLUMN 1: KEBIASAAN -->
            <div class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                    <i class="fas fa-smoking" style="color: #2dd4bf !important;"></i>
                    4. Kebiasaan
                </div>
                <div class="p-3 flex-grow bg-slate-50 text-xs space-y-3.5">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Merokok</label>
                        {!! $getInputHtml('ANM-KB-001', 'Bukan Perokok / Merokok 5 batang sehari') !!}
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Aktivitas Fisik Mingguan</label>
                        {!! $getInputHtml('ANM-KB-002', 'Olahraga 2x seminggu') !!}
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Makan-Minum</label>
                        {!! $getInputHtml('ANM-KB-003', 'Cukup Sayur dan Air putih') !!}
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: RIWAYAT OPERASI -->
            <div class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                    <i class="fas fa-house-medical-circle-exclamation" style="color: #2dd4bf !important;"></i>
                    5. Riwayat Operasi
                </div>
                <div class="p-3 flex-grow bg-slate-50 text-xs flex flex-col justify-center">
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Operasi | Nama Operasi | Keterangan Operasi</label>
                    <div class="mb-3">
                        {!! $getInputHtml('ANM-OP-001', 'Contoh: Ya | Operasi Usus Buntu | Tahun 2022') !!}
                    </div>
                    <p class="text-[10px] text-slate-400 italic leading-relaxed">Format input: [Operasi Ya/Tidak] | [Nama Operasi] | [Keterangan Operasi]</p>
                </div>
            </div>

            <!-- COLUMN 3: PENGGUNAAN OBAT RUTIN -->
            <div class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                    <i class="fas fa-pills" style="color: #2dd4bf !important;"></i>
                    6. Penggunaan Obat Rutin
                </div>
                <div class="p-3 flex-grow bg-slate-50 text-xs flex flex-col justify-center">
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Penggunaan Obat | Fungsi Obat | Nama Obat | Dosis</label>
                    <div class="mb-3">
                        {!! $getInputHtml('ANM-OB-001', 'Contoh: Tidak / Ya | Obat Hipertensi | Amlodipine | 5mg') !!}
                    </div>
                    <p class="text-[10px] text-slate-400 italic leading-relaxed">Format input: [Ada/Tidak] | [Fungsi Obat] | [Nama Obat] | [Dosis]</p>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between mb-8 border-t border-gray-300 pt-4">
            <a href="{{ route('mcu-examinations.anamnesis.index') }}" class="text-slate-600 hover:text-slate-900 font-bold flex items-center gap-2 transition-colors duration-200 text-xs uppercase tracking-wide">
                <i class="fas fa-arrow-left"></i> Batal & Kembali
            </a>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider py-2.5 px-6 rounded shadow transition-all duration-200 flex items-center gap-2 hover:shadow-md border border-slate-700" style="background-color: #0f766e; color: #ffffff;">
                <i class="fas fa-save"></i> Simpan Hasil Anamnesis
            </button>
        </div>
    </form>
    @endif
</div>
@endsection
@extends('layouts.app')
@section('title', 'Input Penunjang Non-Lab')

@section('content')
@php
    // Grouping / metadata for Non-Lab parameters
    $defaultFindings = [
        'EKG' => 'Dalam Batas Normal',
        'Foto Thorax' => 'Cor tak membesar, pulmo dalam batas normal',
        'Treadmill Test' => 'Negative Ischemic Response',
        'Spirometri' => 'Normal',
    ];
@endphp

<div class="w-full mx-auto max-w-7xl px-2">
    <!-- Modern Clinical Patient Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-heart-pulse text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Proses MCU Penunjang Medis Non-Lab</span>
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

    @if($mcuRegistration->examRadiologies->isEmpty())
    <div class="bg-white rounded-lg border border-slate-200 shadow p-12 text-center text-slate-500 mb-6 flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 border border-slate-200 shadow-inner">
            <i class="fas fa-clipboard-list text-2xl text-slate-400"></i>
        </div>
        <h4 class="text-slate-800 font-bold text-lg mb-1">Pemeriksaan Non-Lab Belum Dikonfigurasi</h4>
        <p class="text-slate-500 text-sm max-w-md leading-relaxed">
            Tidak ada komponen penunjang non-lab dalam paket yang dipilih. Silakan edit **Master Paket MCU**, centang item non-lab yang diinginkan, lalu lakukan registrasi ulang pasien.
        </p>
    </div>
    @else

    <!-- Entry Form -->
    <form action="{{ route('mcu-examinations.radiology.store', $mcuRegistration) }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg border border-slate-300 shadow-sm overflow-hidden mb-6">
            <!-- Header section -->
            <div class="px-4 py-3 text-white uppercase tracking-wider font-bold text-xs" style="background-color: #334155 !important;">
                <span class="flex items-center gap-2 text-sm">
                    <i class="fas fa-clipboard-list-check" style="color: #2dd4bf !important;"></i>
                    B. Penunjang Medis Non-Laboratorium
                </span>
            </div>
            
            <!-- Warning Alert box -->
            <div class="bg-amber-50 border-b border-amber-200 px-4 py-2.5 flex items-start gap-2">
                <i class="fas fa-circle-exclamation text-amber-600 mt-0.5 text-xs"></i>
                <span class="text-[11px] font-bold text-amber-800 leading-tight">
                    * Merupakan kesimpulan berupa nilai diluar range normal atau temuan khusus, jika semua hasil normal maka dituliskan "Dalam Batas Normal"
                </span>
            </div>

            <!-- Parameters Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs text-slate-700">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 w-4/12">Jenis Pemeriksaan</th>
                            <th class="px-6 py-3 text-center w-3/12">Status Temuan</th>
                            <th class="px-6 py-3 w-5/12">Hasil Temuan Klinis (Hasil / Kesimpulan)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($mcuRegistration->examRadiologies->sortBy('radiology.display_order') as $index => $exam)
                            @php
                                $paramName = $exam->radiology->name;
                                $currentValue = old('results.'.$exam->id, $exam->findings);
                                if (empty($currentValue)) {
                                    $currentValue = $defaultFindings[$paramName] ?? '';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800 text-sm">
                                    <span class="text-slate-400 mr-1.5 font-medium">{{ $index + 1 }}.</span>
                                    {{ $paramName }}
                                </td>

                                <!-- Status Toggle -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-0">
                                        <!-- Hidden Input to Save status (1 = Normal, 0 = Abnormal) -->
                                        <input type="hidden" name="is_normal[{{ $exam->id }}]" id="status_{{ $exam->id }}" value="{{ old('is_normal.'.$exam->id, $exam->is_normal ? '1' : '0') }}">
                                        
                                        <!-- Normal Button -->
                                        <button type="button" 
                                                onclick="setStatus({{ $exam->id }}, 1)" 
                                                id="btn_normal_{{ $exam->id }}"
                                                class="px-4 py-1.5 rounded-l text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border {{ $exam->is_normal ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200' }}"
                                        >
                                            Normal
                                        </button>
                                        
                                        <!-- Abnormal Button -->
                                        <button type="button" 
                                                onclick="setStatus({{ $exam->id }}, 0)" 
                                                id="btn_abnormal_{{ $exam->id }}"
                                                class="px-4 py-1.5 rounded-r text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border {{ !$exam->is_normal ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200' }}"
                                        >
                                            Abnormal
                                        </button>
                                    </div>
                                </td>

                                <!-- Input findings -->
                                <td class="px-6 py-3">
                                    <input type="hidden" name="exam_ids[]" value="{{ $exam->id }}">
                                    <textarea 
                                        name="results[{{ $exam->id }}]" 
                                        rows="1"
                                        placeholder="Ketik hasil pemeriksaan {{ $paramName }}..."
                                        class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs text-slate-800 font-semibold focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-colors shadow-inner"
                                    >{{ $currentValue }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Action Footer -->
        <div class="mt-8 bg-white border border-slate-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm mb-12">
            <div class="flex items-center gap-2">
                <i class="fas fa-heart-pulse text-teal-600 text-lg"></i>
                <div class="text-left">
                    <span class="text-xs font-bold text-slate-700 block">Validasi Pengisian Hasil</span>
                    <span class="text-[10px] text-slate-500 block">Pastikan semua data penunjang non-lab telah diinputkan dengan benar sebelum disimpan.</span>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('mcu-examinations.radiology.index') }}" class="w-full sm:w-auto text-center px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-50 rounded-md text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-md text-xs font-extrabold shadow transition-all flex items-center justify-center gap-1.5">
                    <i class="fas fa-circle-check"></i>
                    Simpan Hasil Non-Lab
                </button>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
    function setStatus(examId, isNormal) {
        const hiddenInput = document.getElementById('status_' + examId);
        const btnNormal = document.getElementById('btn_normal_' + examId);
        const btnAbnormal = document.getElementById('btn_abnormal_' + examId);

        if (hiddenInput && btnNormal && btnAbnormal) {
            hiddenInput.value = isNormal ? '1' : '0';
            if (isNormal) {
                // Apply Emerald/Green style to normal button
                btnNormal.className = "px-4 py-1.5 rounded-l text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border bg-emerald-600 text-white border-emerald-600";
                // Apply muted style to abnormal button
                btnAbnormal.className = "px-4 py-1.5 rounded-r text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200";
            } else {
                // Apply muted style to normal button
                btnNormal.className = "px-4 py-1.5 rounded-l text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border bg-slate-100 text-slate-400 border-slate-200 hover:bg-slate-200";
                // Apply Amber/Orange style to abnormal button
                btnAbnormal.className = "px-4 py-1.5 rounded-r text-[11px] font-extrabold uppercase tracking-wide transition-all shadow-xs border bg-amber-500 text-white border-amber-500";
            }
        }
    }
</script>
@endsection
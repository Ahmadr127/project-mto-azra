@extends('layouts.app')
@section('title', 'Pemeriksaan Fisik')

@section('content')
<div class="w-full mx-auto">
    <!-- Modern Clinical Patient Banner -->
    <div class="rounded-lg shadow mb-6 overflow-hidden border" style="background-color: #1e293b !important; border-color: #334155 !important;">
        <div class="px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-inner" style="background-color: #334155 !important; border-color: #475569 !important;">
                    <i class="fas fa-stethoscope text-lg" style="color: #2dd4bf !important;"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border" style="color: #2dd4bf !important; background-color: rgba(45, 212, 191, 0.1) !important; border-color: rgba(45, 212, 191, 0.2) !important;">Proses MCU Pemeriksaan Fisik</span>
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

    <!-- Physical Form -->
    <form action="{{ route('mcu-examinations.physical.store', $mcuRegistration) }}" method="POST">
        @csrf
        
        @php
            $res = $mcuRegistration->physicalExamResult;
            $vs = $res->vital_signs ?? [];
            $hn = $res->head_and_neck ?? [];
            $tx = $res->thorax ?? [];
            $ab = $res->abdomen ?? [];
            $ur = $res->urogenital ?? [];
            $ex = $res->extremities ?? [];
            $ot = $res->others ?? [];
        @endphp

        <!-- SECTION 1: ANTROPOMETRI & VITAL SIGNS -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden" x-data="antropometriCalc()">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-weight-scale" style="color: #2dd4bf !important;"></i>
                1. Antropometri & Tanda Vital
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50">
                
                <!-- Antropometri -->
                <div class="bg-white p-4 rounded border border-gray-300 shadow-sm">
                    <h4 class="font-bold mb-3.5 text-xs text-slate-700 uppercase tracking-wider">Antropometri WHO</h4>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Lingkar Pinggang</label>
                        <input type="number" step="0.1" name="vital_signs[waist]" value="{{ $vs['waist'] ?? '' }}" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                        <span class="text-xs text-gray-500">cm</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Tinggi Badan</label>
                        <input type="number" step="0.1" x-model="tb" name="vital_signs[height]" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                        <span class="text-xs text-gray-500">cm</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Berat Badan</label>
                        <input type="number" step="0.1" x-model="bb" name="vital_signs[weight]" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                        <span class="text-xs text-gray-500">kg</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">BMI WHO</label>
                        <input type="text" x-model="bmi" name="vital_signs[bmi]" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 bg-gray-100" readonly>
                        <span class="text-xs text-gray-500">%</span>
                    </div>
                    
                    <div class="mb-2">
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Kesimpulan BMI</label>
                        <input type="text" name="vital_signs[bmi_conclusion]" value="{{ $vs['bmi_conclusion'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Contoh: Overweight">
                    </div>
                </div>

                <!-- Tanda Vital -->
                <div class="bg-white p-4 rounded border border-gray-300 shadow-sm">
                    <h4 class="font-bold mb-3.5 text-xs text-slate-700 uppercase tracking-wider">Tanda Vital</h4>
                    
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-[9px] uppercase font-bold text-gray-500 mb-1 block">Tensi 1 (mmHg)</label>
                            <input type="text" x-model="t1" name="vital_signs[tensi_1]" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500" placeholder="120/80">
                        </div>
                        <div>
                            <label class="text-[9px] uppercase font-bold text-gray-500 mb-1 block">Tensi 2 (mmHg)</label>
                            <input type="text" x-model="t2" name="vital_signs[tensi_2]" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500" placeholder="120/80">
                        </div>
                        <div>
                            <label class="text-[9px] uppercase font-bold text-gray-500 mb-1 block">Tensi 3 (mmHg)</label>
                            <input type="text" x-model="t3" name="vital_signs[tensi_3]" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500" placeholder="120/80">
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-bold">Tensi Rata-rata</label>
                        <input type="text" x-model="tensi_avg" name="vital_signs[tensi_avg]" class="w-1/2 border rounded px-2.5 py-1.5 text-xs font-bold" style="color: #0f766e !important; border-color: #2dd4bf !important; background-color: #f0fdfa !important;" readonly>
                    </div>

                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Nadi</label>
                        <input type="number" name="vital_signs[pulse]" value="{{ $vs['pulse'] ?? '' }}" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500">
                        <span class="text-xs text-gray-500">x/mnt</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Suhu Tubuh</label>
                        <input type="number" step="0.1" name="vital_signs[temp]" value="{{ $vs['temp'] ?? '' }}" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500">
                        <span class="text-xs text-gray-500">°C</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="w-1/3 text-xs text-slate-700 font-semibold">Respirasi</label>
                        <input type="number" name="vital_signs[respiration]" value="{{ $vs['respiration'] ?? '' }}" class="w-1/3 border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500">
                        <span class="text-xs text-gray-500">x/mnt</span>
                    </div>

                    <div class="mb-2">
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Kesimpulan TTV</label>
                        <input type="text" name="vital_signs[conclusion]" value="{{ $vs['conclusion'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: KEPALA & MUKA -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-user" style="color: #2dd4bf !important;"></i>
                2. Kepala & Muka
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5 uppercase text-[10px]">Kepala</label>
                    <input type="text" name="head_and_neck[head]" value="{{ $hn['head'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5 uppercase text-[10px]">Muka</label>
                    <input type="text" name="head_and_neck[face]" value="{{ $hn['face'] ?? 'Simetris' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- SECTION 3: MATA & VISUS -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-eye" style="color: #2dd4bf !important;"></i>
                3. Mata & Visus
            </div>
            <div class="p-4 bg-slate-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5 uppercase text-[10px]">Sklera</label>
                        <input type="text" name="head_and_neck[sclera]" value="{{ $hn['sclera'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5 uppercase text-[10px]">Konjungtiva</label>
                        <input type="text" name="head_and_neck[conjunctiva]" value="{{ $hn['conjunctiva'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5 uppercase text-[10px]">Buta Warna</label>
                        <select name="head_and_neck[color_blind]" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-white">
                            <option value="Tidak Buta Warna" {{ ($hn['color_blind']??'') == 'Tidak Buta Warna' ? 'selected':'' }}>Tidak Buta Warna</option>
                            <option value="Buta Warna Parsial" {{ ($hn['color_blind']??'') == 'Buta Warna Parsial' ? 'selected':'' }}>Buta Warna Parsial</option>
                            <option value="Buta Warna Total" {{ ($hn['color_blind']??'') == 'Buta Warna Total' ? 'selected':'' }}>Buta Warna Total</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white p-4 border border-gray-300 rounded shadow-sm">
                    <h4 class="font-bold mb-3 text-xs uppercase tracking-wider text-slate-700">Visus Mata</h4>
                    <table class="w-full text-left text-xs mb-3 border-collapse">
                        <thead>
                            <tr class="border-b border-gray-300 animate-none" style="background-color: #e2e8f0 !important;">
                                <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">Parameter</th>
                                <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">A. Kanan (Dextra)</th>
                                <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">B. Kiri (Sinistra)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-150">
                                <td class="py-2 px-3 text-slate-700 font-medium">Spheris</td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_od_sph]" value="{{ $hn['visus_od_sph'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_os_sph]" value="{{ $hn['visus_os_sph'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            </tr>
                            <tr class="border-b border-gray-150">
                                <td class="py-2 px-3 text-slate-700 font-medium">Silindris</td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_od_cyl]" value="{{ $hn['visus_od_cyl'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_os_cyl]" value="{{ $hn['visus_os_cyl'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            </tr>
                            <tr class="border-b border-gray-150">
                                <td class="py-2 px-3 text-slate-700 font-medium">Axis</td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_od_axis]" value="{{ $hn['visus_od_axis'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_os_axis]" value="{{ $hn['visus_os_axis'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-slate-700 font-medium">Add</td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_od_add]" value="{{ $hn['visus_od_add'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                <td class="py-2 px-3"><input type="text" name="head_and_neck[visus_os_add]" value="{{ $hn['visus_os_add'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Keterangan Visus</label>
                        <input type="text" name="head_and_neck[visus_notes]" value="{{ $hn['visus_notes'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Contoh: Visus Myopia ODS dengan Astigmatisme ODS">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: THT, MULUT & GIGI -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-teeth-open" style="color: #2dd4bf !important;"></i>
                4. THT, Mulut & Gigi
            </div>
            <div class="p-4 bg-slate-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Hidung -->
                    <div>
                        <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Hidung</h4>
                        <input type="text" name="head_and_neck[nose_konka]" value="{{ $hn['nose_konka'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Konka">
                        <input type="text" name="head_and_neck[nose_septum]" value="{{ $hn['nose_septum'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Septum">
                        <input type="text" name="head_and_neck[nose_polip]" value="{{ $hn['nose_polip'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Polip">
                    </div>
                    <!-- Telinga -->
                    <div>
                        <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Telinga</h4>
                        <input type="text" name="head_and_neck[ear_shape]" value="{{ $hn['ear_shape'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Bentuk">
                        <input type="text" name="head_and_neck[ear_drum]" value="{{ $hn['ear_drum'] ?? 'Intak' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Gendang Telinga">
                        <input type="text" name="head_and_neck[ear_cerumen]" value="{{ $hn['ear_cerumen'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Cerumen">
                    </div>
                    <!-- Tenggorokan -->
                    <div>
                        <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Tenggorokan</h4>
                        <input type="text" name="head_and_neck[throat_tonsil]" value="{{ $hn['throat_tonsil'] ?? 'T1/T1 Tenang' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Tonsil">
                        <input type="text" name="head_and_neck[throat_pharynx]" value="{{ $hn['throat_pharynx'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Faring">
                        <input type="text" name="head_and_neck[throat_larynx]" value="{{ $hn['throat_larynx'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Larynx">
                    </div>
                </div>

                <!-- Gigi (Odontogram) -->
                <div class="bg-white p-4 border border-gray-300 rounded mb-4">
                    <h4 class="font-bold mb-3 text-xs uppercase tracking-wider text-slate-700">Pemeriksaan Gigi (Odontogram)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                        <div>
                            <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Caries</label>
                            <input type="text" name="head_and_neck[dental_caries]" value="{{ $hn['dental_caries'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_caries_loc]" value="{{ $hn['dental_caries_loc'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Lokasi (cth: 27)">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Decay</label>
                            <input type="text" name="head_and_neck[dental_decay]" value="{{ $hn['dental_decay'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_decay_loc]" value="{{ $hn['dental_decay_loc'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Lokasi">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Missing</label>
                            <input type="text" name="head_and_neck[dental_missing]" value="{{ $hn['dental_missing'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_missing_loc]" value="{{ $hn['dental_missing_loc'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Lokasi">
                        </div>
                        <div>
                            <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Calculus</label>
                            <input type="text" name="head_and_neck[dental_calculus]" value="{{ $hn['dental_calculus'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_calculus_loc]" value="{{ $hn['dental_calculus_loc'] ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Lokasi">
                        </div>
                    </div>
                </div>

                <!-- Mulut & Leher -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Mulut</h4>
                        <input type="text" name="head_and_neck[mouth_halitosis]" value="{{ $hn['mouth_halitosis'] ?? 'Tidak' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Halitosis">
                        <input type="text" name="head_and_neck[mouth_stomatitis]" value="{{ $hn['mouth_stomatitis'] ?? 'Tidak' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Stomatitis">
                        <input type="text" name="head_and_neck[mouth_mass]" value="{{ $hn['mouth_mass'] ?? 'Tidak' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Massa">
                    </div>
                    <div>
                        <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Leher</h4>
                        <input type="text" name="head_and_neck[neck_shape]" value="{{ $hn['neck_shape'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Bentuk Leher">
                        <input type="text" name="head_and_neck[neck_lymph]" value="{{ $hn['neck_lymph'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Kelenjar Getah Bening">
                        <input type="text" name="head_and_neck[neck_jvp]" value="{{ $hn['neck_jvp'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="JVP">
                        <input type="text" name="head_and_neck[neck_thyroid]" value="{{ $hn['neck_thyroid'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Kelenjar Thyroid">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: THORAX & ABDOMEN -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-stethoscope" style="color: #2dd4bf !important;"></i>
                5. Dada & Perut
            </div>
            <div class="p-4 bg-slate-50 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Paru-Paru</h4>
                    <input type="text" name="thorax[lung_auscultation]" value="{{ $tx['lung_auscultation'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Auskultasi">
                    <input type="text" name="thorax[lung_sternum]" value="{{ $tx['lung_sternum'] ?? 'Central' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Sternum">
                </div>
                <div>
                    <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Jantung</h4>
                    <input type="text" name="thorax[heart_rhythm]" value="{{ $tx['heart_rhythm'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Irama">
                    <input type="text" name="thorax[heart_murmur]" value="{{ $tx['heart_murmur'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Murmur">
                    <input type="text" name="thorax[heart_gallop]" value="{{ $tx['heart_gallop'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Gallop">
                </div>
                <div>
                    <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Payudara</h4>
                    <input type="text" name="thorax[breast_status]" value="{{ $tx['breast_status'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Keterangan Payudara">
                </div>
                <div>
                    <h4 class="font-bold text-xs mb-2.5 uppercase tracking-wider text-slate-700">Abdomen</h4>
                    <input type="text" name="abdomen[hepar]" value="{{ $ab['hepar'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Hepar">
                    <input type="text" name="abdomen[lien]" value="{{ $ab['lien'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 mb-2" placeholder="Lien">
                    <input type="text" name="abdomen[peristaltik]" value="{{ $ab['peristaltik'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Peristaltik">
                </div>
            </div>
        </div>

        <!-- SECTION 6: UROGENITAL & INGUINAL -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-venus-mars" style="color: #2dd4bf !important;"></i>
                6. Anus, Urogenital & Inguinal
            </div>
            <div class="p-4 bg-slate-50 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Haemorrhoid</label>
                    <input type="text" name="urogenital[haemorrhoid]" value="{{ $ur['haemorrhoid'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Nyeri Ketok CVA</label>
                    <input type="text" name="urogenital[cva_pain]" value="{{ $ur['cva_pain'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Hernia / KGB Inguinal</label>
                    <input type="text" name="urogenital[hernia]" value="{{ $ur['hernia'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- SECTION 7: EKSTREMITAS -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-child" style="color: #2dd4bf !important;"></i>
                7. Ekstremitas
            </div>
            <div class="p-4 bg-slate-50">
                
                <h4 class="font-bold text-xs mb-2 uppercase tracking-wider text-slate-700">Extremitas Atas</h4>
                <table class="w-full text-left text-xs mb-5 border border-gray-300 border-collapse bg-white">
                    <thead>
                        <tr class="border-b border-gray-300" style="background-color: #e2e8f0 !important;">
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">Parameter</th>
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">A. Kanan</th>
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">B. Kiri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Kekuatan</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[up_right_str]" value="{{ $ex['up_right_str'] ?? '5' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[up_left_str]" value="{{ $ex['up_left_str'] ?? '5' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Reflek Fisiologis</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[up_right_physio]" value="{{ $ex['up_right_physio'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[up_left_physio]" value="{{ $ex['up_left_physio'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Reflek Patologis</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[up_right_patho]" value="{{ $ex['up_right_patho'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[up_left_patho]" value="{{ $ex['up_left_patho'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                    </tbody>
                </table>

                <h4 class="font-bold text-xs mb-2 uppercase tracking-wider text-slate-700">Extremitas Bawah</h4>
                <table class="w-full text-left text-xs border border-gray-300 border-collapse bg-white">
                    <thead>
                        <tr class="border-b border-gray-300" style="background-color: #e2e8f0 !important;">
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">Parameter</th>
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">A. Kanan</th>
                            <th class="py-2 px-3 font-semibold text-slate-700 w-4/12">B. Kiri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Kekuatan</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[low_right_str]" value="{{ $ex['low_right_str'] ?? '5' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[low_left_str]" value="{{ $ex['low_left_str'] ?? '5' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Reflek Fisiologis</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[low_right_physio]" value="{{ $ex['low_right_physio'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[low_left_physio]" value="{{ $ex['low_left_physio'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Reflek Patologis</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[low_right_patho]" value="{{ $ex['low_right_patho'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[low_left_patho]" value="{{ $ex['low_left_patho'] ?? 'Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Varices</td>
                            <td class="py-2 px-3 border-r border-gray-200"><input type="text" name="extremities[low_right_varices]" value="{{ $ex['low_right_varices'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                            <td class="py-2 px-3"><input type="text" name="extremities[low_left_varices]" value="{{ $ex['low_left_varices'] ?? 'Tidak Ada' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 8: LAIN-LAIN -->
        <div class="bg-white rounded-lg border border-gray-300 shadow-sm mb-5 overflow-hidden">
            <div class="px-4 py-2.5 font-bold text-xs uppercase tracking-wider flex items-center gap-2 text-white" style="background-color: #334155 !important;">
                <i class="fas fa-folder-plus" style="color: #2dd4bf !important;"></i>
                8. Lain-Lain
            </div>
            <div class="p-4 bg-slate-50 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Kulit</label>
                    <input type="text" name="others[skin]" value="{{ $ot['skin'] ?? 'Dalam Batas Normal' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Romberg Test</label>
                    <input type="text" name="others[romberg]" value="{{ $ot['romberg'] ?? '-' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1 uppercase text-[10px]">Lapang Pandang</label>
                    <input type="text" name="others[visual_field]" value="{{ $ot['visual_field'] ?? '-' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 text-xs text-gray-800 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between mb-8 border-t border-gray-300 pt-4">
            <a href="{{ route('mcu-examinations.physical.index') }}" class="text-slate-600 hover:text-slate-900 font-bold flex items-center gap-2 transition-colors duration-200 text-xs uppercase tracking-wide">
                <i class="fas fa-arrow-left"></i> Batal & Kembali
            </a>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider py-2.5 px-6 rounded shadow transition-all duration-200 flex items-center gap-2 hover:shadow-md border border-slate-700" style="background-color: #0f766e; color: #ffffff;">
                <i class="fas fa-save"></i> Simpan Hasil Fisik
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('antropometriCalc', () => ({
            tb: '{{ $vs['height'] ?? '' }}',
            bb: '{{ $vs['weight'] ?? '' }}',
            t1: '{{ $vs['tensi_1'] ?? '' }}',
            t2: '{{ $vs['tensi_2'] ?? '' }}',
            t3: '{{ $vs['tensi_3'] ?? '' }}',
            
            get bmi() {
                if (this.tb && this.bb) {
                    const heightM = parseFloat(this.tb) / 100;
                    const weight = parseFloat(this.bb);
                    if (heightM > 0) {
                        return (weight / (heightM * heightM)).toFixed(2);
                    }
                }
                return '';
            },
            
            get tensi_avg() {
                const parseTensi = (val) => {
                    if (!val) return null;
                    const parts = val.split('/');
                    if (parts.length === 2) {
                        return { sys: parseInt(parts[0]), dia: parseInt(parts[1]) };
                    }
                    return null;
                };
                
                let valid = [];
                const pt1 = parseTensi(this.t1);
                const pt2 = parseTensi(this.t2);
                const pt3 = parseTensi(this.t3);
                
                if (pt2 && pt3) {
                    valid.push(pt2, pt3);
                } else if (pt1) {
                    valid.push(pt1);
                }

                if (valid.length > 0) {
                    const avgSys = valid.reduce((acc, curr) => acc + curr.sys, 0) / valid.length;
                    const avgDia = valid.reduce((acc, curr) => acc + curr.dia, 0) / valid.length;
                    return Math.round(avgSys) + '/' + Math.round(avgDia);
                }
                return '';
            }
        }))
    })
</script>
@endpush
@endsection
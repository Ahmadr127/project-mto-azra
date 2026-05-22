@extends('layouts.app')
@section('title', 'Pemeriksaan Fisik')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">Input Hasil Pemeriksaan Fisik</h2>
            <p class="text-indigo-100">Pasien: <span class="font-bold">{{ $mcuRegistration->patient->name }}</span> ({{ $mcuRegistration->patient->patient_code }})</p>
        </div>

        <form action="{{ route('mcu-examinations.physical.store', $mcuRegistration) }}" method="POST" class="p-6">
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
            <div class="mb-8" x-data="antropometriCalc()">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">1. Antropometri & Tanda Vital</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Antropometri -->
                    <div class="bg-gray-50 p-4 rounded border">
                        <h4 class="font-semibold mb-3 text-sm text-gray-600 uppercase">Antropometri WHO</h4>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Lingkar Pinggang</label>
                            <input type="number" step="0.1" name="vital_signs[waist]" value="{{ $vs['waist'] ?? '' }}" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">cm</span>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Tinggi Badan</label>
                            <input type="number" step="0.1" x-model="tb" name="vital_signs[height]" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">cm</span>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Berat Badan</label>
                            <input type="number" step="0.1" x-model="bb" name="vital_signs[weight]" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">kg</span>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">BMI WHO</label>
                            <input type="text" x-model="bmi" name="vital_signs[bmi]" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm bg-gray-200" readonly>
                            <span class="text-sm text-gray-500">%</span>
                        </div>
                        
                        <div class="mb-2">
                            <label class="block text-sm mb-1">Kesimpulan BMI</label>
                            <input type="text" name="vital_signs[bmi_conclusion]" value="{{ $vs['bmi_conclusion'] ?? '' }}" class="w-full shadow-sm border rounded py-1 px-2 text-sm" placeholder="Contoh: Overweight">
                        </div>
                    </div>

                    <!-- Tanda Vital -->
                    <div class="bg-gray-50 p-4 rounded border">
                        <h4 class="font-semibold mb-3 text-sm text-gray-600 uppercase">Tanda Vital</h4>
                        
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div>
                                <label class="text-xs text-gray-500">Tensi 1 (mmHg)</label>
                                <input type="text" x-model="t1" name="vital_signs[tensi_1]" class="w-full shadow-sm border rounded py-1 px-2 text-sm" placeholder="120/80">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Tensi 2 (mmHg)</label>
                                <input type="text" x-model="t2" name="vital_signs[tensi_2]" class="w-full shadow-sm border rounded py-1 px-2 text-sm" placeholder="120/80">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Tensi 3 (mmHg)</label>
                                <input type="text" x-model="t3" name="vital_signs[tensi_3]" class="w-full shadow-sm border rounded py-1 px-2 text-sm" placeholder="120/80">
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm font-semibold">Tensi Rata-rata</label>
                            <input type="text" x-model="tensi_avg" name="vital_signs[tensi_avg]" class="w-1/2 shadow-sm border rounded py-1 px-2 text-sm bg-indigo-50 font-bold" readonly>
                        </div>

                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Nadi</label>
                            <input type="number" name="vital_signs[pulse]" value="{{ $vs['pulse'] ?? '' }}" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">x/mnt</span>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Suhu Tubuh</label>
                            <input type="number" step="0.1" name="vital_signs[temp]" value="{{ $vs['temp'] ?? '' }}" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">°C</span>
                        </div>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="w-1/3 text-sm">Respirasi</label>
                            <input type="number" name="vital_signs[respiration]" value="{{ $vs['respiration'] ?? '' }}" class="w-1/3 shadow-sm border rounded py-1 px-2 text-sm">
                            <span class="text-sm text-gray-500">x/mnt</span>
                        </div>

                        <div class="mb-2">
                            <label class="block text-sm mb-1">Kesimpulan TTV</label>
                            <input type="text" name="vital_signs[conclusion]" value="{{ $vs['conclusion'] ?? 'Dalam Batas Normal' }}" class="w-full shadow-sm border rounded py-1 px-2 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: KEPALA & MUKA -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">2. Kepala & Muka</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kepala</label>
                        <input type="text" name="head_and_neck[head]" value="{{ $hn['head'] ?? 'Normal' }}" class="w-full shadow-sm border rounded py-2 px-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Muka</label>
                        <input type="text" name="head_and_neck[face]" value="{{ $hn['face'] ?? 'Simetris' }}" class="w-full shadow-sm border rounded py-2 px-3 text-sm">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: MATA & VISUS -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">3. Mata & Visus</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm mb-1">Sklera</label>
                        <input type="text" name="head_and_neck[sclera]" value="{{ $hn['sclera'] ?? 'Dalam Batas Normal' }}" class="w-full shadow-sm border rounded py-2 px-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Konjungtiva</label>
                        <input type="text" name="head_and_neck[conjunctiva]" value="{{ $hn['conjunctiva'] ?? 'Dalam Batas Normal' }}" class="w-full shadow-sm border rounded py-2 px-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Buta Warna</label>
                        <select name="head_and_neck[color_blind]" class="w-full shadow-sm border rounded py-2 px-3 text-sm">
                            <option value="Tidak Buta Warna" {{ ($hn['color_blind']??'') == 'Tidak Buta Warna' ? 'selected':'' }}>Tidak Buta Warna</option>
                            <option value="Buta Warna Parsial" {{ ($hn['color_blind']??'') == 'Buta Warna Parsial' ? 'selected':'' }}>Buta Warna Parsial</option>
                            <option value="Buta Warna Total" {{ ($hn['color_blind']??'') == 'Buta Warna Total' ? 'selected':'' }}>Buta Warna Total</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 border rounded">
                    <h4 class="font-semibold mb-3 text-sm">Visus Mata</h4>
                    <table class="w-full text-left text-sm mb-3">
                        <thead>
                            <tr class="border-b">
                                <th class="pb-2">Parameter</th>
                                <th class="pb-2">A. Kanan (Dextra)</th>
                                <th class="pb-2">B. Kiri (Sinistra)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-2">Spheris</td>
                                <td class="py-2 pr-2"><input type="text" name="head_and_neck[visus_od_sph]" value="{{ $hn['visus_od_sph'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                                <td class="py-2 pl-2"><input type="text" name="head_and_neck[visus_os_sph]" value="{{ $hn['visus_os_sph'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                            </tr>
                            <tr>
                                <td class="py-2">Silindris</td>
                                <td class="py-2 pr-2"><input type="text" name="head_and_neck[visus_od_cyl]" value="{{ $hn['visus_od_cyl'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                                <td class="py-2 pl-2"><input type="text" name="head_and_neck[visus_os_cyl]" value="{{ $hn['visus_os_cyl'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                            </tr>
                            <tr>
                                <td class="py-2">Axis</td>
                                <td class="py-2 pr-2"><input type="text" name="head_and_neck[visus_od_axis]" value="{{ $hn['visus_od_axis'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                                <td class="py-2 pl-2"><input type="text" name="head_and_neck[visus_os_axis]" value="{{ $hn['visus_os_axis'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                            </tr>
                            <tr>
                                <td class="py-2">Add</td>
                                <td class="py-2 pr-2"><input type="text" name="head_and_neck[visus_od_add]" value="{{ $hn['visus_od_add'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                                <td class="py-2 pl-2"><input type="text" name="head_and_neck[visus_os_add]" value="{{ $hn['visus_os_add'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <label class="block text-sm mb-1">Keterangan Visus</label>
                        <input type="text" name="head_and_neck[visus_notes]" value="{{ $hn['visus_notes'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Contoh: Visus Myopia ODS dengan Astigmatisme ODS">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: THT & GIGI -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">4. THT, Mulut & Gigi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Hidung -->
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Hidung</h4>
                        <input type="text" name="head_and_neck[nose_konka]" value="{{ $hn['nose_konka'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Konka">
                        <input type="text" name="head_and_neck[nose_septum]" value="{{ $hn['nose_septum'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Septum">
                        <input type="text" name="head_and_neck[nose_polip]" value="{{ $hn['nose_polip'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Polip">
                    </div>
                    <!-- Telinga -->
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Telinga</h4>
                        <input type="text" name="head_and_neck[ear_shape]" value="{{ $hn['ear_shape'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Bentuk">
                        <input type="text" name="head_and_neck[ear_drum]" value="{{ $hn['ear_drum'] ?? 'Intak' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Gendang Telinga">
                        <input type="text" name="head_and_neck[ear_cerumen]" value="{{ $hn['ear_cerumen'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Cerumen">
                    </div>
                    <!-- Tenggorokan -->
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Tenggorokan</h4>
                        <input type="text" name="head_and_neck[throat_tonsil]" value="{{ $hn['throat_tonsil'] ?? 'T1/T1 Tenang' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Tonsil">
                        <input type="text" name="head_and_neck[throat_pharynx]" value="{{ $hn['throat_pharynx'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Faring">
                        <input type="text" name="head_and_neck[throat_larynx]" value="{{ $hn['throat_larynx'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Larynx">
                    </div>
                </div>

                <!-- Gigi (Odontogram) -->
                <div class="bg-gray-50 p-4 border rounded mb-4">
                    <h4 class="font-semibold mb-3 text-sm">Pemeriksaan Gigi (Odontogram)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Caries</label>
                            <input type="text" name="head_and_neck[dental_caries]" value="{{ $hn['dental_caries'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_caries_loc]" value="{{ $hn['dental_caries_loc'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Lokasi (cth: 27)">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Decay</label>
                            <input type="text" name="head_and_neck[dental_decay]" value="{{ $hn['dental_decay'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_decay_loc]" value="{{ $hn['dental_decay_loc'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Lokasi">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Missing</label>
                            <input type="text" name="head_and_neck[dental_missing]" value="{{ $hn['dental_missing'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_missing_loc]" value="{{ $hn['dental_missing_loc'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Lokasi">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Calculus</label>
                            <input type="text" name="head_and_neck[dental_calculus]" value="{{ $hn['dental_calculus'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm mb-1" placeholder="Status">
                            <input type="text" name="head_and_neck[dental_calculus_loc]" value="{{ $hn['dental_calculus_loc'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Lokasi">
                        </div>
                    </div>
                </div>

                <!-- Mulut & Leher -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Mulut</h4>
                        <input type="text" name="head_and_neck[mouth_halitosis]" value="{{ $hn['mouth_halitosis'] ?? 'Tidak' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Halitosis">
                        <input type="text" name="head_and_neck[mouth_stomatitis]" value="{{ $hn['mouth_stomatitis'] ?? 'Tidak' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Stomatitis">
                        <input type="text" name="head_and_neck[mouth_mass]" value="{{ $hn['mouth_mass'] ?? 'Tidak' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Massa">
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Leher</h4>
                        <input type="text" name="head_and_neck[neck_shape]" value="{{ $hn['neck_shape'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Bentuk Leher">
                        <input type="text" name="head_and_neck[neck_lymph]" value="{{ $hn['neck_lymph'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Kelenjar Getah Bening">
                        <input type="text" name="head_and_neck[neck_jvp]" value="{{ $hn['neck_jvp'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="JVP">
                        <input type="text" name="head_and_neck[neck_thyroid]" value="{{ $hn['neck_thyroid'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Kelenjar Thyroid">
                    </div>
                </div>
            </div>

            <!-- SECTION 5: THORAX & ABDOMEN -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">5. Dada & Perut</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Paru-Paru</h4>
                        <input type="text" name="thorax[lung_auscultation]" value="{{ $tx['lung_auscultation'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Auskultasi">
                        <input type="text" name="thorax[lung_sternum]" value="{{ $tx['lung_sternum'] ?? 'Central' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Sternum">
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Jantung</h4>
                        <input type="text" name="thorax[heart_rhythm]" value="{{ $tx['heart_rhythm'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Irama">
                        <input type="text" name="thorax[heart_murmur]" value="{{ $tx['heart_murmur'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Murmur">
                        <input type="text" name="thorax[heart_gallop]" value="{{ $tx['heart_gallop'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Gallop">
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Payudara</h4>
                        <input type="text" name="thorax[breast_status]" value="{{ $tx['breast_status'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Keterangan Payudara">
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm mb-2">Abdomen</h4>
                        <input type="text" name="abdomen[hepar]" value="{{ $ab['hepar'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Hepar">
                        <input type="text" name="abdomen[lien]" value="{{ $ab['lien'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm mb-2" placeholder="Lien">
                        <input type="text" name="abdomen[peristaltik]" value="{{ $ab['peristaltik'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Peristaltik">
                    </div>
                </div>
            </div>

            <!-- SECTION 6: UROGENITAL & INGUINAL -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">6. Anus, Urogenital & Inguinal</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Haemorrhoid</label>
                        <input type="text" name="urogenital[haemorrhoid]" value="{{ $ur['haemorrhoid'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Nyeri Ketok CVA</label>
                        <input type="text" name="urogenital[cva_pain]" value="{{ $ur['cva_pain'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Hernia / KGB Inguinal</label>
                        <input type="text" name="urogenital[hernia]" value="{{ $ur['hernia'] ?? 'Tidak Dilakukan Pemeriksaan' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                </div>
            </div>

            <!-- SECTION 7: EKSTREMITAS -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">7. Ekstremitas</h3>
                
                <h4 class="font-semibold mb-2 text-sm">Extremitas Atas</h4>
                <table class="w-full text-left text-sm mb-4 border">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-2">Parameter</th>
                            <th class="p-2">A. Kanan</th>
                            <th class="p-2">B. Kiri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-2">Kekuatan</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[up_right_str]" value="{{ $ex['up_right_str'] ?? '5' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[up_left_str]" value="{{ $ex['up_left_str'] ?? '5' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Reflek Fisiologis</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[up_right_physio]" value="{{ $ex['up_right_physio'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[up_left_physio]" value="{{ $ex['up_left_physio'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Reflek Patologis</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[up_right_patho]" value="{{ $ex['up_right_patho'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[up_left_patho]" value="{{ $ex['up_left_patho'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>

                <h4 class="font-semibold mb-2 text-sm">Extremitas Bawah</h4>
                <table class="w-full text-left text-sm mb-4 border">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-2">Parameter</th>
                            <th class="p-2">A. Kanan</th>
                            <th class="p-2">B. Kiri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-2">Kekuatan</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[low_right_str]" value="{{ $ex['low_right_str'] ?? '5' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[low_left_str]" value="{{ $ex['low_left_str'] ?? '5' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Reflek Fisiologis</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[low_right_physio]" value="{{ $ex['low_right_physio'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[low_left_physio]" value="{{ $ex['low_left_physio'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Reflek Patologis</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[low_right_patho]" value="{{ $ex['low_right_patho'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[low_left_patho]" value="{{ $ex['low_left_patho'] ?? 'Normal' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Varices</td>
                            <td class="p-2 border-r"><input type="text" name="extremities[low_right_varices]" value="{{ $ex['low_right_varices'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2"><input type="text" name="extremities[low_left_varices]" value="{{ $ex['low_left_varices'] ?? 'Tidak Ada' }}" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SECTION 8: LAIN-LAIN -->
            <div class="mb-8">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">8. Lain-Lain</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Kulit</label>
                        <input type="text" name="others[skin]" value="{{ $ot['skin'] ?? 'Dalam Batas Normal' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Romberg Test</label>
                        <input type="text" name="others[romberg]" value="{{ $ot['romberg'] ?? '-' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Lapang Pandang</label>
                        <input type="text" name="others[visual_field]" value="{{ $ot['visual_field'] ?? '-' }}" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="flex items-center justify-end mt-8 border-t pt-4">
                <a href="{{ route('mcu-examinations.physical.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Hasil Fisik
                </button>
            </div>
        </form>
    </div>
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
                // simple calc assuming format like "120/80"
                const parseTensi = (val) => {
                    if (!val) return null;
                    const parts = val.split('/');
                    if (parts.length === 2) {
                        return { sys: parseInt(parts[0]), dia: parseInt(parts[1]) };
                    }
                    return null;
                };
                
                let valid = [];
                // Average usually of tensi 2 and 3 if available, else tensi 1
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
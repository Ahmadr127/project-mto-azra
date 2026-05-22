@extends('layouts.app')
@section('title', 'Review & Resume Akhir')

@section('content')
<div class="w-full mx-auto pb-10">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Review & Resume Akhir MCU</h2>
        <a href="{{ route('mcu-resumes.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali ke Antrean</a>
    </div>

    <!-- Identitas Pasien -->
    <div class="bg-white shadow-sm sm:rounded-lg mb-6 border-l-4 border-blue-500">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Identitas Pasien</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><span class="text-gray-500 text-sm block">No Rekam Medis</span><span class="font-semibold">{{ $mcuRegistration->patient->patient_code }}</span></div>
                <div><span class="text-gray-500 text-sm block">Nama Lengkap</span><span class="font-semibold">{{ $mcuRegistration->patient->name }}</span></div>
                <div><span class="text-gray-500 text-sm block">Jenis Kelamin / Umur</span><span class="font-semibold">{{ $mcuRegistration->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ $mcuRegistration->patient->age }} Thn</span></div>
                <div><span class="text-gray-500 text-sm block">Paket MCU</span><span class="font-semibold">{{ $mcuRegistration->package->name }}</span></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Hasil Pemeriksaan (Scrollable) -->
        <div class="lg:col-span-2 space-y-6 overflow-y-auto" style="max-height: 80vh;">
            
            <!-- Hasil Anamnesis -->
            @if($mcuRegistration->examAnamneses->count() > 0)
            @php
                $anmExams = $mcuRegistration->examAnamneses->keyBy(function($exam) {
                    return trim($exam->anamnesis->code);
                });
                
                $getAnmVal = function($code, $default = '-') use ($anmExams) {
                    $exam = $anmExams->get($code);
                    return $exam ? ($exam->result ?: $default) : $default;
                };
            @endphp
            <div class="bg-white shadow-sm sm:rounded-lg border border-slate-200">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-file-medical text-teal-600"></i>
                        Pemeriksaan Anamnesis & Keluhan Pasien
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    
                    <!-- Keluhan Utama & Koreksi Dokter -->
                    <div class="bg-teal-50/50 border border-teal-100 rounded-lg p-4 space-y-3">
                        <span class="font-extrabold text-[10px] text-teal-800 uppercase tracking-wider block">Koreksi Dokter & Keluhan Utama</span>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            <div class="bg-white p-3 rounded border border-teal-50 shadow-sm">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide mb-1">Koreksi / Keluhan oleh Dokter</span>
                                <span class="font-semibold text-slate-800">{{ $getAnmVal('ANM-KD-001', 'Tidak ada keluhan/koreksi') }}</span>
                            </div>
                            <div class="bg-white p-3 rounded border border-teal-50 shadow-sm">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide mb-1">Keluhan saat Bekerja</span>
                                <span class="font-semibold text-slate-800">{{ $getAnmVal('ANM-KP-001', 'Tidak ada') }}</span>
                            </div>
                            <div class="bg-white p-3 rounded border border-teal-50 shadow-sm">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide mb-1">Keluhan di Luar Pekerjaan</span>
                                <span class="font-semibold text-slate-800">{{ $getAnmVal('ANM-KP-002', 'Tidak ada') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Penyakit Keluarga & Pribadi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Riwayat Penyakit Keluarga -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-100 px-3 py-2 border-b border-slate-200 flex justify-between items-center">
                                <span class="font-extrabold text-xs text-slate-700 uppercase tracking-wider">1. Riwayat Penyakit Keluarga</span>
                            </div>
                            <div class="p-2 bg-white text-xs">
                                <table class="w-full text-left">
                                    <tbody class="divide-y divide-slate-100">
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-500 font-bold w-7/12">Umum</td>
                                            <td class="py-1.5 px-2 text-center w-1/12">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 w-4/12">{{ $getAnmVal('ANM-RK-001', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Hipertensi (Darah Tinggi)</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RK-002') !== 'Tidak' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RK-002', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Kencing Manis (Diabetes)</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RK-003') !== 'Tidak' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RK-003', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Jantung & Pembuluh Darah</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RK-004') !== 'Tidak' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RK-004', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Kanker</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RK-005') !== 'Tidak' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RK-005', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Asthma</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RK-006') !== 'Tidak' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RK-006', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Penyakit Lainnya</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800">{{ $getAnmVal('ANM-RK-007', '-') }}</td>
                                        </tr>
                                        @if($getAnmVal('ANM-RK-008') !== '-' || $getAnmVal('ANM-RK-009') !== '-')
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-500 pl-6">- Lainnya Ayah / Ibu</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800">
                                                A: {{ $getAnmVal('ANM-RK-008', '-') }} / I: {{ $getAnmVal('ANM-RK-009', '-') }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Meninggal Dunia?</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800">{{ $getAnmVal('ANM-RK-010', 'Tidak') }}</td>
                                        </tr>
                                        @if($getAnmVal('ANM-RK-011') !== '-' || $getAnmVal('ANM-RK-012') !== '-')
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-500 pl-6">- Penyebab Ayah / Ibu</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 text-[10px]">
                                                A: {{ $getAnmVal('ANM-RK-011', '-') }} / I: {{ $getAnmVal('ANM-RK-012', '-') }}
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Riwayat Penyakit Pribadi -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-100 px-3 py-2 border-b border-slate-200 flex justify-between items-center">
                                <span class="font-extrabold text-xs text-slate-700 uppercase tracking-wider">2. Riwayat Penyakit Pribadi</span>
                            </div>
                            <div class="p-2 bg-white text-xs">
                                <table class="w-full text-left">
                                    <tbody class="divide-y divide-slate-100">
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-500 font-bold w-7/12">Umum</td>
                                            <td class="py-1.5 px-2 text-center w-1/12">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 w-4/12">{{ $getAnmVal('ANM-RP-001', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Hipertensi (Darah Tinggi)</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-002') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-002', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Kencing Manis (Diabetes)</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-003') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-003', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Jantung & Pembuluh Darah</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-004') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-004', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Kanker</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-005') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-005', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Asthma</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-006') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-006', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Penyakit Paru-paru</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold {{ $getAnmVal('ANM-RP-011') === 'Ya' ? 'text-amber-600 font-bold' : 'text-slate-800' }}">{{ $getAnmVal('ANM-RP-011', 'Tidak') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Ambeien / Alergi</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800">
                                                Amb: {{ $getAnmVal('ANM-RP-008', 'Tidak') }} / Alerg: {{ $getAnmVal('ANM-RP-009', 'Tidak') }}
                                            </td>
                                        </tr>
                                        @if($getAnmVal('ANM-RP-010') !== '-')
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-500 pl-6">- Alergi Lainnya</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 text-[10px]">{{ $getAnmVal('ANM-RP-010', '-') }}</td>
                                        </tr>
                                        @endif
                                        @if($mcuRegistration->patient->gender == 'P')
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Riwayat Haid</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 text-[10px]">{{ $getAnmVal('ANM-RP-012', '-') }} {{ $getAnmVal('ANM-RP-013') !== '-' ? '(' . $getAnmVal('ANM-RP-013') . ')' : '' }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="py-1.5 px-2 text-slate-600 pl-4">Penyakit Lain / Tambahan</td>
                                            <td class="py-1.5 px-2 text-center">:</td>
                                            <td class="py-1.5 px-2 font-semibold text-slate-800 text-[10px]">{{ $getAnmVal('ANM-RP-007', '-') }} / {{ $getAnmVal('ANM-RP-014', '-') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Riwayat Rawat Inap, Operasi, Obat Rutin, & Kebiasaan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Rawat Inap, Operasi, Obat -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-100 px-3 py-2 border-b border-slate-200">
                                <span class="font-extrabold text-xs text-slate-700 uppercase tracking-wider">3. Riwayat Medis Lainnya</span>
                            </div>
                            <div class="p-3 bg-white text-xs space-y-3">
                                <div>
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Riwayat Rawat Inap Rumah Sakit</span>
                                    <ul class="list-disc pl-4 mt-1 space-y-1 font-semibold text-slate-800">
                                        @if($getAnmVal('ANM-RI-001') !== '-' || $getAnmVal('ANM-RI-002') !== '-' || $getAnmVal('ANM-RI-003') !== '-')
                                            @if($getAnmVal('ANM-RI-001') !== '-') <li>{{ $getAnmVal('ANM-RI-001') }}</li> @endif
                                            @if($getAnmVal('ANM-RI-002') !== '-') <li>{{ $getAnmVal('ANM-RI-002') }}</li> @endif
                                            @if($getAnmVal('ANM-RI-003') !== '-') <li>{{ $getAnmVal('ANM-RI-003') }}</li> @endif
                                        @else
                                            <li class="list-none pl-0 text-slate-500 font-medium italic">Tidak ada riwayat rawat inap</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="border-t pt-2">
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Riwayat Bedah / Operasi</span>
                                    <span class="font-semibold text-slate-800 mt-1 block">{{ $getAnmVal('ANM-OP-001', 'Tidak ada riwayat operasi') }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Penggunaan Obat Rutin</span>
                                    <span class="font-semibold text-slate-800 mt-1 block">{{ $getAnmVal('ANM-OB-001', 'Tidak ada konsumsi obat rutin') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kebiasaan (Habits) -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-100 px-3 py-2 border-b border-slate-200">
                                <span class="font-extrabold text-xs text-slate-700 uppercase tracking-wider">4. Kebiasaan & Gaya Hidup</span>
                            </div>
                            <div class="p-3 bg-white text-xs space-y-3">
                                <div>
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Kebiasaan Merokok</span>
                                    <span class="font-semibold text-slate-800 mt-1 block">{{ $getAnmVal('ANM-KB-001', '-') }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Aktivitas Fisik / Olahraga</span>
                                    <span class="font-semibold text-slate-800 mt-1 block">{{ $getAnmVal('ANM-KB-002', '-') }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <span class="text-slate-400 font-bold block text-[9px] uppercase tracking-wide">Pola Makan & Minum</span>
                                    <span class="font-semibold text-slate-800 mt-1 block">{{ $getAnmVal('ANM-KB-003', '-') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            @endif

            <!-- Hasil Fisik -->
            @if($mcuRegistration->physicalExamResult)
            @php $phys = $mcuRegistration->physicalExamResult; @endphp
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Pemeriksaan Fisik</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-bold text-gray-700 text-sm mb-2 border-b">Tanda Vital & Antropometri</h4>
                            <table class="w-full text-sm">
                                <tr><td class="py-1 text-gray-600">Tinggi Badan</td><td class="py-1 font-medium">{{ $phys->vital_signs['tinggi_badan'] ?? '-' }} cm</td></tr>
                                <tr><td class="py-1 text-gray-600">Berat Badan</td><td class="py-1 font-medium">{{ $phys->vital_signs['berat_badan'] ?? '-' }} kg</td></tr>
                                <tr><td class="py-1 text-gray-600">BMI</td><td class="py-1 font-medium">{{ $phys->vital_signs['bmi'] ?? '-' }} ({{ $phys->vital_signs['bmi_kesimpulan'] ?? '-' }})</td></tr>
                                <tr><td class="py-1 text-gray-600">Lingkar Pinggang</td><td class="py-1 font-medium">{{ $phys->vital_signs['lingkar_pinggang'] ?? '-' }} cm</td></tr>
                                <tr><td class="py-1 text-gray-600">Tensi Rata-rata</td><td class="py-1 font-medium">{{ $phys->vital_signs['tensi_avg_sys'] ?? '-' }}/{{ $phys->vital_signs['tensi_avg_dia'] ?? '-' }} mmHg</td></tr>
                                <tr><td class="py-1 text-gray-600">Nadi</td><td class="py-1 font-medium">{{ $phys->vital_signs['nadi'] ?? '-' }} x/mnt</td></tr>
                            </table>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-700 text-sm mb-2 border-b">Kepala & Mata</h4>
                            <table class="w-full text-sm">
                                <tr><td class="py-1 text-gray-600">Kepala</td><td class="py-1 font-medium">{{ $phys->head_and_neck['kepala'] ?? '-' }}</td></tr>
                                <tr><td class="py-1 text-gray-600">Mata (Sklera)</td><td class="py-1 font-medium">{{ $phys->eye['sklera'] ?? '-' }}</td></tr>
                                <tr><td class="py-1 text-gray-600">Buta Warna</td><td class="py-1 font-medium">{{ $phys->eye['buta_warna'] ?? '-' }}</td></tr>
                            </table>
                            <h4 class="font-bold text-gray-700 text-sm mt-4 mb-2 border-b">Visus</h4>
                            <table class="w-full text-sm">
                                <tr><td class="py-1 text-gray-600">OD (Kanan)</td><td class="py-1 font-medium">Sph: {{ $phys->visus['od_spheris'] ?? '-' }}, Cyl: {{ $phys->visus['od_silindris'] ?? '-' }}</td></tr>
                                <tr><td class="py-1 text-gray-600">OS (Kiri)</td><td class="py-1 font-medium">Sph: {{ $phys->visus['os_spheris'] ?? '-' }}, Cyl: {{ $phys->visus['os_silindris'] ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Hasil Lab -->
            @if($mcuRegistration->examLabs->count() > 0)
            @php
                $categoryOrder = [
                    'Hematologi Lengkap',
                    'Urine',
                    'Urine Sedimen',
                    'Fungsi Liver',
                    'Lipid Profil',
                    'Fungsi Ginjal',
                    'Glukosa Darah',
                    'Imunologi',
                    'Lainnya'
                ];
                $groupedExams = $mcuRegistration->examLabs->groupBy(function($exam) {
                    return $exam->lab->category ?? 'Lainnya';
                });
            @endphp
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Laboratorium</h3>
                </div>
                <div class="p-4 space-y-6">
                    @foreach($categoryOrder as $categoryName)
                        @php
                            $exams = $groupedExams->get($categoryName);
                        @endphp
                        @if($exams && $exams->count() > 0)
                            @php
                                $normalExams = $exams->filter(function($exam) {
                                    return !str_ends_with($exam->lab->code, '-KES');
                                })->sortBy('lab.display_order');

                                $summaryExam = $exams->first(function($exam) {
                                    return str_ends_with($exam->lab->code, '-KES');
                                });
                            @endphp
                            
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <!-- Category Title Header -->
                                <div class="bg-slate-100 px-3.5 py-2 flex justify-between items-center border-b border-gray-200">
                                    <span class="font-extrabold text-xs text-slate-800 uppercase tracking-wider">{{ $categoryName }}</span>
                                </div>
                                
                                <div class="p-2 bg-white">
                                    @if($normalExams->count() > 0)
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-slate-50 text-slate-600 border-b border-gray-200 uppercase font-bold text-[10px]">
                                                <th class="py-2 px-3 w-4/12">Pemeriksaan</th>
                                                <th class="py-2 px-3 text-center w-2/12">Hasil</th>
                                                <th class="py-2 px-3 text-center w-3/12">Nilai Rujukan</th>
                                                <th class="py-2 px-3 w-3/12">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($normalExams as $examLab)
                                            <tr>
                                                <td class="py-2 px-3 text-slate-700 font-medium">{{ $examLab->lab->name }}</td>
                                                <td class="py-2 px-3 text-center font-bold {{ $examLab->is_abnormal ? 'text-red-600' : 'text-slate-900' }}">
                                                    {{ $examLab->result_value ?? '-' }}
                                                </td>
                                                <td class="py-2 px-3 text-center text-slate-500 font-mono">{{ $examLab->normal_value ?? $examLab->lab->normal_value }}</td>
                                                <td class="py-2 px-3 text-slate-500 font-mono">{{ $examLab->lab->unit }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                    
                                    @if($summaryExam)
                                    <div class="mt-2 p-2 bg-teal-50 border border-teal-100 rounded text-xs flex items-start gap-1.5">
                                        <i class="fas fa-square-poll-horizontal text-teal-600 mt-0.5"></i>
                                        <div>
                                            <span class="font-bold text-teal-800 block text-[10px] uppercase tracking-wide">Rekapitulasi / Kesimpulan {{ $categoryName }}:</span>
                                            <p class="text-teal-900 font-medium mt-0.5 whitespace-pre-line">{{ $summaryExam->result_value ?: 'Dalam Batas Normal' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Hasil Penunjang Medis Non-Lab -->
            @if($mcuRegistration->examRadiologies->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-heart-pulse text-teal-600"></i>
                        Penunjang Medis Non-Lab
                    </h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider w-1/3">Jenis Pemeriksaan</th>
                                <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider text-center w-1/4">Status Temuan</th>
                                <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider w-5/12">Hasil / Temuan Klinis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($mcuRegistration->examRadiologies->sortBy('radiology.display_order') as $index => $examRad)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4 align-middle font-bold text-slate-800">
                                    <span class="text-slate-400 font-normal mr-1">{{ $index + 1 }}.</span>
                                    {{ $examRad->radiology->name }}
                                </td>
                                <td class="py-3 px-4 align-middle text-center">
                                    @if($examRad->is_normal)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Normal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                            Abnormal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 align-middle text-slate-800 font-medium text-xs whitespace-pre-line">{{ $examRad->findings ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Hasil Konsultasi & Tindakan Dokter -->
            @if($mcuRegistration->examMedicalActions->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg border border-slate-200">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-md text-teal-600"></i>
                        Konsultasi & Tindakan Medis Dokter
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach($mcuRegistration->examMedicalActions as $index => $exam)
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 hover:border-teal-400 transition-colors">
                            <div class="flex items-start gap-2.5 mb-2">
                                <div class="w-7 h-7 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-teal-700">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-800 text-xs uppercase tracking-tight block">
                                        {{ $exam->medicalAction->name }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block">
                                        Kode: {{ $exam->medicalAction->code }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 rounded p-3 text-xs text-slate-700 font-medium whitespace-pre-line shadow-inner">
                                {{ $exam->result ?: 'Dalam Batas Normal' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Kolom Kanan: Form Kesimpulan & Saran -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm sm:rounded-lg sticky top-6">
                <div class="p-4 bg-blue-50 border-b border-blue-100">
                    <h3 class="text-lg font-bold text-blue-800">Tulis Kesimpulan</h3>
                </div>
                
                <form action="{{ route('mcu-resumes.store', $mcuRegistration) }}" method="POST" class="p-4">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="conclusion">Kesimpulan MCU</label>
                        <textarea name="conclusion" id="conclusion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('conclusion') border-red-500 @enderror" placeholder="Contoh: Fit to work, dengan catatan...">{{ old('conclusion', $mcuRegistration->conclusion) }}</textarea>
                        @error('conclusion') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="recommendation">Saran / Anjuran Dokter</label>
                        <textarea name="recommendation" id="recommendation" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('recommendation') border-red-500 @enderror" placeholder="Contoh: Diet rendah garam, olahraga 3x seminggu...">{{ old('recommendation', $mcuRegistration->recommendation) }}</textarea>
                        @error('recommendation') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan & Selesaikan Resume
                        </button>
                        @if($mcuRegistration->status == 'completed')
                        <a href="{{ route('mcu-resumes.pdf', $mcuRegistration->id) }}" target="_blank" class="w-full bg-red-500 hover:bg-red-600 text-white text-center font-bold py-2 px-4 rounded">
                            Cetak Laporan PDF
                        </a>
                        @endif
                    </div>
                    
                    @if($mcuRegistration->resume_by)
                    <div class="mt-6 text-xs text-gray-500 text-center border-t pt-4">
                        Terakhir diupdate oleh: <strong>{{ $mcuRegistration->resumeDoctor->name ?? 'Dokter' }}</strong><br>
                        Pada: {{ date('d M Y H:i', strtotime($mcuRegistration->resume_date)) }}
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

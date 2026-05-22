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
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Laboratorium</h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-2 px-3 font-semibold">Pemeriksaan</th>
                                <th class="py-2 px-3 font-semibold text-center">Hasil</th>
                                <th class="py-2 px-3 font-semibold text-center">Nilai Rujukan</th>
                                <th class="py-2 px-3 font-semibold">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mcuRegistration->examLabs as $examLab)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $examLab->lab->name }}</td>
                                <td class="py-2 px-3 text-center font-bold {{ $examLab->is_abnormal ? 'text-red-600' : 'text-gray-900' }}">{{ $examLab->result ?? '-' }}</td>
                                <td class="py-2 px-3 text-center text-gray-500">{{ $examLab->normal_value ?? $examLab->lab->normal_value }}</td>
                                <td class="py-2 px-3 text-gray-500">{{ $examLab->lab->unit }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Hasil Radiologi -->
            @if($mcuRegistration->examRadiologies->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Radiologi</h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-2 px-3 font-semibold">Pemeriksaan</th>
                                <th class="py-2 px-3 font-semibold">Hasil Bacaan / Kesimpulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mcuRegistration->examRadiologies as $examRad)
                            <tr class="border-b">
                                <td class="py-2 px-3 align-top font-medium w-1/3">{{ $examRad->radiology->name }}</td>
                                <td class="py-2 px-3 whitespace-pre-line text-gray-800">{{ $examRad->result ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

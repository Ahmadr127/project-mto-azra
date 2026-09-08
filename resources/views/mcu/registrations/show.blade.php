@extends('layouts.app')
@section('title', 'Detail Registrasi MCU')

@section('content')
<div class="w-full">
    <!-- Header Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200">
        <div class="p-6 bg-gradient-to-r from-green-600 to-green-700 text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff;">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-xs uppercase font-bold tracking-wider bg-green-800 bg-opacity-50 px-2 py-1 rounded" style="background-color: rgba(6, 78, 59, 0.5);">Detail Registrasi MCU</span>
                    <h2 class="text-3xl font-extrabold mt-1">{{ $mcuRegistration->patient->name }}</h2>
                    <p class="text-green-100 text-sm mt-1">No RM: <span class="font-semibold">{{ $mcuRegistration->patient->patient_code }}</span> | Tanggal Daftar: {{ date('d M Y', strtotime($mcuRegistration->registration_date)) }}</p>
                </div>
                <div class="bg-white text-green-800 font-bold px-4 py-2 rounded-xl shadow-lg text-center flex-shrink-0">
                    <span class="block text-xs text-gray-500 uppercase font-medium">Status MCU</span>
                    @if($mcuRegistration->status == 'completed')
                        <span class="text-green-600 font-extrabold text-sm uppercase">Selesai</span>
                    @elseif($mcuRegistration->status == 'in_progress')
                        <span class="text-yellow-600 font-extrabold text-sm uppercase">Dalam Proses</span>
                    @elseif($mcuRegistration->status == 'cancelled')
                        <span class="text-red-600 font-extrabold text-sm uppercase">Dibatalkan</span>
                    @else
                        <span class="text-blue-600 font-extrabold text-sm uppercase">Terdaftar</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="p-6 bg-white grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="text-gray-500 text-xs uppercase font-bold block mb-1">Paket Pemeriksaan</span>
                <span class="text-gray-900 font-bold text-lg block">{{ $mcuRegistration->package->name }}</span>
                <span class="text-gray-500 text-sm">Tarif Dasar: Rp {{ number_format($mcuRegistration->package->base_price) }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-xs uppercase font-bold block mb-1">Biodata Pasien</span>
                <span class="text-gray-900 font-semibold block">{{ $mcuRegistration->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}, {{ $mcuRegistration->patient->age }} Tahun</span>
                <span class="text-gray-500 text-sm">No HP: {{ $mcuRegistration->patient->phone ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-xs uppercase font-bold block mb-1">Dokter Resume</span>
                <span class="text-gray-900 font-semibold block">
                    {{ $mcuRegistration->resumeDoctor->name ?? 'Belum ada' }}
                </span>
                @if($mcuRegistration->resume_date)
                    <span class="text-gray-500 text-sm">Tgl: {{ date('d M Y', strtotime($mcuRegistration->resume_date)) }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Detail Table Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6 mb-6">
        <h3 class="font-bold text-xl text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-list-check text-green-600"></i>
            Daftar Layanan MCU yang Didapatkan
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pemeriksaan</th>
                        <th scope="col" class="px-6 py-3 text-center class=px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hasil/Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    <!-- Anamnesis Items -->
                    @forelse($mcuRegistration->examAnamneses as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">Anamnesis</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $exam->anamnesis->name ?? 'Anamnesis Default' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($exam->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $exam->result ?? '-' }}</td>
                    </tr>
                    @empty
                    @endforelse

                    <!-- Lab Items -->
                    @forelse($mcuRegistration->examLabs as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">Laboratorium</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $exam->lab->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($exam->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($exam->status == 'completed')
                                <span class="font-semibold">{{ $exam->result_value }}</span> 
                                <span class="text-xs text-gray-500">(Normal: {{ $exam->normal_value ?? '-' }})</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    @endforelse

                    <!-- Non-Lab Items -->
                    @forelse($mcuRegistration->examRadiologies as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-teal-600">Penunjang Non-Lab</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $exam->radiology->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($exam->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $exam->result ?? '-' }}</td>
                    </tr>
                    @empty
                    @endforelse

                    <!-- Physical Exam -->
                    @if($mcuRegistration->physicalExamResult)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600">Pemeriksaan Fisik</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Pemeriksaan Fisik Dokter</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($mcuRegistration->physicalExamResult->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($mcuRegistration->physicalExamResult->status == 'completed')
                                <span class="text-green-600 font-semibold"><i class="fas fa-check-circle"></i> Hasil diinput oleh dr. {{ $mcuRegistration->physicalExamResult->doctor->name ?? '' }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endif

                    <!-- Medical Action / Consultation -->
                    @forelse($mcuRegistration->examMedicalActions as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">Tindakan / Konsultasi</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $exam->medicalAction->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($exam->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $exam->result ?? '-' }}</td>
                    </tr>
                    @empty
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('mcu-registrations.index') }}" class="text-gray-600 hover:text-gray-900 font-semibold flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        @if($mcuRegistration->status == 'completed')
            <a href="{{ route('mcu-resumes.pdf', $mcuRegistration->id) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Cetak Hasil PDF
            </a>
        @else
            <a href="{{ route('mcu-resumes.show', $mcuRegistration->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-stethoscope"></i> Lakukan Review / Input Resume
            </a>
        @endif
    </div>
</div>
@endsection
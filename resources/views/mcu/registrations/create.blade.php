@extends('layouts.app')
@section('title', 'Pendaftaran MCU')

@section('content')
<div class="w-full mx-auto max-w-3xl" x-data="{ 
    selectedPackageId: '', 
    packages: @json($packages),
    get selectedPackage() {
        let self = this;
        return this.packages.find(function(p) { return p.id == self.selectedPackageId; }) || null;
    }
}">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-file-medical text-green-600"></i>
                Pendaftaran MCU Baru
            </h2>

            <form action="{{ route('mcu-registrations.store') }}" method="POST">
                @csrf
                
                <!-- Pilih Pasien -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pasien <span class="text-red-500">*</span></label>
                    <select name="patient_id" class="shadow border rounded-lg w-full py-2.5 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Pilih Paket MCU -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Paket MCU <span class="text-red-500">*</span></label>
                    <select name="mcu_package_id" x-model="selectedPackageId" class="shadow border rounded-lg w-full py-2.5 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} - Rp {{ number_format($package->base_price) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Live Preview Paket Item -->
                <div x-show="selectedPackage" class="mb-6 bg-green-50 bg-opacity-50 border border-green-200 rounded-xl p-5" x-transition>
                    <h3 class="font-bold text-green-800 text-base mb-3 flex items-center gap-2 border-b border-green-200 pb-2">
                        <i class="fas fa-circle-info text-green-600"></i>
                        Detail Item Pemeriksaan Paket: <span class="text-green-900 font-extrabold" x-text="selectedPackage.name"></span>
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Tindakan Medis -->
                        <template x-if="selectedPackage && selectedPackage.items.filter(i => i.item_type.includes('McuMedicalAction')).length > 0">
                            <div>
                                <span class="text-xs uppercase font-extrabold text-orange-600 block mb-1.5 tracking-wider">Tindakan / Konsultasi Dokter</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in selectedPackage.items.filter(i => i.item_type.includes('McuMedicalAction'))" :key="item.id">
                                        <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs px-2.5 py-1 rounded-lg font-semibold border border-orange-200" x-text="item.item ? item.item.name : 'Unknown'"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Lab -->
                        <template x-if="selectedPackage && selectedPackage.items.filter(i => i.item_type.includes('McuLab')).length > 0">
                            <div>
                                <span class="text-xs uppercase font-extrabold text-blue-600 block mb-1.5 tracking-wider">Laboratorium (Lab)</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in selectedPackage.items.filter(i => i.item_type.includes('McuLab'))" :key="item.id">
                                        <span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-semibold border border-blue-200" x-text="item.item ? item.item.name : 'Unknown'"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Radiologi -->
                        <template x-if="selectedPackage && selectedPackage.items.filter(i => i.item_type.includes('McuRadiology')).length > 0">
                            <div>
                                <span class="text-xs uppercase font-extrabold text-teal-600 block mb-1.5 tracking-wider">Penunjang Medis Non-Lab</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in selectedPackage.items.filter(i => i.item_type.includes('McuRadiology'))" :key="item.id">
                                        <span class="inline-flex items-center bg-teal-100 text-teal-800 text-xs px-2.5 py-1 rounded-lg font-semibold border border-teal-200" x-text="item.item ? item.item.name : 'Unknown'"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Anamnesis -->
                        <template x-if="selectedPackage && selectedPackage.items.filter(i => i.item_type.includes('McuAnamnesis')).length > 0">
                            <div>
                                <span class="text-xs uppercase font-extrabold text-purple-600 block mb-1.5 tracking-wider">Anamnesis</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in selectedPackage.items.filter(i => i.item_type.includes('McuAnamnesis'))" :key="item.id">
                                        <span class="inline-flex items-center bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-lg font-semibold border border-purple-200" x-text="item.item ? item.item.name : 'Unknown'"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Pemeriksaan Fisik -->
                        <template x-if="selectedPackage && selectedPackage.items.filter(i => i.item_type.includes('McuPhysicalExam')).length > 0">
                            <div>
                                <span class="text-xs uppercase font-extrabold text-rose-600 block mb-1.5 tracking-wider">Pemeriksaan Fisik</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in selectedPackage.items.filter(i => i.item_type.includes('McuPhysicalExam'))" :key="item.id">
                                        <span class="inline-flex items-center bg-rose-100 text-rose-800 text-xs px-2.5 py-1 rounded-lg font-semibold border border-rose-200" x-text="item.item ? item.item.name : 'Unknown'"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Tanggal Pendaftaran -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="date" name="registration_date" class="shadow border rounded-lg w-full py-2.5 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 mt-8 border-t pt-4">
                    <a href="{{ route('mcu-registrations.index') }}" class="text-gray-600 hover:text-gray-900 font-semibold px-4 py-2">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all duration-200">
                        <i class="fas fa-save mr-1"></i> Daftar & Siapkan Pemeriksaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
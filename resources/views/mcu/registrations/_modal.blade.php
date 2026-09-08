{{--
    MCU Registration Modal Form
    Props:
    - patients: Collection
    - packages: Collection
    - selectedPatientId: int|null
    - modalId: string
--}}
@props([
    'patients' => collect(),
    'packages' => collect(),
    'selectedPatientId' => null,
    'modalId' => 'mcuRegistrationModal',
])

@php
    // Prepare packages for searchable-select: keep items, add display label
    $packageOptions = $packages->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name . ' - Rp ' . number_format($p->base_price),
            'raw_name' => $p->name,
            'patient_code' => null,
            // keep original for preview
            '_raw' => $p,
        ];
    });
@endphp

<x-form-modal :id="$modalId" title="Pendaftaran MCU Baru" size="2xl">
    <div class="p-6">
        <form action="{{ route('mcu-registrations.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Pasien --}}
            <div>
                @if($selectedPatientId)
                    @php $fixedPatient = $patients->firstWhere('id', $selectedPatientId); @endphp
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pasien <span class="text-red-500">*</span></label>
                    <div class="w-full px-3 py-2.5 text-sm bg-green-50 border border-green-200 rounded-lg font-medium text-green-900">
                        {{ $fixedPatient?->name ?? 'Pasien terpilih' }} ({{ $fixedPatient?->patient_code ?? '' }})
                    </div>
                    <input type="hidden" name="patient_id" value="{{ $selectedPatientId }}">
                    <p class="text-xs text-green-600 mt-1">Pasien otomatis dari detail</p>
                @else
                    <x-searchable-select
                        name="patient_id"
                        label="Pilih Pasien"
                        :options="$patients"
                        value-field="id"
                        label-field="name"
                        sub-label-field="patient_code"
                        placeholder="Cari pasien (nama / No. RM)..."
                        :selected="old('patient_id', $selectedPatientId)"
                        :required="true"
                    />
                @endif
                @error('patient_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Paket --}}
            <div>
                <x-searchable-select
                    name="mcu_package_id"
                    label="Pilih Paket MCU"
                    :options="$packageOptions"
                    value-field="id"
                    label-field="name"
                    placeholder="Cari paket..."
                    :selected="old('mcu_package_id')"
                    :required="true"
                />
                @error('mcu_package_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Pendaftaran <span class="text-red-500">*</span></label>
                <input type="date" name="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}" required
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                @error('registration_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="$dispatch('close-modal', '{{ $modalId }}')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg shadow">Daftar & Siapkan Pemeriksaan</button>
            </div>
        </form>
    </div>
</x-form-modal>

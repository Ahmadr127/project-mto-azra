@extends('layouts.app')
@section('title', 'Pendaftaran MCU')

@section('content')
<div class="w-full" x-data="{ openCreate: false }" x-init="$nextTick(() => $dispatch('open-modal', 'mcuRegistrationModalCreate'))">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pendaftaran MCU</h2>
                    <p class="text-sm text-gray-500 mt-1">Form pendaftaran kini menggunakan modal. Klik tombol untuk membuka form.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('mcu-registrations.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali ke Daftar</a>
                    <button type="button" @click="$dispatch('open-modal', 'mcuRegistrationModalCreate')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow">
                        Buka Form Pendaftaran
                    </button>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                Form pendaftaran MCU sekarang tersedia sebagai modal. Jika modal tidak terbuka otomatis, klik “Buka Form Pendaftaran”.
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @include('mcu.registrations._modal', ['patients' => $patients, 'packages' => $packages, 'selectedPatientId' => $selectedPatientId ?? null, 'modalId' => 'mcuRegistrationModalCreate'])
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Auto open modal on page load, and also when validation fails
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'mcuRegistrationModalCreate' }));
        }, 300);
        @if($errors->any())
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'mcuRegistrationModalCreate' }));
            }, 600);
        @endif
    });
</script>
@endpush
@endsection

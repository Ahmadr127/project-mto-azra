@extends('layouts.app')
@section('title', 'Registrasi MCU')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Registrasi MCU</h2>
                <button type="button" @click="$dispatch('open-modal', 'mcuRegistrationModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Daftar Baru
                </button>
            </div>
        </div>

        {{-- Modal Form Pendaftaran --}}
        @include('mcu.registrations._modal', ['patients' => $patients ?? collect(), 'packages' => $packages ?? collect(), 'selectedPatientId' => null, 'modalId' => 'mcuRegistrationModal'])

        @if($errors->any())
            <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                Terdapat kesalahan pada form pendaftaran. Modal akan terbuka otomatis.
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Registrasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket MCU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($registrations as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->registration_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->patient->name }}<br><span class="text-xs text-gray-500">{{ $item->patient->patient_code }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->package->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">{{ $item->status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('mcu-registrations.show', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                            <form action="{{ route('mcu-registrations.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if($errors->any())
        setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'mcuRegistrationModal' })), 400);
    @endif
});
</script>
@endpush
@endsection
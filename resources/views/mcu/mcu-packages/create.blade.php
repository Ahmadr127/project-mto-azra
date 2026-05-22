@extends('layouts.app')
@section('title', 'Tambah Paket MCU')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tambah Paket MCU</h2>

            <form action="{{ route('mcu-packages.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="code">Kode Paket</label>
                        <input type="text" name="code" id="code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('code') border-red-500 @enderror" value="{{ old('code') }}" required>
                        @error('code') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama Paket</label>
                        <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="base_price">Harga Dasar</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('base_price') border-red-500 @enderror" value="{{ old('base_price', 0) }}" required>
                        @error('base_price') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="display_order">Urutan Tampil</label>
                        <input type="number" name="display_order" id="display_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('display_order') border-red-500 @enderror" value="{{ old('display_order', 0) }}" required>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                        <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <hr class="my-6 border-gray-300">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Pilih Item Default Paket</h3>

                <!-- TABS -->
                <div x-data="{ tab: 'tindakan' }">
                    <div class="mb-6">
                        <nav class="flex flex-wrap gap-2 md:gap-3" aria-label="Tabs">
                            <button type="button" @click="tab = 'tindakan'" :class="tab === 'tindakan' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800'" class="whitespace-nowrap py-2 px-4 rounded-lg font-semibold text-sm transition-all duration-200">Tindakan</button>
                            <button type="button" @click="tab = 'lab'" :class="tab === 'lab' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800'" class="whitespace-nowrap py-2 px-4 rounded-lg font-semibold text-sm transition-all duration-200">Lab</button>
                            <button type="button" @click="tab = 'radiologi'" :class="tab === 'radiologi' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800'" class="whitespace-nowrap py-2 px-4 rounded-lg font-semibold text-sm transition-all duration-200">Penunjang Non-Lab</button>
                            <button type="button" @click="tab = 'anamnesis'" :class="tab === 'anamnesis' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800'" class="whitespace-nowrap py-2 px-4 rounded-lg font-semibold text-sm transition-all duration-200">Anamnesis</button>
                            <button type="button" @click="tab = 'fisik'" :class="tab === 'fisik' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800'" class="whitespace-nowrap py-2 px-4 rounded-lg font-semibold text-sm transition-all duration-200">Pem. Fisik</button>
                        </nav>
                    </div>

                    <!-- TAB CONTENTS -->
                    <div x-show="tab === 'tindakan'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($medicalActions as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuMedicalAction:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ (is_array(old('items')) && in_array("McuMedicalAction:{$item->id}", old('items'))) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'lab'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($labs as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuLab:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ (is_array(old('items')) && in_array("McuLab:{$item->id}", old('items'))) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'radiologi'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($radiologies as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuRadiology:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ (is_array(old('items')) && in_array("McuRadiology:{$item->id}", old('items'))) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'anamnesis'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($anamneses as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuAnamnesis:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ (is_array(old('items')) && in_array("McuAnamnesis:{$item->id}", old('items'))) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'fisik'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($physicalExams as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuPhysicalExam:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ (is_array(old('items')) && in_array("McuPhysicalExam:{$item->id}", old('items'))) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('mcu-packages.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Paket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Edit Paket MCU')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Paket MCU</h2>

            <form action="{{ route('mcu-packages.update', $mcuPackage) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="code">Kode Paket</label>
                        <input type="text" name="code" id="code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('code') border-red-500 @enderror" value="{{ old('code', $mcuPackage->code) }}" required>
                        @error('code') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama Paket</label>
                        <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" value="{{ old('name', $mcuPackage->name) }}" required>
                        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="base_price">Harga Dasar</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('base_price') border-red-500 @enderror" value="{{ old('base_price', $mcuPackage->base_price) }}" required>
                        @error('base_price') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="display_order">Urutan Tampil</label>
                        <input type="number" name="display_order" id="display_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('display_order') border-red-500 @enderror" value="{{ old('display_order', $mcuPackage->display_order) }}" required>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $mcuPackage->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                        <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="1" {{ old('status', $mcuPackage->status) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status', $mcuPackage->status) == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <hr class="my-6 border-gray-300">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Pilih Item Default Paket</h3>

                <!-- TABS -->
                <div x-data="{ tab: 'tindakan' }">
                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button type="button" @click="tab = 'tindakan'" :class="tab === 'tindakan' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Tindakan</button>
                            <button type="button" @click="tab = 'lab'" :class="tab === 'lab' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Lab</button>
                            <button type="button" @click="tab = 'radiologi'" :class="tab === 'radiologi' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Radiologi</button>
                            <button type="button" @click="tab = 'anamnesis'" :class="tab === 'anamnesis' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Anamnesis</button>
                            <button type="button" @click="tab = 'fisik'" :class="tab === 'fisik' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pem. Fisik</button>
                        </nav>
                    </div>

                    <!-- TAB CONTENTS -->
                    <div x-show="tab === 'tindakan'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($medicalActions as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuMedicalAction:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ in_array("McuMedicalAction:{$item->id}", old('items', $selectedItems)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'lab'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($labs as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuLab:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ in_array("McuLab:{$item->id}", old('items', $selectedItems)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'radiologi'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($radiologies as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuRadiology:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ in_array("McuRadiology:{$item->id}", old('items', $selectedItems)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }} ({{ $item->code }})</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'anamnesis'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($anamneses as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuAnamnesis:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ in_array("McuAnamnesis:{$item->id}", old('items', $selectedItems)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="tab === 'fisik'" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                        @foreach($physicalExams as $item)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="items[]" value="McuPhysicalExam:{{ $item->id }}" class="form-checkbox h-5 w-5 text-green-600 rounded" {{ in_array("McuPhysicalExam:{$item->id}", old('items', $selectedItems)) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $item->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('mcu-packages.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Perbarui Paket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
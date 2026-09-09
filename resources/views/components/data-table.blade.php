{{--
    Reusable Data Table Component
    Modes:
    1. Legacy footer only: <x-data-table :paginator="$users" />
    2. Full table with No + per-column search (Enter manual):
       <x-data-table :paginator="$users" :columns="$columns" showNumber>
           @foreach($users as $i=>$user)
             <tr><td>{{ $users->firstItem()+$i }}</td><td>...</td></tr>
           @endforeach
       </x-data-table>

    Props:
    - paginator: LengthAwarePaginator
    - columns: array|null - if set, renders full table wrapper with header + search row
        each column: ['key'=>'filter_name','label'=>'Nama','type'=>'text','placeholder'=>'Cari nama...','searchable'=>true]
        type: text|date|select (default text)
    - perPageOptions: array
    - showInfo: bool
    - showNumber: bool - add No column (default true when columns set)
    - searchMode: 'manual'|'auto' (default manual = Enter / Filter button)
--}}

@props([
    'paginator' => null,
    'columns' => null,
    'perPageOptions' => [10, 25, 50, 100],
    'showInfo' => true,
    'showNumber' => true,
    'searchMode' => 'manual',
])

@php
    $isFullTable = is_array($columns) && count($columns) > 0;
    $formId = 'dataTableForm-' . Str::random(6);
@endphp

@if($isFullTable && $paginator)
    {{-- Full table mode with No + per-column search (Enter manual) - FIX nested form & long URL --}}
    {{-- Filter form terpisah (tidak membungkus tbody) agar _token/_method dari delete tidak ikut ke GET --}}
    <form method="GET" action="{{ request()->url() }}" id="{{ $formId }}" class="hidden">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if($showNumber)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-14">No</th>
                    @endif
                    @foreach($columns as $col)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ $col['label'] ?? $col['key'] }}</th>
                    @endforeach
                </tr>
                {{-- Per-column search row --}}
                <tr class="bg-white border-b border-gray-200">
                    @if($showNumber)
                        <th class="px-2 py-2"></th>
                    @endif
                    @foreach($columns as $col)
                        @php
                            $searchable = $col['searchable'] ?? true;
                            $type = $col['type'] ?? 'text';
                            $key = $col['key'];
                            $placeholder = $col['placeholder'] ?? '';
                            $isLast = $loop->last;
                        @endphp
                        <th class="px-2 py-2">
                            @if($searchable)
                                @if($type === 'date')
                                    <input form="{{ $formId }}" type="date" name="{{ $key }}" value="{{ request($key, '') }}"
                                           class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white">
                                @elseif($type === 'select' && isset($col['options']))
                                    <select form="{{ $formId }}" name="{{ $key }}" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white">
                                        <option value="">Semua</option>
                                        @foreach($col['options'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" @selected(request($key) == $optVal)>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input form="{{ $formId }}" type="text" name="{{ $key }}" value="{{ request($key, '') }}" placeholder="{{ $placeholder }}"
                                           class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 placeholder-gray-400">
                                @endif
                            @else
                                @if($isLast)
                                    <div class="flex gap-1">
                                        <button type="submit" form="{{ $formId }}" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md">
                                            Filter
                                        </button>
                                        @if(request()->hasAny(collect($columns)->pluck('key')->toArray()))
                                            <a href="{{ request()->url() }}{{ request('per_page') ? '?per_page='.request('per_page') : '' }}" class="inline-flex items-center justify-center px-2 py-1.5 bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs rounded-md" title="Reset">
                                                ×
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    {{-- JS: hilangkan param kosong dari URL (filter_patient_code= dll) --}}
    @once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('form[id^="dataTableForm-"]').forEach(function(form){
            form.addEventListener('submit', function(e){
                // disable input kosong agar tidak jadi filter_patient_code=&...
                form.querySelectorAll('input,select').forEach(function(el){
                    // hidden per_page tetap kirim jika ada
                    if(el.name !== 'per_page' && !el.value) el.disabled = true;
                });
                document.querySelectorAll('[form="'+form.id+'"]').forEach(function(el){
                    if(!el.value) el.disabled = true;
                });
            });
        });
    });
    </script>
    @endpush
    @endonce

    {{-- Footer: info + per page + pagination (di luar form, tidak nested) --}}
    <div class="px-6 py-4 border-t border-gray-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        @if($showInfo)
            <div class="text-sm text-gray-600">
                Menampilkan
                <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
                entri
            </div>
        @endif
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <label for="per_page-{{ $formId }}" class="whitespace-nowrap">Baris per halaman:</label>
                <select id="per_page-{{ $formId }}" name="per_page"
                        onchange="const url=new URL(window.location.href); url.searchParams.set('per_page', this.value); url.searchParams.delete('page'); window.location.href=url.toString();"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    @foreach($perPageOptions as $opt)
                        <option value="{{ $opt }}" @selected(request('per_page', $paginator->perPage()) == $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                {{ $paginator->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@elseif($paginator)
    {{-- Legacy footer only mode (for patients etc. that have own table) --}}
    <div class="px-6 py-4 border-t border-gray-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        @if($showInfo)
            <div class="text-sm text-gray-600">
                Menampilkan
                <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
                entri
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <label for="per_page" class="whitespace-nowrap">Baris per halaman:</label>
                <select id="per_page" name="per_page"
                        onchange="const url=new URL(window.location.href); url.searchParams.set('per_page', this.value); url.searchParams.delete('page'); window.location.href=url.toString();"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    @foreach($perPageOptions as $opt)
                        <option value="{{ $opt }}" @selected(request('per_page', $paginator->perPage()) == $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                {{ $paginator->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endif

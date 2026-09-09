{{--
    Per-Column Filter Component - Reusable untuk table dengan filter per kolom (CDN mode, no Vite)
    Usage:
    <form method="GET" action="{{ route('users.index') }}" id="usersFilterForm">
        <table>
            <thead>
                <tr>... header titles ...</tr>
                <x-per-column-filter
                    form-id="usersFilterForm"
                    :columns="[
                        ['key' => 'filter_name', 'placeholder' => 'Cari nama...', 'type' => 'text', 'icon' => true],
                        ['key' => 'filter_nik', 'placeholder' => 'NIK...', 'type' => 'text'],
                        ['key' => 'filter_username', 'placeholder' => 'Username...', 'type' => 'text'],
                        ['key' => 'filter_email', 'placeholder' => 'Email...', 'type' => 'text'],
                        ['key' => 'filter_role', 'placeholder' => 'Role...', 'type' => 'text'],
                        ['key' => 'filter_created_at', 'type' => 'date'],
                        ['key' => null, 'type' => 'action'], // aksi -> tombol Filter
                    ]"
                />
            </thead>
        </table>
    </form>

    Props:
    - formId: id form GET yang membungkus table (untuk debounce JS)
    - columns: array, tiap entry ['key'=>string|null, 'type'=>text|date|select|action, 'placeholder'=>string, 'options'=>[value=>label], 'icon'=>bool]
    - buttonText: label tombol filter (opsional)
--}}

@props([
    'formId' => 'perColumnFilterForm',
    'columns' => [],
    'buttonText' => 'Filter',
    'debounceMs' => 600,
])

<tr class="bg-white border-b border-gray-200">
    @foreach($columns as $col)
        @php
            $key = $col['key'] ?? null;
            $type = $col['type'] ?? 'text';
            $placeholder = $col['placeholder'] ?? '';
            $options = $col['options'] ?? [];
            $icon = $col['icon'] ?? false;
            $value = $key ? request($key) : null;
        @endphp
        <th class="px-2 py-2 align-middle">
            @if($type === 'action')
                <button type="submit" form="{{ $formId }}" class="inline-flex items-center justify-center w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md">
                    <i class="fas fa-filter mr-1"></i> {{ $buttonText }}
                </button>
            @elseif($type === 'date')
                <input form="{{ $formId }}" type="date" name="{{ $key }}" value="{{ $value }}"
                       class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
            @elseif($type === 'select')
                <select form="{{ $formId }}" name="{{ $key }}" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    <option value="">{{ $placeholder ?: 'Semua' }}</option>
                    @foreach($options as $optVal => $optLabel)
                        <option value="{{ $optVal }}" @selected((string)$value === (string)$optVal)>{{ $optLabel }}</option>
                    @endforeach
                </select>
            @elseif($key)
                <div class="relative">
                    @if($icon)
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[11px]"></i>
                        <input form="{{ $formId }}" type="text" name="{{ $key }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
                               class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 placeholder-gray-400">
                    @else
                        <input form="{{ $formId }}" type="text" name="{{ $key }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
                               class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 placeholder-gray-400">
                    @endif
                </div>
            @else
                {{-- kolom tanpa filter (mis. Umur hitungan) --}}
                <span class="block py-1"></span>
            @endif
        </th>
    @endforeach
</tr>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // delegasi untuk semua per-column filter forms (support multiple tables per page)
    // FIX: pakai [form="id"] selector agar input di thead (via form attr) terdeteksi, dan cegah _token/_method ikut ke GET
    const forms = document.querySelectorAll('form[id$="FilterForm"]');
    forms.forEach(function(form) {
        if (form.dataset.perColumnInit) return;
        form.dataset.perColumnInit = '1';
        // hapus param kosong saat submit agar URL tidak panjang filter_=&...
        form.addEventListener('submit', function(){
            document.querySelectorAll('[form="'+form.id+'"]').forEach(function(el){
                if(!el.value) el.disabled = true;
            });
            form.querySelectorAll('input,select').forEach(function(el){
                if(el.name !== 'per_page' && !el.value) el.disabled = true;
            });
        });
        const inputs = document.querySelectorAll('[form="'+form.id+'"]');
        let debounceTimer;
        inputs.forEach(function(input) {
            const isText = input.type === 'text';
            input.addEventListener(isText ? 'input' : 'change', function() {
                clearTimeout(debounceTimer);
                if (isText) {
                    debounceTimer = setTimeout(function() {
                        if (input.value.length === 0 || input.value.length >= 2) {
                            form.requestSubmit();
                        }
                    }, {{ $debounceMs ?? 600 }});
                } else {
                    form.requestSubmit();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    form.requestSubmit();
                }
            });
        });
    });
});
</script>
@endpush
@endonce

{{--
    Searchable Select Component - Modern searchable select untuk form
    Usage:
    <x-searchable-select
        name="patient_id"
        label="Pilih Pasien"
        :options="$patients"
        value-field="id"
        label-field="name"
        sub-label-field="patient_code"
        placeholder="Cari pasien..."
        :selected="old('patient_id', $selectedId)"
        :required="true"
    />

    Props:
    - name: field name
    - label: label text
    - options: Collection/array
    - valueField: field for value (default id)
    - labelField: field for label (default name)
    - subLabelField: secondary label (e.g. code)
    - placeholder: placeholder text
    - selected: selected value
    - required: bool
    - disabled: bool
--}}

@props([
    'name',
    'label' => null,
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'subLabelField' => null,
    'placeholder' => 'Pilih...',
    'selected' => null,
    'required' => false,
    'disabled' => false,
])

@php
    $uid = 'ss-'.Str::random(8);
    $selectedValue = old($name, $selected);
    $optionsArray = collect($options)->map(fn($opt) => [
        'value' => data_get($opt, $valueField),
        'label' => data_get($opt, $labelField),
        'subLabel' => $subLabelField ? data_get($opt, $subLabelField) : null,
        'searchText' => trim((data_get($opt, $labelField) ?? '').' '.($subLabelField ? (data_get($opt, $subLabelField) ?? '') : '')),
    ])->values();
@endphp

<div
    x-data="searchableSelect({
        options: {{ Js::from($optionsArray) }},
        selected: {{ Js::from($selectedValue) }},
        placeholder: @js($placeholder),
    })"
    class="relative"
    @click.away="close()"
    id="{{ $uid }}"
>
    @if($label)
        <label for="{{ $uid }}-trigger" class="block text-xs font-semibold text-gray-700 mb-1.5">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    {{-- Hidden input --}}
    <input type="hidden" name="{{ $name }}" :value="selectedValue" @if($required) required @endif>

    {{-- Trigger --}}
    <button
        type="button"
        id="{{ $uid }}-trigger"
        @click="toggle()"
        :disabled="{{ $disabled ? 'true' : 'false' }}"
        class="relative w-full bg-white border rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition {{ $disabled ? 'bg-gray-100 cursor-not-allowed border-gray-200' : 'border-gray-300 hover:border-gray-400' }}"
        :class="{ 'ring-2 ring-green-500 border-green-500': open }"
    >
        <span class="block truncate text-sm" :class="selectedValue ? 'text-gray-900 font-medium' : 'text-gray-400'">
            <span x-text="displayText"></span>
            <span x-show="selectedSubLabel" class="text-gray-400 font-normal ml-1" x-text="'(' + selectedSubLabel + ')'"></span>
        </span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <span class="text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }">▾</span>
        </span>
    </button>

    {{-- Dropdown - fixed to escape modal overflow --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        x-ref="dropdown"
        class="fixed z-[60] bg-white shadow-xl rounded-lg border border-gray-200 max-h-64 flex flex-col overflow-hidden"
        :style="dropdownStyle"
        x-cloak
    >
        {{-- Search --}}
        <div class="p-2 border-b border-gray-100 bg-gray-50">
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">⌕</span>
                <input
                    type="text"
                    x-model="search"
                    x-ref="searchInput"
                    @keydown.escape="close()"
                    @keydown.enter.prevent="selectFirst()"
                    placeholder="Ketik untuk mencari..."
                    class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white"
                >
            </div>
        </div>

        {{-- Options --}}
        <div class="overflow-y-auto flex-1">
            <ul class="py-1">
                <template x-for="opt in filteredOptions" :key="String(opt.value)">
                    <li
                        @click="select(opt.value)"
                        class="cursor-pointer select-none relative py-2.5 pl-3 pr-9 hover:bg-green-50 flex items-center justify-between"
                        :class="{ 'bg-green-50 text-green-900': String(selectedValue) === String(opt.value) }"
                    >
                        <div class="flex flex-col">
                            <span class="block text-sm font-medium truncate" x-text="opt.label"></span>
                            <span x-show="opt.subLabel" class="block text-xs text-gray-400 truncate" x-text="opt.subLabel"></span>
                        </div>
                        <span x-show="String(selectedValue) === String(opt.value)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-green-600">✓</span>
                    </li>
                </template>

                <template x-if="filteredOptions.length === 0">
                    <li class="py-3 px-3 text-sm text-gray-400 text-center">
                        <span x-show="search">Tidak ada hasil untuk "<span x-text="search"></span>"</span>
                        <span x-show="!search">Tidak ada opsi</span>
                    </li>
                </template>
            </ul>
        </div>

        <div x-show="filteredOptions.length > 6" class="px-3 py-1.5 bg-gray-50 border-t border-gray-100 text-xs text-gray-400 text-center">
            <span x-text="filteredOptions.length"></span> opsi
        </div>
    </div>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
function searchableSelect(config) {
    return {
        open: false,
        search: '',
        dropdownPosition: { top: 0, left: 0, width: 0, height: 0 },
        dropUp: false,
        options: config.options || [],
        selectedValue: config.selected !== null && config.selected !== undefined ? String(config.selected) : '',
        placeholder: config.placeholder || 'Pilih...',

        get dropdownStyle() {
            if (this.dropUp) {
                return `bottom: ${window.innerHeight - this.dropdownPosition.top + 4}px; left: ${this.dropdownPosition.left}px; width: ${this.dropdownPosition.width}px;`;
            }
            return `top: ${this.dropdownPosition.top + this.dropdownPosition.height + 4}px; left: ${this.dropdownPosition.left}px; width: ${this.dropdownPosition.width}px;`;
        },

        updatePosition() {
            const rect = this.$el.getBoundingClientRect();
            this.dropdownPosition = {
                top: rect.top,
                left: rect.left,
                width: rect.width,
                height: rect.height
            };
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            this.dropUp = spaceBelow < 260 && spaceAbove > spaceBelow;
        },

        get filteredOptions() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o =>
                (o.label && o.label.toLowerCase().includes(q)) ||
                (o.subLabel && o.subLabel.toLowerCase().includes(q)) ||
                (o.searchText && o.searchText.toLowerCase().includes(q))
            );
        },
        get displayText() {
            if (!this.selectedValue) return this.placeholder;
            const found = this.options.find(o => String(o.value) === String(this.selectedValue));
            return found ? found.label : this.placeholder;
        },
        get selectedSubLabel() {
            if (!this.selectedValue) return '';
            const found = this.options.find(o => String(o.value) === String(this.selectedValue));
            return found ? (found.subLabel || '') : '';
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.updatePosition();
                this.$nextTick(() => this.$refs.searchInput?.focus());
            }
        },
        close() {
            this.open = false;
            this.search = '';
        },
        select(val) {
            this.selectedValue = val !== null ? String(val) : '';
            this.close();
            // dispatch change for Alpine watchers
            this.$dispatch('ss-change', { name: '{{ $name }}', value: this.selectedValue });
        },
        selectFirst() {
            if (this.filteredOptions.length) this.select(this.filteredOptions[0].value);
        }
    }
}
</script>
@endpush
@endonce

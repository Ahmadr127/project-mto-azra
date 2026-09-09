{{--
    Date Range Filter Component - di luar table, untuk MCU Registrations dll (hanya MCU, user tidak perlu)
    Usage:
    <x-date-range-filter
        from-key="filter_date_from"
        to-key="filter_date_to"
        label="Tgl Registrasi"
        default-range="30 days"
    />
    Props:
    - fromKey: query key untuk dari (default filter_date_from)
    - toKey: query key untuk sampai (default filter_date_to)
    - label: label atas (optional)
    - auto: bool auto submit on change (default true)
    - defaultRange: string dinamis untuk default otomatis jika request kosong
        Pilihan siap pakai (komentar):
        '7 days'   => 7 hari terakhir
        '14 days'  => 14 hari
        '30 days'  => 30 hari / 1 bulan (DEFAULT - otomatis ter-apply)
        '60 days'  => 60 hari / 2 bulan
        '90 days'  => 3 bulan
        '1 week'   => 1 minggu
        '2 weeks'  => 2 minggu
        '1 month'  => 1 bulan kalender
        '2 months' => 2 bulan kalender
        '3 months' => 3 bulan kalender
        '6 months' => 6 bulan
        '1 year'   => 1 tahun
        null / ''  => tanpa default (tampilkan semua)
        Cara ubah global: ganti default '30 days' di bawah, atau per-view <x-date-range-filter default-range="60 days" />
    - defaultFrom / defaultTo: override explicit Y-m-d (prioritas di atas defaultRange)
--}}

@props([
    'fromKey' => 'filter_date_from',
    'toKey' => 'filter_date_to',
    'label' => 'Rentang Tanggal',
    'auto' => true,
    'defaultRange' => '30 days',
    'defaultFrom' => null,
    'defaultTo' => null,
])

@php
    use App\Support\DateRangeHelper;
    $formId = 'dateRangeForm-' . Str::random(6);
    // Hitung default dinamis jika request kosong - agar semua yang pakai component otomatis ter-apply
    $defaults = DateRangeHelper::parse($defaultRange);
    $defFrom = $defaultFrom ?? $defaults[0];
    $defTo = $defaultTo ?? $defaults[1];
    // Jika request ada, hargai request; jika tidak, pakai default (otomatis ter-apply)
    $fromVal = request($fromKey, $defFrom ?? '');
    $toVal = request($toKey, $defTo ?? '');
    // hasDate untuk styling & tombol Reset (jika ada filter, tampilkan Reset)
    // Reset nanti kembali ke default, bukan ke kosong
    $hasDate = request()->hasAny([$fromKey, $toKey]) || ($defFrom && $defTo);
    $isDefault = request($fromKey) === $defFrom && request($toKey) === $defTo;
@endphp

<form method="GET" action="{{ request()->url() }}" id="{{ $formId }}" class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex flex-col sm:flex-row gap-3 items-end">
    {{-- Preserve semua query selain date range & page (agar per-column filter & per_page tetap) --}}
    @foreach(request()->except([$fromKey, $toKey, 'page']) as $k => $v)
        @if(is_array($v))
            @foreach($v as $vv)
                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    <div class="flex-1">
        @if($label)
            <div class="text-xs font-semibold text-gray-600 mb-1">{{ $label }}</div>
        @endif
        <div class="flex gap-2">
            <div class="flex-1">
                <label for="{{ $fromKey }}-{{ $formId }}" class="block text-[11px] text-gray-500 mb-1">Dari</label>
                <input type="date" id="{{ $fromKey }}-{{ $formId }}" name="{{ $fromKey }}" value="{{ $fromVal }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @if($hasDate) border-green-300 @endif">
            </div>
            <div class="flex-1">
                <label for="{{ $toKey }}-{{ $formId }}" class="block text-[11px] text-gray-500 mb-1">Sampai</label>
                <input type="date" id="{{ $toKey }}-{{ $formId }}" name="{{ $toKey }}" value="{{ $toVal }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @if($hasDate) border-green-300 @endif">
            </div>
        </div>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg inline-flex items-center gap-1">
            <i class="fas fa-calendar-alt text-xs"></i> Filter Tanggal
        </button>
        @if($hasDate)
            {{-- Reset kembali ke default range, bukan ke kosong --}}
            <a href="{{ request()->url() }}?{{ http_build_query(array_merge(request()->except([$fromKey, $toKey, 'page']), [$fromKey => $defFrom, $toKey => $defTo])) }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold rounded-lg inline-flex items-center" title="Reset ke default {{ $defaultRange }}">
                Reset
            </a>
        @endif
    </div>
</form>


@if($auto)
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('form[id^="dateRangeForm-"]').forEach(function(form){
        const from = form.querySelector('input[name*="from"]');
        const to = form.querySelector('input[name*="to"]');
        [from,to].forEach(function(el){
            if(!el) return;
            el.addEventListener('change', function(){
                // auto submit jika salah satu terisi, atau keduanya kosong (reset)
                // validasi: from <= to jika keduanya ada
                if(from.value && to.value && from.value > to.value){
                    // swap atau alert? kita set to = from
                    // biarkan server handle, tapi warning
                }
                form.requestSubmit();
            });
        });
    });
});
</script>
@endpush
@endonce
@endif

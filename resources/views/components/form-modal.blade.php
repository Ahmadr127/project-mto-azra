{{--
    Form Modal Component - Reusable modal untuk form
    Usage:
    <x-form-modal id="myModal" title="Judul Modal" size="lg">
        <form>...</form>
    </x-form-modal>

    Trigger via: @click="$dispatch('open-modal', 'myModal')" atau x-data open
    Props:
    - id: unique modal id
    - title: modal title
    - size: sm|md|lg|xl|2xl|full (default lg)
    - closable: bool (default true)
    - closeOnBackdrop: bool (default true)
--}}

@props([
    'id' => 'formModal-'.Str::random(8),
    'title' => null,
    'size' => 'lg',
    'closable' => true,
    'closeOnBackdrop' => true,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        'full' => 'max-w-6xl',
    ];
    $maxWidth = $sizeClasses[$size] ?? $sizeClasses['lg'];
@endphp

<div
    x-data="{ open: false }"
    x-init="
        window.addEventListener('open-modal', e => { if(e.detail === '{{ $id }}') open = true });
        window.addEventListener('close-modal', e => { if(e.detail === '{{ $id }}') open = false });
        $watch('open', val => { document.body.style.overflow = val ? 'hidden' : '' });
    "
    @keydown.escape.window="open = false"
    x-cloak
>
    {{-- Backdrop + Container --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @if($closeOnBackdrop) @click="open = false" @endif
        ></div>

        {{-- Modal Panel --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full {{ $maxWidth }} max-h-[90vh] flex flex-col bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
            @click.away="open = false"
        >
            {{-- Header --}}
            @if($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                    <h3 class="text-base font-bold text-gray-900">{{ $title }}</h3>
                    @if($closable)
                        <button type="button" @click="open = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <span class="text-xl leading-none">×</span>
                        </button>
                    @endif
                </div>
            @else
                @if($closable)
                    <button type="button" @click="open = false" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <span class="text-xl leading-none">×</span>
                    </button>
                @endif
            @endif

            {{-- Body (scrollable) --}}
            <div class="flex-1 overflow-y-auto">
                {{ $slot }}
            </div>

            {{-- Footer slot (optional) --}}
            @isset($footer)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>

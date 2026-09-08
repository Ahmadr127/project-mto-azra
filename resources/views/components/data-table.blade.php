{{--
    Reusable Data Table Component
    Usage:
    <x-data-table :paginator="$users" />
    Place inside a wrapper that contains the <table>. The component renders:
    - entries info
    - per-page selector (auto-submit)
    - pagination links (preserves query string)

    Props:
    - paginator: LengthAwarePaginator
    - perPageOptions: array
    - showInfo: bool
--}}

@props([
    'paginator' => null,
    'perPageOptions' => [10, 25, 50, 100],
    'showInfo' => true,
])

@if($paginator)
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
            {{-- Per page selector --}}
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

            {{-- Pagination links --}}
            <div>
                {{ $paginator->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endif

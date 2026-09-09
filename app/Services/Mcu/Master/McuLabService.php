<?php

namespace App\Services\Mcu\Master;

use App\Models\McuLab;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuLabService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuLab::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'code', $search, 'or');
            });
        }
        // Per-column (data-table) - case-insensitive
        if ($request->filled('filter_code')) SearchHelper::whereLike($query, 'code', $request->filter_code);
        if ($request->filled('filter_name')) SearchHelper::whereLike($query, 'name', $request->filter_name);
        if ($request->filled('filter_category')) SearchHelper::whereLike($query, 'category', $request->filter_category);
        if ($request->filled('filter_status')) $query->where('status', $request->filter_status);
        return $query->orderBy('display_order')->latest()->paginate($request->input('per_page', 10))->withQueryString();
    }

    public function create(array $data): McuLab
    {
        return McuLab::create($data);
    }

    public function update(McuLab $lab, array $data): McuLab
    {
        $lab->update($data);
        return $lab;
    }

    public function delete(McuLab $lab): void
    {
        $lab->delete();
    }
}

<?php

namespace App\Services\Mcu\Master;

use App\Models\McuLab;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuLabService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuLab::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
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

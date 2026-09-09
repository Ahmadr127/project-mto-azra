<?php

namespace App\Services\Mcu\Master;

use App\Models\McuMedicalAction;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuMedicalActionService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuMedicalAction::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'code', $search, 'or');
            });
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
    }
    public function create(array $data): McuMedicalAction { return McuMedicalAction::create($data); }
    public function update(McuMedicalAction $m, array $data): McuMedicalAction { $m->update($data); return $m; }
    public function delete(McuMedicalAction $m): void { $m->delete(); }
}

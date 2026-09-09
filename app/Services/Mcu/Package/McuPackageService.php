<?php

namespace App\Services\Mcu\Package;

use App\Models\McuPackage;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class McuPackageService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuPackage::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'code', $search, 'or');
            });
        }
        if ($request->filled('filter_code')) SearchHelper::whereLike($query, 'code', $request->filter_code);
        if ($request->filled('filter_name')) SearchHelper::whereLike($query, 'name', $request->filter_name);
        if ($request->filled('filter_status')) $query->where('status', $request->filter_status);
        return $query->orderBy('display_order')->latest()->paginate($request->input('per_page', 10))->withQueryString();
    }

    public function create(array $data): McuPackage
    {
        return DB::transaction(function () use ($data) {
            $package = McuPackage::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'base_price' => $data['base_price'],
                'description' => $data['description'] ?? null,
                'display_order' => $data['display_order'],
                'status' => $data['status'],
            ]);
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    [$type, $id] = explode(':', $item);
                    $modelClass = "App\\Models\\" . $type;
                    if (class_exists($modelClass)) {
                        $package->items()->create(['item_type' => $modelClass, 'item_id' => $id]);
                    }
                }
            }
            return $package;
        });
    }

    public function update(McuPackage $package, array $data): McuPackage
    {
        return DB::transaction(function () use ($package, $data) {
            $package->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'base_price' => $data['base_price'],
                'description' => $data['description'] ?? null,
                'display_order' => $data['display_order'],
                'status' => $data['status'],
            ]);
            $package->items()->delete();
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    [$type, $id] = explode(':', $item);
                    $modelClass = "App\\Models\\" . $type;
                    if (class_exists($modelClass)) {
                        $package->items()->create(['item_type' => $modelClass, 'item_id' => $id]);
                    }
                }
            }
            return $package;
        });
    }

    public function delete(McuPackage $package): void
    {
        $package->delete();
    }
}

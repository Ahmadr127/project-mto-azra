<?php

namespace App\Services\Mcu\Package;

use App\Models\McuPackage;
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
            $query->where(fn($q) => $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%"));
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
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

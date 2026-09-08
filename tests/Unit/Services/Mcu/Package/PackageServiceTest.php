<?php

namespace Tests\Unit\Services\Mcu\Package;

use App\Models\McuLab;
use App\Models\McuPackage;
use App\Services\Mcu\Package\McuPackageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PackageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected McuPackageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(McuPackageService::class);
    }

    public function test_paginate_with_search(): void
    {
        McuPackage::create(['code' => 'PKT-A', 'name' => 'Paket A', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        McuPackage::create(['code' => 'PKT-B', 'name' => 'Paket B', 'base_price' => 200000, 'display_order' => 2, 'status' => true]);

        $request = Request::create('/', 'GET', ['search' => 'Paket A']);
        $result = $this->service->paginate($request);
        $this->assertCount(1, $result);
        $this->assertEquals('Paket A', $result->first()->name);
    }

    public function test_create_with_items(): void
    {
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true]);
        $pkg = $this->service->create([
            'code' => 'PKT-NEW', 'name' => 'New Paket', 'base_price' => 500000,
            'display_order' => 1, 'status' => true, 'items' => ["McuLab:{$lab->id}"],
        ]);
        $this->assertDatabaseHas('mcu_packages', ['code' => 'PKT-NEW']);
        $this->assertCount(1, $pkg->items);
        $this->assertEquals($lab->id, $pkg->items->first()->item_id);
    }

    public function test_create_without_items(): void
    {
        $pkg = $this->service->create([
            'code' => 'PKT-NO', 'name' => 'No Items', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ]);
        $this->assertCount(0, $pkg->items);
    }

    public function test_create_ignores_invalid_item_class(): void
    {
        $pkg = $this->service->create([
            'code' => 'PKT-INV', 'name' => 'Invalid', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
            'items' => ['NonExistent:1'],
        ]);
        $this->assertCount(0, $pkg->items);
    }

    public function test_update_syncs_items(): void
    {
        $lab1 = McuLab::create(['code' => 'LAB-1', 'name' => 'Lab 1', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $lab2 = McuLab::create(['code' => 'LAB-2', 'name' => 'Lab 2', 'price' => 10000, 'display_order' => 2, 'status' => true]);
        $pkg = McuPackage::create(['code' => 'PKT-SYNC', 'name' => 'Sync', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $pkg->items()->create(['item_type' => McuLab::class, 'item_id' => $lab1->id]);

        $this->service->update($pkg, [
            'code' => 'PKT-SYNC', 'name' => 'Sync Updated', 'base_price' => 200000, 'display_order' => 2, 'status' => true,
            'items' => ["McuLab:{$lab2->id}"],
        ]);

        $pkg->refresh();
        $this->assertEquals('Sync Updated', $pkg->name);
        $this->assertCount(1, $pkg->items);
        $this->assertEquals($lab2->id, $pkg->items->first()->item_id);
    }

    public function test_update_removes_all_items_when_empty(): void
    {
        $lab = McuLab::create(['code' => 'LAB-1', 'name' => 'Lab 1', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $pkg = McuPackage::create(['code' => 'PKT-EMPTY', 'name' => 'Empty', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $pkg->items()->create(['item_type' => McuLab::class, 'item_id' => $lab->id]);

        $this->service->update($pkg, [
            'code' => 'PKT-EMPTY', 'name' => 'Empty', 'base_price' => 100000, 'display_order' => 1, 'status' => true, 'items' => [],
        ]);

        $this->assertCount(0, $pkg->fresh()->items);
    }

    public function test_delete(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-DEL', 'name' => 'Del', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $id = $pkg->id;
        $this->service->delete($pkg);
        $this->assertDatabaseMissing('mcu_packages', ['id' => $id]);
    }
}

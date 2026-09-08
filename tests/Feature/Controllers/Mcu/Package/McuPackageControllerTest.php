<?php

namespace Tests\Feature\Controllers\Mcu\Package;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McuPackageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $role->permissions()->attach(Permission::firstOrCreate(['name' => 'view_dashboard'], ['display_name' => 'View']));
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('mcu-packages.index'))->assertRedirect(route('login'));
        $this->actingAs($this->admin)->get(route('mcu-packages.index'))->assertStatus(200);
    }

    public function test_index_search(): void
    {
        McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket A', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        McuPackage::create(['code' => 'PKT-002', 'name' => 'Paket B', 'base_price' => 200000, 'display_order' => 2, 'status' => true]);

        $response = $this->actingAs($this->admin)->get(route('mcu-packages.index', ['search' => 'Paket A']));
        $this->assertStringContainsString('Paket A', $response->getContent());
        $this->assertStringNotContainsString('Paket B', $response->getContent());
    }

    public function test_create_returns_view_with_masters(): void
    {
        McuLab::create(['code' => 'LAB-001', 'name' => 'Lab 1', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $response = $this->actingAs($this->admin)->get(route('mcu-packages.create'));
        $response->assertStatus(200);
        $response->assertViewHas(['medicalActions', 'labs', 'radiologies', 'anamneses', 'physicalExams']);
    }

    public function test_store_creates_package_with_items(): void
    {
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true]);
        $rad = McuRadiology::create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);

        $data = [
            'code' => 'PKT-TEST-001',
            'name' => 'Paket Test',
            'base_price' => 500000,
            'description' => 'Desc',
            'display_order' => 1,
            'status' => true,
            'items' => ["McuLab:{$lab->id}", "McuRadiology:{$rad->id}"],
        ];

        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), $data);
        $response->assertRedirect(route('mcu-packages.index'));
        $this->assertDatabaseHas('mcu_packages', ['code' => 'PKT-TEST-001']);
        $pkg = McuPackage::where('code', 'PKT-TEST-001')->first();
        $this->assertCount(2, $pkg->items);
    }

    public function test_store_creates_package_without_items(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'code' => 'PKT-NO-ITEM', 'name' => 'No Item', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ]);
        $response->assertRedirect(route('mcu-packages.index'));
        $this->assertDatabaseHas('mcu_packages', ['code' => 'PKT-NO-ITEM']);
        $this->assertCount(0, McuPackage::where('code', 'PKT-NO-ITEM')->first()->items);
    }

    public function test_store_fails_validation(): void
    {
        // Missing code
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'name' => 'No Code', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ]);
        $response->assertSessionHasErrors('code');

        // Duplicate code
        McuPackage::create(['code' => 'PKT-DUP', 'name' => 'Dup', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'code' => 'PKT-DUP', 'name' => 'Dup2', 'base_price' => 100000, 'display_order' => 2, 'status' => true,
        ]);
        $response->assertSessionHasErrors('code');

        // Missing name
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'code' => 'PKT-NONAME', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ]);
        $response->assertSessionHasErrors('name');

        // Invalid price negative
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'code' => 'PKT-NEG', 'name' => 'Neg', 'base_price' => -100, 'display_order' => 1, 'status' => true,
        ]);
        $response->assertSessionHasErrors('base_price');

        // Invalid status
        $response = $this->actingAs($this->admin)->post(route('mcu-packages.store'), [
            'code' => 'PKT-STAT', 'name' => 'Stat', 'base_price' => 100000, 'display_order' => 1, 'status' => 'invalid',
        ]);
        $response->assertSessionHasErrors('status');
    }

    public function test_store_requires_auth(): void
    {
        $response = $this->post(route('mcu-packages.store'), [
            'code' => 'PKT-AUTH', 'name' => 'Auth', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_edit_returns_view(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-EDIT', 'name' => 'Edit', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $response = $this->actingAs($this->admin)->get(route('mcu-packages.edit', $pkg));
        $response->assertStatus(200);
        $response->assertViewHas('mcuPackage');
        $response->assertViewHas('selectedItems');
    }

    public function test_edit_404(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-packages.edit', 99999))->assertStatus(404);
    }

    public function test_update_success(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-UPD', 'name' => 'Old', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $lab = McuLab::create(['code' => 'LAB-UPD', 'name' => 'Lab Upd', 'price' => 10000, 'display_order' => 1, 'status' => true]);

        $response = $this->actingAs($this->admin)->put(route('mcu-packages.update', $pkg), [
            'code' => 'PKT-UPD-NEW',
            'name' => 'New Name',
            'base_price' => 200000,
            'display_order' => 2,
            'status' => false,
            'items' => ["McuLab:{$lab->id}"],
        ]);
        $response->assertRedirect(route('mcu-packages.index'));
        $this->assertDatabaseHas('mcu_packages', ['code' => 'PKT-UPD-NEW', 'name' => 'New Name']);
        $this->assertCount(1, $pkg->fresh()->items);
    }

    public function test_update_syncs_items_replace(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-SYNC', 'name' => 'Sync', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $lab1 = McuLab::create(['code' => 'LAB-S1', 'name' => 'Lab S1', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $lab2 = McuLab::create(['code' => 'LAB-S2', 'name' => 'Lab S2', 'price' => 10000, 'display_order' => 2, 'status' => true]);
        $pkg->items()->create(['item_type' => McuLab::class, 'item_id' => $lab1->id]);

        $this->actingAs($this->admin)->put(route('mcu-packages.update', $pkg), [
            'code' => 'PKT-SYNC', 'name' => 'Sync', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
            'items' => ["McuLab:{$lab2->id}"],
        ]);

        $this->assertCount(1, $pkg->fresh()->items);
        $this->assertEquals($lab2->id, $pkg->fresh()->items->first()->item_id);
    }

    public function test_update_fails_when_code_duplicate(): void
    {
        McuPackage::create(['code' => 'PKT-DUP1', 'name' => 'Dup1', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $pkg2 = McuPackage::create(['code' => 'PKT-DUP2', 'name' => 'Dup2', 'base_price' => 100000, 'display_order' => 2, 'status' => true]);

        $response = $this->actingAs($this->admin)->put(route('mcu-packages.update', $pkg2), [
            'code' => 'PKT-DUP1', 'name' => 'Dup2', 'base_price' => 100000, 'display_order' => 2, 'status' => true,
        ]);
        $response->assertSessionHasErrors('code');
    }

    public function test_update_requires_auth(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-AUTH-UPD', 'name' => 'Auth', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $this->put(route('mcu-packages.update', $pkg), [
            'code' => 'PKT-AUTH-UPD', 'name' => 'Auth', 'base_price' => 100000, 'display_order' => 1, 'status' => true,
        ])->assertRedirect(route('login'));
    }

    public function test_destroy_success(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-DEL', 'name' => 'Del', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $id = $pkg->id;
        $response = $this->actingAs($this->admin)->delete(route('mcu-packages.destroy', $pkg));
        $response->assertRedirect(route('mcu-packages.index'));
        $this->assertDatabaseMissing('mcu_packages', ['id' => $id]);
    }

    public function test_destroy_requires_auth(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-DEL-AUTH', 'name' => 'Del', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $this->delete(route('mcu-packages.destroy', $pkg))->assertRedirect(route('login'));
    }

    public function test_destroy_404(): void
    {
        $this->actingAs($this->admin)->delete(route('mcu-packages.destroy', 99999))->assertStatus(404);
    }
}

<?php

namespace Tests\Feature\Controllers\Mcu\Master;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterControllersTest extends TestCase
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

    protected function auth() { return $this->actingAs($this->admin); }

    // Helper to test a master resource: $modelClass, $routePrefix, $factoryData
    protected function assertMasterCrud(string $modelClass, string $routePrefix, array $validData, array $updateData): void
    {
        // INDEX requires auth
        $this->get(route($routePrefix.'.index'))->assertRedirect(route('login'));
        $this->auth()->get(route($routePrefix.'.index'))->assertStatus(200);

        // CREATE
        $this->auth()->get(route($routePrefix.'.create'))->assertStatus(200);

        // STORE success
        $response = $this->auth()->post(route($routePrefix.'.store'), $validData);
        $response->assertRedirect(route($routePrefix.'.index'));
        $this->assertDatabaseHas((new $modelClass)->getTable(), ['code' => $validData['code']]);

        // STORE validation fails: missing code
        $response = $this->auth()->post(route($routePrefix.'.store'), ['name' => 'No Code']);
        $response->assertSessionHasErrors('code');

        // STORE validation fails: duplicate code
        $response = $this->auth()->post(route($routePrefix.'.store'), $validData);
        $response->assertSessionHasErrors('code');

        // STORE validation fails: missing name
        $invalid = $validData;
        unset($invalid['name']);
        $response = $this->auth()->post(route($routePrefix.'.store'), $invalid);
        $response->assertSessionHasErrors('name');

        // STORE validation fails: invalid price
        $invalid = $validData;
        $invalid['code'] = $validData['code'].'-2';
        $invalid['price'] = -10;
        $response = $this->auth()->post(route($routePrefix.'.store'), $invalid);
        $response->assertSessionHasErrors('price');

        // EDIT
        $model = $modelClass::where('code', $validData['code'])->first();
        $this->auth()->get(route($routePrefix.'.edit', $model))->assertStatus(200);
        $this->app['auth']->logout();
        $this->get(route($routePrefix.'.edit', $model))->assertRedirect(route('login'));
        $this->auth();

        // UPDATE success
        $response = $this->auth()->put(route($routePrefix.'.update', $model), $updateData);
        $response->assertRedirect(route($routePrefix.'.index'));
        $this->assertDatabaseHas((new $modelClass)->getTable(), ['code' => $updateData['code']]);

        // UPDATE validation fails: duplicate code taken by other
        $other = $modelClass::create(array_merge($validData, ['code' => $validData['code'].'-OTHER', 'name' => 'Other']));
        $response = $this->auth()->put(route($routePrefix.'.update', $model), array_merge($updateData, ['code' => $other->code]));
        $response->assertSessionHasErrors('code');

        // UPDATE requires auth
        $this->app['auth']->logout();
        $this->put(route($routePrefix.'.update', $model), $updateData)->assertRedirect(route('login'));
        $this->auth();

        // DESTROY
        $id = $model->id;
        $response = $this->auth()->delete(route($routePrefix.'.destroy', $model));
        $response->assertRedirect(route($routePrefix.'.index'));
        $this->assertDatabaseMissing((new $modelClass)->getTable(), ['id' => $id]);

        // DESTROY requires auth
        $model2 = $modelClass::create(array_merge($validData, ['code' => $validData['code'].'-DEL2', 'name' => 'Del2']));
        $this->app['auth']->logout();
        $this->delete(route($routePrefix.'.destroy', $model2))->assertRedirect(route('login'));
        $this->auth();

        // 404 for not found
        $this->auth()->get(route($routePrefix.'.edit', 99999))->assertStatus(404);
        $this->auth()->put(route($routePrefix.'.update', 99999), $updateData)->assertStatus(404);
        $this->auth()->delete(route($routePrefix.'.destroy', 99999))->assertStatus(404);
    }

    public function test_mcu_lab_crud_all_conditions(): void
    {
        $this->assertMasterCrud(McuLab::class, 'mcu-labs', [
            'code' => 'LAB-TEST-001',
            'name' => 'Lab Test',
            'category' => 'Hematologi',
            'price' => 25000,
            'description' => 'Desc',
            'display_order' => 1,
            'status' => true,
        ], [
            'code' => 'LAB-TEST-001-UPD',
            'name' => 'Lab Updated',
            'category' => 'Hematologi',
            'price' => 30000,
            'description' => 'Updated',
            'display_order' => 2,
            'status' => false,
        ]);

        // Test search filter
        McuLab::create(['code' => 'LAB-SEARCH-001', 'name' => 'SEARCHABLE LAB', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        McuLab::create(['code' => 'LAB-OTHER-001', 'name' => 'Other Lab', 'price' => 10000, 'display_order' => 2, 'status' => true]);
        $response = $this->auth()->get(route('mcu-labs.index', ['search' => 'SEARCHABLE']));
        $this->assertStringContainsString('SEARCHABLE LAB', $response->getContent());
    }

    public function test_mcu_radiology_crud(): void
    {
        $this->assertMasterCrud(McuRadiology::class, 'mcu-radiologies', [
            'code' => 'RAD-TEST-001', 'name' => 'Radiology Test', 'category' => 'Radiologi',
            'price' => 150000, 'description' => 'Desc', 'display_order' => 1, 'status' => true,
        ], [
            'code' => 'RAD-TEST-001-UPD', 'name' => 'Radiology Updated', 'category' => 'Radiologi',
            'price' => 160000, 'description' => 'Upd', 'display_order' => 2, 'status' => false,
        ]);
    }

    public function test_mcu_anamnesis_crud(): void
    {
        $this->assertMasterCrud(McuAnamnesis::class, 'mcu-anamneses', [
            'code' => 'ANA-TEST-001', 'name' => 'Anamnesis Test', 'category' => 'Riwayat',
            'price' => 0, 'description' => 'Desc', 'display_order' => 1, 'status' => true,
        ], [
            'code' => 'ANA-TEST-001-UPD', 'name' => 'Anamnesis Updated', 'category' => 'Riwayat',
            'price' => 0, 'description' => 'Upd', 'display_order' => 2, 'status' => true,
        ]);
    }

    public function test_mcu_physical_exam_crud(): void
    {
        $this->assertMasterCrud(McuPhysicalExam::class, 'mcu-physical-exams', [
            'code' => 'FIS-TEST-001', 'name' => 'Physical Test', 'category' => 'Fisik',
            'price' => 10000, 'description' => 'Desc', 'display_order' => 1, 'status' => true,
        ], [
            'code' => 'FIS-TEST-001-UPD', 'name' => 'Physical Updated', 'category' => 'Fisik',
            'price' => 11000, 'description' => 'Upd', 'display_order' => 2, 'status' => false,
        ]);
    }

    public function test_mcu_medical_action_crud(): void
    {
        $this->assertMasterCrud(McuMedicalAction::class, 'mcu-medical-actions', [
            'code' => 'TND-TEST-001', 'name' => 'Tindakan Test', 'category' => 'Tindakan',
            'price' => 50000, 'description' => 'Desc', 'display_order' => 1, 'status' => true,
        ], [
            'code' => 'TND-TEST-001-UPD', 'name' => 'Tindakan Updated', 'category' => 'Tindakan',
            'price' => 55000, 'description' => 'Upd', 'display_order' => 2, 'status' => true,
        ]);
    }

    public function test_master_store_fails_when_display_order_missing(): void
    {
        $response = $this->auth()->post(route('mcu-labs.store'), [
            'code' => 'LAB-NO-DISP', 'name' => 'Test', 'price' => 10000, 'status' => true,
        ]);
        $response->assertSessionHasErrors('display_order');
    }

    public function test_master_store_fails_when_status_invalid(): void
    {
        $response = $this->auth()->post(route('mcu-labs.store'), [
            'code' => 'LAB-STAT-INV', 'name' => 'Test', 'price' => 10000, 'display_order' => 1, 'status' => 'invalid',
        ]);
        $response->assertSessionHasErrors('status');
    }
}

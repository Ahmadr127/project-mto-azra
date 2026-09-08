<?php

namespace Tests\Unit\Filters\Patient;

use App\Filters\Patient\PatientFilter;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PatientFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function makeFilter(array $params): PatientFilter
    {
        $request = Request::create('/', 'GET', $params);
        return new PatientFilter($request);
    }

    public function test_apply_global_search_filters_name_code_nik(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi Wijaya', 'nik' => '3271000000000001']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi Santoso', 'nik' => '3271000000000002']);
        Patient::create(['patient_code' => 'RM-003', 'name' => 'Citra Lestari', 'nik' => '3271000000000003']);

        $filter = $this->makeFilter(['search' => 'Andi']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('Andi Wijaya', $result->first()->name);
    }

    public function test_global_search_matches_patient_code(): void
    {
        Patient::create(['patient_code' => 'RM-XYZ-123', 'name' => 'Test']);
        Patient::create(['patient_code' => 'RM-ABC-456', 'name' => 'Other']);

        $filter = $this->makeFilter(['search' => 'XYZ']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('RM-XYZ-123', $result->first()->patient_code);
    }

    public function test_per_column_filter_patient_code(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'A']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'B']);

        $filter = $this->makeFilter(['filter_patient_code' => '001']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('RM-001', $result->first()->patient_code);
    }

    public function test_per_column_filter_name(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi']);

        $filter = $this->makeFilter(['filter_name' => 'Budi']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('Budi', $result->first()->name);
    }

    public function test_per_column_filter_phone(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'A', 'phone' => '08123456789']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'B', 'phone' => '08987654321']);

        $filter = $this->makeFilter(['filter_phone' => '0812']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('08123456789', $result->first()->phone);
    }

    public function test_filter_gender(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Laki', 'gender' => 'L']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Perempuan', 'gender' => 'P']);
        Patient::create(['patient_code' => 'RM-003', 'name' => 'Unknown', 'gender' => null]);

        $filter = $this->makeFilter(['filter_gender' => 'L']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('L', $result->first()->gender);
    }

    public function test_combined_filters(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi Wijaya', 'gender' => 'L', 'phone' => '08123456789']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Andi Santoso', 'gender' => 'P', 'phone' => '08123456789']);

        $filter = $this->makeFilter(['filter_name' => 'Andi', 'filter_gender' => 'L']);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(1, $result);
        $this->assertEquals('Andi Wijaya', $result->first()->name);
    }

    public function test_per_page_returns_default_when_invalid(): void
    {
        $filter = $this->makeFilter(['per_page' => '999']);
        $this->assertEquals(10, $filter->perPage());

        $filter = $this->makeFilter(['per_page' => '25']);
        $this->assertEquals(25, $filter->perPage());

        $filter = $this->makeFilter([]);
        $this->assertEquals(10, $filter->perPage());
    }

    public function test_apply_returns_builder_for_chaining(): void
    {
        $filter = $this->makeFilter([]);
        $query = Patient::query();
        $result = $filter->apply($query);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    public function test_no_filters_returns_all(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'A']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'B']);

        $filter = $this->makeFilter([]);
        $result = $filter->apply(Patient::query())->get();

        $this->assertCount(2, $result);
    }
}

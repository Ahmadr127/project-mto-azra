<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\Patient;
use App\Services\Mcu\Registration\McuRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

// Migrate fresh for sqlite memory? Use RefreshDatabase via test? Simulate manually
Artisan::call('migrate:fresh', ['--database' => 'sqlite', '--force' => true]);
echo "Migrated\n";

$patient = Patient::create(['patient_code' => 'RM-TEST', 'name' => 'Test']);
$fis = McuPhysicalExam::create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);
$package = McuPackage::create(['code' => 'PKT-TEST', 'name' => 'Paket Test', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
$package->items()->create(['item_type' => McuPhysicalExam::class, 'item_id' => $fis->id]);

$service = app(McuRegistrationService::class);
$reg = $service->create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now()->format('Y-m-d')]);

echo "Reg ID: ".$reg->id."\n";
echo "PhysicalExamResult: ".var_export($reg->physicalExamResult, true)."\n";
echo "PhysicalExamResult via query: ".var_export($reg->physicalExamResult()->first(), true)."\n";
echo "Count: ". $reg->physicalExamResult()->count()."\n";
echo "All items: ". $package->items->count()."\n";
foreach ($package->items as $item) {
    echo "Item type: ".$item->item_type." id: ".$item->item_id."\n";
    echo "Basename: ".class_basename($item->item_type)."\n";
}

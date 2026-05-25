<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\McuPackage;
use App\Models\McuPackageItem;
use App\Models\McuAnamnesis;
use App\Models\McuPhysicalExam;
use App\Models\McuLab;
use App\Models\McuRadiology;
use App\Models\McuMedicalAction;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Demo Patients (prefixed with "Demo")
        $demoPatients = [
            [
                'patient_code' => 'DEMO-RM-001',
                'nik' => '3271011234560001',
                'name' => 'Demo Pasien Andi Wijaya',
                'gender' => 'L',
                'birth_date' => '1992-05-14',
                'age' => 34,
                'address' => 'Jl. Pajajaran No. 12, Bogor Tengah, Kota Bogor',
                'phone' => '081234567891',
                'company' => 'PT Demo Azra Healthcare',
                'department' => 'Teknologi Informasi',
                'employee_status' => 'Tetap',
                'bpjs' => '0001423456789',
            ],
            [
                'patient_code' => 'DEMO-RM-002',
                'nik' => '3271021234560002',
                'name' => 'Demo Pasien Budi Santoso',
                'gender' => 'L',
                'birth_date' => '1985-11-23',
                'age' => 40,
                'address' => 'Jl. Baranangsiang Indah No. 5, Kota Bogor',
                'phone' => '085712345678',
                'company' => 'PT Demo Azra Healthcare',
                'department' => 'Keuangan & Akuntansi',
                'employee_status' => 'Tetap',
                'bpjs' => '0001423456790',
            ],
            [
                'patient_code' => 'DEMO-RM-003',
                'nik' => '3271031234560003',
                'name' => 'Demo Pasien Citra Lestari',
                'gender' => 'P',
                'birth_date' => '1995-08-09',
                'age' => 30,
                'address' => 'Perumahan Yasmin Sektor 3, Bogor Barat',
                'phone' => '081912345678',
                'company' => 'PT Karya Nusa Indah',
                'department' => 'Sumber Daya Manusia',
                'employee_status' => 'Kontrak',
                'bpjs' => '0001423456791',
            ],
            [
                'patient_code' => 'DEMO-RM-004',
                'nik' => '3271041234560004',
                'name' => 'Demo Pasien Diana Putri',
                'gender' => 'P',
                'birth_date' => '1998-03-30',
                'age' => 28,
                'address' => 'Jl. Pemuda No. 45, Tanah Sareal, Kota Bogor',
                'phone' => '081312345678',
                'company' => 'PT Karya Nusa Indah',
                'department' => 'Pemasaran & Penjualan',
                'employee_status' => 'Magang',
                'bpjs' => null,
            ],
            [
                'patient_code' => 'DEMO-RM-005',
                'nik' => '3271051234560005',
                'name' => 'Demo Pasien Eko Prasetyo',
                'gender' => 'L',
                'birth_date' => '1988-07-20',
                'age' => 37,
                'address' => 'Jl. Pajajaran Indah No. 4, Kota Bogor',
                'phone' => '081212345678',
                'company' => 'PT Semesta Raya',
                'department' => 'Operasional',
                'employee_status' => 'Tetap',
                'bpjs' => '0001423456792',
            ],
            [
                'patient_code' => 'DEMO-RM-006',
                'nik' => '3271061234560006',
                'name' => 'Demo Pasien Fitri Handayani',
                'gender' => 'P',
                'birth_date' => '1993-12-02',
                'age' => 32,
                'address' => 'Jl. Lawang Gintung No. 15, Bogor Selatan',
                'phone' => '085612345678',
                'company' => 'PT Semesta Raya',
                'department' => 'Keuangan',
                'employee_status' => 'Tetap',
                'bpjs' => '0001423456793',
            ],
            [
                'patient_code' => 'DEMO-RM-007',
                'nik' => '3271071234560007',
                'name' => 'Demo Pasien Gilang Ramadhan',
                'gender' => 'L',
                'birth_date' => '1990-09-18',
                'age' => 35,
                'address' => 'Perumahan Cimanggu Permai Blok C, Tanah Sareal, Kota Bogor',
                'phone' => '087812345678',
                'company' => 'PT Global Informatika',
                'department' => 'Support Teknikal',
                'employee_status' => 'Kontrak',
                'bpjs' => null,
            ],
        ];

        foreach ($demoPatients as $patientData) {
            Patient::updateOrCreate(
                ['patient_code' => $patientData['patient_code']],
                $patientData
            );
        }

        // 2. Seed Demo Packages (prefixed with "Demo")
        $demoPackages = [
            [
                'code' => 'DEMO-PKT-BASIC',
                'name' => 'Demo Paket Basic',
                'base_price' => 125000.00,
                'description' => 'Paket demo dasar dengan Anamnesis dan Pemeriksaan Fisik dasar.',
                'display_order' => 10,
                'status' => true,
            ],
            [
                'code' => 'DEMO-PKT-COMPLETE',
                'name' => 'Demo Paket Lengkap',
                'base_price' => 350000.00,
                'description' => 'Paket demo lengkap mencakup Anamnesis, Pemeriksaan Fisik, Tindakan Medis, Lab (GDS), dan Non-Lab (Foto Thorax).',
                'display_order' => 11,
                'status' => true,
            ],
            [
                'code' => 'DEMO-PKT-EXECUTIVE',
                'name' => 'Demo Paket Executive',
                'base_price' => 750000.00,
                'description' => 'Paket demo premium/executive mencakup Anamnesis lengkap, Pemeriksaan Fisik lengkap, Tindakan Medis, seluruh komponen Lab, dan pemeriksaan Non-Lab (EKG & Thorax).',
                'display_order' => 12,
                'status' => true,
            ],
        ];

        $packageModels = [];
        foreach ($demoPackages as $packageData) {
            $packageModels[$packageData['code']] = McuPackage::updateOrCreate(
                ['code' => $packageData['code']],
                $packageData
            );
        }

        // Fetch active items to associate
        $allAnamneses = McuAnamnesis::where('status', true)->get();
        $allPhysicalExams = McuPhysicalExam::where('status', true)->get();
        $gdsLab = McuLab::where('code', 'LAB-MASTER-001')->first() ?? McuLab::first();
        $thoraxRad = McuRadiology::where('code', 'RAD-002')->first() ?? McuRadiology::where('code', 'RAD-MASTER-001')->first() ?? McuRadiology::first();
        $commonAction = McuMedicalAction::where('code', 'TND-MASTER-001')->first() ?? McuMedicalAction::first();

        // 3. Associate items to "Demo Paket Basic"
        $basicPkg = $packageModels['DEMO-PKT-BASIC'];
        // Link all Anamneses
        foreach ($allAnamneses as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $basicPkg->id,
                'item_type' => McuAnamnesis::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Physical Exams
        foreach ($allPhysicalExams as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $basicPkg->id,
                'item_type' => McuPhysicalExam::class,
                'item_id' => $item->id,
            ]);
        }

        // 4. Associate items to "Demo Paket Lengkap"
        $completePkg = $packageModels['DEMO-PKT-COMPLETE'];
        // Link all Anamneses
        foreach ($allAnamneses as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $completePkg->id,
                'item_type' => McuAnamnesis::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Physical Exams
        foreach ($allPhysicalExams as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $completePkg->id,
                'item_type' => McuPhysicalExam::class,
                'item_id' => $item->id,
            ]);
        }
        // Link GDS Lab if exists
        if ($gdsLab) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $completePkg->id,
                'item_type' => McuLab::class,
                'item_id' => $gdsLab->id,
            ]);
        }
        // Link Thorax Radiology if exists
        if ($thoraxRad) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $completePkg->id,
                'item_type' => McuRadiology::class,
                'item_id' => $thoraxRad->id,
            ]);
        }
        // Link Common Medical Action if exists
        if ($commonAction) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $completePkg->id,
                'item_type' => McuMedicalAction::class,
                'item_id' => $commonAction->id,
            ]);
        }

        // 5. Associate items to "Demo Paket Executive"
        $executivePkg = $packageModels['DEMO-PKT-EXECUTIVE'];
        // Link all Anamneses
        foreach ($allAnamneses as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $executivePkg->id,
                'item_type' => McuAnamnesis::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Physical Exams
        foreach ($allPhysicalExams as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $executivePkg->id,
                'item_type' => McuPhysicalExam::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Labs
        $allLabs = McuLab::where('status', true)->get();
        foreach ($allLabs as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $executivePkg->id,
                'item_type' => McuLab::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Radiologies
        $allRadiologies = McuRadiology::where('status', true)->get();
        foreach ($allRadiologies as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $executivePkg->id,
                'item_type' => McuRadiology::class,
                'item_id' => $item->id,
            ]);
        }
        // Link all Medical Actions
        $allMedicalActions = McuMedicalAction::where('status', true)->get();
        foreach ($allMedicalActions as $item) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $executivePkg->id,
                'item_type' => McuMedicalAction::class,
                'item_id' => $item->id,
            ]);
        }
    }
}

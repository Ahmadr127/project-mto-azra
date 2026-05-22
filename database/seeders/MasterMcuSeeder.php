<?php

namespace Database\Seeders;

use App\Models\McuMedicalAction;
use App\Models\McuLab;
use App\Models\McuRadiology;
use App\Models\McuAnamnesis;
use App\Models\McuPhysicalExam;
use App\Models\McuPackage;
use App\Models\McuPackageItem;
use Illuminate\Database\Seeder;

class MasterMcuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed 1 Master Tindakan
        $medicalAction = McuMedicalAction::updateOrCreate(
            ['code' => 'TND-MASTER-001'],
            [
                'name' => 'Konsultasi & Pemeriksaan Tindakan Umum',
                'category' => 'Tindakan Umum',
                'price' => 50000.00,
                'description' => 'Pemeriksaan tindakan awal dan konsultasi dokter umum',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 2. Seed 1 Master Lab
        $lab = McuLab::updateOrCreate(
            ['code' => 'LAB-MASTER-001'],
            [
                'name' => 'Gula Darah Sewaktu (GDS)',
                'category' => 'Glukosa Darah',
                'normal_value' => '< 140 mg/dL',
                'price' => 25000.00,
                'description' => 'Pemeriksaan kadar glukosa darah sewaktu untuk screening diabetes',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 3. Seed 1 Master Non-Lab (McuRadiology)
        $radiology = McuRadiology::updateOrCreate(
            ['code' => 'RAD-MASTER-001'],
            [
                'name' => 'Foto Thorax AP/PA',
                'category' => 'Radiologi / Non-Lab',
                'price' => 150000.00,
                'description' => 'Rontgen dada bagian depan/belakang untuk melihat kondisi jantung & paru-paru',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 4. Seed 1 Master Anamnesis
        $anamnesis = McuAnamnesis::updateOrCreate(
            ['code' => 'ANA-MASTER-001'],
            [
                'name' => 'Riwayat Tekanan Darah Tinggi (Hipertensi)',
                'category' => 'Riwayat Kesehatan Keluarga',
                'price' => 0.00,
                'description' => 'Pemeriksaan riwayat penyakit keturunan tekanan darah tinggi',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 5. Seed 1 Pemeriksaan Fisik
        $physicalExam = McuPhysicalExam::updateOrCreate(
            ['code' => 'FIS-MASTER-001'],
            [
                'name' => 'Pemeriksaan Tanda Vital (Tekanan Darah)',
                'category' => 'Tanda Vital',
                'price' => 10000.00,
                'description' => 'Pengukuran tekanan darah sistolik dan diastolik pasien',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 6. Seed 1 Master Paket MCU
        $package = McuPackage::updateOrCreate(
            ['code' => 'PKT-MASTER-LENGKAP'],
            [
                'name' => 'Paket Master MCU Lengkap',
                'base_price' => 235000.00,
                'description' => 'Paket pemeriksaan kesehatan terpadu mencakup Tindakan, Lab, Non-Lab, Anamnesis, dan Pemeriksaan Fisik',
                'display_order' => 1,
                'status' => true,
            ]
        );

        // 7. Associate ALL active items in the database to the new Master Package
        // This ensures the Master Package covers all Tindakan, Labs, Non-Labs, Anamnesis, and Physical Exams
        $allMedicalActions = McuMedicalAction::where('status', true)->get();
        $allLabs = McuLab::where('status', true)->get();
        $allRadiologies = McuRadiology::where('status', true)->get();
        $allAnamneses = McuAnamnesis::where('status', true)->get();
        $allPhysicalExams = McuPhysicalExam::where('status', true)->get();

        $itemsToLink = [];

        foreach ($allMedicalActions as $item) {
            $itemsToLink[] = ['item_type' => McuMedicalAction::class, 'item_id' => $item->id];
        }
        foreach ($allLabs as $item) {
            $itemsToLink[] = ['item_type' => McuLab::class, 'item_id' => $item->id];
        }
        foreach ($allRadiologies as $item) {
            $itemsToLink[] = ['item_type' => McuRadiology::class, 'item_id' => $item->id];
        }
        foreach ($allAnamneses as $item) {
            $itemsToLink[] = ['item_type' => McuAnamnesis::class, 'item_id' => $item->id];
        }
        foreach ($allPhysicalExams as $item) {
            $itemsToLink[] = ['item_type' => McuPhysicalExam::class, 'item_id' => $item->id];
        }

        foreach ($itemsToLink as $link) {
            McuPackageItem::firstOrCreate([
                'mcu_package_id' => $package->id,
                'item_type' => $link['item_type'],
                'item_id' => $link['item_id'],
            ]);
        }

        // 8. Retroactively sync examinations for any existing registrations using this package (e.g. Patient Alif)
        $registrations = \App\Models\McuRegistration::where('mcu_package_id', $package->id)->get();
        foreach ($registrations as $reg) {
            // Labs
            foreach ($allLabs as $labMaster) {
                $reg->examLabs()->firstOrCreate(
                    ['mcu_lab_id' => $labMaster->id],
                    [
                        'normal_value' => $labMaster->normal_value,
                        'status' => 'pending'
                    ]
                );
            }
            // Medical Actions
            foreach ($allMedicalActions as $actionMaster) {
                $reg->examMedicalActions()->firstOrCreate(
                    ['mcu_medical_action_id' => $actionMaster->id],
                    ['status' => 'pending']
                );
            }
            // Non-Labs (Radiology)
            foreach ($allRadiologies as $radMaster) {
                $reg->examRadiologies()->firstOrCreate(
                    ['mcu_radiology_id' => $radMaster->id],
                    ['status' => 'pending']
                );
            }
            // Anamneses
            foreach ($allAnamneses as $anaMaster) {
                $reg->examAnamneses()->firstOrCreate(
                    ['mcu_anamnesis_id' => $anaMaster->id],
                    ['status' => 'pending']
                );
            }
            // Physical Exam
            if (!$reg->physicalExamResult) {
                $reg->physicalExamResult()->create(['status' => 'pending']);
            }
        }
    }
}

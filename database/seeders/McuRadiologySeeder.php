<?php

namespace Database\Seeders;

use App\Models\McuRadiology;
use App\Models\McuPackage;
use Illuminate\Database\Seeder;

class McuRadiologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'code' => 'RAD-001',
                'name' => 'EKG',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 1,
                'status' => true,
            ],
            [
                'code' => 'RAD-002',
                'name' => 'Foto Thorax',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 2,
                'status' => true,
            ],
            [
                'code' => 'RAD-003',
                'name' => 'Treadmill Test',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 3,
                'status' => true,
            ],
            [
                'code' => 'RAD-004',
                'name' => 'USG Abdomen',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 4,
                'status' => true,
            ],
            [
                'code' => 'RAD-005',
                'name' => 'USG Mammae',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 5,
                'status' => true,
            ],
            [
                'code' => 'RAD-006',
                'name' => 'Audiometri',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 6,
                'status' => true,
            ],
            [
                'code' => 'RAD-007',
                'name' => 'Spirometri',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 7,
                'status' => true,
            ],
            [
                'code' => 'RAD-008',
                'name' => 'Papsmear / IVA Test',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 8,
                'status' => true,
            ],
            [
                'code' => 'RAD-009',
                'name' => 'Harvard Step Test',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 9,
                'status' => true,
            ],
            [
                'code' => 'RAD-010',
                'name' => 'Lain Lain',
                'category' => 'Non Laboratorium',
                'price' => 0,
                'display_order' => 10,
                'status' => true,
            ],
        ];

        $radIds = [];
        foreach ($items as $item) {
            $created = McuRadiology::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
            $radIds[] = $created->id;
        }

        // Automatically assign all seeded radiology/non-lab exams to all active packages
        $packages = McuPackage::where('status', true)->get();
        foreach ($packages as $package) {
            foreach ($radIds as $radId) {
                $package->items()->firstOrCreate([
                    'item_type' => McuRadiology::class,
                    'item_id' => $radId
                ]);
            }
        }
    }
}

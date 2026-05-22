<?php

namespace Database\Seeders;

use App\Models\McuAnamnesis;
use Illuminate\Database\Seeder;

class McuAnamnesisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // 0. Koreksi Dokter
            [
                'code' => 'ANM-KD-001',
                'name' => 'Koreksi Anamnesa / Keluhan dari Pasien Oleh Dokter',
                'category' => 'Koreksi Dokter',
                'price' => 0,
                'description' => 'Koreksi anamnesa oleh dokter pemeriksa',
                'display_order' => 1,
                'status' => true,
            ],

            // 1. Keluhan Utama / Keluhan Kerja
            [
                'code' => 'ANM-KP-001',
                'name' => 'Keluhan Pasien saat ini Dalam Pekerjaan',
                'category' => 'Keluhan Pasien',
                'price' => 0,
                'description' => 'Keluhan kesehatan saat bekerja',
                'display_order' => 2,
                'status' => true,
            ],
            [
                'code' => 'ANM-KP-002',
                'name' => 'Keluhan Pasien saat ini Di Luar Pekerjaan',
                'category' => 'Keluhan Pasien',
                'price' => 0,
                'description' => 'Keluhan kesehatan di luar pekerjaan',
                'display_order' => 3,
                'status' => true,
            ],

            // 2. Riwayat Penyakit Keluarga (Left Column)
            [
                'code' => 'ANM-RK-001',
                'name' => '1. Riwayat Penyakit Keluarga (Umum)',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Keterangan umum riwayat penyakit keluarga',
                'display_order' => 4,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-002',
                'name' => 'Tekanan Darah Tinggi (Hipertensi) Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat darah tinggi keluarga',
                'display_order' => 5,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-003',
                'name' => 'Kencing Manis (Diabetes Melitus) Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat diabetes keluarga',
                'display_order' => 6,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-004',
                'name' => 'Penyakit Jantung dan Pembuluh Darah Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat penyakit jantung keluarga',
                'display_order' => 7,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-005',
                'name' => 'Kanker Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat kanker keluarga',
                'display_order' => 8,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-006',
                'name' => 'Asthma Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat asma keluarga',
                'display_order' => 9,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-007',
                'name' => 'Penyakit Lainnya Keluarga',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Riwayat penyakit lainnya di keluarga',
                'display_order' => 10,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-008',
                'name' => '- Penyakit Lainnya Ayah',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Penyakit lainnya dari ayah',
                'display_order' => 11,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-009',
                'name' => '- Penyakit Lainnya Ibu',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Penyakit lainnya dari ibu',
                'display_order' => 12,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-010',
                'name' => 'Apakah sudah meninggal dunia (Ayah/Ibu)',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Status kematian orang tua',
                'display_order' => 13,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-011',
                'name' => '- Penyebab ayah meninggal',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Penyebab ayah wafat',
                'display_order' => 14,
                'status' => true,
            ],
            [
                'code' => 'ANM-RK-012',
                'name' => '- Penyebab ibu meninggal',
                'category' => 'Riwayat Penyakit Keluarga',
                'price' => 0,
                'description' => 'Penyebab ibu wafat',
                'display_order' => 15,
                'status' => true,
            ],

            // 3. Riwayat Penyakit Pribadi (Right Column)
            [
                'code' => 'ANM-RP-001',
                'name' => '2. Riwayat Penyakit Pribadi (Umum)',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Keterangan umum riwayat penyakit pribadi',
                'display_order' => 16,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-002',
                'name' => 'Tekanan Darah Tinggi (Hipertensi) Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Darah tinggi pribadi',
                'display_order' => 17,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-003',
                'name' => 'Kencing manis Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Diabetes pribadi',
                'display_order' => 18,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-004',
                'name' => 'Penyakit jantung dan pembuluh darah Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Jantung pribadi',
                'display_order' => 19,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-005',
                'name' => 'Kanker Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Kanker pribadi',
                'display_order' => 20,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-006',
                'name' => 'Asthma Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Asma pribadi',
                'display_order' => 21,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-007',
                'name' => 'Penyakit lainnya Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Penyakit lainnya pribadi',
                'display_order' => 22,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-008',
                'name' => 'Ambeien Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Ambeien pribadi',
                'display_order' => 23,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-009',
                'name' => 'Alergi Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Alergi pribadi',
                'display_order' => 24,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-010',
                'name' => '- Alergi Lainya',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Detail alergi lainnya pribadi',
                'display_order' => 25,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-011',
                'name' => 'Penyakit Paru-paru Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Penyakit paru pribadi',
                'display_order' => 26,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-012',
                'name' => 'Riwayat Haid (khusus wanita) Pribadi',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Siklus haid wanita pribadi',
                'display_order' => 27,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-013',
                'name' => '- Riwayat haid lainya',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Detail riwayat haid lainnya pribadi',
                'display_order' => 28,
                'status' => true,
            ],
            [
                'code' => 'ANM-RP-014',
                'name' => '10. Penyakit lainnya (Tambahan)',
                'category' => 'Riwayat Penyakit Pribadi',
                'price' => 0,
                'description' => 'Detail tambahan penyakit lainnya pribadi',
                'display_order' => 29,
                'status' => true,
            ],

            // 4. Riwayat Rawat Inap RS
            [
                'code' => 'ANM-RI-001',
                'name' => '1. Penyebab dirawat | Tahun Rawat | Lama dirawat',
                'category' => 'Riwayat Rawat Inap',
                'price' => 0,
                'description' => 'Riwayat rawat inap 1',
                'display_order' => 30,
                'status' => true,
            ],
            [
                'code' => 'ANM-RI-002',
                'name' => '2. Penyebab dirawat | Tahun Rawat | Lama dirawat',
                'category' => 'Riwayat Rawat Inap',
                'price' => 0,
                'description' => 'Riwayat rawat inap 2',
                'display_order' => 31,
                'status' => true,
            ],
            [
                'code' => 'ANM-RI-003',
                'name' => '3. Penyebab dirawat | Tahun Rawat | Lama dirawat',
                'category' => 'Riwayat Rawat Inap',
                'price' => 0,
                'description' => 'Riwayat rawat inap 3',
                'display_order' => 32,
                'status' => true,
            ],

            // 5. Kebiasaan
            [
                'code' => 'ANM-KB-001',
                'name' => 'Merokok (Perokok/Bukan/Jumlah Batang)',
                'category' => 'Kebiasaan',
                'price' => 0,
                'description' => 'Kebiasaan merokok',
                'display_order' => 33,
                'status' => true,
            ],
            [
                'code' => 'ANM-KB-002',
                'name' => 'Aktivitas Fisik Mingguan (Olahraga)',
                'category' => 'Kebiasaan',
                'price' => 0,
                'description' => 'Aktivitas fisik harian/olahraga',
                'display_order' => 34,
                'status' => true,
            ],
            [
                'code' => 'ANM-KB-003',
                'name' => 'Makan-Minum (Cukup Sayur, Air Putih, Berserat)',
                'category' => 'Kebiasaan',
                'price' => 0,
                'description' => 'Pola makan minum harian',
                'display_order' => 35,
                'status' => true,
            ],

            // 6. Riwayat Operasi
            [
                'code' => 'ANM-OP-001',
                'name' => 'Operasi | Nama Operasi | Keterangan Operasi',
                'category' => 'Riwayat Operasi',
                'price' => 0,
                'description' => 'Riwayat bedah/operasi',
                'display_order' => 36,
                'status' => true,
            ],

            // 7. Penggunaan Obat Rutin
            [
                'code' => 'ANM-OB-001',
                'name' => 'Penggunaan Obat | Fungsi Obat | Nama Obat | Dosis',
                'category' => 'Penggunaan Obat Rutin',
                'price' => 0,
                'description' => 'Konsumsi obat rutin harian',
                'display_order' => 37,
                'status' => true,
            ],
        ];

        foreach ($items as $item) {
            McuAnamnesis::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Urgency;
use Illuminate\Database\Seeder;

class UrgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menjalankan seeder untuk mengisi data urgensi pengaduan.
     */
    public function run(): void
    {
        $urgencies = [
            [
                'code' => 'MISCONDUCT',
                'name' => 'Pelanggaran Ringan',
                'description' => 'Pelanggaran aturan kerja yang bersifat ringan seperti terlambat, tidak rapi, dll.',
                'default_urgency' => 'LOW',
                'target_role' => 'mitra',
                'sla_time' => '3x24',
                'is_active' => true,
            ],
            [
                'code' => 'ABSENT',
                'name' => 'Mangkir / Tidak Hadir',
                'description' => 'Tidak hadir bekerja tanpa pemberitahuan atau izin yang jelas.',
                'default_urgency' => 'MEDIUM',
                'target_role' => 'mitra',
                'sla_time' => '2x24',
                'is_active' => true,
            ],
            [
                'code' => 'FRAUD',
                'name' => 'Penipuan / Kecurangan',
                'description' => 'Tindakan penipuan, pencurian, atau kecurangan finansial.',
                'default_urgency' => 'HIGH',
                'target_role' => 'mitra',
                'sla_time' => '1x24',
                'is_active' => true,
            ],
            [
                'code' => 'HARASSMENT',
                'name' => 'Pelecehan / Kekerasan',
                'description' => 'Pelecehan verbal, fisik, atau seksual yang dilakukan oleh pihak terkait.',
                'default_urgency' => 'CRITICAL',
                'target_role' => 'mitra',
                'sla_time' => '6jam',
                'is_active' => true,
            ],
            [
                'code' => 'PAYMENT_ISSUE',
                'name' => 'Masalah Pembayaran',
                'description' => 'Keterlambatan atau tidak dibayarnya gaji/upah sesuai perjanjian.',
                'default_urgency' => 'HIGH',
                'target_role' => 'klien',
                'sla_time' => '1x24',
                'is_active' => true,
            ],
            [
                'code' => 'WORK_CONDITION',
                'name' => 'Kondisi Kerja Tidak Layak',
                'description' => 'Lingkungan kerja atau kondisi yang tidak sesuai dengan standar.',
                'default_urgency' => 'MEDIUM',
                'target_role' => 'klien',
                'sla_time' => '2x24',
                'is_active' => true,
            ],
            [
                'code' => 'OTHER',
                'name' => 'Lainnya',
                'description' => 'Pengaduan yang tidak termasuk kategori di atas.',
                'default_urgency' => 'LOW',
                'target_role' => 'admin',
                'sla_time' => '3x24',
                'is_active' => true,
            ],
        ];

        foreach ($urgencies as $urgency) {
            Urgency::updateOrCreate(
                ['code' => $urgency['code']],
                $urgency
            );
        }
    }
}

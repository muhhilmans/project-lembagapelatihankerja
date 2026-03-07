<?php

namespace Database\Seeders;

use App\Models\MasterBPJS;
use Illuminate\Database\Seeder;

class MasterBPJSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Dibayar Penuh Pekerja',
                'description' => 'Biaya BPJS sepenuhnya ditanggung oleh pekerja (potong gaji).',
                'employer_fee_pct' => 0,
                'worker_fee_pct' => 10,
                'bpjs_nominal' => 0,
            ],
            [
                'name' => 'Dibayar Penuh Majikan',
                'description' => 'Biaya BPJS sepenuhnya ditanggung oleh majikan (tunjangan).',
                'employer_fee_pct' => 10,
                'worker_fee_pct' => 0,
                'bpjs_nominal' => 0,
            ],
            [
                'name' => 'Bagi Dua (50:50)',
                'description' => 'Biaya BPJS dibagi dua antara pekerja dan majikan.',
                'employer_fee_pct' => 7.5,
                'worker_fee_pct' => 2.5,
                'bpjs_nominal' => 0,
            ],
            [
                'name' => 'Tidak Ada BPJS',
                'description' => 'Tidak mengikutsertakan program BPJS.',
                'employer_fee_pct' => 0,
                'worker_fee_pct' => 0,
                'bpjs_nominal' => 0,
            ],
        ];

        foreach ($data as $item) {
            MasterBPJS::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Garansi;
use Illuminate\Database\Seeder;

class GaransiSeeder extends Seeder
{
    public function run(): void
    {
        $garansis = [
            [
                'name'             => 'Garansi 1 Bulan',
                'max_replacements' => 1,
                'price'            => 150000,
                'is_active'        => true,
            ],
            [
                'name'             => 'Garansi 3 Bulan',
                'max_replacements' => 2,
                'price'            => 350000,
                'is_active'        => true,
            ],
            [
                'name'             => 'Garansi 6 Bulan',
                'max_replacements' => 3,
                'price'            => 600000,
                'is_active'        => true,
            ],
            [
                'name'             => 'Garansi 12 Bulan',
                'max_replacements' => 5,
                'price'            => 1000000,
                'is_active'        => true,
            ],
        ];

        foreach ($garansis as $item) {
            Garansi::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}

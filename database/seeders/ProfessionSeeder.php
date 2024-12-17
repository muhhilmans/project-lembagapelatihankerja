<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            [
                'name' => 'Pekerja Rumah Tangga',
            ],
            [
                'name' => 'Satpam',
            ],
            [
                'name' => 'Supir',
            ],
            [
                'name' => 'Baby Sitter',
            ],
        ];

        foreach ($datas as $data) {
            Profession::create($data);
        }
    }
}

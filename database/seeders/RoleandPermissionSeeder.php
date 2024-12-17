<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleandPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'owner',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'majikan',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'pembantu',
            'guard_name' => 'web',
        ]);
    }
}

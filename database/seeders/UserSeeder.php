<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Profession;
use App\Models\EmployeDetail;
use App\Models\ServantDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $password = Hash::make('password');

        $datas = [
            ['name' => 'Super Admin', 'email' => 'it@sipembantu.com', 'username' => 'superadmin', 'password' => Hash::make('ItSipembantu2025%'), 'is_active' => true, 'email_verified_at' => now(), 'role' => 'superadmin'],
            // ['name' => 'Admin', 'email' => 'admin@gmail.com', 'username' => 'admin', 'is_active' => true, 'email_verified_at' => now(), 'role' => 'admin'],
            // ['name' => 'Owner', 'email' => 'owner@gmail.com', 'username' => 'owner', 'is_active' => true, 'email_verified_at' => now(), 'role' => 'owner'],
            // ['name' => 'Majikan', 'email' => 'majikan@gmail.com', 'username' => 'majikan', 'is_active' => true, 'email_verified_at' => now(), 'role' => 'majikan'],
            // ['name' => 'Pembantu 1', 'email' => 'pembantusatu@gmail.com', 'username' => 'pembantusatu', 'is_active' => true, 'email_verified_at' => now(), 'role' => 'pembantu'],
            // ['name' => 'Pembantu 2', 'email' => 'pembantudua@gmail.com', 'username' => 'pembantudua', 'email_verified_at' => null, 'is_active' => false, 'role' => 'pembantu'],
        ];

        foreach ($datas as $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                // 'password' => $password,
                'password' => $data['password'],
                'email_verified_at' => $data['email_verified_at'],
                'is_active' => $data['is_active'],
            ]);

            $role = Role::findByName($data['role'], 'web');
            $user->assignRole($role);

            $profession = Profession::inRandomOrder()->first();

            if ($data['role'] == 'pembantu') {
                ServantDetail::create([
                    'user_id' => $user->id,
                    'profession_id' => $profession->id
                ]);
            }

            if ($data['role'] == 'majikan') {
                EmployeDetail::create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Profession;
use App\Models\ServantDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyPembantuSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua akun dummy lama (@test.com)
        User::where('email', 'like', '%@test.com')->each(function ($u) {
            $u->servantDetails()->delete();
            $u->roles()->detach();
            $u->delete();
        });

        $role       = Role::findByName('pembantu', 'web');
        $profession = Profession::inRandomOrder()->first();

        $user = User::create([
            'name'              => 'test pembantu',
            'username'          => 'testPEMBANTU',
            'email'             => 'testpembantu@test.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        $user->assignRole($role);

        ServantDetail::create([
            'user_id'        => $user->id,
            'gender'         => 'female',
            'religion'       => 'Islam',
            'phone'          => '081200000001',
            'address'        => 'Jl. Test No. 1, Jakarta',
            'profession_id'  => $profession?->id ?? null,
            'working_status' => false,
            'is_bank'        => false,
            'is_bpjs'        => false,
        ]);

        $this->command->info('Akun dibuat: testpembantu@test.com | password: password');
    }
}

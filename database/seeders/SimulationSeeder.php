<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use Illuminate\Support\Str;

class SimulationSeeder extends Seeder
{
    public function run()
    {
        $employer = User::whereHas('roles', function($q){
            $q->where('name', 'majikan');
        })->where('name', 'like', '%Majikan Test 1%')->first();

        if (!$employer) {
            $this->command->warn('Employer "Majikan Test 1" not found exactly. Using the first available majikan.');
            $employer = User::whereHas('roles', function($q){
                $q->where('name', 'majikan');
            })->first();
        }

        if (!$employer) {
            $this->command->error('No majikan found at all!');
            return;
        }

        $this->command->info('Using Majikan: ' . $employer->name . ' (ID: ' . $employer->id . ')');

        // Target workers
        $usernames = ['pembantu_test1', 'pembantu_test2', 'pembantu_test3'];
        $workers = [];

        foreach ($usernames as $un) {
            $worker = User::where('username', $un)->first();
            if (!$worker) {
                $this->command->info("Worker $un not found. Creating...");
                $worker = User::create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => ucwords(str_replace('_', ' ', $un)),
                    'username' => $un,
                    'email' => $un . '@test.com',
                    'password' => bcrypt('password'),
                ]);
                $worker->assignRole('pembantu');
            }
            $workers[$un] = $worker;
        }

        // Create Vacancy
        $vacancy = \App\Models\Vacancy::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $employer->id,
            'title' => 'Lowongan Pekerja Rumah Tangga',
            'profession_id' => null, // Making it null to avoid foreign key issues
            'description' => 'Lowongan khusus untuk simulasi sistem.',
            'requirements' => 'Rajin, jujur, dan bertanggung jawab.',
            'benefits' => 'Gaji kompetitif, tempat tinggal.',
            'closing_date' => now()->addDays(30),
            'limit' => 5,
            'status' => 1, // tinyint(1)
        ]);
        
        $this->command->info('Vacancy created successfully: ' . $vacancy->id);

        $startDate = now();

        // Application 1: Kontrak (pembantu_test1)
        \App\Models\Application::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'servant_id' => $workers['pembantu_test1']->id,
            'vacancy_id' => $vacancy->id,
            'employe_id' => $employer->id,
            'status' => 'accepted', 
            'salary_type' => 'contract',
            'salary' => 3000000,
            'admin_fee' => 1000000,
            'is_infal' => false,
            'work_start_date' => $startDate,
            'work_end_date' => $startDate->copy()->addYears(1),
        ]);
        $this->command->info('Application for worker 1 (Kontrak) created.');

        // Application 2: Fee/Infal - Harian (pembantu_test2) -> daily
        \App\Models\Application::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'servant_id' => $workers['pembantu_test2']->id,
            'vacancy_id' => $vacancy->id,
            'employe_id' => $employer->id,
            'status' => 'accepted',
            'salary_type' => 'fee',
            'is_infal' => true,
            'infal_frequency' => 'daily',
            'salary' => 150000, // per hari
            'admin_fee' => 500000,
            'work_start_date' => $startDate,
            'work_end_date' => $startDate->copy()->addDays(14),
        ]);
        $this->command->info('Application for worker 2 (Infal Harian) created.');

        // Application 3: Fee/Infal - Mingguan (pembantu_test3) -> weekly
        \App\Models\Application::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'servant_id' => $workers['pembantu_test3']->id,
            'vacancy_id' => $vacancy->id,
            'employe_id' => $employer->id,
            'status' => 'accepted',
            'salary_type' => 'fee',
            'is_infal' => true,
            'infal_frequency' => 'weekly',
            'salary' => 1000000, // per minggu
            'admin_fee' => 800000,
            'work_start_date' => $startDate,
            'work_end_date' => $startDate->copy()->addMonths(2),
        ]);
        $this->command->info('Application for worker 3 (Infal Mingguan) created.');

        $this->command->info('Simulation successfully completed.');
    }
}

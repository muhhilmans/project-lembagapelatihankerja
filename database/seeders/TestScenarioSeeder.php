<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\ServantDetail;
use App\Models\EmployeDetail;
use App\Models\Review;
use App\Models\Role;
use App\Models\Profession;
use App\Models\Scheme;
use App\Models\Garansi;
use App\Models\Pengaduan;
use App\Models\Urgency;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== SIPEMBANTU Test Scenario Seeder ===');
        $this->command->newLine();

        // ─────────────────────────────────────────────────
        // 1) CREATE / FIND ACCOUNTS
        // ─────────────────────────────────────────────────
        $this->command->info('▶ [1/6] Membuat akun test...');

        $accounts = [
            ['name' => 'Majikan Satu',      'username' => 'majikan1',       'email' => 'majikan1@test.com',       'role' => 'majikan'],
            ['name' => 'Majikan Test 1',     'username' => 'majikan_test1',  'email' => 'majikan_test1@test.com',  'role' => 'majikan'],
            ['name' => 'Pembantu Test 1',    'username' => 'pembantu_test1', 'email' => 'pembantu_test1@test.com', 'role' => 'pembantu'],
            ['name' => 'Pembantu Test 2',    'username' => 'pembantu_test2', 'email' => 'pembantu_test2@test.com', 'role' => 'pembantu'],
            ['name' => 'Pembantu Test 3',    'username' => 'pembantu_test3', 'email' => 'pembantu_test3@test.com', 'role' => 'pembantu'],
            ['name' => 'Test Mitra',         'username' => 'testMitra',      'email' => 'testmitra@test.com',      'role' => 'pembantu'],
        ];

        $users = [];
        $profession = Profession::first();

        foreach ($accounts as $acc) {
            $user = User::where('username', $acc['username'])->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $acc['name'],
                    'username'          => $acc['username'],
                    'email'             => $acc['email'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
                $role = Role::findByName($acc['role'], 'web');
                $user->assignRole($role);
                $this->command->info("  ✅ Dibuat: {$acc['username']} ({$acc['role']})");
            } else {
                $this->command->info("  ⏭️ Sudah ada: {$acc['username']}");
            }

            // Create detail records if missing
            if ($acc['role'] === 'pembantu' && !$user->servantDetails) {
                ServantDetail::create([
                    'user_id'        => $user->id,
                    'gender'         => 'female',
                    'religion'       => 'Islam',
                    'phone'          => '0812' . rand(10000000, 99999999),
                    'address'        => 'Jl. Test No. ' . rand(1, 100) . ', Jakarta',
                    'profession_id'  => $profession?->id,
                    'working_status' => false,
                    'is_bank'        => false,
                    'is_bpjs'        => false,
                ]);
            }

            if ($acc['role'] === 'majikan' && !$user->employeDetails) {
                EmployeDetail::create([
                    'user_id' => $user->id,
                    'phone'   => '0812' . rand(10000000, 99999999),
                    'address' => 'Jl. Majikan No. ' . rand(1, 50) . ', Jakarta Selatan',
                ]);
            }

            $users[$acc['username']] = $user;
        }

        // Shortcuts
        $majikan1      = $users['majikan1'];
        $majikanTest1  = $users['majikan_test1'];
        $pembantu1     = $users['pembantu_test1'];
        $pembantu2     = $users['pembantu_test2'];
        $pembantu3     = $users['pembantu_test3'];
        $testMitra     = $users['testMitra'];

        // ─────────────────────────────────────────────────
        // 2) FETCH SCHEME & GARANSI
        // ─────────────────────────────────────────────────
        $scheme  = Scheme::where('is_active', true)->first();
        $garansi = Garansi::where('is_active', true)->orderBy('price', 'desc')->first(); // 3 bulan garansi

        if (!$scheme) {
            $this->command->warn('  ⚠️ Tidak ada Scheme aktif, skema fee akan null.');
        }
        if (!$garansi) {
            $this->command->warn('  ⚠️ Tidak ada Garansi aktif, garansi kontrak akan null.');
        }

        // ─────────────────────────────────────────────────
        // 3) CREATE VACANCIES
        // ─────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('▶ [2/6] Membuat lowongan...');

        $now = Carbon::now();

        // Clean up existing test vacancies to avoid duplicates
        $existingVacancies = Vacancy::where('title', 'like', '%[TEST]%')->pluck('id');
        if ($existingVacancies->count() > 0) {
            // Delete related data first
            Review::whereIn('application_id', Application::whereIn('vacancy_id', $existingVacancies)->pluck('id'))->delete();
            Pengaduan::whereIn('contract_id', Application::whereIn('vacancy_id', $existingVacancies)->pluck('id'))->delete();
            Application::whereIn('vacancy_id', $existingVacancies)->delete();
            Vacancy::whereIn('id', $existingVacancies)->forceDelete();
            $this->command->info('  🗑️ Data test lama dibersihkan.');
        }

        // Also clean hire-type applications (no vacancy_id) that reference test employers
        $testEmployerIds = [$majikan1->id, $majikanTest1->id];
        $testServantIds  = [$pembantu1->id, $pembantu2->id, $pembantu3->id, $testMitra->id];

        $hireApps = Application::whereNull('vacancy_id')
            ->whereIn('employe_id', $testEmployerIds)
            ->whereIn('servant_id', $testServantIds)
            ->pluck('id');

        if ($hireApps->count() > 0) {
            Review::whereIn('application_id', $hireApps)->delete();
            Pengaduan::whereIn('contract_id', $hireApps)->delete();
            Application::whereIn('id', $hireApps)->delete();
            $this->command->info('  🗑️ Data hire test lama dibersihkan.');
        }

        // Vacancy A: majikan1 - Kontrak PRT
        $vacancyA = Vacancy::create([
            'user_id'      => $majikan1->id,
            'title'        => '[TEST] Asisten Rumah Tangga - Kontrak',
            'description'  => 'Dicari ART berpengalaman untuk kontrak kerja 1 tahun.',
            'requirements' => 'Rajin, jujur, berpengalaman minimal 1 tahun.',
            'benefits'     => 'Gaji bulanan, tempat tinggal, makan.',
            'closing_date' => $now->copy()->addDays(60),
            'limit'        => 3,
            'status'       => 1,
        ]);
        $this->command->info("  ✅ Vacancy A: {$vacancyA->title}");

        // Vacancy B: majikan1 - Fee/Infal
        $vacancyB = Vacancy::create([
            'user_id'      => $majikan1->id,
            'title'        => '[TEST] Pekerja Harian & Per Jam',
            'description'  => 'Butuh pekerja untuk tugas harian dan per jam.',
            'requirements' => 'Fleksibel, bisa bekerja shift.',
            'benefits'     => 'Bayaran harian/per jam kompetitif.',
            'closing_date' => $now->copy()->addDays(60),
            'limit'        => 5,
            'status'       => 1,
        ]);
        $this->command->info("  ✅ Vacancy B: {$vacancyB->title}");

        // Vacancy C: majikan_test1 - Kontrak Baby Sitter
        $vacancyC = Vacancy::create([
            'user_id'      => $majikanTest1->id,
            'title'        => '[TEST] Baby Sitter - Kontrak',
            'description'  => 'Dicari baby sitter untuk anak usia 2 tahun.',
            'requirements' => 'Sabar, pengalaman mengurus bayi, sertifikat pelatihan.',
            'benefits'     => 'Gaji tinggi, bonus bulanan.',
            'closing_date' => $now->copy()->addDays(60),
            'limit'        => 2,
            'status'       => 1,
        ]);
        $this->command->info("  ✅ Vacancy C: {$vacancyC->title}");

        // Vacancy D: majikan_test1 - Fee Mingguan/Bulanan
        $vacancyD = Vacancy::create([
            'user_id'      => $majikanTest1->id,
            'title'        => '[TEST] Pekerja Infal Mingguan & Bulanan',
            'description'  => 'Butuh pekerja infal untuk kerja mingguan dan bulanan.',
            'requirements' => 'Pengalaman kebersihan, bisa bekerja mandiri.',
            'benefits'     => 'Bayaran per minggu/bulan.',
            'closing_date' => $now->copy()->addDays(60),
            'limit'        => 5,
            'status'       => 1,
        ]);
        $this->command->info("  ✅ Vacancy D: {$vacancyD->title}");

        // ─────────────────────────────────────────────────
        // 4) CREATE APPLICATIONS
        // ─────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('▶ [3/6] Membuat aplikasi/kontrak...');

        $apps = [];

        // ── App 1: pembantu_test1 → Vacancy A (majikan1) → Kontrak Aktif ──
        $apps[1] = Application::create([
            'servant_id'      => $pembantu1->id,
            'vacancy_id'      => $vacancyA->id,
            'employe_id'      => $majikan1->id,
            'status'          => 'accepted',
            'salary_type'     => 'contract',
            'salary'          => 3500000,
            'admin_fee'       => 1200000,
            'is_infal'        => false,
            'work_start_date' => $now->copy()->subMonths(2),
            'work_end_date'   => $now->copy()->addMonths(10),
            'garansi_id'      => $garansi?->id,
            'garansi_price'   => $garansi?->price,
            'warranty_duration' => $garansi?->name,
            'scheme_id'       => $scheme?->id,
        ]);
        // Set working_status true (kontrak aktif)
        ServantDetail::where('user_id', $pembantu1->id)->update(['working_status' => true]);
        $this->command->info("  ✅ App 1: pembantu_test1 → Kontrak Aktif (majikan1) Rp 3.5jt/bln");

        // ── App 2: pembantu_test2 → Vacancy A (majikan1) → Kontrak Selesai ──
        $apps[2] = Application::create([
            'servant_id'      => $pembantu2->id,
            'vacancy_id'      => $vacancyA->id,
            'employe_id'      => $majikan1->id,
            'status'          => 'laidoff',
            'salary_type'     => 'contract',
            'salary'          => 3000000,
            'admin_fee'       => 1000000,
            'is_infal'        => false,
            'work_start_date' => $now->copy()->subMonths(14),
            'work_end_date'   => $now->copy()->subMonths(2),
            'end_reason'      => 'selesai_kontrak',
            'garansi_id'      => $garansi?->id,
            'garansi_price'   => $garansi?->price,
            'warranty_duration' => $garansi?->name,
            'scheme_id'       => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 2: pembantu_test2 → Kontrak Selesai (majikan1) - selesai_kontrak");

        // ── App 3: pembantu_test3 → Vacancy B (majikan1) → Fee Infal Harian Aktif ──
        $apps[3] = Application::create([
            'servant_id'       => $pembantu3->id,
            'vacancy_id'       => $vacancyB->id,
            'employe_id'       => $majikan1->id,
            'status'           => 'accepted',
            'salary_type'      => 'fee',
            'salary'           => 150000,
            'is_infal'         => true,
            'infal_frequency'  => 'daily',
            'work_start_date'  => $now->copy()->subDays(14),
            'work_end_date'    => $now->copy()->addDays(16),
            'scheme_id'        => $scheme?->id,
        ]);
        // Fee/infal workers: working_status stays false (can multi-employer)
        ServantDetail::where('user_id', $pembantu3->id)->update(['working_status' => false]);
        $this->command->info("  ✅ App 3: pembantu_test3 → Fee Infal Harian (majikan1) Rp 150rb/hari");

        // ── App 4: testMitra → Vacancy B (majikan1) → Fee Infal Per Jam Aktif ──
        $apps[4] = Application::create([
            'servant_id'       => $testMitra->id,
            'vacancy_id'       => $vacancyB->id,
            'employe_id'       => $majikan1->id,
            'status'           => 'accepted',
            'salary_type'      => 'fee',
            'salary'           => 35000,
            'is_infal'         => true,
            'infal_frequency'  => 'hourly',
            'infal_time_in'    => '08:00',
            'infal_time_out'   => '12:00',
            'infal_hourly_rate' => 35000,
            'work_start_date'  => $now->copy()->subDays(7),
            'scheme_id'        => $scheme?->id,
        ]);
        ServantDetail::where('user_id', $testMitra->id)->update(['working_status' => false]);
        $this->command->info("  ✅ App 4: testMitra → Fee Infal Per Jam (majikan1) Rp 35rb/jam 08:00-12:00");

        // ── App 5: pembantu_test3 → Vacancy D (majikan_test1) → Fee Infal Mingguan (MULTI-EMPLOYER!) ──
        $apps[5] = Application::create([
            'servant_id'       => $pembantu3->id,
            'vacancy_id'       => $vacancyD->id,
            'employe_id'       => $majikanTest1->id,
            'status'           => 'accepted',
            'salary_type'      => 'fee',
            'salary'           => 800000,
            'is_infal'         => true,
            'infal_frequency'  => 'weekly',
            'work_start_date'  => $now->copy()->subWeeks(3),
            'work_end_date'    => $now->copy()->addWeeks(5),
            'scheme_id'        => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 5: pembantu_test3 → Fee Infal Mingguan (majikan_test1) Rp 800rb/minggu [MULTI-EMPLOYER]");

        // ── App 6: testMitra → Vacancy D (majikan_test1) → Fee Infal Bulanan (MULTI-EMPLOYER!) ──
        $apps[6] = Application::create([
            'servant_id'       => $testMitra->id,
            'vacancy_id'       => $vacancyD->id,
            'employe_id'       => $majikanTest1->id,
            'status'           => 'accepted',
            'salary_type'      => 'fee',
            'salary'           => 2000000,
            'is_infal'         => true,
            'infal_frequency'  => 'monthly',
            'work_start_date'  => $now->copy()->subMonths(1),
            'work_end_date'    => $now->copy()->addMonths(5),
            'scheme_id'        => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 6: testMitra → Fee Infal Bulanan (majikan_test1) Rp 2jt/bulan [MULTI-EMPLOYER]");

        // ── App 7: pembantu_test2 → Vacancy C (majikan_test1) → Kontrak Diganti ──
        $apps[7] = Application::create([
            'servant_id'      => $pembantu2->id,
            'vacancy_id'      => $vacancyC->id,
            'employe_id'      => $majikanTest1->id,
            'status'          => 'laidoff',
            'salary_type'     => 'contract',
            'salary'          => 4000000,
            'admin_fee'       => 1500000,
            'is_infal'        => false,
            'work_start_date' => $now->copy()->subMonths(6),
            'work_end_date'   => $now->copy()->subMonths(1),
            'end_reason'      => 'diganti',
            'garansi_id'      => $garansi?->id,
            'garansi_price'   => $garansi?->price,
            'warranty_duration' => $garansi?->name,
            'scheme_id'       => $scheme?->id,
        ]);
        // pembantu_test2 is no longer actively contracted
        ServantDetail::where('user_id', $pembantu2->id)->update(['working_status' => false]);
        $this->command->info("  ✅ App 7: pembantu_test2 → Kontrak Diganti (majikan_test1) - diganti");

        // ── App 8: pembantu_test1 → Vacancy C (majikan_test1) → Kontrak Mengundurkan Diri ──
        // This is a PAST contract (before the current active contract at vacancy A)
        $apps[8] = Application::create([
            'servant_id'      => $pembantu1->id,
            'vacancy_id'      => $vacancyC->id,
            'employe_id'      => $majikanTest1->id,
            'status'          => 'laidoff',
            'salary_type'     => 'contract',
            'salary'          => 3800000,
            'admin_fee'       => 1300000,
            'is_infal'        => false,
            'work_start_date' => $now->copy()->subMonths(10),
            'work_end_date'   => $now->copy()->subMonths(3),
            'end_reason'      => 'mengundurkan_diri',
            'garansi_id'      => $garansi?->id,
            'garansi_price'   => $garansi?->price,
            'warranty_duration' => $garansi?->name,
            'scheme_id'       => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 8: pembantu_test1 → Kontrak Mengundurkan Diri (majikan_test1) - mengundurkan_diri");

        // ── App 9: testMitra → Vacancy A (majikan1) → Fee Reguler Diberhentikan ──
        $apps[9] = Application::create([
            'servant_id'      => $testMitra->id,
            'vacancy_id'      => $vacancyA->id,
            'employe_id'      => $majikan1->id,
            'status'          => 'laidoff',
            'salary_type'     => 'fee',
            'salary'          => 1800000,
            'is_infal'        => false,
            'work_start_date' => $now->copy()->subMonths(4),
            'work_end_date'   => $now->copy()->subMonths(1),
            'end_reason'      => 'diberhentikan',
            'scheme_id'       => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 9: testMitra → Fee Reguler Diberhentikan (majikan1) - diberhentikan");

        // ── App 10: pembantu_test2 → Vacancy D (majikan_test1) → Infal Harian Mengundurkan Diri ──
        $apps[10] = Application::create([
            'servant_id'       => $pembantu2->id,
            'vacancy_id'       => $vacancyD->id,
            'employe_id'       => $majikanTest1->id,
            'status'           => 'laidoff',
            'salary_type'      => 'fee',
            'salary'           => 120000,
            'is_infal'         => true,
            'infal_frequency'  => 'daily',
            'work_start_date'  => $now->copy()->subDays(30),
            'work_end_date'    => $now->copy()->subDays(5),
            'end_reason'       => 'mengundurkan_diri',
            'scheme_id'        => $scheme?->id,
        ]);
        $this->command->info("  ✅ App 10: pembantu_test2 → Infal Harian Mengundurkan Diri (majikan_test1) - mengundurkan_diri");

        // ─────────────────────────────────────────────────
        // 5) CREATE REVIEWS & RATINGS
        // ─────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('▶ [4/6] Membuat review dan rating...');

        $reviews = [
            // App 2: selesai_kontrak (pembantu_test2 di majikan1)
            [
                'application_id' => $apps[2]->id,
                'reviewer_id'    => $majikan1->id,
                'reviewee_id'    => $pembantu2->id,
                'rating'         => 5,
                'comment'        => 'Sangat rajin dan disiplin. Pekerja terbaik yang pernah kami miliki! Selalu tepat waktu dan inisiatif tinggi.',
            ],
            [
                'application_id' => $apps[2]->id,
                'reviewer_id'    => $pembantu2->id,
                'reviewee_id'    => $majikan1->id,
                'rating'         => 4,
                'comment'        => 'Majikan baik dan selalu tepat waktu membayar gaji. Lingkungan kerja nyaman.',
            ],
            // App 7: diganti (pembantu_test2 di majikan_test1)
            [
                'application_id' => $apps[7]->id,
                'reviewer_id'    => $majikanTest1->id,
                'reviewee_id'    => $pembantu2->id,
                'rating'         => 3,
                'comment'        => 'Cukup baik tapi kurang inisiatif. Perlu diingatkan terus untuk tugas-tugas rutin.',
            ],
            // App 8: mengundurkan_diri (pembantu_test1 di majikan_test1)
            [
                'application_id' => $apps[8]->id,
                'reviewer_id'    => $majikanTest1->id,
                'reviewee_id'    => $pembantu1->id,
                'rating'         => 4,
                'comment'        => 'Pekerja bagus, sayang harus resign karena alasan keluarga. Sangat recommended.',
            ],
            [
                'application_id' => $apps[8]->id,
                'reviewer_id'    => $pembantu1->id,
                'reviewee_id'    => $majikanTest1->id,
                'rating'         => 5,
                'comment'        => 'Majikan sangat profesional dan mengerti kebutuhan pekerja. Terima kasih.',
            ],
            // App 9: diberhentikan (testMitra di majikan1)
            [
                'application_id' => $apps[9]->id,
                'reviewer_id'    => $majikan1->id,
                'reviewee_id'    => $testMitra->id,
                'rating'         => 2,
                'comment'        => 'Sering terlambat dan kurang disiplin. Tidak menyelesaikan tugas tepat waktu.',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
        $this->command->info("  ✅ {$this->getReviewCount(count($reviews))} review dibuat.");

        // ─────────────────────────────────────────────────
        // 6) CREATE PENGADUAN (COMPLAINTS)
        // ─────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('▶ [5/6] Membuat data pengaduan...');

        // Get urgency types
        $urgencies = Urgency::where('is_active', true)->get();
        $urgencyRingan = $urgencies->firstWhere('code', 'MINOR') ?? $urgencies->first();
        $urgencyLainnya = $urgencies->firstWhere('code', 'OTHER') ?? $urgencies->last();

        // Clean old test pengaduan
        Pengaduan::whereIn('reporter_id', array_merge($testEmployerIds, $testServantIds))
            ->whereIn('reported_user_id', array_merge($testEmployerIds, $testServantIds))
            ->delete();

        $complaints = [
            // 1. majikan1 mengadu pembantu_test3 (fee harian aktif) - OPEN
            [
                'contract_id'       => $apps[3]->id,
                'complaint_type_id' => $urgencyRingan?->id,
                'urgency_level'     => $urgencyRingan?->default_urgency ?? 'MEDIUM',
                'reporter_id'       => $majikan1->id,
                'reported_user_id'  => $pembantu3->id,
                'description'       => 'Pekerja sering datang terlambat 30 menit dari jadwal kerja yang disepakati. Sudah ditegur 2x tapi belum ada perubahan.',
                'status'            => 'open',
            ],
            // 2. testMitra mengadu majikan_test1 (fee bulanan) - OPEN
            [
                'contract_id'       => $apps[6]->id,
                'complaint_type_id' => $urgencyLainnya?->id,
                'urgency_level'     => $urgencyLainnya?->default_urgency ?? 'LOW',
                'reporter_id'       => $testMitra->id,
                'reported_user_id'  => $majikanTest1->id,
                'description'       => 'Majikan belum membayar gaji bulan lalu sesuai jadwal. Sudah melewati tanggal pembayaran 1 minggu.',
                'status'            => 'open',
            ],
            // 3. majikan_test1 mengadu pembantu_test2 (kontrak diganti) - INVESTIGATING
            [
                'contract_id'       => $apps[7]->id,
                'complaint_type_id' => $urgencyRingan?->id,
                'urgency_level'     => 'HIGH',
                'reporter_id'       => $majikanTest1->id,
                'reported_user_id'  => $pembantu2->id,
                'description'       => 'Pekerja merusak peralatan rumah tangga bernilai tinggi. Permintaan ganti rugi sudah disampaikan tapi tidak ditanggapi.',
                'status'            => 'investigating',
            ],
            // 4. pembantu_test1 mengadu majikan1 (kontrak aktif) - RESOLVED
            [
                'contract_id'       => $apps[1]->id,
                'complaint_type_id' => $urgencyLainnya?->id,
                'urgency_level'     => 'LOW',
                'reporter_id'       => $pembantu1->id,
                'reported_user_id'  => $majikan1->id,
                'description'       => 'Terjadi kesalahpahaman mengenai jam kerja lembur. Sudah diselesaikan secara kekeluargaan.',
                'status'            => 'resolved',
                'resolved_at'       => now()->subDays(3),
            ],
        ];

        foreach ($complaints as $complaint) {
            Pengaduan::create($complaint);
        }
        $this->command->info('  ✅ ' . count($complaints) . ' pengaduan test dibuat.');

        // ─────────────────────────────────────────────────
        // 7) SUMMARY
        // ─────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('▶ [6/6] Ringkasan Data Test:');
        $this->command->newLine();

        $this->command->table(
            ['Username', 'Role', 'Password', 'Status Kerja'],
            [
                ['majikan1',       'Majikan',  'password', '-'],
                ['majikan_test1',  'Majikan',  'password', '-'],
                ['pembantu_test1', 'Pembantu', 'password', 'Kontrak Aktif (majikan1)'],
                ['pembantu_test2', 'Pembantu', 'password', 'Bebas (semua laidoff)'],
                ['pembantu_test3', 'Pembantu', 'password', 'Multi: majikan1+majikan_test1'],
                ['testMitra',      'Pembantu', 'password', 'Multi: majikan1+majikan_test1'],
            ]
        );

        $this->command->newLine();
        $this->command->table(
            ['#', 'Worker', 'Majikan', 'Tipe', 'Frekuensi', 'Gaji', 'Status', 'End Reason'],
            [
                ['1',  'pembantu_test1', 'majikan1',      'Kontrak', '-',       'Rp 3.500.000', 'accepted', '-'],
                ['2',  'pembantu_test2', 'majikan1',      'Kontrak', '-',       'Rp 3.000.000', 'laidoff',  'selesai_kontrak'],
                ['3',  'pembantu_test3', 'majikan1',      'Fee',     'Harian',  'Rp 150.000',   'accepted', '-'],
                ['4',  'testMitra',      'majikan1',      'Fee',     'Per Jam', 'Rp 35.000',    'accepted', '-'],
                ['5',  'pembantu_test3', 'majikan_test1', 'Fee',     'Mingguan','Rp 800.000',   'accepted', '-'],
                ['6',  'testMitra',      'majikan_test1', 'Fee',     'Bulanan', 'Rp 2.000.000', 'accepted', '-'],
                ['7',  'pembantu_test2', 'majikan_test1', 'Kontrak', '-',       'Rp 4.000.000', 'laidoff',  'diganti'],
                ['8',  'pembantu_test1', 'majikan_test1', 'Kontrak', '-',       'Rp 3.800.000', 'laidoff',  'mengundurkan_diri'],
                ['9',  'testMitra',      'majikan1',      'Fee',     '-',       'Rp 1.800.000', 'laidoff',  'diberhentikan'],
                ['10', 'pembantu_test2', 'majikan_test1', 'Fee',     'Harian',  'Rp 120.000',   'laidoff',  'mengundurkan_diri'],
            ]
        );

        $this->command->newLine();
        $this->command->info('✅ Seeder selesai! Semua data test berhasil dibuat.');
        $this->command->info('📌 Login dengan password: password');
    }

    private function getReviewCount(int $count): string
    {
        return $count . ' buah';
    }
}

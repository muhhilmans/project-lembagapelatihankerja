<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'testMitra')->firstOrFail();

        $notifications = [
            [
                'title' => 'Lamaran Diterima',
                'body'  => 'Selamat! Lamaran Anda ke Majikan 1 telah diterima dan menunggu jadwal interview.',
                'type'  => 'application_accepted',
                'data'  => ['application_id' => 'sample-uuid-001'],
                'read_at' => null,
            ],
            [
                'title' => 'Jadwal Interview',
                'body'  => 'Jadwal interview Anda telah ditetapkan pada Senin, 2 Juni 2026 pukul 09:00.',
                'type'  => 'interview_scheduled',
                'data'  => ['application_id' => 'sample-uuid-001', 'interview_date' => '2026-06-02'],
                'read_at' => null,
            ],
            [
                'title' => 'Tawaran Pekerjaan',
                'body'  => 'Majikan 1 telah mengirimkan tawaran pekerjaan. Silakan tinjau dan berikan keputusan Anda.',
                'type'  => 'job_offer',
                'data'  => ['application_id' => 'sample-uuid-001'],
                'read_at' => null,
            ],
            [
                'title' => 'Pengaduan Diperbarui',
                'body'  => 'Status pengaduan Anda telah diperbarui menjadi "investigating" oleh admin.',
                'type'  => 'complaint_updated',
                'data'  => ['complaint_id' => 'sample-uuid-002', 'status' => 'investigating'],
                'read_at' => now(), // sudah dibaca
            ],
            [
                'title' => 'Kontrak Selesai',
                'body'  => 'Kontrak kerja Anda dengan Majikan 1 telah berakhir. Silakan berikan ulasan.',
                'type'  => 'contract_ended',
                'data'  => ['application_id' => 'sample-uuid-001'],
                'read_at' => now(), // sudah dibaca
            ],
        ];

        foreach ($notifications as $notif) {
            $user->notifications()->create([
                'id'              => Str::uuid(),
                'type'            => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $user->id,
                'data'            => json_encode([
                    'title' => $notif['title'],
                    'body'  => $notif['body'],
                    'type'  => $notif['type'],
                    'data'  => $notif['data'],
                ]),
                'read_at'         => $notif['read_at'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        $this->command->info("✅ 5 notifikasi berhasil dibuat untuk user: {$user->name} ({$user->username})");
        $this->command->info("   - 3 belum dibaca (unread)");
        $this->command->info("   - 2 sudah dibaca (read)");
    }
}

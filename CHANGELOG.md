# Changelog

Semua perubahan penting pada project ini didokumentasikan di file ini.
Format mengacu pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased] — 2026-05-11 / 2026-05-12

### Fitur Baru: Sistem Notifikasi Real-time (Pusher)

#### Ditambahkan
- **`app/Notifications/GeneralNotification.php`** — Notification class baru yang mendukung dua channel pengiriman sekaligus:
  - `database` → disimpan ke tabel `notifications` (dapat diambil via API)
  - `broadcast` → dikirim real-time ke client via Pusher (channel `notifications.{user_id}`)
- **`app/Http/Controllers/Api/NotificationController.php`** — Controller API baru untuk manajemen notifikasi user:
  - `GET /api/notifications` — daftar notifikasi user (paginate 10), beserta `unread_count`
  - `POST /api/notifications/{id}/read` — tandai satu notifikasi sudah dibaca
  - `POST /api/notifications/read-all` — tandai semua notifikasi sudah dibaca
- **`database/migrations/2026_05_08_173919_create_notifications_table.php`** — Tabel `notifications` dengan skema:
  - `id` UUID (primary key)
  - `type` string — nama class notification
  - `notifiable_id` UUID & `notifiable_type` string (uuidMorphs, sesuai User UUID)
  - `data` text — payload JSON notifikasi
  - `read_at` timestamp nullable

#### Diubah
- **`.env`** — `BROADCAST_DRIVER` diubah dari `log` ke `pusher`; kredensial Pusher diisi (`APP_ID`, `APP_KEY`, `APP_SECRET`, `APP_CLUSTER=ap1`)
- **`config/broadcasting.php`** — Ditambahkan `'verify' => env('APP_ENV') !== 'local'` pada `client_options` Pusher untuk mengatasi error SSL certificate di environment lokal Windows
- **`routes/api.php`** — Ditambahkan 3 route notifikasi di grup `jwt.auth` global

#### Diperbaiki
- **`database/migrations/..._create_notifications_table.php`** — Diganti `morphs()` → `uuidMorphs()` karena User model menggunakan UUID sebagai primary key. `morphs()` membuat `notifiable_id` sebagai `unsignedBigInteger` yang tidak kompatibel dengan UUID string
- **`app/Http/Controllers/Api/NotificationController.php`** (`markAllAsRead`) — Diganti `->unreadNotifications->markAsRead()` (load semua ke memory) menjadi `->unreadNotifications()->update(['read_at' => now()])` (satu query langsung ke DB)
- **`app/Http/Controllers/Api/NotificationController.php`** — Response semua method kini konsisten menggunakan field `success: true/false`

---

### Fitur Baru: Sistem Pengaduan (Complaint) API — Akses Mobile

#### Ditambahkan
- **`routes/api.php`** — Endpoint pengaduan baru di grup `jwt.auth` global (dapat diakses semua role):
  - `GET /api/complaint-types` — daftar tipe pengaduan aktif dari tabel `urgencies`
  - `GET /api/complaints` — daftar pengaduan (role-aware: admin lihat semua, user hanya miliknya)
  - `POST /api/complaints` — buat pengaduan baru
  - `GET /api/complaints/{id}` — detail pengaduan (role-aware)
- **`routes/api.php`** — Endpoint pengaduan khusus admin di grup `role_api:admin,superadmin`:
  - `PUT /api/complaints/{id}/status` — ubah status pengaduan (open / investigating / resolved)
  - `POST /api/complaints/{id}/resolve` — selesaikan pengaduan (shortcut ke status=resolved, backward compatible)

#### Diubah
- **`app/Http/Controllers/Api/ComplaintController.php`** — Penulisan ulang menyeluruh:

  | Method | Perubahan |
  |--------|-----------|
  | `complaintTypes()` | **Baru** — ambil daftar tipe pengaduan aktif dari `Urgency` |
  | `index()` | Ditambahkan role-awareness: admin lihat semua + filter search/status; user biasa hanya lihat pengaduan sebagai reporter atau reported. Eager load lebih efisien dengan select kolom spesifik |
  | `store()` | **Baru** — buat pengaduan dengan validasi ketat keamanan: (1) kontrak harus aktif dan milik user, (2) terlapor harus pihak lain dalam kontrak yang sama — mencegah user lapor sembarangan orang. `urgency_level` di-set otomatis dari `complaint_type.default_urgency` sesuai sistem web. Kirim notifikasi Pusher ke admin setelah pengaduan dibuat |
  | `show()` | Ditambahkan role-awareness: admin lihat semua; user hanya bisa lihat detail pengaduannya sendiri (anti-IDOR) |
  | `changeStatus()` | **Baru** — menggantikan `resolve()` lama; menangani semua transisi status (open/investigating/resolved) dengan validasi `resolution_notes` wajib saat resolved. Kirim notifikasi Pusher ke reporter saat status berubah |
  | `resolve()` | Dijadikan wrapper yang memanggil `changeStatus()` dengan `status=resolved` untuk backward compatibility |

#### Dihapus dari Grup Admin
- Route `GET /api/complaints` dan `GET /api/complaints/{id}` dipindah ke grup global (`jwt.auth`) agar user biasa juga bisa mengakses pengaduannya sendiri via mobile

---

### Audit & Perbaikan Bug: Ketidaksesuaian Web vs API — 2026-05-12

Hasil audit menyeluruh perbandingan alur sistem web dan API ditemukan 4 bug kritis yang langsung diperbaiki.

#### Diperbaiki

- **`app/Http/Controllers/Api/ApplicationController.php` baris 470** — Typo `$app->sheme_id` diubah ke `$app->scheme_id`. Kolom DB aslinya adalah `scheme_id` (bukan `sheme_id`). Bug ini menyebabkan field `scheme_id` selalu mengembalikan `null` di semua response API.

- **`app/Http/Controllers/Api/WorkerController.php` — `complaintWork()`** — Bug logika: `reported_user_id` di-set ke `$application->servant_id` padahal method ini dipakai oleh **pembantu** yang melapor **majikan**. Diperbaiki menjadi `$employerId` (yaitu `employe_id` atau `vacancy->user_id`). Bug ini menyebabkan pengaduan pembantu selalu menunjuk ke diri sendiri (servant) bukan ke majikan.

- **`app/Http/Controllers/Api/WorkerController.php` — `uploadMajikanFee()`** — Validasi `quantity` yang selalu `required` diganti menjadi conditional: hanya wajib diisi jika `infal_frequency` adalah `hourly`, `daily`, atau `weekly`. Sebelumnya API selalu reject upload jika tidak ada `quantity`, padahal pada skema fee reguler (non-infal) field ini tidak diperlukan — sesuai logika web.

- **`app/Http/Controllers/Api/WorkerController.php` — `uploadMajikanFee()`** — Formula kalkulasi admin fee diganti dari `schemaSalary->adds_client` / `adds_mitra` (model `Salary`, simple persentase) menjadi `scheme->client_data` / `mitra_data` (model `Scheme`, array per-item yang mendukung nilai flat maupun persentase `%`). Formula lama menyebabkan hasil kalkulasi total gaji majikan dan pembantu berbeda dengan yang tampil di web.

#### Ketidaksesuaian yang Dicatat (Tidak Diperbaiki Sesi Ini)

Temuan di bawah memerlukan diskusi lebih lanjut karena melibatkan perubahan alur besar:

| # | Deskripsi | File | Keterangan |
|---|-----------|------|------------|
| 1 | `changeStatus()` API hanya support 3 status (`schedule`, `passed`, `rejected`), web support lebih banyak (`interview`, `verify`, `contract`, `accepted`, dll) | `Api/ApplicationController.php:274` | Perlu tambah handler per-status |
| 2 | Tidak ada `updateSalary()` di API — update skema gaji (contract/fee/infal) hanya bisa dari web | `Api/ApplicationController.php` | Perlu implementasi baru |
| 3 | Web `store()` complaint menerima field `message`, API menerima `description` — keduanya disimpan ke kolom `description` di DB | `ComplaintController.php:114` vs `Api/ComplaintController.php:96` | Perlu standardisasi nama field |
| 4 | Web tidak ada minimum panjang deskripsi complaint, API minimal 20 karakter | `ComplaintController.php:114` | Pertimbangkan menambah validasi di web |

---

## Riwayat Sebelumnya

### [184ff4f] — feat: Enhance login process with input validation, user checks, and token management

### [8705399] — feat: Refactor complaints to pengaduan migration and add new API endpoints

### [144f914] — push latest changes and recon feature

### [90272b4] — feat: update worker salary controllers and migrations for absence & extra deduction

### [371414e] — feat/fix: major updates for worker tracking, complaint system, fee calculation and UI

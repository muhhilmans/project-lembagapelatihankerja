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

## Riwayat Sebelumnya

### [184ff4f] — feat: Enhance login process with input validation, user checks, and token management

### [8705399] — feat: Refactor complaints to pengaduan migration and add new API endpoints

### [144f914] — push latest changes and recon feature

### [90272b4] — feat: update worker salary controllers and migrations for absence & extra deduction

### [371414e] — feat/fix: major updates for worker tracking, complaint system, fee calculation and UI

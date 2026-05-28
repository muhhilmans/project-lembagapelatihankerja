# Alur Sistem API — Lembaga Pelatihan Kerja

Dokumen ini menjelaskan alur lengkap sistem dari sisi **Client (Majikan)** dan **Mitra (Pembantu)** via API, beserta keterlibatan **Admin** yang hanya bisa dilakukan lewat website.

---

## TAHAP 1 — Registrasi

| Role | Endpoint |
|---|---|
| Mitra | `POST /api/register-servant` |
| Majikan | `POST /api/register-employe` |

**Ketentuan:**
- `email` dan `username` harus unik di seluruh sistem
- `password` minimal 8 karakter, wajib ada `password_confirmation` yang sama
- Mitra wajib isi `profession_id` (ambil dari `GET /api/professions`) + data pribadi lengkap (tempat lahir, tanggal lahir, gender, agama)
- Setelah register, akun otomatis `is_active = false` — **belum bisa login**
- Sistem kirim OTP 6 digit ke email, berlaku **5 menit**

---

## TAHAP 2 — Verifikasi OTP

`POST /api/verify-otp`

**Ketentuan:**
- OTP harus dimasukkan sebelum 5 menit habis
- Jika expired, minta ulang via `POST /api/verify-otp/resend`
- Resend dibatasi **1x per menit**
- Setelah OTP benar → `email_verified_at` terisi

---

## TAHAP 3 — Aktivasi oleh Admin

> **Admin (web)** mengaktifkan akun secara manual.

**Ketentuan:**
- Akun baru default `is_active = false`
- Login API gagal **403** selama belum diaktifkan admin
- Login API gagal **401** jika email belum diverifikasi OTP

---

## TAHAP 4 — Login

`POST /api/login`

**Ketentuan:**
- Field `account` bisa diisi **email atau username** (sistem deteksi otomatis)
- Jika akun tidak aktif → 403
- Jika email belum verifikasi → 401
- Jika sudah punya token lama yang masih aktif → token lama di-blacklist otomatis
- Token berlaku **30 hari**

---

## TAHAP 5 — Majikan Buat Lowongan

`POST /api/vacancy`

**Ketentuan:**
- Wajib: `title`, `profession_ids[]` (array), `closing_date`, `limit`, `description`, `requirements`
- `limit` = jumlah mitra yang dibutuhkan
- Lowongan otomatis diarsipkan (soft delete) jika jumlah `accepted` sudah mencapai `limit`
- Jika `limit` dinaikkan saat edit → lowongan aktif kembali otomatis
- Lowongan yang diarsipkan bisa dipulihkan via `POST /api/vacancy/{id}/restore`

---

## TAHAP 6A — Mitra Cari & Apply Lowongan

```
GET  /api/seek-vacancy          → cari lowongan aktif
GET  /api/seek-vacancy/{id}     → detail lowongan
POST /api/apply-job             → apply
```

**Query Params (Optional):**
- `profession_ids`: Filter berdasarkan UUID profesi (bisa multi-id dipisah koma)
- `search`: Cari berdasarkan judul lowongan atau nama majikan
- `min_rating`: Filter berdasarkan minimal rata-rata rating majikan (1-5)
- `page`: Pagination (5 data per halaman)

**Ketentuan apply:**
- Tidak bisa melamar lowongan yang sama dua kali
- Tidak bisa melamar jika punya kontrak aktif tipe **contract** atau **fee regular** (`is_infal = false`)
- Mitra tipe **infal** (`is_infal = true`) boleh melamar ke banyak tempat sekaligus
- Status awal lamaran → `pending`

---

## TAHAP 6B — Majikan Hire Langsung (tanpa lowongan)

```
GET  /api/seek-mitra                      → cari mitra
GET  /api/seek-mitra/detail/{id}          → detail mitra
POST /api/seek-mitra/detail/{id}/hire     → hire langsung
```

**Query Params (Optional):**
- `search` / `name`: Cari berdasarkan nama mitra
- `religion`: Filter berdasarkan agama (Islam, Kristen, dll)
- `profession_ids`: Filter UUID profesi (bisa multi-id dipisah koma)
- `is_inval`: 1 = Bersedia, 0 = Tidak (Inval/Infal)
- `is_stay`: 1 = Bersedia, 0 = Tidak (Pulang Pergi)
- `min_age` & `max_age`: Range usia mitra
- `min_experience` & `max_experience`: Range tahun pengalaman kerja
- `min_rating`: Minimal rating mitra (1-5)
- `page`: Pagination (10 data per halaman)

**Ketentuan:**
- Mitra yang muncul di pencarian hanya yang `working_status = false`
- Mitra infal tetap muncul meski sedang punya kontrak aktif
- Status awal → `pending`

---

## TAHAP 7 — Admin Set Jadwal Interview

> **Admin (web)** → ubah status ke `schedule`

**Ketentuan:**
- Admin set `interview_date` dan `notes_interview`
- Hanya bisa dari status `pending`
- Majikan & mitra bisa lihat jadwal via `GET /api/schedule-interview` (maks 3 jadwal terdekat)

---

## TAHAP 8 — Admin Set Link Interview

> **Admin (web)** → ubah status ke `interview`

**Ketentuan:**
- Admin set `link_interview` dan `notes_interview`
- Hanya bisa dari status `schedule`

---

## TAHAP 9 — Majikan Set Gaji & Tipe Kontrak

`POST /api/all-worker/{id}/set-salary`

**Ketentuan umum:**
- Hanya bisa jika status belum `accepted`, `rejected`, atau `laidoff`
- Wajib pilih `salary_type`: `contract` atau `fee`
- Jika status masih `interview` saat dipanggil → **otomatis naik ke `passed`**

**Jika `contract`:**
| Field | Wajib | Keterangan |
|---|---|---|
| contract_salary | ✅ | Gaji bulanan |
| contract_start_date | ✅ | Tanggal mulai |
| admin_fee | ❌ | Biaya admin |
| contract_end_date | ❌ | Tanggal selesai |
| garansi_id | ❌ | ID paket garansi |
| garansi_price | ❌ | Harga garansi |
| warranty_duration | ❌ | Durasi garansi |

**Jika `fee` + tidak infal (`is_infal = false` — fee reguler):**
| Field | Wajib | Keterangan |
|---|---|---|
| fee_salary_regular | ✅ | Tarif gaji |
| fee_frequency_regular | ✅ | Frekuensi (misal: bulanan) |
| fee_end_date_regular | ❌ | Tanggal akhir majikan (pembantu +7 hari) |

**Jika `fee` + infal (`is_infal = true`):**
| Field | Wajib | Keterangan |
|---|---|---|
| infal_frequency | ✅ | `hourly` / `daily` / `weekly` |
| fee_salary_infal | ✅ | Tarif per satuan |
| infal_start_date | ✅ | Tanggal mulai |
| infal_end_date | ❌ | Tanggal selesai (tidak untuk hourly) |
| infal_time_in | ✅* | Jam masuk (wajib jika hourly) |
| infal_time_out | ✅* | Jam keluar (wajib jika hourly) |
| infal_hourly_rate | ✅* | Tarif per jam (wajib jika hourly) |

---

## TAHAP 10 — Majikan Kirim Tawaran ke Mitra

`PUT /api/all-applicant/{id}/change` dengan `status=verify`

**Ketentuan:**
- Tidak bisa jika status sudah `accepted`, `rejected`, atau `laidoff`
- Opsional kirim `notes_verify` sebagai catatan untuk mitra

---

## TAHAP 11 — Mitra Terima atau Tolak Tawaran

`PUT /api/all-application/{id}/choose`

**Ketentuan:**
- **Hanya bisa saat status = `passed`**
- Nilai yang dikirim: `choose` (terima) atau `rejected` (tolak)
- Jika `rejected` → proses selesai
- Jika `choose` → lanjut ke upload kontrak

---

## TAHAP 12 — Majikan Upload File Kontrak

`POST /api/all-worker/{id}/upload-contract`

**Ketentuan:**
| Field | Wajib | Keterangan |
|---|---|---|
| work_start_date | ✅ | Tanggal mulai kerja (YYYY-MM-DD) |
| file_contract | ✅ | File kontrak (pdf/jpg/jpeg/png, max 5MB) |

**Efek setelah berhasil:**
- Status → `accepted`
- Jika **bukan infal**: `working_status` pembantu = `true`, semua lamaran lain pembantu → `rejected`
- Jika **infal**: pembantu tetap bisa punya kontrak aktif di tempat lain
- Jika kuota lowongan terpenuhi → lowongan otomatis ditutup

---

## TAHAP 13 — Majikan Upload Bukti Pembayaran

### Contract Bulanan
`POST /api/all-worker/{id}/uploadPayment-contract`

| Field | Wajib | Keterangan |
|---|---|---|
| month | ✅ | Format `YYYY-MM` |
| proof_majikan | ✅ | File bukti (jpg/jpeg/png/pdf, max 2MB) |

- Record gaji bulan itu dibuat otomatis jika belum ada
- Total gaji = `salary` + `admin_fee`
- Upload ulang di bulan sama → foto diganti

### Fee / Infal
`POST /api/all-worker/{id}/uploadPayment-fee`

| Field | Wajib | Keterangan |
|---|---|---|
| month | ✅ | Format `YYYY-MM` |
| proof_majikan | ✅ | File bukti (jpg/jpeg/png/pdf, max 2MB) |
| absence_days | ❌ | Jumlah hari tidak hadir |
| absence_reason | ❌ | Alasan tidak hadir |
| extra_deduction | ❌ | Potongan tambahan |
| quantity | ✅* | Wajib jika `infal_frequency = hourly/daily/weekly` |

- Sistem hitung otomatis: `tarif × quantity − potongan absen − extra_deduction`
- Jika ada `scheme`: fee klien ditambah ke tagihan majikan, potongan mitra dikurangi dari gaji pembantu
- Upload ulang bulan sama → data di-update, bukan duplikat

---

## TAHAP 14 — Admin Verifikasi & Teruskan ke Pembantu

> **Admin (web)**

### Verifikasi bukti majikan
`POST /api/all-worker/{id}/verify-payment`

| Field | Wajib | Keterangan |
|---|---|---|
| month | ✅ | Format `YYYY-MM` |
| action | ✅ | `verified` atau `rejected` |

- Jika `rejected` → foto bukti dihapus, majikan bisa upload ulang
- Jika `verified` → lanjut upload bukti ke pembantu

### Upload bukti ke pembantu — Contract
`POST /api/all-worker/{id}/uploadPayment-admin-contract`

| Field | Wajib | Keterangan |
|---|---|---|
| month | ✅ | Format `YYYY-MM` |
| proof_admin | ✅ | File bukti transfer (jpg/jpeg/png/pdf, max 2MB) |

### Upload bukti ke pembantu — Fee
`POST /api/all-worker/{id}/uploadPayment-admin-fee/{salary_id}`

| Field | Wajib | Keterangan |
|---|---|---|
| proof_admin | ✅ | File bukti transfer (jpg/jpeg/png/pdf, max 2MB) |

> `salary_id` = `data.gaji[].id` dari `GET /api/all-worker/{id}`

---

## TAHAP 15 — Operasional Kontrak Aktif

### Perpanjang kontrak
`POST /api/all-worker/{id}/extend-contract`

| Field | Wajib | Keterangan |
|---|---|---|
| extend_months | ✅ | Jumlah bulan tambahan (min: 1) |

- Diperpanjang dari tanggal akhir yang ada
- Jika belum ada tanggal akhir → dihitung dari start + 12 bulan sebagai basis

### Akhiri kontrak
`POST /api/all-worker/{id}/end-contract`

| Field | Wajib | Keterangan |
|---|---|---|
| end_reason | ✅ | Alasan pengakhiran |

- Status → `laidoff`
- `work_end_date` = hari ini

### Tukar pembantu (garansi)
`POST /api/all-worker/{id}/swap-servant`

- Tidak butuh body
- Kontrak lama → `laidoff`, `work_end_date` = start + 1 bulan
- Majikan hire pembantu pengganti secara manual setelah ini

### Aduan pekerja (oleh Majikan)
`POST /api/all-worker/{id}/complaint-worker`

| Field | Wajib | Keterangan |
|---|---|---|
| complaint_type_id | ✅ | UUID tipe dari `GET /api/complaint-types` |
| description | ✅ | Min 20 karakter |

- Urgensi otomatis dari `default_urgency` tipe yang dipilih
- Admin dinotifikasi via Pusher

### Aduan majikan (oleh Mitra)
`POST /api/all-work/{id}/complaint-work`

- Ketentuan sama seperti di atas

---

## TAHAP 16 — Setelah Kontrak Selesai

### Ulasan
`POST /api/reviews/{id}`

| Field | Wajib | Keterangan |
|---|---|---|
| rating | ✅ | Nilai 1–5 |
| comment | ✅ | Komentar (max 1000 karakter) |

**Ketentuan:**
- Hanya bisa jika status kontrak = `laidoff`
- Setiap user hanya bisa beri **1 ulasan per kontrak**
- Majikan mereview mitra, mitra mereview majikan (otomatis berdasarkan role)

---

## Ringkasan Keterlibatan Admin (web only)

| Aksi | Kapan |
|---|---|
| Aktifkan akun baru | Setelah pengguna registrasi |
| Set jadwal interview (`schedule`) | Setelah majikan terima pelamar |
| Set link interview (`interview`) | Sebelum hari interview |
| Verifikasi pembayaran majikan | Setelah majikan upload bukti |
| Upload bukti transfer ke pembantu | Setelah pembayaran diverifikasi |
| Kelola & selesaikan pengaduan | Kapan saja |

---

## Aturan Tipe Kontrak

| Tipe | Bisa Apply ke Banyak Tempat? | working_status dikunci? |
|---|---|---|
| `contract` | ❌ Tidak | ✅ Ya |
| `fee` reguler (`is_infal = false`) | ❌ Tidak | ✅ Ya |
| `fee` infal (`is_infal = true`) | ✅ Ya | ❌ Tidak |

---

## Alur Status Lamaran

```
pending
  └─→ schedule   (admin set jadwal interview)
        └─→ interview   (admin set link interview)
              └─→ passed   (majikan set salary / setSalary auto-promote)
                    ├─→ rejected   (mitra tolak via chooseStatus)
                    └─→ choose     (mitra terima via chooseStatus)
                          └─→ verify   (majikan konfirmasi)
                                └─→ accepted   (majikan upload kontrak)
                                      └─→ laidoff   (kontrak selesai / diakhiri)
```

# Elite Class Progress Report - MA Unggul SABIRA

Sistem monitoring progres akademik untuk kelas unggulan. Aplikasi ini mendukung input evaluasi mingguan, pengelolaan komponen penilaian, analitik progres siswa, serta integrasi SSO Gate SABIRA.

## Ringkasan Fitur

- **Manajemen Program & Mata Pelajaran**
  - Buat/edit program dan mata pelajaran.
  - Status aktif/nonaktif.
- **Komponen Penilaian**
  - Buat/edit komponen per mata pelajaran.
  - Atur bobot, aktif/nonaktif, urutan.
- **Enrollment (Pendaftaran Siswa)**
  - Assign siswa ke mata pelajaran.
  - Aktivasi/deaktivasi enrollment.
- **Evaluasi Mingguan**
  - Input evaluasi per siswa per minggu.
  - Lock evaluasi (pembina) dan unlock (superadmin).
- **Analitik Progres**
  - Tren mingguan per siswa.
  - Perbandingan progres antar siswa (line chart).
  - Filter timeframe (4/8/12 minggu, 1/3/6/12 bulan).
- **RBAC & Audit Log**
  - Hak akses berbasis role + permission.
  - Audit log perubahan data.
- **SSO Gate SABIRA**
  - Login via SSO (OIDC/OAuth2).
- **Superadmin Settings**
  - Pengaturan umum (nama, tagline, deskripsi, logo).
  - Server (SMTP, SSO) dapat diatur lewat UI.
  - Secret sensitif terenkripsi di database.

## Role & Akses

Role utama:
- **Superadmin**: full access + unlock evaluasi + settings
- **Admin**: manajemen program, mapel, komponen, enrollment
- **Pembina**: input evaluasi, analitik progres, dan akses mapel yang ditugaskan
- **Siswa**: melihat progres pribadi

Hak akses diatur via **Superadmin > Hak Akses**.

## Alur Aplikasi (Ringkas)

1. **Admin/Superadmin** membuat program & mata pelajaran.
2. **Admin/Superadmin** membuat komponen penilaian per mapel.
3. **Admin/Superadmin** assign pembina ke mapel (penugasan).
4. **Admin/Pembina** assign siswa ke mapel (enrollment) sesuai scope penugasan.
5. **Pembina** input evaluasi mingguan (komponen + skor total).
6. **Pembina** dapat lock evaluasi. **Superadmin** bisa unlock.
7. **Pembina/Siswa** memantau progres di dashboard dan halaman analitik.

## Struktur Modul Utama

- `app/Http/Controllers/Admin` — program, mapel, komponen, enrollment, pembina assignment
- `app/Http/Controllers/Pembina` — evaluasi, progress
- `app/Http/Controllers/Student` — dashboard & progres siswa
- `app/Http/Controllers/Superadmin` — permissions, audit logs, settings
- `app/Services` — analytics, evaluation, settings
- `resources/views` — UI
- `database/seeders` — roles, permissions, demo data

## Konfigurasi Settings

Pengaturan disimpan di tabel `settings` dan diterapkan saat boot aplikasi.

**General**
- `general.app_name`, `general.app_tagline`, `general.app_description`, `general.app_logo`

**SMTP**
- `smtp.host`, `smtp.port`, `smtp.username`, `smtp.password`, `smtp.encryption`, `smtp.from_name`, `smtp.from_address`

**SSO**
- `sso.base_url`, `sso.client_id`, `sso.client_secret`, `sso.redirect_uri`, `sso.authorize_endpoint`, `sso.token_endpoint`, `sso.userinfo_endpoint`, `sso.scopes`

> `smtp.password` dan `sso.client_secret` disimpan terenkripsi di database.

## Setup Lokal

1) Install dependencies:

```bash
composer install
```

2) Salin env:

```bash
cp .env.example .env
php artisan key:generate
```

3) Migrasi + seed:

```bash
php artisan migrate
php artisan db:seed
```

4) Storage link (logo/settings):

```bash
php artisan storage:link
```

5) Jalankan aplikasi:

```bash
php artisan serve
```

Catatan: Aplikasi menggunakan CDN untuk asset (Tailwind/Alpine/Chart.js), jadi **tidak memerlukan npm build**.

## SSO Gate SABIRA

Konfigurasi SSO bisa lewat **Superadmin > Pengaturan**, atau langsung di `.env` / `config/sso.php`.

Pastikan endpoint `authorize`, `token`, `userinfo` sesuai aplikasi Gate SSO Anda.

## Seeder & Demo Data

- **Default user** hanya dibuat pada **non-production**.
- **DemoSeeder** (local env) membuat data program, mapel, komponen, enrollments, dan evaluasi mingguan.

## Testing

```bash
php artisan test
```

## Catatan Deployment (VPS)

Sebelum deploy:
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` terisi
- Pastikan konfigurasi DB, SMTP, SSO sudah benar
- Jalankan:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Catatan penting:
- Seeder default user **tidak dibuat** di production.
- Buat akun superadmin secara manual (tinker) bila perlu.

Contoh pembuatan superadmin:

```bash
php artisan tinker
>>> $u = App\Models\User::create([
... 'name' => 'Super Administrator',
... 'email' => 'superadmin@sabira.sch.id',
... 'password' => bcrypt('password'),
... 'is_active' => true,
... ]);
>>> $u->roles()->sync([App\Models\Role::where('name','superadmin')->first()->id]);
```

---

Jika butuh dokumentasi teknis tambahan (API, ERD, arsitektur deployment), beri tahu saya.

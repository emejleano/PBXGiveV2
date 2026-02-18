# PBXCF Backend — Portal Crowdfunding (Operational Layer)

Backend REST API untuk platform crowdfunding PBXCF, dibangun dengan Laravel 12.

---

## 📌 Tech Stack

| Komponen         | Teknologi                                      |
| ---------------- | ---------------------------------------------- |
| Framework        | Laravel 12                                     |
| PHP              | 8.3                                            |
| Database         | MySQL                                          |
| Arsitektur       | REST API (stateless, tanpa session-based auth)  |
| Authentication   | Laravel Sanctum (token-based / Bearer Token)   |
| API Docs         | Scramble — OpenAPI (Swagger) auto-generated     |
| Multi-tenant     | Tenant ID based (via `tenant_id` di setiap tabel) |
| Prefix Tabel     | `pbxcf_`                                       |

---

## 🚀 Cara Setup Project (Pertama Kali)

### 1. Clone & Install Dependencies

```bash
git clone <repo-url>
cd pbxcf-backend
composer install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pbxcf_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Buat Database

Buat database MySQL dengan nama `pbxcf_db` (atau sesuai `.env`).

### 4. Jalankan Migration

```bash
php artisan migrate
```

### 5. Jalankan Server

```bash
php artisan serve
```

Server berjalan di `http://localhost:8000`.

---

## 📖 Akses API Documentation

Setelah server berjalan, buka di browser:

| Halaman       | URL                                  |
| ------------- | ------------------------------------ |
| Swagger UI    | `http://localhost:8000/docs/api`     |
| OpenAPI JSON  | `http://localhost:8000/docs/api.json`|

Dokumentasi API di-generate otomatis oleh **Scramble** dari route dan controller yang ada.

---

## 🔐 Authentication (Sanctum)

Project ini menggunakan **Laravel Sanctum** dengan mode **token-based** (Bearer Token), **bukan** session/cookie.

### Cara Kerja:

1. User login → server generate **Personal Access Token**
2. Client menyimpan token tersebut
3. Setiap request ke endpoint yang protected, kirim header:
   ```
   Authorization: Bearer {token}
   ```
4. Jika tidak ada token / token invalid → response `401 Unauthenticated`

### Konfigurasi Penting:

| Config                        | Nilai                  | Keterangan                          |
| ----------------------------- | ---------------------- | ----------------------------------- |
| Default Auth Guard            | `sanctum`              | `config/auth.php`                   |
| Sanctum Guard                 | `web` (fallback)       | `config/sanctum.php`                |
| Token Expiration              | 7 hari (10080 menit)    | Configurable via `.env`             |
| User Model Trait              | `HasApiTokens`         | `app/Models/User.php`               |

---

## 📁 Struktur Project Saat Ini

```
pbxcf-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── BaseApiController.php  # Base controller + response helpers
│   │   ├── Middleware/
│   │   │   └── EnsureTenant.php           # Multi-tenant middleware (X-Tenant-ID)
│   │   └── Requests/                      # Form request validations (akan diisi)
│   ├── Models/
│   │   ├── Traits/
│   │   │   └── BelongsToTenant.php        # Auto-scope & auto-set tenant_id
│   │   ├── Campaign.php
│   │   ├── CampaignUpdate.php
│   │   ├── Category.php
│   │   ├── Donation.php
│   │   ├── Tenant.php
│   │   ├── User.php                       # + HasApiTokens, BelongsToTenant, roles
│   │   └── Withdrawal.php
│   ├── Services/                          # Business logic (akan diisi)
│   └── Providers/
│       └── AppServiceProvider.php         # Scramble Bearer Token config
├── bootstrap/
│   └── app.php                            # API routing + tenant middleware alias
├── config/
│   ├── auth.php                           # Guard default: sanctum
│   ├── database.php                       # MySQL prefix: pbxcf_
│   ├── sanctum.php                        # Token-based config
│   └── scramble.php                       # API docs config
├── database/migrations/                   # 11 migration files
├── routes/
│   ├── api.php                            # API routes (tenant-scoped group ready)
│   └── web.php
└── .env.example
```

---

## 🗄️ Database Tables (prefix: `pbxcf_`)

| Tabel                          | Keterangan                                  |
| ------------------------------ | ------------------------------------------- |
| `pbxcf_tenants`                | Data tenant/organisasi                      |
| `pbxcf_users`                  | User dengan `tenant_id` & `role`            |
| `pbxcf_categories`             | Kategori campaign (per tenant)              |
| `pbxcf_campaigns`              | Campaign crowdfunding                       |
| `pbxcf_donations`              | Donasi ke campaign                          |
| `pbxcf_campaign_updates`       | Update/progress dari campaign               |
| `pbxcf_withdrawals`            | Pencairan dana campaign                     |
| `pbxcf_personal_access_tokens` | Sanctum API tokens                          |
| `pbxcf_sessions`               | Session storage                             |
| `pbxcf_cache` / `cache_locks`  | Cache storage                               |
| `pbxcf_jobs` / `job_batches` / `failed_jobs` | Queue management         |

---

## 🏢 Multi-Tenant

### Konsep

Setiap data di-scope berdasarkan `tenant_id`. Satu database, banyak tenant (shared database, shared schema).

### Cara Kerja

1. **Header `X-Tenant-ID`** — Client kirim header ini di setiap request ke endpoint tenant-scoped
2. **Middleware `tenant`** — Validasi tenant exists & aktif, reject jika tidak valid
3. **Trait `BelongsToTenant`** — Otomatis filter query + set `tenant_id` saat create record
4. **Superadmin** — Bisa akses semua data lintas tenant (skip tenant scope)

### User Roles

| Role         | Akses                                  |
| ------------ | -------------------------------------- |
| `superadmin` | Akses semua tenant, full control       |
| `admin`      | Admin per tenant                       |
| `operator`   | Operator per tenant                    |
| `donor`      | Donator (default role)                 |

### Contoh Request dengan Tenant

```bash
curl -X GET http://localhost:8000/api/campaigns \
  -H "Authorization: Bearer {token}" \
  -H "X-Tenant-ID: 1"
```

## Progress Setup

### Setup Foundation

- [x] Project Laravel 12 berjalan
- [x] Laravel Sanctum terpasang & dikonfigurasi (token-based, stateless)
- [x] Scramble (OpenAPI/Swagger) terpasang & bisa diakses di `/docs/api`
- [x] Migration core selesai (16 tabel dengan prefix `pbxcf_`)
- [x] Multi-tenant middleware aktif (`EnsureTenant` + `BelongsToTenant` trait)
- [x] Struktur modular siap digunakan

---

## 🔧 Environment Variables Penting

```dotenv
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pbxcf_db
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000
SANCTUM_TOKEN_EXPIRATION=10080    # Token berlaku 7 hari (dalam menit)
```

---

## 📝 Catatan

- Semua API endpoint menggunakan prefix `/api`
- Authentication menggunakan **Bearer Token** (bukan cookie/session)
- API docs otomatis ter-update setiap ada perubahan route/controller
- Scramble menampilkan **Authorize** button di Swagger UI untuk input Bearer Token
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
│   ├── Http/Controllers/        # API Controllers
│   ├── Models/
│   │   └── User.php             # User model + HasApiTokens
│   └── Providers/
│       └── AppServiceProvider.php  # Scramble security config
├── bootstrap/
│   └── app.php                  # API routing + statefulApi middleware
├── config/
│   ├── auth.php                 # Guard default: sanctum
│   ├── sanctum.php              # Sanctum config (token-based)
│   └── scramble.php             # API docs config
├── database/migrations/         # Migration files
├── routes/
│   ├── api.php                  # API routes (prefix /api)
│   └── web.php                  # Web routes
└── .env.example                 # Environment template
```

---

## 📦 Package yang Diinstall

### Production

| Package              | Versi    | Fungsi                              |
| -------------------- | -------- | ----------------------------------- |
| `laravel/framework`  | ^12.0    | Framework utama                     |
| `laravel/sanctum`    | ^4.3     | API authentication (Bearer Token)   |
| `dedoc/scramble`     | ^0.13.14 | Auto-generate OpenAPI/Swagger docs  |
| `laravel/tinker`     | ^2.10    | REPL untuk debugging                |

### Development

| Package                 | Fungsi                    |
| ----------------------- | ------------------------- |
| `fakerphp/faker`        | Generate fake data        |
| `laravel/pint`          | Code style fixer          |
| `phpunit/phpunit`       | Unit testing              |
| `mockery/mockery`       | Mocking untuk test        |
| `nunomaduro/collision`  | Better error reporting    |

---

## Progress Setup

### Hari 1 — Setup Foundation

- Project Laravel 12 berjalan
- Laravel Sanctum terpasang & dikonfigurasi (token-based, stateless)
- Scramble (OpenAPI/Swagger) terpasang & bisa diakses di `/docs/api`
- Migration `personal_access_tokens` berhasil

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
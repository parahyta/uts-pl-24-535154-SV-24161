# Craftory 🧶

**Craftory** adalah aplikasi web untuk mencatat, mengorganisasi, dan memantau proyek kerajinan pribadi (*craft projects*), seperti crochet, knitting, scrapbooking, painting, embroidery, dan aktivitas DIY lainnya.

Aplikasi menggunakan **Vue 3** sebagai frontend, **Laravel 12** sebagai RESTful API, **Laravel Sanctum** untuk autentikasi berbasis token, serta **PostgreSQL** untuk penyimpanan data. Lingkungan container dijalankan menggunakan **Docker Compose**, dengan **Nginx** sebagai server frontend sekaligus *reverse proxy* dan **FrankenPHP** sebagai server aplikasi Laravel.

Proyek ini dikembangkan sebagai bagian dari Ujian Tengah Semester mata kuliah **Konstruksi dan Evolusi Perangkat Lunak**.

## Fitur Utama

- Registrasi akun, login, dan logout menggunakan Laravel Sanctum.
- Dashboard berisi ringkasan proyek berdasarkan status pengerjaan.
- Menampilkan daftar proyek dan detail masing-masing proyek.
- Menambahkan, memperbarui, dan menghapus proyek kerajinan (CRUD).
- Menyimpan kategori, progres, status, tanggal mulai, target penyelesaian, tingkat kesulitan, bahan, catatan, URL referensi gambar, dan URL tutorial.
- Pencarian dan penyaringan proyek berdasarkan nama, kategori, atau status.
- Pembatasan data proyek berdasarkan akun pengguna yang terautentikasi.
- Pengujian API menggunakan Postman dan Laravel Feature Tests.
- Otomatisasi CI/CD melalui GitHub Actions dan publikasi image ke GHCR.

## Teknologi

| Komponen | Teknologi |
| --- | --- |
| Frontend | Vue 3, Vite, Vue Router, Axios, Tailwind CSS |
| Backend/API | Laravel 12, PHP 8.4, Laravel Sanctum |
| Database | PostgreSQL 17 |
| Server frontend | Nginx |
| Server backend | FrankenPHP (PHP 8.4) |
| Containerization | Docker, Docker Compose |
| Testing | Laravel Feature Tests, Postman |
| CI/CD | GitHub Actions, GitHub Container Registry (GHCR) |

## Struktur Repository

```text
.
├── .github/
│   └── workflows/
│       └── ci.yml
├── backend/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   ├── .dockerignore
│   ├── Caddyfile
│   ├── Dockerfile
│   ├── composer.json
│   └── composer.lock
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   ├── router/
│   │   ├── services/
│   │   └── views/
│   ├── .dockerignore
│   ├── Dockerfile
│   ├── nginx.conf
│   ├── package.json
│   └── package-lock.json
├── postman/
│   └── Craftory-REST-API.postman_collection.json
├── .env.example
├── docker-compose.yml
└── README.md
```

## Arsitektur dan Alur Data

```text
Browser (http://localhost:8080)
          |
          v
Vue.js + Axios
          | HTTP /api/...
          v
Nginx (service: frontend)
          | reverse proxy
          v
FrankenPHP + Laravel (service: backend:80)
          | Eloquent / PostgreSQL driver
          v
PostgreSQL (service: db:5432)
          |
          v
Respons JSON -> Laravel -> Nginx -> Vue.js
```

Contoh pembuatan proyek:

1. Pengguna mengisi formulir **Create Project** pada frontend Vue.
2. Axios mengirim `POST /api/projects` dengan Bearer Token.
3. Nginx meneruskan permintaan `/api` ke service Docker `backend:80`.
4. Laravel Sanctum memverifikasi token; controller memvalidasi data dan menyimpan proyek melalui Eloquent.
5. PostgreSQL pada service `db:5432` menyimpan data ke volume persisten.
6. Laravel mengembalikan respons JSON dengan status **201 Created**, kemudian Vue memperbarui tampilan.

## Menjalankan dengan Docker Compose (Direkomendasikan)

### Prasyarat

- Docker Desktop atau Docker Engine beserta Docker Compose.
- Git untuk mengambil repository.

### 1. Clone repository

```bash
git clone https://github.com/parahyta/uts-pl-24-535154-SV-24161.git
cd uts-pl-24-535154-SV-24161
```

### 2. Siapkan environment di root repository

Salin file contoh menjadi `.env`:

```bash
cp .env.example .env
```

Pastikan `.env` root berisi variabel berikut dengan **nilai milikmu sendiri**:

```dotenv
DB_DATABASE=craftory
DB_USERNAME=craftory_user
DB_PASSWORD=ISI_PASSWORD_DATABASE_YANG_KUAT
APP_KEY=ISI_APP_KEY_LARAVEL
```

Buat `APP_KEY` yang valid (diawali `base64:`) menggunakan salah satu cara berikut jika PHP dan dependensi backend sudah tersedia secara lokal:

```bash
cd backend
php artisan key:generate --show
cd ..
```

Atau buat kunci dengan perintah PHP dari image backend setelah image dibangun:

```bash
docker compose build backend
docker compose run --rm --no-deps --entrypoint php backend artisan key:generate --show
```

Salin hasil kunci tersebut ke `APP_KEY` dalam `.env` root. **Jangan commit `.env` atau membagikan nilai rahasia.**

### 3. Build dan jalankan layanan

```bash
docker compose up -d --build
```

Periksa ketiga layanan:

```bash
docker compose ps
```

Service yang digunakan:

| Service | Fungsi | Port/jaringan |
| --- | --- | --- |
| `frontend` | Vue dan Nginx | `localhost:8080` -> container port `80` |
| `backend` | Laravel melalui FrankenPHP | `backend:80` (internal) |
| `db` | PostgreSQL | `db:5432` (internal) |

Buka aplikasi: **http://localhost:8080**

Backend menjalankan migrasi database saat service dimulai. Pada saat pertama kali menggunakan database container, silakan **Register** akun baru karena database Docker terpisah dari database PostgreSQL lokal.

### 4. Perintah Docker yang berguna

```bash
# Status service
docker compose ps

# Pantau log backend FrankenPHP
docker compose logs -f backend

# Log semua service
docker compose logs --tail=80

# Hentikan dan hapus container tanpa menghapus volume data
docker compose down

# Jalankan kembali
docker compose up -d
```

**Peringatan:** `docker compose down -v` menghapus named volume PostgreSQL. Jangan gunakan opsi `-v` jika ingin mempertahankan data proyek.

### Persistensi database

PostgreSQL menggunakan named volume `postgres_data` dalam Docker Compose. Data proyek tetap tersedia setelah menjalankan `docker compose down` lalu `docker compose up -d`, selama volume tidak dihapus.

## Menjalankan secara Lokal tanpa Docker

Untuk pengembangan manual, siapkan **PHP 8.4**, Composer, Node.js, npm, dan PostgreSQL.

### Backend Laravel

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada `backend/.env` dengan PostgreSQL lokal (`DB_CONNECTION=pgsql`, host, port, nama database, pengguna, dan password). Setelah database tersedia:

```bash
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

Perintah `php artisan serve` **hanya digunakan untuk pengembangan manual lokal**. Backend dalam Docker Compose menggunakan **FrankenPHP**.

### Frontend Vue

Pada terminal lain:

```bash
cd frontend
npm ci
```

Buat `frontend/.env` dengan konfigurasi:

```dotenv
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Kemudian jalankan:

```bash
npm run dev
```

Buka alamat localhost yang ditampilkan Vite. Dalam Docker Compose, frontend menggunakan `/api` melalui Nginx, bukan alamat localhost backend dari lingkungan development.

## RESTful API

Endpoint berikut berada di bawah prefix `/api`. Endpoint yang bertanda **Autentikasi** membutuhkan header `Authorization: Bearer <token>`.

| Method | Endpoint | Fungsi | Akses |
| --- | --- | --- | --- |
| `POST` | `/api/register` | Registrasi pengguna | Publik |
| `POST` | `/api/login` | Login dan mendapatkan token | Publik |
| `POST` | `/api/logout` | Logout/mencabut token | Autentikasi |
| `GET` | `/api/user` | Mengambil data pengguna aktif | Autentikasi |
| `GET` | `/api/projects` | Daftar proyek milik pengguna | Autentikasi |
| `GET` | `/api/projects/{id}` | Detail proyek | Autentikasi |
| `POST` | `/api/projects` | Membuat proyek baru | Autentikasi |
| `PUT` | `/api/projects/{id}` | Memperbarui proyek | Autentikasi |
| `DELETE` | `/api/projects/{id}` | Menghapus proyek | Autentikasi |

### Contoh payload membuat proyek

```json
{
  "name": "Crochet Bag",
  "category": "crochet",
  "status": "in_progress",
  "progress": 40,
  "start_date": "2026-10-25",
  "target_date": null,
  "reference_image_url": null,
  "tutorial_url": null,
  "difficulty": "beginner",
  "materials": "Benang dan hakpen",
  "notes": "Proyek latihan"
}
```

Nilai `status` dan `progress` harus konsisten: `not_started` = 0%, `in_progress` = 1–99%, dan `completed` = 100%. Kategori yang tersedia: `crochet`, `knitting`, `scrapbooking`, `painting`, `embroidery`, `beading`, `clay_craft`, `sewing`, dan `other`.

## Pengujian

### Laravel Feature Tests

Untuk lingkungan lokal (sesuai konfigurasi testing Laravel pada repository):

```bash
cd backend
php artisan test
```

GitHub Actions menjalankan pengujian backend dengan SQLite in-memory untuk memisahkan pengujian dari database PostgreSQL utama.

### Build frontend

```bash
cd frontend
npm ci
npm run build
```

### Postman Collection

Import file berikut ke Postman:

[`postman/Craftory-REST-API.postman_collection.json`](postman/Craftory-REST-API.postman_collection.json)

Collection menyediakan contoh request autentikasi dan operasi CRUD. Jalankan login untuk memperoleh token pengujian baru dan gunakan token tersebut pada request yang memerlukan autentikasi. **Jangan menyimpan atau meng-commit token aktif ke file Collection.**

## Git Workflow dan CI/CD

Pengembangan menggunakan branch `feature/**`, branch integrasi `dev`, dan branch rilis `main`. Perubahan diintegrasikan melalui Pull Request dengan pemeriksaan CI yang diwajibkan oleh branch protection.

Workflow GitHub Actions berada di [`.github/workflows/ci.yml`](.github/workflows/ci.yml) dan mencakup:

- **Backend Tests:** menjalankan Laravel Feature Tests.
- **Frontend Build:** menjalankan `npm ci` dan `npm run build`.
- **Deploy Docker Images to GHCR:** berjalan **hanya pada event `push` ke `main`**, setelah kedua job CI berhasil.

Pada Pull Request, `dev`, dan branch fitur, job deploy dilewati (*Skipped*). Saat deployment dijalankan, workflow membangun image backend dan frontend lalu mempublikasikannya ke **GitHub Container Registry (GHCR)** dengan **tag commit SHA**, bukan `latest`.

Nama image:

```text
ghcr.io/parahyta/uts-pl-24-535154-sv-24161-backend:<commit-sha>
ghcr.io/parahyta/uts-pl-24-535154-sv-24161-frontend:<commit-sha>
```

Ganti `<commit-sha>` dengan SHA commit dari workflow pada branch `main`.

## Keamanan

- Jangan commit `.env`, `APP_KEY`, password database, Bearer Token, atau data rahasia lainnya.
- Repository menyediakan `.env.example` sebagai panduan tanpa kredensial sungguhan.
- `APP_KEY` dan `DB_PASSWORD` dikelola melalui **GitHub Actions Secrets**; tidak disisipkan ke Docker image saat build.
- GHCR menggunakan `GITHUB_TOKEN` otomatis dengan izin `packages: write`.
- Token Laravel Sanctum yang pernah tidak sengaja dipublikasikan harus **dicabut** pada database penerbit token, bukan hanya dihapus dari file terbaru.
- Untuk deployment produksi, gunakan secret dan konfigurasi runtime yang sesuai; konfigurasi Docker Compose dalam proyek ini ditujukan untuk pengujian lokal.

## Troubleshooting

| Masalah | Pemeriksaan / solusi |
| --- | --- |
| `apt-get` gagal mengunduh paket | Periksa koneksi Docker ke repository Debian, lalu ulangi build. |
| `composer install` gagal karena Symfony membutuhkan PHP >= 8.4.1 | Gunakan base image yang menyediakan PHP 8.4. |
| Frontend menampilkan `502 Bad Gateway` | Periksa `docker compose logs backend` dan tujuan `proxy_pass http://backend:80` pada Nginx. |
| Backend tidak berjalan dengan FrankenPHP | Periksa konfigurasi `backend/Caddyfile`, `entrypoint`/`command` Compose, dan log backend. |
| Login gagal setelah pindah ke database container | Register pengguna baru di database Docker atau cek log/status respons API. |
| Data hilang setelah restart | Pastikan `postgres_data` digunakan dan jangan menjalankan `docker compose down -v`. |
| Deploy GHCR berstatus `Skipped` di PR | Normal: job deploy hanya berjalan saat `push` ke `main`. |

## Repository dan Dokumentasi

- **Repository GitHub:** https://github.com/parahyta/uts-pl-24-535154-SV-24161
- **Postman Collection:** [`postman/Craftory-REST-API.postman_collection.json`](postman/Craftory-REST-API.postman_collection.json)
- **GitHub Actions:** [`.github/workflows/ci.yml`](.github/workflows/ci.yml)
- **Docker Compose:** [`docker-compose.yml`](docker-compose.yml)

## Pengembang

Dikembangkan untuk tugas Ujian Tengah Semester **Konstruksi dan Evolusi Perangkat Lunak**, Program Studi D-IV Teknologi Rekayasa Perangkat Lunak, Sekolah Vokasi, Universitas Gadjah Mada, 2026.

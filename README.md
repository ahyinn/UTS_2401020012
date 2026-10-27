# Sistem Data Buku Perpustakaan (Tipe A)

Praktikum Pemrograman Web (INF11197) - Studi Kasus Modul 1-7
Nama: Muhammad Fadhila Arifin | NIM: 2401020012

Laravel + PHP + MySQL/MariaDB (PDO) + Blade tanpa CSS/JS.

## Struktur
- `kategori_buku` (1) --- (N) `buku`; FK `buku.kategori_buku_id` ON UPDATE CASCADE ON DELETE RESTRICT
- UNIQUE: `kode_buku`, `isbn`; CHECK: `tahun_terbit BETWEEN 2000 AND 2026`

## Route
| Method | URL | Fungsi |
|---|---|---|
| GET | /buku | Indeks, tambah, cari (kode/judul/isbn), filter kategori |
| POST | /buku | Create |
| GET | /buku/{id}/edit | Form edit |
| PUT | /buku/{id} | Update |
| DELETE | /buku/{id} | Delete |

## Instalasi
1. `composer install` lalu `cp .env.example .env` dan `php artisan key:generate`
2. Isi DB_USERNAME / DB_PASSWORD di .env
3. Impor SQL: `mysql -u root -p < database/sql/perpustakaan_2401020012.sql`
4. `php artisan serve` -> buka http://127.0.0.1:8000/buku

> `.env` tidak boleh di-commit (sudah ada di `.gitignore`).

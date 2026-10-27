DROP DATABASE IF EXISTS perpustakaan_2401020012;
CREATE DATABASE perpustakaan_2401020012 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan_2401020012;

CREATE TABLE kategori_buku (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE buku (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_buku CHAR(10) NOT NULL UNIQUE,
    judul VARCHAR(100) NOT NULL,
    isbn CHAR(13) NOT NULL UNIQUE,
    tahun_terbit SMALLINT UNSIGNED NOT NULL,
    kategori_buku_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_buku_tahun CHECK (tahun_terbit BETWEEN 2000 AND 2026),
    CONSTRAINT fk_buku_kategori FOREIGN KEY (kategori_buku_id)
        REFERENCES kategori_buku (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO kategori_buku (id, nama_kategori) VALUES
(1, 'Teknologi'),
(2, 'Kemaritiman');

INSERT INTO buku (kode_buku, judul, isbn, tahun_terbit, kategori_buku_id) VALUES
('BK00000001', 'Pemrograman Web Dasar',    '9786020000011', 2022, 1),
('BK00000002', 'Basis Data Praktis',       '9786020000028', 2023, 1),
('BK00000003', 'Sistem Informasi Maritim', '9786020000035', 2024, 2);

#!/usr/bin/env bash
set -e
git init
git branch -M main

git add -A
git reset -q database/sql routes/web.php resources/views/buku README.md docs
git commit -m "chore: inisialisasi proyek Laravel dan .env.example"

git add database/sql/perpustakaan_2401020012.sql
git commit -m "feat(db): tambah DDL dua tabel berelasi dan data awal"

git add routes/web.php
git commit -m "feat(routes): tambah koneksi PDO, validasi, dan fungsi statusTahunBuku()"

git add resources/views/buku/index.blade.php
git commit -m "feat(view): tambah halaman indeks dengan form tambah, cari, filter, dan tabel"

git add resources/views/buku/edit.blade.php
git commit -m "feat(view): tambah form edit untuk Update"

git add README.md docs
git commit -m "docs: tambah README, pengujian, dan catatan issue"

git remote add origin https://github.com/<username>/perpustakaan_2401020012.git
git push -u origin main
git log --oneline

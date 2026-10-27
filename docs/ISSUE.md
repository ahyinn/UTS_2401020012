# Judul Issue: Update gagal dengan error 419 Page Expired pada form edit

## Masalah
Saat menyimpan perubahan di /buku/{id}/edit, halaman menampilkan "419 Page Expired".

## Langkah Pemeriksaan
1. Memeriksa form edit di Network tab / view-source: token `_token` tidak ada.
2. Membandingkan dengan form tambah: `@csrf` ada, namun pada edit.blade.php belum ditambahkan.
3. Memeriksa `storage/logs/laravel.log` dan `php artisan route:list`: route PUT /buku/{id} terdaftar.

## Perbaikan
Menambahkan `@csrf` dan `@method('PUT')` pada form di `resources/views/buku/edit.blade.php`.

## Hasil Uji Ulang
Update data berhasil, redirect ke /buku dengan pesan "Data buku berhasil diperbarui."
Commit penutup: `Closes #1`.

> Catatan: sesuaikan isi issue dengan masalah NYATA yang Anda temui saat mengerjakan.

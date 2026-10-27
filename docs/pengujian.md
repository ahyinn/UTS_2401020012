# Skenario Pengujian (data uji siap pakai)
| No | Uji | Langkah / Input | Hasil yang diharapkan |
|---|---|---|---|
| 1 | Read | Buka /buku | 3 buku awal + nama kategori tampil |
| 2 | Create valid | BK00000004, "Jaringan Komputer", 9786020000042, 2024, Teknologi | Tersimpan, pesan berhasil |
| 3 | Create tidak valid | kode "BK1", judul "ab", ISBN "123", tahun 1999 | 4 pesan error, input lama tetap |
| 4 | Duplikat | kode BK00000001 / ISBN 9786020000011 | Ditolak, data lama utuh |
| 5 | Update | Edit BK00000003 -> judul baru | Form terisi data lama, perubahan tersimpan |
| 6 | Konflik update | Ubah ISBN BK00000003 menjadi 9786020000011 | Ditolak (ISBN milik buku lain) |
| 7 | Delete | Hapus BK00000004 | Data terhapus via form DELETE + CSRF |
| 8 | Pencarian | q = "Basis", "BK0000000", "97860200000" | Hasil sesuai pada kode/judul/ISBN |
| 9 | Filter | Pilih kategori Kemaritiman | Hanya buku kategori itu tampil |
| 10 | Error handling | Ubah DB_PASSWORD salah di .env lalu `php artisan config:clear`, buka /buku | Pesan aman singkat; detail di storage/logs/laravel.log |

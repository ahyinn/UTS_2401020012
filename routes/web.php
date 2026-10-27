<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

const PESAN_AMAN = 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.';

function statusTahunBuku(int $tahun): string
{
    if ($tahun >= 2023) {
        return 'Baru';
    }
    return 'Lama';
}

function koneksiPdo(): PDO
{
    $pdo = DB::connection()->getPdo();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function bersihkanInput($nilai): string
{
    return trim(strip_tags((string) $nilai));
}

function ambilInputBuku(Request $request): array
{
    return [
        'kode_buku'        => bersihkanInput($request->input('kode_buku')),
        'judul'            => bersihkanInput($request->input('judul')),
        'isbn'             => bersihkanInput($request->input('isbn')),
        'tahun_terbit'     => bersihkanInput($request->input('tahun_terbit')),
        'kategori_buku_id' => bersihkanInput($request->input('kategori_buku_id')),
    ];
}

function validasiBuku(array $data)
{
    return Validator::make($data, [
        'kode_buku'        => ['required', 'regex:/^[A-Za-z0-9]{10}$/'],
        'judul'            => ['required', 'string', 'min:3', 'max:100'],
        'isbn'             => ['required', 'regex:/^[0-9]{13}$/'],
        'tahun_terbit'     => ['required', 'integer', 'between:2000,2026'],
        'kategori_buku_id' => ['required', 'integer'],
    ], [
        'kode_buku.required'        => 'Kode buku wajib diisi.',
        'kode_buku.regex'           => 'Kode buku harus tepat 10 karakter alfanumerik.',
        'judul.required'            => 'Judul wajib diisi.',
        'judul.min'                 => 'Judul minimal 3 karakter.',
        'judul.max'                 => 'Judul maksimal 100 karakter.',
        'isbn.required'             => 'ISBN wajib diisi.',
        'isbn.regex'                => 'ISBN harus tepat 13 digit angka.',
        'tahun_terbit.required'     => 'Tahun terbit wajib diisi.',
        'tahun_terbit.integer'      => 'Tahun terbit harus berupa angka.',
        'tahun_terbit.between'      => 'Tahun terbit harus antara 2000 dan 2026.',
        'kategori_buku_id.required' => 'Kategori wajib dipilih.',
        'kategori_buku_id.integer'  => 'Kategori tidak valid.',
    ]);
}

function daftarKategori(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT id, nama_kategori FROM kategori_buku ORDER BY nama_kategori');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function kategoriAda(PDO $pdo, $id): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM kategori_buku WHERE id = :id');
    $stmt->execute([':id' => (int) $id]);
    return (int) $stmt->fetchColumn() > 0;
}

function cekDuplikatBuku(PDO $pdo, array $data, ?int $kecualiId = null): array
{
    $error = [];
    $sql = 'SELECT kode_buku, isbn FROM buku WHERE (kode_buku = :kode OR isbn = :isbn)';
    $param = [':kode' => $data['kode_buku'], ':isbn' => $data['isbn']];
    if ($kecualiId !== null) {
        $sql .= ' AND id <> :id';
        $param[':id'] = $kecualiId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($param);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (strcasecmp($row['kode_buku'], $data['kode_buku']) === 0) {
            $error['kode_buku'] = 'Kode buku sudah digunakan.';
        }
        if ($row['isbn'] === $data['isbn']) {
            $error['isbn'] = 'ISBN sudah digunakan.';
        }
    }
    return $error;
}

function semuaErrorBuku(PDO $pdo, array $data, ?int $kecualiId = null): array
{
    $errors = array_map(fn ($m) => $m[0], validasiBuku($data)->errors()->toArray());

    if (!isset($errors['kategori_buku_id']) && !kategoriAda($pdo, $data['kategori_buku_id'])) {
        $errors['kategori_buku_id'] = 'Kategori tidak tersedia.';
    }
    return $errors + cekDuplikatBuku($pdo, $data, $kecualiId);
}

Route::get('/', fn () => redirect('/buku'));

Route::get('/buku', function (Request $request) {
    $q = bersihkanInput($request->query('q'));
    $kategori = bersihkanInput($request->query('kategori_buku_id'));
    $bukuList = [];
    $kategoriList = [];

    try {
        $pdo = koneksiPdo();
        $kategoriList = daftarKategori($pdo);

        $sql = 'SELECT b.id, b.kode_buku, b.judul, b.isbn, b.tahun_terbit, k.nama_kategori
                FROM buku b
                INNER JOIN kategori_buku k ON k.id = b.kategori_buku_id
                WHERE 1 = 1';
        $param = [];

        if ($q !== '') {
            $sql .= ' AND (b.kode_buku LIKE :q1 OR b.judul LIKE :q2 OR b.isbn LIKE :q3)';
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $param[':q1'] = $like;
            $param[':q2'] = $like;
            $param[':q3'] = $like;
        }
        if ($kategori !== '' && ctype_digit($kategori)) {
            $sql .= ' AND b.kategori_buku_id = :kategori';
            $param[':kategori'] = (int) $kategori;
        }
        $sql .= ' ORDER BY b.id ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($param);
        $bukuList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        report($e);
        session()->now('error', PESAN_AMAN);
    }

    return view('buku.index', compact('bukuList', 'kategoriList', 'q', 'kategori'));
})->name('buku.index');

Route::post('/buku', function (Request $request) {
    $data = ambilInputBuku($request);

    try {
        $pdo = koneksiPdo();
        $errors = semuaErrorBuku($pdo, $data);

        if (!empty($errors)) {
            return back()->withInput($data)->withErrors($errors);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO buku (kode_buku, judul, isbn, tahun_terbit, kategori_buku_id)
             VALUES (:kode, :judul, :isbn, :tahun, :kategori)'
        );
        $stmt->execute([
            ':kode'     => $data['kode_buku'],
            ':judul'    => $data['judul'],
            ':isbn'     => $data['isbn'],
            ':tahun'    => (int) $data['tahun_terbit'],
            ':kategori' => (int) $data['kategori_buku_id'],
        ]);
    } catch (Throwable $e) {
        report($e);
        return back()->withInput($data)->with('error', PESAN_AMAN);
    }

    return redirect('/buku')->with('success', 'Data buku berhasil ditambahkan.');
})->name('buku.store');

Route::get('/buku/{id}/edit', function ($id) {
    try {
        $pdo = koneksiPdo();
        $stmt = $pdo->prepare(
            'SELECT id, kode_buku, judul, isbn, tahun_terbit, kategori_buku_id
             FROM buku WHERE id = :id'
        );
        $stmt->execute([':id' => (int) $id]);
        $buku = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$buku) {
            return redirect('/buku')->with('error', 'Data buku tidak ditemukan.');
        }
        $kategoriList = daftarKategori($pdo);
    } catch (Throwable $e) {
        report($e);
        return redirect('/buku')->with('error', PESAN_AMAN);
    }

    return view('buku.edit', compact('buku', 'kategoriList'));
})->whereNumber('id')->name('buku.edit');

Route::put('/buku/{id}', function (Request $request, $id) {
    $id = (int) $id;
    $data = ambilInputBuku($request);

    try {
        $pdo = koneksiPdo();

        $cek = $pdo->prepare('SELECT COUNT(*) FROM buku WHERE id = :id');
        $cek->execute([':id' => $id]);
        if ((int) $cek->fetchColumn() === 0) {
            return redirect('/buku')->with('error', 'Data buku tidak ditemukan.');
        }

        $errors = semuaErrorBuku($pdo, $data, $id);
        if (!empty($errors)) {
            return back()->withInput($data)->withErrors($errors);
        }

        $stmt = $pdo->prepare(
            'UPDATE buku
             SET kode_buku = :kode, judul = :judul, isbn = :isbn,
                 tahun_terbit = :tahun, kategori_buku_id = :kategori
             WHERE id = :id'
        );
        $stmt->execute([
            ':kode'     => $data['kode_buku'],
            ':judul'    => $data['judul'],
            ':isbn'     => $data['isbn'],
            ':tahun'    => (int) $data['tahun_terbit'],
            ':kategori' => (int) $data['kategori_buku_id'],
            ':id'       => $id,
        ]);
    } catch (Throwable $e) {
        report($e);
        return back()->withInput($data)->with('error', PESAN_AMAN);
    }

    return redirect('/buku')->with('success', 'Data buku berhasil diperbarui.');
})->whereNumber('id')->name('buku.update');

Route::delete('/buku/{id}', function ($id) {
    try {
        $stmt = koneksiPdo()->prepare('DELETE FROM buku WHERE id = :id');
        $stmt->execute([':id' => (int) $id]);
        $hapus = $stmt->rowCount() > 0;
    } catch (Throwable $e) {
        report($e);
        return redirect('/buku')->with('error', PESAN_AMAN);
    }

    if ($hapus) {
        return redirect('/buku')->with('success', 'Data buku berhasil dihapus.');
    }
    return redirect('/buku')->with('error', 'Data buku tidak ditemukan.');
})->whereNumber('id')->name('buku.destroy');

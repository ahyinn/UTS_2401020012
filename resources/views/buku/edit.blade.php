<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku</title>
</head>
<body>
    <h1>Edit Buku</h1>

    @if (session('error'))
        <p><strong>Gagal:</strong> {{ session('error') }}</p>
    @endif
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/buku/{{ $buku['id'] }}" method="POST">
        @csrf
        @method('PUT')
        <p>
            <label>Kode Buku (10 karakter)</label><br>
            <input type="text" name="kode_buku" value="{{ old('kode_buku', $buku['kode_buku']) }}">
        </p>
        <p>
            <label>Judul (3-100 karakter)</label><br>
            <input type="text" name="judul" value="{{ old('judul', $buku['judul']) }}">
        </p>
        <p>
            <label>ISBN (13 digit)</label><br>
            <input type="text" name="isbn" value="{{ old('isbn', $buku['isbn']) }}">
        </p>
        <p>
            <label>Tahun Terbit (2000-2026)</label><br>
            <input type="text" name="tahun_terbit" value="{{ old('tahun_terbit', $buku['tahun_terbit']) }}">
        </p>
        <p>
            <label>Kategori</label><br>
            <select name="kategori_buku_id">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriList as $k)
                    <option value="{{ $k['id'] }}"
                        @if (old('kategori_buku_id', $buku['kategori_buku_id']) == $k['id']) selected @endif>
                        {{ $k['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Simpan Perubahan</button>
        <a href="/buku">Batal</a>
    </form>
</body>
</html>
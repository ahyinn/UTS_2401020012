<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku Perpustakaan</title>
</head>
<body>
    <h1>Sistem Data Buku Perpustakaan</h1>
    <p>Muhammad Fadhila Arifin - 2401020012</p>

    @if (session('success'))
        <p><strong>Berhasil:</strong> {{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p><strong>Gagal:</strong> {{ session('error') }}</p>
    @endif

    <h2>Tambah Buku</h2>
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    @endif
    <form action="/buku" method="POST">
        @csrf
        <p>
            <label>Kode Buku (10 karakter)</label><br>
            <input type="text" name="kode_buku" value="{{ old('kode_buku') }}">
        </p>
        <p>
            <label>Judul (3-100 karakter)</label><br>
            <input type="text" name="judul" value="{{ old('judul') }}">
        </p>
        <p>
            <label>ISBN (13 digit)</label><br>
            <input type="text" name="isbn" value="{{ old('isbn') }}">
        </p>
        <p>
            <label>Tahun Terbit (2000-2026)</label><br>
            <input type="text" name="tahun_terbit" value="{{ old('tahun_terbit') }}">
        </p>
        <p>
            <label>Kategori</label><br>
            <select name="kategori_buku_id">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriList as $k)
                    <option value="{{ $k['id'] }}" @if (old('kategori_buku_id') == $k['id']) selected @endif>
                        {{ $k['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Simpan</button>
    </form>

    <h2>Cari dan Filter</h2>
    <form action="/buku" method="GET">
        <input type="text" name="q" value="{{ $q }}" placeholder="Kode, judul, atau ISBN">
        <select name="kategori_buku_id">
            <option value="">-- Semua Kategori --</option>
            @foreach ($kategoriList as $k)
                <option value="{{ $k['id'] }}" @if ($kategori == $k['id']) selected @endif>
                    {{ $k['nama_kategori'] }}
                </option>
            @endforeach
        </select>
        <button type="submit">Cari</button>
        <a href="/buku">Reset</a>
    </form>

    <h2>Daftar Buku</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Judul</th>
                <th>ISBN</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bukuList as $i => $b)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $b['kode_buku'] }}</td>
                    <td>{{ $b['judul'] }}</td>
                    <td>{{ $b['isbn'] }}</td>
                    <td>{{ $b['tahun_terbit'] }}</td>
                    <td>
                        @if (statusTahunBuku((int) $b['tahun_terbit']) === 'Baru')
                            Baru
                        @else
                            Lama
                        @endif
                    </td>
                    <td>{{ $b['nama_kategori'] }}</td>
                    <td>
                        <a href="/buku/{{ $b['id'] }}/edit">Edit</a>
                        <form action="/buku/{{ $b['id'] }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Data tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
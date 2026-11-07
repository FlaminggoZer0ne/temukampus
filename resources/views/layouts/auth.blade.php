<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body>
<div class="tk-auth">
    <section class="tk-auth-hero">
        <a href="{{ route('beranda') }}" class="tk-logo">
            <x-logo :mark="52" :text="30" />
        </a>

        <div class="copy">
            <h1 class="tk-display">Barang hilang di kampus? Temuin yuk</h1>
            <p>Sistem informasi Lost &amp; Found untuk mahasiswa, petugas keamanan, dan admin kampus.</p>
            <div class="tk-features">
                <div class="tk-feature"><span class="ic-wrap"><x-icon name="camera" /></span> Lapor barang hilang atau temuan lengkap dengan foto</div>
                <div class="tk-feature"><span class="ic-wrap"><x-icon name="search" /></span> Cari barang berdasarkan kategori dan lokasi</div>
                <div class="tk-feature"><span class="ic-wrap"><x-icon name="shield" /></span> Klaim diverifikasi petugas sebelum barang diserahkan</div>
            </div>
        </div>

        <div class="foot">Tugas Besar Praktikum Pemrograman Web · Telkom University</div>
    </section>

    <section class="tk-auth-form">
        @yield('content')
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

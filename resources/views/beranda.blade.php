@extends('layouts.public')
@section('judul', 'Beranda')

@section('content')
<section class="tk-hero">
    <div class="tk-container">
        <h1 class="tk-display">Barang hilang di kampus? Temuin yuk</h1>
        <p>Sistem informasi Lost &amp; Found untuk mahasiswa, petugas keamanan, dan admin kampus.</p>
        @auth
            <a href="{{ route('laporan.index') }}" class="tk-btn tk-btn-primary">Cari barang</a>
        @else
            <a href="{{ route('login') }}" class="tk-btn tk-btn-primary">Masuk</a>
            <a href="{{ route('register') }}" class="tk-btn tk-btn-outline ms-2" style="color: var(--text-inverse); border-color: var(--navy-soft)">Daftar sebagai mahasiswa</a>
        @endauth
    </div>
</section>

<section class="tk-section">
    <div class="tk-container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="tk-heading" style="font-size: 24px">Barang temuan terbaru</h2>
                <p class="tk-muted mb-0">Sudah diverifikasi petugas dan siap diklaim pemiliknya.</p>
            </div>
            @auth <a href="{{ route('laporan.index') }}" class="fw-semibold">Lihat semua</a> @endauth
        </div>

        @if ($laporan->isEmpty())
            <div class="tk-card tk-empty">Belum ada barang temuan yang tampil.</div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @foreach ($laporan as $item)
                    <div class="col"><x-item-card :laporan="$item" /></div>
                @endforeach
            </div>
        @endif

        @guest
            <p class="text-center tk-muted mt-4 mb-0">Masuk terlebih dahulu untuk melihat detail dan mengajukan klaim.</p>
        @endguest
    </div>
</section>
@endsection

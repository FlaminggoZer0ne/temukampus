@extends('layouts.app')
@section('judul', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan aktivitas Lost & Found, ' . now()->translatedFormat('F Y'))

@section('content')
<div class="row g-4 mb-4">
    @foreach ([
        ['package',   'tone-orange', $total['laporan'],      'Total laporan'],
        ['clipboard', 'tone-blue',   $total['menunggu'],     'Menunggu verifikasi'],
        ['check',     'tone-green',  $total['dikembalikan'], 'Barang dikembalikan'],
        ['users',     'tone-gray',   $total['user'],         'Pengguna terdaftar'],
    ] as [$ikon, $tone, $angka, $label])
        <div class="col-sm-6 col-xl-3">
            <div class="tk-card tk-stat">
                <span class="icon-wrap {{ $tone }}"><x-icon :name="$ikon" :size="22" /></span>
                <span class="num">{{ number_format($angka, 0, ',', '.') }}</span>
                <span class="cap">{{ $label }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="tk-card h-100">
            <div class="tk-card-head">
                <h3 class="tk-subtitle">Laporan per bulan</h3>
                <span class="tk-legend"><i></i> Jumlah laporan</span>
            </div>
            <div class="tk-bars" id="chart-bulanan" data-url="{{ route('api.statistik') }}">
                <p class="tk-muted mb-0">Memuat…</p>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="tk-card h-100">
            <div class="tk-card-head"><h3 class="tk-subtitle">Laporan per kategori</h3></div>
            <div id="chart-kategori"><p class="tk-muted mb-0">Memuat…</p></div>
        </div>
    </div>
</div>

<div class="tk-card">
    <div class="tk-card-head">
        <h3 class="tk-subtitle">Laporan terbaru</h3>
        <a href="{{ route('admin.laporan.index') }}" class="fw-semibold">Lihat semua</a>
    </div>
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Jenis</th><th>Lokasi</th><th>Pelapor</th><th>Tanggal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($terbaru as $l)
                    <tr>
                        <td class="strong">{{ $l->nama_barang }}</td>
                        <td><x-badge :value="$l->jenis" /></td>
                        <td>{{ $l->lokasi->nama_lokasi }}</td>
                        <td>{{ $l->user->nama }}</td>
                        <td>{{ $l->tanggal_kejadian->translatedFormat('j M Y') }}</td>
                        <td><x-badge :value="$l->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('judul', 'Dashboard Petugas')
@section('heading', 'Dashboard')
@section('subheading', 'Antrean verifikasi hari ini')

@section('actions')
    <a href="{{ route('laporan.create') }}" class="tk-btn tk-btn-primary">+ Catat barang</a>
@endsection

@section('content')
<div class="row g-4 mb-4">
    @foreach ([
        ['clipboard', 'tone-orange', $total['laporan_menunggu'], 'Laporan menunggu verifikasi', route('petugas.laporan')],
        ['check',     'tone-blue',   $total['klaim_menunggu'],   'Klaim menunggu verifikasi',   route('petugas.klaim')],
        ['package',   'tone-gray',   $total['siap_serah'],       'Siap serah-terima',            route('petugas.serahterima')],
        ['check',     'tone-green',  $total['dikembalikan'],     'Barang dikembalikan',          null],
    ] as [$ikon, $tone, $angka, $label, $href])
        <div class="col-sm-6 col-xl-3">
            <div class="tk-card tk-stat position-relative">
                <span class="icon-wrap {{ $tone }}"><x-icon :name="$ikon" :size="22" /></span>
                <span class="num">{{ $angka }}</span>
                @if ($href)
                    <a href="{{ $href }}" class="cap stretched-link text-decoration-none" style="color: var(--text-muted)">{{ $label }}</a>
                @else
                    <span class="cap">{{ $label }}</span>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="tk-card h-100">
            <div class="tk-card-head">
                <h3 class="tk-subtitle">Laporan terbaru menunggu</h3>
                <a href="{{ route('petugas.laporan') }}" class="fw-semibold">Verifikasi</a>
            </div>
            <div class="tk-table-wrap">
                <table class="tk-table" style="min-width: 0">
                    <thead><tr><th>Barang</th><th>Jenis</th><th>Pelapor</th></tr></thead>
                    <tbody>
                        @forelse ($laporanMenunggu as $l)
                            <tr>
                                <td class="strong">{{ $l->nama_barang }}</td>
                                <td><x-badge :value="$l->jenis" /></td>
                                <td>{{ $l->user->nama }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty">Tidak ada laporan yang menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="tk-card h-100">
            <div class="tk-card-head">
                <h3 class="tk-subtitle">Klaim terbaru menunggu</h3>
                <a href="{{ route('petugas.klaim') }}" class="fw-semibold">Verifikasi</a>
            </div>
            <div class="tk-table-wrap">
                <table class="tk-table" style="min-width: 0">
                    <thead><tr><th>Barang</th><th>Pengklaim</th><th>Diajukan</th></tr></thead>
                    <tbody>
                        @forelse ($klaimMenunggu as $k)
                            <tr>
                                <td class="strong">{{ $k->laporan->nama_barang }}</td>
                                <td>{{ $k->user->nama }}</td>
                                <td>{{ $k->created_at->translatedFormat('j M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty">Tidak ada klaim yang menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

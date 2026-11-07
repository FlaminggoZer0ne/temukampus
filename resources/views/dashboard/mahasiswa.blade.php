@extends('layouts.app')
@section('judul', 'Beranda')
@section('heading', 'Halo, ' . explode(' ', auth()->user()->nama)[0])
@section('subheading', 'Pantau laporan dan klaim milik Anda')

@section('actions')
    <a href="{{ route('laporan.create') }}" class="tk-btn tk-btn-primary">+ Lapor barang</a>
@endsection

@section('content')
@php
    $hitung = fn ($c, $s) => $c->where('status', $s)->count();
@endphp
<div class="row g-4 mb-4">
    @foreach ([
        ['package',   'tone-orange', $laporan->count(),                 'Total laporan saya'],
        ['clipboard', 'tone-blue',   $hitung($laporan, 'menunggu'),     'Laporan menunggu verifikasi'],
        ['check',     'tone-green',  $hitung($laporan, 'dikembalikan'), 'Barang kembali'],
        ['search',    'tone-gray',   $hitung($klaim, 'menunggu'),       'Klaim diproses'],
    ] as [$ikon, $tone, $angka, $label])
        <div class="col-sm-6 col-xl-3">
            <div class="tk-card tk-stat">
                <span class="icon-wrap {{ $tone }}"><x-icon :name="$ikon" :size="22" /></span>
                <span class="num">{{ $angka }}</span>
                <span class="cap">{{ $label }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="tk-card h-100">
            <div class="tk-card-head">
                <h3 class="tk-subtitle">Laporan saya</h3>
                <a href="{{ route('laporan.saya') }}" class="fw-semibold">Lihat semua</a>
            </div>
            <div class="tk-table-wrap">
                <table class="tk-table" style="min-width: 0">
                    <thead><tr><th>Barang</th><th>Jenis</th><th>Tanggal</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($laporan->take(5) as $l)
                            <tr>
                                <td class="strong"><a href="{{ route('laporan.show', $l) }}" class="text-reset">{{ $l->nama_barang }}</a></td>
                                <td><x-badge :value="$l->jenis" /></td>
                                <td>{{ $l->tanggal_kejadian->translatedFormat('j M Y') }}</td>
                                <td><x-badge :value="$l->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Belum ada laporan. <a href="{{ route('laporan.create') }}">Buat laporan pertama</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="tk-card h-100">
            <div class="tk-card-head">
                <h3 class="tk-subtitle">Klaim saya</h3>
                <a href="{{ route('klaim.index') }}" class="fw-semibold">Lihat semua</a>
            </div>
            <div class="tk-table-wrap">
                <table class="tk-table" style="min-width: 0">
                    <thead><tr><th>Barang</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($klaim->take(5) as $k)
                            <tr>
                                <td class="strong">{{ $k->laporan->nama_barang }}</td>
                                <td><x-badge :value="$k->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty">Belum ada klaim. <a href="{{ route('laporan.index') }}">Cari barang</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

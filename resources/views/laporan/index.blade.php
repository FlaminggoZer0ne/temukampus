@extends('layouts.app')
@section('judul', 'Cari Barang')
@section('heading', 'Cari barang')
@section('subheading', auth()->user()->isMahasiswa() ? 'Semua barang yang sudah diverifikasi petugas' : 'Semua laporan barang hilang dan temuan')

@section('actions')
    @if (!auth()->user()->isAdmin())
        <a href="{{ route('laporan.create') }}" class="tk-btn tk-btn-primary">+ Lapor barang</a>
    @endif
@endsection

@section('content')
<div class="tk-filter">
    <label class="tk-search">
        <x-icon name="search" :size="18" />
        <input type="search" id="cari" placeholder="Cari nama barang, mis. dompet, kunci, KTM…" autocomplete="off">
    </label>

    <select id="filter-kategori" class="form-select tk-select" style="width: 190px">
        <option value="">Semua kategori</option>
        @foreach ($kategori as $k)<option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>@endforeach
    </select>

    <select id="filter-lokasi" class="form-select tk-select" style="width: 190px">
        <option value="">Semua lokasi</option>
        @foreach ($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
    </select>

    @unless (auth()->user()->isMahasiswa())
        <select id="filter-status" class="form-select tk-select" style="width: 170px">
            <option value="">Semua status</option>
            @foreach (['menunggu', 'aktif', 'diklaim', 'dikembalikan', 'ditolak'] as $s)
                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    @endunless

    <div class="tk-segmented" id="filter-jenis">
        <button type="button" class="active" data-jenis="">Semua</button>
        <button type="button" data-jenis="hilang">Hilang</button>
        <button type="button" data-jenis="temuan">Temuan</button>
    </div>
</div>

<div class="tk-result-info">
    <span id="info-hasil">Memuat…</span>
    <strong>Urutkan: Terbaru</strong>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-4" id="hasil"
     data-url="{{ route('api.laporan.search') }}"
     data-icons="{{ asset('img/icons') }}"
     data-detail="{{ url('/laporan') }}"></div>
@endsection

@extends('layouts.app')
@section('judul', $laporan->nama_barang)
@section('heading', 'Detail barang')
@section('subheading', 'Dilaporkan ' . $laporan->created_at->translatedFormat('j F Y'))

@section('actions')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('laporan.index') }}" class="tk-btn tk-btn-outline">&larr; Kembali</a>
@endsection

@section('content')
@php
    $user = auth()->user();
    $bisaKlaim = $user->isMahasiswa() && $laporan->jenis === 'temuan' && $laporan->status === 'aktif'
        && $laporan->user_id !== $user->id && !$sudahKlaim;
@endphp
<div class="row g-4">
    <div class="col-lg-6">
        <div class="tk-photo tk-detail-photo">
            @if ($laporan->foto_url)
                <img src="{{ $laporan->foto_url }}" alt="{{ $laporan->nama_barang }}">
            @else
                <x-icon name="image" :size="48" />
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="tk-card h-100 d-flex flex-column gap-3">
            <div class="d-flex gap-2">
                <x-badge :value="$laporan->jenis" />
                <x-badge :value="$laporan->status" />
            </div>

            <h2 class="tk-heading">{{ $laporan->nama_barang }}</h2>

            <div class="tk-detail-list">
                <div><x-icon name="map-pin" :size="16" /> <b>{{ $laporan->lokasi->nama_lokasi }}</b></div>
                <div><x-icon name="calendar" :size="16" /> <b>{{ $laporan->tanggal_kejadian->translatedFormat('j F Y') }}</b></div>
                <div><x-icon name="tag" :size="16" /> <b>{{ $laporan->kategori->nama_kategori }}</b></div>
                <div><x-icon name="users" :size="16" /> <span>Pelapor:</span> <b>{{ $laporan->user->nama }}</b></div>
            </div>

            <div>
                <div class="tk-label mb-1">Deskripsi</div>
                <p class="mb-0" style="white-space: pre-line">{{ $laporan->deskripsi }}</p>
            </div>

            <div class="mt-auto d-flex flex-wrap gap-2 pt-2">
                @if ($bisaKlaim)
                    <a href="{{ route('klaim.create', $laporan) }}" class="tk-btn tk-btn-primary">Klaim barang ini</a>
                @elseif ($sudahKlaim)
                    <span class="tk-badge orange">Anda sudah mengajukan klaim</span>
                @endif

                @if ($laporan->user_id === $user->id && ($user->isPetugas() || $laporan->status === 'menunggu') && !$user->isAdmin())
                    <a href="{{ route('laporan.edit', $laporan) }}" class="tk-btn tk-btn-outline">Ubah laporan</a>
                @endif

                @if ($user->isAdmin())
                    <form method="POST" action="{{ route('admin.laporan.destroy', $laporan) }}" data-confirm="Hapus laporan ini secara permanen?">
                        @csrf @method('DELETE')
                        <button type="submit" class="tk-btn tk-btn-outline" style="color: var(--red)">Hapus laporan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

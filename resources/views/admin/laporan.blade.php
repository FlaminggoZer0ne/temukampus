@extends('layouts.app')
@section('judul', 'Semua Laporan')
@section('heading', 'Semua laporan')
@section('subheading', 'Pantau dan hapus laporan yang tidak valid atau spam')

@section('content')
@php
    $filter = request('status');
    $tabs = ['' => 'Semua', 'menunggu' => 'Menunggu', 'aktif' => 'Aktif', 'diklaim' => 'Diklaim', 'dikembalikan' => 'Dikembalikan', 'ditolak' => 'Ditolak'];
@endphp

<div class="tk-segmented mb-4 flex-wrap">
    @foreach ($tabs as $v => $t)
        <a href="{{ route('admin.laporan.index', $v ? ['status' => $v] : []) }}" @class(['active' => (string) $filter === (string) $v])>{{ $t }}</a>
    @endforeach
</div>

<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Jenis</th><th>Lokasi</th><th>Pelapor</th><th>Tanggal</th><th>Status</th><th style="width:80px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($laporan as $l)
                    <tr>
                        <td class="strong"><a href="{{ route('laporan.show', $l) }}" class="text-reset">{{ $l->nama_barang }}</a></td>
                        <td><x-badge :value="$l->jenis" /></td>
                        <td>{{ $l->lokasi->nama_lokasi }}</td>
                        <td>{{ $l->user->nama }}</td>
                        <td>{{ $l->tanggal_kejadian->translatedFormat('j M Y') }}</td>
                        <td><x-badge :value="$l->status" /></td>
                        <td>
                            <form method="POST" action="{{ route('admin.laporan.destroy', $l) }}"
                                  data-confirm="Hapus laporan &quot;{{ $l->nama_barang }}&quot; secara permanen?">
                                @csrf @method('DELETE')
                                <button type="submit" class="tk-icon-btn trash" title="Hapus"><x-icon name="trash" :size="16" /></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($laporan->hasPages())
        <div class="mt-3 d-flex justify-content-end">{{ $laporan->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection

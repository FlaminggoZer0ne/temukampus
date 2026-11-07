@extends('layouts.app')
@section('judul', 'Laporan Saya')
@section('heading', 'Laporan saya')
@section('subheading', 'Pantau status laporan yang Anda buat')

@section('actions')
    <a href="{{ route('laporan.create') }}" class="tk-btn tk-btn-primary">+ Lapor barang</a>
@endsection

@section('content')
<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Jenis</th><th>Lokasi</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($laporan as $l)
                    @php
                        $bolehUbah = auth()->user()->isPetugas() || $l->status === 'menunggu';
                    @endphp
                    <tr>
                        <td class="strong"><a href="{{ route('laporan.show', $l) }}" class="text-reset">{{ $l->nama_barang }}</a></td>
                        <td><x-badge :value="$l->jenis" /></td>
                        <td>{{ $l->lokasi->nama_lokasi }}</td>
                        <td>{{ $l->tanggal_kejadian->translatedFormat('j M Y') }}</td>
                        <td><x-badge :value="$l->status" /></td>
                        <td>
                            <div class="actions">
                                @if ($bolehUbah)
                                    <a href="{{ route('laporan.edit', $l) }}" class="tk-icon-btn edit" title="Ubah"><x-icon name="edit" :size="16" /></a>
                                    <form method="POST" action="{{ route('laporan.destroy', $l) }}" data-confirm="Hapus laporan &quot;{{ $l->nama_barang }}&quot;?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tk-icon-btn trash" title="Hapus"><x-icon name="trash" :size="16" /></button>
                                    </form>
                                @else
                                    <span class="tk-muted tk-caption">Sudah diproses</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada laporan. <a href="{{ route('laporan.create') }}">Buat laporan</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

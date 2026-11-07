@extends('layouts.app')
@section('judul', 'Verifikasi Laporan')
@section('heading', 'Verifikasi laporan')
@section('subheading', 'Periksa laporan baru sebelum tampil di daftar publik')

@section('content')
<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Jenis</th><th>Kategori</th><th>Lokasi</th><th>Pelapor</th><th>Tanggal</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($laporan as $l)
                    <tr>
                        <td class="strong"><a href="{{ route('laporan.show', $l) }}" class="text-reset">{{ $l->nama_barang }}</a></td>
                        <td><x-badge :value="$l->jenis" /></td>
                        <td>{{ $l->kategori->nama_kategori }}</td>
                        <td>{{ $l->lokasi->nama_lokasi }}</td>
                        <td>{{ $l->user->nama }}</td>
                        <td>{{ $l->tanggal_kejadian->translatedFormat('j M Y') }}</td>
                        <td>
                            <div class="actions">
                                <button type="button" class="tk-btn tk-btn-primary tk-btn-sm btn-status-laporan"
                                        data-url="{{ route('api.laporan.status', $l) }}" data-status="aktif"
                                        data-kosong="Semua laporan sudah diverifikasi.">Setujui</button>
                                <button type="button" class="tk-btn tk-btn-outline tk-btn-sm btn-status-laporan"
                                        data-url="{{ route('api.laporan.status', $l) }}" data-status="ditolak"
                                        data-confirm="Tolak laporan &quot;{{ $l->nama_barang }}&quot;?"
                                        data-kosong="Semua laporan sudah diverifikasi.">Tolak</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Semua laporan sudah diverifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

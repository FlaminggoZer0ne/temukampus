@extends('layouts.app')
@section('judul', 'Klaim Saya')
@section('heading', 'Klaim saya')
@section('subheading', 'Status klaim yang Anda ajukan')

@section('actions')
    <a href="{{ route('laporan.index') }}" class="tk-btn tk-btn-primary">Cari barang</a>
@endsection

@section('content')
<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Lokasi</th><th>Diajukan</th><th>Status</th><th>Catatan petugas</th></tr>
            </thead>
            <tbody>
                @forelse ($klaim as $k)
                    <tr>
                        <td class="strong"><a href="{{ route('laporan.show', $k->laporan) }}" class="text-reset">{{ $k->laporan->nama_barang }}</a></td>
                        <td>{{ $k->laporan->lokasi->nama_lokasi }}</td>
                        <td>{{ $k->created_at->translatedFormat('j M Y') }}</td>
                        <td><x-badge :value="$k->status" /></td>
                        <td class="tk-muted">
                            {{ $k->catatan_petugas ?: '—' }}
                            @if ($k->status === 'disetujui' && $k->laporan->status === 'diklaim')
                                <div class="tk-caption" style="color: var(--green)">Silakan ambil barang di pos petugas.</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada klaim. <a href="{{ route('laporan.index') }}">Cari barang temuan</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

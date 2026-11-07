@extends('layouts.app')
@section('judul', 'Serah-terima')
@section('heading', 'Serah-terima')
@section('subheading', 'Klaim yang sudah disetujui dan menunggu penyerahan barang')

@section('content')
<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Barang</th><th>Lokasi temuan</th><th>Pemilik</th><th>No. HP</th><th>Catatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($klaim as $k)
                    <tr>
                        <td class="strong">{{ $k->laporan->nama_barang }}</td>
                        <td>{{ $k->laporan->lokasi->nama_lokasi }}</td>
                        <td>{{ $k->user->nama }}<div class="tk-caption tk-muted">{{ $k->user->nim_nip }}</div></td>
                        <td>{{ $k->user->no_hp ?: '—' }}</td>
                        <td class="tk-muted">{{ $k->catatan_petugas ?: '—' }}</td>
                        <td>
                            <button type="button" class="tk-btn tk-btn-primary tk-btn-sm btn-status-laporan"
                                    data-url="{{ route('api.laporan.status', $k->laporan) }}" data-status="dikembalikan"
                                    data-confirm="Tandai &quot;{{ $k->laporan->nama_barang }}&quot; sudah diserahkan ke {{ $k->user->nama }}?"
                                    data-kosong="Tidak ada barang yang menunggu serah-terima.">Tandai dikembalikan</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada barang yang menunggu serah-terima.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

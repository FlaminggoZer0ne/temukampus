@props(['value'])
@php
    // Jenis: hilang = merah, temuan = biru.
    // Status laporan/klaim: menunggu = oranye, aktif/disetujui = hijau, diklaim = biru,
    // dikembalikan = abu, ditolak = merah.
    $tone = [
        'hilang' => 'red', 'temuan' => 'blue',
        'menunggu' => 'orange', 'aktif' => 'green', 'disetujui' => 'green',
        'diklaim' => 'blue', 'dikembalikan' => 'gray', 'ditolak' => 'red',
    ][$value] ?? 'gray';
@endphp
<span {{ $attributes->class(['tk-badge', $tone]) }}>{{ ucfirst($value) }}</span>

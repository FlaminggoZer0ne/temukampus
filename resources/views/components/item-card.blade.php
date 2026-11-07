@props(['laporan'])
{{-- Kartu barang (ItemCard/Grid di Figma). --}}
<a href="{{ route('laporan.show', $laporan) }}" class="tk-item">
    <div class="tk-photo">
        @if ($laporan->foto_url)
            <img src="{{ $laporan->foto_url }}" alt="{{ $laporan->nama_barang }}">
        @else
            <x-icon name="image" :size="28" />
        @endif
    </div>
    <div class="tk-item-body">
        <x-badge :value="$laporan->jenis" />
        <span class="nama">{{ $laporan->nama_barang }}</span>
        <span class="tk-item-meta"><x-icon name="map-pin" :size="14" /> {{ $laporan->lokasi->nama_lokasi }}</span>
        <span class="tk-item-meta"><x-icon name="calendar" :size="14" /> {{ $laporan->tanggal_kejadian->translatedFormat('j M Y') }}</span>
    </div>
</a>

@extends('layouts.app')
@section('judul', 'Klaim Barang')
@section('heading', 'Klaim barang')
@section('subheading', 'Buktikan bahwa barang ini milik Anda')

@php $sembunyikanError = true; @endphp

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="tk-card d-flex gap-3 align-items-center">
            <div class="tk-photo flex-shrink-0" style="width: 72px; height: 72px">
                @if ($laporan->foto_url)
                    <img src="{{ $laporan->foto_url }}" alt="">
                @else
                    <x-icon name="image" :size="28" />
                @endif
            </div>
            <div>
                <div class="tk-subtitle">{{ $laporan->nama_barang }}</div>
                <div class="tk-caption tk-muted">Temuan · {{ $laporan->lokasi->nama_lokasi }} · {{ $laporan->tanggal_kejadian->translatedFormat('j M Y') }}</div>
            </div>
        </div>
        <p class="tk-muted mt-3 mb-0">
            Petugas akan mencocokkan bukti Anda sebelum barang diserahkan. Jelaskan ciri khusus yang
            hanya diketahui pemilik, misalnya isi tas, goresan, atau stiker.
        </p>
    </div>

    <div class="col-lg-7">
        <div class="tk-card">
            <form method="POST" action="{{ route('klaim.store', $laporan) }}" enctype="multipart/form-data" class="d-flex flex-column gap-3" novalidate>
                @csrf
                <div class="tk-field">
                    <label for="bukti_kepemilikan">Bukti kepemilikan</label>
                    <textarea id="bukti_kepemilikan" name="bukti_kepemilikan" class="form-control @error('bukti_kepemilikan') is-invalid @enderror"
                              placeholder="Contoh: Gantungan kunci berbentuk bola basket warna oranye, ada 2 kunci." required>{{ old('bukti_kepemilikan') }}</textarea>
                    @error('bukti_kepemilikan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="tk-field">
                    <label for="foto_bukti">Foto bukti <span class="tk-muted fw-normal">(opsional, mis. foto lama bersama barang)</span></label>
                    <input type="file" id="foto_bukti" name="foto_bukti" accept="image/*" data-preview="#bukti-preview"
                           class="form-control @error('foto_bukti') is-invalid @enderror">
                    @error('foto_bukti')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <img id="bukti-preview" class="tk-preview mt-2" alt="Pratinjau foto bukti">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="tk-btn tk-btn-primary">Ajukan klaim</button>
                    <a href="{{ route('laporan.show', $laporan) }}" class="tk-btn tk-btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('judul', 'Verifikasi Klaim')
@section('heading', 'Verifikasi klaim')
@section('subheading', 'Periksa bukti kepemilikan sebelum barang diserahkan')

@section('content')
@php
    $tabs = ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];
    $placeholder = \Illuminate\Support\Facades\Blade::render('<x-icon name="image" :size="28" />');
@endphp

<div class="tk-segmented mb-4">
    @foreach ($tabs as $v => $t)
        <a href="{{ route('petugas.klaim', ['tab' => $v]) }}" @class(['active' => $tab === $v])>
            {{ $t }}@if ($v === 'menunggu') (<span id="tab-menunggu-count">{{ $hitung['menunggu'] ?? 0 }}</span>)@endif
        </a>
    @endforeach
</div>

<div class="row g-4 align-items-start">
    <div class="col-xl">
        <div class="tk-card">
            <div class="tk-table-wrap">
                <table class="tk-table" id="tabel-klaim">
                    <thead><tr><th>Barang</th><th>Pengklaim</th><th>Diajukan</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($klaim as $k)
                            @php
                                $data = [
                                    'id'          => $k->id,
                                    'barang'      => $k->laporan->nama_barang,
                                    'info'        => ucfirst($k->laporan->jenis) . ' · ' . $k->laporan->lokasi->nama_lokasi,
                                    'pengklaim'   => $k->user->nama,
                                    'nim'         => $k->user->nim_nip,
                                    'hp'          => $k->user->no_hp,
                                    'diajukan'    => $k->created_at->translatedFormat('j M Y, H.i'),
                                    'bukti'       => $k->bukti_kepemilikan,
                                    'status'      => $k->status,
                                    'catatan'     => $k->catatan_petugas,
                                    'foto_barang' => $k->laporan->foto_url,
                                    'foto_bukti'  => $k->foto_bukti_url,
                                ];
                            @endphp
                            <tr class="selectable" data-id="{{ $k->id }}" data-klaim="{{ json_encode($data) }}">
                                <td class="strong">{{ $k->laporan->nama_barang }}</td>
                                <td>{{ $k->user->nama }}</td>
                                <td>{{ $k->created_at->translatedFormat('j M Y') }}</td>
                                <td><x-badge :value="$k->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Tidak ada klaim {{ $tab }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-auto" style="width: 100%; max-width: 448px">
        <div class="tk-card d-flex flex-column gap-3" id="detail-klaim">
            <div class="tk-empty @unless ($klaim->isEmpty()) d-none @endunless" id="panel-detail-kosong">Pilih klaim untuk melihat detailnya.</div>

            <div id="panel-detail-isi" @class(['d-none' => $klaim->isEmpty()])>
              <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="tk-subtitle">Detail klaim</h3>
                    <span id="d-badge"></span>
                </div>

                <div class="d-flex gap-3 align-items-center">
                    <div class="tk-photo flex-shrink-0" id="d-foto-barang" style="width:72px;height:72px" data-placeholder="{{ $placeholder }}">{!! $placeholder !!}</div>
                    <div>
                        <div class="tk-subtitle" id="d-barang"></div>
                        <div class="tk-caption tk-muted" id="d-info"></div>
                    </div>
                </div>

                <div class="tk-data">
                    <div><span>Pengklaim</span><b id="d-pengklaim"></b></div>
                    <div><span>NIM</span><b id="d-nim"></b></div>
                    <div><span>No. HP</span><b id="d-hp"></b></div>
                    <div><span>Diajukan</span><b id="d-diajukan"></b></div>
                </div>

                <div>
                    <div class="tk-label mb-1">Bukti kepemilikan</div>
                    <p class="mb-0" id="d-bukti" style="white-space: pre-line"></p>
                </div>

                <div class="tk-photo" id="d-foto-bukti" style="height:110px" data-placeholder="{{ $placeholder }}">{!! $placeholder !!}</div>

                <div class="tk-field">
                    <label for="d-catatan">Catatan petugas</label>
                    <input type="text" id="d-catatan" class="form-control" placeholder="Opsional, mis. jadwal pengambilan" maxlength="500">
                </div>

                <div class="tk-row-actions" id="d-aksi">
                    <button type="button" class="tk-btn tk-btn-outline flex-fill btn-status-klaim" data-status="ditolak"
                            data-url="{{ route('api.klaim.status', '__ID__') }}">Tolak</button>
                    <button type="button" class="tk-btn tk-btn-primary flex-fill btn-status-klaim" data-status="disetujui"
                            data-url="{{ route('api.klaim.status', '__ID__') }}">Setujui klaim</button>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection

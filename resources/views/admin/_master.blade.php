{{--
  Tabel + modal CRUD untuk data master sederhana (kategori / lokasi).
  Parameter: $judul, $satuan, $field, $items, $rute (mis. "admin.kategori")
--}}
@php $sembunyikanError = false; @endphp

<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th>Nama {{ $satuan }}</th>
                    <th style="width:170px">Jumlah laporan</th>
                    <th style="width:170px">Dibuat</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $item)
                    <tr>
                        <td class="strong">{{ $i + 1 }}</td>
                        <td>
                            {{ $item->$field }}
                            @if ($item->keterangan)<div class="tk-caption tk-muted">{{ $item->keterangan }}</div>@endif
                        </td>
                        <td>{{ $item->laporan_count }}</td>
                        <td>{{ $item->created_at->translatedFormat('j M Y') }}</td>
                        <td>
                            <div class="actions">
                                <button type="button" class="tk-icon-btn edit" title="Ubah"
                                        data-bs-toggle="modal" data-bs-target="#modalForm"
                                        data-action="{{ route($rute.'.update', $item) }}"
                                        data-item="{{ json_encode([$field => $item->$field, 'keterangan' => $item->keterangan]) }}">
                                    <x-icon name="edit" :size="16" />
                                </button>
                                <form method="POST" action="{{ route($rute.'.destroy', $item) }}"
                                      data-confirm="Hapus {{ $satuan }} &quot;{{ $item->$field }}&quot;?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tk-icon-btn trash" title="Hapus"><x-icon name="trash" :size="16" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada {{ $satuan }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="tk-caption tk-muted mt-3 mb-0">Menampilkan {{ $items->count() }} {{ $satuan }}</p>
</div>

{{-- Modal tambah / ubah --}}
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true"
     data-crud data-store="{{ route($rute.'.store') }}"
     data-title-add="Tambah {{ $satuan }}" data-title-edit="Ubah {{ $satuan }}">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px">
        <form method="POST" action="{{ route($rute.'.store') }}" class="modal-content" novalidate>
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah {{ $satuan }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="tk-field">
                    <label for="m-nama">Nama {{ $satuan }}</label>
                    <input type="text" id="m-nama" name="{{ $field }}" class="form-control" maxlength="100"
                           placeholder="Contoh: {{ $contoh }}" required>
                </div>
                <div class="tk-field">
                    <label for="m-ket">Keterangan</label>
                    <input type="text" id="m-ket" name="keterangan" class="form-control" maxlength="150" placeholder="Opsional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tk-btn tk-btn-outline" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="tk-btn tk-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

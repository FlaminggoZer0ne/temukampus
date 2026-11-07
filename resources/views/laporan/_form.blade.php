{{-- Form laporan dipakai create & edit. $laporan opsional. --}}
@php
    $l = $laporan ?? null;
    $err = fn ($f) => $errors->has($f) ? 'is-invalid' : '';
@endphp

<form method="POST" action="{{ $aksi }}" enctype="multipart/form-data" class="row g-4" novalidate>
    @csrf
    @if ($l) @method('PUT') @endif

    <div class="col-12">
        <label class="d-block fw-semibold mb-2">Jenis laporan</label>
        <div class="tk-segmented" id="pilih-jenis">
            @foreach (['hilang' => 'Barang hilang', 'temuan' => 'Barang temuan'] as $v => $t)
                <label class="mb-0">
                    <input type="radio" name="jenis" value="{{ $v }}" class="d-none" @checked(old('jenis', $l->jenis ?? 'hilang') === $v)>
                    <span class="d-block px-3 py-2 rounded-3 fw-semibold" style="cursor:pointer">{{ $t }}</span>
                </label>
            @endforeach
        </div>
        @error('jenis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 tk-field">
        <label for="nama_barang">Nama barang</label>
        <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $l->nama_barang ?? '') }}"
               class="form-control {{ $err('nama_barang') }}" placeholder="Contoh: Dompet kulit coklat" required>
        @error('nama_barang')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 tk-field">
        <label for="tanggal_kejadian">Tanggal kejadian</label>
        <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" max="{{ now()->toDateString() }}"
               value="{{ old('tanggal_kejadian', optional($l->tanggal_kejadian ?? null)->toDateString()) }}"
               class="form-control {{ $err('tanggal_kejadian') }}" required>
        @error('tanggal_kejadian')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 tk-field">
        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" class="form-select {{ $err('kategori_id') }}" required>
            <option value="">Pilih kategori</option>
            @foreach ($kategori as $k)
                <option value="{{ $k->id }}" @selected(old('kategori_id', $l->kategori_id ?? '') == $k->id)>{{ $k->nama_kategori }}</option>
            @endforeach
        </select>
        @error('kategori_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 tk-field">
        <label for="lokasi_id">Lokasi</label>
        <select id="lokasi_id" name="lokasi_id" class="form-select {{ $err('lokasi_id') }}" required>
            <option value="">Pilih lokasi</option>
            @foreach ($lokasi as $x)
                <option value="{{ $x->id }}" @selected(old('lokasi_id', $l->lokasi_id ?? '') == $x->id)>{{ $x->nama_lokasi }}</option>
            @endforeach
        </select>
        @error('lokasi_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 tk-field">
        <label for="deskripsi">Deskripsi / ciri-ciri</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control {{ $err('deskripsi') }}"
                  placeholder="Warna, merek, ciri khusus, isi barang, dll." required>{{ old('deskripsi', $l->deskripsi ?? '') }}</textarea>
        @error('deskripsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 tk-field">
        <label for="foto">Foto barang <span class="tk-muted fw-normal">(opsional, maks. 2 MB)</span></label>
        <input type="file" id="foto" name="foto" accept="image/*" data-preview="#foto-preview"
               class="form-control {{ $err('foto') }}">
        @error('foto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <img id="foto-preview" class="tk-preview mt-2" alt="Pratinjau foto"
             @if ($l && $l->foto_url) src="{{ $l->foto_url }}" style="display:block" @endif>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="tk-btn tk-btn-primary">{{ $l ? 'Simpan perubahan' : 'Kirim laporan' }}</button>
        <a href="{{ url()->previous() === url()->current() ? route('laporan.saya') : url()->previous() }}" class="tk-btn tk-btn-outline">Batal</a>
    </div>
</form>

@push('scripts')
<script>
    // Segmented jenis: tandai pilihan aktif.
    $(function () {
        const sync = () => $('#pilih-jenis label').each(function () {
            $(this).find('span').toggleClass('active', $(this).find('input').is(':checked'))
                   .css({ background: $(this).find('input').is(':checked') ? 'var(--navy)' : 'transparent',
                          color: $(this).find('input').is(':checked') ? 'var(--text-inverse)' : 'var(--text-muted)' });
        });
        $('#pilih-jenis input').on('change', sync); sync();
    });
</script>
@endpush

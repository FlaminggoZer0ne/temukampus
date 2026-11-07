/* =====================================================================
   TemuKampus — app.js (jQuery)
   - Sidebar mobile, konfirmasi hapus, preview foto
   - Live search barang (AJAX/JSON)
   - Verifikasi laporan / klaim & serah-terima tanpa reload (AJAX PATCH)
   - Modal tambah/ubah (kategori, lokasi, user)
   - Grafik dashboard dari /api/statistik
   ===================================================================== */
$(function () {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' }
    });

    const esc = (s) => $('<div>').text(s ?? '').html();
    const BADGE = {
        hilang: 'red', temuan: 'blue', menunggu: 'orange', aktif: 'green', disetujui: 'green',
        diklaim: 'blue', dikembalikan: 'gray', ditolak: 'red'
    };
    const badge = (v) => `<span class="tk-badge ${BADGE[v] || 'gray'}">${esc(v.charAt(0).toUpperCase() + v.slice(1))}</span>`;
    const ic = (base, name, size) =>
        `<span class="tk-ic" style="--s:${size}px;--src:url('${base}/${name}.svg')"></span>`;

    /* ---------- Sidebar (mobile) ---------- */
    $('#menuBtn').on('click', () => $('#sidebar, #backdrop').addClass('open'));
    $('#backdrop').on('click', () => $('#sidebar, #backdrop').removeClass('open'));

    /* ---------- Konfirmasi hapus ---------- */
    $(document).on('submit', 'form[data-confirm]', function (e) {
        if (!window.confirm($(this).data('confirm'))) e.preventDefault();
    });

    /* ---------- Preview foto sebelum upload ---------- */
    $(document).on('change', 'input[type="file"][data-preview]', function () {
        const $img = $($(this).data('preview'));
        const file = this.files && this.files[0];
        if (!file) { $img.hide().removeAttr('src'); return; }
        const reader = new FileReader();
        reader.onload = (ev) => $img.attr('src', ev.target.result).show();
        reader.readAsDataURL(file);
    });

    /* ---------- Live search barang ---------- */
    const $hasil = $('#hasil');
    if ($hasil.length) {
        const url = $hasil.data('url');
        const iconBase = $hasil.data('icons');
        const detailBase = $hasil.data('detail');
        let timer = null, xhr = null;

        const kartu = (it) => {
            const foto = it.foto_url
                ? `<img src="${esc(it.foto_url)}" alt="${esc(it.nama_barang)}">`
                : ic(iconBase, 'image', 28);
            return `<div class="col">
                <a href="${detailBase}/${it.id}" class="tk-item">
                    <div class="tk-photo">${foto}</div>
                    <div class="tk-item-body">
                        ${badge(it.jenis)}
                        <span class="nama">${esc(it.nama_barang)}</span>
                        <span class="tk-item-meta">${ic(iconBase, 'map-pin', 14)} ${esc(it.lokasi ? it.lokasi.nama_lokasi : '-')}</span>
                        <span class="tk-item-meta">${ic(iconBase, 'calendar', 14)} ${esc(it.tanggal_label)}</span>
                    </div>
                </a></div>`;
        };

        const cari = () => {
            if (xhr) xhr.abort();
            $hasil.css('opacity', .5);
            xhr = $.get(url, {
                q: $('#cari').val(),
                kategori: $('#filter-kategori').val(),
                lokasi: $('#filter-lokasi').val(),
                jenis: $('#filter-jenis .active').data('jenis') || '',
                status: $('#filter-status').val() || ''
            }).done((res) => {
                $hasil.html(res.data.length
                    ? res.data.map(kartu).join('')
                    : '<div class="col-12"><div class="tk-empty">Barang tidak ditemukan.</div></div>');
                $('#info-hasil').text(`Menampilkan ${res.data.length} dari ${res.total} barang`);
            }).fail((x, status) => {
                if (status !== 'abort') $hasil.html('<div class="col-12"><div class="tk-empty">Gagal memuat data. Coba lagi.</div></div>');
            }).always(() => $hasil.css('opacity', 1));
        };

        $('#cari').on('keyup input', () => { clearTimeout(timer); timer = setTimeout(cari, 250); });
        $('#filter-kategori, #filter-lokasi, #filter-status').on('change', cari);
        $('#filter-jenis').on('click', 'button', function () {
            $(this).addClass('active').siblings().removeClass('active');
            cari();
        });
        cari();
    }

    /* ---------- Update status laporan (verifikasi / serah-terima) ---------- */
    const patch = (url, data) => $.ajax({ url, type: 'PATCH', data });
    const pesanError = (x) => (x.responseJSON && x.responseJSON.message) || 'Terjadi kesalahan. Coba lagi.';

    const hapusBaris = ($tr, kosongPesan) => {
        const $tbody = $tr.closest('tbody');
        $tr.fadeOut(200, function () {
            $(this).remove();
            if (!$tbody.children('tr').length) {
                $tbody.html(`<tr><td colspan="9" class="empty">${kosongPesan}</td></tr>`);
            }
        });
    };

    $(document).on('click', '.btn-status-laporan', function () {
        const $b = $(this), $tr = $b.closest('tr');
        const status = $b.data('status');
        if ($b.data('confirm') && !window.confirm($b.data('confirm'))) return;

        $tr.find('button').prop('disabled', true);
        patch($b.data('url'), { status })
            .done(() => hapusBaris($tr, $b.data('kosong') || 'Tidak ada data.'))
            .fail((x) => { window.alert(pesanError(x)); $tr.find('button').prop('disabled', false); });
    });

    /* ---------- Verifikasi klaim: pilih baris -> panel detail ---------- */
    const $detail = $('#detail-klaim');
    if ($detail.length) {
        const pilih = ($tr) => {
            $('#tabel-klaim tr.selected').removeClass('selected');
            $tr.addClass('selected');
            const d = $tr.data('klaim');
            $detail.data('id', d.id);
            $('#d-barang').text(d.barang);
            $('#d-info').text(d.info);
            $('#d-pengklaim').text(d.pengklaim);
            $('#d-nim').text(d.nim || '-');
            $('#d-hp').text(d.hp || '-');
            $('#d-diajukan').text(d.diajukan);
            $('#d-bukti').text(d.bukti);
            $('#d-badge').html(badge(d.status));
            $('#d-catatan').val(d.catatan || '');
            $('#d-foto-barang').html(d.foto_barang ? `<img src="${esc(d.foto_barang)}" alt="">` : $('#d-foto-barang').data('placeholder'));
            $('#d-foto-bukti').html(d.foto_bukti ? `<img src="${esc(d.foto_bukti)}" alt="">` : $('#d-foto-bukti').data('placeholder'));
            const menunggu = d.status === 'menunggu';
            $('#d-aksi').toggle(menunggu);
            $('#d-catatan').prop('disabled', !menunggu);
        };

        $('#tabel-klaim').on('click', 'tr.selectable', function () { pilih($(this)); });
        const $pertama = $('#tabel-klaim tr.selectable').first();
        if ($pertama.length) pilih($pertama);

        $('.btn-status-klaim').on('click', function () {
            const status = $(this).data('status');
            const id = $detail.data('id');
            const $tr = $(`#tabel-klaim tr[data-id="${id}"]`);
            $('.btn-status-klaim').prop('disabled', true);
            patch($(this).data('url').replace('__ID__', id), { status, catatan_petugas: $('#d-catatan').val() })
                .done(() => {
                    // Baris pindah tab, jadi hilangkan dari daftar lalu pilih baris berikutnya.
                    const $next = $tr.next('tr.selectable').length ? $tr.next('tr.selectable') : $tr.prev('tr.selectable');
                    $tr.remove();
                    const tersisa = $('#tabel-klaim tr.selectable').length;
                    $('#tab-menunggu-count').text(tersisa);
                    if ($next.length) pilih($next);
                    else {
                        $('#tabel-klaim tbody').html('<tr><td colspan="4" class="empty">Semua klaim sudah diproses.</td></tr>');
                        $('#panel-detail-isi').addClass('d-none'); $('#panel-detail-kosong').removeClass('d-none');
                    }
                })
                .fail((x) => window.alert(pesanError(x)))
                .always(() => $('.btn-status-klaim').prop('disabled', false));
        });
    }

    /* ---------- Modal tambah / ubah (dipakai kategori, lokasi, user) ---------- */
    $('.modal[data-crud]').on('show.bs.modal', function (ev) {
        const $m = $(this), $form = $m.find('form'), $btn = $(ev.relatedTarget);
        const edit = $btn.length && $btn.data('action');

        $form[0].reset();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('input[name="_method"]').remove();

        if (edit) {
            $form.attr('action', $btn.data('action'))
                 .append('<input type="hidden" name="_method" value="PUT">');
            $m.find('.modal-title').text($m.data('title-edit'));
            $.each($btn.data('item') || {}, (k, v) => $form.find(`[name="${k}"]`).val(v));
            $form.find('[data-password-hint]').show();
            $form.find('input[name="password"]').prop('required', false);
        } else {
            $form.attr('action', $m.data('store'));
            $m.find('.modal-title').text($m.data('title-add'));
            $form.find('[data-password-hint]').hide();
            $form.find('input[name="password"]').prop('required', true);
        }
    });

    /* ---------- Grafik dashboard dari AJAX/JSON ---------- */
    const $bulanan = $('#chart-bulanan');
    if ($bulanan.length) {
        $.get($bulanan.data('url')).done((res) => {
            const max = Math.max(1, ...res.per_bulan.map((b) => b.jumlah));
            const tinggi = 170; // px untuk nilai tertinggi
            $bulanan.html(res.per_bulan.map((b, i) => {
                const last = i === res.per_bulan.length - 1;
                return `<div class="tk-bar ${last ? 'current' : ''}">
                    <span class="val">${b.jumlah}</span>
                    <div class="fill" style="height:${Math.max(4, Math.round(b.jumlah / max * tinggi))}px"></div>
                    <span class="lbl">${esc(b.label)}</span></div>`;
            }).join(''));

            const maxK = Math.max(1, ...res.per_kategori.map((k) => k.jumlah));
            $('#chart-kategori').html(res.per_kategori.length ? res.per_kategori.map((k) =>
                `<div class="tk-prog">
                    <div class="row-lbl"><span>${esc(k.nama)}</span><b>${k.jumlah}</b></div>
                    <div class="track"><span style="width:${Math.round(k.jumlah / maxK * 100)}%"></span></div>
                </div>`).join('') : '<p class="tk-muted mb-0">Belum ada data.</p>');
        }).fail(() => $bulanan.html('<p class="tk-muted">Gagal memuat statistik.</p>'));
    }
});

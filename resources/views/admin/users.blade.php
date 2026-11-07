@extends('layouts.app')
@section('judul', 'Kelola User')
@section('heading', 'Kelola user')
@section('subheading', 'Atur akun dan level pengguna')

@section('actions')
    <button type="button" class="tk-btn tk-btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">+ Tambah user</button>
@endsection

@section('content')
@php
    $filter = request('role');
    $tabs = ['' => 'Semua', 'admin' => 'Admin', 'petugas' => 'Petugas', 'mahasiswa' => 'Mahasiswa'];
    $warna = ['admin' => 'orange', 'petugas' => 'blue', 'mahasiswa' => 'gray'];
@endphp

<div class="tk-segmented mb-4">
    @foreach ($tabs as $v => $t)
        <a href="{{ route('admin.users.index', $v ? ['role' => $v] : []) }}" @class(['active' => (string) $filter === (string) $v])>{{ $t }}</a>
    @endforeach
</div>

<div class="tk-card">
    <div class="tk-table-wrap">
        <table class="tk-table">
            <thead>
                <tr><th>Nama</th><th>NIM / NIP</th><th>Email</th><th>No. HP</th><th>Role</th><th>Laporan</th><th style="width:110px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr>
                        <td class="strong">{{ $u->nama }}@if ($u->id === auth()->id()) <span class="tk-caption tk-muted fw-normal">(Anda)</span>@endif</td>
                        <td>{{ $u->nim_nip ?: '—' }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->no_hp ?: '—' }}</td>
                        <td><span class="tk-badge {{ $warna[$u->role] }}">{{ $u->role_label }}</span></td>
                        <td>{{ $u->laporan_count }}</td>
                        <td>
                            <div class="actions">
                                <button type="button" class="tk-icon-btn edit" title="Ubah"
                                        data-bs-toggle="modal" data-bs-target="#modalForm"
                                        data-action="{{ route('admin.users.update', $u) }}"
                                        data-item="{{ json_encode(['nama' => $u->nama, 'nim_nip' => $u->nim_nip, 'email' => $u->email, 'no_hp' => $u->no_hp, 'role' => $u->role]) }}">
                                    <x-icon name="edit" :size="16" />
                                </button>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                          data-confirm="Hapus user &quot;{{ $u->nama }}&quot; beserta semua laporannya?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tk-icon-btn trash" title="Hapus"><x-icon name="trash" :size="16" /></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="tk-caption tk-muted mt-3 mb-0">Menampilkan {{ $users->count() }} user</p>
</div>

<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true"
     data-crud data-store="{{ route('admin.users.store') }}" data-title-add="Tambah user" data-title-edit="Ubah user">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px">
        <form method="POST" action="{{ route('admin.users.store') }}" class="modal-content" novalidate>
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah user</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="tk-field">
                    <label for="m-nama">Nama lengkap</label>
                    <input type="text" id="m-nama" name="nama" class="form-control" maxlength="100" required>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 tk-field">
                        <label for="m-nim">NIM / NIP</label>
                        <input type="text" id="m-nim" name="nim_nip" class="form-control" maxlength="20" placeholder="Opsional">
                    </div>
                    <div class="col-sm-6 tk-field">
                        <label for="m-hp">No. HP</label>
                        <input type="text" id="m-hp" name="no_hp" class="form-control" maxlength="20" placeholder="Opsional">
                    </div>
                </div>
                <div class="tk-field">
                    <label for="m-email">Email</label>
                    <input type="email" id="m-email" name="email" class="form-control" maxlength="100" required>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 tk-field">
                        <label for="m-role">Role</label>
                        <select id="m-role" name="role" class="form-select" required>
                            @foreach (\App\Models\User::ROLES as $r)
                                <option value="{{ $r }}" @selected($r === 'mahasiswa')>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 tk-field">
                        <label for="m-pass">Password</label>
                        <input type="password" id="m-pass" name="password" class="form-control" minlength="6" autocomplete="new-password" required>
                        <span class="hint" data-password-hint style="display:none">Kosongkan bila tidak diubah.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tk-btn tk-btn-outline" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="tk-btn tk-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

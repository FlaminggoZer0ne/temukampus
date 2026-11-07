@extends('layouts.app')
@section('judul', 'Edit Profil')
@section('heading', 'Edit profil')
@section('subheading', 'Perbarui data akun Anda')

@php $sembunyikanError = true; @endphp

@section('content')
<div class="tk-card" style="max-width: 640px">
    <form method="POST" action="{{ route('profil.update') }}" class="d-flex flex-column gap-3" novalidate>
        @csrf @method('PUT')

        <div class="tk-field">
            <label for="nama">Nama lengkap</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
            @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-sm-6 tk-field">
                <label for="nim_nip">{{ $user->isMahasiswa() ? 'NIM' : 'NIP' }}</label>
                <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}" class="form-control @error('nim_nip') is-invalid @enderror">
                @error('nim_nip')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6 tk-field">
                <label for="no_hp">No. HP</label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="form-control @error('no_hp') is-invalid @enderror">
                @error('no_hp')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="tk-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <hr class="my-1">
        <p class="tk-muted mb-0">Kosongkan password bila tidak ingin mengubahnya.</p>

        <div class="row g-3">
            <div class="col-sm-6 tk-field">
                <label for="password">Password baru</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6 tk-field">
                <label for="password_confirmation">Ulangi password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>
        </div>

        <div><button type="submit" class="tk-btn tk-btn-primary">Simpan perubahan</button></div>
    </form>
</div>
@endsection

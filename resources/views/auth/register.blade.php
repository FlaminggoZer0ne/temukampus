@extends('layouts.auth')
@section('judul', 'Daftar')

@section('content')
@php $sembunyikanError = true; @endphp
<form method="POST" action="{{ route('register') }}" novalidate>
    @csrf
    <div class="head">
        <h2 class="tk-heading">Daftar akun mahasiswa</h2>
        <p>Akun petugas dan admin dibuat oleh administrator kampus.</p>
    </div>

    @include('partials.flash')

    <div class="tk-field">
        <label for="nama">Nama lengkap</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
        @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3">
        <div class="col-sm-6 tk-field">
            <label for="nim_nip">NIM</label>
            <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" class="form-control @error('nim_nip') is-invalid @enderror" required>
            @error('nim_nip')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-sm-6 tk-field">
            <label for="no_hp">No. HP</label>
            <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror" placeholder="Opsional">
            @error('no_hp')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="tk-field">
        <label for="email">Email kampus</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror"
               placeholder="nama@student.telkomuniversity.ac.id" required>
        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3">
        <div class="col-sm-6 tk-field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-sm-6 tk-field">
            <label for="password_confirmation">Ulangi password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
        </div>
    </div>

    <button type="submit" class="tk-btn tk-btn-primary tk-btn-block">Daftar</button>

    <p class="text-center tk-muted mb-0">
        Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk</a>
    </p>
</form>
@endsection

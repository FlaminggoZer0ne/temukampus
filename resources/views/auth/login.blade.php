@extends('layouts.auth')
@section('judul', 'Masuk')

@section('content')
<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    <div class="head">
        <h2 class="tk-heading">Masuk ke akun</h2>
        <p>Admin, petugas, dan mahasiswa masuk dari halaman yang sama.</p>
    </div>

    @include('partials.flash')

    <div class="tk-field">
        <label for="email">Email kampus</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="nama@student.telkomuniversity.ac.id" autocomplete="username" autofocus required>
    </div>

    <div class="tk-field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="••••••••" autocomplete="current-password" required>
    </div>

    <div class="tk-check">
        <label for="remember">
            <input class="form-check-input mt-0" type="checkbox" id="remember" name="remember" value="1" @checked(old('email') === null ? true : old('remember'))>
            Ingat saya
        </label>
        <a href="mailto:admin@temukampus.test?subject=Reset%20password%20TemuKampus">Lupa password?</a>
    </div>

    <button type="submit" class="tk-btn tk-btn-primary tk-btn-block">Masuk</button>

    <p class="text-center tk-muted mb-0">
        Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar sebagai mahasiswa</a>
    </p>
</form>
@endsection

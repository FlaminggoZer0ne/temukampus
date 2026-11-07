<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body>
<header class="tk-public-nav">
    <a href="{{ route('beranda') }}" class="tk-logo">
        <x-logo :mark="38" :text="22" />
    </a>
    <div class="d-flex gap-2">
        @auth
            <a href="{{ route('dashboard') }}" class="tk-btn tk-btn-primary tk-btn-sm">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="tk-btn tk-btn-primary tk-btn-sm">Masuk</a>
        @endauth
    </div>
</header>

@yield('content')

<footer class="tk-section pt-0 text-center tk-muted tk-caption">
    Tugas Besar Praktikum Pemrograman Web · Telkom University
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

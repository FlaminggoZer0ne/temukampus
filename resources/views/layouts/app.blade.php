<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    @stack('styles')
</head>
<body>
<div class="tk-shell">
    @include('partials.sidebar')

    <main class="tk-main">
        <div class="tk-topbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="tk-menu-btn" id="menuBtn" aria-label="Buka menu"><span style="font-size:18px;line-height:1">&#9776;</span></button>
                <div>
                    <h1 class="tk-heading">@yield('heading')</h1>
                    @hasSection('subheading')<p>@yield('subheading')</p>@endif
                </div>
            </div>
            <div class="tk-actions">
                @yield('actions')
                @php
                    $user = auth()->user();
                    [$bellUrl, $bellCount] = match (true) {
                        $user->isPetugas() => [route('petugas.laporan'),
                            \App\Models\Laporan::where('status', 'menunggu')->count() + \App\Models\Klaim::where('status', 'menunggu')->count()],
                        $user->isAdmin()   => [route('admin.laporan.index', ['status' => 'menunggu']),
                            \App\Models\Laporan::where('status', 'menunggu')->count()],
                        default            => [route('klaim.index'), 0],
                    };
                @endphp
                <a href="{{ $bellUrl }}" class="tk-bell position-relative" title="Notifikasi">
                    <x-icon name="bell" :size="18" />
                    @if ($bellCount > 0)
                        <span class="position-absolute top-0 end-0 translate-middle p-1 rounded-circle" style="background: var(--orange); margin: 6px 0 0 -4px;"></span>
                    @endif
                </a>
            </div>
        </div>

        @include('partials.flash')

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>

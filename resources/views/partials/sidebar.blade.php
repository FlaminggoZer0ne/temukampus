@php
    $user = auth()->user();

    // [ikon, label, route, pola route aktif]
    $menu = match ($user->role) {
        'admin' => [
            ['chart',     'Dashboard',     'dashboard',            ['dashboard']],
            ['users',     'Kelola User',   'admin.users.index',    ['admin.users.*']],
            ['tag',       'Kategori',      'admin.kategori.index', ['admin.kategori.*']],
            ['map-pin',   'Lokasi',        'admin.lokasi.index',   ['admin.lokasi.*']],
            ['clipboard', 'Semua Laporan', 'admin.laporan.index',  ['admin.laporan.*']],
        ],
        'petugas' => [
            ['chart',     'Dashboard',          'dashboard',           ['dashboard']],
            ['clipboard', 'Verifikasi Laporan', 'petugas.laporan',     ['petugas.laporan']],
            ['check',     'Verifikasi Klaim',   'petugas.klaim',       ['petugas.klaim']],
            ['package',   'Serah-terima',       'petugas.serahterima', ['petugas.serahterima']],
            ['search',    'Cari Barang',        'laporan.index',       ['laporan.index', 'laporan.show']],
            ['plus',      'Catat Barang',       'laporan.create',      ['laporan.create', 'laporan.edit', 'laporan.saya']],
        ],
        default => [
            ['home',      'Beranda',      'dashboard',      ['dashboard']],
            ['search',    'Cari Barang',  'laporan.index',  ['laporan.index', 'laporan.show']],
            ['plus',      'Lapor Barang', 'laporan.create', ['laporan.create', 'laporan.edit']],
            ['clipboard', 'Laporan Saya', 'laporan.saya',   ['laporan.saya']],
            ['check',     'Klaim Saya',   'klaim.index',    ['klaim.*']],
        ],
    };

    // Angka kecil di menu verifikasi petugas.
    $badge = [];
    if ($user->isPetugas()) {
        $badge['petugas.laporan'] = \App\Models\Laporan::where('status', 'menunggu')->count();
        $badge['petugas.klaim']   = \App\Models\Klaim::where('status', 'menunggu')->count();
    }
@endphp

<aside class="tk-sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="tk-logo">
        <x-logo :mark="38" :text="22" />
    </a>

    <div class="tk-role">{{ $user->role_label }}</div>

    <nav class="tk-nav">
        @foreach ($menu as [$ikon, $label, $rute, $aktif])
            <a href="{{ route($rute) }}" @class(['active' => request()->routeIs(...$aktif)])>
                <x-icon :name="$ikon" />
                <span>{{ $label }}</span>
                @if (($badge[$rute] ?? 0) > 0)
                    <span class="badge-count">{{ $badge[$rute] }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="tk-user">
        <a href="{{ route('profil') }}" class="tk-user-link" title="Edit profil">
            <span class="tk-avatar">{{ $user->inisial }}</span>
            <span class="tk-user-name">
                <strong>{{ $user->nama }}</strong>
                <small>{{ $user->role === 'admin' ? $user->email : $user->role_label }}</small>
            </span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="tk-logout" title="Keluar"><x-icon name="logout" :size="18" /></button>
        </form>
    </div>
</aside>
<div class="tk-backdrop" id="backdrop"></div>

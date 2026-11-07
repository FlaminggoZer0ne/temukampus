@if (session('success'))
    <div class="tk-alert success" role="alert"><x-icon name="check" :size="18" /> <span>{{ session('success') }}</span></div>
@endif
@if (session('error'))
    <div class="tk-alert error" role="alert"><x-icon name="x" :size="18" /> <span>{{ session('error') }}</span></div>
@endif
@if ($errors->any() && empty($sembunyikanError))
    <div class="tk-alert error" role="alert">
        <x-icon name="x" :size="18" />
        <div>
            Periksa kembali isian Anda:
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

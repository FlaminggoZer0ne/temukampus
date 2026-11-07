@props(['mark' => 38, 'text' => 22, 'tone' => 'dark'])
{{--
  Logo TemuKampus — Konsep A "Pin Cari" (Figma, halaman Logo).
  tone="dark"  : untuk latar gelap (navy), "Temu" berwarna terang.
  tone="light" : untuk latar terang, "Temu" berwarna navy.
--}}
<span {{ $attributes->class(['tk-brand', 'on-light' => $tone === 'light']) }}>
    <img src="{{ asset('img/logo-mark.svg') }}" width="{{ $mark }}" height="{{ $mark }}" alt="" style="margin-left: -{{ round($mark * 0.1875) }}px">
    <span class="tk-wordmark" style="font-size: {{ $text }}px"><span class="t">Temu</span><span class="k">Kampus</span></span>
</span>

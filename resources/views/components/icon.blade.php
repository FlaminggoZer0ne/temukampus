@props(['name', 'size' => 20])
{{-- Ikon SVG dari Figma (public/img/icons/{name}.svg), warna mengikuti `color` elemen induk. --}}
<span {{ $attributes->class('tk-ic') }}
      style="--s: {{ $size }}px; --src: url('{{ asset('img/icons/'.$name.'.svg') }}')"
      aria-hidden="true"></span>

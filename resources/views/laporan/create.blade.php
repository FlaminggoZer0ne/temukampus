@extends('layouts.app')
@section('judul', 'Lapor Barang')
@section('heading', 'Lapor barang')
@section('subheading', 'Laporkan barang hilang atau temuan beserta fotonya')

@php $sembunyikanError = true; @endphp

@section('content')
<div class="tk-card" style="max-width: 820px">
    @include('laporan._form', ['aksi' => route('laporan.store')])
</div>
@endsection

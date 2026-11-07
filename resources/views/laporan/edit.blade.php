@extends('layouts.app')
@section('judul', 'Ubah Laporan')
@section('heading', 'Ubah laporan')
@section('subheading', $laporan->nama_barang)

@php $sembunyikanError = true; @endphp

@section('content')
<div class="tk-card" style="max-width: 820px">
    @include('laporan._form', ['aksi' => route('laporan.update', $laporan)])
</div>
@endsection

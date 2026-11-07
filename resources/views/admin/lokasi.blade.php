@extends('layouts.app')
@section('judul', 'Kelola Lokasi')
@section('heading', 'Kelola lokasi')
@section('subheading', 'Tambah, ubah, atau hapus lokasi kampus')

@section('actions')
    <button type="button" class="tk-btn tk-btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">+ Tambah lokasi</button>
@endsection

@section('content')
    @include('admin._master', [
        'satuan' => 'lokasi', 'field' => 'nama_lokasi', 'items' => $lokasi,
        'rute' => 'admin.lokasi', 'contoh' => 'Laboratorium 2',
    ])
@endsection

@extends('layouts.app')
@section('judul', 'Kelola Kategori')
@section('heading', 'Kelola kategori')
@section('subheading', 'Tambah, ubah, atau hapus kategori barang')

@section('actions')
    <button type="button" class="tk-btn tk-btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">+ Tambah kategori</button>
@endsection

@section('content')
    @include('admin._master', [
        'satuan' => 'kategori', 'field' => 'nama_kategori', 'items' => $kategori,
        'rute' => 'admin.kategori', 'contoh' => 'Alat tulis',
    ])
@endsection

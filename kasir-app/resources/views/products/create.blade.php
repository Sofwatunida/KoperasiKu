@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('content')
    <div class="mb-6"><h1 class="page-title">Tambah Produk Baru</h1><p class="page-subtitle">Masukkan data barang koperasi.</p></div>
    <div class="max-w-4xl">@include('products._form')</div>
@endsection
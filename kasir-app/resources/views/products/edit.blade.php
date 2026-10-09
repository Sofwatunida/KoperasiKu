@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')
    <div class="mb-6"><h1 class="page-title">Edit Produk</h1><p class="page-subtitle">Perbarui informasi {{ $product->name }}.</p></div>
    <div class="max-w-4xl">@include('products._form', ['product' => $product])</div>
@endsection
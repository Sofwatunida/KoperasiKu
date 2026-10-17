@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('content')
    <div class="mb-6"><h1 class="page-title">Tambah Pengguna Baru</h1><p class="page-subtitle">Buat akun super admin, admin, atau kasir koperasi.</p></div>
    <div class="max-w-4xl">@include('users._form')</div>
@endsection
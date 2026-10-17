@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('content')
    <div class="mb-6"><h1 class="page-title">Edit Pengguna</h1><p class="page-subtitle">Perbarui data, role, atau password akun.</p></div>
    <div class="max-w-4xl">@include('users._form')</div>
@endsection
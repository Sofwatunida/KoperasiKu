@extends('layouts.app')

@section('title', __('products.edit_title'))

@section('content')
    <div class="mb-6"><h1 class="page-title">{{ __('products.edit_title') }}</h1><p class="page-subtitle">{{ __('products.edit_subtitle', ['name' => $product->name]) }}</p></div>
    <div class="max-w-4xl">@include('products._form', ['product' => $product])</div>
@endsection

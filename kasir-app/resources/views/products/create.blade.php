@extends('layouts.app')

@section('title', __('products.create_title'))

@section('content')
    <div class="mb-6"><h1 class="page-title">{{ __('products.create_heading') }}</h1><p class="page-subtitle">{{ __('products.create_subtitle') }}</p></div>
    <div class="max-w-4xl">@include('products._form')</div>
@endsection

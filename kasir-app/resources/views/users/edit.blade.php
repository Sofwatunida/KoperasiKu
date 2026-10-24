@extends('layouts.app')
@section('title', __('users.edit_title'))
@section('content')
    <div class="mb-6"><h1 class="page-title">{{ __('users.edit_title') }}</h1><p class="page-subtitle">{{ __('users.edit_subtitle') }}</p></div>
    <div class="max-w-4xl">@include('users._form')</div>
@endsection

@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/administrador.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.administrador')
@endsection

{{-- Aquí iría el contenido del panel --}}
@section('content')
  @yield('content')
@endsection

@stack('scripts')
@yield('scripts')
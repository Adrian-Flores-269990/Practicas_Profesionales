@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/administrador.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
@endpush

@section('subheader')
  @include('partials.nav.administrador')
@endsection

@section('content')
  @yield('content')
@endsection

@stack('scripts')
@yield('scripts')

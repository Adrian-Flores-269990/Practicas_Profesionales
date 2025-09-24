@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/administrador.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.administrador')
@endsection

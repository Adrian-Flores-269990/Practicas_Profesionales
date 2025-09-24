@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/encargado.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.encargado')
@endsection

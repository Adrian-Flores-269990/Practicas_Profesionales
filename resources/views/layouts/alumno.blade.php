@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/alumno.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.alumno')
@endsection



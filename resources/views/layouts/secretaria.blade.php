@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/secretaria.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.secretaria')
@endsection

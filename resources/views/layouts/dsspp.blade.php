@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/dsspp.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.dsspp')
@endsection

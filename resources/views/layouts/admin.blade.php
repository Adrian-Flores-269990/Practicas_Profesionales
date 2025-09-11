@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('subheader')
  @include('partials.nav.admin')
@endsection

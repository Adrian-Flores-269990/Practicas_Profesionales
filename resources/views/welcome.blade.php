@extends('layouts.app')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush
@section('title','Prácticas Profesionales')

@section('content')
  <div class="home-wrap">
    <div class="dropdown" style="margin:8px 0 12px">
      <button class="dropbtn">PRACTICAS PROFESIONALES</button>
      <div class="dropmenu">
        <a href="{{ route('alumno.login') }}">Alumno</a>
        <a href="{{ route('empleado.login') }}">Empleado</a>
      </div>
    </div>

    <div class="home-hero">
      <img src="{{ asset('images/fondo-ingenieria.jpeg') }}" alt="Facultad de Ingeniería">    </div>
  </div>
@endsection

{{-- Footer ESPECIAL full-bleed (cubre todo el ancho) --}}
@section('footer_custom')
  <footer class="footer-bleed">
    <div class="footer-title">SECRETARÍA ACADÉMICA</div>
    <div class="footer-text">
      Bienvenidos a la página de la Secretaría Académica de la Facultad de Ingeniería. La misión de esta Secretaría
      es auxiliar al Director en sus funciones; organizar y coordinar las actividades académicas de licenciatura y
      posgrado, así como supervisar los eventos académicos de la Facultad.
    </div>
  </footer>

@endsection

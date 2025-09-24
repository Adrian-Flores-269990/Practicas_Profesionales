@extends('layouts.alumno')
@section('title','Inicio Alumno')

@section('content')
  <div class="container-fluid info-alumno-wrapper">
    <div class="bloque-datos">
      <div><img src="{{ asset('images/perfil.webp') }}" alt="Foto del alumno" class="foto-alumno"></div>
      <div class="datos-izquierda">
        <div class="campo-pareado"><div class="campo-label">Clave UASLP</div><div class="campo-valor">XXXXXX</div></div>
        <div class="campo-pareado"><div class="campo-label">Clave Ingeniería</div><div class="campo-valor">XXXXXXXXXXXX</div></div>
        <div class="campo-pareado"><div class="campo-label">Nombre</div><div class="campo-valor">NOMBRE DEL ALUMNO</div></div>
        <div class="campo-pareado"><div class="campo-label">Carrera</div><div class="campo-valor">NOMBRE DE LA CARRERA</div></div>
        <div class="campo-pareado"><div class="campo-label">Asesor</div><div class="campo-valor">NOMBRE DEL ASESOR</div></div>
        <div class="campo-pareado"><div class="campo-label">Ciclo escolar</div><div class="campo-valor">CICLO ESCOLAR</div></div>
        <div class="campo-pareado"><div class="campo-label">Semestre</div><div class="campo-valor">SEMESTRE</div></div>
      </div>
    </div>

    <div class="datos-derecha">
      <div class="campo-extra"><div class="label">Fecha</div><div class="valor">25/02/2025</div></div>
      <div class="campo-extra"><div class="label">Condición</div><div class="valor">REGULAR</div></div>
      <div class="campo-extra"><div class="label">Situación</div><div class="valor">INSCRITO</div></div>
    </div>
  </div>
@endsection

@extends('layouts.dsspp')
@section('title','Resultado de búsqueda')

@section('content')
<div class="container mt-5">
  <div class="card">
    <div class="card-header text-white fw-bold" style="background-color: #000066;">
      Resultado de la búsqueda
    </div>
    <div class="card-body">
      <p><strong>Clave:</strong> {{ $alumno->Clave_Alumno }}</p>
      <p><strong>Nombre:</strong> {{ $alumno->Nombre }} {{ $alumno->ApellidoP_Alumno }} {{ $alumno->ApellidoM_Alumno }}</p>
      <p><strong>Carrera:</strong> {{ $alumno->Carrera }}</p>
      <p><strong>Correo:</strong> {{ $alumno->CorreoElectronico }}</p>

      <a href="{{ route('dsspp.alumnos') }}" class="btn btn-secondary">Volver</a>
    </div>
  </div>
</div>
@endsection
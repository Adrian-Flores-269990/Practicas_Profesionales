@extends('layouts.encargado')
@section('title','Estadísticas empresas')

@section('content')


    <nav class="navbar" style="background-color: #000066;">
      <div class="container-fluid justify-content-center">
        <span class="navbar-text text-white mx-auto" style="font-weight: 500;">
          Estadísticas de empresas con las que se tienen convenios para prácticas profesionales
        </span>
      </div>
    </nav>

    <div class="container mt-4">
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="empresaDropdown" class="form-label fw-bold">Nombre de la empresa</label>
          <select class="form-select" id="empresaDropdown">
            <option selected>Selecciona la empresa</option>
            {{-- Opciones aquí --}}
          </select>
        </div>
        <div class="col-md-6">
          <label for="versionDropdown" class="form-label fw-bold">Versión</label>
          <select class="form-select" id="versionDropdown">
            <option selected>Selecciona versión de cuestionario</option>
            {{-- Opciones  aquí --}}
          </select>
        </div>
      </div>
    </div>

@endsection
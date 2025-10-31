@extends('layouts.dsspp')
@section('title','Consultar alumno por clave')

@section('content')
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header text-white" style="background-color: #000066;">
            Consultar alumno por clave única
          </div>
          <div class="card-body">
            {{-- Formulario para buscar alumno --}}
            <form action="{{ route('dsspp.consultar_alumno') }}" method="GET">
              @csrf
              <div class="mb-3">
                <label for="claveAlumno" class="form-label fw-bold">Clave del alumno</label>
                <input type="text" class="form-control" id="claveAlumno" name="claveAlumno" placeholder="Ingrese la clave del alumno" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Consultar</button>
            </form>

            {{-- Mostrar mensajes de éxito o error --}}
            @if (session('error'))
              <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
            @if (session('success'))
              <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
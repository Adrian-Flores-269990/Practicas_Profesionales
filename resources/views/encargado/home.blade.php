@extends('layouts.encargado')
@section('title','Consultar alumno por clave')

@section('content')
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-white" style="background-color: #003f84;">
            Consultar alumno por clave única
          </div>
          <div class="card-body">
            <form>
              <div class="mb-3">
                <label for="claveAlumno" class="form-label">Clave del alumno</label>
                <input type="text" class="form-control" id="claveAlumno" placeholder="Ingrese la clave del alumno">
              </div>
              <button type="submit" class="btn btn-success">Consultar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div> {{-- contenido principal encargado --}}
@endsection

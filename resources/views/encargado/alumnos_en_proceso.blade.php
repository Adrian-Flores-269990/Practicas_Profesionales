@extends('layouts.encargado')
@section('title','Alumnos en proceso de prácticas')

@section('content')

    <style>
      .navbar-nav .nav-link {
        margin-right: 18px;
        border-radius: 5px;
        transition: background-color 0.3s;
        padding-left: 16px;
        padding-right: 16px;
      }
      .navbar-nav .nav-link:hover {
        background-color: #004A98;
      }
    </style>

    {{-- Segunda navbar --}}
    <nav class="navbar" style="background-color: #000066;">
      <div class="container-fluid justify-content-center">
        <span class="navbar-text text-white mx-auto" style="font-weight: 500;">
          Lista de alumnos en proceso de prácticas profesionales
        </span>
      </div>
    </nav>
    {{--segunda navbar --}}

    <div class="container mt-4">
      <table class="table">
        <thead>
          <tr>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Clave</th>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Nombre</th>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Carrera</th>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Materia</th>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Solicitud</th>
            <th style="background-color: rgba(36, 207, 219, 0.25); color: #000; text-align: center; margin: 5px;">Estado</th>
          </tr>
        </thead>
        <tbody>
          {{-- Aquí irán los datos de los alumnos --}}
        </tbody>
      </table>
    </div>

@endsection
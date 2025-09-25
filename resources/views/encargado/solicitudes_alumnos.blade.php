@extends('layouts.encargado')
@section('title','Solicitudes de prácticas')

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
    <nav class="navbar navbar-expand-lg" style="background-color: #00499884;">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Estado</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Solicitud</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Reportes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Evaluación</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    {{-- Segunda navbar --}}
    <nav class="navbar" style="background-color: #000066;">
      <div class="container-fluid justify-content-center">
        <span class="navbar-text text-white mx-auto" style="font-weight: 500;">
          Lista de alumnos que solicitan prácticas profesionales
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

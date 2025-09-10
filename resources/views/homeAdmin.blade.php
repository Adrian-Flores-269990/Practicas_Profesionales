@extends('layouts.app')

@section('content')
<style>
  .admin-navbar {
  background-color: #f3f4f6;
  padding: 0.1rem 0;
}

.admin-navbar .navbar-nav {
  display: flex;
  justify-content: space-around;
  width: 100%;
}

.admin-navbar .nav-link {
  color: #374151;
  transition: background-color 0.2s ease;
}

.admin-navbar .nav-link:hover {
  background-color: #e5e7eb;
  color: #1e3a8a;
}

.dropdown-menu a {
  color: #374151;
}

.dropdown-menu a:hover {
  background-color: #f8fafc;
  color: #1e3a8a;
}

</style>

<!-- Menú de navegación -->
<nav class="admin-navbar navbar navbar-expand-lg">
  <div class="container justify-content-center">
    <ul class="navbar-nav" style="width: 100%; justify-content: space-around; display: flex;">

      <!-- Prácticas Profesionales -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="practicasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Prácticas Profesionales
        </a>
        <ul class="dropdown-menu" aria-labelledby="practicasDropdown">
          <li><a class="dropdown-item" href="#">Encargados de Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="#">Consultar Alumno</a></li>
          <li><a class="dropdown-item" href="#">Autorizaciones Pendientes</a></li>
          <li><a class="dropdown-item" href="#">Alumnos en Proceso de Prácticas</a></li>
          <li><a class="dropdown-item" href="#">Alumnos que Finalizaron Prácticas</a></li>
          <li><a class="dropdown-item" href="#">Registrar Nueva Empresa</a></li>
          <li><a class="dropdown-item" href="#">Estadísticas de la Empresa</a></li>
          <li><a class="dropdown-item" href="#">Generar Constancia de Validación</a></li>
          <li><a class="dropdown-item" href="#">Consultar Constancias de Validación</a></li>
          <li><a class="dropdown-item" href="#">Bitácora</a></li>
        </ul>
      </li>

      <!-- Proceso -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="procesoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Proceso
        </a>
        <ul class="dropdown-menu" aria-labelledby="procesoDropdown">
          <li><a class="dropdown-item" href="#">Diagrama de Proceso</a></li>
          <li><a class="dropdown-item" href="#">Proceso Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="#">Formato de Reporte (anexo 1)</a></li>
          <li><a class="dropdown-item" href="#">Detalles</a></li>
          <li><a class="dropdown-item" href="#">FAQs</a></li>
          <li><a class="dropdown-item" href="#">Estadísticas de Empresas</a></li>
        </ul>
      </li>

      <!-- Ayuda -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="ayudaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Ayuda
        </a>
        <ul class="dropdown-menu" aria-labelledby="ayudaDropdown">
          <li><a class="dropdown-item" href="#">Ayuda Alumno</a></li>
          <li><a class="dropdown-item" href="#">Ayuda Empleado</a></li>
        </ul>
      </li>

      <!-- Cerrar sesión -->
      <li class="nav-item">
        <a class="nav-link" href="#">Cerrar Sesión</a>
      </li>

    </ul>
  </div>
</nav>

<!-- Contenido principal -->
<div></div>
@endsection

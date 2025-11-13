@include('partials.modals_admin')

@php
  // Detectar si es admin
  $esAdmin = session('rol') === 'admin' || (Auth::user() && Auth::user()->rol === 'admin');
@endphp

<nav class="administrador-navbar navbar navbar-expand-lg">
  <div class="container justify-content-center">
    <ul class="nav" style="width: 100%; justify-content: space-around; display: flex;">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrador.inicio') }}">Inicio</a>
        </li>
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="practicasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Prácticas Profesionales
        </a>
        <ul class="dropdown-menu" aria-labelledby="practicasDropdown">
          <li><a class="dropdown-item" href="{{ route('administrador.encargados') }}">Encargados de Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="{{ route('administrador.consultar_alumno') }}">Consultar Alumno</a></li>
          <li><a class="dropdown-item" href="#">Autorizaciones Pendientes</a></li>
          <li><a class="dropdown-item" href="{{ route('administrador.empresas') }}">Empresas Registradas</a></li>
          <li><a class="dropdown-item" href="#">Estadísticas de Empresas</a></li>
          <li><a class="dropdown-item" href="{{ route('administrador.constancias') }}">Constancia de Validación</a></li>
          <li><a class="dropdown-item" href="{{ route('admin.bitacora') }}">Bitácora</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="procesoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Proceso
        </a>
        <ul class="dropdown-menu" aria-labelledby="procesoDropdown">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#diagrama">Diagrama de Proceso</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#proceso">Proceso Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modal">Formato de Reporte (anexo 1)</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detalles">Detalles</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#faq">FAQs</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="ayudaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Ayuda
        </a>
        <ul class="dropdown-menu" aria-labelledby="ayudaDropdown">
          <li><a class="dropdown-item" href="#">Ayuda Alumno</a></li>
          <li><a class="dropdown-item" href="#">Ayuda Empleado</a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a
          class="nav-link {{ request()->routeIs('administrador.empleados') ? 'active' : '' }}"
          href="{{ route('administrador.empleados') }}"
        >
          Modificar Roles
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('welcome') }}">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>

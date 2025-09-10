@extends('layouts.app')

@section('content')
<style>
  .info-alumno-wrapper {
    display: flex;
    justify-content: space-between;
    background-color: #dbeafe;
    padding: 0;
    align-items: center;
    font-size: 0.55rem;
    width: 100%
  }

  .foto-alumno {
    width: 170px;
    height: 93px;
    object-fit: contain;
    background-color: white;
  }

  .datos-izquierda {
    display: flex;
    flex-direction: column;
  }

  .campo-pareado {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.campo-label {
  background-color: #000066;
  color: white;
  font-weight: 700;
  padding: 0px 4px;
  flex: 0 0 25%;
  text-align: right;
}

.campo-valor {
  color: #03246f;
  padding: 0px 4px;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

  .bloque-datos {
  display: flex;
  flex-direction: row;
  width: 100%;
  max-width: 900px;
}

.datos-izquierda {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-width: 400px;
}

  .datos-derecha {
    display: flex;
    flex-direction: column;
    gap: 1px;
  }

  .campo-extra {
    display: flex;
    flex-direction: column;
  }

  .campo-extra .label {
  background-color: #000066;
  color: white;
  font-size: 0.6rem;
  text-align: center;
  padding: 1px 4px;
  width: 100%;
}

.campo-extra .valor {
  background-color: #dbeafe;
  color: #03246f;
  font-size: 0.6rem;
  padding: 0px 150px;
  text-align: center;
  width: 100%;
}

  .alumno-navbar {
    background-color: #f3f4f6;
    padding: 0.1rem 0;
  }

  .alumno-navbar .navbar-nav {
    display: flex;
    justify-content: space-around;
    width: 100%;
  }

  .alumno-navbar .nav-link:hover {
  background-color: #e5e7eb;
  color: #1e3a8a;
}
</style>

<!-- Información del alumno -->
<div class="container-fluid info-alumno-wrapper">
  <!-- Foto + Datos -->
  <div class="bloque-datos">
    <!-- Foto -->
    <div>
      <img src="{{ asset('images/perfil.webp') }}" alt="Foto del alumno" class="foto-alumno">
    </div>

    <!-- Datos del alumno -->
    <div class="datos-izquierda">
      <div class="campo-pareado">
        <div class="campo-label">Clave UASLP</div>
        <div class="campo-valor">XXXXXX</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Clave Ingeniería</div>
        <div class="campo-valor">XXXXXXXXXXXX</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Nombre</div>
        <div class="campo-valor">NOMBRE DEL ALUMNO</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Carrera</div>
        <div class="campo-valor">NOMBRE DE LA CARRERA</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Asesor</div>
        <div class="campo-valor">NOMBRE DEL ASESOR</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Ciclo escolar</div>
        <div class="campo-valor">CICLO ESCOLAR</div>
      </div>
      <div class="campo-pareado">
        <div class="campo-label">Semestre</div>
        <div class="campo-valor">SEMESTRE</div>
      </div>
    </div>
  </div>

  <!-- Datos extra -->
<div class="datos-derecha">
  <div class="campo-extra">
    <div class="label">Fecha</div>
    <div class="valor">25/02/2025</div>
  </div>
  <div class="campo-extra">
    <div class="label">Condición</div>
    <div class="valor">REGULAR</div>
  </div>
  <div class="campo-extra">
    <div class="label">Situación</div>
    <div class="valor">INSCRITO</div>
  </div>
</div>

</div>

<!-- Menú -->
<nav class="alumno-navbar navbar navbar-expand-lg">
  <div class="container justify-content-center">
    <ul class="navbar-nav">
  <!-- Registrarse -->
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="registrarseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Registrarse
    </a>
    <ul class="dropdown-menu" aria-labelledby="registrarseDropdown">
      <li><a class="dropdown-item" href="#">Inscribir Prácticas Profesionales I</a></li>
      <li><a class="dropdown-item" href="#">Inscribir Prácticas Profesionales II</a></li>
    </ul>
  </li>

  <!-- Expediente del Alumno -->
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="expedienteDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Expediente del Alumno
    </a>
    <ul class="dropdown-menu" aria-labelledby="expedienteDropdown">
      <li><a class="dropdown-item" href="#">Solicitud de Prácticas Profesionales (FPP01)</a></li>
      <li><a class="dropdown-item" href="#">Registro de Solicitu de Autorización (FPP02)</a></li>
      <li><a class="dropdown-item" href="#">Carta de Presentación</a></li>
      <li><a class="dropdown-item" href="#">Carta de Aceptación</a></li>
      <li><a class="dropdown-item" href="#">Carta de Desglose de Percepciones</a></li>
      <li><a class="dropdown-item" href="#">Solicitud de Recibo para Ayuda Económica</a></li>
      <li><a class="dropdown-item" href="#">Recibo de Pago</a></li>
      <li><a class="dropdown-item" href="#">Reportes Parciales</a></li>
      <li><a class="dropdown-item" href="#">Reporte Final</a></li>
      <li><a class="dropdown-item" href="#">Carta de Término</a></li>
      <li><a class="dropdown-item" href="#">Constancia de Validación de Prácticas Profesionles</a></li>
      <li><a class="dropdown-item" href="#">Otros Archivos</a></li>
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
  <li class="nav-item">
    <a class="nav-link" href="https://servicios.ing.uaslp.mx/secretaria_academica/practicas_prof/Ayuda/index.php">Ayuda</a>
  </li>

  <!-- Cerrar Sesión -->
  <li class="nav-item">
    <a class="nav-link" href="#">Cerrar Sesión</a>
  </li>
</ul>

  </div>
</nav>

<!-- Contenido principal -->
<div></div>
@endsection

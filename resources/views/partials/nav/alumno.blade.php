@include('partials.modals')

@php
  $alumno = session('alumno');
  $existe = \App\Models\SolicitudFPP01::where('Clave_Alumno', $alumno['cve_uaslp'])
            ->where('Autorizacion', 1)
            ->where('Apoyo_Economico', 1)
            ->count();
@endphp

<nav class="alumno-navbar navbar bg-light border-bottom mb-4">
  <div class="container justify-content-center">
    <ul class="nav">

      <li class="nav-item">
        <a class="nav-link" href="{{ route('alumno.inicio') }}">Inicio</a>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="registrarseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Registrarse
        </a>
        <ul class="dropdown-menu" aria-labelledby="registrarseDropdown">
          <li>
              <a class="dropdown-item"
                href="{{ route('alumno.guardarMateria', ['nivel' => 1]) }}">
                Inscribir Prácticas Profesionales I
              </a>
          </li>

          <li>
              <a class="dropdown-item"
                href="{{ route('alumno.guardarMateria', ['nivel' => 2]) }}">
                Inscribir Prácticas Profesionales II
              </a>
          </li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="expedienteDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Expediente del Alumno
        </a>
        <ul class="dropdown-menu" aria-labelledby="expedienteDropdown">
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.solicitudes') }}">Solicitud de Prácticas Profesionales (FPP01)</a></li>
          <li><a class="dropdown-item" href="#">Registro de Solicitud de Autorización (FPP02)</a></li>
          <li><a class="dropdown-item" href="{{ route('cartaPresentacion.mostrar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Carta_Presentacion']) }}">Carta de Presentación</a></li>
          <li><a class="dropdown-item" href="{{ route('cartaAceptacion.mostrar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Carta_Aceptacion']) }}">Carta de Aceptación</a></li>
          @if ($existe == true)
          <li><a class="dropdown-item" href="{{ route('desglosePercepciones.mostrar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Carta_Desglose_Percepciones']) }}">Carta de Desglose de Percepciones</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.ayudaEconomica') }}">Solicitud de Recibo para Ayuda Económica</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.reciboPago') }}">Recibo de Pago</a></li>
          @endif
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.reportesParciales') }}">Reportes Parciales</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.reporteFinal') }}">Reporte Final</a></li>
          <li><a class="dropdown-item" href="#">Carta de Término</a></li>
          <li><a class="dropdown-item" href="#">Constancia de Validación de Prácticas Profesionles</a></li>
          <li><a class="dropdown-item" href="#">Otros Archivos</a></li>
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
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#miModal">Estadísticas de Empresas</a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="https://servicios.ing.uaslp.mx/secretaria_academica/practicas_prof/Ayuda/index.php">Ayuda</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('welcome') }}">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>

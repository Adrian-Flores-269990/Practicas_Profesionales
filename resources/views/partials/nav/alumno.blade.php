@include('partials.modals')

@php
    $alumno = session('alumno');
    $clave = $alumno['cve_uaslp'];

    $estadoFPP01 = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
                ->value('estado');

    $estadoFPP02 = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)')
                ->value('estado');

    $estadoCartaAlumno = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE PRESENTACIÓN (ALUMNO)')
                ->value('estado');

    $estadoCartaAceptacion = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE ACEPTACIÓN (ALUMNO)')
                ->value('estado');

    $estadoCartaTermino = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE TÉRMINO')
                ->value('estado');

    $estadoDesglose = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CARTA DE DESGLOSE DE PERCEPCIONES')
                ->value('estado');

    $estadoAyudaEconomica = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                    ->where('etapa', 'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA')
                    ->value('estado');

    $estadoReciboPago = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                    ->where('etapa', 'RECIBO DE PAGO')
                    ->value('estado');

    $estadoReporteParcial = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'REPORTE PARCIAL')
                ->value('estado');

    $estadoReporteFinal = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                    ->where('etapa', 'REPORTE FINAL')
                    ->value('estado');

    $estadoConstancia = \App\Models\EstadoProceso::where('clave_alumno', $clave)
                ->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
                ->value('estado');

    $existe = \App\Models\SolicitudFPP01::where('Clave_Alumno', $clave)
                ->where('Autorizacion', 1)
                ->where('Apoyo_Economico', 1)
                ->count();
@endphp

<nav class="alumno-navbar navbar bg-light border-bottom mb-4">
  <div class="container justify-content-center">
    <ul class="nav">

      {{-- INICIO --}}
      <li class="nav-item">
        <a class="nav-link" href="{{ route('alumno.inicio') }}">Inicio</a>
      </li>

      {{-- REGISTRARSE --}}
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          Registrarse
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('alumno.guardarMateria', ['nivel'=>1]) }}">Inscribir Prácticas Profesionales I</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.guardarMateria', ['nivel'=>2]) }}">Inscribir Prácticas Profesionales II</a></li>
        </ul>
      </li>

      {{-- EXPEDIENTE --}}
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          Expediente del Alumno
        </a>

        <ul class="dropdown-menu">

          {{-- FPP01 --}}
          <li>
            <a class="dropdown-item" href="{{ route('alumno.expediente.solicitudes') }}">
              Solicitud de Prácticas Profesionales (FPP01)
            </a>
          </li>

          {{-- FPP02 --}}
          @if($estadoFPP01 === 'proceso' || $estadoFPP02 === 'pendiente' || $estadoFPP02 === 'proceso' || $estadoFPP02 === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('registroFPP02.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Solicitud_FPP02_Firmada']) }}">
                Registro de Solicitud de Autorización (FPP02)
              </a>
            </li>
          @endif

          {{-- CARTA PRESENTACIÓN --}}
          @if($estadoCartaAlumno === 'proceso' || $estadoCartaAlumno === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('cartaPresentacion.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Carta_Presentacion']) }}">
                Carta de Presentación
              </a>
            </li>
          @endif

          {{-- CARTA ACEPTACIÓN --}}
          @if($estadoCartaAceptacion === 'proceso' || $estadoCartaAceptacion === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('cartaAceptacion.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Carta_Aceptacion']) }}">
                Carta de Aceptación
              </a>
            </li>
          @endif

          {{-- DESGLOSE --}}
          @if($estadoDesglose === 'proceso' || $estadoDesglose === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('desglosePercepciones.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Carta_Desglose_Percepciones']) }}">
                Carta de Desglose de Percepciones
              </a>
            </li>
          @endif

          {{-- AYUDA ECONÓMICA --}}
          @if($estadoAyudaEconomica === 'proceso' || $estadoAyudaEconomica === 'realizado')
            <li>
              <a class="dropdown-item" href="{{ route('alumno.expediente.ayudaEconomica') }}">
                Solicitud de Recibo para Ayuda Económica
              </a>
            </li>
          @endif

          {{-- RECIBO --}}
          @if($estadoReciboPago === 'proceso' || $estadoReciboPago === 'realizado')
            <li>
              <a class="dropdown-item" href="{{ route('alumno.expediente.reciboPago') }}">
                Recibo de Pago
              </a>
            </li>
          @endif

          {{-- REPORTES PARCIALES --}}
          @if($estadoReporteParcial === 'proceso' || $estadoReporteParcial === 'realizado')
            <li>
              <a class="dropdown-item" href="{{ route('alumno.reportes.lista') }}">
                Reportes Parciales
              </a>
            </li>
          @endif

          {{-- CARTA DE TÉRMINO --}}
          @if($estadoCartaTermino === 'proceso' || $estadoCartaTermino === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('cartaTermino.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Carta_Termino']) }}">
                Carta de Término
              </a>
            </li>
          @endif

          {{-- CONSTANCIA DE FINALIZACION --}}
          @if($estadoConstancia === 'proceso' || $estadoConstancia === 'realizado')
            <li>
              <a class="dropdown-item"
                href="{{ route('constancia.mostrar', ['claveAlumno'=>$clave, 'tipo'=>'Constancia']) }}">
                Constancia de Finalización
              </a>
            </li>
          @endif

        </ul>
      </li>

      {{-- PROCESO --}}
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Proceso</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#diagrama">Diagrama de Proceso</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#proceso">Proceso Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modal">Formato de Reporte (anexo 1)</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detalles">Detalles</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#faq">FAQs</a></li>
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#miModal">Estadísticas de Empresas</a></li>
        </ul>
      </li>

      {{-- AYUDA --}}
      <li class="nav-item">
        <a class="nav-link" href="https://servicios.ing.uaslp.mx/secretaria_academica/practicas_prof/Ayuda/index.php">Ayuda</a>
      </li>

      {{-- LOGOUT --}}
      <li class="nav-item">
        <a class="nav-link" href="{{ route('welcome') }}">Cerrar Sesión</a>
      </li>

    </ul>
  </div>
</nav>

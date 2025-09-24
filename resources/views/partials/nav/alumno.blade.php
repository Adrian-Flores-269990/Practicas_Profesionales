<nav class="alumno-navbar navbar bg-light border-bottom mb-4">
  <div class="container justify-content-center">
    <ul class="nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="registrarseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Registrarse
        </a>
        <ul class="dropdown-menu" aria-labelledby="registrarseDropdown">
          <li><a class="dropdown-item" href="{{ route('alumno.estado') }}">Inscribir Prácticas Profesionales I</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.estado') }}">Inscribir Prácticas Profesionales II</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="expedienteDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Expediente del Alumno
        </a>
        <ul class="dropdown-menu" aria-labelledby="expedienteDropdown">
          <li><a class="dropdown-item" href="#">Solicitud de Prácticas Profesionales (FPP01)</a></li>
          <li><a class="dropdown-item" href="#">Registro de Solicitud de Autorización (FPP02)</a></li>
          <li><a class="dropdown-item" href="#">Carta de Presentación</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.cartaAceptacion') }}">Carta de Aceptación</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.desglosePercepciones') }}">Carta de Desglose de Percepciones</a></li>
          <li><a class="dropdown-item" href="#">Solicitud de Recibo para Ayuda Económica</a></li>
          <li><a class="dropdown-item" href="{{ route('alumno.expediente.reciboPago') }}">Recibo de Pago</a></li>
          <li><a class="dropdown-item" href="#">Reportes Parciales</a></li>
          <li><a class="dropdown-item" href="#">Reporte Final</a></li>
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
          <li><a class="dropdown-item" href="#">Diagrama de Proceso</a></li>
          <li><a class="dropdown-item" href="#">Proceso Prácticas Profesionales</a></li>
          <li><a class="dropdown-item" href="#">Formato de Reporte (anexo 1)</a></li>
          <li><a class="dropdown-item" href="#">Detalles</a></li>
          <li><a class="dropdown-item" href="#">FAQs</a></li>
          <li><a class="dropdown-item" href="#">Estadísticas de Empresas</a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="https://servicios.ing.uaslp.mx/secretaria_academica/practicas_prof/Ayuda/index.php">Ayuda</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="#">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>

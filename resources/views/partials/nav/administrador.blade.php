@include('partials.modals')

<nav class="administrador-navbar navbar navbar-expand-lg">
  <div class="container justify-content-center">
    <ul class="nav" style="width: 100%; justify-content: space-around; display: flex;">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.home') }}">Inicio</a>
        </li>
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
        <a class="nav-link" href="{{ route('welcome') }}">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>

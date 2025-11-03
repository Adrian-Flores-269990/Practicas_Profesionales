@include('partials.modals')

<nav class="encargado-navbar navbar navbar-expand-lg">
  <div class="container justify-content-center">
    <ul class="nav" style="width: 100%; justify-content: space-around; display: flex;">

    


      <li class="nav-item">
          <a class="nav-link" href="{{ route('encargado.inicio') }}">Inicio</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="practicasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Prácticas Profesionales
        </a>
        <ul class="dropdown-menu" aria-labelledby="practicasDropdown">
<<<<<<< HEAD
          <li><a class="dropdown-item" href="{{ route('encargado.consultar_alumno') }}">Consultar Alumno</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.solicitudes_alumnos') }}">Solicitudes Pendientes</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.alumnos_en_proceso') }}">Alumnos en Proceso de Prácticas</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.alumnos_finalizados') }}">Alumnos que terminaron de Prácticas</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.estadisticas_empresas') }}">Estadísticas de la Empresa</a></li>
          <li><a class="dropdown-item" href=" {{ route('encargado.registrar_empresa') }}">Registrar Nueva Empresa</a></li>
          <li><a class="dropdown-item" href="#">Consultar Reportes por Área</a></li>
=======
          <li><a class="dropdown-item" href="{{ route('encargado.home') }}">Consultar alumno</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.solicitudes_alumnos') }}">Solicitudes pendientes</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.alumnos_en_proceso') }}">Alumnos en proceso de prácticas</a></li>
          <li><a class="dropdown-item" href="#">Alumnos que finalizaron prácticas</a></li>
          <li><a class="dropdown-item" href="{{ route('encargado.estadisticas_empresas') }}">Estadísticas de las empresas</a></li>
          <li><a class="dropdown-item" href="#">Registrar nueva empresa</a></li>
          <li><a class="dropdown-item" href="#">Consultar reportes por área</a></li>
>>>>>>> a5308f025c34d51820a3bf5f20a77c865d89bdc1
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="procesoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Proceso
        </a>
        <ul class="dropdown-menu" aria-labelledby="procesoDropdown">
          <li><a class="dropdown-item" href="#">Detalles</a></li>
          <li><a class="dropdown-item" href="#">FAQs</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="ayudaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Ayuda
        </a>
        <ul class="dropdown-menu" aria-labelledby="ayudaDropdown">
          <li><a class="dropdown-item" href="#">Ayuda Empleado</a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('welcome') }}">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>

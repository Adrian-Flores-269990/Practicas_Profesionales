<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4 submenu-alumno">
    <div class="submenu-alumno">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.estado') ? 'active' : '' }}" href="{{ route('alumno.estado') }}">Estado</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.solicitud') ? 'active' : '' }}" href="{{ route('alumno.solicitud') }}">Solicitud</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.registro') ? 'active' : '' }}" href="{{ route('alumno.registro') }}">Registro</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.reporte') ? 'active' : '' }}" href="{{ route('alumno.reporte') }}">Nuevo Reporte</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.evaluacion') ? 'active' : '' }}" href="{{ route('alumno.evaluacion') }}">Evaluación</a>
            </li>
        </ul>
    </div>
</nav>

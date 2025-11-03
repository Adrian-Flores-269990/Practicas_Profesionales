@php
$claveAlumno = session('alumno')['cve_uaslp'] ?? null;

$ultimaSolicitud = \App\Models\SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                ->latest('Id_Solicitud_FPP01')
                ->first();

$bloqueado = false;

if ($ultimaSolicitud) {
    // ÚLTIMA solicitud está en proceso = bloquear
    if ($ultimaSolicitud->Estado_Departamento == 'pendiente' ||
        $ultimaSolicitud->Estado_Encargado == 'pendiente') {
        $bloqueado = true;
    }

    // ÚLTIMA solicitud aprobada por ambos = bloquear
    if ($ultimaSolicitud->Estado_Departamento == 'aprobado' &&
        $ultimaSolicitud->Estado_Encargado == 'aprobado') {
        $bloqueado = true;
    }

    // Si la última está rechazada → permitir
    if ($ultimaSolicitud->Estado_Departamento == 'rechazado' ||
        $ultimaSolicitud->Estado_Encargado == 'rechazado') {
        $bloqueado = false;
    }
}
@endphp


<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4 submenu-alumno">
    <div class="submenu-alumno">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.estado') ? 'active' : '' }}" href="{{ route('alumno.estado') }}">Estado</a>
            </li>
            <li class="nav-item">
                @if($bloqueado)
                    <a class="nav-link disabled text-secondary" style="pointer-events:none;">
                    Solicitud 🛑
                    </a>
                @else
                    <a class="nav-link {{ request()->routeIs('alumno.solicitud') ? 'active' : '' }}"
                    href="{{ route('alumno.solicitud') }}">
                    Solicitud
                    </a>
                @endif
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

@php
use App\Models\SolicitudFPP01;
use App\Models\EstadoProceso;

$claveAlumno = session('alumno')['cve_uaslp'] ?? null;

$ultimaSolicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
    ->latest('Id_Solicitud_FPP01')
    ->first();

// Estados de bloqueo por menú
$bloqueoSolicitud = false;
$bloqueoRegistro = true;
$bloqueoReporte = true;
$bloqueoEvaluacion = true;

if ($ultimaSolicitud) {
    $dep = $ultimaSolicitud->Estado_Departamento;
    $enc = $ultimaSolicitud->Estado_Encargado;

    // 🟥 BLOQUEO SOLICITUD
    // Si la solicitud está en proceso o aprobada → bloquear
    if (($dep == 'pendiente' || $enc == 'pendiente') ||
        ($dep == 'aprobado' && $enc == 'aprobado')) {
        $bloqueoSolicitud = true;
    }

    // Si fue rechazada → desbloquear
    if ($dep == 'rechazado' || $enc == 'rechazado') {
        $bloqueoSolicitud = false;
    }

    // 🟧 BLOQUEO REGISTRO
    // Se desbloquea si la solicitud fue aprobada por ambos
    if ($dep == 'aprobado' && $enc == 'aprobado') {
        $bloqueoRegistro = false;
    }

    // 🟨 BLOQUEO REPORTE
    // Se desbloquea si el registro fue aprobado o realizado
    $registro = EstadoProceso::where('clave_alumno', $claveAlumno)
        ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
        ->first();

    if ($registro && in_array($registro->estado, ['aprobado', 'realizado'])) {
        $bloqueoReporte = false;
    }

    // 🟩 BLOQUEO EVALUACIÓN
    // Se desbloquea si el reporte final fue aprobado
    $reporte = EstadoProceso::where('clave_alumno', $claveAlumno)
        ->where('etapa', 'REPORTE FINAL')
        ->first();

    if ($reporte && $reporte->estado === 'aprobado') {
        $bloqueoEvaluacion = false;
    }
}
@endphp


<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4 submenu-alumno">
    <div class="submenu-alumno">
        <ul class="nav">

            {{-- 🔹 Estado: siempre activo --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('alumno.estado') ? 'active' : '' }}" href="{{ route('alumno.estado') }}">
                    Estado
                </a>
            </li>

            {{-- 🔹 Solicitud --}}
            <li class="nav-item">
                @if($bloqueoSolicitud)
                    <a class="nav-link disabled text-secondary" style="pointer-events:none;">Solicitud</a>
                @else
                    <a class="nav-link {{ request()->routeIs('alumno.solicitud') ? 'active' : '' }}" href="{{ route('alumno.solicitud') }}">Solicitud</a>
                @endif
            </li>

            {{-- 🔹 Registro --}}
            <li class="nav-item">
                @if($bloqueoRegistro)
                    <a class="nav-link disabled text-secondary" style="pointer-events:none;">Registro</a>
                @else
                    <a class="nav-link {{ request()->routeIs('alumno.registro') ? 'active' : '' }}" href="{{ route('alumno.registro') }}">Registro</a>
                @endif
            </li>

            {{-- 🔹 Nuevo Reporte --}}
            <li class="nav-item">
                @if($bloqueoReporte)
                    <a class="nav-link disabled text-secondary" style="pointer-events:none;">Nuevo Reporte</a>
                @else
                    <a class="nav-link {{ request()->routeIs('alumno.reporte') ? 'active' : '' }}" href="{{ route('alumno.reporte') }}">Nuevo Reporte</a>
                @endif
            </li>

            {{-- 🔹 Evaluación --}}
            <li class="nav-item">
                @if($bloqueoEvaluacion)
                    <a class="nav-link disabled text-secondary" style="pointer-events:none;">Evaluación</a>
                @else
                    <a class="nav-link {{ request()->routeIs('alumno.evaluacion') ? 'active' : '' }}" href="{{ route('alumno.evaluacion') }}">Evaluación</a>
                @endif
            </li>

        </ul>
    </div>
</nav>

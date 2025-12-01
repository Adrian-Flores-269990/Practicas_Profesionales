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
    $registro = EstadoProceso::where('clave_alumno', $claveAlumno)
        ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
        ->first();

    if ($dep === 'aprobado' && $enc === 'aprobado') {
        // Si aún NO ha hecho el registro -> puede entrar
        if (!$registro || !in_array($registro->estado, ['realizado', 'aprobado'])) {
            $bloqueoRegistro = false;
        } else {
            // Si ya lo hizo -> se bloquea otra vez
            $bloqueoRegistro = true;
        }
    }

    // 🟨 BLOQUEO DE NUEVO REPORTE — LÓGICA UNIFICADA (PARCIAL Y FINAL)
    // === ESTADOS PARCIALES ===
    $estadoReporteParcial = EstadoProceso::estado($claveAlumno, 'REPORTE PARCIAL');
    $estadoRevisionParcial = EstadoProceso::estado($claveAlumno, 'REVISIÓN REPORTE PARCIAL');
    $estadoCorreccionParcial = EstadoProceso::estado($claveAlumno, 'CORRECCIÓN REPORTE PARCIAL');

    // === ESTADOS FINALES ===
    $estadoReporteFinal = EstadoProceso::estado($claveAlumno, 'REPORTE FINAL');
    $estadoRevisionFinal = EstadoProceso::estado($claveAlumno, 'REVISIÓN REPORTE FINAL');
    $estadoCorreccionFinal = EstadoProceso::estado($claveAlumno, 'CORRECCIÓN REPORTE FINAL');
    $estadoCalificacionFinal = EstadoProceso::estado($claveAlumno, 'CARTA DE TÉRMINO');


    // ------------------------------
    // 🔥 1) LÓGICA PARCIAL — SOLO SI AÚN NO SE HA LLEGADO AL REPORTE FINAL
    // ------------------------------

    $puedeParcial = false;

    if ($estadoReporteFinal === 'pendiente') {  // ⬅️ IMPORTANTE: si ya entró a FINAL, PARCIAL YA NO APLICA

        $puedeParcial =
            in_array($estadoReporteParcial, ['proceso', 'realizado']) &&
            $estadoRevisionParcial !== 'proceso';

        // Corrección parcial SÍ permite subir reporte
        if ($estadoCorreccionParcial === 'proceso') {
            $puedeParcial = true;
        }
    }


    // ------------------------------
    // 🔥 2) LÓGICA FINAL CORREGIDA
    // ------------------------------

    $puedeFinal = false;

    /*
    ✔ Puede subir SI:
    - REPORTE FINAL está en 'proceso' o 'realizado'
    - Y NO está en revisión final 'proceso'
    - Y NO está en calificación final 'proceso'
    - Corrección final sí permite
    */

    if (in_array($estadoReporteFinal, ['proceso', 'realizado'])) {
        $puedeFinal = true;
    }

    // Bloquea SOLO si está en revisión final → PROCESO
    if ($estadoRevisionFinal === 'proceso') {
        $puedeFinal = false;
    }

    // Calificación final SOLO bloquea si está en PROCESO
    if ($estadoCalificacionFinal === 'proceso') {
        $puedeFinal = false;
    }

    // Corrección sí desbloquea siempre
    if ($estadoCorreccionFinal === 'proceso') {
        $puedeFinal = true;
    }

    // ------------------------------
    // ✔ 3) REGLA FINAL: SI YA SE SUBIÓ CARTA DE TÉRMINO → BLOQUEAR REPORTE
    // ------------------------------
    if ($estadoCalificacionFinal === 'realizado') {
        $puedeParcial = false;
        $puedeFinal = false;
    }

    // ------------------------------
    // ✔ 4) RESULTADO FINAL DEFINITIVO
    // ------------------------------
    $bloqueoReporte = !($puedeParcial || $puedeFinal);

    // 🟩 BLOQUEO EVALUACIÓN — se desbloquea cuando la etapa EVALUACIÓN DE LA EMPRESA está en proceso
    $estadoEvaluacionEmpresa = EstadoProceso::estado($claveAlumno, 'EVALUACIÓN DE LA EMPRESA');

    // Desbloquear sólo cuando esté en proceso
    if ($estadoEvaluacionEmpresa === 'proceso') {
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
                    <a class="nav-link {{ request()->routeIs('registroFPP02.mostrar') ? 'active' : '' }}" href="{{ route('registroFPP02.mostrar', ['claveAlumno' => $claveAlumno, 'tipo' => 'Solicitud_FPP02_Firmada']) }}">Registro</a>
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

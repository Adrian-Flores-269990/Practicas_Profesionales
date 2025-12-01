@extends('layouts.encargado')

@section('title', 'Calificación Final del Alumno')

@push('styles')
<style>
    .header-title {
        background: #001a57;
        color: white;
        padding: 14px 0;
        text-align: center;
        font-size: 22px;
        font-weight: 600;
        border-radius: 6px;
    }

    .info-box {
        background: #f7f9fc;
        padding: 18px;
        border-radius: 10px;
        border: 1px solid #dce3ee;
    }

    .promedio-final {
        background: #003b82;
        color: white;
        font-size: 20px;
        padding: 12px;
        text-align: center;
        border-radius: 10px;
        font-weight: bold;
    }
</style>
@endpush


@section('content')

<div class="container mt-4">

    <!-- TÍTULO -->
    <div class="header-title mb-4">
        <i class="bi bi-award me-2"></i> CALIFICACIÓN FINAL DEL ALUMNO
    </div>

    <div class="row">

        <!-- COLUMNA IZQUIERDA -->
        <div class="col-md-4">

            <div class="info-box mb-3">
                <h5 class="fw-bold">{{ $alumno->Nombre }}</h5>

                <p class="mb-1"><strong>Clave:</strong> {{ $alumno->Clave_Alumno }}</p>
                <p class="mb-1"><strong>Materia:</strong> {{ $solicitud->Materia }}</p>
            </div>

            <!-- PROMEDIO FINAL -->
            <div class="promedio-final mb-4">
                PROMEDIO FINAL: {{ $promedio }}
            </div>

            <!-- FORMULARIOS INVISIBLES -->
            <form id="form-aprobar"
                method="POST"
                action="{{ route('encargado.calificacion.aprobar', $solicitud->Id_Solicitud_FPP01) }}">
                @csrf
            </form>

            <form id="form-rechazar"
                method="POST"
                action="{{ route('encargado.calificacion.rechazar', $solicitud->Id_Solicitud_FPP01) }}">
                @csrf
            </form>

            <!-- BOTONES DE ACCIÓN -->
            <div class="d-flex gap-3">

                <button onclick="aprobarAlumno()"
                        class="btn btn-success w-100">
                    <i class="bi bi-check-circle me-1"></i> Aprobar
                </button>

                <button onclick="rechazarAlumno()"
                        class="btn btn-danger w-100">
                    <i class="bi bi-x-circle me-1"></i> Rechazar
                </button>

            </div>

        </div>

        <!-- COLUMNA DERECHA (TABLA) -->
        <div class="col-md-8">

            <h5 class="fw-bold mb-2">Reportes enviados:</h5>
            <span class="badge bg-primary mb-3">{{ count($reportes) }}</span>

            <table class="table table-bordered table-striped align-middle shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>No. Reporte</th>
                        <th>Calificación</th>
                        <th>Archivo</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($reportes as $rep)
                    <tr>
                        <td class="fw-bold">
                            {{ $rep->Numero_Reporte == 100 ? 'Final' : $rep->Numero_Reporte }}
                        </td>

                        <td>
                            <span class="badge bg-primary fs-6">{{ $rep->Calificacion }}</span>
                        </td>

                        <td>
                            @if($rep->Nombre_Archivo)
                                <a target="_blank"
                                href="{{ '/storage/expedientes/reportes/' . $rep->Nombre_Archivo }}"
                                class="btn btn-sm btn-outline-danger">
                                PDF
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// CONFIRMAR APROBACIÓN
function aprobarAlumno() {
    Swal.fire({
        title: '¿Confirmar aprobación?',
        text: "El alumno pasará a la siguiente etapa.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-aprobar').submit();
        }
    });
}

// CONFIRMAR RECHAZO
function rechazarAlumno() {
    Swal.fire({
        title: '¿Rechazar calificación?',
        text: "El estado regresará a PENDIENTE.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-rechazar').submit();
        }
    });
}

</script>
@endpush

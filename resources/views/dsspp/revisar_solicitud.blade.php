@extends('layouts.dsspp')
@section('title', 'Revisión de solicitud - DSSPP')

@push('styles')
<style>
  .header-alumno {
    background: #007bff;
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .alumno-nombre {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .alumno-detalle {
    opacity: 0.95;
    font-size: 1rem;
  }

  .seccion-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
  }

  .seccion-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  }

  .seccion-header {
    background: #c3cfe2;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 1.05rem;
    color: #2d3748;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .seccion-header i {
    font-size: 1.2rem;
  }

  .seccion-header.aceptada {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
    color: white !important;
  }

  .seccion-header.rechazada {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
    color: white !important;
  }

  .seccion-body {
    padding: 1.5rem;
  }

  .dato-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .dato-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .dato-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .dato-valor {
    color: #2d3748;
    font-size: 1rem;
    font-weight: 500;
  }

  .divider {
    border: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #cbd5e0, transparent);
    margin: 1.5rem 0;
  }

  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
  }

  .btn-aceptar {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-aceptar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72,187,120,0.4);
    color: white;
  }

  .btn-rechazar {
    background: #f01a1aff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-rechazar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,101,101,0.4);
    color: white;
  }

  .btn-regresar {
    background: #888f9bff;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
  }

  .btn-regresar:hover {
    background: #4a5568;
    transform: translateY(-3px);
    color: white;
  }

  .pdf-link {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }

  .pdf-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66,153,225,0.4);
    color: white;
  }

  .comentarios-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-top: 2rem;
  }

  .status-icon {
    font-size: 1.1rem;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REVISIÓN DE SOLICITUD DE PRÁCTICAS PROFESIONALES
  </h4>

  <div class="container py-4">

    {{-- Header con datos del alumno --}}
    @php $alumno = $solicitud->alumno; @endphp
    <div class="header-alumno">
      <div class="alumno-nombre">
        <i class="bi bi-person-circle me-2"></i>
        {{ $alumno->Nombre ?? 'No disponible' }}
        {{ $alumno->ApellidoP_Alumno ?? '' }}
        {{ $alumno->ApellidoM_Alumno ?? '' }}
      </div>
      <div class="row alumno-detalle">
        <div class="col-md-4">
          <i class="bi bi-bookmark-fill me-1"></i>
          Clave: <strong>{{ $alumno->Clave_Alumno ?? 'N/A' }}</strong>
        </div>
        <div class="col-md-4">
          <i class="bi bi-mortarboard-fill me-1"></i>
          {{ $alumno->Carrera ?? 'N/A' }}
        </div>
        <div class="col-md-4">
          <i class="bi bi-calendar-check me-1"></i>
          Solicitud: {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud ?? now())->format('d/m/Y') }}
        </div>
      </div>
    </div>

    {{-- FORMULARIO --}}
    <form action="{{ route('dsspp.autorizarSolicitud', $solicitud->Id_Solicitud_FPP01) }}" method="POST" id="form-revision">
      @csrf
      @method('PUT')

      {{-- PREVIEW DEL PDF --}}
      @php
        $claveAlumno = $alumno->Clave_Alumno ?? null;
        $pdfPath = null;

        if ($claveAlumno) {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files('expedientes/carta-vigencia-derechos');
            $pdfs = collect($files)->filter(fn($f) => str_contains($f, '0'.$claveAlumno))->sortDesc();
            if ($pdfs->count() > 0) $pdfPath = $pdfs->first();
        }
      @endphp

      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          Carta de Vigencia de Derechos
        </div>
        <div class="seccion-body">
          @if($pdfPath)
            <h6 class="fw-bold mb-3">Documento subido:</h6>
            <iframe src="{{ asset('storage/' . $pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>
            <div class="d-flex gap-2 mt-3">
              <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="pdf-link">
                <i class="bi bi-box-arrow-up-right"></i> Abrir PDF en nueva pestaña
              </a>
            </div>
          @else
            <div class="alert alert-warning mb-0">
              <i class="bi bi-exclamation-triangle me-2"></i>
              No se encontró la carta de vigencia de derechos del alumno.
            </div>
          @endif
        </div>
      </div>

            {{-- FECHA DE ASIGNACIÓN --}}
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-calendar3"></i>
          Fecha de Asignación
        </div>
        <div class="seccion-body">
          <label for="Fecha_Asignacion" class="fw-bold mb-2">Seleccione la fecha de asignación:</label>
          <input 
              type="date" 
              id="Fecha_Asignacion" 
              name="Fecha_Asignacion" 
              class="form-control"
              required
          >
          <small class="text-muted mt-2 d-inline-block">
            * Este campo es obligatorio para aceptar o rechazar la solicitud.
          </small>
        </div>
      </div>

      {{-- BOTONES FINALES --}}
      <div class="mt-4 text-center d-flex gap-3 justify-content-center">
        <button type="button" id="btn-aceptar" class="btn-aceptar">
          <i class="bi bi-check-circle-fill me-2"></i>
          Aceptar
        </button>

        <button type="button" id="btn-rechazar" class="btn-rechazar">
          <i class="bi bi-x-circle-fill me-2"></i>
          Rechazar
        </button>

        <a href="{{ route('dsspp.solicitudes') }}" class="btn-regresar">
          <i class="bi bi-arrow-left me-2"></i>
          Regresar
        </a>
      </div>

      <input type="hidden" name="accion" id="accion" value="">
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btnAceptar = document.getElementById('btn-aceptar');
  const btnRechazar = document.getElementById('btn-rechazar');
  const form = document.getElementById('form-revision');
  const accionInput = document.getElementById('accion');

  btnAceptar.addEventListener('click', () => {
    accionInput.value = 'aceptar';
    form.submit();
  });

  btnRechazar.addEventListener('click', () => {
    accionInput.value = 'rechazar';
    form.submit();
  });
});
</script>
@endpush

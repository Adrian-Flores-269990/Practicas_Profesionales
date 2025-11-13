@extends('layouts.alumno')

@section('title', 'Confirmación de datos FPP02')

@push('styles')
<style>
  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
  }
  .info-row:last-child { border-bottom: none; }
  .info-label { font-weight: 500; color: #555; }
  .info-value { color: #222; }
  .btn-row {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }
  .btn-aceptar {
    background: #004795;
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: 0.2s;
  }
  .btn-aceptar:hover { background: #003b70; }
  .seccion-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
  }
  .seccion-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
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
  .seccion-header i { font-size: 1.2rem; }
  .seccion-body { padding: 1.5rem; }
  .dato-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  .dato-item { display: flex; flex-direction: column; gap: 0.5rem; }
  .dato-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .dato-valor { color: #2d3748; font-size: 1rem; font-weight: 500; }

  /* Estilos de la zona de subida */
  .zona-subida {
    border: 2px dashed #004795;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: 0.3s ease;
  }
  .zona-subida:hover { background: #eef3fb; }
  .btn-subir {
    background: #004795;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.6rem 1.2rem;
    cursor: pointer;
    transition: 0.3s;
  }
  .btn-subir:hover { background: #003b70; }
</style>
@endpush

@section('content')
@include('partials.nav.registro')

@php $alumno = $solicitud->alumno; @endphp

<div class="container mt-4">

  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REVISIÓN DE SOLICITUD DE PRÁCTICAS PROFESIONALES
  </h4>

  {{-- SECCIÓN DE INFORMACIÓN (solo visible si NO ha impreso aún) --}}
  <div id="info-section" class="{{ $mostrarUpload ? 'display:none;' : '' }}">
    {{-- Alumno --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-person-badge"></i> DATOS DE ALUMNO</div>
      <div class="seccion-body">
        <div class="info-row"><span class="info-label">Nombre:</span><span class="info-value">{{ $alumno->Nombre ?? '' }} {{ $alumno->ApellidoP_Alumno ?? '' }} {{ $alumno->ApellidoM_Alumno ?? '' }}</span></div>
        <div class="info-row"><span class="info-label">Clave UASLP:</span><span class="info-value">{{ $alumno->Clave_Alumno ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">Carrera:</span><span class="info-value">{{ $alumno->Carrera ?? 'No disponible' }}</span></div>
      </div>
    </div>

    {{-- Empresa --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-building"></i> DATOS DE LA EMPRESA</div>
      <div class="seccion-body">
        <div class="info-row"><span class="info-label">Nombre:</span><span class="info-value">{{ $empresa->Nombre_Depn_Emp ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">RFC:</span><span class="info-value">{{ $empresa->RFC_Empresa ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">Dirección:</span>
          <span class="info-value">
            {{ $empresa->Calle ?? '---' }} #{{ $empresa->Numero ?? '' }}, {{ $empresa->Colonia ?? '' }},
            {{ $empresa->Municipio ?? '' }}, {{ $empresa->Estado ?? '' }}, CP {{ $empresa->Cp ?? '' }}
          </span>
        </div>
        <div class="info-row"><span class="info-label">Teléfono:</span><span class="info-value">{{ $empresa->Telefono ?? 'No disponible' }}</span></div>
      </div>
    </div>

    {{-- Proyecto --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-clipboard-check"></i> DATOS DEL PROYECTO</div>
      <div class="seccion-body">
        <div class="info-row"><span class="info-label">Nombre del Proyecto:</span><span class="info-value">{{ $solicitud->Nombre_Proyecto ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">Área o Departamento:</span><span class="info-value">{{ $sector->Area_Depto ?? $empresa->Area_Depto ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">Horario:</span><span class="info-value">{{ $solicitud->Horario_Entrada ?? '---' }} - {{ $solicitud->Horario_Salida ?? '---' }}</span></div>
      </div>
    </div>

    {{-- Formulario que abre el PDF --}}
    <form id="form-imprimir" action="{{ route('alumno.generarFpp02') }}" method="POST" target="_blank" style="display:none;">
        @csrf
    </form>

    <div class="btn-row">
        <button type="button" id="btn-imprimir" class="btn-aceptar">Imprimir FPP02</button>
    </div>
  </div>

  {{-- SECCIÓN PARA SUBIR PDF FIRMADO (visible si YA imprimió) --}}
  <div id="upload-section" class="{{ $mostrarUpload ? '' : 'display:none;' }}">
    <div class="bg-white p-4 rounded shadow-sm w-100">
      <h5 class="fw-bold mb-4 text-center"><i class="bi bi-upload"></i> Subir formato FPP02 firmado</h5>

      {{-- Mostrar PDF subido si ya existe --}}
      @php
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? null;
        $pdfPath = null;
        if ($claveAlumno) {
          $files = \Illuminate\Support\Facades\Storage::disk('public')->files('expedientes/fpp02-firmados');
          $pdfs = collect($files)->filter(fn($f) => str_contains($f, $claveAlumno . '_FPP02_firmado'))->sortDesc();
          if ($pdfs->count() > 0) $pdfPath = $pdfs->first();
        }
      @endphp

      @if($pdfPath)
        <div class="mb-4">
          <h6 class="fw-bold">Documento subido:</h6>
          <iframe src="{{ asset('storage/' . $pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7;"></iframe>
          <div class="d-flex gap-2 mt-2">
            <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="btn btn-outline-primary">Abrir PDF</a>
            <form method="POST" action="{{ route('cartaAceptacion.eliminar', ['claveAlumno' => $claveAlumno]) }}">
              @csrf
              <input type="hidden" name="archivo" value="{{ $pdfPath }}">
              <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar el documento?')">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </form>
          </div>
        </div>
      @endif

      {{-- Mensajes de éxito y error --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Formulario de carga --}}
      <form method="POST" action="{{ route('alumno.subirFpp02Firmado') }}" enctype="multipart/form-data" id="form-fpp02">
        @csrf

        <div class="border rounded p-4 bg-light mb-4 text-center position-relative" id="zonaSubida">
          <div id="archivoInstrucciones">
            <i class="bi bi-cloud-upload display-6 text-muted"></i>
            <p class="text-muted">Arrastra y suelta tu PDF aquí o selecciónalo manualmente</p>
            <p class="small text-danger">Solo archivos PDF menores a 20 MB</p>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="botonSubir">Seleccionar archivo</button>
          </div>

          <input type="file" class="form-control d-none" id="archivoUpload" accept=".pdf" name="archivo" required>

          <div id="archivoPreview" class="mt-3 d-none">
            <div class="card border-primary shadow-sm">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1" id="archivoNombre"></h6>
                  <p class="mb-0 text-muted small" id="archivoTamaño"></p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnEliminarArchivo">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-danger" onclick="resetFormulario()">Cancelar</button>
          <button type="submit" class="btn btn-success">Subir PDF firmado</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btnImprimir = document.getElementById('btn-imprimir');
  const infoSection = document.getElementById('info-section');
  const uploadSection = document.getElementById('upload-section');
  const formImprimir = document.getElementById('form-imprimir');

  if (btnImprimir) {
    btnImprimir.addEventListener('click', () => {
      // 1) enviar el formulario al backend (se abre en pestaña nueva por target="_blank")
      formImprimir.submit();

      // 2) mostrar la sección de subida en la pestaña actual
      infoSection.style.display = 'none';
      uploadSection.style.display = 'block';
    });
  }

  // Lógica para subida
  const inputArchivo = document.getElementById('archivoUpload');
  const botonSubir = document.getElementById('botonSubir');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const preview = document.getElementById('archivoPreview');
  const nombreArchivo = document.getElementById('archivoNombre');
  const tamañoArchivo = document.getElementById('archivoTamaño');
  const btnEliminar = document.getElementById('btnEliminarArchivo');

  botonSubir.addEventListener('click', () => inputArchivo.click());

  inputArchivo.addEventListener('change', () => {
    if (inputArchivo.files.length > 0) {
      const file = inputArchivo.files[0];
      instrucciones.classList.add('d-none');
      preview.classList.remove('d-none');
      nombreArchivo.textContent = file.name;
      tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    }
  });

  btnEliminar.addEventListener('click', () => {
    inputArchivo.value = "";
    preview.classList.add('d-none');
    instrucciones.classList.remove('d-none');
  });
});

function resetFormulario() {
  const form = document.getElementById('form-fpp02');
  const preview = document.getElementById('archivoPreview');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const inputArchivo = document.getElementById('archivoUpload');

  form.reset();
  inputArchivo.value = "";
  preview.classList.add('d-none');
  instrucciones.classList.remove('d-none');
}
</script>
@endpush
@endsection

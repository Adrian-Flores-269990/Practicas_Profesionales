@extends('layouts.alumno')

@section('title','Carta de Término')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@push('styles')
<style>
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
    background: #17a2b8;
    color: white;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .seccion-header i {
    font-size: 1.2rem;
  }

  .seccion-body {
    padding: 1.5rem;
  }

  /* Botones estilo revisar_solicitud */
  .btn-open-pdf {
    background: #17a2b8;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    text-decoration: none;
  }

  .btn-open-pdf:hover {
    background: #138496;
    color: white;
    transform: translateX(4px);
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

  .btn-success-enviar {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-success-enviar:hover {
    background: #1a6f42;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31,137,80,0.4);
    color: white;
  }

  .btn-cancelar {
    background: #f01a1aff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-cancelar:hover {
    background: #c81717;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(240,26,26,0.4);
    color: white;
  }

  .btn-guardar {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-guardar:hover {
    background: #5a6268;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108,117,125,0.4);
  }

  /* Zona de subida */
  .drop-zone {
    border: 2px dashed #004795;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: 0.3s ease;
  }

  .drop-zone:hover {
    background: #eef3fb;
  }

  .drop-zone.dragover {
    background: #e3f2fd;
    border-color: #1976d2;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <!-- Header -->
  <div class="detalle-header">
    <div class="container">
      <h4 class="text-center">
        <i class="bi bi-file-earmark-text me-2"></i>
        CARTA DE TÉRMINO
      </h4>
    </div>
  </div>

  <div class="container py-4">
    {{-- Mensajes de éxito --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Mensajes de error --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Mostrar PDF subido si existe --}}
    @php
      $alumno = session('alumno');
      $claveAlumno = $alumno['Clave_Alumno'] ?? ($alumno['cve_uaslp'] ?? null);
    @endphp

    @if($pdfPath)
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          Carta de Término
        </div>
        <div class="seccion-body">
          <iframe src="{{ asset($pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>

          <div class="d-flex gap-2 mt-2">
            <a href="{{ asset($pdfPath) }}" target="_blank" class="btn-open-pdf">
              <i class="bi bi-box-arrow-up-right"></i>
              Abrir en nueva pestaña
            </a>
          </div>
        </div>
      </div>

      {{-- BOTONES FINALES - Fuera del PDF --}}
      <div class="mt-4 text-center d-flex gap-3 justify-content-center">
        <button type="button" onclick="if(confirm('¿Seguro que deseas eliminar el documento actual?')) { document.getElementById('formEliminarPDF').submit(); }"
          class="btn-rechazar">
          <i class="bi bi-trash me-2"></i>
          Eliminar PDF
        </button>

        <form id="formEliminarPDF" method="POST"
              action="{{ route('cartaTermino.eliminar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Carta_Termino']) }}"
              style="display: none;">
          @csrf
          <input type="hidden" name="archivo" value="{{ $pdfPath }}">
        </form>
      </div>

    @else
      <div class="seccion-card">
        <div class="seccion-header">
          <i class="bi bi-cloud-upload"></i>
          SUBIR DOCUMENTO
        </div>
        <div class="seccion-body">
          <form method="POST" action="{{ route('cartaTermino.upload') }}" enctype="multipart/form-data" id="form-reporte">
            @csrf

            {{-- Área de envío de archivo --}}
            <div class="mb-4">
              <h6 class="fw-bold mb-3">
                <i class="bi bi-upload"></i> Subir documento emitido por la empresa
              </h6>

              <div class="drop-zone" style="min-height: 180px;" id="zonaSubida">
                <div id="archivoInstrucciones">
                  <div class="mb-2">
                    <i class="bi bi-cloud-upload display-6 text-muted"></i>
                  </div>
                  <p class="text-muted">Arrastre y suelte los archivos aquí para subirlos</p>
                  <p class="small text-danger">Archivos de tamaño igual o menor a 20MB, únicamente archivos en PDF</p>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="botonSubir">Seleccionar Archivos</button>
                </div>
              </div>

              <input type="file" class="form-control d-none" id="archivoUpload" accept=".pdf" name="archivo" required>

              {{-- Vista previa del archivo seleccionado --}}
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

            {{-- BOTONES FINALES - Estilo revisar_solicitud --}}
            <div class="mt-4 text-center d-flex gap-3 justify-content-center">
              <button type="button" class="btn-guardar">
                <i class="bi bi-save me-2"></i>
                Guardar cambios
              </button>

              <button type="button" class="btn-cancelar" onclick="resetFormulario()">
                <i class="bi bi-x-circle me-2"></i>
                Cancelar
              </button>

              <button type="submit" class="btn-success-enviar">
                <i class="bi bi-send me-2"></i>
                Enviar
              </button>
            </div>
          </form>
        </div>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputArchivo = document.getElementById('archivoUpload');
  const botonSubir = document.getElementById('botonSubir');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const preview = document.getElementById('archivoPreview');
  const nombreArchivo = document.getElementById('archivoNombre');
  const tamañoArchivo = document.getElementById('archivoTamaño');
  const btnEliminar = document.getElementById('btnEliminarArchivo');
  const zonaSubida = document.getElementById('zonaSubida');

  // Mostrar input al hacer clic en el botón
  if (botonSubir) {
    botonSubir.addEventListener('click', () => {
      inputArchivo.click();
    });
  }

  // Cuando se selecciona un archivo
  if (inputArchivo) {
    inputArchivo.addEventListener('change', () => {
      if (inputArchivo.files.length > 0) {
        const file = inputArchivo.files[0];

        instrucciones.classList.add('d-none');
        preview.classList.remove('d-none');
        nombreArchivo.textContent = file.name;
        tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        zonaSubida.classList.add('d-none');
      }
    });
  }

  // Cambiar estilo cuando se arrastra un archivo encima
  if (zonaSubida) {
    zonaSubida.addEventListener("dragover", (e) => {
      e.preventDefault();
      zonaSubida.classList.add("dragover");
    });

    zonaSubida.addEventListener("dragleave", () => {
      zonaSubida.classList.remove("dragover");
    });

    // Capturar archivo arrastrado
    zonaSubida.addEventListener("drop", (e) => {
      e.preventDefault();
      zonaSubida.classList.remove("dragover");

      const file = e.dataTransfer.files[0];
      if (!file) return;

      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      inputArchivo.files = dataTransfer.files;

      instrucciones.classList.add('d-none');
      preview.classList.remove('d-none');
      nombreArchivo.textContent = file.name;
      tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
      zonaSubida.classList.add('d-none');
    });
  }

  // Eliminar archivo
  if (btnEliminar) {
    btnEliminar.addEventListener('click', () => {
      inputArchivo.value = "";
      preview.classList.add('d-none');
      instrucciones.classList.remove('d-none');
      zonaSubida.classList.remove('d-none');
    });
  }
});

// Cancelar: limpia todo el formulario
function resetFormulario() {
  const form = document.getElementById('form-reporte');
  const inputArchivo = document.getElementById('archivoUpload');
  const preview = document.getElementById('archivoPreview');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const zonaSubida = document.getElementById('zonaSubida');

  if (form) form.reset();
  if (inputArchivo) inputArchivo.value = "";
  if (preview) preview.classList.add('d-none');
  if (instrucciones) instrucciones.classList.remove('d-none');
  if (zonaSubida) zonaSubida.classList.remove('d-none');
}
</script>
@endpush
@endsection

@extends('layouts.alumno')

@section('title', 'Confirmación de datos FPP02')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@push('styles')
<style>
  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .info-label {
    font-weight: 500; color: #555;
  }

  .info-value {
    color: #222;
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
    background: #17a2b8;
    color: white;
    padding: 1.25rem 2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.05rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .seccion-header i {
    font-size: 1.2rem;
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

  /* Estilos de la zona de subida */
  .zona-subida {
    border: 2px dashed #004795;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: 0.3s ease;
  }

  .zona-subida:hover {
    background: #eef3fb;
  }

  .btn-subir {
    background: #004795;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.6rem 1.2rem;
    cursor: pointer;
    transition: 0.3s;
  }

  .btn-subir:hover {
    background: #003b70;
  }

  /* Botones estilo revisar_solicitud */
  .btn-imprimir {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-imprimir:hover {
    background: #1f8950ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31,137,80,0.4);
    color: white;
  }

  .btn-volver {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-volver:hover {
    background: #5a6268;
    color: white;
    transform: translateX(-4px);
  }

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

</style>
@endpush


@section('content')
@include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
  <!-- Header -->
  <div class="detalle-header">
    <div class="container">
      <h4 class="text-center">
        <i class="bi bi-file-earmark-text me-2"></i>
        REVISIÓN DE SOLICITUD DE PRÁCTICAS PROFESIONALES
      </h4>
    </div>
  </div>
</div>

<div class="container py-4">
  {{-- SECCIÓN DE INFORMACIÓN (solo visible si NO ha impreso aún) --}}
  <div id="info-section" style="{{ $mostrarUpload ? 'display:none;' : '' }}">

    {{-- Alumno --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-person-badge"></i> DATOS DE ALUMNO</div>
      <div class="seccion-body">
        <div class="info-row"><span class="info-label">Nombre:</span><span class="info-value">{{ $alumno->Nombre ?? '' }} {{ $alumno->ApellidoP_Alumno ?? '' }} {{ $alumno->ApellidoM_Alumno ?? '' }}</span></div>
        <div class="info-row"><span class="info-label">Clave UASLP:</span><span class="info-value">{{ $alumno->Clave_Alumno ?? 'No disponible' }}</span></div>
        <div class="info-row"><span class="info-label">Carrera:</span><span class="info-value">{{ $alumno->Carrera ?? 'No disponible' }}</span></div>
      </div>
    </div>

    {{-- De la Asignación del Departamento de Servicio Social y Prácticas Profesionales --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-calendar-check"></i> DE LA ASIGNACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (DSSPP)</div>
      <div class="seccion-body">
        <p class="fw-bold mb-2">Periodo de Prácticas Profesionales asignado por el DSSPP</p>
        <div class="info-row"><span class="info-label">Periodo:</span><span class="info-value">{{ $solicitud['Fecha_Inicio'] ?? '---' }} - {{ $solicitud['Fecha_Termino'] ?? '---' }}</span></div>
        <p class="fw-bold mb-2 mt-3">Jornada Laboral</p>
        <div class="info-row"><span class="info-label">Días de la Semana:</span><span class="info-value">{{ $solicitud['Dias_Semana'] ?? '' }}</span></div>
        <div class="info-row"><span class="info-label">Horario:</span><span class="info-value">{{ $solicitud->Horario_Entrada ?? '---' }} - {{ $solicitud->Horario_Salida ?? '---' }}</span></div>
      </div>
    </div>

    {{-- Empresa --}}
    <div class="seccion-card">
      <div class="seccion-header"><i class="bi bi-building"></i> DATOS DE LA EMPRESA</div>
      <div class="seccion-body">
        <div class="info-row"><span class="info-label">Nombre:</span><span class="info-value">{{ $empresa->Nombre_Depn_Emp ?? 'No disponible' }}</span></div>
        @if($tipoSector === 'privado')
          <div class="info-row"><span class="info-label">Razón Social:</span><span class="info-value">{{ $sector->Razon_Social ?? 'No disponible' }}</span></div>
        @endif
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
      </div>
    </div>

    {{-- Formulario oculto para generar el PDF --}}
    <form id="formulario-registro" action="{{ route('alumno.generarFpp02') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Datos faltantes --}}
      <div class="seccion-card">
        <div class="seccion-header"><i class="bi bi-file-earmark-check"></i> REQUISITOS</div>
        <div class="seccion-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-bold">Servicio Social en el mismo periodo</label>
              <div class="d-flex gap-3 mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="ss" id="ind-s" value="si" required>
                  <label class="form-check-label" for="ind-s">SI</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="ss" id="ind-n" value="no" required>
                  <label class="form-check-label" for="ind-n">NO</label>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Número de Meses</label>
              <input type="text" name="num_meses" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Total de Horas</label>
              <input type="text" name="total_horas" class="form-control" required>
            </div>
          </div>
        </div>
      </div>

      {{-- BOTONES FINALES - Estilo revisar_solicitud --}}
      <div class="mt-4 text-center d-flex gap-3 justify-content-center">
        <button type="button" id="btn-imprimir" class="btn-imprimir">
          <i class="bi bi-printer me-2"></i>
          Generar y Descargar FPP02
        </button>
      </div>
    </form>
  </div>

  {{-- SECCIÓN PARA SUBIR PDF FIRMADO (visible si YA imprimió) --}}
  <div id="upload-section" style="{{ $mostrarUpload ? '' : 'display:none;' }}">
    <div class="seccion-card">
      <div class="seccion-header">
        <i class="bi bi-file-earmark-pdf-fill"></i>
               DOCUMENTO FIRMADO
      </div>
      <div class="seccion-body">



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

        {{-- Mostrar PDF subido si ya existe --}}
        @php
          $alumno = session('alumno');
          $claveAlumno = $alumno['cve_uaslp'] ?? null;
        @endphp

        @if($pdfPath)
          <div class="mb-4">
            <iframe src="{{ asset($pdfPath) }}" width="100%" height="500px" style="border:1px solid #4583B7; border-radius:8px;"></iframe>

            <div class="d-flex gap-2 mt-2">
              <a href="{{ asset($pdfPath) }}" target="_blank" class="btn-open-pdf">
                <i class="bi bi-box-arrow-up-right"></i>
                Abrir en nueva pestaña
              </a>
            </div>
          </div>

          {{-- BOTONES FINALES - Fuera del PDF --}}
          <div class="mt-4 text-center d-flex gap-3 justify-content-center">

            <button type="button" onclick="event.preventDefault(); document.getElementById('formEliminarPDF').submit();"
              class="btn-rechazar">
              <i class="bi bi-trash me-2"></i>
              Eliminar PDF
            </button>

            <button type="button" id="btn-volver-desde-pdf" class="btn-volver">
              <i class="bi bi-arrow-left me-2"></i>
              Volver a la vista previa
            </button>

            <form id="formEliminarPDF" method="POST"
                  action="{{ route('registroFPP02.eliminar', ['claveAlumno' => $alumno['cve_uaslp'], 'tipo' => 'Solicitud_FPP02_Firmada']) }}"
                  style="display: inline;">
              @csrf
              <input type="hidden" name="archivo" value="{{ $pdfPath }}">
            </form>
          </div>

        @else
          <form method="POST" action="{{ route('registroFPP02.upload') }}" enctype="multipart/form-data" id="form-reporte">
            @csrf

            {{-- Área de envío de archivo --}}
            <div class="mb-4">
              <h6 class="fw-bold mb-3">
                <i class="bi bi-upload"></i> Subir documento firmado por la empresa
              </h6>

              <div class="border rounded border-dashed p-4 text-center bg-white position-relative" style="min-height: 180px; border: 2px dashed #004795;" id="zonaSubida">
                <div id="archivoInstrucciones">
                  <div class="mb-2">
                    <i class="bi bi-cloud-upload display-6 text-muted"></i>
                  </div>
                  <p class="text-muted">Arrastre y suelte los archivos aquí para subirlos</p>
                  <p class="small text-danger">Archivos de tamaño igual o menor a 20MB, únicamente archivos en PDF</p>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="botonSubir">Seleccionar Archivos</button>
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
        @endif

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btnImprimir = document.getElementById('btn-imprimir');
  const infoSection = document.getElementById('info-section');
  const uploadSection = document.getElementById('upload-section');
  const formImprimir = document.getElementById('formulario-registro');
  const btnVolverPreview = document.getElementById('btn-volver-preview');

  // Validar y enviar formulario
  if (btnImprimir && formImprimir) {
    btnImprimir.addEventListener('click', () => {
      const requiredFields = formImprimir.querySelectorAll('[required]');
      let isValid = true;

      // Validar campos de texto
      requiredFields.forEach(field => {
        if (field.type !== 'radio' && !field.value.trim()) {
          isValid = false;
          field.classList.add('is-invalid');
        } else {
          field.classList.remove('is-invalid');
        }
      });

      // Validar radios por grupo
      const radioGroups = {};
      requiredFields.forEach(field => {
        if (field.type === 'radio') {
          if (!radioGroups[field.name]) {
            radioGroups[field.name] = false;
          }
          if (field.checked) {
            radioGroups[field.name] = true;
          }
        }
      });

      Object.values(radioGroups).forEach(isChecked => {
        if (!isChecked) isValid = false;
      });

      if (isValid) {
        formImprimir.submit();
        infoSection.style.display = 'none';
        uploadSection.style.display = 'block';
      } else {
        alert('Por favor completa todos los campos requeridos antes de continuar.');
      }
    });
  }

  // Botón para volver a la vista previa (desde el PDF subido)
  const btnVolverDesdePdf = document.getElementById('btn-volver-desde-pdf');
  if (btnVolverDesdePdf) {
    btnVolverDesdePdf.addEventListener('click', () => {
      uploadSection.style.display = 'none';
      infoSection.style.display = 'block';
    });
  }

  // Lógica para subida de archivos
  const inputArchivo = document.getElementById('archivoUpload');
  const botonSubir = document.getElementById('botonSubir');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const preview = document.getElementById('archivoPreview');
  const nombreArchivo = document.getElementById('archivoNombre');
  const tamañoArchivo = document.getElementById('archivoTamaño');
  const btnEliminar = document.getElementById('btnEliminarArchivo');
  const zonaSubida = document.getElementById('zonaSubida');

  if (botonSubir) {
    botonSubir.addEventListener('click', () => inputArchivo.click());
  }

  if (inputArchivo) {
    inputArchivo.addEventListener('change', () => {
      if (inputArchivo.files.length > 0) {
        const file = inputArchivo.files[0];
        instrucciones.classList.add('d-none');
        preview.classList.remove('d-none');
        nombreArchivo.textContent = file.name;
        tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
      }
    });
  }

  if (btnEliminar) {
    btnEliminar.addEventListener('click', () => {
      inputArchivo.value = "";
      preview.classList.add('d-none');
      instrucciones.classList.remove('d-none');
    });
  }

  // Drag and drop
  if (zonaSubida) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      zonaSubida.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      zonaSubida.addEventListener(eventName, () => {
        zonaSubida.classList.add('border-primary', 'bg-light');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      zonaSubida.addEventListener(eventName, () => {
        zonaSubida.classList.remove('border-primary', 'bg-light');
      }, false);
    });

    zonaSubida.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;

      if (files.length > 0 && files[0].type === 'application/pdf') {
        inputArchivo.files = files;
        const event = new Event('change');
        inputArchivo.dispatchEvent(event);
      }
    }, false);
  }

  // Reset del formulario
  window.resetFormulario = function() {
    const form = document.getElementById('form-reporte');
    const preview = document.getElementById('archivoPreview');
    const instrucciones = document.getElementById('archivoInstrucciones');
    const inputArchivo = document.getElementById('archivoUpload');

    if (form) form.reset();
    if (inputArchivo) inputArchivo.value = "";
    if (preview) preview.classList.add('d-none');
    if (instrucciones) instrucciones.classList.remove('d-none');
  }
});
</script>
@endpush
@endsection

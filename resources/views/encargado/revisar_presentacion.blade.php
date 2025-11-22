@extends('layouts.encargado')
@section('title','Revisión de Carta de Presentación')

@push('styles')
<style>
  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
  }

  .btn-aceptar {
    background: #1f8950;
    color: white;
    padding: 0.7rem 1.6rem;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    transition: 0.3s;
  }
  .btn-aceptar:hover {
    background: #166b3d;
  }

  .btn-rechazar {
    background: #d93030;
    color: white;
    padding: 0.7rem 1.6rem;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    transition: 0.3s;
  }
  .btn-rechazar:hover {
    background: #b82121;
  }

  .btn-volver {
    background: #6c757d;
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    border: none;
  }
</style>
@endpush

@section('content')
<div class="container-fluid my-0 p-0">

  <h4 class="text-center fw-bold text-white py-3" style="background-color:#000066;">
    REVISAR CARTA DE PRESENTACIÓN (ENCARGADO)
  </h4>

  <div class="bg-white p-4 rounded shadow-sm">

    <h5 class="fw-bold mb-3">
      Alumno: {{ $alumno->Nombre }} {{ $alumno->ApellidoP_Alumno }} ({{ $claveAlumno }})
    </h5>

    @if($pdfPath)
      <iframe src="{{ asset($pdfPath) }}" 
              style="border:1px solid #4361ee; width:100%; height:900px;"></iframe>

      <div class="mt-3 text-center">
        <a href="{{ asset($pdfPath) }}" target="_blank" class="btn btn-primary">
          Abrir en nueva pestaña
        </a>
      </div>

      {{-- FORMULARIO DE ACCIONES --}}
      <form action="{{ route('encargado.cartaPresentacion.accion') }}" method="POST">
        @csrf
        <input type="hidden" name="claveAlumno" value="{{ $claveAlumno }}">

        <div class="action-buttons">

          <button name="accion" value="aprobar" class="btn-aceptar">
            Aceptar
          </button>

          <button name="accion" value="rechazar" class="btn-rechazar">
            Rechazar
          </button>

          <a href="{{ route('encargado.cartasPresentacion') }}" class="btn-volver">
            ← Volver al listado
          </a>

        </div>
      </form>

    @else
      <div class="alert alert-danger text-center">
        No existe archivo de carta generado.
      </div>
    @endif

  </div>
</div>
@endsection


@push('scripts')
<script>
  const inputArchivo = document.getElementById('archivoUpload');
  const botonSubir = document.getElementById('botonSubir');
  const instrucciones = document.getElementById('archivoInstrucciones');
  const preview = document.getElementById('archivoPreview');
  const nombreArchivo = document.getElementById('archivoNombre');
  const tamañoArchivo = document.getElementById('archivoTamaño');
  const btnEliminar = document.getElementById('btnEliminarArchivo');
  const zonaSubida = document.getElementById('zonaSubida');

  // Mostrar input al hacer clic en el botón
  botonSubir.addEventListener('click', () => {
    inputArchivo.click();
  });

  // Cuando se selecciona un archivo
  inputArchivo.addEventListener('change', () => {
    if (inputArchivo.files.length > 0) {
      const file = inputArchivo.files[0];

      // Oculta instrucciones y muestra preview
      instrucciones.classList.add('d-none');
      preview.classList.remove('d-none');
      nombreArchivo.textContent = file.name;
      tamañoArchivo.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    }
  });
</script>
@endpush

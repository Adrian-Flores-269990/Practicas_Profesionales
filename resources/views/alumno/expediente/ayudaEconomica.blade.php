@extends('layouts.alumno')

@section('title','Solicitud de Recibo para Ayuda Económica')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">

@section('content')
<div class="container-fluid my-0 p-0">
    <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA
            </h4>
        </div>
    </div>

  <div class="bg-white p-4 rounded shadow-sm w-100">
    <form id="form-recibo" method="POST" action="{{ route('recibo.descargar') }}">
      @csrf

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Nombre del alumno:</label>
          <input type="text" name="nombre" class="form-control" value="ACEVEDO GOLDARACENA FERNANDO" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Carrera:</label>
          <input type="text" name="carrera" class="form-control" value="INGENIERÍA CIVIL" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Clave del alumno:</label>
          <input type="text" name="clave" class="form-control" value="194659" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Fecha de solicitud:</label>
          <input type="date" name="fecha" class="form-control" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Periodo:</label>
          <input type="text" name="periodo" class="form-control" placeholder="DD/MM/AA a DD/MM/AA" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Cantidad:</label>
          <input type="number" step="0.01" name="cantidad" class="form-control" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Nombre de la empresa:</label>
        <input type="text" name="empresa" class="form-control" value="TANGAMANGA" required>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Persona que autoriza:</label>
          <input type="text" name="autoriza" class="form-control" value="LUIS FLORES" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Teléfono (Empresa):</label>
          <input type="text" name="telefono_empresa" class="form-control" value="800 706 1100" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Cargo:</label>
          <input type="text" name="cargo" class="form-control" value="RECURSOS HUMANOS" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Teléfono (Alumno):</label>
          <input type="text" name="telefono_alumno" class="form-control" value="444 506 7895" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Fecha de entrega:</label>
          <input type="date" name="fecha_entrega" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">¿Por parte de quién tiene el seguro?</label>
          <input type="text" name="seguro" class="form-control" required>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary">Guardar</button>
        <button type="reset" class="btn btn-danger">Cancelar</button>
        <button type="submit" class="btn btn-success">Enviar</button>
      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script>
  document.getElementById('form-recibo').addEventListener('submit', function (e) {
    let valid = true;
    let inputs = this.querySelectorAll('input');

    inputs.forEach(input => {
      if (input.value.trim() === '') {
        input.classList.add('is-invalid');
        valid = false;
      } else {
        input.classList.remove('is-invalid');
      }
    });

    if (!valid) {
      e.preventDefault();
      alert('Por favor, llena todos los campos antes de enviar.');
    }
  });
</script>
@endsection

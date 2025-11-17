@extends('layouts.alumno')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@section('title','Evaluación de la Empresa')

@section('content')
@include('partials.nav.registro')


<div class="container-fluid my-0 p-0">
    <!-- Header -->
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                EVALUACIÓN DE LA EMPRESA POR EL ALUMNO
            </h4>
        </div>
    </div>
  <div class="bg-white p-4 rounded shadow-sm w-100">
    <form id="form-evaluacion">
      {{-- Datos Generales con fondo gris clarito --}}
      <div class="bg-light p-3 rounded mb-4">
        <div class="mb-3">
          <label class="form-label fw-bold">Nombre de la empresa:</label>
          <p class="form-control-plaintext">FPP01</p>
        </div>
        <div>
          <label class="form-label fw-bold">Actividad principal que realizó el Alumno:</label>
          <p class="form-control-plaintext">FPP01</p>
        </div>
      </div>

      {{-- Escala --}}
      <div class="mb-2">
        <span class="fw-semibold text-danger">Escala de 1 a 4 (1: Ninguna, 2: Poca, 3: Regular, 4: Mucha)</span>
      </div>

      {{-- Preguntas --}}
      @php
        $preguntas = [
          'Relación de la Actividad con la carrera',
          'Interacción con el tutor de la empresa',
          'Asesoría por parte del tutor de la empresa',
          'Asesoría por parte de la dirección de la empresa',
          'Asesoría por parte de otros ingenieros en la empresa',
          'Asesoría por parte del personal técnico en la empresa',
          'Disponibilidad de materiales menores para la actividad',
          'Disponibilidad de recursos informáticos para la actividad',
          'Disponibilidad de equipo para la actividad',
          'Seguridad para el desarrollo de actividades',
          'Actitud respetuosa por parte del personal de la empresa',
        ];
      @endphp

    {{-- Contenedor único para todas las preguntas --}}
    <div class="bg-light p-3 rounded mb-4">

    {{-- Preguntas con opción múltiple --}}
    @foreach($preguntas as $index => $pregunta)
        <div class="row align-items-center mb-3">
        <label class="col-md-10 form-label mb-0">{{ ($index+1) . '. ' . $pregunta }}</label>
        <div class="col-md-2">
            <select class="form-select" name="pregunta_{{$index+1}}" required>
            <option value="" hidden>---</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            </select>
        </div>
        </div>
    @endforeach

    {{-- Preguntas binarias --}}
    <div class="row align-items-center mb-3">
        <label class="col-md-10 col-form-label">12.- ¿Recibiste remuneración económica?</label>
        <div class="col-md-2">
        <select class="form-select" name="remuneracion" required>
            <option value="">---</option>
            <option value="SI">Sí</option>
            <option value="NO">No</option>
        </select>
        </div>
    </div>

    <div class="row align-items-center mb-3">
        <label class="col-md-10 col-form-label">13.- ¿Recomendarías esta empresa para que otros compañeros realicen una estancia profesional?</label>
        <div class="col-md-2">
        <select class="form-select" name="recomienda" required>
            <option value="">---</option>
            <option value="SI">Sí</option>
            <option value="NO">No</option>
        </select>
        </div>
    </div>

    {{-- Preguntas abiertas --}}
    <div class="mb-4">
        <label for="mejoras" class="form-label fw-bold">14.- Recomendaciones para el mejoramiento de la experiencia del practicante en la empresa</label>
        <textarea id="mejoras" name="mejoras" rows="2" class="form-control"></textarea>
    </div>

    <div class="mb-4">
        <label for="comentarios_adicionales" class="form-label fw-bold">15.- Comentarios adicionales</label>
        <textarea id="comentarios_adicionales" name="comentarios_adicionales" rows="2" class="form-control"></textarea>
    </div>

    </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary">Guardar cambios</button>
        <button type="button" class="btn btn-danger" onclick="resetEvaluacion()">Cancelar</button>
        <button type="submit" class="btn btn-success">Enviar</button>
      </div>

    </form>
  </div>
</div>

@push('scripts')
<script>
  function resetEvaluacion() {
    document.getElementById('form-evaluacion').reset();
  }
</script>
@endpush

@endsection

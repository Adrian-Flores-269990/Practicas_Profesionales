@extends('layouts.alumno')
@section('title','Registro de solicitud de prácticas profesionales')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush


@php
  $alumno = session('alumno');
@endphp


@section('content')
@include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    SOLICITUD DE REGISTRO
  </h4>
  

  <div class="row">
    <section class="col-12">
      <div class="accordion" id="soliAccordion">

        {{-- 1. Solicitante --}}
        <div class="accordion-item soli-card mb-3">
          <h2 class="accordion-header" id="h-solicitante">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
              DATOS GENERALES DEL SOLICITANTE
            </button>
          </h2>

          <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion" aria-labelledby="h-solicitante">
            <div class="accordion-body section-card p-3">
              <form id="f-solicitante" class="row g-3">

                <!-- Fila 1: Créditos / Nivel -->
                <div class="col-12 col-md-6">
                  <label for="creditos_aprobados" class="form-label">Número de créditos aprobados a la fecha <span class="text-danger">*</span></label>
                  <input type="text" name="numero_creditos" class="form-control" value="{{ $alumno['creditos'] ?? '-' }}" readonly>
                </div>

                <div class="col-12 col-md-6">
                  <label for="nivel_plan" class="form-label">Nivel de plan de estudios aprobado a la fecha <span class="text-danger">*</span></label>
                  <input type="text" name="semestre" class="form-control" value="{{ $alumno['semestre'] ?? '-' }}" readonly>
                </div>

                <!-- Fila 2: Créditos otros espacios / Total con PP I / Asignación DSSPP -->
                <div class="col-12 col-md-4">
                  <label for="creditos_otros" class="form-label">Número de créditos en otros espacios de formación <span class="text-danger">*</span></label>
                  <input id="creditos_otros" name="creditos_otros" type="number" class="form-control" placeholder="Ej. 6">
                </div>

                <div class="col-12 col-md-4">
                  <label for="total_con_pp1" class="form-label">Total créditos con Prácticas profesionales I <span class="text-danger">*</span></label>
                  <input id="total_con_pp1" name="total_con_pp1" type="number" min="1" max="12" class="form-control">
                </div>

                <div class="col-12 col-md-4">
                  <label for="asignacion_dsspp" class="form-label">Asignación oficial del DSSPP <span class="text-danger">*</span></label>
                  <input id="asignacion_dsspp" name="asignacion_dsspp" type="text" class="form-control" placeholder="Clave / Folio">
                </div>

                <!-- Fila 3: Fecha de asignación -->
                <div class="col-12 col-md-4">
                  <label for="fecha_asignacion" class="form-label">Fecha de asignación <span class="text-danger">*</span></label>
                  <input id="fecha_asignacion" name="fecha_asignacion" type="date" class="form-control">
                </div>

                <!-- Acción -->
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- 2. ASIGNACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (DSPP) --}}
        <div class="accordion-item soli-card mb-3">
          <h2 class="accordion-header" id="h-practicas">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
              ASIGNACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (DSPP) 
            </button>
          </h2>

          <div id="sec-practicas" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-practicas">
            <div class="accordion-body section-card p-3">
              <form id="f-practicas" class="row g-3">

                <div class="col-12 col-md-3">
                  <label for="inicio_practicas" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                  <input id="inicio_practicas" name="inicio_practicas" type="date" class="form-control">
                </div>

                <div class="col-12 col-md-3">
                  <label for="fin_practicas" class="form-label">Fecha de término <span class="text-danger">*</span></label>
                  <input id="fin_practicas" name="fin_practicas" type="date" class="form-control">
                </div>

                <div class="col-12"></div>

                <div class="col-12">
                  <label class="form-label">Tipo de sector <span class="text-danger">*</span></label>
                  <div class="d-flex flex-wrap gap-3 ms-2">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="sector" id="s-mun" value="municipal">
                      <label class="form-check-label" for="s-mun">Municipal</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="sector" id="s-est" value="estatal">
                      <label class="form-check-label" for="s-est">Estatal</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="sector" id="s-fed" value="federal">
                      <label class="form-check-label" for="s-fed">Federal</label>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-8">
                  <label for="nombre_dependencia" class="form-label">Nombre de la dependencia <span class="text-danger">*</span></label>
                  <input id="nombre_dependencia" name="nombre_dependencia" type="text" class="form-control" placeholder="TANGAMANGA">
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label d-block">¿Guardar datos de la empresa?</label>
                  <div class="btn-group" role="group" aria-label="Guardar empresa">
                    <input type="radio" class="btn-check" name="saveempresa" id="save-si" autocomplete="off">
                    <label class="btn btn-soft" for="save-si">Sí</label>

                    <input type="radio" class="btn-check" name="saveempresa" id="save-no" autocomplete="off">
                    <label class="btn btn-soft" for="save-no">No</label>
                  </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- 3. Empresa / Prácticas --}}
        <div class="accordion-item soli-card mb-3">
          <h2 class="accordion-header" id="h-empresa">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
              DE LAS PRÁCTICAS PROFESIONALES
            </button>
          </h2>

          <div id="sec-empresa" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-empresa">
            <div class="accordion-body section-card p-3">
              <form id="f-empresa" class="row g-3">

                <div class="col-12 col-md-6">
                  <label for="razon_social" class="form-label">Razón social <span class="text-danger">*</span></label>
                  <input id="razon_social" name="razon_social" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-6">
                  <label for="rfc" class="form-label">RFC <span class="text-danger">*</span></label>
                  <input id="rfc" name="rfc" type="text" class="form-control">
                </div>

                <div class="col-12">
                  <label for="descripcion_empresa" class="form-label">Proyecto que desarrollará y/o puesto que ocupará <span class="text-danger">*</span></label>
                  <textarea id="descripcion_empresa" name="descripcion_empresa" class="form-control" rows="3"></textarea>
                </div>

                <!-- Dirección: una sola fila con 4 cols (ajusta según importancia) -->
                <div class="col-12 col-md-4">
                  <label for="calle" class="form-label">Calle <span class="text-danger">*</span></label>
                  <input id="calle" name="calle" type="text" class="form-control">
                </div>

                <div class="col-6 col-md-2">
                  <label for="num_ext" class="form-label">No. <span class="text-danger">*</span></label>
                  <input id="num_ext" name="num_ext" type="text" class="form-control">
                </div>

                <div class="col-6 col-md-3">
                  <label for="colonia" class="form-label">Colonia <span class="text-danger">*</span></label>
                  <input id="colonia" name="colonia" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-3">
                  <label for="cp" class="form-label">Código Postal <span class="text-danger">*</span></label>
                  <input id="cp" name="cp" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-4">
                  <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                  <input id="estado" name="estado" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-4">
                  <label for="municipio" class="form-label">Municipio <span class="text-danger">*</span></label>
                  <input id="municipio" name="municipio" type="text" class="form-control">
                </div>

                <!-- Asesor externo -->
                <div class="col-12 col-md-4">
                  <label for="nombre_asesor" class="form-label">Nombre del asesor externo <span class="text-danger">*</span></label>
                  <input id="nombre_asesor" name="nombre_asesor" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-4">
                  <label for="puesto_asesor" class="form-label">Puesto del asesor <span class="text-danger">*</span></label>
                  <input id="puesto_asesor" name="puesto_asesor" type="text" class="form-control">
                </div>

                <div class="col-12 col-md-4">
                  <label for="correo_asesor" class="form-label">Correo del asesor <span class="text-danger">*</span></label>
                  <input id="correo_asesor" name="correo_asesor" type="email" class="form-control" placeholder="nombre@ejemplo.com">
                </div>

                <div class="col-12 col-md-4">
                  <label for="tel_asesor" class="form-label">Teléfono del asesor <span class="text-danger">*</span></label>
                  <input id="tel_asesor" name="tel_asesor" type="tel" class="form-control" placeholder="10 dígitos">
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        {{-- 4. Periodo --}}
        <div class="accordion-item soli-card mb-3">
          <h2 class="accordion-header" id="h-encargado">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-encargado">
              PERIODO DE LAS PRÁCTICAS PARA LA ACREDITACIÓN DEL ESPACIO DE FORMACIÓN DE PRÁCTICAS PROFESIONALES
            </button>
          </h2>

          <div id="sec-encargado" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-encargado">
            <div class="accordion-body section-card p-3">
              <form id="f-encargado" class="row g-3">
                <div class="col-12 col-md-6">
                  <label for="num_meses" class="form-label">Número de meses <span class="text-danger">*</span></label>
                  <input id="num_meses" name="num_meses" class="form-control" type="number">
                </div>

                <div class="col-12 col-md-6">
                  <label for="total_horas" class="form-label">Total de horas <span class="text-danger">*</span></label>
                  <input id="total_horas" name="total_horas" class="form-control" type="number">
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                  <a href="{{ route('alumno.inicio') }}" class="btn btn-danger">Cancelar</a>
                  <button type="submit" class="btn btn-success">Enviar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>
</div>
@endsection

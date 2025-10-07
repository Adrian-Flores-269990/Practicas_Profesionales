@extends('layouts.alumno')
@section('title','Registro de solicitud de prácticas profesionales')

@push('styles')
<style>

  .form-label{ font-weight:700; }

</style>
@endpush

@section('content')
@include('partials.nav.registro')

<div class="container-fluid py-3">
    <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    SOLICITUD DE REGISTRO 
  </h4>

  <div class="row g-3">

    {{-- Contenido --}}
    <section class="col-12">
      <div class="accordion" id="soliAccordion">

        {{-- 1. Solicitante --}}
        <div class="accordion-item soli-card">
          <h2 class="accordion-header" id="h-solicitante">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
              Datos generales
            </button>
          </h2>
          <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion" aria-labelledby="h-solicitante">

            <div class="accordion-body">
              <form id="f-solicitante">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Número de créditos aprobados a la fecha</label>
                    <input type="text" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nivel de plan de estudios aprobado a la fecha:</label>
                    <input type="text" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Número de créditos a cursar/cursando en otros espacios de formación</label>
                    <input type="text" class="form-control mt-1">
                  </div>




                  <div class="col-md-4">
                    <label class="form-label">Total número de créditos a cuesar con el espacio de formación de prácticas profesionales I</label>
                    <input type="number" min="1" max="12" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Asignación oficial del DSSPP</label>
                    <input type="email" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Fecha de asignación</label>
                    <input type="email" class="form-control">
                  </div>
                </div>
              </form>
            </div>

              <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
              </div>

          </div>
        </div>

        {{-- 2. DSSPP --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-practicas">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
              Asignación del DSSPP
            </button>
          </h2>
          <div id="sec-practicas" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-practicas">
            <div class="accordion-body">

              <form id="f-practicas" class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Fecha de inicio</label>
                  <input type="date" class="form-control mt-1">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Fecha de término</label>
                  <input type="date" class="form-control mt-1">
                </div>
                <div class="col-12"></div>
                <div class="col-12">
                  <label class="form-label">Tipo de sector</label>
                  <div class="d-inline-flex align-items-center gap-3 ms-2">
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

                <div class="col-12">
                  <label class="form-label">Nombre de la dependencia</label>
                  <input type="text" class="form-control mt-1" placeholder="TANGAMANGA">
                </div>

                <div class="col-12">
                  <label class="form-label d-block">¿Deseas guardar los datos de la empresa?</label>
                  <div class="btn-group" role="group">
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

        {{-- 3. Prácticas profesionales --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-empresa">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
              De las prácticas profesionales
            </button>
          </h2>
          <div id="sec-empresa" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-empresa">
            <div class="accordion-body">
              <form id="f-empresa" class="row g-3">

                <div class="col-md-6">
                  <label class="form-label">Razón social</label>
                  <input type="text" class="form-control">
                </div>

                <div class="col-md-6">
                  <label class="form-label">RFC</label>
                  <input type="text" class="form-control">
                </div>

                <div class="col-12">
                  <label class="form-label">Descripción</label>
                  <textarea class="form-control" rows="3"></textarea>
                </div>


                <div class="col-md-4">
                  <label class="form-label">Calle</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-2">
                  <label class="form-label">No.</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Colonia</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Código Postal</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Estado</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Municipio</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Nombre del Asesor Externo</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                  <div class="col-md-4">
                  <label class="form-label">Puesto del Asesor Externo</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                  <div class="col-md-4">
                  <label class="form-label">Correo del Asesor Externo</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                  <div class="col-md-4">
                  <label class="form-label">Telefono del Asesor Externo</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>

              </form>
            </div>
          </div>
        </div>


        {{-- 4. Periodo --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-encargado">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-encargado">
              Periodo de las prácticas
            </button>
          </h2>
          <div id="sec-encargado" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-encargado">
            <div class="accordion-body">
              <form id="f-encargado" class="row g-3">
                <div class="col-md-6"><label class="form-label">Numero de meses</label><input class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Total de horas</label><input class="form-control"></div>
              
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

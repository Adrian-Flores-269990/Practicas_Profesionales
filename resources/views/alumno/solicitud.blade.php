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
    SOLICITUD DE REGISTRO DEL ALUMNO
  </h4>

  <div class="row g-3">

    {{-- Contenido --}}
    <section class="col-12">

      <div class="accordion" id="soliAccordion">

        {{-- 1. Solicitante --}}
        <div class="accordion-item soli-card">
          <h2 class="accordion-header" id="h-solicitante">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
              Datos generales del solicitante
            </button>
          </h2>
          <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion" aria-labelledby="h-solicitante">
            <div class="accordion-body">
              <form id="f-solicitante">
                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label">Matrícula</label>
                    <input type="text" class="form-control" placeholder="">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Programa</label>
                    <input type="text" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Semestre</label>
                    <input type="number" min="1" max="12" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Email UASLP</label>
                    <input type="email" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        {{-- 2. Prácticas profesionales --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-practicas">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
              Prácticas profesionales – Sector público/privado/UASLP
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
                  <span class="form-label">Tipo de sector</span>
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
                  <input type="text" class="form-control mt-1" placeholder="">
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
                  <label class="form-label">Área o Departamento</label>
                  <input type="text" class="form-control mt-1" placeholder="">
                </div>

                <div class="col-12">
                  <label class="form-label">¿Deseas guardar los datos de la empresa?</label>
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

        {{-- 3. Empresa --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-empresa">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
              Perfil de la empresa
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

                  <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        {{-- 4. Encargado --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-encargado">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-encargado">
              Encargado de prácticas / Asesor externo
            </button>
          </h2>
          <div id="sec-encargado" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-encargado">
            <div class="accordion-body">
              <form id="f-encargado" class="row g-3">
                <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Puesto</label><input class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Correo</label><input type="email" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Teléfono</label><input class="form-control"></div>
                
                
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        {{-- 5. Proyecto --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-proyecto">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-proyecto">
              Proyecto y actividades
            </button>
          </h2>
          <div id="sec-proyecto" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-proyecto">
            <div class="accordion-body">
              <form id="f-proyecto" class="row g-3">
                <div class="col-12"><label class="form-label">Título del proyecto</label><input class="form-control"></div>
                <div class="col-12"><label class="form-label">Actividades</label><textarea class="form-control" rows="4"></textarea></div>
                
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>      

              </form>
            </div>
          </div>
        </div>

        {{-- 6. Horario --}}
        <div class="accordion-item soli-card mt-3">
          <h2 class="accordion-header" id="h-horario">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario">
              Horario
            </button>
          </h2>
          <div id="sec-horario" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-horario">
            <div class="accordion-body">
              <form id="f-horario" class="row g-2">
                <div class="col-md-3"><label class="form-label">Horas por semana</label><input type="number" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Turno</label>
                  <select class="form-select"><option>Matutino</option><option>Vespertino</option><option>Mixto</option></select>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                  <button type="button" class="btn btn-secondary" disabled>Guardar cambios</button>
                </div>   

              </form>
            </div>
          </div>
        </div>

        {{-- 7. Créditos / Apoyo --}}
        <div class="accordion-item soli-card mt-3 mb-3">
          <h2 class="accordion-header" id="h-creditos">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-creditos">
              Créditos / Apoyo económico
            </button>
          </h2>
          <div id="sec-creditos" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-creditos">
            <div class="accordion-body">
              <form id="f-creditos" class="row g-3">
                <div class="col-md-4"><label class="form-label">Créditos a solicitar</label><input type="number" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Apoyo económico</label><input type="text" class="form-control" placeholder="$"></div>
              
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

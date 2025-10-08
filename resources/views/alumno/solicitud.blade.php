@extends('layouts.alumno')
@section('title','Registro de solicitud de prácticas profesionales')

@push('styles')
<style>
  .form-label { font-weight: 700; }
</style>
@endpush

@section('content')
@include('partials.nav.registro')

<div class="container-fluid py-3">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    SOLICITUD DE REGISTRO DEL ALUMNO
  </h4>

  <form action="{{ route('solicitud.store') }}" method="POST">
    @csrf

    <div class="accordion" id="soliAccordion">

      {{-- 1. Datos del solicitante --}}
      <div class="accordion-item soli-card">
        <h2 class="accordion-header" id="h-solicitante">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
            Datos generales del solicitante
          </button>
        </h2>
        <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion">
          <div class="accordion-body row g-3">
            <div class="col-md-6">
              <label class="form-label">Matrícula</label>
              <input type="text" name="matricula" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Inducción Platica Informativa PP</label>
                <div class="d-inline-flex align-items-center gap-3 ms-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="induccionpp" id="ind-s" value="si">
                    <label class="form-check-label" for="ind-s">SI</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="induccionpp" id="ind-n" value="no">
                    <label class="form-check-label" for="ind-n">NO</label>
                  </div>
                </div>
              </div>
            <div class="col-md-6 d-flex align-items-center">
              <label for="tipo_seguro" class="form-label me-2 mb-0">Tipo de seguro: IMSS</label>
              <input class="form-check-input" type="checkbox" name="tipo_seguro" id="tipo_seguro" value="1">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nombre completo</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">NSS</label>
              <input type="text" name="nss" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono Local o Celular</label>
                <input type="text" name="telefono" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Programa</label>
              <input type="text" name="programa" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Semestre</label>
              <input type="number" name="semestre" min="1" max="12" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label d-block mb-2">Estado</label>
              <div class="d-flex align-items-center">
                <div class="form-check me-4">
                  <input class="form-check-input" type="radio" name="estado" id="estado_alumno" value="alumno">
                  <label class="form-check-label" for="estado_alumno">Alumno</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="estado" id="estado_pasante" value="pasante">
                  <label class="form-check-label" for="estado_pasante">Pasante</label>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label d-block mb-2">Estadística general</label>
              <div class="d-flex align-items-center">
                <div class="form-check me-4">
                  <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_si" value="si">
                  <label class="form-check-label" for="estadistica_si">Sí</label>
                  </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_no" value="no">
                  <label class="form-check-label" for="estadistica_no">No</label>
                  </div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Nacimiento</label>
              <input type="text" name="fechanacimiento" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label d-block mb-2">Constancia de Vigencia de Derechos</label>
              <div class="d-flex align-items-center">
                <div class="form-check me-4">
                  <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_si" value="si">
                  <label class="form-check-label" for="estadistica_si">Sí</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_no" value="no">
                  <label class="form-check-label" for="estadistica_no">No</label>
                </div>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <label for="cartapasante" class="form-label me-2 mb-0">Carta Pasante</label>
              <input class="form-check-input" type="checkbox" name="cartapasante" id="cartapasante" value="1">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <label for="egresadosit" class="form-label me-2 mb-0">Egresado Situación Especial</label>
              <input class="form-check-input" type="checkbox" name="egresadosit" id="egresadosit" value="1">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <label for="extensionSF" class="form-label me-2 mb-0">Extensión Seguro Facultativo</label>
              <input class="form-check-input" type="checkbox" name="extensionSF" id="extensionSF" value="1">
            </div>
          </div>
          </div>
        </div>

      {{-- 2. Sector y empresa --}}
        <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-practicas">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
            Prácticas profesionales – Sector público/privado/UASLP
          </button>
        </h2>
        <div id="sec-practicas" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-practicas">
          <div class="accordion-body">
            <div class="row g-3">
      
              <div class="col-md-3">
                <label class="form-label">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control mt-1" required>
              </div>
      
              <div class="col-md-3">
                <label class="form-label">Fecha de término</label>
                <input type="date" name="fecha_termino" class="form-control mt-1" required>
              </div>
      
              <!-- AQUI -->
                
              <!-- Tipo de sector -->
              <div class="col-md-4">
                <label class="form-label">Tipo de sector</label>
                <select name="sector" id="sector" class="form-select mt-1" required>
                  <option value="">Seleccione...</option>
                  <option value="privado">Privado</option>
                  <option value="publico">Público</option>
                  <option value="uaslp">UASLP</option>
                </select>
              </div>
      
              <!-- Campos que se abren -->
      
              <!-- Sector privado -->
              <div id="sectorPrivado" class="mt-3" style="display: none;">
                <h5 class="mb-2">Datos del sector privado</h5>
                <div class="mb-2">
                  <label class="form-label">Area o Departamento</label>
                  <input type="text" class="form-control" name="area_depto_priv">
                </div>
                <div class="mb-2">
                  <label class="form-label">Número de Trabajadores</label>
                  <input type="text" class="form-control" name="num_trabajadores">
                </div>
                <div class="mb-3">
                  <label class="form-label d-block mb-2">Actividad o Giro</label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="actividad_giro" id="actividad_giro" value="1">
                      <label class="form-check-label" for="estadistica_si">Extractiva</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="actividad_giro" id="actividad_giro" value="2">
                      <label class="form-check-label" for="estadistica_no">Manufacturera</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="actividad_giro" id="actividad_giro" value="3">
                      <label class="form-check-label" for="estadistica_no">Comercial</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="actividad_giro" id="actividad_giro" value="4">
                      <label class="form-check-label" for="estadistica_no">Comisionista</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="actividad_giro" id="actividad_giro" value="5">
                      <label class="form-check-label" for="estadistica_no">Servicio</label>
                    </div>
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Razón Social</label>
                  <input type="text" class="form-control" name="razon_social">
                </div>
                <div class="mb-3">
                  <label class="form-label d-block mb-2">Empresa Outsourcing</label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="empresa_outsourcing" id="estadistica_si" value="si">
                      <label class="form-check-label" for="estadistica_si">Sí</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="empresa_outsourcing" id="estadistica_no" value="no">
                      <label class="form-check-label" for="estadistica_no">No</label>
                    </div>
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Razón Social Outsourcing</label>
                  <input type="text" class="form-control" name="razon_social_outsourcing">
                </div>
              </div>
      
              <!-- Sector público -->
              <div id="sectorPublico" class="mt-3" style="display: none;">
                <h5 class="mb-2">Datos del sector público</h5>
                <div class="mb-2">
                  <label class="form-label">Área o Departamento</label>
                  <input type="text" class="form-control" name="area_depto_public">
                </div>
                <div class="mb-2">
                  <label class="form-label d-block mb-2">Ámbito</label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito" value="1">
                      <label class="form-check-label" for="ambito">Municipal</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito" value="2">
                      <label class="form-check-label" for="ambito">Estatal</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito" value="3">
                      <label class="form-check-label" for="ambito">Federal</label>
                    </div>
                  </div>
                </div>
              </div>
      
              <!-- Sector UASLP -->
              <div id="sectorUaslp" class="mt-3" style="display: none;">
                <h5 class="mb-2">Datos del sector UASLP</h5>
                <div class="mb-2">
                  <label class="form-label">Área o Departamento</label>
                  <input type="text" class="form-control" name="area_depto_uaslp">
                </div>
                <div class="mb-2">
                  <label class="form-label d-block mb-2">Tipo de Entidad</label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_entidad" value="1">
                      <label class="form-check-label" for="tipo_entidad">Instituto</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_entidad" value="2">
                      <label class="form-check-label" for="tipo_entidad">Centro de Investigación</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-3"><label class="form-label">Entidad Académica</label>
                  <select name="entidad_academica" class="form-select" required>
                      <option value="1">Entidad1</option>
                      <option value="0">Entidad2</option>
                      <option value="2">Entidad3</option>
                  </select>
                </div>
              </div>
      
              <script>
                document.getElementById('sector').addEventListener('change', function() {
                  const privado = document.getElementById('sectorPrivado');
                  const publico = document.getElementById('sectorPublico');
                  const uaslp = document.getElementById('sectorUaslp');
      
                  // Ocultar todos
                  privado.style.display = 'none';
                  publico.style.display = 'none';
                  uaslp.style.display = 'none';
      
                  // Mostrar el correspondiente
                  if (this.value === 'privado') {
                    privado.style.display = 'block';
                  } else if (this.value === 'publico') {
                    publico.style.display = 'block';
                  } else if (this.value === 'uaslp') {
                    uaslp.style.display = 'block';
                  }
                });
              </script>
      
                    <!---->
      
            </div>
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
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombre de la empresa</label>
                    <input type="text" name="nombre_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Razón social</label>
                    <input type="text" name="razon_social" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">RFC</label>
                    <input type="text" name="rfc" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Calle</label>
                    <input type="text" name="calle_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Colonia</label>
                    <input type="text" name="colonia_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">C.P.</label>
                    <input type="text" name="cp_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Municipio</label>
                    <input type="text" name="municipio_empresa" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <input type="text" name="estado_empresa" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion_empresa" class="form-control" rows="3" required></textarea>
                  </div>
                </div>
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
            <div id="sec-encargado" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Nombre Encargado de PP</label><input name="encargado_nombre" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Nombre Asesor Externo</label><input name="asesorexterno_nombre" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Area del Asesor Externo</label><input name="asesorexterno_area" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Puesto del Asesor Externo</label><input name="asesorexterno_puesto" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Correo del Asesor Externo</label><input type="email" name="asesorexterno_correo" class="form-control" required></div>
                  <div class="col-md-6"><label class="form-label">Teléfono del Asesor Externo</label><input name="asesorexterno_telefono" class="form-control" required></div>
                </div>
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
            <div id="sec-proyecto" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-12"><label class="form-label">Nombre del proyecto</label><input name="mombre_proyecto" class="form-control" required></div>
                  <div class="col-12"><label class="form-label">Actividades</label><textarea name="actividades" class="form-control" rows="4" required></textarea></div>
                </div>
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
                <div class="row g-2">
                  <!--<div class="col-md-3">
                    <label class="form-label">Horas por semana</label>
                    <input type="number" name="horas_semana" class="form-control" required>
                  </div>-->
                  <div class="col-md-3">
                    <label class="form-label">Turno</label>
                    <select name="turno" class="form-select" required>
                        <option value="1">Matutino</option>
                        <option value="0">Vespertino</option>
                        <option value="2">Mixto</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Hora de entrada</label>
                    <select name="horario_entrada" class="form-select" required>
                      @for ($h = 6; $h <= 22; $h++)
                        <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                        <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Hora de salida</label>
                    <select name="horario_salida" class="form-select" required>
                      @for ($h = 6; $h <= 22; $h++)
                        <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                        <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Días de asistencia</label>
                    <div class="d-flex flex-wrap gap-2">
                      @php
                        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                        $dias_val = ['L','M','X','J','V','S','D'];
                      @endphp
                      @foreach ($dias as $i => $dia)
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="dias_asistencia[]" value="{{ $dias_val[$i] }}" id="dia_{{ $dias_val[$i] }}">
                          <label class="form-check-label" for="dia_{{ $dias_val[$i] }}">{{ $dia }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
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
            <div id="sec-creditos" class="accordion-collapse collapse">
              <div class="accordion-body">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Validacion de Creditos</label>
                    <div class="d-inline-flex align-items-center gap-3 ms-2">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="val_creditos" id="vc-s" value="si">
                        <label class="form-check-label" for="vc-s">SI</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="val_creditos" id="vc-n" value="no">
                        <label class="form-check-label" for="vc-n">NO</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Apoyo Económico</label>
                    <div class="d-inline-flex align-items-center gap-3 ms-2">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="apoyoeco" id="ap-s" value="si">
                        <label class="form-check-label" for="ap-s">SI</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="apoyoeco" id="ap-n" value="no">
                        <label class="form-check-label" for="ap-n">NO</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Extensión de Prácticas</label>
                    <div class="d-inline-flex align-items-center gap-3 ms-2">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="extension" id="ex-s" value="si">
                        <label class="form-check-label" for="ex-s">SI</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="extension" id="ex-n" value="no">
                        <label class="form-check-label" for="ex-n">NO</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Expedición de Recibos</label>
                    <div class="d-inline-flex align-items-center gap-3 ms-2">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="expe_rec" id="exr-s" value="si">
                        <label class="form-check-label" for="exr-s">SI</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="expe_rec" id="exr-n" value="no">
                        <label class="form-check-label" for="exr-n">NO</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
          <a href="{{ route('alumno.inicio') }}" class="btn btn-danger">Cancelar</a>
          <button type="submit" class="btn btn-success">Enviar solicitud</button>
        </div>
      </form>
    </section>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const accordionItems = document.querySelectorAll('.accordion-item');
  const form = document.querySelector('form');
  const sectorSelect = document.getElementById('sector');
  const sectorSections = document.querySelectorAll('.sector-section');

  // Mostrar solo la sección del sector seleccionado
  sectorSelect.addEventListener('change', () => {
    sectorSections.forEach(sec => sec.classList.add('d-none'));
    const selected = document.getElementById('sector-' + sectorSelect.value);
    if (selected) selected.classList.remove('d-none');
  });

  // Colorear secciones completas
  function checkSection(section) {
    const inputs = section.querySelectorAll('input, select, textarea');
    let completed = true;
    inputs.forEach(input => {
      if (input.hasAttribute('required') && !input.value) completed = false;
    });
    const header = section.querySelector('.accordion-header button');
    header.style.backgroundColor = completed ? '#4583B7' : '';
    header.style.color = completed ? 'white' : '';
  }

  accordionItems.forEach(item => {
    const inputs = item.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      input.addEventListener('input', () => checkSection(item));
      input.addEventListener('change', () => checkSection(item));
    });
    checkSection(item);
  });

  // Validación al enviar
  form.addEventListener('submit', function(e) {
    let allValid = true;
    accordionItems.forEach(item => {
      const inputs = item.querySelectorAll('input[required], select[required], textarea[required]');
      inputs.forEach(input => {
        if (!input.value) allValid = false;
      });
    });
    if (!allValid) {
      e.preventDefault();
      alert('Debe completar todas las secciones antes de enviar.');
    }
  });
});
</script>
@endpush

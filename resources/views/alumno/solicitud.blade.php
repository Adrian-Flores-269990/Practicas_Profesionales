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
    SOLICITUD DE REGISTRO DEL ALUMNO
  </h4>

  <form action="{{ route('solicitud.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="accordion" id="soliAccordion">

      {{-- 1. Datos del solicitante --}}
      <div class="accordion-item soli-card">
        <h2 class="accordion-header" id="h-solicitante">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
            DATOS GENERALES DEL SOLICITANTE
          </button>
        </h2>

        <div id="sec-solicitante" class="accordion-collapse collapse show" data-bs-parent="#soliAccordion">
          <div class="accordion-body">

            {{-- Sección de datos automáticos del alumno --}}
            <div class="row g-3 mb-3 ">
              <div class="col-md-6">
                <label class="form-label">Fecha de Solicitud</label>
                <input type="date" name="fecha_solicitud" class="form-control" value="{{ date('Y-m-d') }}" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label">Nombre del Alumno</label>
                <input type="text" name="nombre_alumno" class="form-control" value="{{ $alumno['nombres'] ?? '' }} {{ $alumno['paterno'] ?? '' }} {{ $alumno['materno'] ?? '' }}" readonly>
              </div>


              <div class="col-md-4">
                <label class="form-label">Clave</label>
                <input type="text" name="clave" class="form-control" value="{{ $alumno['cve_uaslp'] ?? '-' }}" readonly>
              </div>

              <div class="col-md-4">
                <label class="form-label">Semestre</label>
                <input type="text" name="semestre" class="form-control" value="{{ $alumno['semestre'] ?? '-' }}" readonly>
              </div>

              <div class="col-md-4">
                <label class="form-label">Número de Créditos</label>
                <input type="text" name="numero_creditos" class="form-control" value="{{ $alumno['creditos'] ?? '-' }}" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label">Carrera</label>
                <input type="text" name="carrera" class="form-control" value="{{ $alumno['carrera'] ?? '-' }}" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo_electronico" class="form-control" value="{{ $alumno['correo_electronico'] ?? '-' }}" readonly>
              </div>
            </div>


            <!-- Fila 1: Inducción y Tipo de Seguro -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Inducción Plática Informativa PP <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="induccionpp" id="ind-s" value="si" required>
                    <label class="form-check-label" for="ind-s">SI</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="induccionpp" id="ind-n" value="no" required>
                    <label class="form-check-label" for="ind-n">NO</label>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label d-block">Tipo de seguro</label>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="tipo_seguro" id="tipo_seguro" value="1">
                  <label class="form-check-label" for="tipo_seguro">IMSS</label>
                </div>
              </div>
            </div>

            <!-- Fila 2: NSF y Teléfono -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">NSF <span class="text-danger">*</span></label>
                <input type="text" name="nsf" class="form-control" placeholder="Número de Seguro Social" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Teléfono Local o Celular <span class="text-danger">*</span></label>
                <input type="tel" name="telefono" class="form-control" placeholder="Ej: 4441234567" required>
              </div>
            </div>

            <!-- Fila 3: Fecha de Nacimiento y Estado -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                <input type="date" name="fechanacimiento" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label d-block">Estado <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="estado" id="estado_alumno" value="alumno" required>
                    <label class="form-check-label" for="estado_alumno">Alumno</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="estado" id="estado_pasante" value="pasante" required>
                    <label class="form-check-label" for="estado_pasante">Pasante</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fila 4: Estadística General y Constancia -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label d-block">Estadística General <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_si" value="si" required>
                    <label class="form-check-label" for="estadistica_si">Sí</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="estadistica_general" id="estadistica_no" value="no" required>
                    <label class="form-check-label" for="estadistica_no">No</label>
                  </div>
                </div>
                 {{-- Upload de estadística general (se muestra solo si selecciona "Sí") --}}
                 <div class="mt-3" id="upload_estadistica" style="display: none;">
                   <div class="alert alert-info mb-2">
                     <i class="bi bi-info-circle"></i> Por favor, sube tu archivo de estadística general en formato PDF (Saca una captura de tu estadística general en el portal de ingeniería)
                   </div>
                   <label class="form-label">Subir Estadística General (PDF) <span class="text-danger">*</span></label>
                   <input type="file" name="estadistica_pdf" class="form-control mb-2" accept=".pdf" id="estadistica_file">
                   <small class="form-text text-muted">Tamaño máximo: 5MB</small>
                 </div>
              </div>

              <div class="col-md-6">
                <label class="form-label d-block">Constancia de vigencia de derechos(Seguro social) <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="constancia_derechos" id="constancia_si" value="si" required>
                    <label class="form-check-label" for="constancia_si">Sí</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="constancia_derechos" id="constancia_no" value="no" required>
                    <label class="form-check-label" for="constancia_no">No</label>
                  </div>
                </div>
              </div>
            </div>

            {{-- Upload de constancia (se muestra solo si selecciona "Sí") --}}
            <div class="row g-3 mb-3" id="upload_constancia" style="display: none;">
              <div class="col-12">
                <div class="alert alert-info mb-2">
                  <i class="bi bi-info-circle"></i> Por favor, sube tu constancia de vigencia de derechos en formato PDF
                </div>
                <label class="form-label">Subir Constancia (PDF) <span class="text-danger">*</span></label>
                <input type="file" name="constancia_pdf" class="form-control mb-2" accept=".pdf" id="constancia_file">
                <small class="form-text text-muted">Tamaño máximo: 5MB</small>
              </div>
            </div>

            {{-- Mensajes de éxito --}}
            @if(session('success'))
              <div class="alert alert-success mt-2">{{ session('success') }}</div>
            @endif
            {{-- Mensajes de error --}}
            @if($errors->any())
              <div class="alert alert-danger mt-2">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- Fila 5: Checkboxes finales -->
            <div class="row g-3">
              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="cartapasante" id="cartapasante" value="1">
                  <label class="form-check-label" for="cartapasante">Carta Pasante</label>
                </div>
                 <div class="mt-2" id="upload_cartapasante" style="display: none;">
                   <div class="alert alert-info mb-2">
                     <i class="bi bi-info-circle"></i> Si tienes tu carta de pasante, súbela en formato PDF
                   </div>
                   <label class="form-label">Subir Carta Pasante (PDF)</label>
                   <input type="file" name="cartapasante_pdf" class="form-control mb-2" accept=".pdf" id="cartapasante_file">
                   <small class="form-text text-muted">Tamaño máximo: 5MB</small>
                 </div>
              </div>

              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="egresadosit" id="egresadosit" value="1">
                  <label class="form-check-label" for="egresadosit">Egresado Situación Especial</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="extensionSF" id="extensionSF" value="1">
                  <label class="form-check-label" for="extensionSF">Extensión Seguro Facultativo</label>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>



      {{-- 2. Sector y empresa --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-practicas">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-practicas">
            DATOS GENERALES DE LAS PRÁCTICAS PROFESIONALES
          </button>
        </h2>
        <div id="sec-practicas" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-practicas">
          <div class="accordion-body">
            <div class="row g-3">

              <div class="col-md-3">
                <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inicio" class="form-control mt-1" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Fecha de término <span class="text-danger">*</span></label>
                <input type="date" name="fecha_termino" class="form-control mt-1" required>
              </div>

              <!-- Tipo de sector -->
              <div class="col-md-4">
                <label class="form-label">Tipo de sector <span class="text-danger">*</span></label>
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
                <div class="row g-3">
                  <div class="col-md-6 position-relative">
                    <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_empresa_privado" id="nombre_empresa_privado" class="form-control" autocomplete="off" data-require="true">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="razon_social" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">RFC <span class="text-danger">*</span></label>
                    <input type="text" name="rfc_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="form-label">Ramo <span class="text-danger">*</span></label>
                    <select name="ramo_privado" id="ramo_privado" class="form-select mt-1" data-require="true">
                      <option value="">Seleccione un ramo...</option>
                      <option value="1">Agricultura, ganadería y caza</option>
                      <option value="2">Transporte y comunicaciones</option>
                      <option value="3">Industria manufacturera</option>
                      <option value="4">Restaurantes y hoteles</option>
                      <option value="5">Servicios profesionales y técnicos especializados</option>
                      <option value="6">Servicios de reparación y mantenimiento</option>
                      <option value="7">Servicios educativos</option>
                      <option value="8">Construcción</option>
                      <option value="9">Otro</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Calle <span class="text-danger">*</span></label>
                    <input type="text" name="calle_empresa_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Número <span class="text-danger">*</span></label>
                    <input type="text" name="numero_empresa_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Colonia <span class="text-danger">*</span></label>
                    <input type="text" name="colonia_empresa_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">C.P. <span class="text-danger">*</span></label>
                    <input type="text" name="cp_empresa_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Estado <span class="text-danger">*</span></label>
                    <input type="text" name="estado_empresa_privado" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Municipio <span class="text-danger">*</span></label>
                    <input type="text" name="municipio_empresa_privado" class="form-control" data-require="true">
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                  <input type="text" name="telefono_privado" class="form-control" data-require="true">
                </div>
                <div class="mb-2">
                  <label class="form-label">Área o Departamento <span class="text-danger">*</span></label>
                  <input type="text" name="area_depto_privado" class="form-control" data-require="true">
                </div>
                <div class="mb-2">
                  <label class="form-label">Número de Trabajadores <span class="text-danger">*</span></label>
                  <select name="num_trabajadores" class="form-control" data-require="true">
                    <option value="1">Micro (1 - 30)</option>
                    <option value="2">Pequeña (31 - 100)</option>
                    <option value="3">Mediana (101 - 250)</option>
                    <option value="4">Grande (más de 250)</option>
                  </select>
                </div>
                <div class="col-md-6">
                <label class="form-label">Actividad o giro <span class="text-danger">*</span></label>
                <select name="actividad_giro" class="form-select" data-require="true">
                    <option value="">Seleccione...</option>
                    <option value="1">Extractiva</option>
                    <option value="2">Manufacturera</option>
                    <option value="3">Comercial</option>
                    <option value="4">Comisionista</option>
                    <option value="5">Servicio</option>
                </select>
                </div>
                <div class="mb-3">
                  <label class="form-label d-block mb-2">Empresa Outsourcing <span class="text-danger">*</span></label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="empresa_outsourcing" id="out_si" value="si" data-require="true">
                      <label class="form-check-label" for="out_si">Sí</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="empresa_outsourcing" id="out_no" value="no" data-require="true">
                      <label class="form-check-label" for="out_no">No</label>
                    </div>
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Razón Social Outsourcing <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="razon_social_outsourcing" data-require="true">
                </div>
              </div>

              <!-- Sector público -->
              <div id="sectorPublico" class="mt-3" style="display: none;">
                <div class="row g-3">
                  <div class="col-md-6 position-relative">
                    <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_empresa_publico" class="form-control" autocomplete="off" data-require="true">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">RFC <span class="text-danger">*</span></label>
                    <input type="text" name="rfc_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label class="form-label">Ramo <span class="text-danger">*</span></label>
                    <select name="ramo_publico" id="ramo_publico" class="form-select mt-1" data-require="true">
                      <option value="">Seleccione un ramo...</option>
                      <option value="1">Agricultura, ganadería y caza</option>
                      <option value="2">Transporte y comunicaciones</option>
                      <option value="3">Industria manufacturera</option>
                      <option value="4">Restaurantes y hoteles</option>
                      <option value="5">Servicios profesionales y técnicos especializados</option>
                      <option value="6">Servicios de reparación y mantenimiento</option>
                      <option value="7">Servicios educativos</option>
                      <option value="8">Construcción</option>
                      <option value="9">Otro</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Calle <span class="text-danger">*</span></label>
                    <input type="text" name="calle_empresa_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Número <span class="text-danger">*</span></label>
                    <input type="text" name="numero_empresa_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Colonia <span class="text-danger">*</span></label>
                    <input type="text" name="colonia_empresa_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">C.P. <span class="text-danger">*</span></label>
                    <input type="text" name="cp_empresa_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Estado <span class="text-danger">*</span></label>
                    <input type="text" name="estado_empresa_publico" class="form-control" data-require="true">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Municipio <span class="text-danger">*</span></label>
                    <input type="text" name="municipio_empresa_publico" class="form-control" data-require="true">
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="telefono_publico" data-require="true">
                </div>
                <div class="mb-2">
                  <label class="form-label">Área o Departamento <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="area_depto_publico" data-require="true">
                </div>
                <div class="mb-2">
                  <label class="form-label d-block mb-2">Ámbito <span class="text-danger">*</span></label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito_mun" value="1" data-require="true">
                      <label class="form-check-label" for="ambito_mun">Municipal</label>
                    </div>
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito_est" value="2" data-require="true">
                      <label class="form-check-label" for="ambito_est">Estatal</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="ambito" id="ambito_fed" value="3" data-require="true">
                      <label class="form-check-label" for="ambito_fed">Federal</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sector UASLP -->
              <div id="sectorUaslp" class="mt-3" style="display: none;">
                <div class="mb-2">
                  <label class="form-label">Área o Departamento <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="area_depto_uaslp" data-require="true">
                </div>
                <div class="mb-2">
                  <label class="form-label d-block mb-2">Tipo de Entidad <span class="text-danger">*</span></label>
                  <div class="d-flex align-items-center">
                    <div class="form-check me-4">
                      <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_inst" value="1" data-require="true">
                      <label class="form-check-label" for="tipo_inst">Instituto</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_centro" value="2" data-require="true">
                      <label class="form-check-label" for="tipo_centro">Centro de Investigación <span class="text-danger">*</span></label>
                    </div>
                  </div>
                </div>
                <div class="col-md-3"><label class="form-label">Entidad Académica <span class="text-danger">*</span></label>
                  <select name="entidad_academica" class="form-select" data-require="true">
                      <option value="1">Entidad1</option>
                      <option value="2">Entidad2</option>
                      <option value="3">Entidad3</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 4. Encargado --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-encargado">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-encargado">
            ENCARGADO DE PRÁCTICAS / ASESOR EXTERNO
          </button>
        </h2>
        <div id="sec-encargado" class="accordion-collapse collapse">
          <div class="accordion-body">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Nombre Encargado de PP <span class="text-danger">*</span></label><input name="encargado_nombre" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Nombre Asesor Externo <span class="text-danger">*</span></label><input name="asesorexterno_nombre" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Area del Asesor Externo <span class="text-danger">*</span></label><input name="asesorexterno_area" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Puesto del Asesor Externo <span class="text-danger">*</span></label><input name="asesorexterno_puesto" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Correo del Asesor Externo <span class="text-danger">*</span></label><input type="email" name="asesorexterno_correo" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Teléfono del Asesor Externo <span class="text-danger">*</span></label><input name="asesorexterno_telefono" class="form-control" required></div>
            </div>
          </div>
        </div>
      </div>

      {{-- 5. Proyecto --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-proyecto">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-proyecto">
            PROYECTO Y ACTIVIDADES
          </button>
        </h2>
        <div id="sec-proyecto" class="accordion-collapse collapse">
          <div class="accordion-body">
            <div class="row g-3">
              <div class="col-12"><label class="form-label">Nombre del proyecto <span class="text-danger">*</span></label><input name="mombre_proyecto" class="form-control" required></div>
              <div class="col-12"><label class="form-label">Actividades <span class="text-danger">*</span></label><textarea name="actividades" class="form-control" rows="4" required></textarea></div>
            </div>
          </div>
        </div>
      </div>

      {{-- 6. Horario --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-horario">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario">
            HORARIO
          </button>
        </h2>
        <div id="sec-horario" class="accordion-collapse collapse" data-bs-parent="#soliAccordion" aria-labelledby="h-horario">
          <div class="accordion-body">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label">Turno <span class="text-danger">*</span></label>
                <select name="turno" class="form-select" required>
                    <option value="M">Matutino</option>
                    <option value="V">Vespertino</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Hora de entrada <span class="text-danger">*</span></label>
                <input type="time" name="horario_entrada" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Hora de salida <span class="text-danger">*</span></label>
                <input type="time" name="horario_salida" class="form-control" required>
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
            CRÉDITOS / APOYO ECONÓMICO
          </button>
        </h2>
        <div id="sec-creditos" class="accordion-collapse collapse">
          <div class="accordion-body">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Validacion de Creditos <span class="text-danger">*</span></label>
                <div class="d-inline-flex align-items-center gap-3 ms-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="val_creditos" id="vc-s" value="si" required>
                    <label class="form-check-label" for="vc-s">SI</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="val_creditos" id="vc-n" value="no" required>
                    <label class="form-check-label" for="vc-n">NO</label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Apoyo Económico <span class="text-danger">*</span></label>
                <div class="d-inline-flex align-items-center gap-3 ms-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="apoyoeco" id="ap-s" value="si" required>
                    <label class="form-check-label" for="ap-s">SI</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="apoyoeco" id="ap-n" value="no" required>
                    <label class="form-check-label" for="ap-n">NO</label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Extensión de Prácticas <span class="text-danger">*</span></label>
                <div class="d-inline-flex align-items-center gap-3 ms-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="extension" id="ex-s" value="si" required>
                    <label class="form-check-label" for="ex-s">SI</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="extension" id="ex-n" value="no" required>
                    <label class="form-check-label" for="ex-n">NO</label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Expedición de Recibos <span class="text-danger">*</span></label>
                <div class="d-inline-flex align-items-center gap-3 ms-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="expe_rec" id="exr-s" value="si" required>
                    <label class="form-check-label" for="exr-s">SI</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="expe_rec" id="exr-n" value="no" required>
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const accordionItems = document.querySelectorAll('.accordion-item');
  const form = document.querySelector('form');
  const sectorSelect = document.getElementById('sector');

  // Script para mostrar/ocultar upload de constancia
  const constanciaSi = document.getElementById('constancia_si');
  const constanciaNo = document.getElementById('constancia_no');
  const uploadDiv = document.getElementById('upload_constancia');
  const fileInput = document.getElementById('constancia_file');

  // Script para mostrar/ocultar upload de estadística general
  const estadisticaSi = document.getElementById('estadistica_si');
  const estadisticaNo = document.getElementById('estadistica_no');
  const uploadEstadisticaDiv = document.getElementById('upload_estadistica');
  const estadisticaFileInput = document.getElementById('estadistica_file');

  // Script para mostrar/ocultar upload de carta pasante
  const cartapasanteCheckbox = document.getElementById('cartapasante');
  const uploadCartaPasanteDiv = document.getElementById('upload_cartapasante');
  const cartapasanteFileInput = document.getElementById('cartapasante_file');

  function toggleUploadConstancia() {
    if (constanciaSi.checked) {
      uploadDiv.style.display = 'block';
      fileInput.setAttribute('required', 'required');
    } else {
      uploadDiv.style.display = 'none';
      fileInput.removeAttribute('required');
      fileInput.value = '';
    }
  }

  function toggleUploadEstadistica() {
    if (estadisticaSi.checked) {
      uploadEstadisticaDiv.style.display = 'block';
      estadisticaFileInput.setAttribute('required', 'required');
    } else {
      uploadEstadisticaDiv.style.display = 'none';
      estadisticaFileInput.removeAttribute('required');
      estadisticaFileInput.value = '';
    }
  }

  function toggleUploadCartaPasante() {
    if (cartapasanteCheckbox.checked) {
      uploadCartaPasanteDiv.style.display = 'block';
      cartapasanteFileInput.setAttribute('required', 'required');
    } else {
      uploadCartaPasanteDiv.style.display = 'none';
      cartapasanteFileInput.removeAttribute('required');
      cartapasanteFileInput.value = '';
    }
  }

  constanciaSi.addEventListener('change', toggleUploadConstancia);
  constanciaNo.addEventListener('change', toggleUploadConstancia);
  estadisticaSi.addEventListener('change', toggleUploadEstadistica);
  estadisticaNo.addEventListener('change', toggleUploadEstadistica);
  cartapasanteCheckbox.addEventListener('change', toggleUploadCartaPasante);

  // Script para mostrar sectores
  sectorSelect.addEventListener('change', function () {
    const privado = document.getElementById('sectorPrivado');
    const publico = document.getElementById('sectorPublico');
    const uaslp = document.getElementById('sectorUaslp');

    const sectores = [privado, publico, uaslp];

    // Ocultar todos y quitar "required"
    sectores.forEach(sector => {
      sector.style.display = 'none';
      sector.querySelectorAll('[required]').forEach(field => {
        field.removeAttribute('required');
      });
    });

    // Mostrar el correspondiente y agregar "required"
    let activo;
    if (this.value === 'privado') {
      activo = privado;
    } else if (this.value === 'publico') {
      activo = publico;
    } else if (this.value === 'uaslp') {
      activo = uaslp;
    }

    if (activo) {
      activo.style.display = 'block';
      activo.querySelectorAll('[data-require="true"]').forEach(field => {
        field.setAttribute('required', 'required');
      });
    }
  });

  // Colorear secciones completas
  function checkSection(section) {
    let completed = true;

    // Validar inputs normales (text, date, email, etc.)
    const normalInputs = section.querySelectorAll('input[required]:not([type="radio"]):not([type="checkbox"]), select[required], textarea[required]');
    normalInputs.forEach(input => {
      if (!input.value) completed = false;
    });

    // Validar radio buttons (verificar que al menos uno esté seleccionado por grupo)
    const radioGroups = {};
    const radioInputs = section.querySelectorAll('input[type="radio"][required]');
    radioInputs.forEach(radio => {
      if (!radioGroups[radio.name]) {
        radioGroups[radio.name] = false;
      }
      if (radio.checked) {
        radioGroups[radio.name] = true;
      }
    });

    // Si algún grupo de radio no tiene selección, no está completo
    Object.values(radioGroups).forEach(isChecked => {
      if (!isChecked) completed = false;
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

@extends('layouts.encargado')
@section('title', 'Revisión de solicitud')

@php
    $diasMap = [
        'L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles',
        'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sábado', 'D' => 'Domingo',
    ];
    $diasCadena = $solicitud->Dias_Semana ?? '';
    $dias = [];
    foreach (str_split($diasCadena) as $letra) {
        if (isset($diasMap[$letra])) $dias[] = $diasMap[$letra];
    }

    $horario = $solicitud->Horario_Mat_Ves === 'M' ? 'Matutino' :
               ($solicitud->Horario_Mat_Ves === 'V' ? 'Vespertino' : 'No especificado');

    $rel = $solicitud->dependenciaMercadoSolicitud;
    $empresa = $rel ? $rel->dependenciaEmpresa : null;
    $privado = $rel ? $rel->sectorPrivado : null;
    $publico = $rel ? $rel->sectorPublico : null;
    $uaslp = $rel ? $rel->sectorUaslp : null;

    $tipoSector = null;
    if ($privado) $tipoSector = 'privado';
    elseif ($publico) $tipoSector = 'publico';
    elseif ($uaslp) $tipoSector = 'uaslp';

    $ramoOptions = [
        1 => 'Agricultura, ganadería y caza', 2 => 'Transporte y comunicaciones',
        3 => 'Industria manufacturera', 4 => 'Restaurantes y hoteles',
        5 => 'Servicios profesionales y técnicos especializados',
        6 => 'Servicios de reparación y mantenimiento', 7 => 'Servicios educativos',
        8 => 'Construcción', 9 => 'Otro',
    ];

    $numTrabajadoresOptions = [
        1 => 'Micro (1 - 30)', 2 => 'Pequeña (31 - 100)',
        3 => 'Mediana (101 - 250)', 4 => 'Grande (más de 250)',
    ];

    $actividadGiroOptions = [
        1 => 'Extractiva', 2 => 'Manufacturera', 3 => 'Comercial',
        4 => 'Comisionista', 5 => 'Servicio',
    ];

    $entidadOptions = [0 => 'Entidad2', 1 => 'Entidad1', 2 => 'Entidad3'];
@endphp




@php
    $alumno = $solicitud->alumno ?? null;
    $claveAlumno = $alumno->Clave_Alumno ?? null;
    $pdfEstadistica = null;
    $pdfPasante = null;

    if ($claveAlumno) {
        // 🔹 Buscar archivo de Carta Pasante (solo si Carta_Pasante == 1)
        if ($solicitud->Carta_Pasante == 1) {
            $filesPasante = Storage::disk('public')->files('expedientes/carta-pasante');
            $pdfsPasante = collect($filesPasante)
                ->filter(fn($f) => str_contains($f, '0' . $claveAlumno))
                ->sortDesc();
            if ($pdfsPasante->count() > 0) {
                $pdfPasante = $pdfsPasante->first();
            }
        }

        // 🔹 Buscar archivo de Estadística General
        $filesEstadistica = Storage::disk('public')->files('expedientes/estadistica-general');
        $pdfsEstadistica = collect($filesEstadistica)
            ->filter(fn($f) => str_contains($f, '0' . $claveAlumno))
            ->sortDesc();
        if ($pdfsEstadistica->count() > 0) {
            $pdfEstadistica = $pdfsEstadistica->first();
        }
    }
@endphp





@push('styles')
<style>
  .header-alumno {
    background: #007bff; /* 2 opciones de color #004795 */
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .alumno-nombre {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .alumno-detalle {
    opacity: 0.95;
    font-size: 1rem;
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
    background: #c3cfe2;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 1.05rem;
    color: #2d3748;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .seccion-header i {
    font-size: 1.2rem;
  }

  .seccion-header.aceptada {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
    color: white !important;
  }

  .seccion-header.rechazada {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
    color: white !important;
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

  .divider {
    border: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #cbd5e0, transparent);
    margin: 1.5rem 0;
  }

  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
  }

  .btn-aceptar {
    background: #1f8950ff;
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-aceptar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72,187,120,0.4);
    color: white;
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

  .btn-modificar {
    background: #718096;
    color: white;
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }

  .btn-modificar:hover {
    background: #4a5568;
    transform: translateY(-2px);
    color: white;
  }

  .comentarios-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-top: 2rem;
  }

  .btn-submit {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
  }

  .btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(102,126,234,0.4);
    color: white;
  }

  .btn-regresar {
    background: #888f9bff;
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
  }

  .btn-regresar:hover {
    background: #4a5568;
    transform: translateY(-3px);
    color: white;
  }

  .pdf-link {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
  }

  .pdf-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66,153,225,0.4);
    color: white;
  }

  .status-icon {
    font-size: 1.1rem;
  }
</style>
@endpush

@section('content')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    REVISIÓN DE SOLICITUD DE PRÁCTICAS PROFESIONALES
  </h4>

  <div class="container py-4">

    {{-- Header con datos del alumno --}}
    @php $alumno = $solicitud->alumno; @endphp
    <div class="header-alumno">
      <div class="alumno-nombre">
        <i class="bi bi-person-circle me-2"></i>
        {{ $alumno->Nombre ?? 'No disponible' }}
        {{ $alumno->ApellidoP_Alumno ?? '' }}
        {{ $alumno->ApellidoM_Alumno ?? '' }}
      </div>
      <div class="row alumno-detalle">
        <div class="col-md-4">
          <i class="bi bi-bookmark-fill me-1"></i>
          Clave: <strong>{{ $alumno->Clave_Alumno ?? 'N/A' }}</strong>
        </div>
        <div class="col-md-4">
          <i class="bi bi-mortarboard-fill me-1"></i>
          {{ $alumno->Carrera ?? 'N/A' }}
        </div>
        <div class="col-md-4">
          <i class="bi bi-calendar-check me-1"></i>
          Solicitud: {{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud ?? now())->format('d/m/Y') }}
        </div>
      </div>
    </div>

    {{-- FORMULARIO --}}
    <form action="{{ route('encargado.autorizar', $solicitud->Id_Solicitud_FPP01) }}" method="POST" id="form-revision">
      @csrf
      @method('PUT')

      {{-- SECCIÓN 1: DATOS GENERALES DEL SOLICITANTE --}}
      <div class="seccion-card">
        <div class="seccion-header" onclick="toggleSection(this)">
          <i class="bi bi-person-badge"></i>
          DATOS GENERALES DEL SOLICITANTE
          <i class="bi bi-chevron-down ms-auto status-icon"></i>
        </div>
        <div class="seccion-body">

          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Semestre</span>
              <span class="dato-valor">{{ $alumno->Semestre ?? 'No disponible' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Número de Créditos</span>
              <span class="dato-valor">{{ $alumno->Creditos ?? 'No disponible' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Correo Electrónico</span>
              <span class="dato-valor">{{ $alumno->CorreoElectronico ?? 'No disponible' }}</span>
            </div>
          </div>

          <hr class="divider">

          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Inducción Plática Informativa PP</span>
              <span class="dato-valor">{{ $solicitud->Induccion_PP ? 'Sí' : 'No' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Tipo de Seguro</span>
              <span class="dato-valor">{{ $solicitud->Tipo_Seguro ? 'IMSS' : 'Otro' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">NSF</span>
              <span class="dato-valor">{{ $solicitud->NSF ?? 'No especificado' }}</span>
            </div>
          </div>

          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Fecha de Nacimiento</span>
              <span class="dato-valor">
                {{ $alumno->Fecha_Nacimiento ? \Carbon\Carbon::parse($alumno->Fecha_Nacimiento)->format('d/m/Y') : 'No disponible' }}
              </span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Estado</span>
              <span class="dato-valor">{{ $solicitud->Estado_Alumno == 'P' ? 'Pasante' : 'Alumno' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Carta Pasante</span>
              <span class="dato-valor">{{ $solicitud->Carta_Pasante ? 'Sí' : 'No' }}</span>
            </div>
          </div>





          <div class="dato-row">
              {{--Estadística General --}}
              <div class="dato-item">
                  <span class="dato-label">Estadística General</span>
                  @if ($pdfEstadistica)
                      <a href="{{ asset('storage/' . $pdfEstadistica) }}" target="_blank" class="pdf-link">
                          <i class="bi bi-file-pdf"></i> Ver PDF
                      </a>
                  @else
                      <span class="dato-valor">No disponible</span>
                  @endif
              </div>

              {{--Carta de Pasante --}}
              <div class="dato-item">
                  <span class="dato-label">Carta de Pasante</span>
                  @if ($solicitud->Carta_Pasante == 1 && $pdfPasante)
                      <a href="{{ asset('storage/' . $pdfPasante) }}" target="_blank" class="pdf-link">
                          <i class="bi bi-file-pdf"></i> Ver PDF
                      </a>
                    @else
                    <span class="dato-valor">N/A</span>
                  @endif
              </div>



            <div class="dato-item">
              <span class="dato-label">Egresado Situación Especial</span>
              <span class="dato-valor">{{ $solicitud->Egresado_Sit_Esp ? 'Sí' : 'No' }}</span>
            </div>
          </div>



          <div class="action-buttons">
            <button type="button" class="btn-aceptar btn-accion" data-seccion="solicitante" data-valor="1">
              <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>
            <button type="button" class="btn-rechazar btn-accion" data-seccion="solicitante" data-valor="0">
              <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
            <input type="hidden" name="seccion_solicitante" id="seccion_solicitante" value="">
          </div>
        </div>
      </div>

      {{-- SECCIÓN 2: EMPRESA/SECTOR --}}
      <div class="seccion-card">
        <div class="seccion-header" onclick="toggleSection(this)">
            <i class="bi bi-building"></i>
            DATOS GENERALES DE LAS PRÁCTICAS PROFESIONALES
            <i class="bi bi-chevron-down ms-auto status-icon"></i>
        </div>
        <div class="seccion-body">

            <div class="dato-row">
            <div class="dato-item">
                <span class="dato-label">Tipo de Sector</span>
                <span class="dato-valor">
                @if($tipoSector == 'privado') <i class="bi bi-building me-1"></i> Sector Privado
                @elseif($tipoSector == 'publico') <i class="bi bi-bank me-1"></i> Sector Público
                @elseif($tipoSector == 'uaslp') <i class="bi bi-mortarboard me-1"></i> Sector UASLP
                @else No especificado
                @endif
                </span>
            </div>
            </div>

            {{-- DATOS GENERALES DE LA EMPRESA (para cualquier sector) --}}
            <div class="dato-row">
            <div class="dato-item">
                <span class="dato-label">Nombre / Dependencia / Empresa</span>
                <span class="dato-valor">{{ $empresa->Nombre_Depn_Emp ?? 'No especificado' }}</span>
            </div>
            <div class="dato-item">
                <span class="dato-label">RFC</span>
                <span class="dato-valor">{{ $empresa->RFC_Empresa ?? 'No especificado' }}</span>
            </div>
            </div>

            <div class="dato-row">
            <div class="dato-item">
                <span class="dato-label">Dirección</span>
                <span class="dato-valor">
                {{ $empresa->Calle ?? 'No especificado' }}
                #{{ $empresa->Numero ?? 'No especificado' }},
                {{ $empresa->Colonia ?? 'No especificado' }},
                {{ $empresa->Municipio ?? 'No especificado' }},
                {{ $empresa->Estado ?? 'No especificado' }},
                CP {{ $empresa->Cp ?? 'No especificado' }}
                </span>
            </div>
            <div class="dato-item">
                <span class="dato-label">Teléfono</span>
                <span class="dato-valor">{{ $empresa->Telefono ?? 'No especificado' }}</span>
            </div>
            </div>

            {{-- DATOS ESPECÍFICOS SEGÚN EL SECTOR --}}
            @if ($privado)
            <div class="dato-row">
                <div class="dato-item">
                <span class="dato-label">Razón Social</span>
                <span class="dato-valor">{{ $privado->Razon_Social ?? 'No especificado' }}</span>
                </div>
            </div>
            <div class="dato-row">
                <div class="dato-item">
                <span class="dato-label">Área o Departamento</span>
                <span class="dato-valor">{{ $privado->Area_Depto ?? 'No especificado' }}</span>
                </div>
                <div class="dato-item">
                <span class="dato-label">Ramo</span>
                <span class="dato-valor">{{ $ramoOptions[$empresa->Ramo] ?? 'No especificado' }}</span>
                </div>
                <div class="dato-item">
                <span class="dato-label">Número de Trabajadores</span>
                <span class="dato-valor">{{ $numTrabajadoresOptions[$privado->Num_Trabajadores] ?? 'No especificado' }}</span>
                </div>
            </div>

            @elseif ($publico)
            <div class="dato-row">
                <div class="dato-item">
                <span class="dato-label">Área o Departamento</span>
                <span class="dato-valor">{{ $publico->Area_Depto ?? 'No especificado' }}</span>
                </div>
                <div class="dato-item">
                <span class="dato-label">Ámbito</span>
                <span class="dato-valor">
                    @switch($publico->Ambito)
                    @case(1) Municipal @break
                    @case(2) Estatal @break
                    @case(3) Federal @break
                    @default No especificado
                    @endswitch
                </span>
                </div>
            </div>

            @elseif ($uaslp)
            <div class="dato-row">
                <div class="dato-item">
                <span class="dato-label">Área o Departamento</span>
                <span class="dato-valor">{{ $uaslp->Area_Depto ?? 'No especificado' }}</span>
                </div>
                <div class="dato-item">
                <span class="dato-label">Tipo de Entidad</span>
                <span class="dato-valor">
                    {{ $uaslp->Tipo_Entidad == 1 ? 'Instituto' : ($uaslp->Tipo_Entidad == 2 ? 'Centro de Investigación' : 'No especificado') }}
                </span>
                </div>
                <div class="dato-item">
                <span class="dato-label">Entidad Académica</span>
                <span class="dato-valor">{{ $entidadOptions[$uaslp->Id_Entidad_Academica] ?? 'No especificado' }}</span>
                </div>
            </div>
            @endif

            <div class="action-buttons">
            <button type="button" class="btn-aceptar btn-accion" data-seccion="empresa" data-valor="1">
                <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>
            <button type="button" class="btn-rechazar btn-accion" data-seccion="empresa" data-valor="0">
                <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
            <input type="hidden" name="seccion_empresa" id="seccion_empresa" value="">
            </div>
        </div>
      </div>

      {{-- SECCIÓN 3: PROYECTO --}}
      <div class="seccion-card">
        <div class="seccion-header" onclick="toggleSection(this)">
          <i class="bi bi-clipboard-check"></i>
          PROYECTO Y ACTIVIDADES
          <i class="bi bi-chevron-down ms-auto status-icon"></i>
        </div>
        <div class="seccion-body">
          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Nombre del Proyecto</span>
              <span class="dato-valor">{{ $solicitud->Nombre_Proyecto }}</span>
            </div>
          </div>
          <div class="dato-item">
            <span class="dato-label">Actividades</span>
            <div class="bg-light p-3 rounded mt-2">
              {!! nl2br(e($solicitud->Actividades)) !!}
            </div>
          </div>

          <div class="action-buttons">
            <button type="button" class="btn-aceptar btn-accion" data-seccion="proyecto" data-valor="1">
              <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>
            <button type="button" class="btn-rechazar btn-accion" data-seccion="proyecto" data-valor="0">
              <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
            <input type="hidden" name="seccion_proyecto" id="seccion_proyecto" value="">
          </div>
        </div>
      </div>

      {{-- SECCIÓN 4: HORARIO --}}
      <div class="seccion-card">
        <div class="seccion-header" onclick="toggleSection(this)">
          <i class="bi bi-clock"></i>
          HORARIO
          <i class="bi bi-chevron-down ms-auto status-icon"></i>
        </div>
        <div class="seccion-body">
          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Horario de Entrada</span>
              <span class="dato-valor">{{ $solicitud->Horario_Entrada }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Horario de Salida</span>
              <span class="dato-valor">{{ $solicitud->Horario_Salida }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">Turno</span>
              <span class="dato-valor">{{ $horario }}</span>
            </div>
          </div>
          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">Días de Asistencia</span>
              <span class="dato-valor">{{ count($dias) ? implode(', ', $dias) : 'No especificado' }}</span>
            </div>
          </div>

          <div class="action-buttons">
            <button type="button" class="btn-aceptar btn-accion" data-seccion="horario" data-valor="1">
              <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>
            <button type="button" class="btn-rechazar btn-accion" data-seccion="horario" data-valor="0">
              <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
            <input type="hidden" name="seccion_horario" id="seccion_horario" value="">
          </div>
        </div>
      </div>

      {{-- SECCIÓN 5: CRÉDITOS --}}
      <div class="seccion-card">
        <div class="seccion-header" onclick="toggleSection(this)">
          <i class="bi bi-cash-coin"></i>
          CRÉDITOS / APOYO ECONÓMICO
          <i class="bi bi-chevron-down ms-auto status-icon"></i>
        </div>
        <div class="seccion-body">
          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">¿Requiere Créditos?</span>
              <span class="dato-valor">{{ $solicitud->Validacion_Creditos ? 'Sí' : 'No' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">¿Es Extensión de Prácticas?</span>
              <span class="dato-valor">{{ $solicitud->Extension_Practicas ? 'Sí' : 'No' }}</span>
            </div>
          </div>
          <div class="dato-row">
            <div class="dato-item">
              <span class="dato-label">¿Recibe Apoyo Económico?</span>
              <span class="dato-valor">{{ $solicitud->Expedicion_Recibos ? 'Sí' : 'No' }}</span>
            </div>
            <div class="dato-item">
              <span class="dato-label">¿Requiere Expedición de Recibos?</span>
              <span class="dato-valor">{{ $solicitud->Expedicion_Recibos ? 'Sí' : 'No' }}</span>
            </div>
          </div>

          <div class="action-buttons">
            <button type="button" class="btn-aceptar btn-accion" data-seccion="creditos" data-valor="1">
              <i class="bi bi-check-lg me-1"></i> Aceptar
            </button>
            <button type="button" class="btn-rechazar btn-accion" data-seccion="creditos" data-valor="0">
              <i class="bi bi-x-lg me-1"></i> Rechazar
            </button>
            <input type="hidden" name="seccion_creditos" id="seccion_creditos" value="">
          </div>
        </div>
      </div>

      {{-- COMENTARIOS --}}
      <div class="comentarios-section">
        <label for="comentario_encargado" class="form-label fw-bold mb-3">
          <i class="bi bi-chat-left-text me-2"></i>
          Comentarios del Encargado
        </label>
        <textarea name="comentario_encargado" id="comentario_encargado" rows="4" class="form-control" placeholder="Escriba observaciones o motivos de rechazo..."></textarea>
        <small class="text-muted mt-2 d-block">
          <i class="bi bi-info-circle me-1"></i>
          Estos comentarios serán visibles para el alumno
        </small>
      </div>

      {{-- BOTONES FINALES --}}
      <div class="mt-4 text-center d-flex gap-3 justify-content-center">
        <button type="submit" class="btn-submit">
          <i class="bi bi-check-circle-fill me-2"></i>
          Guardar Revisión
        </button>
        <a href="{{ route('encargado.solicitudes_alumnos') }}" class="btn-regresar">
          <i class="bi bi-arrow-left me-2"></i>
          Regresar
        </a>
      </div>
    </form>

  </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle de secciones (collapsar/expandir)
function toggleSection(header) {
  const body = header.nextElementSibling;
  const icon = header.querySelector('.status-icon');

  if (body.style.display === 'none') {
    body.style.display = 'block';
    icon.classList.remove('bi-chevron-right');
    icon.classList.add('bi-chevron-down');
  } else {
    body.style.display = 'none';
    icon.classList.remove('bi-chevron-down');
    icon.classList.add('bi-chevron-right');
  }
}

// Manejo de botones Aceptar/Rechazar
document.addEventListener('DOMContentLoaded', function() {
  const botones = document.querySelectorAll('.btn-accion');

  botones.forEach(btn => {
    btn.addEventListener('click', function() {
      const seccion = this.dataset.seccion;
      const valor = this.dataset.valor; // 1 = aceptar, 0 = rechazar
      const card = this.closest('.seccion-card');
      const inputHidden = document.getElementById(`seccion_${seccion}`);
      const contenedorBotones = this.closest('.action-buttons');
      const header = card.querySelector('.seccion-header');

      // Guardar el valor en el input oculto
      inputHidden.value = valor;

      // Cambiar color del header según elección
      header.classList.remove('aceptada', 'rechazada');
      if (valor == 1) {
        header.classList.add('aceptada');
        header.querySelector('.status-icon').className = 'bi bi-check-circle-fill ms-auto status-icon';
      } else {
        header.classList.add('rechazada');
        header.querySelector('.status-icon').className = 'bi bi-x-circle-fill ms-auto status-icon';
      }

      // Ocultar botones originales
      contenedorBotones.style.display = 'none';

      // Crear contenedor para el botón modificar
      const contenedorModificar = document.createElement('div');
      contenedorModificar.className = 'text-end pt-3 border-top';
      contenedorModificar.innerHTML = `
        <button type="button" class="btn-modificar">
          <i class="bi bi-pencil-square me-1"></i>
          Modificar Elección
        </button>
      `;

      // Función para restaurar al estado original
      contenedorModificar.querySelector('.btn-modificar').onclick = function() {
        header.classList.remove('aceptada', 'rechazada');
        header.querySelector('.status-icon').className = 'bi bi-chevron-down ms-auto status-icon';
        inputHidden.value = '';
        contenedorModificar.remove();
        contenedorBotones.style.display = 'flex';
      };

      // Insertar el botón de modificar
      card.querySelector('.seccion-body').appendChild(contenedorModificar);
    });
  });
});
</script>
@endpush

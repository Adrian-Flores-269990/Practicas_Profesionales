@extends('layouts.dsspp')
@section('title', 'Revisión de solicitud - DSSPP')

@php
    $diasMap = ['L'=>'Lunes','M'=>'Martes','X'=>'Miércoles','J'=>'Jueves','V'=>'Viernes','S'=>'Sábado','D'=>'Domingo'];
    $diasCadena = $solicitud->Dias_Semana ?? '';
    $dias = [];
    foreach (str_split($diasCadena) as $l) if(isset($diasMap[$l])) $dias[] = $diasMap[$l];

    $horario = $solicitud->Horario_Mat_Ves === 'M' ? 'Matutino' : ($solicitud->Horario_Mat_Ves === 'V' ? 'Vespertino' : 'No especificado');

    // Relaciones de sector/empresa
    $rel = $solicitud->dependenciaMercadoSolicitud;
    $empresa = $rel->dependenciaEmpresa ?? null;
    $privado = $rel->sectorPrivado ?? null;
    $publico = $rel->sectorPublico ?? null;
    $uaslp   = $rel->sectorUaslp ?? null;

    $tipoSector = $privado ? 'privado' : ($publico ? 'publico' : ($uaslp ? 'uaslp' : null));

    // Catálogos
    $ramoOptions = [1=>'Agricultura, ganadería y caza',2=>'Transporte y comunicaciones',3=>'Industria manufacturera',4=>'Restaurantes y hoteles',5=>'Servicios profesionales y técnicos especializados',6=>'Servicios de reparación y mantenimiento',7=>'Servicios educativos',8=>'Construcción',9=>'Otro'];
    $numTrabajadoresOptions = [1=>'Micro (1 - 30)',2=>'Pequeña (31 - 100)',3=>'Mediana (101 - 250)',4=>'Grande (más de 250)'];
    $actividadGiroOptions = [1=>'Extractiva',2=>'Manufacturera',3=>'Comercial',4=>'Comisionista',5=>'Servicio'];
    $entidadOptions = [0=>'Entidad2',1=>'Entidad1',2=>'Entidad3'];
@endphp

<style>
  .dato-label{font-weight:600;color:#002244}
  .dato-valor{color:#333}
  .seccion-datos{background:#f9fafc;border-radius:10px;padding:15px 20px}
</style>

@section('content')
<div class="container py-4">
  <nav class="navbar" style="background-color:#000066;">
    <div class="container-fluid justify-content-center">
      <span class="navbar-text text-white mx-auto fw-bold">
        <h4>Revisión de solicitud del alumno - Departamento de Servicio Social y Prácticas Profesionales</h4>
      </span>
    </div>
  </nav>

  <form action="{{ route('dsspp.autorizarSolicitud', $solicitud->Id_Solicitud_FPP01) }}" method="POST" id="form-revision">
    @csrf
    @method('PUT')

    <div class="accordion" id="accordionSolicitud">

      {{-- ================= DATOS GENERALES DEL SOLICITANTE ================= --}}
      <div class="accordion-item soli-card">
        <h2 class="accordion-header" id="h-solicitante">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
            DATOS GENERALES DEL SOLICITANTE
          </button>
        </h2>
        <div id="sec-solicitante" class="accordion-collapse collapse show" aria-labelledby="h-solicitante">
          <div class="accordion-body seccion-datos">
            @php $alumno = $solicitud->alumno; @endphp

            <div class="mb-2">
              <span class="dato-label">Fecha de solicitud:</span>
              <span class="dato-valor">{{ \Carbon\Carbon::parse($solicitud->Fecha_Solicitud ?? now())->format('d/m/Y') }}</span>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Nombre del alumno:</span>
                <span class="dato-valor">{{ $alumno->Nombre ?? 'No disponible' }} {{ $alumno->ApellidoP_Alumno ?? '' }} {{ $alumno->ApellidoM_Alumno ?? '' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Clave UASLP:</span>
                <span class="dato-valor">{{ $alumno->Clave_Alumno ?? 'No disponible' }}</span>
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Semestre:</span>
                <span class="dato-valor">{{ $alumno->Semestre ?? 'No disponible' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Número de créditos:</span>
                <span class="dato-valor">{{ $alumno->Creditos ?? 'No disponible' }}</span>
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Carrera:</span>
                <span class="dato-valor">{{ $alumno->Carrera ?? 'No disponible' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Correo electrónico:</span>
                <span class="dato-valor">{{ $alumno->CorreoElectronico ?? 'No disponible' }}</span>
              </div>
            </div>

            <hr>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Inducción plática informativa PP:</span>
                <span class="dato-valor">{{ $solicitud->Induccion_PP ? 'Sí' : 'No' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Tipo de seguro:</span>
                <span class="dato-valor">{{ $solicitud->Tipo_Seguro ? 'IMSS' : 'Otro' }}</span>
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">NSF:</span>
                <span class="dato-valor">{{ $solicitud->NSF ?? 'No especificado' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Fecha de nacimiento:</span>
                <span class="dato-valor">{{ \Carbon\Carbon::parse($alumno->Fecha_Nacimiento ?? '')->format('d/m/Y') ?? 'No disponible' }}</span>
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Estado:</span>
                <span class="dato-valor">{{ $solicitud->Estado_Alumno == 'P' ? 'Pasante' : 'Alumno' }}</span>
              </div>
            </div>

            <hr>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Carta pasante:</span>
                <span class="dato-valor">{{ $solicitud->Carta_Pasante ? 'Sí' : 'No' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Estadística general:</span>
                @if(!empty($solicitud->Estadistica_General))
                  <a href="{{ asset('storage/pdf_solicitudes/' . $solicitud->Estadistica_General) }}" target="_blank" class="btn btn-outline-primary btn-sm">Ver PDF</a>
                @else
                  <span class="dato-valor">No disponible</span>
                @endif
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-6">
                <span class="dato-label">Egresado situación especial:</span>
                <span class="dato-valor">{{ $solicitud->Egresado_Sit_Esp ? 'Sí' : 'No' }}</span>
              </div>
              <div class="col-md-6">
                <span class="dato-label">Constancia de vigencia de derechos:</span>
                @if(!empty($solicitud->Constancia_Vig_Der))
                  <a href="{{ asset('storage/pdf_solicitudes/' . $solicitud->Constancia_Vig_Der) }}" target="_blank" class="btn btn-outline-primary btn-sm">Ver PDF</a>
                @else
                  <span class="dato-valor">No disponible</span>
                @endif
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-md-4">
                <span class="dato-label">Extensión seguro facultativo:</span>
                <span class="dato-valor">{{ $solicitud->Extension_SF ? 'Sí' : 'No' }}</span>
              </div>
            </div>
          </div>

          <div class="mt-3 text-end">
            <button type="button" class="btn btn-success btn-accion" data-seccion="solicitante" data-valor="1">Aceptar</button>
            <button type="button" class="btn btn-danger btn-accion" data-seccion="solicitante" data-valor="0">Rechazar</button>
            <input type="hidden" name="seccion_solicitante" id="seccion_solicitante" value="">
          </div>
        </div>
      </div>

      {{-- ================= DATOS GENERALES DE LAS PRÁCTICAS / EMPRESA ================= --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-empresa">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
            DATOS GENERALES DE LAS PRÁCTICAS PROFESIONALES
          </button>
        </h2>
        <div id="sec-empresa" class="accordion-collapse collapse" aria-labelledby="h-empresa">
          <div class="accordion-body">
            <p><strong>Tipo de sector:</strong>
              @if($tipoSector == 'privado') Sector Privado
              @elseif($tipoSector == 'publico') Sector Público
              @elseif($tipoSector == 'uaslp') Sector UASLP
              @else No especificado
              @endif
            </p>

            {{-- Sector Privado --}}
            @if ($privado)
              <p><strong>Nombre de la empresa:</strong> {{ $empresa->Nombre_Depn_Emp ?? 'No especificado' }}</p>
              <p><strong>Razón Social:</strong> {{ $privado->Razon_Social }}</p>
              <p><strong>RFC:</strong> {{ $empresa->RFC_Empresa ?? 'No especificado' }}</p>
              <p><strong>Dirección:</strong> {{ $empresa->Calle }} #{{ $empresa->Numero }}, {{ $empresa->Colonia }}, {{ $empresa->Municipio }}, {{ $empresa->Estado }}, CP {{ $empresa->Cp }}</p>
              <p><strong>Teléfono:</strong> {{ $empresa->Telefono }}</p>
              <p><strong>Área o Departamento:</strong> {{ $privado->Area_Depto }}</p>
              <p><strong>Ramo:</strong> {{ $ramoOptions[$empresa->Ramo] ?? 'No especificado' }}</p>
              <p><strong>Número de Trabajadores:</strong> {{ $numTrabajadoresOptions[$privado->Num_Trabajadores] ?? 'No especificado' }}</p>
              <p><strong>Actividad o Giro:</strong> {{ $actividadGiroOptions[$privado->Actividad_Giro] ?? 'No especificado' }}</p>
              <p><strong>Empresa Outsourcing:</strong> {{ $privado->Emp_Outsourcing ? 'Sí' : 'No' }}</p>
              @if($privado->Razon_Social_Outsourcing)
                <p><strong>Razón Social Outsourcing:</strong> {{ $privado->Razon_Social_Outsourcing }}</p>
              @endif

            {{-- Sector Público --}}
            @elseif ($publico)
              <p><strong>Nombre de la dependencia:</strong> {{ $empresa->Nombre_Depn_Emp ?? 'No especificado' }}</p>
              <p><strong>RFC:</strong> {{ $empresa->RFC_Empresa ?? 'No especificado' }}</p>
              <p><strong>Ramo:</strong> {{ $ramoOptions[$empresa->Ramo] ?? 'No especificado' }}</p>
              <p><strong>Dirección:</strong> {{ $empresa->Calle }} #{{ $empresa->Numero }}, {{ $empresa->Colonia }}, {{ $empresa->Municipio }}, {{ $empresa->Estado }}, CP {{ $empresa->Cp }}</p>
              <p><strong>Teléfono:</strong> {{ $empresa->Telefono }}</p>
              <p><strong>Área o Departamento:</strong> {{ $publico->Area_Depto }}</p>
              <p><strong>Ámbito:</strong>
                @switch($publico->Ambito)
                  @case(1) Municipal @break
                  @case(2) Estatal @break
                  @case(3) Federal @break
                  @default No especificado
                @endswitch
              </p>

            {{-- Sector UASLP --}}
            @elseif ($uaslp)
              <p><strong>Área o Departamento:</strong> {{ $uaslp->Area_Depto }}</p>
              <p><strong>Tipo de Entidad:</strong>
                @if($uaslp->Tipo_Entidad == 1) Instituto
                @elseif($uaslp->Tipo_Entidad == 2) Centro de Investigación
                @else No especificado
                @endif
              </p>
              <p><strong>Entidad Académica:</strong> {{ $entidadOptions[$uaslp->Id_Entidad_Academica] ?? 'No especificado' }}</p>

            @else
              <p>No se encontraron datos del sector.</p>
            @endif

            <div class="mt-3 text-end">
              <button type="button" class="btn btn-success btn-accion" data-seccion="empresa" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="empresa" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_empresa" id="seccion_empresa" value="">
            </div>
          </div>
        </div>
      </div>

      {{-- ================= PROYECTO ================= --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-proyecto">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-proyecto" aria-expanded="false">
            PROYECTO Y ACTIVIDADES
          </button>
        </h2>
        <div id="sec-proyecto" class="accordion-collapse collapse" aria-labelledby="h-proyecto">
          <div class="accordion-body">
            <p><strong>Nombre del proyecto:</strong> {{ $solicitud->Nombre_Proyecto }}</p>
            <p><strong>Actividades:</strong></p>
            <div class="border rounded p-2 bg-light">{!! nl2br(e($solicitud->Actividades)) !!}</div>

            <div class="mt-3 text-end">
              <button type="button" class="btn btn-success btn-accion" data-seccion="proyecto" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="proyecto" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_proyecto" id="seccion_proyecto" value="">
            </div>
          </div>
        </div>
      </div>

      {{-- ================= HORARIO ================= --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-horario">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario" aria-expanded="false">
            HORARIO
          </button>
        </h2>
        <div id="sec-horario" class="accordion-collapse collapse" aria-labelledby="h-horario">
          <div class="accordion-body">
            <div class="row mb-2">
              <div class="col-md-6"><span class="dato-label">Horario de entrada: </span><span class="dato-valor">{{ $solicitud->Horario_Entrada }}</span></div>
              <div class="col-md-6"><span class="dato-label">Horario de salida: </span><span class="dato-valor">{{ $solicitud->Horario_Salida }}</span></div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6"><span class="dato-label">Matutino o Vespertino: </span><span class="dato-valor">{{ $horario }}</span></div>
              <div class="col-md-6"><span class="dato-label">Días de asistencia: </span><span class="dato-valor">{{ count($dias) ? implode(', ', $dias) : 'No especificado' }}</span></div>
            </div>

            <div class="mt-3 text-end">
              <button type="button" class="btn btn-success btn-accion" data-seccion="horario" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="horario" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_horario" id="seccion_horario" value="">
            </div>
          </div>
        </div>
      </div>

      {{-- ================= CRÉDITOS / APOYO ================= --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-creditos">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-creditos" aria-expanded="false">
            CRÉDITOS / APOYO ECONÓMICO
          </button>
        </h2>
        <div id="sec-creditos" class="accordion-collapse collapse" aria-labelledby="h-creditos">
          <div class="accordion-body">
            <div class="row mb-2">
              <div class="col-md-6"><span class="dato-label">¿Requiere créditos? </span><span class="dato-valor">{{ $solicitud->Validacion_Creditos ? 'Sí' : 'No' }}</span></div>
              <div class="col-md-6"><span class="dato-label">¿Es extensión de prácticas? </span><span class="dato-valor">{{ $solicitud->Extension_Practicas ? 'Sí' : 'No' }}</span></div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6"><span class="dato-label">¿Recibe apoyo económico? </span><span class="dato-valor">{{ $solicitud->Apoyo_Economico ? 'Sí' : 'No' }}</span></div>
              <div class="col-md-6"><span class="dato-label">¿Requiere expedición de recibos? </span><span class="dato-valor">{{ $solicitud->Expedicion_Recibos ? 'Sí' : 'No' }}</span></div>
            </div>

            <div class="mt-3 text-end">
              <button type="button" class="btn btn-success btn-accion" data-seccion="creditos" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="creditos" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_creditos" id="seccion_creditos" value="">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ================= COMENTARIOS DSSPP ================= --}}
    <div class="mt-4">
      <label for="comentario_departamento" class="form-label fw-semibold">Comentarios del Departamento DSSPP:</label>
      <textarea name="comentario_departamento" id="comentario_departamento" rows="3" class="form-control" placeholder="Escriba observaciones o motivos de rechazo..."></textarea>
    </div>

    <div class="mt-4 text-center">
      <button type="submit" class="btn btn-primary">Guardar revisión</button>
      <a href="{{ route('dsspp.solicitudes') }}" class="btn btn-secondary ms-2">Regresar</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-accion').forEach(btn => {
    btn.addEventListener('click', function() {
      const seccion = this.dataset.seccion;
      const valor   = this.dataset.valor; // 1 aceptar, 0 rechazar
      const card = this.closest('.accordion-item');
      const inputHidden = document.getElementById(`seccion_${seccion}`);
      const contenedorBotones = this.parentElement;
      const headerButton = card.querySelector('.accordion-button');

      inputHidden.value = valor;

      if (valor == 1) { headerButton.style.backgroundColor = '#458B4E'; headerButton.style.color = 'white'; }
      else            { headerButton.style.backgroundColor = '#C44545'; headerButton.style.color = 'white'; }

      contenedorBotones.style.display = 'none';
      const modificar = document.createElement('button');
      modificar.textContent = 'Modificar elección';
      modificar.className = 'btn btn-secondary mt-2';
      modificar.type = 'button';
      modificar.onclick = function() {
        headerButton.style.backgroundColor = '';
        headerButton.style.color = '';
        inputHidden.value = '';
        modificar.remove();
        contenedorBotones.style.display = 'block';
      };
      contenedorBotones.parentElement.appendChild(modificar);
    });
  });
});
</script>
@endsection
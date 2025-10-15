@extends('layouts.encargado')
@section('title', 'Revisión de solicitud')

@php
    $diasMap = [
        'L' => 'Lunes',
        'M' => 'Martes',
        'X' => 'Miércoles',
        'J' => 'Jueves',
        'V' => 'Viernes',
        'S' => 'Sábado',
        'D' => 'Domingo',
    ];

    $diasCadena = $solicitud->Dias_Semana ?? '';
    $dias = [];

    foreach (str_split($diasCadena) as $letra) {
        if (isset($diasMap[$letra])) {
            $dias[] = $diasMap[$letra];
        }
    }
@endphp

@php
    $horario = $solicitud->Horario_Mat_Ves === 'M' ? 'Matutino' :
               ($solicitud->Horario_Mat_Ves === 'V' ? 'Vespertino' : 'No especificado');
@endphp

@php
$rel = $solicitud->dependenciaMercadoSolicitud;

$empresa = null;
$privado = null;
$publico = null;
$uaslp = null;
$tipoSector = null;

if ($rel) {
    $empresa = $rel->dependenciaEmpresa;
    $privado = $rel->sectorPrivado;
    $publico = $rel->sectorPublico;
    $uaslp = $rel->sectorUaslp;

    if ($privado) {
        $tipoSector = 'privado';
    } elseif ($publico) {
        $tipoSector = 'publico';
    } elseif ($uaslp) {
        $tipoSector = 'uaslp';
    }
}
@endphp

@php
// Mapas para mostrar el texto en lugar del número
$ramoOptions = [
    1 => 'Agricultura, ganadería y caza',
    2 => 'Transporte y comunicaciones',
    3 => 'Industria manufacturera',
    4 => 'Restaurantes y hoteles',
    5 => 'Servicios profesionales y técnicos especializados',
    6 => 'Servicios de reparación y mantenimiento',
    7 => 'Servicios educativos',
    8 => 'Construcción',
    9 => 'Otro',
];

$numTrabajadoresOptions = [
    1 => 'Micro (1 - 30)',
    2 => 'Pequeña (31 - 100)',
    3 => 'Mediana (101 - 250)',
    4 => 'Grande (más de 250)',
];

$actividadGiroOptions = [
    1 => 'Extractiva',
    2 => 'Manufacturera',
    3 => 'Comercial',
    4 => 'Comisionista',
    5 => 'Servicio',
];
@endphp

@php
$entidadOptions = [
    0 => 'Entidad2',
    1 => 'Entidad1',
    2 => 'Entidad3'
];
@endphp

@section('content')
<div class="container py-4">
  <nav class="navbar" style="background-color: #000066;">
    <div class="container-fluid justify-content-center">
      <span class="navbar-text text-white mx-auto fw-bold">
        <h4>Revisión de solicitud del alumno</h4>
      </span>
    </div>
  </nav>

  {{-- FORMULARIO --}}
  <form action="{{ route('encargado.autorizar', $solicitud->Id_Solicitud_FPP01) }}" method="POST" id="form-revision">
    @csrf
    @method('PUT')

    <div class="accordion" id="accordionSolicitud">

      {{-- DATOS GENERALES DEL SOLICITANTE --}}
        <div class="accordion-item soli-card">
        <h2 class="accordion-header" id="h-solicitante">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-solicitante" aria-expanded="true">
            Datos generales del solicitante
            </button>
        </h2>
        <div id="sec-solicitante" class="accordion-collapse collapse show" aria-labelledby="h-solicitante">
            <div class="accordion-body">
            @php $alumno = $solicitud->alumno; @endphp

            <p><strong>Nombre:</strong>
                {{ $alumno->Nombre ?? 'No disponible' }}
                {{ $alumno->ApellidoP_Alumno ?? '' }}
                {{ $alumno->ApellidoM_Alumno ?? '' }}
            </p>

            <p><strong>Clave UASLP:</strong> {{ $alumno->Clave_Alumno ?? 'No disponible' }}</p>
            <p><strong>Carrera:</strong> {{ $alumno->Carrera ?? 'No disponible' }}</p>
            <p><strong>Materia:</strong> {{ $alumno->Clave_Materia ?? 'No disponible' }}</p>
            <p><strong>Semestre:</strong> {{ $alumno->Semestre ?? 'No disponible' }}</p>
            <p><strong>Correo electrónico:</strong> {{ $alumno->CorreoElectronico ?? 'No disponible' }}</p>
            <p><strong>Teléfono celular:</strong> {{ $alumno->TelefonoCelular ?? 'No disponible' }}</p>

            <hr>

            <p><strong>Tipo de seguro:</strong> {{ $solicitud->Tipo_Seguro ? 'IMSS' : 'Otro' }}</p>
            <p><strong>NSF:</strong> {{ $solicitud->NSF ?? 'No especificado' }}</p>
            <p><strong>Constancia de derechos:</strong> {{ $solicitud->Constancia_Vig_Der ? 'Sí' : 'No' }}</p>
            <p><strong>Estadística general:</strong> {{ $solicitud->Estadistica_General ? 'Sí' : 'No' }}</p>
            <p><strong>Extensión de prácticas:</strong> {{ $solicitud->Extension_Practicas ? 'Sí' : 'No' }}</p>
            <p><strong>Fecha de inicio:</strong> {{ $solicitud->Fecha_Inicio }}</p>
            <p><strong>Fecha de término:</strong> {{ $solicitud->Fecha_Termino }}</p>

            <div class="mt-3 text-center">
                <button type="button" class="btn btn-success btn-accion" data-seccion="solicitante" data-valor="1">Aceptar</button>
                <button type="button" class="btn btn-danger btn-accion" data-seccion="solicitante" data-valor="0">Rechazar</button>
                <input type="hidden" name="seccion_solicitante" id="seccion_solicitante" value="">
            </div>
            </div>
        </div>
        </div>

      {{-- EMPRESA / SECTOR --}}
        <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-empresa">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-empresa">
            Datos de la empresa y sector
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

            {{-- SECTOR PRIVADO --}}
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

            {{-- SECTOR PÚBLICO --}}
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

            {{-- SECTOR UASLP --}}
            @elseif ($uaslp)
            <p><strong>Área o Departamento:</strong> {{ $uaslp->Area_Depto }}</p>
            <p><strong>Tipo de Entidad:</strong>
                @if($uaslp->Tipo_Entidad == 1) Instituto
                @elseif($uaslp->Tipo_Entidad == 2) Centro de Investigación
                @else No especificado
                @endif
            </p>
            <p><strong>Entidad Académica:</strong> {{ $entidadOptions[$uaslp->Id_Entidad_Academica] ?? 'No especificado' }} </p>
            @else
            <p>No se encontraron datos del sector.</p>
            @endif

            <div class="mt-3 text-center">
                <button type="button" class="btn btn-success btn-accion" data-seccion="empresa" data-valor="1">Aceptar</button>
                <button type="button" class="btn btn-danger btn-accion" data-seccion="empresa" data-valor="0">Rechazar</button>
                <input type="hidden" name="seccion_empresa" id="seccion_empresa" value="">
            </div>

            </div>
        </div>
      </div>

      {{-- PROYECTO --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-proyecto">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-proyecto" aria-expanded="false">
            Proyecto y actividades
          </button>
        </h2>
        <div id="sec-proyecto" class="accordion-collapse collapse" aria-labelledby="h-proyecto">
          <div class="accordion-body">
            <p><strong>Nombre del proyecto:</strong> {{ $solicitud->Nombre_Proyecto }}</p>
            <p><strong>Actividades:</strong></p>
            <div class="border rounded p-2 bg-light">
              {!! nl2br(e($solicitud->Actividades)) !!}
            </div>

            <div class="mt-3 text-center">
              <button type="button" class="btn btn-success btn-accion" data-seccion="proyecto" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="proyecto" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_proyecto" id="seccion_proyecto" value="">
            </div>
          </div>
        </div>
      </div>

      {{-- HORARIO --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-horario">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-horario" aria-expanded="false">
            Horario y días de asistencia
          </button>
        </h2>
        <div id="sec-horario" class="accordion-collapse collapse" aria-labelledby="h-horario">
          <div class="accordion-body">
            <p><strong>Matutino o Vespertino:</strong> {{ $horario }}</p>
            <p><strong>Hora de entrada:</strong> {{ $solicitud->Horario_Entrada }}</p>
            <p><strong>Hora de salida:</strong> {{ $solicitud->Horario_Salida }}</p>
            <p><strong>Días de asistencia:</strong>
            {{ count($dias) ? implode(', ', $dias) : 'No especificado' }}
            </p>

            <div class="mt-3 text-center">
              <button type="button" class="btn btn-success btn-accion" data-seccion="horario" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="horario" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_horario" id="seccion_horario" value="">
            </div>
          </div>
        </div>
      </div>

      {{-- CRÉDITOS Y APOYO --}}
      <div class="accordion-item soli-card mt-3">
        <h2 class="accordion-header" id="h-creditos">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-creditos" aria-expanded="false">
            Créditos y apoyo económico
          </button>
        </h2>
        <div id="sec-creditos" class="accordion-collapse collapse" aria-labelledby="h-creditos">
          <div class="accordion-body">
            <p><strong>¿Requiere créditos?</strong> {{ $solicitud->Validacion_Creditos ? 'Sí' : 'No' }}</p>
            <p><strong>¿Es extensión de prácticas?</strong> {{ $solicitud->Extension_Practicas ? 'Sí' : 'No' }}</p>
            <p><strong>¿Requiere expedición de recibos?</strong> {{ $solicitud->Expedicion_Recibos ? 'Sí' : 'No' }}</p>
            <p><strong>¿Recibe apoyo económico?</strong> {{ $solicitud->Apoyo_Economico ? 'Sí' : 'No' }}</p>

            <div class="mt-3 text-center">
              <button type="button" class="btn btn-success btn-accion" data-seccion="creditos" data-valor="1">Aceptar</button>
              <button type="button" class="btn btn-danger btn-accion" data-seccion="creditos" data-valor="0">Rechazar</button>
              <input type="hidden" name="seccion_creditos" id="seccion_creditos" value="">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- COMENTARIOS --}}
    <div class="mt-4">
      <label for="comentario_encargado" class="form-label fw-semibold">Comentarios del encargado:</label>
      <textarea name="comentario_encargado" id="comentario_encargado" rows="3" class="form-control" placeholder="Escriba observaciones o motivos de rechazo..."></textarea>
    </div>

    {{-- BOTONES GENERALES --}}
    <div class="mt-4 text-center">
      <button type="submit" class="btn btn-primary">Guardar revisión</button>
      <a href="{{ route('encargado.solicitudes_alumnos') }}" class="btn btn-secondary ms-2">Regresar</a>
    </div>
  </form>
</div>

{{-- Script para manejo de botones dinámicos --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const botones = document.querySelectorAll('.btn-accion');

  botones.forEach(btn => {
    btn.addEventListener('click', function() {
      const seccion = this.dataset.seccion;
      const valor = this.dataset.valor; // 1 = aceptar, 0 = rechazar
      const card = this.closest('.accordion-item');
      const inputHidden = document.getElementById(`seccion_${seccion}`);
      const contenedorBotones = this.parentElement;
      const headerButton = card.querySelector('.accordion-button'); // encabezado del acordeón

      // Guardar el valor en el input oculto
      inputHidden.value = valor;

      // Cambiar color del encabezado según elección
      if (valor == 1) {
        headerButton.style.backgroundColor = '#458B4E'; // verde bonito
        headerButton.style.color = 'white';
      } else {
        headerButton.style.backgroundColor = '#C44545'; // rojo bonito
        headerButton.style.color = 'white';
      }

      // Ocultar botones originales
      contenedorBotones.style.display = 'none';

      // Crear botón "Modificar elección"
      const botonModificar = document.createElement('button');
      botonModificar.textContent = 'Modificar elección';
      botonModificar.className = 'btn btn-secondary mt-2';
      botonModificar.type = 'button';

      // Función para restaurar al estado original
      botonModificar.onclick = function() {
        headerButton.style.backgroundColor = '';
        headerButton.style.color = '';
        inputHidden.value = '';
        botonModificar.remove();
        contenedorBotones.style.display = 'block';
      };

      // Insertar el botón de modificar después del contenedor
      contenedorBotones.parentElement.appendChild(botonModificar);
    });
  });
});
</script>


@endsection

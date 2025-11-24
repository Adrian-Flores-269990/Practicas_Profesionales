@extends('layouts.administrador')
@section('title', 'Empresas Registradas')

@section('content')

@php
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

$entidadOptions = [
    0 => 'Entidad2',
    1 => 'Entidad1',
    2 => 'Entidad3'
];
@endphp

<h4 class="text-center fw-bold text-white py-3 shadow-sm"
    style="background: linear-gradient(90deg, #00124E, #003B95);">
    EMPRESAS REGISTRADAS
</h4>

<div class="container mt-4">

  {{-- ALERTA --}}
  @if(session('success'))
  <div id="alerta-flotante"
       class="alert alert-success shadow-lg position-fixed top-0 start-50 translate-middle-x mt-3"
       style="z-index: 1055; opacity: 0;">
       {{ session('success') }}
  </div>
  @endif

  {{-- TABLA --}}
  <div class="table-responsive shadow-sm rounded" style="max-width: 100%; overflow-x: auto;">
  <table class="table table-hover align-middle table-bordered" style="min-width: 1500px;">

    <thead style="background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);">
      <tr>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:200px; padding:15px;">Nombre</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:300px; padding:15px;">Dirección</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:220px; padding:15px;">Contacto</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:140px; padding:15px;">Sector</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:300px; padding:15px;">Mercado / Datos</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:100px; padding:15px;">Acciones</th>
      </tr>
    </thead>

    <tbody style="white-space: nowrap;">
        @foreach($empresas as $e)
        @php
          $empresa = $e->dependenciaEmpresa ?? null;
        @endphp

        @if(!$empresa)
          @continue
        @endif

        <tr>
            <td class="text-center fw-bold">{{ $empresa->Nombre_Depn_Emp }}</td>

            {{-- DIRECCIÓN --}}
            <td class="text-center">
                {{ $empresa->Calle }} #{{ $empresa->Numero }},
                Col. {{ $empresa->Colonia }},
                {{ $empresa->Municipio }}, {{ $empresa->Estado }},
                CP {{ $empresa->Cp }}
            </td>

            {{-- CONTACTO --}}
            <td class="text-center" style="font-size:0.9rem; white-space: nowrap;">
                <span>Teléfono: {{ $empresa->Teléfono ?? 'N/A' }}</span>,
                <span>RFC: {{ $empresa->RFC_Empresa ?? 'N/A' }}</span>,
                <span>Ramo: {{ $empresa->Ramo ? $ramoOptions[$empresa->Ramo] ?? $empresa->Ramo : 'N/A' }}</span>
            </td>

            {{-- SECTOR --}}
            <td class="text-center">
                @if($e->sectorPrivado) <span>Privado</span> @endif
                @if($e->sectorPublico) <span>Público</span> @endif
                @if($e->sectorUaslp) <span>UASLP</span> @endif
            </td>

            {{-- MERCADO / OTROS DATOS --}}
            <td class="text-center" style="font-size:0.9rem; white-space: nowrap;">
                @php $sep = false; @endphp

                @if($sp = $e->sectorPrivado)
                    <span>Departamento: {{ $sp->Area_Depto ?? 'N/A' }}</span>,
                    <span>Trabajadores: {{ $sp->Num_Trabajadores ? $numTrabajadoresOptions[$sp->Num_Trabajadores] ?? $sp->Num_Trabajadores : 'N/A' }}</span>,
                    <span>Giro: {{ $sp->Actividad_Giro ? $actividadGiroOptions[$sp->Actividad_Giro] ?? $sp->Actividad_Giro : 'N/A' }}</span>,
                    <span>Razón social: {{ $sp->Razon_Social ?? 'N/A' }}</span>
                    @php $sep = true; @endphp
                @endif

                @if($spu = $e->sectorPublico)
                    @if($sep), @endif
                    <span>Departamento: {{ $spu->Area_Depto ?? 'N/A' }}</span>,
                    <span>Ámbito: {{ $spu->Ambito ?? 'N/A' }}</span>
                    @php $sep = true; @endphp
                @endif

                @if($su = $e->sectorUaslp)
                    @if($sep), @endif
                    <span>Departamento: {{ $su->Area_Depto ?? 'N/A' }}</span>,
                    <span>Tipo: {{ $su->Tipo_Entidad ? $entidadOptions[$su->Tipo_Entidad] ?? $su->Tipo_Entidad : 'N/A' }}</span>
                    @php $sep = true; @endphp
                @endif

                @if($e->Porcentaje)
                    @if($sep), @endif
                    <span>Mercado: {{ $e->Porcentaje ?? 'N/A' }}%</span>
                @endif
            </td>

            {{-- ACCIONES --}}
            <td class="text-center">
                <form action="{{ route('administrador.empresa.destroy', $empresa->Id_Depn_Emp) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta empresa?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  const alerta = document.getElementById("alerta-flotante");
  if (alerta) {
    alerta.style.transition = "opacity .6s ease";
    alerta.style.opacity = "1";
    setTimeout(() => {
      alerta.style.opacity = "0";
      alerta.addEventListener("transitionend", () => alerta.remove());
    }, 3000);
  }
});
</script>
@endsection

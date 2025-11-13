@extends('layouts.administrador')
@section('title', 'Empresas Registradas')

@section('content')

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

  <div class="table-responsive shadow-sm rounded" style="max-width: 100%; overflow-x: auto;">
  <table class="table table-hover align-middle table-bordered" style="min-width: 1400px;">

    <thead style="background: linear-gradient(135deg, #384daaff 0%, #84a3e2ff 100%);">
      <tr>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:200px; padding:15px;">Nombre</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:300px; padding:15px;">Dirección</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:220px; padding:15px;">Contacto</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:140px; padding:15px;">Sector</th>
        <th class="text-center text-uppercase text-white fw-bold" style="background:transparent; min-width:300px; padding:15px;">Mercado / Datos</th>
      </tr>
    </thead>

    <tbody style="white-space: nowrap;">

        @foreach($empresas as $e)
        @php
          $empresa = $e->dependenciaEmpresa ?? null;
        @endphp

        @if(!$empresa)
          @continue {{-- Salta esta iteración si no tiene empresa relacionada --}}
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
          <td class="text-center">
            <div><strong>Tel:</strong> {{ $empresa->Teléfono }}</div>
            <div><strong>RFC:</strong> {{ $empresa->RFC_Empresa }}</div>
            <div><strong>Ramo:</strong> {{ $empresa->Ramo }}</div>
          </td>

          {{-- SECTOR --}}
          <td class="text-center">
            @if($e->sectorPrivado)
              <span class="badge bg-secondary d-block mb-1">Privado</span>
            @endif
            @if($e->sectorPublico)
              <span class="badge bg-info d-block mb-1">Público</span>
            @endif
            @if($e->sectorUaslp)
              <span class="badge bg-warning d-block">UASLP</span>
            @endif
          </td>

          {{-- MERCADO / OTROS DATOS --}}
          <td class="text-center" style="font-size:0.9rem">

            {{-- PRIVADO --}}
            @if($sp = $e->sectorPrivado)
              <div class="mb-1"><strong>Depto:</strong> {{ $sp->Area_Depto }}</div>
              <div><strong>Trabajadores:</strong> {{ $sp->Num_Trabajadores }}</div>
              <div><strong>Giro:</strong> {{ $sp->Actividad_Giro }}</div>
              <div><strong>Razón social:</strong> {{ $sp->Razon_Social }}</div>
            @endif

            {{-- PUBLICO --}}
            @if($spu = $e->sectorPublico)
              <div><strong>Depto:</strong> {{ $spu->Area_Depto }}</div>
              <div><strong>Ámbito:</strong> {{ $spu->Ambito }}</div>
            @endif

            {{-- UASLP --}}
            @if($su = $e->sectorUaslp)
              <div><strong>Depto:</strong> {{ $su->Area_Depto }}</div>
              <div><strong>Tipo:</strong> {{ $su->Tipo_Entidad }}</div>
            @endif

            <div class="mt-1"><strong>% Mercado:</strong> {{ $e->Porcentaje }}%</div>
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
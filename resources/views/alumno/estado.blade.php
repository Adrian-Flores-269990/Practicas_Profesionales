@extends('layouts.alumno')

@section('title','Estado del Alumno')

@section('content')
  @include('partials.nav.registro')

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    ESTADO DEL ALUMNO DURANTE EL PROCESO DE PRÁCTICAS PROFESIONALES
  </h4>
    <div class="bg-white p-4 rounded shadow-sm w-100">
        <div class="container mb-3">
            <div class="d-flex justify-content-center flex-wrap mt-4">
                <div class="d-flex align-items-center gap-3 mx-4">
                    <div class="leyenda-cuadro" style="background: green;"></div>
                    <span>Actividad realizada</span>
                </div>
                <div class="d-flex align-items-center gap-3 mx-4">
                    <div class="leyenda-cuadro" style="background: yellow;"></div>
                    <span>Actividad en proceso</span>
                </div>
                <div class="d-flex align-items-center gap-3 mx-4">
                    <div class="leyenda-cuadro" style="background: red;"></div>
                    <span>Actividad pendiente</span>
                </div>
            </div>
            <div class="arrow-grid">
                <!-- Fila 1 -->
                <div class="arrow arrow-right">REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES</div>
                <div class="arrow arrow-right">AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)</div>
                <div class="arrow arrow-right">AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)</div>
                <!-- Fila 2 -->
                <div class="arrow arrow-left">CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)</div>
                <div class="arrow arrow-left">AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)</div>
                <div class="arrow arrow-left">REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES</div>
                <!-- Fila 3 -->
                <div class="arrow arrow-right">CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)</div>
                <div class="arrow arrow-right">CARTA DE PRESENTACIÓN (ALUMNO)</div>
                <div class="arrow arrow-right">CARTA DE ACEPTACIÓN (ALUMNO)</div>
                <!-- Fila 4 -->
                <div class="arrow arrow-left">SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA</div>
                <div class="arrow arrow-left">CARTA DE DESGLOSE DE PERCEPCIONES</div>
                <div class="arrow arrow-left">CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)</div>
                <!-- Fila 5 -->
                <div class="arrow arrow-right">RECIBO DE PAGO</div>
                <div class="arrow arrow-right">REPORTE PARCIAL NO. X</div>
                <div class="arrow arrow-right">REVISIÓN REPORTE PARCIAL NO. X</div>
                <!-- Fila 6 -->
                <div class="arrow arrow-left">REVISIÓN REPORTE FINAL</div>
                <div class="arrow arrow-left">REPORTE FINAL</div>
                <div class="arrow arrow-left">CORRECCIÓN REPORTE PARCIAL NO. X</div>
                <!-- Fila 7 -->
                <div class="arrow arrow-right">CORRECCIÓN REPORTE FINAL</div>
                <div class="arrow arrow-right">CALIFICACIÓN REPORTE FINAL</div>
                <div class="arrow arrow-right">CARTA DE TÉRMINO</div>
                <!-- Fila 8 -->
                <div class="arrow arrow-left">EVALUACIÓN DEL ALUMNO</div>
                <div class="arrow arrow-left">CALIFICACIÓN FINAL</div>
                <div class="arrow arrow-left">EVALUACIÓN DE LA EMPRESA</div>
                <!-- Fila 9 -->
                <div class="arrow arrow-right">LIBERACIÓN DEL ALUMNO</div>
                <div class="arrow arrow-right">CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES</div>
                <div class="arrow arrow-right">DOCUMENTO EXTRA (EJEMPLO)</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

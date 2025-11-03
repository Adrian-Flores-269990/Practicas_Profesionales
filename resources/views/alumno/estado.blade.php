@extends('layouts.alumno')

@section('title', 'Estado del Alumno')

@section('content')
@include('partials.nav.registro')

<style>
/* FLECHAS ALTERNADAS — 3 COLUMNAS FIJAS Y RESPONSIVAS SIN ROMPER FILAS */

/* Contenedor principal */
.arrow-grid {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 40px;
  margin-top: 40px;
  padding: 30px 10px;
}

/* Cada fila */
.arrow-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* siempre 3 columnas */
  justify-items: center;
  align-items: center;
  width: 100%;
  position: relative;
  gap: 0;
}

/* Flechas base */
.arrow {
  position: relative;
  width: 90%; /* ocupa la mayor parte del espacio de su columna */
  min-height: 90px;
  padding: 20px 25px;
  font-weight: 600;
  font-size: 13px;
  text-align: center;
  border-radius: 6px;
  border: 1.5px solid rgba(0, 0, 0, 0.3);
  line-height: 1.4em;
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
  z-index: 2;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.arrow:hover {
  transform: scale(1.03);
}

/* Flechas direccionales */
.arrow-right::after,
.arrow-left::before {
  content: "";
  position: absolute;
  top: 0;
  width: 0;
  height: 0;
  border-top: 45px solid transparent;
  border-bottom: 45px solid transparent;
  z-index: 3;
}

/* Flecha derecha */
.arrow-right::after {
  right: -40px;
  border-left: 40px solid;
}

/* Flecha izquierda */
.arrow-left::before {
  left: -45px;
  border-right: 45px solid;
}

/* Compensación visual */
.arrow-left { margin-left: 45px; }
.arrow-right { margin-right: 45px; }

/* Colores coherentes */
.bg-success { background-color: #198754 !important; color: #fff; }
.bg-warning { background-color: #ffc107 !important; color: #000; }
.bg-danger  { background-color: #dc3545 !important; color: #fff; }

.bg-success.arrow-right::after { border-left-color: #198754; }
.bg-success.arrow-left::before { border-right-color: #198754; }

.bg-warning.arrow-right::after { border-left-color: #ffc107; }
.bg-warning.arrow-left::before { border-right-color: #ffc107; }

.bg-danger.arrow-right::after  { border-left-color: #dc3545; }
.bg-danger.arrow-left::before  { border-right-color: #dc3545; }

/* 📱 Responsivo: mantener 3 columnas, pero reducir tamaño */
@media (max-width: 1200px) {
  .arrow {
    width: 85%;
    font-size: 12px;
    min-height: 80px;
    padding: 15px 20px;
  }
  .arrow-right::after,
  .arrow-left::before {
    border-top: 40px solid transparent;
    border-bottom: 40px solid transparent;
  }
}

@media (max-width: 992px) {
  .arrow {
    width: 80%;
    font-size: 11px;
    min-height: 70px;
    padding: 12px 18px;
  }
  .arrow-right::after,
  .arrow-left::before {
    border-top: 35px solid transparent;
    border-bottom: 35px solid transparent;
  }
}

@media (max-width: 768px) {
  .arrow {
    width: 75%;
    font-size: 10px;
    min-height: 60px;
    padding: 10px 15px;
  }
  .arrow-right::after,
  .arrow-left::before {
    border-top: 30px solid transparent;
    border-bottom: 30px solid transparent;
  }
}

/* 🔒 Evitar que se apilen o colapsen */
@media (max-width: 500px) {
  .arrow-row {
    grid-template-columns: repeat(3, 1fr);
    transform: scale(0.9);
  }
}

/* Leyenda */
.leyenda-cuadro {
  width: 25px;
  height: 25px;
  border-radius: 5px;
  border: 1px solid black;
}
</style>

<div class="container-fluid my-0 p-0">
  <h4 class="text-center fw-bold text-white py-3" style="background-color: #000066;">
    ESTADO DEL ALUMNO DURANTE EL PROCESO DE PRÁCTICAS PROFESIONALES
  </h4>

  <div class="bg-white p-4 rounded shadow-sm w-100">

    @if(session('error'))
    <div class="alert alert-danger text-center fw-bold">
      {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success text-center fw-bold">
      {{ session('success') }}
    </div>
    @endif
    
    <!-- Leyenda -->
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
    </div>

    <!-- FLECHAS POR FILAS -->
    <div class="arrow-grid">
      @foreach ($procesos->chunk(3) as $filaIndex => $fila)
        @php
          $directionClass = $filaIndex % 2 == 0 ? 'arrow-right' : 'arrow-left';
        @endphp

        <div class="arrow-row">
          @foreach ($fila as $proceso)
            @php
              $colorClass = match($proceso->estado) {
                  'realizado' => 'bg-success',
                  'proceso' => 'bg-warning',
                  default => 'bg-danger',
              };
            @endphp

            <div class="arrow {{ $colorClass }} {{ $directionClass }}">
              {{ strtoupper($proceso->etapa) }}
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

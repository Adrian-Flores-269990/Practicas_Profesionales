@extends('layouts.alumno')
@section('title', 'Estado del Alumno')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alumno.css') }}?v={{ filemtime(public_path('css/alumno.css')) }}">
@endpush

@section('content')
@include('partials.nav.registro')

<style>
/* ================================
  DISEÑO ROADMAP OPTIMIZADO
================================ */

.roadmap-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* Leyenda moderna */
.leyenda-modern {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin: 10px 0 35px 0;
  flex-wrap: wrap;
}

.leyenda-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 24px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

.leyenda-dot {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  position: relative;
}

.leyenda-dot::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  animation: pulse 2s ease infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.3); opacity: 0; }
}

.leyenda-realizado .leyenda-dot { background: #1f8950ff; }
.leyenda-realizado .leyenda-dot::after { background: #1f8950ff; }

.leyenda-proceso .leyenda-dot { background: #f59e0b; }
.leyenda-proceso .leyenda-dot::after { background: #f59e0b; }

.leyenda-pendiente .leyenda-dot { background: #ef4444; }
.leyenda-pendiente .leyenda-dot::after { background: #ef4444; }

.leyenda-text {
  font-size: 16px;
  font-weight: 600;
  color: #374151;
}

/* Grid de tarjetas */
.procesos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 24px;
  margin-top: 0;
}

/* Tarjeta individual */
.proceso-card {
  background: white;
  border-radius: 16px;
  padding: 0;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  position: relative;
  animation: slideUp 0.5s ease forwards;
  opacity: 0;
}

@keyframes slideUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.proceso-card {
  transform: translateY(20px);
}

.proceso-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* Barra superior de color */
.proceso-header {
  height: 8px;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.proceso-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Cuerpo de la tarjeta */
.proceso-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Número del paso */
.proceso-numero {
  display: flex;
  align-items: center;
  gap: 12px;
}

.numero-badge {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  color: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.numero-text {
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Título del proceso */
.proceso-titulo {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  line-height: 1.4;
  min-height: 44px;
  display: flex;
  align-items: center;
}

/* Estado visual */
.proceso-status-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-icon {
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Colores por estado - REALIZADO (Verde) */
.estado-realizado .proceso-header {
  background: #1f8950ff;
}

.estado-realizado .numero-badge {
  background: #1f8950ff;
}

.estado-realizado .status-badge {
  background: #d1fae5;
  color: #065f46;
  border: 2px solid #1f8950ff;
}

/* Colores por estado - EN PROCESO (Amarillo/Naranja) */
.estado-proceso .proceso-header {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.estado-proceso .numero-badge {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.estado-proceso .status-badge {
  background: #fef3c7;
  color: #92400e;
  border: 2px solid #f59e0b;
}

/* Colores por estado - PENDIENTE (Rojo) */
.estado-pendiente .proceso-header {
  background: #f01a1aff;
}

.estado-pendiente .numero-badge {
  background: #f01a1aff;
}

.estado-pendiente .status-badge {
  background: #fff;
  color: #991b1b;
  border: 2px solid #ef4444;
}

/* Colores por estado - DESHABILITADO (Gris) */
.estado-deshabilitado .proceso-header {
  background: #626262ff;
}

.estado-deshabilitado .numero-badge {
  background: #626262ff;
}

.estado-deshabilitado .status-badge {
  background: #adadadff;
  color: #333333ff;
  border: 2px solid #414141ff;
}

/* Responsive */
@media (max-width: 768px) {
  .procesos-grid {
    grid-template-columns: 1fr;
  }
}

/* Animaciones escalonadas */
.proceso-card:nth-child(1) { animation-delay: 0.05s; }
.proceso-card:nth-child(2) { animation-delay: 0.1s; }
.proceso-card:nth-child(3) { animation-delay: 0.15s; }
.proceso-card:nth-child(4) { animation-delay: 0.2s; }
.proceso-card:nth-child(5) { animation-delay: 0.25s; }
.proceso-card:nth-child(6) { animation-delay: 0.3s; }
.proceso-card:nth-child(7) { animation-delay: 0.35s; }
.proceso-card:nth-child(8) { animation-delay: 0.4s; }
.proceso-card:nth-child(9) { animation-delay: 0.45s; }
.proceso-card:nth-child(10) { animation-delay: 0.5s; }
.proceso-card:nth-child(11) { animation-delay: 0.55s; }
.proceso-card:nth-child(12) { animation-delay: 0.6s; }
</style>

<div class="container-fluid my-0 p-0">
    <div class="detalle-header">
        <div class="container">
            <h4 class="text-center">
                <i class="bi bi-file-earmark-text me-2"></i>
                ESTADO DEL ALUMNO DURANTE EL PROCESO DE PRÁCTICAS PROFESIONALES
            </h4>
        </div>
    </div>

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

        <div class="roadmap-container">
            <!-- Leyenda -->
            <div class="leyenda-modern">
                <div class="leyenda-item leyenda-realizado">
                    <div class="leyenda-dot"></div>
                    <span class="leyenda-text">Actividad Realizada</span>
                </div>
                <div class="leyenda-item leyenda-proceso">
                    <div class="leyenda-dot"></div>
                    <span class="leyenda-text">Actividad en Proceso</span>
                </div>
                <div class="leyenda-item leyenda-pendiente">
                    <div class="leyenda-dot"></div>
                    <span class="leyenda-text">Actividad Pendiente</span>
                </div>
            </div>

            <!-- Grid de Procesos -->
            <div class="procesos-grid">
                @foreach ($procesos as $index => $proceso)
                    @php
                        $estadoClass = match($proceso->estado) {
                            'realizado' => 'estado-realizado',
                            'proceso' => 'estado-proceso',
                            'deshabilitado' => 'estado-deshabilitado',
                            default => 'estado-pendiente',
                        };

                        $estadoTexto = match($proceso->estado) {
                            'realizado' => 'Completado',
                            'proceso' => 'En Proceso',
                            'deshabilitado' => 'Deshabilitado',
                            default => 'Pendiente',
                        };

                        $icono = match($proceso->estado) {
                            'realizado' => '✓',
                            'proceso' => '⟳',
                            'deshabilitado' => '—',
                            default => '○',
                        };
                    @endphp

                    <div class="proceso-card {{ $estadoClass }}">
                        <div class="proceso-header"></div>
                        <div class="proceso-body">
                            <div class="proceso-numero">
                                <div class="numero-badge">{{ $index + 1 }}</div>
                                <span class="numero-text">Paso {{ $index + 1 }}</span>
                            </div>

                            <div class="proceso-titulo">
                                {{ $proceso->etapa }}
                            </div>

                            <div class="proceso-status-container">
                                <div class="status-badge">
                                    <span class="status-icon">{{ $icono }}</span>
                                    <span>{{ $estadoTexto }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

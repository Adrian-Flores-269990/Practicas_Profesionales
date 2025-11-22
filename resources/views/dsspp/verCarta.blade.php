@extends('layouts.dsspp')
@section('title', 'Vista previa de Carta')

@section('content')
<div class="container py-4">

    <h3 class="text-center mb-3 fw-bold">Vista previa de Carta de Presentación</h3>

    {{-- Alerta --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- PDF --}}
    <div class="card shadow p-3 mb-4">
        @if($pdfPath)
            <iframe src="{{ $pdfPath }}" width="100%" height="600px"></iframe>
        @else
            <p class="text-danger">No se encontró el archivo PDF.</p>
        @endif
    </div>

    <div class="d-flex justify-content-between">
        
        {{-- BOTÓN VOLVER --}}
        <a href="{{ route('dsspp.carta') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>

        <div>
            {{-- ACEPTAR --}}
            <form action="{{ route('dsspp.carta.aprobar', request()->clave) }}" 
                  method="POST" style="display:inline;">
                @csrf
                <button class="btn btn-success px-4">Aceptar</button>
            </form>

            {{-- RECHAZAR --}}
            <form action="{{ route('dsspp.carta.rechazar', request()->clave) }}" 
                  method="POST" style="display:inline;">
                @csrf
                <button class="btn btn-danger px-4">Rechazar</button>
            </form>
        </div>
    </div>

</div>
@endsection

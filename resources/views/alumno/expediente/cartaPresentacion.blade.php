@extends('layouts.alumno')

@section('title','Carta de Presentación')

@push('styles')
<style>
    .seccion {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        margin-bottom: 2rem;
    }
    .btn-descargar {
        background: #0d6efd; color: white; font-weight: 600;
        border-radius: 8px; padding: 0.7rem 1.4rem;
    }
    .btn-eliminar {
        background: #dc3545; color: white; font-weight: 600;
        border-radius: 8px; padding: 0.7rem 1.4rem;
    }
    .btn-regresar {
        background: #6c757d; color: white; font-weight: 600;
        border-radius: 8px; padding: 0.7rem 1.4rem;
    }
    .btn-enviar {
        background: #198754; color: white; font-weight: 600;
        border-radius: 8px; padding: 0.7rem 1.4rem;
    }
    .upload-box {
        border: 2px dashed #6c757d;
        padding: 2rem; text-align: center;
        border-radius: 10px; color: #6c757d;
    }
</style>
@endpush

@section('content')

@php
    // Llega desde el controlador
    $alumno = session('alumno');
    $clave = $alumno['cve_uaslp'] ?? null;
@endphp

<div class="container-fluid p-0 my-0">

    <h4 class="text-center fw-bold text-white py-3" style="background-color:#000066;">
        CARTA DE PRESENTACIÓN
    </h4>

    {{-- =======================================
        SI YA EXISTE CARTA FIRMADA → SOLO PREVIEW
       ======================================= --}}
    @if($pdfPathFirmada ?? false)
        <div class="seccion">
            <h5 class="fw-bold mb-3">Carta de presentación firmada</h5>

            <iframe src="{{ $pdfPathFirmada }}" width="100%" height="600px"></iframe>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('alumno.estado') }}" class="btn btn-regresar">Regresar al Estado</a>

                <form action="{{ route('cartaPresentacion.eliminar', [
                        'claveAlumno' => $clave,
                        'tipo' => 'Carta_Presentacion_Firmada'
                    ]) }}" method="POST">
                    @csrf
                    <button class="btn btn-eliminar" onclick="return confirm('¿Eliminar documento?')">
                        Eliminar Documento
                    </button>
                </form>
            </div>
        </div>

    @else
        {{-- ============================
            1. DESCARGAR CARTA GENERADA
           ============================ --}}
        <div class="seccion">
            <h5 class="fw-bold mb-3">Descargar la carta de presentación</h5>

            @if($pdfPath)
                <p class="text-muted">
                    La carta ya fue generada por DSSPP y revisada por el Encargado. Puedes descargarla para llevarla a la empresa.
                </p>

                <div class="text-center mt-4">
                    <a href="{{ asset($pdfPath) }}" class="btn btn-descargar" download>
                        Descargar Carta Presentación PDF
                    </a>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    Aún no existe una carta de presentación generada por DSSPP.
                </div>
            @endif
        </div>

        {{-- ============================
            2. SUBIR CARTA FIRMADA
           ============================ --}}
        <div class="seccion">
            <h5 class="fw-bold mb-3">Subir carta de presentación firmada por la empresa</h5>

            @if(!$pdfPath)
                <div class="alert alert-secondary text-center">
                    Debes esperar a que DSSPP genere tu carta para poder subir la firmada.
                </div>
            @else
                <form action="{{ route('cartaPresentacion.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="claveAlumno" value="{{ $clave }}">

                    <div class="upload-box mb-3">
                        <p class="mb-2">Arrastra o selecciona la carta firmada (solo PDF, máx 20 MB)</p>
                        <input type="file" name="archivo" accept="application/pdf" class="form-control mt-2" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-eliminar">Cancelar</button>
                        <button type="submit" class="btn btn-enviar">Enviar</button>
                    </div>
                </form>
            @endif
        </div>
    @endif

</div>

@endsection

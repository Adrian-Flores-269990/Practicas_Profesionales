<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato FPP02</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; margin: 40px; color: #111; }
        h1, h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        .section { margin-bottom: 25px; }
        .section-title { background-color: #dbe4ff; padding: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>FORMATO FPP02</h2>
    <h3>ASIGNACIÓN DE PRÁCTICAS PROFESIONALES</h3>
    <br>

    {{-- DATOS DEL ALUMNO --}}
    <div class="section">
        <div class="section-title">Datos del alumno</div>
        <table>
            <tr>
                <th>Nombre</th>
                <td>
                    {{ $alumno->Nombre ?? '---' }}
                    {{ $alumno->ApellidoP_Alumno ?? '' }}
                    {{ $alumno->ApellidoM_Alumno ?? '' }}
                </td>
            </tr>
            <tr>
                <th>Clave UASLP</th>
                <td>{{ $alumno->Clave_Alumno ?? '---' }}</td>
            </tr>
            <tr>
                <th>Carrera</th>
                <td>{{ $alumno->Carrera ?? '---' }}</td>
            </tr>
        </table>
    </div>

    {{-- DATOS DE LA EMPRESA --}}
    <div class="section">
        <div class="section-title">Datos de la empresa o dependencia</div>
        <table>
            <tr>
                <th>Nombre</th>
                <td>{{ $empresa->Nombre_Depn_Emp ?? '---' }}</td>
            </tr>
            <tr>
                <th>RFC</th>
                <td>{{ $empresa->RFC_Empresa ?? '---' }}</td>
            </tr>
            <tr>
                <th>Dirección</th>
                <td>
                    {{ $empresa->Calle ?? '' }} #{{ $empresa->Numero ?? '' }},
                    {{ $empresa->Colonia ?? '' }},
                    {{ $empresa->Municipio ?? '' }},
                    {{ $empresa->Estado ?? '' }},
                    CP {{ $empresa->Cp ?? '' }}
                </td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $empresa->Telefono ?? '---' }}</td>
            </tr>
        </table>
    </div>

    {{-- DATOS DEL PROYECTO --}}
    <div class="section">
        <div class="section-title">Datos del proyecto</div>
        <table>
            <tr>
                <th>Nombre del proyecto</th>
                <td>{{ $solicitud->Nombre_Proyecto ?? '---' }}</td>
            </tr>
            <tr>
                <th>Área o departamento</th>
                <td>{{ $sector->Area_Depto ?? $empresa->Area_Depto ?? '---' }}</td>
            </tr>
            <tr>
                <th>Horario</th>
                <td>{{ $solicitud->Horario_Entrada ?? '---' }} - {{ $solicitud->Horario_Salida ?? '---' }}</td>
            </tr>
            <tr>
                <th>Periodo</th>
                <td>{{ $solicitud->Periodo ?? '---' }}</td>
            </tr>
        </table>
    </div>

    {{-- DATOS DEL FPP02 --}}
    @if(isset($fpp02))
    <div class="section">
        <div class="section-title">Asignación oficial</div>
        <table>
            <tr>
                <th>Asignación Oficial DSSPP</th>
                <td>{{ $fpp02->Asignacion_Oficial_DSSPP ? 'Sí' : 'No' }}</td>
            </tr>
            <tr>
                <th>Fecha de Asignación</th>
                <td>{{ \Carbon\Carbon::parse($fpp02->Fecha_Asignacion)->format('d/m/Y') ?? '---' }}</td>
            </tr>
            <tr>
                <th>Servicio Social</th>
                <td>{{ $fpp02->Servicio_Social ? 'Sí' : 'No' }}</td>
            </tr>
            <tr>
                <th>Duración</th>
                <td>{{ $fpp02->Num_Meses ?? '---' }} meses ({{ $fpp02->Total_Horas ?? '---' }} horas)</td>
            </tr>
        </table>
    </div>
    @endif
</body>
</html>

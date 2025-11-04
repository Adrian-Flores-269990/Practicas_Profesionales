<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato FPP02</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; margin: 30px; }
        h1, h2, h3 { text-align: center; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h2>Formato FPP02</h2>
    <p><strong>Alumno:</strong> {{ $alumno->nombre ?? '---' }}</p>
    <p><strong>Clave UASLP:</strong> {{ $alumno->cve_uaslp ?? '---' }}</p>
    <p><strong>Carrera:</strong> {{ $alumno->carrera ?? '---' }}</p>

    <div class="section">
        <h3>Datos de la solicitud</h3>
        <table>
            <tr>
                <th>Dependencia</th>
                <td>{{ $solicitud->Nombre_Dependencia ?? '---' }}</td>
            </tr>
            <tr>
                <th>Proyecto</th>
                <td>{{ $solicitud->Nombre_Proyecto ?? '---' }}</td>
            </tr>
            <tr>
                <th>Periodo</th>
                <td>{{ $solicitud->Periodo ?? '---' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>

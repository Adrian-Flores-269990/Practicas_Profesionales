<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Recibo para Ayuda Económica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .titulo {
            background-color: #000066;
            color: white;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 14px;
            table-layout: fixed;
        }
        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .fw-bold {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .no-border {
            border: none;
        }
        .firma {
            height: 60px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>

    <div class="titulo">
        SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA
    </div>

    <table>
        <tr>
            <td colspan="2" class="fw-bold">SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA</td>
            <td class="fw-bold">F-SPC10</td>
        </tr>
        <tr>
            <td class="fw-bold">NOMBRE DEL ALUMNO:</td>
            <td>{{ $data['nombre'] }}</td>
            <td class="fw-bold">CARRERA: {{ $data['carrera'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">CLAVE DEL ALUMNO:</td>
            <td>{{ $data['clave'] }}</td>
            <td class="fw-bold">FECHA SOLICITUD: {{ $data['fecha'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">PERIODO:</td>
            <td>{{ $data['periodo'] }}</td>
            <td class="fw-bold">CANTIDAD: ${{ $data['cantidad'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">NOMBRE DE LA EMPRESA:</td>
            <td colspan="2">{{ $data['empresa'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">PERSONA QUE AUTORIZA:</td>
            <td>{{ $data['autoriza'] }}</td>
            <td class="fw-bold">TEL (EMPRESA): {{ $data['telefono_empresa'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">CARGO:</td>
            <td colspan="2">{{ $data['cargo'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">TEL (ALUMNO):</td>
            <td>{{ $data['telefono_alumno'] }}</td>
            <td class="fw-bold">FECHA ENTREGA: {{ $data['fecha_entrega'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">FIRMA DE RECIBIDO</td>
            <td colspan="2" class="firma"></td>
        </tr>
    </table>

    <p>
        <span class="fw-bold">¿Por parte de quién tiene el seguro?: </span>
        <span style="text-decoration: underline;">{{ $data['seguro'] }}</span>
    </p>
</body>
</html>

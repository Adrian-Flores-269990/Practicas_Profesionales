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
            font-weight: bold;
            padding: 12px;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 14px;
        }
        td {
            border: 1px solid #000;
            padding: 8px 12px;
            vertical-align: middle;
        }
        .fw-bold {
            font-weight: bold;
            background-color: #f2f2f2;
            width: 25%;
        }
        .firma {
            height: 60px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>

    <div class="titulo">
        <span>SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA</span>
        <span>FOLIO: F-SPC10</span>
    </div>

    <table>
        <tr>
            <td class="fw-bold">NOMBRE DEL ALUMNO:</td>
            <td colspan="3">{{ $data['nombre'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">CARRERA:</td>
            <td colspan="3">{{ $data['carrera'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">CLAVE DEL ALUMNO:</td>
            <td>{{ $data['clave'] }}</td>
            <td class="fw-bold">TELÉFONO (ALUMNO):</td>
            <td>{{ $data['telefono_alumno'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">FECHA SOLICITUD:</td>
            <td>{{ $data['fecha'] }}</td>
            <td class="fw-bold">FECHA ENTREGA:</td>
            <td>{{ $data['fecha_entrega'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">PERIODO:</td>
            <td>{{ $data['periodo'] }}</td>
            <td class="fw-bold">CANTIDAD:</td>
            <td>${{ $data['cantidad'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">NOMBRE DE LA EMPRESA:</td>
            <td>{{ $data['empresa'] }}</td>
            <td class="fw-bold">TELÉFONO (EMPRESA):</td>
            <td>{{ $data['telefono_empresa'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">PERSONA QUE AUTORIZA:</td>
            <td>{{ $data['autoriza'] }}</td>
            <td class="fw-bold">CARGO:</td>
            <td>{{ $data['cargo'] }}</td>
        </tr>
        <tr>
            <td class="fw-bold">FIRMA DE RECIBIDO:</td>
            <td colspan="3" class="firma"></td>
        </tr>
    </table>
    <p>
        <span class="fw-bold">¿Por parte de quién tiene el seguro?:</span>
        <span style="text-decoration: underline;">{{ $data['seguro'] }}</span>
    </p>
</body>
</html>

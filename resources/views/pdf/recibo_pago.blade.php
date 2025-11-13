<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 24px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 10px; }
        .seccion { border:1px solid #444; padding:10px 14px; margin-bottom:12px; border-radius:4px; }
        .fila { display:flex; justify-content:space-between; margin-bottom:6px; }
        .label { font-weight: bold; width:40%; }
        .valor { width:58%; text-align:right; }
        table { width:100%; border-collapse: collapse; margin-top:10px; }
        th, td { border:1px solid #444; padding:6px; font-size:11px; }
        th { background:#f0f0f0; }
        .totales { text-align:right; font-weight:bold; }
        .footer { margin-top:30px; font-size:10px; text-align:center; color:#555; }
    </style>
</head>
<body>
    <h1>RECIBO DE PAGO - PRÁCTICAS PROFESIONALES</h1>
    <div class="seccion">
        <div class="fila"><span class="label">Folio:</span><span class="valor">{{ $data['folio'] }}</span></div>
        <div class="fila"><span class="label">Fecha de Solicitud:</span><span class="valor">{{ $data['fecha_solicitud'] }}</span></div>
        <div class="fila"><span class="label">Periodo:</span><span class="valor">{{ $data['fecha_inicio'] }} al {{ $data['fecha_termino'] }}</span></div>
        <div class="fila"><span class="label">Fecha de Entrega:</span><span class="valor">{{ $data['fecha_entrega'] }}</span></div>
    </div>

    <div class="seccion">
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Apoyo económico por periodo</td>
                    <td style="text-align:right;">$ {{ number_format($data['salario'],2) }}</td>
                </tr>
                <tr>
                    <td class="totales">TOTAL</td>
                    <td class="totales">$ {{ number_format($data['salario'],2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="seccion">
        <div class="fila"><span class="label">Autoriza:</span><span class="valor">{{ $data['autoriza'] }}</span></div>
        <div class="fila"><span class="label">Cargo:</span><span class="valor">{{ $data['cargo_autoriza'] }}</span></div>
    </div>

    <p style="font-size:10px;">Este recibo corresponde al apoyo económico pactado en el marco de las prácticas profesionales del alumno. Cualquier duda o aclaración deberá realizarse dentro de los 5 días hábiles posteriores a la fecha de entrega.</p>

    <div class="footer">Generado automáticamente - Departamento de Servicio Social y Prácticas Profesionales</div>
</body>
</html>
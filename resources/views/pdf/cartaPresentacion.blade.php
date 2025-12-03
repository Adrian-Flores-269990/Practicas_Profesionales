<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presentación PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            margin: -30;
            padding: 0;
        }

        .encabezadoYpie {
            font-family: 'Roboto', sans-serif;
            font-size: 11pt;
            font-weight: 50;           /* ultra delgado */
            letter-spacing: -0.5pt;     /* letras más pegadas */
            transform: skew(-10deg);    /* inclinación visual */
            color: #ADADAD; 
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .izquierda {
            width: 5cm;
            padding: 0;
            margin: 0;
        }

        .derecha {
            padding-left: 0.65cm;
            padding-right: 1.32cm;
            /*padding-top: 1.42cm;*/
            padding-top: 0.77cm;
            /*padding-bottom: 1.62cm;*/
            padding-bottom: 0.97cm;
            vertical-align: top;
        }
        
        .textoJunto p {
            margin-bottom: -15px;
        }

        .imagen-superpuesta {
            position: absolute;
            top: 20px; /* distancia desde arriba */
            left: 300px; /* distancia desde la izquierda */
            width: 100px; /* tamaño de la imagen */
            z-index: 10; /* asegura que esté encima del texto */
            opacity: 0.8; /* opcional, para que el texto se vea debajo */
            }

    </style>
</head>
<body>

    @php
        use Carbon\Carbon;

        $diasArreglo = [
            'L' => 'lunes',
            'M' => 'martes',
            'X' => 'miércoles',
            'J' => 'jueves',
            'V' => 'viernes',
            'S' => 'sábado',
            'D' => 'domingo',
        ];
        $orden = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

        $dias = str_split($solicitud['Dias_Semana']);
        $indices = array_map(fn($d) => array_search($d, $orden), $dias);

        $consecutivos = true;
        for ($i = 1; $i < count($indices); $i++) {
            if ($indices[$i] !== $indices[$i - 1] + 1) {
                $consecutivos = false;
                break;
            }
        }

        if ($consecutivos) {
            $primerDia = $orden[$indices[0]];
            $ultimoDia = $orden[$indices[count($indices) - 1]];
            $texto = 'de ' . $diasArreglo[$primerDia] . ' a ' . $diasArreglo[$ultimoDia];
        } else {
            $texto = implode(', ', array_map(fn($d) => $diasArreglo[$d], $dias));
        }

        $horaEntrada = Carbon::createFromFormat('H:i:s', $solicitud['Horario_Entrada'])->format('G:i');
        $horaSalida = Carbon::createFromFormat('H:i:s', $solicitud['Horario_Salida'])->format('G:i');

        function capitalizar($texto) {
            $texto = mb_strtolower($texto, 'UTF-8');
            $palabras = explode(' ', $texto);
            return implode(' ', array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1), 'UTF-8') . mb_substr($p, 1), $palabras));
        }
    @endphp

    <table class="tabla">
        <tr>
            <!-- Columna izquierda: imagen con medidas exactas -->
            <td class="izquierda">
                <img src="{{ public_path('images/logoUaslp.jpg') }}"
                    style="width: 5cm; height: 27.65cm; object-fit: fill; display: block;">
            </td>

            <!-- Columna derecha: contenido -->
            <td class="derecha">
                <div class="encabezadoYpie" style="text-align: right;">
                    @php
                        $anios = $anio - 1945;
                    @endphp
                    <p>“1945-{{ $anio }}: "{{ $anios }} años formando profesionales de la ingeniería en beneficio de la sociedad"<p>
                </div>
                <div style="text-align: right;">
                    <p style="margin-top: 0; margin-bottom: -5px;">San Luis Potosí, S.L.P. a {{ $fechaHoy }}</p>
                    @if($solicitud['Validacion_Creditos'] == 1)
                        <p><strong>Of. Num.</strong> FI/DSSPP/PCV/124.2025<p>
                    @else
                        <p><strong>Of. Num.</strong> FI/DSSPP/PSV/124.2025<p>
                    @endif
                </div>
                <div class="textoJunto" style="font-weight: bold;">
                    <p>NICOLAS ALEJANDRO CORRENTI</p>
                    <p>DEPARTAMENTO TI</p>
                    <p style="margin-bottom: 15px;">PEFAI</p>
                    <p>P R E S E N T E.-</p>
                </div>
                <div style="margin-top: 30px; margin-bottom: 30px;">
                    <p style="text-align: right;"><strong>Asunto:</strong> Presentación Prácticas Profesionales</p>
                    <div style="margin-bottom: 20px;">
                        Por este conducto nos permitimos presentar a sus finas atenciones a <strong>{{ capitalizar($alumno['Nombre']) ?? 'S/D' }} {{ capitalizar($alumno['ApellidoP_Alumno']) ?? 'S/D' }} {{ capitalizar($alumno['ApellidoM_Alumno']) ?? 'S/D' }},</strong> 
                        con No. de clave <strong>{{ $solicitud['Clave_Alumno'] ?? 'S/D' }},</strong> de la carrera <strong>{{ capitalizar($alumno['Carrera']) ?? 'S/D' }},</strong> cubierto con seguro 
                        facultativo número <strong>{{ $solicitud['NSF'] ?? 'S/D' }},</strong> y quien de acuerdo con el <strong>Reglamento Interno de Servicio Social 
                        y Prácticas Profesionales de la Facultad de Ingeniería</strong> está en aptitud para realizar sus 
                        <strong>Prácticas Profesionales 
                        @if($solicitud['Validacion_Creditos'] == 1)
                            (Con validación de créditos),
                        @else
                            (Sin validación de créditos),
                        @endif</strong> en el departamento <strong>S/D,</strong> asignado al 
                        proyecto: <strong>{{ $solicitud['Nombre_Proyecto'] ?? 'S/D' }},</strong> realizando las siguientes actividades: <strong>{{ $solicitud['Actividades'] ?? 'S/D' }}.</strong> Quien cubrirá un horario de <strong>{{ $texto ?? 'S/D' }} de {{ $horaEntrada ?? 'S/D' }} a {{ $horaSalida ?? 'S/D' }} HRS.</strong>
                    </div>
                    <div style="margin-bottom: 30px;">
                        Lo anterior bajo la supervisión de <strong>{{ $asesor['Nombre'] ?? 'S/D' }} {{ $asesor['Apellido_Paterno'] ?? 'S/D' }} {{ $asesor['Apellido_Materno'] ?? 'S/D' }}.</strong> Cubriendo un periodo de prácticas del <strong>{{ $fechaInicio ?? 'S/D' }}</strong> al <strong>{{ $fechaTermino ?? 'S/D' }}</strong>.
                    </div>
                    <div style="font-weight: bold; text-align: center;">
                        "SIEMPRE AUTÓNOMA POR MI PATRIA EDUCARÉ"
                    </div>
                </div>
                <div class="textoJunto" style="position: relative; text-align: center; font-size: 12px;">
                    <img src="{{ public_path('images/sello.png') }}" class="imagen-superpuesta"
                        style="width: 3.92cm; height: 4.03cm;">
                    <p>COORDINADOR DE SERVICIO SOCIAL</p>
                    <p style="margin-bottom: 2.90cm;">DE LA FACULTAD DE INGENIERÍA</p>
                    <p>____________________________________</p>
                    <p>M.C. GUILLERMO ALVARADO VALDEZ</p>
                </div>
                <div class="textoJunto" style="margin-bottom: 20px; position: absolute; bottom: 0;">
                    <p class="encabezadoYpie">c.c.p. Encargado de Prácticas Profesionales de carrera</p>
                    <p class="encabezadoYpie">c.c.p. Empresa</p>
                    <p class="encabezadoYpie">c.c.p. Alumno</p>
                    <p>&nbsp;</p>
                    <p class="encabezadoYpie">*L.G.I. BMI</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

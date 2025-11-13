<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>FPP02 - Registro de Solicitud</title>
    <style>
        @page {
            margin: 15mm 20mm 10mm 10mm;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            max-width: 95%;
            margin: 0 auto;
            padding: 0 10mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .header-logos {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        
        .logo-left, .logo-right {
            display: table-cell;
            vertical-align: middle;
            width: 18%;
            text-align: center;
        }
        
        .header-text {
            display: table-cell;
            vertical-align: middle;
            width: 64%;
            text-align: center;
        }
        
        .logo-left img, .logo-right img {
            height: 100px;
            width: auto;
            max-width: 100%;
        }
        
        .header-text h3 {
            font-size: 10pt;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
        }
        
        .header-text p {
            font-size: 8pt;
            margin: 1px 0;
        }
        
        .document-title {
            background-color: #e0e0e0;
            padding: 5px 6px;
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            border: 1px solid #000;
            margin: 8px 0 10px 0;
            line-height: 1.2;
        }
        
        .alumno-info {
            margin-bottom: 10px;
            font-size: 8pt;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        
        .alumno-line {
            margin: 3px 0;
        }
        
        .label {
            font-weight: bold;
            font-size: 7.5pt;
        }
        
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 6px 1px 6px;
            min-width: 250px;
        }
        
        .underline-small {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 6px 1px 6px;
            min-width: 120px;
        }
        
        .section-title {
            background-color: #FFFF;
            padding: 4px 5px;
            font-weight: bold;
            font-size: 7.5pt;
            border: 1px solid #FFFF;
            margin-top: 10px;
            margin-bottom: 0;
        }
        
        .first-section {
            margin-top: 8px;
        }
        
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 7pt;
        }
        
        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        
        table.main-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 6.5pt;
            line-height: 1.2;
        }
        
        table.main-table td {
            font-size: 7pt;
            min-height: 18px;
        }
        
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin: 0 3px;
            vertical-align: middle;
            background: white;
        }
        
        .checkbox.checked::after {
            content: 'X';
            display: block;
            text-align: center;
            line-height: 10px;
            font-weight: bold;
            font-size: 8pt;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 2px;
            text-align: center;
            font-size: 6.5pt;
        }
        
        .footer-note {
            margin-top: 10px;
            font-size: 7pt;
            text-align: left;
        }
        
        .small-text {
            font-size: 6.5pt;
        }
        
        .smaller-text {
            font-size: 6pt;
        }
    </style>
</head>



@php $alumno = $solicitud->alumno; 
@endphp

<body>
    <div class="container">
    <!-- ENCABEZADO -->
    <div class="header">
        <div class="header-logos">
            <div class="logo-left">
                <img src="{{ public_path('images/logoUaslpSolido.png') }}" alt="Logo UASLP">
            </div>
            <div class="header-text">
                <h3>UNIVERSIDAD AUTÓNOMA DE SAN LUIS POTOSÍ</h3>
                <h3>FACULTAD DE INGENIERÍA</h3>
                <p>ÁREA MECÁNICA Y ELÉCTRICA</p>
                <p class="subtitle">ESPACIO DE FORMACIÓN DE PRÁCTICAS PROFESIONALES</p>
            </div>
            <div class="logo-right">
                <img src="{{ public_path('images/logoIngenieriaUaslp.png') }}" alt="Logo Ingeniería">
            </div>
        </div>
    </div>
    
    <!-- TÍTULO DEL DOCUMENTO -->
    <div class="document-title">
        HOJA DE REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES<br>
        PARA ACREDITAR EL ESPACIO DE FORMACIÓN DE PRÁCTICAS PROFESIONALES.
    </div>
    
    <!-- INFORMACIÓN DEL ALUMNO -->
    <div class="alumno-info">
        <div class="alumno-line">
            <span class="label">ALUMNO(A):</span> 
            <span class="underline">{{ $nombreCompleto }}</span>
            <span class="label" style="margin-left: 15px;">CLAVE ÚNICA:</span> 
            <span class="underline-small">{{ $claveUnica }}</span>
        </div>
    </div>
    
    <!-- I. REQUISITOS -->
    <div class="section-title first-section">I.- REQUISITOS:</div>
    <table class="main-table">
        <tr>
            <th style="width: 25%;">No. de Créditos probados a<br>la fecha:</th>
            <th style="width: 25%;">Nivel del plan de estudios<br>aprobado a la fecha:</th>
            <th style="width: 25%;">No. Créditos a cursar o<br>cursando en otros espacios<br>de formación:</th>
            <th style="width: 25%;">Total No. créditos a cursar con<br>el espacio de formación de<br>Prácticas Profesionales I:</th>
        </tr>
        <tr>
            <td style="height: 20px;">&nbsp;</td>
            <td>{{ $alumno['creditos'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
    
    <table class="main-table" style="margin-top: 2px;">
        <tr>
            <th colspan="2" style="width: 60%;">Asignación oficial del DSSPP (Departamento<br>de Servicio Social y Prácticas Profesionales)<br>(X):</th>
            <th style="width: 40%;">Fecha de asignación<br><span class="smaller-text">(dd/mm/aaaa):</span></th>
        </tr>
        <tr>
            <td style="width: 30%; text-align: center; height: 18px;"><span class="label">SI</span> <span class="checkbox"></span></td>
            <td style="width: 30%; text-align: center;"><span class="label">NO</span> <span class="checkbox checked"></span></td>
            <td>&nbsp;</td>
        </tr>
    </table>
    
    <!-- II. ASIGNACIÓN DEL DSSPP -->
    <div class="section-title">II.- DE LA ASIGNACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (DSSPP):</div>
    <table class="main-table">
        <tr>
            <th colspan="3" style="width: 45%;">Período de Prácticas Profesionales asignado por el<br>DSSPP:</th>
            <th colspan="2" style="width: 35%;">Jornada laboral:</th>
            <th colspan="2" style="width: 20%;">Servicio Social que se realizará en el<br>mismo periodo (X):</th>
        </tr>
        <tr>
            <td style="width: 20%; text-align: center;"><strong>Fecha Inicial</strong><br><span class="smaller-text">(dd/mm/aa)</span></td>
            <td style="width: 20%; text-align: center;"><strong>Fecha Final</strong><br><span class="smaller-text">(dd/mm/aa)</span></td>
            <td style="width: 15%; text-align: center;"><strong>Días de la<br>Semana</strong></td>
            <td style="width: 15%; text-align: center;"><strong>De las:</strong></td>
            <td style="width: 15%; text-align: center;"><strong>A las:</strong></td>
            <td style="width: 7.5%; text-align: center;"><span class="label">SI</span> <span class="checkbox"></span></td>
            <td style="width: 7.5%; text-align: center;"><span class="label">NO</span> <span class="checkbox checked"></span></td>
        </tr>
        <tr>
            <td style="height: 18px; text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
        </tr>
    </table>
    
    <!-- III. DE LAS PRÁCTICAS PROFESIONALES -->
    <div class="section-title">III.- DE LAS PRÁCTICAS PROFESIONALES:</div>
    <table class="main-table">
        <tr>
            <th style="width: 50%;">Razón Social de la Empresa:</th>
            <th style="width: 50%;">Dirección (Calle y No., Col., Localidad y Edo.</th>
        </tr>
        <tr>
            <td style="height: 18px; padding: 2px 3px;">{{ $razonSocial }}</td>
            <td style="padding: 2px 3px;">{{ $direccionEmpresa }}</td>
        </tr>
    </table>
    
    <table class="main-table" style="margin-top: 2px;">
        <tr>
            <th style="width: 50%;">Área o Departamento:</th>
            <th style="width: 50%;">Proyecto que desarrollará y/o puesto que ocupará:</th>
        </tr>
        <tr>
            <td style="height: 18px; padding: 2px 3px;">{{ $areaDepartamento }}</td>
            <td style="padding: 2px 3px;">{{ $nombreProyecto }}</td>
        </tr>
    </table>
    
    <table class="main-table" style="margin-top: 2px;">
        <tr>
            <th style="width: 35%;">Nombre del Asesor Externo y/o Jefe<br>Inmediato:</th>
            <th style="width: 22%;">Cargo:</th>
            <th style="width: 18%;">Teléfono:</th>
            <th style="width: 25%;">e-mail:</th>
        </tr>
        <tr>
            <td style="height: 16px; padding: 2px 3px;">{{ $nombreAsesor }}</td>
            <td style="padding: 2px 3px;">{{ $cargoAsesor }}</td>
            <td style="padding: 2px 3px;">{{ $telefonoAsesor }}</td>
            <td class="small-text" style="padding: 2px 3px; word-break: break-all;">{{ $emailAsesor }}</td>
        </tr>
    </table>
    
    <!-- III. PERÍODO DE PRÁCTICAS -->
    <div class="section-title">III.- PERÍODO DE PRÁCTICAS PARA LA ACREDITACIÓN DEL ESPACIO DE FORMACIÓN DE PRÁCTICAS PROFESIONALES I (1906):</div>
    <table class="main-table">
        <tr>
            <th style="width: 14%;">Fecha inicial<br><span class="smaller-text">(dd/mm/aa):</span></th>
            <th style="width: 14%;">Fecha Final<br><span class="smaller-text">(dd/mm/aaaa):</span></th>
            <th style="width: 12%;"># de Meses:</th>
            <th style="width: 16%;">Días de la<br>Semana:</th>
            <th style="width: 14%;">De las:</th>
            <th style="width: 14%;">A las:</th>
            <th style="width: 16%;">Total de<br>horas:</th>
        </tr>
        <tr>
            <td style="height: 16px; text-align: center; padding: 2px;">{{ $fechaInicial }}</td>
            <td style="text-align: center; padding: 2px;">{{ $fechaFinal }}</td>
            <td style="text-align: center; padding: 2px;">&nbsp;</td>
            <td style="text-align: center; padding: 2px;">&nbsp;</td>
            <td style="text-align: center; padding: 2px;">{{ $horarioEntrada }}</td>
            <td style="text-align: center; padding: 2px;">{{ $horarioSalida }}</td>
            <td style="text-align: center; padding: 2px;">&nbsp;</td>
        </tr>
    </table>
    
    <!-- IV. AUTORIZACIÓN -->
    <div class="section-title">IV.- AUTORIZACIÓN:</div>
    <table class="main-table">
        <tr>
            <th colspan="2" style="width: 20%;">Autorización (X):</th>
            <th style="width: 20%;">Fecha<br><span class="smaller-text">(dd/mm/aaaa):</span></th>
            <th style="width: 35%;">Nombre y firma del profesor:</th>
            <th style="width: 25%;">Firma del<br>alumno:</th>
        </tr>
        <tr>
            <td style="width: 10%; text-align: center; height: 35px;">
                <span class="label">SI</span> <span class="checkbox"></span>
            </td>
            <td style="width: 10%; text-align: center;">
                <span class="label">NO</span> <span class="checkbox"></span>
            </td>
            <td style="text-align: center;">&nbsp;</td>
            <td>
                <div class="signature-line"></div>
            </td>
            <td>
                <div class="signature-line"></div>
            </td>
        </tr>
    </table>
    
</body>
</html>
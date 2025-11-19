<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EstadoProceso;
use App\Models\SolicitudFPP01;
use App\Models\SolicitudFPP02;
use App\Models\Expediente;
use App\Models\Area;
use App\Models\AsesorExterno; // Asegurado de que esté presente para el FPP02
use App\Models\Alumno;
use Carbon\Carbon; // Asegurado de que esté presente para el FPP02

class PdfController extends Controller
{
    // -------------------------------------------------------
    // GENERAR CARTA PRESENTACIÓN (Genera la Carta de Presentación)
    // -------------------------------------------------------
    public function generarCartaPresentacion($claveAlumno, $expediente)
    {
        Carbon::setLocale('es');
        $fechaHoy = Carbon::now()->translatedFormat('j \d\e F \d\e Y');
        $anio = Carbon::now()->format('Y');
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', '1')
                                    ->first();
        $alumno = Alumno::where('Clave_Alumno', $claveAlumno)
                                    ->first();
        $area = Area::where('Id_Area', $alumno['Clave_Area'])
                                    ->first();
        $asesor = AsesorExterno::where('Id_Asesor_Externo', $solicitud['Datos_Asesor_Externo'])
                                    ->first();
        $fechaInicio = Carbon::createFromFormat('Y-m-d', $solicitud['Fecha_Inicio'])->translatedFormat('j \d\e F \d\e Y');
        $fechaTermino = Carbon::createFromFormat('Y-m-d', $solicitud['Fecha_Termino'])->translatedFormat('j \d\e F \d\e Y');

        $pdf = Pdf::loadView('pdf.cartaPresentacion', compact('solicitud', 'alumno', 'area', 'asesor', 'fechaHoy', 'anio', 'fechaInicio', 'fechaTermino'))
            ->setPaper('letter');
        $tipoDoc = 'Carta_Presentacion';
        $nombreArchivo = $claveAlumno . '_carta_presentacion_' . time() . '.pdf';
        $rutaCarpeta = 'expedientes/' . $tipoDoc;
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }
        $pdf->save(storage_path('app/public/' . $rutaCarpeta . '/' . $nombreArchivo));

        $expediente -> update([
            'Carta_Presentacion' => $nombreArchivo,
        ]);
    }



    // -------------------------------------------------------
    // MOSTRAR DOCUMENTO (Visualiza PDFs del expediente)
    // -------------------------------------------------------
    public function mostrarDocumento($claveAlumno, $tipo)
    {
        $pdfPath = null;

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if ($expediente && isset($expediente[$tipo])) {
            $rutaRelativa = "expedientes/{$tipo}/" . $expediente[$tipo];

            if (Storage::disk('public')->exists($rutaRelativa)) {
                $pdfPath = asset('storage/' . $rutaRelativa);
            }
        }

        // Siempre retorna la vista con pdfPath (puede ser null)
        if ($tipo === 'Carta_Presentacion') {
            $pdfPathFirmada = null;
            $tipoFirmada = $tipo . '_Firmada';
            if ($expediente && isset($expediente[$tipoFirmada])) {
                $rutaRelativa = "expedientes/{$tipoFirmada}/" . $expediente[$tipoFirmada];

                if (Storage::disk('public')->exists($rutaRelativa)) {
                    $pdfPathFirmada = asset('storage/' . $rutaRelativa);
                }
            }
            return view('alumno.expediente.cartaPresentacion', compact('pdfPath', 'pdfPathFirmada'));
            
        }
        if ($tipo === 'Carta_Aceptacion') {
            return view('alumno.expediente.cartaAceptacion', compact('pdfPath'));
        }
        if ($tipo === 'Carta_Desglose_Percepciones') {
            return view('alumno.expediente.desglosePercepciones', compact('pdfPath'));
        }
        if ($tipo === 'Solicitud_FPP02_Firmada') {
            return $this->confirmaFPP02($pdfPath);
        }

        return abort(404, 'Tipo de documento no válido');
    }

    // -------------------------------------------------------
    // MOSTRAR DOCUMENTO (Visualiza PDFs a empleados)
    // -------------------------------------------------------
    public function mostrarDocumentoEmpleados($claveAlumno, $tipo, $documento)
    {
        $pdfPath = null;

        if (isset($documento)) {
            $rutaRelativa = "expedientes/{$tipo}/" . $documento;

            if (Storage::disk('public')->exists($rutaRelativa)) {
                $pdfPath = asset('storage/' . $rutaRelativa);
            }
        }

        // Siempre retorna la vista con pdfPath (puede ser null)
        if ($tipo === 'Solicitud_FPP02_Firmada') {
            return view('encargado.revisar_registro', compact('pdfPath', 'claveAlumno'));
        }

        return abort(404, 'Tipo de documento no válido');
    }

    // -------------------------------------------------------
    // ELIMINAR DOCUMENTO (Elimina un PDF específico del expediente)
    // -------------------------------------------------------
    public function eliminarDocumento(Request $request, $claveAlumno, $tipo)
    {
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        if (! $solicitud) {
            return abort(404, 'Solicitud no autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (! $expediente || ! isset($expediente[$tipo]) || $expediente[$tipo] === null) {
            return redirect()->back()->with('error', 'No se encontró el documento a eliminar.');
        }

        $rutaRelativa = "expedientes/{$tipo}/" . $expediente[$tipo];

        // Eliminar del storage si existe
        if (Storage::disk('public')->exists($rutaRelativa)) {
            Storage::disk('public')->delete($rutaRelativa);
        }

        // Quitar el nombre en la base de datos
        $expediente->$tipo = null;
        $expediente->save();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }

    // -------------------------------------------------------
    // SUBIR CARTA PRESENTACIÓN
    // -------------------------------------------------------
    public function subirCartaPresentacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        $file = $request->file('archivo');
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';
        $tipoDoc = 'Carta_Presentacion_Firmada';
        $nombreArchivo = $claveAlumno . '_carta_presentacion_firmada_' . time() . '.' . $file->getClientOriginalExtension();
        $rutaCarpeta = 'expedientes/' . $tipoDoc;
        $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        $saved = Storage::disk('public')->putFileAs($rutaCarpeta, $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar archivo (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $rutaCompleta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $rutaCompleta),
        ]);

        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            
            if (!$solicitud) {
                return back()->withErrors(['No se encontró la solicitud autorizada para este alumno.']);
            }
            
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
                                                    ->first();
            
            if (!$expediente) {
                 // Debería existir si la solicitud fue autorizada, pero por seguridad
                $expediente = Expediente::create(['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01]);
            }
            
            $expediente->update([
                $tipoDoc => $nombreArchivo,
            ]);

            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }

    // -------------------------------------------------------
    // SUBIR CARTA ACEPTACION
    // -------------------------------------------------------
    public function subirCartaAceptacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        $file = $request->file('archivo');
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';
        $tipoDoc = 'Carta_Aceptacion';
        $nombreArchivo = $claveAlumno . '_carta_aceptacion_' . time() . '.' . $file->getClientOriginalExtension();
        $rutaCarpeta = 'expedientes/' . $tipoDoc;
        $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        $saved = Storage::disk('public')->putFileAs($rutaCarpeta, $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar archivo (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $rutaCompleta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $rutaCompleta),
        ]);

        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            
            if (!$solicitud) {
                return back()->withErrors(['No se encontró la solicitud autorizada para este alumno.']);
            }
            
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
                                                    ->first();
            
            if (!$expediente) {
                 // Debería existir si la solicitud fue autorizada, pero por seguridad
                $expediente = Expediente::create(['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01]);
            }
            
            $expediente->update([
                $tipoDoc => $nombreArchivo,
            ]);

            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }


    // -------------------------------------------------------
    // SUBIR DESGLOSE PERCEPCIONES
    // -------------------------------------------------------
    public function subirDesglosePercepciones(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        $file = $request->file('archivo');
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';
        $tipoDoc = 'Carta_Desglose_Percepciones';
        $nombreArchivo = $claveAlumno . '_desglose_percepciones_' . time() . '.' . $file->getClientOriginalExtension();
        $rutaCarpeta = 'expedientes/' . $tipoDoc;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        $saved = Storage::disk('public')->putFileAs($rutaCarpeta, $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar desglose percepciones (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'saved' => $saved,
        ]);

        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            
            if (!$solicitud) {
                 return back()->withErrors(['No se encontró la solicitud autorizada para este alumno.']);
            }
            
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
                                                    ->first();

            if (!$expediente) {
                $expediente = Expediente::create(['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01]);
            }
                                                    
            $expediente->update([
                $tipoDoc => $nombreArchivo,
            ]);

            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }

    // -------------------------------------------------------
    // PDF FPP02 - ACTUALIZADO CON FORMATO OFICIAL
    // -------------------------------------------------------
    public function generarFpp02(Request $request)
    {
        $validated = $request->validate([
            'ss' => 'required|in:si,no',
            'num_meses' => 'required|numeric|min:1',
            'total_horas' => 'required|numeric|min:1',
        ]);

        $alumnoSesion = session('alumno');
        $claveAlumno = $alumnoSesion['cve_uaslp'] ?? null;

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                    ->where('Autorizacion', 1)
                                    ->first();

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        $idRegistro = $expediente->Id_Solicitud_FPP02;

        $registro = SolicitudFPP02::where('Id_Solicitud_FPP02', $idRegistro)->first();

        $ss = $request->ss === 'si' ? 1 : 0;
        $registro->update([
            'Servicio_Social' => $ss,
            'Num_Meses' => $request->num_meses,
            'Total_Horas' => $request->total_horas,
        ]);

        if (!$claveAlumno) {
            return back()->withErrors(['No se encontró la sesión del alumno.']);
        }

        // Obtener solicitud con todas las relaciones
        $solicitud = SolicitudFPP01::with([
            'alumno',
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp'
        ])
        ->where('Clave_Alumno', $claveAlumno)
        ->latest('Id_Solicitud_FPP01')
        ->first();

        if (!$solicitud) {
            return back()->withErrors(['No se encontró la información de la solicitud.']);
        }

        // Obtener datos de dependencia y sector
        $dependencia = $solicitud->dependenciaMercadoSolicitud;
        $empresa = optional($dependencia)->dependenciaEmpresa;
        $sector = $dependencia?->Id_Privado
            ? $dependencia->sectorPrivado
            : ($dependencia?->Id_Publico
                ? $dependencia->sectorPublico
                : $dependencia?->sectorUaslp);

        // ⭐ OBTENER ASESOR EXTERNO SI EXISTE
        $asesorExterno = null;
        if ($solicitud->Clave_Asesor_Externo) {
            $asesorExterno = AsesorExterno::find($solicitud->Clave_Asesor_Externo);
        }

        // Preparar datos para el PDF con formato oficial
        $alumnoData = $solicitud->alumno;

        $data = [
            // Datos del alumno
            'nombreCompleto' => trim(
                ($alumnoData->Nombre ?? '') . ' ' .
                ($alumnoData->ApellidoP_Alumno ?? '') . ' ' .
                ($alumnoData->ApellidoM_Alumno ?? '')
            ),
            'claveUnica' => $alumnoData->Clave_Alumno ?? '',
            'carrera' => $alumnoData->Carrera ?? '',

            // Datos de la empresa
            'razonSocial' => $empresa->Nombre_Depn_Emp ?? ($sector->Nombre_Depn_Emp ?? ''),
            'direccionEmpresa' => $this->formatearDireccion($empresa, $sector),
            'rfc' => $empresa->RFC_Empresa ?? '',
            'telefono' => $empresa->Telefono ?? ($sector->Telefono ?? ''),

            // Datos del proyecto
            'areaDepartamento' => $sector->Area_Depto ?? ($empresa->Area_Depto ?? ''),
            'nombreProyecto' => $solicitud->Nombre_Proyecto ?? '',

            // ⭐ DATOS DEL ASESOR EXTERNO (desde la tabla asesor_externo)
            'nombreAsesor' => $asesorExterno
                ? trim(
                    ($asesorExterno->Nombre ?? '') . ' ' .
                    ($asesorExterno->Apellido_Paterno ?? '') . ' ' .
                    ($asesorExterno->Apellido_Materno ?? '')
                )
                : '',
            'cargoAsesor' => $asesorExterno->Puesto ?? '',
            'telefonoAsesor' => $asesorExterno->Telefono ?? '',
            'emailAsesor' => $asesorExterno->Correo ?? '',

            // Fechas y horarios
            'fechaInicial' => $solicitud->Fecha_Inicio
                ? Carbon::parse($solicitud->Fecha_Inicio)->format('d/m/Y')
                : '',
            'fechaFinal' => $solicitud->Fecha_Termino
                ? Carbon::parse($solicitud->Fecha_Termino)->format('d/m/Y')
                : '',
            'horarioEntrada' => $solicitud->Horario_Entrada ?? '',
            'horarioSalida' => $solicitud->Horario_Salida ?? '',

            // Fecha de generación
            'fechaGeneracion' => now()->format('d/m/Y'),

            // Pasar objetos completos para uso en la vista (por si acaso)
            'alumno' => $alumnoData,
            'solicitud' => $solicitud,
            'empresa' => $empresa,
            'sector' => $sector,
        ];

        // Generar PDF con el formato oficial FPP02
        $pdf = Pdf::loadView('pdf.fpp02', $data)
            ->setPaper('letter', 'portrait');

        $nombreArchivo = 'FPP02_' . $claveAlumno . '_' . time() . '.pdf';
        $rutaCarpeta = 'expedientes/fpp02';
        $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

        // Crear carpeta si no existe
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        // Guardar PDF en storage
        Storage::disk('public')->put($rutaCompleta, $pdf->output());

        // ACTUALIZAR SEMÁFORO (Usando la función actualizarSemaforo, no marcarEtapa)
        $this->actualizarSemaforo($claveAlumno, 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES');

        // Guardar sesión
        session(['fpp02_impreso_' . $claveAlumno => true]);

        // Descargar PDF
        return $pdf->download('Formato_FPP02.pdf');
    }

    // -------------------------------------------------------
    // SUBIR FPP02 FIRMADO
    // -------------------------------------------------------
    public function subirFpp02Firmado(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480',
        ]);

        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? 'sinclave';

        $file = $request->file('archivo');
        $tipoDoc = 'Solicitud_FPP02_Firmada';
        $nombreArchivo = $claveAlumno . '_FPP02_firmado_' . time() . '.pdf';
        $rutaCarpeta = 'expedientes/' . $tipoDoc;
        $rutaCompleta = $rutaCarpeta . '/' . $nombreArchivo;

        // Asegurarse de que el directorio existe en el disco public
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        $saved = Storage::disk('public')->putFileAs($rutaCarpeta, $file, $nombreArchivo);

        // Log para depuración
        Log::info('Intento de guardar archivo (public disk)', [
            'nombreArchivo' => $nombreArchivo,
            'ruta' => $rutaCompleta,
            'saved' => $saved,
            'full_path' => storage_path('app/public/' . $rutaCompleta),
        ]);

        // Lógica del semáforo:
        // ETAPA 4 → realizado (El alumno subió el documento)
        $this->marcarEtapa(
            $claveAlumno, 
            'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES', 
            'realizado'
        );

        // ETAPA 5 → proceso (Notifica al encargado que debe revisar)
        $this->marcarEtapa(
            $claveAlumno, 
            'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)', 
            'proceso'
        );
        
        if ($saved) {

            $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
                                                    ->where('Autorizacion', 1)
                                                    ->first();
            
            if (!$solicitud) {
                return back()->withErrors(['No se encontró la solicitud autorizada para este alumno.']);
            }
            
            $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)
                                                    ->first();

            if (!$expediente) {
                $expediente = Expediente::create(['Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01]);
            }
                                                    
            $expediente->update([
                $tipoDoc => $nombreArchivo,
            ]);

            return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
        } else {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }
    }


    // -------------------------------------------------------
    // FUNCIONES AUXILIARES
    // -------------------------------------------------------
    
    // Función auxiliar para formatear dirección
    private function formatearDireccion($empresa, $sector)
    {
        $entidad = $empresa ?? $sector;
        
        if (!$entidad) {
            return '';
        }
        
        $direccion = trim(
            ($entidad->Calle ?? '') . ' ' . 
            ($entidad->Numero ? '#' . $entidad->Numero : '') . ', ' .
            ($entidad->Colonia ?? '') . ', ' .
            ($entidad->Municipio ?? '') . ', ' .
            ($entidad->Estado ?? '') . ', ' .
            ($entidad->Cp ? 'CP ' . $entidad->Cp : '')
        );
        
        // Limpiar comas y espacios extras
        $direccion = preg_replace('/,\s*,/', ',', $direccion);
        $direccion = preg_replace('/,\s*$/', '', $direccion);
        
        return $direccion;
    }

    // Función auxiliar para marcar una etapa específica
    private function marcarEtapa($claveAlumno, $etapa, $estado)
    {
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => $etapa,
            ],
            [
                'estado' => $estado,
            ]
        );
    }

    public function confirmaFPP02($pdfPath)
    {
        $alumno = session('alumno');
        $clave = $alumno['cve_uaslp'] ?? null;

        if (!$clave) {
            return redirect()->route('alumno.inicio')
                ->with('error', 'No se encontró la clave del alumno en la sesión.');
        }

        $solicitud = SolicitudFPP01::with([
            'alumno',
            'dependenciaMercadoSolicitud.dependenciaEmpresa',
            'dependenciaMercadoSolicitud.sectorPrivado',
            'dependenciaMercadoSolicitud.sectorPublico',
            'dependenciaMercadoSolicitud.sectorUaslp',
            'autorizaciones'
        ])
        ->where('Clave_Alumno', $clave)
        ->latest('Id_Solicitud_FPP01')
        ->first();

        if (!$solicitud) {
            return redirect()->route('alumno.estado')
                ->with('error', 'No se encontró ninguna solicitud previa.');
        }

        $alumno = Alumno::where('Clave_Alumno', $clave)->first();

        $dependencia = $solicitud->dependenciaMercadoSolicitud;
        $empresa = optional($dependencia)->dependenciaEmpresa;

        $sector = null;
        $tipoSector = null;
        if ($dependencia?->Id_Privado) {
            $sector = $dependencia->sectorPrivado;
            $tipoSector = 'privado';
        } elseif ($dependencia?->Id_Publico) {
            $sector = $dependencia->sectorPublico;
            $tipoSector = 'publico';
        } elseif ($dependencia?->Id_UASLP) {
            $sector = $dependencia->sectorUaslp;
            $tipoSector = 'uaslp';
        }

        // Determinar si debe mostrar upload
        $registroFpp02 = EstadoProceso::where('clave_alumno', $clave)
            ->where('etapa', 'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES')
            ->first();

        $mostrarUpload =
            session('fpp02_impreso_' . $clave, false) ||
            ($registroFpp02 && $registroFpp02->estado === 'realizado');

        return view('alumno.registro', compact('alumno', 'solicitud', 'empresa', 'sector', 'tipoSector', 'mostrarUpload', 'pdfPath'));
    }
    
    // Función auxiliar para actualizar el semáforo (marca la actual como realizado y la siguiente como pendiente)
    private function actualizarSemaforo($claveAlumno, $etapaActual)
    {
        try {
            // Actualizar la etapa actual
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => $etapaActual,
                ],
                [
                    'estado' => 'realizado',
                ]
            );

            // Lista completa de etapas en orden
            $etapas = [
                'REGISTRO DE SOLICITUD DE PRÁCTICAS PROFESIONALES',
                'AUTORIZACIÓN DEL DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES (FPP01)',
                'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP01)',
                'REGISTRO DE SOLICITUD DE AUTORIZACIÓN DE PRÁCTICAS PROFESIONALES',
                'AUTORIZACIÓN DEL ENCARGADO DE PRÁCTICAS PROFESIONALES (FPP02)',
                'CARTA DE PRESENTACIÓN (DEPARTAMENTO DE SERVICIO SOCIAL Y PRÁCTICAS PROFESIONALES)',
                'CARTA DE PRESENTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                'CARTA DE PRESENTACIÓN (ALUMNO)',
                'CARTA DE ACEPTACIÓN (ALUMNO)',
                'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)',
                'CARTA DE DESGLOSE DE PERCEPCIONES',
                'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA',
                'RECIBO DE PAGO',
                'REPORTE PARCIAL NO. X',
                'REVISIÓN REPORTE PARCIAL NO. X',
                'CORRECCIÓN REPORTE PARCIAL NO. X',
                'REPORTE FINAL',
                'REVISIÓN REPORTE FINAL',
                'CORRECCIÓN REPORTE FINAL',
                'CALIFICACIÓN REPORTE FINAL',
                'CARTA DE TÉRMINO',
                'EVALUACIÓN DE LA EMPRESA',
                'CALIFICACIÓN FINAL',
                'EVALUACIÓN DEL ALUMNO',
                'LIBERACIÓN DEL ALUMNO',
                'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES',
            ];

            // Buscar la siguiente etapa
            $indiceActual = array_search($etapaActual, $etapas);
            if ($indiceActual !== false && isset($etapas[$indiceActual + 1])) {
                $siguienteEtapa = $etapas[$indiceActual + 1];

                EstadoProceso::updateOrCreate(
                    [
                        'clave_alumno' => $claveAlumno,
                        'etapa' => $siguienteEtapa,
                    ],
                    [
                        'estado' => 'pendiente',
                    ]
                );

                Log::info("Siguiente etapa marcada como pendiente", [
                    'clave_alumno' => $claveAlumno,
                    'siguiente' => $siguienteEtapa
                ]);
            }

            Log::info("Etapa actual marcada como realizada", [
                'clave_alumno' => $claveAlumno,
                'actual' => $etapaActual
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar semáforo: " . $e->getMessage());
        }
    }
}
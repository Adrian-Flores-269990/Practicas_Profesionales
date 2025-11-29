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
    // public function mostrarDocumento($claveAlumno, $tipo)
    // {
    //     Log::info("MOSTRAR DOCUMENTO", [
    //         'claveAlumno' => $claveAlumno,
    //         'tipoSolicitado' => $tipo
    //     ]);
    //     $pdfPath = null;
    //     $pdfPathFirmada = null;

    //     $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
    //         ->where('Autorizacion', 1)
    //         ->first();

    //     if (! $solicitud) {
    //         return abort(404, 'Solicitud no autorizada');
    //     }

    //     $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

    //     // ----------------------------------------------------
    //     // CARTA DE PRESENTACIÓN (GENERADA)
    //     // ----------------------------------------------------
    //     if ($expediente && $expediente->Carta_Presentacion) {

    //         $rutaGenerada = "expedientes/Carta_Presentacion/" . $expediente->Carta_Presentacion;

    //         if (Storage::disk('public')->exists($rutaGenerada)) {
    //             $pdfPath = asset("storage/" . $rutaGenerada);
    //         }
    //     }

    //     // ----------------------------------------------------
    //     // CARTA DE PRESENTACIÓN FIRMADA (ALUMNO)
    //     // ----------------------------------------------------
    //     if ($expediente && $expediente->Carta_Presentacion_Firmada) {

    //         $rutaFirmada = "expedientes/Carta_Presentacion_Firmada/" . $expediente->Carta_Presentacion_Firmada;

    //         if (Storage::disk('public')->exists($rutaFirmada)) {
    //             $pdfPathFirmada = asset("storage/" . $rutaFirmada);
    //         }
    //     }

    //     return view('alumno.expediente.cartaPresentacion', compact('pdfPath','pdfPathFirmada','claveAlumno'));
    // }

    public function mostrarDocumento($claveAlumno, $tipo)
    {
        Log::info(" mostrarDocumento llamado", [
            'claveAlumno' => $claveAlumno,
            'tipo' => $tipo
        ]);

        $pdfPath = null;
        $pdfPathFirmada = null;

        // Buscar solicitud autorizada
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->where('Autorizacion', 1)
            ->first();

        if (! $solicitud) {
            Log::warning(" No existe solicitud autorizada");
            return abort(404, 'Solicitud no autorizada');
        }

        // Buscar expediente
        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (! $expediente) {
            Log::warning(" No existe expediente");
            return abort(404, 'No se encontró expediente');
        }

        // ===============================
        // ARCHIVO PRINCIPAL
        // ===============================

        if (isset($expediente[$tipo]) && $expediente[$tipo] !== null) {

            $ruta = "expedientes/{$tipo}/" . $expediente[$tipo];

            Log::info("➡ Revisando archivo principal", ['ruta' => $ruta]);

            if (Storage::disk('public')->exists($ruta)) {
                $pdfPath = asset("storage/" . $ruta);
                Log::info("✔ Archivo principal encontrado", ['url' => $pdfPath]);
            } else {
                Log::warning("⚠ Archivo principal no existe en storage");
            }
        }

        // ===============================
        // CARTA PRESENTACIÓN (FIRMADA)
        // ===============================
        if ($tipo === 'Carta_Presentacion') {

            $firmadaCampo = 'Carta_Presentacion_Firmada';

            if ($expediente->$firmadaCampo) {
                $rutaFirmada = "expedientes/{$firmadaCampo}/" . $expediente->$firmadaCampo;

                Log::info("➡ Revisando archivo firmado", ['rutaFirmada' => $rutaFirmada]);

                if (Storage::disk('public')->exists($rutaFirmada)) {
                    $pdfPathFirmada = asset("storage/" . $rutaFirmada);
                    Log::info("✔ Archivo firmado encontrado", ['url' => $pdfPathFirmada]);
                }
            }

            return view('alumno.expediente.cartaPresentacion', [
                'pdfPath' => $pdfPath,
                'pdfPathFirmada' => $pdfPathFirmada,
                'claveAlumno' => $claveAlumno
            ]);
        }

        // ===============================
        // CARTA ACEPTACIÓN
        // ===============================
        if ($tipo === 'Carta_Aceptacion') {
            return view('alumno.expediente.cartaAceptacion', compact('pdfPath', 'claveAlumno'));
        }

        // ===============================
        // DESGLOSE DE PERCEPCIONES
        // ===============================
        if ($tipo === 'Carta_Desglose_Percepciones') {
            return view('alumno.expediente.desglosePercepciones', compact('pdfPath', 'claveAlumno'));
        }

        // ===============================
        // FPP02 FIRMADO
        // ===============================
        if ($tipo === 'Solicitud_FPP02_Firmada') {
            Log::info(" Mostrando FPP02 Firmado");
            return $this->confirmaFPP02($pdfPath);
        }

        Log::error(" Tipo de documento no válido", ['tipo' => $tipo]);
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
        if ($tipo === 'Carta_Presentacion_Firmada') {
            return view('encargado.revisar_presentacion', compact('pdfPath', 'claveAlumno'));
        }
        if ($tipo === 'Carta_Aceptacion') {
            return view('encargado.revisar_aceptacion', compact('pdfPath', 'claveAlumno'));
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

        if (! $solicitud) return back()->with('error','Solicitud no autorizada');

        $exp = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (! isset($exp[$tipo]) || $exp[$tipo] == null)
            return back()->with('error','El documento no existe.');

        $ruta = "expedientes/$tipo/" . $exp[$tipo];

        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }

        $exp->$tipo = null;
        $exp->save();

        // REVERTIR ESTADO
        if ($tipo === 'Carta_Presentacion_Firmada') {

            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => 'CARTA DE PRESENTACIÓN (ALUMNO)'
                ],
                [
                    'estado' => 'proceso'
                ]
            );
            EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
            ],
            ['estado' => 'pendiente']
        );
        }

        // ----------------------------------------------
        // REVERTIR ESTADOS SI SE ELIMINA CARTA ACEPTACIÓN
        // ----------------------------------------------
        if ($tipo === 'Carta_Aceptacion') {

            // 1️⃣ Regresar CARTA DE ACEPTACIÓN (ALUMNO) a PROCESO (amarillo)
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
                ],
                [
                    'estado' => 'proceso'
                ]
            );

            // 2️⃣ Regresar CARTA DE ACEPTACIÓN (ENCARGADO...) a PENDIENTE (rojo)
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => 'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
                ],
                [
                    'estado' => 'pendiente'
                ]
            );
        }

        return back()->with('success','Documento eliminado correctamente.');
    }

    // -------------------------------------------------------
    // SUBIR CARTA PRESENTACIÓN
    // -------------------------------------------------------
    public function subirCartaPresentacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:20480',
            'claveAlumno' => 'required'
        ]);

        $clave = $request->claveAlumno;

        $solicitud = SolicitudFPP01::where('Clave_Alumno', $clave)
            ->where('Autorizacion', 1)
            ->first();

        if (! $solicitud) {
            return back()->with('error','No se encontró solicitud autorizada');
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        // Guardar archivo
        $file = $request->file('archivo');
        $nombreArchivo = $clave . "_carta_presentacion_firmada_" . time() . ".pdf";
        $ruta = "expedientes/Carta_Presentacion_Firmada";

        Storage::disk('public')->makeDirectory($ruta);
        Storage::disk('public')->putFileAs($ruta, $file, $nombreArchivo);

        // Actualizar expediente
        $expediente->Carta_Presentacion_Firmada = $nombreArchivo;
        $expediente->save();

        // Cambiar estado a REALIZADO
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $clave,
                'etapa' => 'CARTA DE PRESENTACIÓN (ALUMNO)'
            ],
            [
                'estado' => 'realizado'
            ]
        );

        // Siguiente etapa → pendiente
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $clave,
                'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
            ],
            [
                'estado' => 'proceso'
            ]
        );

        return back()->with('success','Carta firmada subida correctamente.');
    }

    // -------------------------------------------------------
    // SUBIR CARTA ACEPTACION
    // -------------------------------------------------------
    public function subirCartaAceptacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('archivo');
        $alumno = session('alumno');
        $claveAlumno = $alumno['cve_uaslp'] ?? null;

        if (!$claveAlumno) {
            return back()->withErrors(['No se encontró la sesión del alumno.']);
        }

        // --------------------------
        //   DATOS DEL ARCHIVO
        // --------------------------
        $tipoDoc = 'Carta_Aceptacion';
        $nombreArchivo = $claveAlumno . '_carta_aceptacion_' . time() . '.' . $file->getClientOriginalExtension();
        $rutaCarpeta = "expedientes/$tipoDoc";

        // Crear carpeta si no existe
        if (!Storage::disk('public')->exists($rutaCarpeta)) {
            Storage::disk('public')->makeDirectory($rutaCarpeta);
        }

        // Guardar archivo
        $saved = Storage::disk('public')->putFileAs($rutaCarpeta, $file, $nombreArchivo);

        if (!$saved) {
            return back()->withErrors(['No se pudo guardar el archivo.']);
        }

        // --------------------------
        //   GUARDAR EN EXPEDIENTE
        // --------------------------
        $solicitud = SolicitudFPP01::where('Clave_Alumno', $claveAlumno)
            ->where('Autorizacion', 1)
            ->first();

        if (!$solicitud) {
            return back()->withErrors(['No se encontró la solicitud autorizada.']);
        }

        $expediente = Expediente::where('Id_Solicitud_FPP01', $solicitud->Id_Solicitud_FPP01)->first();

        if (!$expediente) {
            $expediente = Expediente::create([
                'Id_Solicitud_FPP01' => $solicitud->Id_Solicitud_FPP01
            ]);
        }

        // Se guarda correctamente en la BD
        $expediente->update([
            'Carta_Aceptacion' => $nombreArchivo
        ]);

        // --------------------------
        //   ACTUALIZAR SEMÁFORO
        // --------------------------

        // 1️⃣ CARTA DE ACEPTACIÓN (ALUMNO) → REALIZADO (verde)
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'CARTA DE ACEPTACIÓN (ALUMNO)'
            ],
            [
                'estado' => 'realizado'
            ]
        );

        // 2️⃣ Siguiente etapa → CARTA DE ACEPTACIÓN (ENCARGADO...) → PROCESO (amarillo)
        EstadoProceso::updateOrCreate(
            [
                'clave_alumno' => $claveAlumno,
                'etapa' => 'CARTA DE ACEPTACIÓN (ENCARGADO DE PRÁCTICAS PROFESIONALES)'
            ],
            [
                'estado' => 'proceso'
            ]
        );

        return back()->with('success', 'Archivo subido correctamente: ' . $nombreArchivo);
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

            $alumno = session('alumno');
            $claveAlumno = $alumno['cve_uaslp'];
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => 'CARTA DE DESGLOSE DE PERCEPCIONES'
                ],
                ['estado' => 'realizado']
            );
            EstadoProceso::updateOrCreate(
                [
                    'clave_alumno' => $claveAlumno,
                    'etapa' => 'SOLICITUD DE RECIBO PARA AYUDA ECONÓMICA'
                ],
                ['estado' => 'proceso']
            );

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
                'REPORTE PARCIAL',
                'REVISIÓN REPORTE PARCIAL',
                'CORRECCIÓN REPORTE PARCIAL',
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

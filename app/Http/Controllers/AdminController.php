<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\Alumno;
use App\Models\CarreraIngenieria;
use Illuminate\Support\Facades\DB;
use App\Models\EstadoProceso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\SolicitudFPP01;
use App\Models\SolicitudFPP02;
use App\Models\Reporte;
use App\Models\Expediente;

class AdminController extends Controller
{
    /**
     *  Mostrar lista de empleados con su rol
     */
    public function index()
    {
        $empleados = Empleado::with('rol')->get();
        $roles = Rol::all();
        $empleadosSinRol = Empleado::whereNull('Id_Rol')
            ->orWhere('Id_Rol', 0)
            ->get();

        return view('administrador.empleados', compact('empleados', 'roles', 'empleadosSinRol'));
    }

    /**
     *  Actualizar el rol de un empleado (desde la tabla o modal de edición)
     */
    public function actualizarRol(Request $request, $id)
    {
        $request->validate([
            'Id_Rol' => 'required|exists:roles,id'
        ]);

        $empleado = Empleado::findOrFail($id);
        $empleado->Id_Rol = $request->Id_Rol;
        $empleado->save();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     *  Mostrar formulario para crear nuevo empleado
     */
    public function create()
    {
        $roles = Rol::all();
        return view('administrador.crear_empleado', compact('roles'));
    }

    /*
     *  Guardar un nuevo empleado en la base de datos
     */
    public function store(Request $request)
    {
         $request->validate([
            'Nombre' => 'required|string|max:255',
            'RPE' => 'required|string|max:100',
            'Correo' => 'nullable|email',
            'Id_Rol' => 'nullable|exists:roles,id',
        ]);

        Empleado::create([
            'RPE' => $request->RPE,
            'Nombre' => $request->Nombre,
            'Clave_Area' => $request->Clave_Area,
            'Area' => $request->Area,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Carrera' => $request->Carrera,
            'Cargo' => $request->Cargo,
            'Correo' => $request->Correo,
            'Telefono' => $request->Telefono,
            'Id_Rol' => $request->Id_Rol,
        ]);

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado agregado correctamente.');
    }

    /**
     *  Mostrar formulario de edición (si usas página aparte)
     */
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        $roles = Rol::all();
        return view('administrador.editar_empleado', compact('empleado', 'roles'));
    }

    /**
     *  Actualizar datos de un empleado (usado también por el modal)
     */
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $empleado->update([
            'RPE' => $request->RPE,
            'Nombre' => $request->Nombre,
            'Clave_Area' => $request->Clave_Area,
            'Area' => $request->Area,
            'Clave_Carrera' => $request->Clave_Carrera,
            'Carrera' => $request->Carrera,
            'Cargo' => $request->Cargo,
            'Correo' => $request->Correo,
            'Telefono' => $request->Telefono,
            'Id_Rol' => $request->Id_Rol,
        ]);

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     *  Eliminar un empleado
     */
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    /**
     *  Nuevo método: Asignar rol a un empleado existente sin rol
     */
    public function asignarRol(Request $request)
    {
        $request->validate([
            'Id_Encargado' => 'required|exists:encargado,Id_Encargado',
            'Id_Rol' => 'required|exists:roles,id',
        ]);

        $empleado = Empleado::findOrFail($request->Id_Encargado);
        $empleado->Id_Rol = $request->Id_Rol;
        $empleado->save();

        return redirect()->route('administrador.empleados')
            ->with('success', 'Rol asignado correctamente a ' . $empleado->Nombre . '.');
    }

    public function actualizarImagen(Request $request, $nombre)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagen = $request->file('imagen');
        $rutaDestino = public_path('images/' . $nombre);

        // Reemplazar imagen existente
        $imagen->move(public_path('images'), $nombre);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    /**
     * Consultar alumno por clave o apellidos
     */
    public function consultarAlumno(Request $request)
    {
        $alumnos = [];
        $documentos = [];

        if ($request->filled('busqueda')) {
            $busqueda = trim($request->busqueda);

            // Variante para buscar FPP01 por clave sin el PRIMER 0 a la izquierda (si lo tiene)
            $busquedaClaveSinPrimerCero = $busqueda;
            if (preg_match('/^0\d+$/', $busqueda)) {
                // quitar solo el primer 0
                $busquedaClaveSinPrimerCero = substr($busqueda, 1);
            }

            // Buscar alumnos con solicitudes que coincidan por clave o apellidos
            $alumnosQuery = Alumno::whereHas('solicitudes')
                ->where(function($query) use ($busqueda, $busquedaClaveSinPrimerCero) {
                    // Clave exacta ingresada o sin el primer 0 a la izquierda
                    $query->where(function($q) use ($busqueda, $busquedaClaveSinPrimerCero) {
                        $q->where('Clave_Alumno', 'LIKE', "%{$busqueda}%");
                        if ($busquedaClaveSinPrimerCero !== $busqueda) {
                            $q->orWhere('Clave_Alumno', 'LIKE', "%{$busquedaClaveSinPrimerCero}%");
                        }
                    })
                    // Búsqueda por apellidos/nombre con el texto tal cual se ingresó
                    ->orWhere('ApellidoP_Alumno', 'LIKE', "%{$busqueda}%")
                    ->orWhere('ApellidoM_Alumno', 'LIKE', "%{$busqueda}%")
                    ->orWhere(DB::raw("CONCAT(ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%")
                    ->orWhere(DB::raw("CONCAT(Nombre, ' ', ApellidoP_Alumno, ' ', ApellidoM_Alumno)"), 'LIKE', "%{$busqueda}%");
                })
                ->with('solicitudes')
                ->limit(10)
                ->get();

             // Obtener los estados de proceso (semáforo) para todas las claves encontradas
            $claves = $alumnosQuery->pluck('Clave_Alumno')->toArray();
            $estadosProceso = EstadoProceso::whereIn('clave_alumno', $claves)
                ->orderBy('clave_alumno')
                ->orderBy('id')
                ->get()
                ->groupBy('clave_alumno');

            $alumnos = $alumnosQuery->map(function($alumno) use ($estadosProceso) {
                // Obtener la última (más reciente) solicitud FPP01 del alumno para resumen
                $solicitud = $alumno->solicitudes->load('dependenciaMercadoSolicitud.dependenciaEmpresa')
                    ->sortByDesc(function($s){
                        // Usa fecha solicitud si existe, si no el id como fallback
                        return $s->Fecha_Solicitud ?? $s->Id_Solicitud_FPP01;
                    })->first();

                $resumenSolicitud = null;
                if ($solicitud) {
                    // Obtener nombre de empresa desde la relación
                    $empresaNombre = null;
                    if ($solicitud->dependenciaMercadoSolicitud && $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa) {
                        $empresaNombre = $solicitud->dependenciaMercadoSolicitud->dependenciaEmpresa->Nombre_Depn_Emp;
                    }

                    // Construir horario desde los campos de la base de datos
                    $horario = null;
                    if ($solicitud->Horario_Mat_Ves || $solicitud->Horario_Entrada || $solicitud->Horario_Salida) {
                        $turno = $solicitud->Horario_Mat_Ves === 'M' ? 'Matutino' : ($solicitud->Horario_Mat_Ves === 'V' ? 'Vespertino' : '');
                        $horas = '';
                        if ($solicitud->Horario_Entrada && $solicitud->Horario_Salida) {
                            $horas = " ({$solicitud->Horario_Entrada} - {$solicitud->Horario_Salida})";
                        }
                        $dias = $solicitud->Dias_Semana ? " - {$solicitud->Dias_Semana}" : '';
                        $horario = trim($turno . $horas . $dias);
                    }

                    // Construimos un resumen ligero evitando exponer todo el modelo
                    $resumenSolicitud = [
                        'id' => $solicitud->Id_Solicitud_FPP01 ?? null,
                        'fecha_registro' => $solicitud->Fecha_Solicitud ?? null,
                        'empresa' => $empresaNombre,
                        'proyecto' => $solicitud->Nombre_Proyecto ?? null,
                        'horario' => $horario,
                        'estado_encargado' => $solicitud->Estado_Encargado ?? null,
                        'autorizacion' => $solicitud->Autorizacion ?? null,
                    ];
                }

                // Obtener estados de proceso (semáforo) del alumno
                $semaforo = [];
                if (isset($estadosProceso[$alumno->Clave_Alumno])) {
                    $semaforo = $estadosProceso[$alumno->Clave_Alumno]->map(function($estado) {
                        return [
                            'etapa' => $estado->etapa,
                            'estado' => $estado->estado,
                        ];
                    })->toArray();
                }

                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                    'solicitud_fpp01' => $resumenSolicitud,
                    'semaforo' => $semaforo,
                ];
            })->toArray();

            // Si la búsqueda parece ser una clave exacta y hay al menos un alumno que coincide por clave
            $posibleClave = $busqueda; // Para PDFs se busca TAL CUAL fue ingresada
            $claveDirecta = null;
            if (preg_match('/^\d{4,}$/', $posibleClave)) { // Ej. 194659
                $claveDirecta = $posibleClave;
            }

            if ($claveDirecta) {
                $documentos = $this->buscarDocumentosAlumno($claveDirecta);
            }
        }

        return view('administrador.consultar_alumno', compact('alumnos', 'documentos'));
    }

    /**
     * Busca en storage/public/expedientes/* los PDFs cuyo nombre inicia con la clave del alumno.
     */
    private function buscarDocumentosAlumno(string $claveAlumno): array
    {
        $base = 'expedientes';
        if (!Storage::disk('public')->exists($base)) {
            return [];
        }

        // Buscar recursivamente todos los archivos bajo expedientes
        $files = Storage::disk('public')->allFiles($base);

        $encontrados = [];
        foreach ($files as $file) {
            // Solo PDFs
            if (!Str::endsWith(strtolower($file), '.pdf')) continue;

            $filename = basename($file);

            // Coincidencia: empieza con "<clave>_"
            if (Str::startsWith($filename, $claveAlumno . '_')) {
                $encontrados[] = [
                    'path' => $file,
                    'titulo' => $this->tituloDocumento($filename),
                    'nombre' => $filename,
                    'size_kb' => round(Storage::disk('public')->size($file) / 1024, 1),
                    'url' => asset('storage/' . $file),
                    'modificado' => date('Y-m-d H:i', Storage::disk('public')->lastModified($file)),
                ];
                continue;
            }

            // Soporte adicional: algunos FPP02 se nombran como "FPP02_<clave>_..."
            if (Str::contains($filename, 'FPP02_' . $claveAlumno . '_')) {
                $encontrados[] = [
                    'path' => $file,
                    'titulo' => $this->tituloDocumento($filename),
                    'nombre' => $filename,
                    'size_kb' => round(Storage::disk('public')->size($file) / 1024, 1),
                    'url' => asset('storage/' . $file),
                    'modificado' => date('Y-m-d H:i', Storage::disk('public')->lastModified($file)),
                ];
            }
        }

        return collect($encontrados)->sortByDesc('modificado')->values()->toArray();
    }
    /**
     * Determina título legible según el nombre del archivo.
     */
    private function tituloDocumento(string $filename): string
    {
        $map = [
            '_desglose_percepciones_' => 'Carta de Desglose de Percepciones',
            '_carta_aceptacion_' => 'Carta de Aceptación',
            '_carta_vigencia_derechos_' => 'Constancia Vigencia de Derechos',
            '_estadistica_general_' => 'Estadística General',
            '_carta_pasante_' => 'Carta Pasante',
            '_FPP02_firmado_' => 'Formato FPP02 Firmado',
            'FPP02_' => 'Formato FPP02 Generado',
        ];
        foreach ($map as $needle => $titulo) {
            if (Str::contains($filename, $needle)) return $titulo;
        }
        return 'Documento';
    }

    public function filtrarAlumnosPorEstado(Request $request)
    {
        $tipo = $request->tipo; // 'proceso' o 'terminado'

        // Alumnos que tienen etapas registradas
        $alumnos = Alumno::whereHas('estados') // solo los que tienen etapas
            ->where(function($query) use ($tipo) {
                if ($tipo === 'terminado') {
                    // Deben tener la constancia final NO en pendiente
                    $query->whereHas('estados', function ($q) {
                        $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->where('estado', '!=', 'pendiente');
                    });
                }
                if ($tipo === 'proceso') {
                    // Tienen etapas, pero NO tienen constancia final completada
                    $query->whereDoesntHave('estados', function ($q) {
                        $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
                        ->where('estado', '!=', 'pendiente');
                    });
                }
            })
            ->with('estados')
            ->get()
            ->map(function($alumno) {
                return [
                    'cve_uaslp' => $alumno->Clave_Alumno,
                    'nombres' => $alumno->Nombre,
                    'paterno' => $alumno->ApellidoP_Alumno,
                    'materno' => $alumno->ApellidoM_Alumno,
                    'carrera' => $alumno->Carrera,
                    'semestre' => $alumno->Semestre,
                    'creditos' => $alumno->Creditos,
                    'correo' => $alumno->CorreoElectronico,
                ];
            });

        return view('administrador.consultar_alumno', [
            'alumnos' => $alumnos,
            'filtro' => $tipo
        ]);
    }

    public function indexEncargados()
    {
        $encargados = Empleado::where('Id_Rol', 2)->get();

        foreach ($encargados as $e) {

            $carreras = $e->Carrera ? array_map('trim', explode(',', $e->Carrera)) : [];

            $e->listaCarreras = $carreras;

            // Reportes requeridos por alumno (según cada carrera asignada)
           $reportes = DB::table('requisito_carrera as rc')
                ->join('carrera_ingenieria as ci', 'rc.Id_Carrera_I', '=', 'ci.Id_Carrera_I')
                ->whereIn('ci.Descripcion_Mayúsculas', $carreras)
                ->distinct()
                ->pluck('rc.num_reporte');
            $e->reportes_requeridos = $reportes->isEmpty() ? null : $reportes;

            // Solicitudes asignadas
            $e->solicitudes_asignadas = DB::table('solicitud_fpp01 as s')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->count();

            // Reportes asignados
            $e->reportes_asignados = DB::table('reporte as r')
                ->join('expediente as ex', 'r.Id_Expediente', '=', 'ex.Id_Expediente')
                ->join('solicitud_fpp01 as s', 'ex.Id_Solicitud_FPP01', '=', 's.Id_Solicitud_FPP01')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->count();

            // Evaluaciones asignadas
            $e->evaluaciones_asignadas = DB::table('evaluacion as ev')
                ->join('solicitud_fpp01 as s', 'ev.Id_Solicitud_FPP01', '=', 's.Id_Solicitud_FPP01')
                ->join('alumno as a', 's.Clave_Alumno', '=', 'a.Clave_Alumno')
                ->whereIn('a.Carrera', $carreras)
                ->count();
        }

        $carreras = CarreraIngenieria::all();

        return view('administrador.encargados', compact('encargados', 'carreras'));
    }

    public function updateCarreras(Request $request, $id)
    {
        $request->validate([
            'carreras' => 'required|array',
            'numero_reportes' => 'required|integer|min:1|max:10',
        ]);

        $carreras = $request->carreras;
        $numReportes = $request->numero_reportes;

        $empleado = Empleado::findOrFail($id);
        $empleado->Carrera = implode(', ', $carreras);
        $empleado->save();

        DB::table('requisito_carrera as rc')
            ->join('carrera_ingenieria as ci', 'rc.Id_Carrera_I', '=', 'ci.Id_Carrera_I')
            ->whereIn('ci.Descripcion_Mayúsculas', $carreras)
            ->update([
                'rc.num_reporte' => $numReportes
            ]);

        return back()->with('success', 'Carreras y número de reportes actualizados correctamente.');
    }

    public function indexEmpresas()
    {
        // Cargar todas las relaciones necesarias
        $empresas = \App\Models\DependenciaMercadoSolicitud::with([
            'dependenciaEmpresa',
            'sectorPrivado',
            'sectorPublico',
            'sectorUaslp'
        ])->get();

        return view('administrador.empresas', compact('empresas'));
    }

    public function constancias(Request $request)
    {
        $query = Alumno::whereHas('estados', function ($q) {
            $q->where('etapa', 'CONSTANCIA DE VALIDACIÓN DE PRÁCTICAS PROFESIONALES')
            ->where('estado', '!=', 'pendiente');
        });

        if ($request->busqueda) {
            $query->where(function ($q) use ($request) {
                $q->where('Clave_Alumno', 'like', "%{$request->busqueda}%")
                ->orWhere('Nombre', 'like', "%{$request->busqueda}%")
                ->orWhere('ApellidoP_Alumno', 'like', "%{$request->busqueda}%")
                ->orWhere('ApellidoM_Alumno', 'like', "%{$request->busqueda}%");
            });
        }

        if ($request->fecha_inicio) {
            $query->whereHas('estados', fn($q) =>
                $q->whereDate('updated_at', '>=', $request->fecha_inicio)
            );
        }

        if ($request->fecha_termino) {
            $query->whereHas('estados', fn($q) =>
                $q->whereDate('updated_at', '<=', $request->fecha_termino)
            );
        }

        $alumnos = $query->with('estados')->get();

        return view('administrador.constancias', compact('alumnos'));
    }

    public function autorizacionesPendientes()
    {
        $autorizaciones = collect();

        /* ================= SOLICITUDES FPP01 ================= */
        $solicitudes = SolicitudFPP01::with('alumno')
            ->where(function($q){
                $q->where('Autorizacion', 'pendiente')
                ->orWhereNull('Autorizacion');
            })
            ->get();

        foreach ($solicitudes as $s) {
            $autorizaciones->push([
                'tipo' => 'Solicitud',
                'id' => $s->Id_Solicitud_FPP01,
                'clave' => $s->Clave_Alumno,
                'alumno_nombre' => $s->alumno->Nombre ?? '',
                'alumno_apellido' => $s->alumno->ApellidoP_Alumno ?? '',
                'carrera' => $s->alumno->Carrera ?? '',
                'fecha' => $s->Fecha_Solicitud,
                'estado' => $s->Autorizacion,
                'materia' => $s->Materia,
                'fecha_inicio' => $s->Fecha_Inicio,
                'fecha_termino' => $s->Fecha_Termino,
            ]);
        }

        /* ================= REGISTROS FPP02 ================= */
        $registros = Expediente::with(['solicitud.alumno', 'registro'])
            ->whereNotNull('Solicitud_FPP02_Firmada')
            ->get();

        foreach ($registros as $r) {
            if(!$r->solicitud || !$r->solicitud->alumno || !$r->registro){
                continue;
            }

            $autorizaciones->push([
                'tipo' => 'Registro',
                'id' => $r->id,
                'clave' => $r->solicitud->Clave_Alumno,
                'alumno_nombre' => $r->solicitud->alumno->Nombre ?? '',
                'alumno_apellido' => $r->solicitud->alumno->ApellidoP_Alumno ?? '',
                'carrera' => $r->solicitud->alumno->Carrera ?? '',
                'fecha' => $r->solicitud->Fecha_Solicitud ?? null,
                'estado' => $r->registro->Autorizacion ?? 'pendiente',
                'empresa' => $r->registro->Empresa ?? '—',
                'proyecto' => $r->registro->Proyecto ?? '—',
            ]);
        }

        /* ================= REPORTES ================= */
        $reportes = Reporte::with('alumno')
            ->whereNull('Calificacion')
            ->get();

        foreach ($reportes as $rep) {
            $autorizaciones->push([
                'tipo' => 'Reporte',
                'id' => $rep->id,
                'clave' => $rep->Clave_Alumno,
                'alumno_nombre' => $rep->alumno->Nombre ?? '',
                'alumno_apellido' => $rep->alumno->ApellidoP_Alumno ?? '',
                'carrera' => $rep->alumno->Carrera ?? '',
                'fecha' => $rep->Fecha_Reporte,
                'estado' => 'pendiente',
                'titulo' => $rep->Titulo,
            ]);
        }

        $carreras = CarreraIngenieria::orderBy('Descripcion_Capitalizadas')->get();

        return view('administrador.autorizaciones', compact(
            'autorizaciones',
            'carreras',
            'solicitudes',
            'registros',
            'reportes'
        ));
    }

    public function destroyEmpresa($id)
    {
        DB::transaction(function() use ($id) {
            // Buscar la empresa
            $empresa = \App\Models\DependenciaEmpresa::findOrFail($id);

            // Borrar relaciones DependenciaMercadoSolicitud
            $relaciones = \App\Models\DependenciaMercadoSolicitud::where('Id_Depend_Emp', $id)->get();

            foreach ($relaciones as $rel) {
                // Si hay solicitud asociada, limpiar referencias de expedientes
                $solicitudId = $rel->Id_Solicitud_FPP01;

                // Borrar expedientes relacionados
                \App\Models\Expediente::where('Id_Solicitud_FPP01', $solicitudId)->delete();

                // Opcional: podrías resetear los campos de la solicitud en lugar de borrar
                \App\Models\SolicitudFPP01::where('Id_Solicitud_FPP01', $solicitudId)
                    ->update([
                        'Datos_Empresa' => 0,
                        'Productos_Servicios_Emp' => null,
                        'Nombre_Proyecto' => null,
                    ]);

                // Finalmente, borrar la relación con la empresa
                $rel->delete();
            }

            // Borrar sectores relacionados
            if ($empresa->Clasificacion == '1') { // privado
                \App\Models\SectorPrivado::where('Id_Privado', function($q) use ($id) {
                    $q->select('Id_Privado')->from('dependenciamercadosolicitud')->where('Id_Depend_Emp', $id);
                })->delete();
            } elseif ($empresa->Clasificacion == '0') { // público
                \App\Models\SectorPublico::where('Id_Publico', function($q) use ($id) {
                    $q->select('Id_Publico')->from('dependenciamercadosolicitud')->where('Id_Depend_Emp', $id);
                })->delete();
            }

            // Finalmente, borrar la empresa
            $empresa->delete();
        });

        return redirect()->route('administrador.empresas')
            ->with('success', 'Empresa eliminada correctamente.');
    }
}

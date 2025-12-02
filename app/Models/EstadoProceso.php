<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProceso extends Model
{
    protected $table = 'estado_proceso';
    protected $fillable = ['clave_alumno', 'etapa', 'estado', 'fecha_termino' ];

    public $timestamps = false;

    public static function estado($claveAlumno, $etapa)
    {
        return self::where('clave_alumno', $claveAlumno)
            ->where('etapa', $etapa)
            ->value('estado');
    }

    public static function actualizarEstado($claveAlumno, $etapa, $estado)
    {
        $registro = self::where('clave_alumno', $claveAlumno)
            ->where('etapa', $etapa)
            ->first();

        if ($registro) {
            $registro->estado = $estado;
            $registro->save();
        }
    }

}
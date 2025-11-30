<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProceso extends Model
{
    protected $table = 'estado_proceso';
    protected $fillable = ['clave_alumno', 'etapa', 'estado'];

    public $timestamps = false;

    public static function estado($claveAlumno, $etapa)
    {
        return self::where('clave_alumno', $claveAlumno)
            ->where('etapa', $etapa)
            ->value('estado');
    }

}
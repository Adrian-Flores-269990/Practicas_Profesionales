<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProceso extends Model
{
    protected $table = 'estado_proceso';
    protected $fillable = ['clave_alumno', 'etapa', 'estado'];

    public $timestamps = false;
}
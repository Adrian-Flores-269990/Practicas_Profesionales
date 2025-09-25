<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encargado extends Model
{
    protected $table = 'Encargado'; // nombre exacto de la tabla

    protected $primaryKey = 'Id_Encargado';

    public $timestamps = false; // porque solo tienes created_at, no updated_at

    protected $fillable = [
        'Nombre',
        'RPE',
        'Area',
        'Carrera',
        'Cargo',
        'Correo_Electronico',
        'Telefono',
        'Contrasena'
    ];
}
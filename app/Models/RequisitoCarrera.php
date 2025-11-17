<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoCarrera extends Model
{
    protected $table = 'requisito_carrera';
    protected $primaryKey = 'Clave';
    public $timestamps = false;

    protected $fillable = [
        'Clave',
        'Id_Carrera_I',
        'Espacio_de_Formacion',
        'Obligatoria_Optativa',
        'Creditos_Prerequisitos',
        'Nivel',
        'Creditos',
    ];

    // Un requisito pertenece a una carrera
    public function carrera()
    {
        return $this->belongsTo(CarreraIngenieria::class, 'Id_Carrera_I', 'Id_Carrera_I');
    }
}

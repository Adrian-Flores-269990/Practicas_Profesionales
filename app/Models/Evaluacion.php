<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluacion';
    protected $primaryKey = 'Id_Evaluacion';
        public $timestamps = true;

    protected $fillable = [
        'Tipo_Evaluacion',
        'Id_Solicitud_FPP01',
        'Id_Depn_Emp',
        'Estado'
    ];

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'Id_Evaluacion', 'Id_Evaluacion');
    }

}

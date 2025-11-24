<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $table = 'respuesta';
    protected $primaryKey = 'Id_Respuesta';
    public $timestamps = false;

    protected $fillable = [
        'Id_Evaluacion',
        'Id_Pregunta',
        'Respuesta'
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'Id_Evaluacion', 'Id_Evaluacion');
    }
}

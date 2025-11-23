<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependenciaEmpresa extends Model
{
    use HasFactory;

    protected $table = 'dependencia_empresa';
    protected $primaryKey = 'Id_Depn_Emp';
    public $timestamps = false;

    protected $fillable = [
        'Nombre_Depn_Emp',
        'Clasificacion',
        'Calle',
        'Numero',
        'Colonia',
        'Cp',
        'Estado',
        'Municipio',
        'Teléfono',
        'Ramo',
        'RFC_Empresa',
        'Comentario_Encargado',
        'Autorizada'
    ];

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'Id_Depn_Emp', 'Id_Depn_Emp');
    }
}

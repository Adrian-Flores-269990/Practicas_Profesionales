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
        'Nombre_Depn_Emp', 'Calle', 'Numero', 'Colonia', 'Cp',
        'Estado', 'Municipio', 'RFC_Empresa', 'Razon_Social'
    ];
}

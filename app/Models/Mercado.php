<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercado extends Model
{
    use HasFactory;

    protected $table = 'mercado';
    protected $primaryKey = 'Id_Mercado';
    public $timestamps = false;

    protected $fillable = [
        'Nombre'
    ];
}

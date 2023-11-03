<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaProcedimientoInvasivo extends Model
{
    public $table = "formulario_hoja_enfermeria_procedimientos_invasivos";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
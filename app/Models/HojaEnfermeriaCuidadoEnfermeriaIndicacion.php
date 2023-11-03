<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaCuidadoEnfermeriaIndicacion extends Model
{
    public $table = "formulario_hoja_enfermeria_cuidado_enfermeria_indicacion";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}

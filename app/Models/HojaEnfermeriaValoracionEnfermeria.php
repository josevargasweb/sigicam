<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaValoracionEnfermeria extends Model
{
    public $table = "formulario_hoja_enfermeria_valoracion_enfermeria";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}
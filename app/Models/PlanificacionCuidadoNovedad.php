<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidadoNovedad extends Model
{
    public $table = "formulario_planificacion_cuidados_novedades";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}

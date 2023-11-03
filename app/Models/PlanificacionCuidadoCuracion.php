<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidadoCuracion extends Model
{
    public $table = "formulario_planificacion_cuidados_curaciones";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidadoProteccion extends Model
{
    public $table = "formulario_planificacion_cuidados_protecciones";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}

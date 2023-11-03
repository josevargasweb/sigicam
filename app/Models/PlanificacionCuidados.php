<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PlanificacionCuidados extends Model
{
    protected $table = "formulario_planificacion_cuidados";
    protected $primaryKey = 'id_formulario_planificacion_cuidados';
    public $timestamps = false;
    public $incrementing = false;
}

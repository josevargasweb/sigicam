<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nova extends Model
{
    protected $table = "formulario_escala_nova";
	public $timestamps = false;
	protected $primaryKey = "id_formulario_escala_nova";
	protected $fillable = ['estado_mental', 'incontinencia', 'movilidad', 'nutricion_ingesta', 'actividad', 'total'];
}

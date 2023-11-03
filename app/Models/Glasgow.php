<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Glasgow extends Model
{
    protected $table = "formulario_escala_glasgow";
	public $timestamps = false;
	protected $primaryKey = "id_formulario_escala_glasgow";
	protected $fillable = ['id_formulario_escala_glasgow','caso','usuario_responsable','fecha_creacion','fecha_modificacion','apertura_ocular','respuesta_verbal','respuesta_motora','total','tipo','usuario_modifica','visible','id_anterior'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentariosAgregadosIndicacionMedica extends Model
{
    protected $table = "comentarios_agregados_indicacion_medica";
    public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

    public function indicacion_medica(){
		return $this->belongTo('App\Models\IndicacionMedica','id');
	}

    // UN COMENTARIO TIENE UN USUARIO INGRESA.
    public function usuario(){
        return $this->hasOne('App\Models\Usuario','id','usuario_ingresa');
    }
}

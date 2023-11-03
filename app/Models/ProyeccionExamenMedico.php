<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyeccionExamenMedico extends Model
{
    protected $table = "proyecciones_examen_medico";
    public $timestamps = false;
	protected $primaryKey = "id";

    //relacion uno a muchos.
	public function examen_medico(){
		return $this->belongTo('App\Models\ExamenMedico','id');
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamenMedico extends Model
{
    protected $table = "examenes_medicos";
    public $timestamps = false;
    protected $primaryKey = "id";

    //relacion uno a muchos
    public function cirugias_previas(){
		return $this->hasMany('App\Models\CirugiaPreviaExamenMedico','examen_medico_id');
	}

	public function proyecciones(){
		return $this->hasMany('App\Models\ProyeccionExamenMedico','examen_medico_id');
    }
}

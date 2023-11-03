<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use Carbon\Carbon;

class ConfiguracionVisitas extends Model
{
    protected $table = "configuracion_visitas";
    protected $primaryKey = "id";
    public $timestamps = false;
	protected $fillable = [
        'visible',
    ];


    public static function existenVisitasPorCaso($idCaso){
		$visitas=self::where("id_caso", "=", $idCaso)->whereNull("fecha_modificacion")->first();
		if(is_null($visitas)){ return false;}
		return true;
	}
	
	public function ocultarRegistros($id_caso){
		$id_usuario = Auth::user()->id;
		$tiempo = Carbon::now()->format("Y-m-d H:i:s");
		$sql = "UPDATE configuracion_visitas
		SET
		visible = false,
		usuario_modifica = $id_usuario,
		fecha_modificacion = '$tiempo'
		WHERE 
		id_caso = ?
		AND visible = true";
		DB::update($sql,[$id_caso]);
	}
	
	public function guardar($request){
		$tiempo = Carbon::now()->format("Y-m-d H:i:s");
		$id_usuario = Auth::user()->id;
		$this->fecha = $tiempo;
		$this->fecha_creacion = $tiempo;
		//$this->fecha_modificacion = $tiempo;
		$this->recibe_visitas = $request->recibe_visitas_;
		$this->comentario_visitas = strip_tags($request->comentario_visitas_);
		$this->num_personas_visitas = $request->cantidad_personas_;
		$this->cant_horas_visitas = $request->cantidad_horas_;
		$this->usuario_asigna = $id_usuario;
		//$this->usuario_modifica = $id_usuario;
		$this->attributes['visible'] = true;
		$this->id_caso = $request->id_caso_;
		$this->save();
	}

}

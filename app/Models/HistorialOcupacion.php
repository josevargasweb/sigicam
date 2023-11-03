<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
class HistorialOcupacion extends Historial{

	protected $table = "historial_ocupaciones";
	protected $criterio = "cama";
	protected $fillable = ["cama"];
	public function camas(){
		return $this->belongsTo("App\Models\Cama", "cama", "id");
	}
	public function casos(){
		return $this->belongsTo("App\Models\Caso", "caso", "id");
	}

	public function scopeEnFecha($query, $fecha = null){
		/* @var $query HistorialOcupacion */
		if($fecha){
			return $query->where("fecha", "<=", $fecha);
		}
		else{
			return $query;
		}
	}

	public function scopeNoLiberados($query, $fecha = null){
		/* @var $query HistorialOcupacion */
		if(is_null($fecha)){
			$fecha = \Carbon\Carbon::now();
		}
		DB::statement("DROP TABLE IF EXISTS temp_historial_ocupaciones");
		/* POR QUE TIENE QUE SER DISTINCT ? */
		DB::statement("CREATE TEMPORARY TABLE temp_historial_ocupaciones AS (SELECT distinct on (cama) id as t_id, cama as t_cama, caso as t_caso, fecha as t_fecha, (CASE WHEN fecha_liberacion > ? OR fecha_liberacion is NULL THEN null ELSE fecha_liberacion END) as t_fecha_liberacion, motivo as t_motivo FROM t_historial_ocupaciones WHERE fecha < ?  ORDER BY cama, fecha desc)", ["{$fecha}", "{$fecha}"]);
		return $query->join("temp_historial_ocupaciones as tmp", "tmp.t_id", "=", "historial_ocupaciones.id")->whereNull("tmp.t_fecha_liberacion");
	}

	public function scopeAlta($query){
		return $query->whereMotivo_liberacion('alta')->orderBy("fecha", "desc");
	}

	public static function getPorPaciente($idPaciente){
		return self::noLiberados()->orderBy("fecha", "desc")->actuales()->wherePaciente($idPaciente)->first();
	}

	public function scopeLibres($query){
		return $query->where("rk", "=", 1)->whereNotNull("fecha_liberacion");
	}

	public function establecimiento(){
		return Establecimiento::whereHas("unidades", function ($unidad) {
			$unidad->whereHas("camas", function ($camas) {
				$camas->where(
					"camas.id", $this->cama
				);
			});
		})->get();
	}
	public static function estaOcupada($camas){
		return self::whereNull('fecha_liberacion')->whereIn('cama',$camas)->count();
   }
}


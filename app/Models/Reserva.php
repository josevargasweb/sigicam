<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Reserva extends Model{
	protected $table = "t_reservas";

	public function scopeVigentes($query){
		return $query->where("rk", 1)->where("queda", ">", '00:00:00');
	}

	public function scopeReservasVigentes($q, \Carbon\Carbon $fecha = null){
		if(is_null($fecha)){
			$fecha = \Carbon\Carbon::now();
		}
		/* @var $q \Illuminate\Database\Query\Builder */
		DB::statement("DROP table IF EXISTS temp_reservas");
		DB::statement("CREATE TEMP TABLE temp_reservas AS (SELECT distinct on (cama) id as t_id, fecha as t_fecha, tiempo as t_tiempo, cama as t_cama FROM t_reservas WHERE fecha < ? ORDER BY cama, fecha desc)", ["{$fecha}"]);
		return $q->join("temp_reservas as tmp", "tmp.t_id", "=", "t_reservas.id")->whereRaw("(t_reservas.fecha + t_reservas.tiempo) > ?", ["{$fecha}"]);
	}

	public function camasReservadas(){
		return $this->belongsTo("App\Models\Cama", "cama", "id");
	}



}


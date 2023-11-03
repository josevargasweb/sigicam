<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use OwenIt\Auditing\Contracts\Auditable;


class HistorialBloqueo extends Model implements Auditable{
	use \OwenIt\Auditing\Auditable;
	
	protected $table = "t_historial_bloqueo_camas";
	protected $criterio = "cama";
  
 	protected $auditInclude = [
    
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;


	public function camas(){
		return $this->belongsTo("App\Models\Cama", "cama", "id");
	}

	public function scopeNoLibres($query){
		return $query->where("evento", "=", "ocupado")
		->orWhere('evento', "=", "reservado");
	}

	public static function estaCamaBloqueada($idCama){
		$historial=DB::table("camas_habilitadas as c")
		->where("c.id", "=", $idCama)->count();
		if($historial == 0) return false;
		return true;
		/*$historial=DB::table("historial_bloqueo_camas as h")
		->where("cama", "=", $idCama)->first();
		if(is_null($historial)) return true;
		if(is_null($historial->fecha_habilitacion)) return true;*/
	}
	public static function estaBloqueada($camas){
		return self::whereNull('fecha_habilitacion')->whereIn('cama',$camas)->count();
	}

}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class CaracteristicasAgente extends Model{
	
	protected $table = "caracteristicas_agente";
	public $timestamps = false;

	public static function getlocalizacion(){
		$response=array();

		$motivos=DB::table("caracteristicas_agente as e")
		->select("e.nombre")
		->get();
		foreach ($motivos as $motivo) {
			if ($motivo->nombre == "Otros") {
				$ultimo = $motivo->nombre;
			}else{
				$response[$motivo->nombre]=ucwords($motivo->nombre);
			}
		}
		$response[$ultimo]=ucwords($ultimo);
		return $response;
	}


}

?>
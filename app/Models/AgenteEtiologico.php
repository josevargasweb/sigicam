<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;

class AgenteEtiologico extends Model{
	
	protected $table = "agente_etiologico";
	public $timestamps = false;

	public static function getlocalizacion(){
		$response=array();

		$motivos=DB::table("agente_etiologico as e")
		->select("e.nombre")
		->get();
		foreach ($motivos as $motivo) {
			if ($motivo->nombre == "Otros virus") {
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
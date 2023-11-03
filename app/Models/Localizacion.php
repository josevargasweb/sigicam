<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;

class Localizacion extends Model{
	
	protected $table = "localizacion_infeccion";
	public $timestamps = false;

	public static function getlocalizacion(){
		$response=array();

		$motivos=DB::table("localizacion_infeccion as e")
		->select("e.nombre")
		->get();
		foreach ($motivos as $motivo) {
			if ($motivo->nombre == "Otro") {
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
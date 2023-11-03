<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class ProcedimientoInvasivo extends Model{
	
	protected $table = "procedimiento_invasivo";
	public $timestamps = false;

	public static function getlocalizacion(){
		$response=array();

		$motivos=DB::table("procedimiento_invasivo as e")
		->select("e.nombre")
		->orderBy('id', 'asc')
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
<?php namespace App\Models{

use DB;

class Prevision {
	public static function getPrevisiones(){
		$response=array();
		$motivos=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "prevision_salud")
		->whereNotIn('e.enumlabel', ['LIBRE ELECCIÓN', 'INDETERMINADO', 'CONVENIO', 'PARTICULAR', 'FFAA', 'SIN INFORMACION', 'PRAIS', 'OTROS'])->get();
		foreach ($motivos as $motivo) {
			if ($motivo->enumlabel == "DESCONOCIDO") {
				$ultimo = [$motivo->enumlabel , $motivo->enumlabel];
			}else{
				$response[$motivo->enumlabel]=ucwords($motivo->enumlabel);
			}
		}
		$response[$ultimo[1]] = ucwords($ultimo[0]);
		return $response;
	}
}

}
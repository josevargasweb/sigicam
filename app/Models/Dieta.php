<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;

class Dieta extends Model{
	protected $table = "t_dietas_pacientes";
	
	public static function getDietas($excepto = null){
		$response=array();
		$dietas=DB::table("pg_enum as e")
		->join("pg_type as t", "e.enumtypid", "=", "t.oid")
		->select("e.enumlabel")
		->where("t.typname", "=", "dieta");
		if($excepto){
			$dietas->where("e.enumlabel", "<>", $excepto);
		}
		$dietas = $dietas->get();
		foreach ($dietas as $dieta) {
			$response[$dieta->enumlabel]=ucwords($dieta->enumlabel);
		}
		return $response;
		/*$dietas = DB::table(DB::raw("(SELECT enumlabel as nombre FROM pg_enum WHERE enumtypid = 32313) AS dietas"))->get();
		$ret = array();
		foreach ($dietas as $dieta){
			$ret[$dieta->nombre] = $dieta->nombre;
		}
		return $ret;*/
	}
}
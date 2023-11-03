<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tipousuario;
use DB;

class DisponibilidadContingencia extends Model{
	protected $table = "disponibilidad_contingencia";
	public $timestamps = false;

	public static function getHospitales(){
		return DB::table("disponibilidad_contingencia as dc")
		->join("establecimientos as e", "dc.establecimiento", "=", "e.id");
	}

	public static function getHospitalesPorUsuario($usuario, $id){
		return self::getHospitales()
		->join("solicitudes_contingencia as sc", "sc.id", "=", "dc.solicitud")
		->where("dc.solicitud", "=", $id)
		->where("sc.usuario", "=", $usuario)
		->select("e.nombre")->get();
	}

	public static function getTodosHospitales($id){
		return self::getHospitales()
		->join("solicitudes_contingencia as sc", "sc.id", "=", "dc.solicitud")
		->where("dc.solicitud", "=", $id)
		->select("e.nombre")->get();
	}

	public static function getHospitalesPorContingencia($usuario, $id, $tipoUsuario){
		$response=array();
		$nombres=($tipoUsuario == TipoUsuario::ADMINSS || $tipoUsuario == TipoUsuario::MONITOREO_SSVQ) ? self::getTodosHospitales($id) : self::getHospitalesPorUsuario($usuario, $id);
		foreach($nombres as $nombre){
			$response[]=$nombre->nombre;
		}
		return $response;
	}

}


<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class EstablecimientosExtrasistema extends Model{

	protected $table = "establecimientos_extrasistema";
	public $timestamps = false;

	public static function getEstablecimiento(){
		$response=array();
		$estabs=self::all();
		foreach($estabs as $estab) $response[$estab->id]=$estab->nombre;
		return $response;
	}

	public static function existeEstablecimiento($nombre){
		if(empty($nombre)) return false;
		$estab=self::where("nombre", "=", $nombre)->first();
		if(is_null($estab)) return false;
		return true;
	}
}

?>
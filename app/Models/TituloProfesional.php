<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class TituloProfesional extends Model{

    protected $table = "titulo_profesional";
    protected $primaryKey = "id";
	public $timestamps = false;

    public static function getTituloProfesionales(){
		$response=array();
		$titulos=self::all();
		foreach($titulos as $titulo){
			$response[$titulo->codigo]=$titulo->nombre;
		}
		return $response;
	}
    public static function getTituloProfesional($codigo){
		return self::where("codigo", "=", $codigo)->first();
	}

}

?>

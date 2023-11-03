<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class Especialidades extends Model{

    protected $table = "especialidades";
    protected $primaryKey = "id";
	public $timestamps = false;

    public static function getEspecialidades(){
		$response=array();
		$especialidades=self::all();
		foreach($especialidades as $especialidad){
			$response[$especialidad->id]=$especialidad->nombre;
		}
		return $response;
	}

}

?>

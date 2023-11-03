<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class EspecialidadMedica extends Model{

    protected $table = "especialidades_medicas";
    protected $primaryKey = "id";
	public $timestamps = false;

    public static function getEspecialidadesMedicas(){
		$response=array();
		$especialidades=self::all();
		foreach($especialidades as $especialidad){
			$response[$especialidad->codigo]=$especialidad->nombre;
		}
		return $response;
	}
    public static function getEspecialidadesMedicasArray($arrayEspecialidades){
		return self::whereIn('codigo', $arrayEspecialidades)->get();
	}

}

?>

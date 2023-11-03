<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Session;
use DB;

class Medico extends Model{
	
	protected $table = "medico";
	public $timestamps = false;
	protected $primaryKey = "id_medico";

	public static function getMedicos(){
		$medicos = Medico::select('id_medico', 'nombre_medico', 'apellido_medico')
				->where('establecimiento_medico', '=', Session::get('idEstablecimiento'))
				->where('visible_medico', '=', true)
				->get();

		$response = array();
		foreach ($medicos as $medico) {
			$response[$medico->id_medico] = $medico->nombre_medico.' '.$medico->apellido_medico;
		}

		return $response;
		
	}

	public static function nombreMedico($id){
		$medico = Medico::find($id);

		$response = "";
		if(isset($medico)){
			$response = $medico->nombre_medico." ".$medico->apellido_medico;
		}

		return $response;
		
	}
	
	public static function infoMedicoHabilitados(){
		$habilitados = Medico::join('establecimientos as e','medico.establecimiento_medico','=','e.id')
		->leftjoin('especialidades_medico','medico.id_medico','=','especialidades_medico.id_medico')
		->leftjoin('titulo_profesional','medico.cod_titulo','=','titulo_profesional.id')
		->leftjoin('especialidades_medicas','especialidades_medicas.codigo','=','especialidades_medico.cod_especialidad')
		->select("medico.id_medico","medico.rut_medico","medico.dv_medico","e.nombre as nombre_establecimiento","medico.nombre_medico","medico.apellido_medico","medico.celular","medico.correo","titulo_profesional.nombre as titulo_profesional",DB::raw("string_agg(especialidades_medicas.nombre,',') as especialidad"))
		->groupBy("medico.id_medico","medico.rut_medico","medico.dv_medico","nombre_establecimiento","medico.nombre_medico","medico.apellido_medico","medico.celular","medico.correo","titulo_profesional")
		->where(function($q){
			$q->where("especialidades_medico.visible", "=",  true)
			->orWhereNull('especialidades_medico.visible');
		})
		->where("medico.visible_medico", "=",  true)
		->get();
		
		return $habilitados;
	}

	public static function infoMedicoDeshabilitados(){
		$deshabilitados = Medico::join('establecimientos as e','medico.establecimiento_medico','=','e.id')
		->leftjoin('especialidades_medico','medico.id_medico','=','especialidades_medico.id_medico')
		->leftjoin('titulo_profesional','medico.cod_titulo','=','titulo_profesional.id')
		->leftjoin('especialidades_medicas','especialidades_medicas.codigo','=','especialidades_medico.cod_especialidad')
		->select("medico.id_medico","medico.rut_medico","medico.dv_medico","e.nombre as nombre_establecimiento","medico.nombre_medico","medico.apellido_medico","medico.celular","medico.correo","titulo_profesional.nombre as titulo_profesional",DB::raw("string_agg(especialidades_medicas.nombre,',') as especialidad"))
		->groupBy("medico.id_medico","medico.rut_medico","medico.dv_medico","nombre_establecimiento","medico.nombre_medico","medico.apellido_medico","medico.celular","medico.correo","titulo_profesional")
		->where(function($q){
			$q->where("especialidades_medico.visible", "=",  true)
			->orWhereNull('especialidades_medico.visible');
		})
		->where("medico.visible_medico", "=",  false)
		->get();
		
		return $deshabilitados;
	}

	

	
}

?>
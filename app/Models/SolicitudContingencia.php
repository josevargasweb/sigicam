<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Tipousuario;

class SolicitudContingencia extends Model{
	protected $table = "solicitudes_contingencia";
	public $timestamps = true;


	public static function getContingencias(){
		return DB::table("solicitudes_contingencia as sc")
		->where("valido", "=", true)
		->select("sc.solicitante", "sc.fecha", "sc.n_pacientes_espera", 
			"sc.n_pacientes_basica", "sc.n_pacientes_compleja", "sc.id", "sc.establecimiento")
		->orderBy("sc.fecha","desc")
		->get();
	}

	public function usuarioSolicitante(){
		return $this->belongsTo("App\Models\Usuario", "usuario", "id");
	}

	public static function getContingenciasPorEstablecimiento($id){
		return DB::table("solicitudes_contingencia as sc")
		->where("sc.establecimiento", "=", $id)
		->where("valido", "=", true)
		->select("sc.solicitante", "sc.fecha", "sc.n_pacientes_espera", 
			"sc.n_pacientes_basica", "sc.n_pacientes_compleja", "sc.id", "sc.establecimiento")
		->orderBy("sc.fecha","desc")
		->get();
	}

}

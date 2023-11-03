<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Carbon\Carbon;
use Auth;


class ComentariosIndicacionMedica extends Model{

    protected $table = "comentarios_indicacion_medica";
	public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

	//relacion uno a muchos.
	public function indicacion_medica(){
		return $this->belongTo('App\Models\IndicacionMedica','id');
	}

	public static function comparar($id, $datos){
		if($id){
			$editadoOriginal = ComentariosIndicacionMedica::find($id);
			$editado = $editadoOriginal->only([
				'id',
				'im_id',
				'caso',
				'comentario'
			]);

			$resultado = array_diff_assoc($datos, $editado);
			if(empty($resultado)){
				$resp = "nada";
			}else{
				$resp = "cambios";
			}
		}else{
			$resp = "nada";
		}
		return $resp;
	}

	public static function falso($falsear,$fecha){
		$falsear->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Editado'
		]);
		return $falsear;
	}

	public static function nuevo($data_comentario,$fecha,$id_indicacion){
		$actualizado = new ComentariosIndicacionMedica;
		$actualizado->im_id = $id_indicacion;
		$actualizado->caso = $data_comentario["caso"];
		$actualizado->usuario_ingresa = Auth::user()->id;
		$actualizado->fecha_creacion = $fecha;
		$actualizado->visible = true;
		$actualizado->comentario = $data_comentario["comentario"];
		$actualizado->save();
		return $actualizado;
	}

	public static function eliminar($eliminar,$fecha){
		$eliminar->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Eliminado'
		]);
		return $eliminar;
	}
}

?>
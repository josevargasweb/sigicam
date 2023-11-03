<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Carbon\Carbon;
use Auth;


class FarmacosIndicacionMedica extends Model{

    protected $table = "farmacos_indicacion_medica";
	public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

	//relacion uno a muchos.
	public function indicacion_medica(){
		return $this->belongTo('App\Models\IndicacionMedica','id');
	}

	public static function comparar($id, $datos){
		if($id){
			$editadoOriginal = FarmacosIndicacionMedica::find($id);
			$editado = $editadoOriginal->only([
				'id',
				'im_id',
				'caso',
				'id_farmaco',
				// 'nombre_farmaco',
				'via_administracion',
				'intervalo_farmaco',
				'detalle_farmaco'
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

	public static function falso($falsear, $fecha){
		$falsear->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Editado'
		]);
		return $falsear;
	}

	public static function suspender($suspender,$fecha){
		$suspender->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Suspendido'
		]);
		return $suspender;
	}

	public static function nuevo($data_farmaco,$fecha,$id_indicacion){
		if($id_indicacion){
			$indicacion = $id_indicacion;
		}else{
			$indicacion = $data_farmaco["im_id"];
		}
		$actualizado = new FarmacosIndicacionMedica;
		$actualizado->im_id = $indicacion;
		$actualizado->caso = $data_farmaco["caso"];
		$actualizado->usuario_ingresa = Auth::user()->id;
		$actualizado->fecha_creacion = $fecha;
		$actualizado->visible = true;
		$actualizado->id_farmaco = $data_farmaco["id_farmaco"];
		$actualizado->via_administracion = ($data_farmaco["via_administracion"] != '')?$data_farmaco["via_administracion"]:null;
		$actualizado->intervalo_farmaco = ($data_farmaco["intervalo_farmaco"] != '')?$data_farmaco["intervalo_farmaco"]:null; 
		$actualizado->detalle_farmaco = ($data_farmaco["detalle_farmaco"] != '')?$data_farmaco["detalle_farmaco"]:null; 
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
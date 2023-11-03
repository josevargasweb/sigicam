<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Carbon\Carbon;
use Auth;

class TiposReposoIndicacionMedica extends Model{

    protected $table = "tipos_reposo_indicacion_medica";
	public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

	//relacion uno a muchos.
	public function indicacion_medica(){
		return $this->belongTo('App\Models\IndicacionMedica','id');
	}

	public static function compararVista($eliminar,$tipos_){
		return in_array($eliminar,$tipos_);
	}

	public static function comparar($id,$datos_tipo){
		if($id){
			$array_comparacion = ['caso','im_id','tipo'];
			if ($id == 9) {
				$array_comparacion = ['caso','im_id','tipo','detalle_tipo'];
			}
			$editadoOriginal = TiposReposoIndicacionMedica::where('im_id',$datos_tipo["im_id"])
				->where('caso',$datos_tipo["caso"])
				->where('tipo',$id)
				->where('visible',true)
				->first($array_comparacion);

			if($editadoOriginal){
				//Existe el antiguo
				$resultado = array_diff_assoc($datos_tipo,$editadoOriginal->toArray());
				if(empty($resultado)){
					//No hay cambios
					$resp = "nada";
				}else{
					//Se modifico lo que ya tenia
					$resp = "cambios";
				}
			}else{
				//Nuevo ya que no existia
				$resp = "nuevo";
			}
		}else{
			//Si no hay id del reposo, esto indica que no hay datos
			$resp = "nada";
		}
		return $resp;
	}

	public static function nuevo($datos,$fecha,$id_indicacion){
		$tipos_reposo_nuevo = new TiposReposoIndicacionMedica;
		$tipos_reposo_nuevo->im_id = $id_indicacion;
		$tipos_reposo_nuevo->caso = $datos["caso"];
		$tipos_reposo_nuevo->usuario_ingresa = Auth::user()->id;
		$tipos_reposo_nuevo->fecha_creacion = $fecha;
		$tipos_reposo_nuevo->visible = true;
		$tipos_reposo_nuevo->tipo = $datos["tipo"];
		$tipos_reposo_nuevo->detalle_tipo = ($datos["tipo"] == 9) ? $datos["detalle_tipo"] : null;
		$tipos_reposo_nuevo->save();
		return $tipos_reposo_nuevo;
	}

	public static function falso($datos,$fecha,$id){
		if($id){
			$falsear_reposo = TiposReposoIndicacionMedica::find($id);
			$estado = "eliminado";
		}else{
			$falsear_reposo = TiposReposoIndicacionMedica::where('caso',$datos["caso"])
			->where('im_id',$datos["im_id"])
			->where('tipo',$datos["tipo"])
			->where('visible',true)
			->first();
			$estado = "Editado";
		}
					
		if($falsear_reposo){
			$falsear_reposo->update([
				'usuario_modifica' => Auth::user()->id,
				'fecha_modificacion' => $fecha,
				'visible' => false,
				'estado' => $estado
			]);
			return true;
		}else{
			return false;
		}
	}

	public static function eliminar($eliminar,$fecha){
		$eliminar->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Eliminado'
		]);
	}

	public static function primeraLetraTipoReposo($tipo_reposo){
		$resp = "";
		switch ($tipo_reposo) {
			case 1:
				$resp = "A";
				break;
			case 2:
				$resp = "S";
				break;
			case 3:
				$resp = "R";
				break;
			case 4:
				$resp = "R-A";
				break;
			case 5:
				$resp = "O";
				break;
		}
		return $resp;
	}
}

?>
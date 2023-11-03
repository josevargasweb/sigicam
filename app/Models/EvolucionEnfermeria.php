<?php
namespace App\models;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barthel;
use Auth;
use Carbon\Carbon;
use Log;

class EvolucionEnfermeria extends Model{
	
	protected $table = "evolucion_enfermeria";
	protected $primaryKey = "id";
    public $timestamps = false;

	public static function guardar($nuevo){
		$copia = new EvolucionEnfermeria;
		$copia->caso = $nuevo->idCaso;
		$copia->usuario_ingresa = Auth::user()->id;
		$copia->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$copia->neurologico = strip_tags($nuevo->neurologico);
		$copia->cardiovascular = strip_tags($nuevo->cardiovascular);
		$copia->respiratorio = strip_tags($nuevo->respiratorio);
		$copia->digestivo = strip_tags($nuevo->digestivo);
		$copia->metabolico = strip_tags($nuevo->metabolico);
		$copia->musculoesqueletico = strip_tags($nuevo->musculoesqueletico);
		$copia->tegumentario = strip_tags($nuevo->tegumentario);
		$copia->genitourinario = strip_tags($nuevo->genitourinario);
		
		return $copia;
	}

	public static function guardarBarthel($request, $evolEnf,$tipo_barthel){

		//request trae datos del formulario nuevo
		//evolEnf trae los datos del formualrio antiguo id del formulario anterior
		//tipo_barthel trae que es epicrisis
		
		$barthel = new Barthel;

		if($evolEnf != null && $evolEnf->indbarthel){
			Log::info("viene de Epicrisis");
			Log::info($evolEnf);

			$barthelAnterior = Barthel::where("caso",$evolEnf->caso)
					->where('visible', true)
                    ->where('tipo', 'Epicrisis')
                    ->first();

			if (!$barthelAnterior) {
				//si no encontro el formulario, pero venia con un id, significaque ese formulario fue actualizado
				return array("info" => "Este formulario ya ha sido modificado");	
			}

			$barthelAnterior->update([
				'usuario_modifica' => Auth::user()->id,
				'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
				'visible' => false
			]);
			
			$barthel->id_anterior = $barthelAnterior->id_formulario_barthel;
			
		}

		$barthel->usuario_responsable = Auth::user()->id;
		$barthel->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
		$barthel->caso = $request->caso;
		$barthel->comida = $request->comida;
		$barthel->lavado = $request->lavado;
		$barthel->vestido = $request->vestido;
		$barthel->arreglo = $request->arreglo;
		$barthel->deposicion =$request->deposicion;
		$barthel->miccion = $request->miccion;
		$barthel->retrete = $request->retrete;
		$barthel->trasferencia = $request->trasferencia;
		$barthel->deambulacion = $request->deambulacion; 
		$barthel->escaleras = $request->escaleras;
		$barthel->tipo = $tipo_barthel;
		
		return $barthel;
	}

}
 
?>
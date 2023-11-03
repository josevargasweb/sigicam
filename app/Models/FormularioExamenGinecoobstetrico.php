<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class FormularioExamenGinecoobstetrico extends Model
{
    protected $primaryKey = 'id';
    protected $table = "formulario_examen_ginecoobstetrico";
    public $timestamps = false;
	
	public function guardar($request){

		$ego = self::where("caso","=",$request->caso_ego)->first();
		if(!$ego){
			$ego = new FormularioExamenGinecoobstetrico();
			$ego->caso = $request->caso_ego;
			$ego->id_paciente = $request->id_paciente_ego;
		}
		
		$ego->vulva = $request->vulva_ego;
		$ego->vagina_tacto_vaginal = $request->vagina_tacto_vaginal_ego;
		$ego->fondo_de_saco_tacto_vaginal = $request->fondo_de_saco_tacto_vaginal_ego;
		$ego->anexos = $request->anexos_ego;
		$ego->otros_tacto_vaginal = $request->otros_tacto_vaginal_ego;
		$ego->vagina_especuloscopia = $request->vagina_especuloscopia_ego;
		$ego->utero = $request->utero_ego;
		$ego->cervix = $request->cervix_ego;
		$ego->fondo_de_saco_especuloscopia = $request->fondo_de_saco_especuloscopia_ego;
		$ego->otros_especuloscopia = $request->otros_especuloscopia_ego;
		$ego->recto_ano = $request->recto_ano_ego;
		$ego->presentacion = $request->presentacion_ego;
		$ego->altura_uterina = $request->altura_uterina_ego;
		$ego->tono = $request->tono_ego;
		$ego->encajamiento = $request->encajamiento_ego;
		$ego->dorso = $request->dorso_ego;
		$ego->contracciones = $request->contracciones_ego === "si" ? true : ($request->contracciones_ego === "no" ? false : null);
		$ego->lcf = $request->lcf_ego;
		$ego->desaceleraciones = $request->desaceleraciones_ego === "si" ? true : ($request->desaceleraciones_ego === "no" ? false : null);
		$ego->longitud_cuello_uterino = $request->longitud_cuello_uterino_ego;
		$ego->dilatacion_cuello_uterino = $request->dilatacion_cuello_uterino_ego;
		$ego->membranas = $request->membranas_ego;
		$ego->liquido_amniotico = $request->liquido_amniotico_ego;
		$ego->posicion = $request->posicion_ego;
		$ego->plano = $request->plano_ego;
		$ego->evaluacion_pelvis = $request->evaluacion_pelvis_ego;
		$ego->otros_examen_obstetrico = $request->otros_examen_obstetrico_ego;
		$ego->usuario_responsable = Auth::user()->id;
		
		$ego->save();
	}
	public function cargar($request){
		return self::where("caso","=",$request->caso)->first();
	}
}

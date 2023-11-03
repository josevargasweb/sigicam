<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;
use Log;

class Ginecologica extends Model
{
    protected $table = 'formulario_ie_ginecologica';
	public $timestamps = false;


    public static function crearNuevo($request, $modificar_ginecologica){
        $nuevo_ginecologica = new Ginecologica;
        $nuevo_ginecologica->idcaso = $request->idCaso;

		if($modificar_ginecologica != null || $modificar_ginecologica != ""){
            $nuevo_ginecologica->id_anterior =  $modificar_ginecologica->id;
		}

        if($request->gesta == 'si'){
            $nuevo_ginecologica->gesta = true;
        }elseif($request->gesta == 'no'){
            $nuevo_ginecologica->gesta = false;
        }else{
            $nuevo_ginecologica->gesta = null;
        }
        if($request->observacionGesta == ''){
            $nuevo_ginecologica->gesta_observacion = null;
        }else{
            $nuevo_ginecologica->gesta_observacion = $request->observacionGesta;
        }

        if($request->parto == 'si'){
            $nuevo_ginecologica->parto = true;
        }elseif($request->parto == 'no'){
            $nuevo_ginecologica->parto = false;
        }else{
            $nuevo_ginecologica->parto = null;
        }
        if($request->observacionParto == ''){
            $nuevo_ginecologica->parto_observacion = null;
        }else{
            $nuevo_ginecologica->parto_observacion = $request->observacionParto;
        }
        
        if($request->aborto == 'si'){
            $nuevo_ginecologica->aborto = true;
        }elseif($request->aborto == 'no'){
            $nuevo_ginecologica->aborto = false;
        }else{
            $nuevo_ginecologica->aborto = null;
        }
        if($request->observacionAborto == ''){
            $nuevo_ginecologica->aborto_observacion = null;
        }else{
            $nuevo_ginecologica->aborto_observacion = $request->observacionAborto;
        }
        
        if($request->vaginal == 'si'){
            $nuevo_ginecologica->parto_vaginal = true;
        }elseif($request->vaginal == 'no'){
            $nuevo_ginecologica->parto_vaginal = false;
        }else{
            $nuevo_ginecologica->parto_vaginal = null;
        }
        if($request->observacionVaginal == ''){
            $nuevo_ginecologica->parto_vaginal_observacion = null;
        }else{
            $nuevo_ginecologica->parto_vaginal_observacion = $request->observacionVaginal;
        }
        
        if($request->forceps == 'si'){
            $nuevo_ginecologica->forceps = true;
        }elseif($request->forceps == 'no'){
            $nuevo_ginecologica->forceps = false;
        }else{
            $nuevo_ginecologica->forceps = null;
        }

        if($request->cesaria == 'si'){
            $nuevo_ginecologica->cesarias = true;
        }elseif($request->cesaria == 'no'){
            $nuevo_ginecologica->cesarias = false;
        }else{
            $nuevo_ginecologica->cesarias = null;
        }
        if($request->observacionCesaria == ''){
            $nuevo_ginecologica->cesarias_observacion = null;
        }else{
            $nuevo_ginecologica->cesarias_observacion = $request->observacionCesaria;
        }

        $nuevo_ginecologica->vivos_muertos = $request->vivosMuertos;
        $nuevo_ginecologica->fecha_ultimo_parto = $request->fechaUltimoParto;

        if($request->anticonceptivo == 'si'){
            $nuevo_ginecologica->metodo_anticonceptivo = true;
        }elseif($request->anticonceptivo == 'no'){
            $nuevo_ginecologica->metodo_anticonceptivo = false;
        }else{
            $nuevo_ginecologica->metodo_anticonceptivo = null;
        }
        if($request->fechaUltimoAnticonceptivo == ''){
            $nuevo_ginecologica->metodo_anticonceptivo_observacion = null;
        }else{
            $nuevo_ginecologica->metodo_anticonceptivo_observacion = $request->fechaUltimoAnticonceptivo;
        }

        if($request->menarquia == 'si'){
            $nuevo_ginecologica->menarquia = true;
        }elseif($request->menarquia == 'no'){
            $nuevo_ginecologica->menarquia = false;
        }else{
            $nuevo_ginecologica->menarquia = null;
        }
        if($request->observacionMenarquia == ''){
            $nuevo_ginecologica->menarquia_observacion = null;
        }else{
            $nuevo_ginecologica->menarquia_observacion = $request->observacionMenarquia;
        }

        $nuevo_ginecologica->ciclo_menstrual = $request->cicloMenstrual;


        if($request->menopausia == 'si'){
            $nuevo_ginecologica->menopausia = true;
        }elseif($request->menopausia == 'no'){
            $nuevo_ginecologica->menopausia = false;
        }else{
            $nuevo_ginecologica->menopausia = null;
        }

        $nuevo_ginecologica->pap = $request->pap;
        $nuevo_ginecologica->fur = $request->fur;

      
        $nuevo_ginecologica->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');

      

		return $nuevo_ginecologica;
	}

}

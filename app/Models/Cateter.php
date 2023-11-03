<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;
use Log;

class Cateter extends Model
{
    //
    protected $table = "formulario_cateteres";
	public $timestamps = false;
    protected $primaryKey = "id";

    public static function crearNuevo($request, $modificar, $cateters){

        $cateter = new Cateter;
        DB::beginTransaction();

            //foreach($request->cateteres as $key => $cateters){
            //if($request->fecha[$cateters] != ''){
            $cateter = new Cateter;
            $cateter->caso = strip_tags($request->idCaso);

            if($modificar != null || $modificar != ""){
                $cateter->id_anterior = $modificar->id;
            }
            $cateter->usuario_responsable = Auth::user()->id;
            $cateter->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $cateter->tipo_cateter = $cateters;
            if($request->numero[$cateters] != ''){
                $cateter->numero = $request->numero[$cateters] ;
            }else{
                $cateter->numero = null;
            }
            $cateter->fecha_instalacion = Carbon::parse($request->fecha[$cateters])->format("Y-m-d H:i:s") ;
            $cateter->lugar_instalacion = $request->lugar[$cateters] ;
            $cateter->responsable_instalcion = $request->responsableInst[$cateters] ;
            $cateter->material_fabricacion = $request->material[$cateters] ;
            if ($request->fechaCura[$cateters] != '') {
                $cateter->fecha_curacion = Carbon::parse($request->fechaCura[$cateters])->format("Y-m-d") ;
            }else{$cateter->fecha_curacion = null;}
                $cateter->responsable_curacioin = $request->responsableCura[$cateters] ;
                $cateter->observacion = $request->observacion[$cateters] ;
            if($cateters == '4'){
                if($request->tipoCvc!=''){
                    $cateter->tipo = $request->tipoCvc ;
                }else{
                    $cateter->tipo = null;
                }
                $cateter->via_instalacion = $request->viaCvc ;
            }
            if($cateters == '6'){
                $cateter->medicion_cuff = $request->cuffTraqueo ;
            }else{
                $cateter->medicion_cuff = null;
            }
            if($cateters == '7'){
                if ($request->tipoOsto!='') {
                    $cateter->tipo = $request->tipoOsto ;
                }else{
                    $cateter->tipo = null;
                }
                $boolean = $request->baguetaOsto ;
                if($boolean == 'no'){
                  $boolean = false;
                }else{
                  $boolean = true;
                }
                $cateter->cuidado_enfermeria = $request->cuidadoOsto ;
                $cateter->valoracion_estomaypiel = $request->valoracionEstomaOsto ;
                $cateter->responsable_curacion_ostomias = $request->cuidadoEstomaOsto ;
                $cateter->medicion_efluente = $request->medicionEfluenteOsto ;
                $cateter->detalle_educacion = $request->detalleEducacionOsto ;
                $cateter->bagueta = $boolean ;
            }

            $cateter->save();
            return $cateter;
        //}
    }

    public static function diferencia($id,$arrayForm){
        //Funcion para realizar comparacion entre areggloss
        $otros = Cateter::select(
                    "id",
                    "caso",
                    "numero",
                    "fecha_instalacion",
                    "lugar_instalacion",
                    "responsable_instalcion",
                    "material_fabricacion",
                    "fecha_curacion",
                    "responsable_curacioin",
                    "observacion",
                    "tipo",
                    "via_instalacion",
                    "medicion_cuff",
                    "cuidado_enfermeria",
                    "valoracion_estomaypiel",
                    "responsable_curacion_ostomias",
                    "medicion_efluente",
                    "detalle_educacion",
                    "bagueta",
                    "tipo_cateter")
                    ->where('id', $id)
                    ->where("visible",'true')
                    ->first();
        
        //Consulta para comparar el arreglo
        //dd($otros);
        //dd($arrayForm);
        $arrayFormLimpio = array_filter($arrayForm, function($v){ 
            return !is_null($v) && $v !== ''; 
        });
        $otrosLimpio = array_filter($otros->toArray(), function($v){ 
            return !is_null($v) && $v !== ''; 
           });
        $resultado = array_diff_assoc($arrayFormLimpio, $otrosLimpio);
        if(empty($resultado)){
            return 'nada';
        }else{
            return 'modificar';
        }

        //  return $resultado;

    }

}

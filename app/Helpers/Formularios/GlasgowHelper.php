<?php

namespace App\Helpers\Formularios;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\Glasgow;
use App\Models\IEGeneral;


class GlasgowHelper {


    public static function glasgowModificacion($request, $modificar, $tipo){

        //Falta que al comparar los valores estos sean diferentes 
        /* $glasgowEliminar = Nova::find($modificar->indglasgow);
        $arrayGlasgowAntiguo = '';
        if($glasgowEliminar != ''){ 
            $arrayGlasgowAntiguo = $glasgowEliminar->estado_mental.",".$glasgowEliminar->incontinencia.",".$glasgowEliminar->movilidad.",".$glasgowEliminar->nutricion_ingesta.",".$glasgowEliminar->actividad;                    
        } */

        $id_anterior = "";
        //Si tiene error es porque esta malo
        //Si tiene exito es porque se ejecuto correctamente
        //Si no tiene ninguno es porque se elimino correctamente
        $glasgow = [
            'error' => '',
            'exito' => ''     
        ];

        Log::info("Entro a glasgowHelper");
        if($request->arrayGlasgow != '' && count(explode(",", $request->arrayGlasgow)) == 3){
            $glasgowForm = explode(",", $request->arrayGlasgow);
            //valida si se envian datos que no corresponden
            $validarGlasgow = ["1" =>'1','2','3','4','5','6'];
            $existe = array_diff($glasgowForm, $validarGlasgow);
            if(count($existe) > 0){
                $glasgow['error'] =  "Error al ingresar los datos del examen físico general";   
                return $glasgow; 
            }
            
            if($modificar != null && $modificar->indglasgow){
                //Como aqui se ve solo el glasgow de ingreso solo se busca el ultimo de ingreso que esta en estado visible y es actualizado
                $glasgowAnterior = Glasgow::where('id_formulario_escala_glasgow',$modificar->indglasgow)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

                if (!$glasgowAnterior) {
                    //si no encontro el formulario glasgow, pero venia con un id, significaque ese formulario fue actualizado
                    $glasgow['error'] = "Este formulario ya ha sido modificado";
                    return $glasgow; 	
                }else{
                    //Si lo encontro, este debe falsearlo para quen osea viisble    
                    $glasgowAnterior->usuario_modifica = Auth::user()->id;
                    $glasgowAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $glasgowAnterior->visible = false;
                    $glasgowAnterior->save();

                    //Se almacena la variable de 
                    $id_anterior = $glasgowAnterior->id_formulario_escala_glasgow;
                }
                
            }

            if ($glasgow['error'] == '') {
                //Si no se encontro errores, se puede pasar a guardar el glasgow nuevo 
                $glasgowNuevo = IEGeneral::guardarGlasgow($request, $modificar,$tipo);

                if ($id_anterior != "") {
                    //Si se completo el id_anterior, se puede guardra como que fue un antiguo
                    $glasgowNuevo->id_anterior = $id_anterior;
                }
                $glasgowNuevo->visible = true;
                $glasgowNuevo->save();
                $glasgow['exito'] = $glasgowNuevo;
            }

            
        }else if ($modificar != null && $modificar->indglasgow) {
            //en caso de que el formulario venga vacio, se debe proceder a eliminar o dejar con visible false el ultimo glasgow de ingreso
            $glasgowEliminar = Glasgow::where('caso',$request->idCaso)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

            if($glasgowEliminar){
                $glasgowEliminar->usuario_modifica = Auth::user()->id;
                $glasgowEliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $glasgowEliminar->visible = false;
                $glasgowEliminar->save();
            }

        }

        return $glasgow;
    }


    


}
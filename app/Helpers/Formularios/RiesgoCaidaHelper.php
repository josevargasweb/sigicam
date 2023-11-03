<?php

namespace App\Helpers\Formularios;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\HojaEnfermeriaRiesgoCaida;
use App\Models\IEGeneral;


class RiesgoCaidaHelper {


    public static function riesgoCaidaModificacion($request, $modificar, $tipo){

        //Falta que al comparar los valores estos sean diferentes 
        /* $riesgoEliminar = Nova::find($modificar->indriesgo);
        $arrayNovaAntiguo = '';
        if($riesgoEliminar != ''){ 
            $arrayNovaAntiguo = $riesgoEliminar->estado_mental.",".$riesgoEliminar->incontinencia.",".$riesgoEliminar->movilidad.",".$riesgoEliminar->nutricion_ingesta.",".$riesgoEliminar->actividad;                    
        } */

        $id_anterior = "";
        //Si tiene error es porque esta malo
        //Si tiene exito es porque se ejecuto correctamente
        //Si no tiene ninguno es porque se elimino correctamente
        $riesgo = [
            'error' => '',
            'exito' => ''     
        ];

        Log::info("Entro a riesgoHelper");
        if($request->arrayRiesgoCaida != '' && count(explode(",", $request->arrayRiesgoCaida)) == 5 || $request->arrayRiesgoCaida != '' && count(explode(",", $request->arrayRiesgoCaida)) == 4 &&  $request->arrayRiesgoCaidaMedicamento != ''){
            $riesgoForm = explode(",", $request->arrayRiesgoCaida);
            //valida si se envian datos que no corresponden
            $validarRiesgo = ["1" =>'0','1','2','3','4','5','6'];
            $existe = array_diff($riesgoForm, $validarRiesgo);
            if(count($existe) > 0){
                $riesgo['error'] =  "Error al ingresar los datos del examen físico general";   
                return $riesgo; 
            }

            if($request->arrayRiesgoCaidaMedicamento != ''){
                $medicamentoForm = explode(",", $request->arrayRiesgoCaidaMedicamento);
                //valida si se envian datos que no corresponden
                $validarMedicamento = ["1" =>'0','1','2','3','4','5','6'];
                $existe = array_diff($medicamentoForm, $validarMedicamento);
                if(count($existe) > 0){
                    $riesgo['error'] =  "Error al ingresar los datos del examen físico general";   
                    return $riesgo; 
                }
            }
            
            if($modificar != null && $modificar->indriesgo){
                //Como aqui se ve solo el riesgo caida de ingreso solo se busca el ultimo de ingreso que esta en estado visible y es actualizado
                $riesgoAnterior = HojaEnfermeriaRiesgoCaida::where('id',$modificar->indriesgo)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

                if (!$riesgoAnterior) {
                    //si no encontro el formulario riesgo caida, pero venia con un id, significaque ese formulario fue actualizado
                    $riesgo['error'] = "Este formulario ya ha sido modificado";
                    return $riesgo; 	
                }else{
                    //Si lo encontro, este debe falsearlo para quen osea viisble    
                    $riesgoAnterior->usuario_modifica = Auth::user()->id;
                    $riesgoAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $riesgoAnterior->visible = false;
                    $riesgoAnterior->save();

                    //Se almacena la variable de 
                    $id_anterior = $riesgoAnterior->id;
                }
                
            }

            if ($riesgo['error'] == '') {
                //Si no se encontro errores, se puede pasar a guardar el riesgo caida nuevo 
                $riesgoNuevo = IEGeneral::guardarRiesgo($request, $modificar,$tipo);

                if ($id_anterior != "") {
                    //Si se completo el id_anterior, se puede guardra como que fue un antiguo
                    $riesgoNuevo->id_anterior = $id_anterior;
                }
                $riesgoNuevo->visible = true;
                $riesgoNuevo->save();
                
                $riesgo['exito'] = $riesgoNuevo;
            }

            
        }else if ($modificar != null && $modificar->indriesgo) {
            //en caso de que el formulario venga vacio, se debe proceder a eliminar o dejar con visible false el ultimo riesgo caida de ingreso
            $riesgoEliminar = HojaEnfermeriaRiesgoCaida::where('caso',$request->idCaso)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

            if($riesgoEliminar){
                $riesgoEliminar->usuario_modifica = Auth::user()->id;
                $riesgoEliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $riesgoEliminar->visible = false;
                $riesgoEliminar->save();
            }

        }

        return $riesgo;
    }


    


}
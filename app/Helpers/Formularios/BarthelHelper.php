<?php

namespace App\Helpers\Formularios;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\Barthel;
use App\Models\IEGeneral;


class BarthelHelper {


    public static function barthelModificacion($request, $modificar, $tipo){

        //Falta que al comparar los valores estos sean diferentes 
        /* $barthelEliminar = Nova::find($modificar->indbarthel);
        $arrayBarthelAntiguo = '';
        if($barthelEliminar != ''){ 
            $arrayBarthelAntiguo = $barthelEliminar->estado_mental.",".$barthelEliminar->incontinencia.",".$barthelEliminar->movilidad.",".$barthelEliminar->nutricion_ingesta.",".$barthelEliminar->actividad;                    
        } */

        $id_anterior = "";
        //Si tiene error es porque esta malo
        //Si tiene exito es porque se ejecuto correctamente
        //Si no tiene ninguno es porque se elimino correctamente
        $barthel = [
            'error' => '',
            'exito' => ''     
        ];

        Log::info("Entro a barthelHelper");
        if($request->arrayBarthel != '' && count(explode(",", $request->arrayBarthel)) == 10){
            $barthelForm = explode(",", $request->arrayBarthel);
            //valida si se envian datos que no corresponden
            $validarBarthel = ["1" =>'0','5','10','15'];
            $existe = array_diff($barthelForm, $validarBarthel);
            if(count($existe) > 0){
                $barthel['error'] =  "Error al ingresar los datos del examen físico general";   
                return $barthel; 
            }
            
            if($modificar != null && $modificar->indbarthel){
                //Como aqui se ve solo el barthel de ingreso solo se busca el ultimo de ingreso que esta en estado visible y es actualizado
                $barthelAnterior = Barthel::where('id_formulario_barthel',$modificar->indbarthel)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

                if (!$barthelAnterior) {
                    //si no encontro el formulario Barthel, pero venia con un id, significaque ese formulario fue actualizado
                    $barthel['error'] = "Este formulario ya ha sido modificado";
                    return $barthel; 	
                }else{
                    //Si lo encontro, este debe falsearlo para quen osea viisble    
                    $barthelAnterior->usuario_modifica = Auth::user()->id;
                    $barthelAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $barthelAnterior->visible = false;
                    $barthelAnterior->save();

                    //Se almacena la variable de 
                    $id_anterior = $barthelAnterior->id_formulario_barthel;
                }
                
            }

            if ($barthel['error'] == '') {
                //Si no se encontro errores, se puede pasar a guardar el Barthel nuevo 
                $barthelNuevo = IEGeneral::guardarBarthel($request, $modificar,$tipo);

                if ($id_anterior != "") {
                    //Si se completo el id_anterior, se puede guardra como que fue un antiguo
                    $barthelNuevo->id_anterior = $id_anterior;
                }
                $barthelNuevo->visible = true;
                $barthelNuevo->save();
                $barthel['exito'] = $barthelNuevo;
            }

            
        }else if ($modificar != null && $modificar->indbarthel) {
            //en caso de que el formulario venga vacio, se debe proceder a eliminar o dejar con visible false el ultimo barthel de ingreso
            $barthelEliminar = Barthel::where('caso',$request->idCaso)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

            if($barthelEliminar){
                $barthelEliminar->usuario_modifica = Auth::user()->id;
                $barthelEliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $barthelEliminar->visible = false;
                $barthelEliminar->save();
            }

        }

        return $barthel;
    }


    


}
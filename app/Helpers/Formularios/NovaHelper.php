<?php

namespace App\Helpers\Formularios;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\Nova;
use App\Models\IEGeneral;


class NovaHelper {


    public static function novaModificacion($request, $modificar, $tipo){

        //Falta que al comparar los valores estos sean diferentes 
        /* $novaEliminar = Nova::find($modificar->indnova);
        $arrayNovaAntiguo = '';
        if($novaEliminar != ''){ 
            $arrayNovaAntiguo = $novaEliminar->estado_mental.",".$novaEliminar->incontinencia.",".$novaEliminar->movilidad.",".$novaEliminar->nutricion_ingesta.",".$novaEliminar->actividad;                    
        } */

        $id_anterior = "";
        //Si tiene error es porque esta malo
        //Si tiene exito es porque se ejecuto correctamente
        //Si no tiene ninguno es porque se elimino correctamente
        $nova = [
            'error' => '',
            'exito' => ''     
        ];

        Log::info("Entro a novaHelper");
        if($request->arrayNova != '' && count(explode(",", $request->arrayNova)) == 5){
            $novaForm = explode(",", $request->arrayNova);
            //valida si se envian datos que no corresponden
            $validarNova = ["1" =>'0','1','2','3'];
            $existe = array_diff($novaForm, $validarNova);
            if(count($existe) > 0){
                $nova['error'] =  "Error al ingresar los datos del examen físico general";   
                return $nova; 
            }
            
            if($modificar != null && $modificar->indnova){
                //Como aqui se ve solo el nova de ingreso solo se busca el ultimo de ingreso que esta en estado visible y es actualizado
                $novaAnterior = Nova::where('id_formulario_escala_nova',$modificar->indnova)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

                if (!$novaAnterior) {
                    //si no encontro el formulario nova, pero venia con un id, significaque ese formulario fue actualizado
                    $nova['error'] = "Este formulario ya ha sido modificado";
                    return $nova; 	
                }else{
                    //Si lo encontro, este debe falsearlo para quen osea viisble    
                    $novaAnterior->usuario_modifica = Auth::user()->id;
                    $novaAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $novaAnterior->visible = false;
                    $novaAnterior->save();

                    //Se almacena la variable de 
                    $id_anterior = $novaAnterior->id_formulario_escala_nova;
                }
                
            }

            if ($nova['error'] == '') {
                //Si no se encontro errores, se puede pasar a guardar el nova nuevo 
                $novaNuevo = IEGeneral::guardarNova($request, $modificar,$tipo);

                if ($id_anterior != "") {
                    //Si se completo el id_anterior, se puede guardra como que fue un antiguo
                    $novaNuevo->id_anterior = $id_anterior;
                }
                $novaNuevo->visible = true;
                $novaNuevo->save();
                $nova['exito'] = $novaNuevo;
            }

            
        }else if ($modificar != null && $modificar->indnova) {
            //en caso de que el formulario venga vacio, se debe proceder a eliminar o dejar con visible false el ultimo nova de ingreso
            $novaEliminar = Nova::where('caso',$request->idCaso)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

            if($novaEliminar){
                $novaEliminar->usuario_modifica = Auth::user()->id;
                $novaEliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $novaEliminar->visible = false;
                $novaEliminar->save();
            }

        }

        return $nova;
    }


    


}
<?php

namespace App\Helpers\Formularios;

use Exception;
use DB;
use Auth;
use Log;
use Carbon\Carbon;

use App\Models\RiesgoUlceras;
use App\Models\IEGeneral;


class UlceraHelper {


    public static function riesgoUlceraModificacion($request, $modificar, $tipo){

        //Falta que al comparar los valores estos sean diferentes 
        /* $riesgoUlceraEliminar = RiesgoUlceras::find($modificar->indulcera);
        $arrayRiesgoUlcerasAntiguo = '';
        if($riesgoUlceraEliminar != ''){ 
            $arrayRiesgoUlcerasAntiguo = $riesgoUlceraEliminar->estado_mental.",".$riesgoUlceraEliminar->incontinencia.",".$riesgoUlceraEliminar->movilidad.",".$riesgoUlceraEliminar->nutricion_ingesta.",".$riesgoUlceraEliminar->actividad;                    
        } */

        $id_anterior = "";
        //Si tiene error es porque esta malo
        //Si tiene exito es porque se ejecuto correctamente
        //Si no tiene ninguno es porque se elimino correctamente
        $riesgoUlcera = [
            'error' => '',
            'exito' => ''     
        ];

        Log::info("Entro a riesgoUlceraHelper");
        if($request->arrayRiesgoUlceras != '' && count(explode(",", $request->arrayRiesgoUlceras)) == 5){
            $riesgoUlceraForm = explode(",", $request->arrayRiesgoUlceras);
            //valida si se envian datos que no corresponden
            $validarRiesgoUlceras = ["1" =>'1','2','3','4'];
            $existe = array_diff($riesgoUlceraForm, $validarRiesgoUlceras);
            if(count($existe) > 0){
                $riesgoUlcera['error'] =  "Error al ingresar los datos del examen físico general";   
                return $riesgoUlcera; 
            }
            
            if($modificar != null && $modificar->indulcera){
                //Como aqui se ve solo el nova de ingreso solo se busca el ultimo de ingreso que esta en estado visible y es actualizado
                $riesgoUlceraAnterior = RiesgoUlceras::where('id_formulario_escala_nova',$modificar->indulcera)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

                if (!$riesgoUlceraAnterior) {
                    //si no encontro el formulario nova, pero venia con un id, significaque ese formulario fue actualizado
                    $riesgoUlcera['error'] = "Este formulario ya ha sido modificado";
                    return $riesgoUlcera; 	
                }else{
                    //Si lo encontro, este debe falsearlo para quen osea viisble    
                    $riesgoUlceraAnterior->usuario_modifica = Auth::user()->id;
                    $riesgoUlceraAnterior->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                    $riesgoUlceraAnterior->visible = false;
                    $riesgoUlceraAnterior->save();

                    //Se almacena la variable de 
                    $id_anterior = $riesgoUlceraAnterior->id_formulario_escala_nova;
                }
                
            }

            if ($riesgoUlcera['error'] == '') {
                //Si no se encontro errores, se puede pasar a guardar el nova nuevo 
                $riesgoUlceraNuevo = IEGeneral::guardarRiesgoUlceras($request, $modificar,$tipo);

                if ($id_anterior != "") {
                    //Si se completo el id_anterior, se puede guardra como que fue un antiguo
                    $riesgoUlceraNuevo->id_anterior = $id_anterior;
                }
                $riesgoUlceraNuevo->visible = true;
                $riesgoUlceraNuevo->save();
                $riesgoUlcera['exito'] = $riesgoUlceraNuevo;
            }

            
        }else if ($modificar != null && $modificar->indulcera) {
            //en caso de que el formulario venga vacio, se debe proceder a eliminar o dejar con visible false el ultimo nova de ingreso
            $riesgoUlceraEliminar = RiesgoUlceras::where('caso',$request->idCaso)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();

            if($riesgoUlceraEliminar){
                $riesgoUlceraEliminar->usuario_modifica = Auth::user()->id;
                $riesgoUlceraEliminar->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $riesgoUlceraEliminar->visible = false;
                $riesgoUlceraEliminar->save();
            }

        }

        return $riesgoUlcera;
    }


    


}
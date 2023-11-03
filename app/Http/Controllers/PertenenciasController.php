<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Models\Pertenencias;

use Auth;
use Log;
use DB;
use View;
use Form;
use Exception;
use Session;

class PertenenciasController extends Controller{

    public function obtenerPertenencias($caso){

        $pertenencias = Pertenencias::where('caso',$caso)->where('visible', true)->get();
        $resultado = [];
        foreach ($pertenencias as $key => $pertenencia) {
            $pert = ($pertenencia->pertenencia) ? $pertenencia->pertenencia : "";
            $pert_paciente = "<div class='form-group'>
                <div class='col-md-12'>
                <input class='form-control' id='pertenencia".$key."' name='pertenenciaE' type='text' value='".$pert."' onKeyup='validarPertenencia(".$key.")'><span style='color:#a94442' id='errorPertenencia".$key."'></span>
                </div>
                </div>";

            $fecha = ($pertenencia->fecha_creacion) ? Carbon::parse($pertenencia->fecha_creacion)->format('d-m-Y H:i') : "";
            $fecha_creacion = "<div class='form-group'><div class='col-md-12 pl-0 pr-0'><input class='dPpertenenciaE form-control' id='fecha_creacion".$key."' name='fecha_creacionE".$key."' type='text' value='".$fecha."' onkeyup='validarFechaCreacion(".$key.")'><span style='color:#a94442' id='errorFechaCreacion".$key."'></span></div></div>";
            
            $fecha2 = ($pertenencia->fecha_recepcion) ? Carbon::parse($pertenencia->fecha_recepcion)->format('d-m-Y H:i') : "";
            $fecha_recepcion = "<div class='form-group'><div class='col-md-12 pl-0 pr-0'><input class='dPpertenenciaR form-control' id='fecha_recepcion".$key."' name='fecha_recepcionE".$key."' type='text' value='".$fecha2."' onkeyup='validarFechaRecepcion(".$key.")'><span style='color:#a94442' id='errorFechaRecepcion".$key."'></span></div></div>";

            $respo = ($pertenencia->responsable) ? $pertenencia->responsable : "";
            $responsable = "<div class='form-group'><div class='col-md-12' ><input class='form-control' id='responsable".$key."' name='responsableE' type='text' value='".$respo."' onKeyup='validarResponsable(".$key.")'><span style='color:#a94442' id='errorResponsable".$key."'></span></div></div>";
           
            $entr = ($pertenencia->persona) ? $pertenencia->persona : "";
            $entrega = "<div class='form-group'><div class='col-md-12'><input class='form-control' id='entrega".$key."' name='entregaE' type='text' value='".$entr."' onKeyup='validarEntrega(".$key.")'><span style='color:#a94442' id='errorEntrega".$key."'></span></div></div>";

            $resultado [] = [
                $pert_paciente,
                $fecha_recepcion,
                $responsable,
                $fecha_creacion,
                $entrega,
                "<div class='row'>
                    <div class='col-md-5'>
                        <button type='button' class='btn-xs btn-warning' onclick='modificarPertenencia(".$pertenencia->id.",".$key.")'>Modificar</button>
                    </div>
                    <div class='col-md-5'>
                        <button type='button' class='btn-xs btn-danger' onclick='eliminarPertenencia(".$pertenencia->id.")'>Eliminar</button>
                    </div>
                </div>"
            ];
        }

        return response()->json(["aaData" => $resultado]);
        //return $pertenencias;
    }

    public function modificarPertenencia(Request $request){

        //para validar los datos que vienen del editar
        $validador = Validator::make($request->all(), [
            'pertenencia' => 'required',
            'fecha_creacion' => 'required',
            'fecha_recepcion' => 'required',
            'responsable' => 'required',
            'entrega' => 'required'
        ],[
            'pertenencia.required' => 'Debe ingresar la pertenencia',
            'fecha_creacion.required' => 'Debe ingresar una fecha de entrega',
            'fecha_recepcion.required' => 'Debe ingresar una fecha de recepción',
            'responsable.required' => 'Debe ingresar el responsable',
            'entrega.required' => 'Debe ingresar a quien le entrega',
        ]);


        //si falta algun dato
        if ($validador->fails()){   
            //retorna los errores
            return response()->json(['errores'=>$validador->errors()->all()]);
        }

        //si nada falla, entra al flujo normal
        if ($validador->passes()) {
            try {
                DB::beginTransaction();
    
                $modificar = Pertenencias::find($request->id);
                $modificar->usuario_modifica = Auth::user()->id;
                $modificar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                $modificar->visible = false;
                $modificar->tipo_modificacion = 'Editado';
                $modificar->save();
    
                $nuevoRegistro = new Pertenencias;
                $nuevoRegistro->caso = $modificar->caso;
                $nuevoRegistro->usuario = Auth::user()->id;
                $nuevoRegistro->visible = true;
                $nuevoRegistro->pertenencia = $request->pertenencia;
                $nuevoRegistro->fecha_creacion = $request->fecha_creacion;
                $nuevoRegistro->fecha_recepcion = $request->fecha_recepcion;
                $nuevoRegistro->responsable = $request->responsable;
                $nuevoRegistro->persona = $request->entrega;
                $nuevoRegistro->id_anterior = $modificar->id;
                $nuevoRegistro->save();
    
                DB::commit();
                return response()->json(["exito" => "Se ha modificado la pertenencia exitosamente"]);
            } catch (Exception $e) {
                Log::info($e);
                DB::rollback();
                return response()->json(["error" => "Error al modificar la pertenencia"]);
            }
        }
    }

    public function eliminarPertenencia(Request $request){
        try {
            DB::beginTransaction();
            
            $eliminar = Pertenencias::find($request->id);
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado la pertenencia exitosamente"]);
        } catch(Exception $e){
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al eliminar la pertenencia"]);
        }
    }

    public function agregarPertenencia(Request $request){
        try {
            DB::beginTransaction();

            $nuevoRegistro = new Pertenencias;
            $nuevoRegistro->caso = $request->caso;
            $nuevoRegistro->usuario = Auth::user()->id;
            $nuevoRegistro->visible = true;
            $nuevoRegistro->pertenencia = $request->pertenencia;
           
            $nuevoRegistro->fecha_recepcion = Carbon::parse(strip_tags($request->fecha_recepcion));
            $nuevoRegistro->responsable = $request->responsable;
            $nuevoRegistro->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado la pertenencia exitosamente"]);
        } catch (Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la pertenencia"]);
        }
            
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use Auth;
use Log;
use DB;
use View;
use Form;
use Exception;
use Session;

use App\Models\EgresoRecienNacidoGineco;
use App\Models\OtrosDiagnosticosErn;
use App\Models\CuidadosAltaErn;

class EgresosRecienNacidoGinecoController extends Controller{

    public function obtenerEgresosRn(Request $request){
        
        $buscar = $request->buscar;
        $egresos = [];
        if($buscar == ''){
            $egresos_recien_nacidos = EgresoRecienNacidoGineco::where('caso',$request->caso)
            ->where('visible',true)
            ->orderBy('id','Desc')
            ->paginate(5);
        }else{
            $egresos_recien_nacidos = EgresoRecienNacidoGineco::where('caso',$request->caso)
            ->where('visible',true)
            ->where(function($q) use ($buscar) {
                $q->where('run','ilike','%'. $buscar . '%')
                ->orWhere('nombre','ilike','%'. $buscar . '%')
                ->orWhere('paterno','ilike','%'. $buscar . '%')
                ->orWhere('materno','ilike','%'. $buscar . '%')
                ->orWhere('destino','ilike','%'. $buscar . '%');
            })
            ->orderBy('id','Desc')
            ->paginate(5);
        }
        
        foreach ($egresos_recien_nacidos as $ern) {
            $otros_diagnosticos = OtrosDiagnosticosErn::where('caso',$request->caso)->where('erng_id',$ern->id)->get();
            $cuidados_alta = CuidadosAltaErn::where('caso',$request->caso)->where('erng_id',$ern->id)->get();

            $egresos[] = [
                "id" => $ern->id,
                "run" => $ern->run,
                "dv" => $ern->dv,
                "ficha" => $ern->ficha,
                "nombre" => $ern->nombre,
                "paterno" => $ern->paterno,
                "materno" => $ern->materno,
                "cuidador" => $ern->cuidador,
                "vinculo" => $ern->vinculo,
                "telefono_cuidador" => $ern->telefono_cuidador,
                "diagnostico_medico" => $ern->diagnostico_medico,
                "fecha_egreso" => $ern->fecha_egreso,
                "destino" => $ern->destino,
                "fecha_cesfam" => $ern->fecha_cesfam,
                "otros_diagnosticos" => $otros_diagnosticos,
                "cuidados_alta" => $cuidados_alta
            ];
        }
        
        return [
            'pagination' => [
                'total' => $egresos_recien_nacidos->total(),
                'current_page' => $egresos_recien_nacidos->currentPage(),
                'per_page' => $egresos_recien_nacidos->perPage(),
                'last_page' => $egresos_recien_nacidos->lastPage(),
                'from' => $egresos_recien_nacidos->firstItem(),
                'to' => $egresos_recien_nacidos->lastItem(),
            ],
            'egresos' => $egresos];
    }

    public function egresarRecienNacido(Request $request){
        try {
            $validador = Validator::make($request->all(), [
                'fecha_egreso' => 'required',
                'fecha_cesfam' => 'required'
            ],[
                'fecha_egreso.required' => 'Debe ingresar la fecha de egreso',
                'fecha_cesfam.required' => 'Debe ingresar la fecha de cesfam'
            ]);
    
    
            //si falta algun dato
            if ($validador->fails()){   
                //retorna los errores
                return response()->json(['errores'=>$validador->errors()->all()]);
            }
            
            DB::beginTransaction();
            $erng = new EgresoRecienNacidoGineco;
            $erng->caso = $request->caso;
            $erng->run = ($request->run) ? $request->run : null;
            $dv = ($request->dv == 'K' || $request->dv == 'k') ? 10 : $request->dv;
            $erng->dv = ($dv) ? $dv : null;
            $erng->ficha = strip_tags($request->ficha);
            $erng->nombre = strip_tags($request->nombre);
            $erng->paterno = strip_tags($request->paterno);
            $erng->materno = strip_tags($request->materno);
            $erng->cuidador = strip_tags($request->cuidador);
            $erng->vinculo = strip_tags($request->vinculo);
            $erng->telefono_cuidador = strip_tags($request->telefono_cuidador);
            $erng->diagnostico_medico = strip_tags($request->diagnostico_medico);
            $erng->fecha_egreso = $request->fecha_egreso;
            $erng->destino = $request->destino;
            $erng->fecha_cesfam = $request->fecha_cesfam;
            $erng->usuario_ingresa = Auth::user()->id;
            $erng->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
            $erng->visible = true;
            $erng->save();

            //otros_diagnosticos
            $otros_diagnosticos = $request->otros_diagnosticos;
            if($otros_diagnosticos){
                foreach ($otros_diagnosticos as $otro_diag) {
                    if($otro_diag){
                        $od = new OtrosDiagnosticosErn;
                        $od->caso = $request->caso;
                        $od->erng_id = $erng->id;
                        $od->usuario_ingresa = Auth::user()->id;
                        $od->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                        $od->visible = true;
                        $od->diagnostico = $otro_diag["diagnostico"];
                        $od->save();
                    }else{
                        throw new Exception("No debe ingresar otros diagnosticos vacíos.");
                    }
                }
            }
            
            //cuidados_alta
            $cuidados_alta = $request->cuidados_alta;
            if($cuidados_alta){
                foreach ($cuidados_alta as $cuidado) {
                    if($cuidado){
                        $od = new CuidadosAltaErn;
                        $od->caso = $request->caso;
                        $od->erng_id = $erng->id;
                        $od->usuario_ingresa = Auth::user()->id;
                        $od->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                        $od->visible = true;
                        $od->cuidado = $cuidado["cuidado"];
                        $od->save();
                    }else{
                        throw new Exception("No debe ingresar cuidados alta vacíos.");
                    }
                }
            }

            DB::commit();
            return response()->json(["exito" => "Registro realizado exitosamente."]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            $errores_controlados = [
                "No debe ingresar otros diagnosticos vacíos.",
                "No debe ingresar cuidados alta vacíos."
            ];
            $error = "No se ha podido realizar el registro.";
            if(in_array($ex->getMessage(), $errores_controlados)){
                $error = $ex->getMessage();
            } 
            return response()->json(["error" => $error]);
        }
    }

    public function eliminarRecienNacido(Request $request){
        try {
            DB::beginTransaction();
            $eliminar = EgresoRecienNacidoGineco::findOrFail($request->id);
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Registro eliminado exitosamente."]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "No se ha podido eliminar el registro.", "msg" => $ex]);
        }

    }

    public function actualizarRecienNacido(Request $request){
        try {
            DB::beginTransaction();
            $actualizar = EgresoRecienNacidoGineco::findOrFail($request->id);

            $validador = Validator::make($request->all(), [
                'fecha_egreso' => 'required',
                'fecha_cesfam' => 'required'
            ],[
                'fecha_egreso.required' => 'Debe ingresar la fecha de egreso',
                'fecha_cesfam.required' => 'Debe ingresar la fecha de cesfam'
            ]);
    
    
            //si falta algun dato
            if ($validador->fails()){   
                //retorna los errores
                return response()->json(['errores'=>$validador->errors()->all()]);
            }

            //falsear el actual.
            $actualizar->visible = false;
            $actualizar->estado = "Editado";
            $actualizar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
            $actualizar->usuario_modifica = Auth::user()->id;
            $actualizar->save();

            //registrar el nuevo
            $nuevo = new EgresoRecienNacidoGineco;
            $nuevo->caso = $actualizar->caso;
            $nuevo->run = ($request->run) ? $request->run : null;
            $dv = ($request->dv == 'K' || $request->dv == 'k') ? 10 : $request->dv;
            $nuevo->dv = ($dv) ? $dv : null;
            $nuevo->ficha = ($request->ficha) ? strip_tags($request->ficha) : null;
            $nuevo->nombre = ($request->nombre) ? strip_tags($request->nombre) : null;
            $nuevo->paterno = ($request->paterno) ? strip_tags($request->paterno) : null;
            $nuevo->materno = ($request->materno) ? strip_tags($request->materno) : null;
            $nuevo->cuidador = ($request->cuidador) ? strip_tags($request->cuidador) : null;
            $nuevo->vinculo = ($request->vinculo) ? strip_tags($request->vinculo) : null;
            $nuevo->telefono_cuidador = ($request->telefono_cuidador) ? strip_tags($request->telefono_cuidador) : null;
            $nuevo->diagnostico_medico = ($request->diagnostico_medico) ? strip_tags($request->diagnostico_medico) : null;
            $nuevo->fecha_egreso = ($request->fecha_egreso) ? $request->fecha_egreso : null;
            $nuevo->destino = ($request->destino) ? $request->destino : null;
            $nuevo->fecha_cesfam = $request->fecha_cesfam;
            $nuevo->usuario_ingresa = Auth::user()->id;
            $nuevo->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
            $nuevo->visible = true;
            $nuevo->id_anterior = $actualizar->id;
            $nuevo->save();

            //otros diagnosticos
            $otros_diagnosticos_vista = $request->otros_diagnosticos;
            $otros_diagnosticos_bd = OtrosDiagnosticosErn::where('caso',$request->caso)->where('erng_id',$actualizar->id)->where('visible', true)->get();
            foreach ($otros_diagnosticos_bd as $key => $od_bd) {
                // falsear los registros actuales.
                $od_bd->visible = false;
                $od_bd->usuario_modifica = Auth::user()->id;
                $od_bd->estado = "Editado";
                $od_bd->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                $od_bd->save();

                //crear copias de los registros anteriores.
                $od_nuevo = new OtrosDiagnosticosErn;
                $od_nuevo->erng_id = $nuevo->id;
                $od_nuevo->usuario_ingresa = $od_bd->usuario_ingresa;
                $od_nuevo->fecha_creacion = $od_bd->fecha_creacion;
                $od_nuevo->visible = true;
                $od_nuevo->diagnostico = $od_bd->diagnostico;
                $od_nuevo->save(); 
            }

            $nuevos_otros_diagnosticos_bd = OtrosDiagnosticosErn::where('caso',$request->caso)->where('erng_id',$nuevo->id)->where('visible', true)->get();

            $od_agregados_serialize = array_diff(array_map('serialize',$otros_diagnosticos_vista), array_map('serialize',$nuevos_otros_diagnosticos_bd->toArray()));
            $od_agregados_unserialize = array_map('unserialize', $od_agregados_serialize);

            $od_quitados_serialize = array_diff(array_map('serialize',$nuevos_otros_diagnosticos_bd->toArray()), array_map('serialize',$otros_diagnosticos_vista));
            $od_quitados_unserialize = array_map('unserialize', $od_quitados_serialize);

            if($od_agregados_unserialize || $od_quitados_unserialize){
                if($od_agregados_unserialize){
                    foreach ($od_agregados_unserialize as $key => $od_agregado) {
                        if(isset($od_agregado["id"])){
                            $editar = OtrosDiagnosticosErn::findOrFail($od_agregado["id"]);
                            $editar->visible = false;
                            $editar->usuario_modifica = Auth::user()->id;
                            $editar->estado = "Editado";
                            $editar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                            $editar->save();

                            $nuevo_od = new OtrosDiagnosticosErn;
                            $nuevo_od->caso = $request->caso;
                            $nuevo_od->erng_id = $nuevo->id;
                            $nuevo_od->usuario_ingresa = Auth::user()->id;
                            $nuevo_od->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                            $nuevo_od->visible = true;
                            $nuevo_od->diagnostico = $od_agregado["diagnostico"];
                            $nuevo_od->save(); 
                        }else{
                            if(!empty($od_agregado)){
                                $nuevo_od = new OtrosDiagnosticosErn;
                                $nuevo_od->caso = $request->caso;
                                $nuevo_od->erng_id = $nuevo->id;
                                $nuevo_od->usuario_ingresa = Auth::user()->id;
                                $nuevo_od->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                                $nuevo_od->visible = true;
                                $nuevo_od->diagnostico = $od_agregado["diagnostico"];
                                $nuevo_od->save();
                            }else{
                                throw new Exception("No debe ingresar otros diagnosticos vacíos.");
                            }
                        }
                    }
                }

                if($od_quitados_unserialize){
                    foreach ($od_quitados_unserialize as $key => $od_quitado) {
                        $quitar_od = OtrosDiagnosticosErn::findOrFail($od_quitado["id"]);
                        $quitar_od->visible = false;
                        $quitar_od->estado = "Eliminado";
                        $quitar_od->save();
                    }
                }
            }

            // cuidados_alta
            $cuidados_alta_vista = $request->cuidados_alta;
            $cuidados_alta_bd = CuidadosAltaErn::where('caso',$request->caso)->where('erng_id',$actualizar->id)->where('visible', true)->get();
            foreach ($cuidados_alta_bd as $key => $ca_bd) {
                // falsear los registros actuales.
                $ca_bd->visible = false;
                $ca_bd->usuario_modifica = Auth::user()->id;
                $ca_bd->estado = "Editado";
                $ca_bd->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                $ca_bd->save();

                //crear copias de los registros anteriores.
                $ca_nuevo = new CuidadosAltaErn;
                $ca_nuevo->erng_id = $nuevo->id;
                $ca_nuevo->usuario_ingresa = $ca_bd->usuario_ingresa;
                $ca_nuevo->fecha_creacion = $ca_bd->fecha_creacion;
                $ca_nuevo->visible = true;
                $ca_nuevo->cuidado = $ca_bd->cuidado;
                $ca_nuevo->save(); 
            }

            $nuevos_cuidados_alta_bd = CuidadosAltaErn::where('caso',$request->caso)->where('erng_id',$nuevo->id)->where('visible', true)->get();

            $ca_agregados_serialize = array_diff(array_map('serialize',$cuidados_alta_vista), array_map('serialize',$nuevos_cuidados_alta_bd->toArray()));
            $ca_agregados_unserialize = array_map('unserialize', $ca_agregados_serialize);

            $ca_quitados_serialize = array_diff(array_map('serialize',$nuevos_cuidados_alta_bd->toArray()), array_map('serialize',$cuidados_alta_vista));
            $ca_quitados_unserialize = array_map('unserialize', $ca_quitados_serialize);

            if($ca_agregados_unserialize || $ca_quitados_unserialize){
                if($ca_agregados_unserialize){
                    foreach ($ca_agregados_unserialize as $key => $ca_agregado) {
                        if(isset($ca_agregado["id"])){
                            $editar = CuidadosAltaErn::findOrFail($ca_agregado["id"]);
                            $editar->visible = false;
                            $editar->usuario_modifica = Auth::user()->id;
                            $editar->estado = "Editado";
                            $editar->fecha_modificacion = Carbon::now()->format('Y-m-d H:i:s');
                            $editar->save();

                            $nuevo_ca = new CuidadosAltaErn;
                            $nuevo_ca->caso = $request->caso;
                            $nuevo_ca->erng_id = $nuevo->id;
                            $nuevo_ca->usuario_ingresa = Auth::user()->id;
                            $nuevo_ca->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                            $nuevo_ca->visible = true;
                            $nuevo_ca->cuidado = $ca_agregado["cuidado"];
                            $nuevo_ca->save(); 
                        }else{
                            if(!empty($ca_agregado)){
                                $nuevo_ca = new CuidadosAltaErn;
                                $nuevo_ca->caso = $request->caso;
                                $nuevo_ca->erng_id = $nuevo->id;
                                $nuevo_ca->usuario_ingresa = Auth::user()->id;
                                $nuevo_ca->fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
                                $nuevo_ca->visible = true;
                                $nuevo_ca->cuidado = $ca_agregado["cuidado"];
                                $nuevo_ca->save();
                            }else{
                                throw new Exception("No debe ingresar cuidados alta vacíos.");
                            }
                        }
                    }
                }

                if($ca_quitados_unserialize){
                    foreach ($ca_quitados_unserialize as $key => $ca_quitado) {
                        $quitar_ca = CuidadosAltaErn::findOrFail($ca_quitado["id"]);
                        $quitar_ca->visible = false;
                        $quitar_ca->estado = "Eliminado";
                        $quitar_ca->save();
                    }
                }

            }

            DB::commit();
            return response()->json(["exito" => "Registro actualizado exitosamente.", "msg" => $request]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            $errores_controlados = [
                "No debe ingresar otros diagnosticos vacíos.",
                "No debe ingresar cuidados alta vacíos."
            ];
            $error = "No se ha podido actualizar el registro.";
            if(in_array($ex->getMessage(), $errores_controlados)){
                $error = $ex->getMessage();
            } 
            return response()->json(["error" => $error]);
        }
    }
}
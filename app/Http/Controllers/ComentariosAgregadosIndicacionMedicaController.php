<?php

namespace App\Http\Controllers;

use App\Models\ComentariosAgregadosIndicacionMedica;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Auth;
use DB;
use Log;
use Exception;
use Response;

class ComentariosAgregadosIndicacionMedicaController extends Controller
{

    public function agregarComentario(Request $request){
        try {
            Log::info($request);
            $id_caso = base64_decode($request->caso_);
            $fecha_creacion = Carbon::now()->format('Y-m-d H:i:s');
            $usuario = Auth::user()->id;
            DB::beginTransaction();
            if($request->id_comentario_){
                $mesage = "El comentario ha sido actualizado correctamente.";
                Log::info("deberia editar {$request->id_comentario_}");
                $comentario = ComentariosAgregadosIndicacionMedica::find($request->id_comentario_);
                $comentario->update([
                    'usuario_modifica' => $usuario,
                    'fecha_modificacion' => Carbon::now()->format("Y-m-d H:i:s"),
                    'visible' => false
                ]);

                //nuevo
                $comentario_indicacion_medica = new ComentariosAgregadosIndicacionMedica;
                $comentario_indicacion_medica->im_id = $request->id_indicacion_;
                $comentario_indicacion_medica->caso = $id_caso;
                $comentario_indicacion_medica->usuario_ingresa = Auth::user()->id;
                $comentario_indicacion_medica->fecha_creacion = $comentario->fecha_creacion; //de la que falsea.
                $comentario_indicacion_medica->created_at = $fecha_creacion;
                $comentario_indicacion_medica->visible = true;
                $comentario_indicacion_medica->comentario = $request->comentario_indicacion_;
                $comentario_indicacion_medica->id_anterior = $comentario->id;
                $comentario_indicacion_medica->save();

                $id = $comentario->im_id;
            }else{
                $mesage = "El comentario ha sido ingresado correctamente.";
                Log::info("deberia agregar uno nuevo");
                $comentario_indicacion_medica = new ComentariosAgregadosIndicacionMedica;
                $comentario_indicacion_medica->im_id = $request->id_indicacion_;
                $comentario_indicacion_medica->caso = $id_caso;
                $comentario_indicacion_medica->usuario_ingresa = Auth::user()->id;
                $comentario_indicacion_medica->fecha_creacion = $fecha_creacion;
                $comentario_indicacion_medica->created_at = $fecha_creacion;
                $comentario_indicacion_medica->visible = true;
                $comentario_indicacion_medica->comentario = $request->comentario_indicacion_;
                $comentario_indicacion_medica->save();

                $id = $comentario_indicacion_medica->im_id;
            }
            DB::commit();
            return response()->json(array("exito"=> $mesage, "id" => $id));
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function cargarComentariosIndicacion($id){
        try {
            $resultado = [];
            $comentarios = ComentariosAgregadosIndicacionMedica::where('im_id',$id)->where('visible',true)->orderBy('fecha_creacion','desc')->get();
            foreach ($comentarios as $comentario) {
                $nombre_usuario = $comentario["usuario"]["nombres"] ? $comentario["usuario"]["nombres"] : "";
                $apellido_paterno = $comentario["usuario"]["apellido_paterno"] ? $comentario["usuario"]["apellido_paterno"] : "";
                $apellido_materno = $comentario["usuario"]["apellido_materno"] ? $comentario["usuario"]["apellido_materno"] : "";
                // $created_at = $comentario["created_at"] ? Carbon::parse($comentario["created_at"])->format('d-m-y H:i:s') : "Sin información";
                $fecha_creacion = $comentario["fecha_creacion"] ? Carbon::parse($comentario["fecha_creacion"])->format('d-m-y H:i:s') : "Sin información";
                $editado = ($comentario["id_anterior"]) ? "<span class='label label-info'>Editado</span>" : "";  
                $resultado [] = [
                    "<input type='hidden' value='{$fecha_creacion}'>
                    <strong> {$nombre_usuario} {$apellido_paterno} {$apellido_materno} </strong> {$fecha_creacion} {$editado} <br><br>". $comentario["comentario"],
                    "<div>
                        <button class='btn btn-warning' onClick='editarComentario(".$comentario["id"].",".json_encode($comentario["comentario"]).")'>Editar</button>
                        <button class='btn btn-danger' onClick='eliminarComentario(".$comentario["id"].")'>Eliminar</button>
                    </div>"
                ];
            }
            return response()->json(["aaData" => $resultado]);
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error"=>$ex));
        }
    }

    public function eliminarComentarioAgregado($id){
        try {
            $fecha = Carbon::now()->format('Y-m-d H:i:s');
            $eliminar = ComentariosAgregadosIndicacionMedica::find($id);
                $eliminar->update([
                    'usuario_modifica' => Auth::user()->id,
                    'fecha_modificacion' => $fecha,
                    'visible' => false,
                    'estado' => 'Eliminado'
                ]);
                $id = $eliminar->im_id;
                return response()->json(array("exito"=> "Comentario eliminado exitosamente.", "id" => $id));
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array("error" => "No existe información sobre la indicación."));
        }
    }
}

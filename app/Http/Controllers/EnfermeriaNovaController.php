<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nova;
use App\Models\IEGeneral;
use Auth;
use DB;
use View;
use Carbon\Carbon;
use Log;


class EnfermeriaNovaController extends Controller
{
    public function index($caso)
    {
        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();
                    
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return View::make("Gestion/gestionEnfermeria/historialNova", array("caso"=>$caso, "nombreCompleto"=>$nombreCompleto));
    }

    public function datosTablaNova($caso){

        $datos = Nova::where("caso","=",$caso)->where('visible', true)->get();
        $response=[];
        foreach($datos as $dato){
            $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$dato->id_formulario_escala_nova.")'>Ver/Editar</button>";

            $fecha= Carbon::parse($dato->fecha_creacion)->format("d-m-Y H:i");
            $total = $dato->estado_mental + $dato->incontinencia + $dato->movilidad + $dato->nutricion_ingesta + $dato->actividad;
            $detalle = '';
            if($total == 0){
                $detalle = "Sin riesgo";
            }else if($total >= 1 && $total <= 4){
                $detalle = "Riesgo bajo";
            }else if($total >= 5 && $total <= 8){
                $detalle = "Riesgo medio";
            }else if($total >= 9 && $total <= 15){
                $detalle = "Riesgo alto";
            } 
            $total = $total ." - ".$detalle. " - ".$dato->tipo;
            $response[]  = array($opciones,$fecha, $dato->estado_mental, $dato->incontinencia, $dato->movilidad, $dato->nutricion_ingesta, $dato->actividad, $total);
        }
        return response()->json(["aaData" => $response]);
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis','Editar'];
            $existe = in_array($request->tipoFormNova, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->id_formulario_escala_nova && $request->tipoFormNova == 'Ingreso'){
                //Si es ingreso, Se debe buscar si tiene un formulario activo y actualizarlo para que sea reemplazado
                Log::info("Ingreso Nova");
                $nova = Nova::where('id_formulario_escala_nova',$request->id_formulario_escala_nova)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();
                
            }else if($request->id_formulario_escala_nova && ($request->tipoFormNova == 'En Curso' || $request->tipoFormNova == 'Editar')){
                //si es En curso, se debe consultar por el id 
                Log::info("En curso o editado Nova");
                if ($request->id_formulario_escala_nova) {                    
                    //buscar el id del formulario y comprobar si esta visible
                    $nova = Nova::where('id_formulario_escala_nova',$request->id_formulario_escala_nova)
                        ->where('visible', true)
                        ->first();

                    if (!$nova) {
                        //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                        return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                    }
                    
                    if($nova->estado_mental == $request->estado_mental && $nova->incontinencia == $request->incontinencia && $nova->movilidad == $request->movilidad && $nova->nutricion_ingesta == $request->nutricion_ingesta && $nova->actividad == $request->actividad && $nova->total == $request->total){
                        return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                    }
                }  
            }
         
            if (isset($nova)) {
                //Al final, si se trae algun tipo de valor de nova, este se debe modificar, de lo contrario se omite y se asume que es nuevo
                
                $nova->usuario_modifica = Auth::user()->id;
                $nova->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $nova->visible = false;
                $nova->save();
            }

            $nuevonova = new Nova;
            $nuevonova->caso = $request->caso;
            $nuevonova->usuario_responsable = Auth::user()->id;;
            $nuevonova->estado_mental = $request->estado_mental;
            $nuevonova->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevonova->incontinencia = $request->incontinencia;
            $nuevonova->movilidad = $request->movilidad;
            $nuevonova->nutricion_ingesta = $request->nutricion_ingesta;
            $nuevonova->actividad = $request->actividad;
            $nuevonova->total = $request->total;
            $nuevonova->visible = true;
            if(isset($nova)){
                //si existe algun tipo de dato en nova, este debe significar que tenia un anterior y debe ser cambiado
                $nuevonova->id_anterior = $nova->id_formulario_escala_nova;
                $nuevonova->tipo = $nova->tipo;
            }else{
                $nuevonova->tipo = $request->tipoFormNova;
            }
            $nuevonova->save();

            if(isset($nova)  && $nova->tipo == 'Ingreso'){
                //Si es nova con formato ingreso, debe modificarse el formulario asociado en su seccion de examen fisico general del Ingreso de enfermeria en RCE
                IEGeneral::where('caso', $request->caso)
                    ->whereNotNull('indnova')
                    ->update([
                        'indnova' => $nuevonova->id_formulario_escala_nova,
                    ]);
            }
            DB::commit();
            return response()->json(array("exito" => "Formulario guardado correctamente"));
        }
        
        catch(Exception $e){
            Log::info($e);
            DB::rollBack();
            return response()->json(["error" => $e]);
        }
        
    }

    public function edit($id)
    {
        //
        $datos = Nova::find($id);
        return $datos;
    }

}

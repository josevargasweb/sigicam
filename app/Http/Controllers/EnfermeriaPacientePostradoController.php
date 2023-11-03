<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PacientePostrado;
use Carbon\Carbon;
use Auth;
use DB;
use Log;
use View;

class EnfermeriaPacientePostradoController extends Controller
{
    public function ingresoPacientePostrado (Request $request){
        try {
            DB::beginTransaction();
            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis'];
            $existe = in_array($request->tipoFormPacientePostrado, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

            $nuevoPacientePostrado = new PacientePostrado;

            if(isset($request->id_formulario_paciente_postrado) && $request->id_formulario_paciente_postrado != ""){
                $pacientePostrado = PacientePostrado::where('id_formulario_paciente_postrado',$request->id_formulario_paciente_postrado)
                        ->where('visible', true)
                        ->first();

                if (!$pacientePostrado) {
                    //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }
           
                if($pacientePostrado->fecha == Carbon::createFromFormat("d-m-Y H:i", $request->fecha)->format("Y-m-d H:i:s") && $pacientePostrado->sitio == $request->sitio){
                    return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                }

                $pacientePostrado->usuario_modifica = Auth::user()->id;
                $pacientePostrado->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $pacientePostrado->visible = false;
                $pacientePostrado->save();

                $nuevoPacientePostrado->id_anterior = $pacientePostrado->id_formulario_paciente_postrado;
            }

            $nuevoPacientePostrado->caso = $request->idCaso;
            $nuevoPacientePostrado->usuario_responsable = Auth::user()->id;
            $nuevoPacientePostrado->fecha_creacion =  Carbon::now()->format("Y-m-d H:i:s");
            $nuevoPacientePostrado->visible = true;
            $nuevoPacientePostrado->fecha = $request->fecha;
            $nuevoPacientePostrado->sitio = $request->sitio;
            $nuevoPacientePostrado->tipo = $request->tipoFormPacientePostrado;

            $nuevoPacientePostrado->save();
            DB::commit();
            return response()->json(["exito" => "El ingreso se ha realizado exitosamente."]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "No se ha podido realizar el ingreso."]);
        }
    }

    public function historialPacientePostrado($caso){

        $hist = DB::table("formulario_paciente_postrado")
        ->where("caso",$caso)
        //->latest()
        ->where("visible",true)
        ->orderBy("fecha_creacion", "asc")
        ->get();

        $paciente = DB::table("pacientes as p") 
        ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
        ->join("casos as c", "p.id", "=", "c.paciente") 
        ->where("c.id", $caso) 
        ->first();

        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return view('Gestion.gestionEnfermeria.historialPacientePostrado', compact(['hist', 'caso', 'nombreCompleto']));
    }

    public function buscarHistorialPacientePostrado(Request $request){
        $datos = PacientePostrado::where("caso",$request->idCaso)->where("visible",true)->get();
        $response = [];
        foreach ($datos as $d) {
            $fecha = date("d-m-Y H:i", strtotime($d->fecha));
            $sitio = $d->sitio;

             //opciones
             $opciones = "<button class='btn btn-primary' type='button' onclick='editar(".$d->id_formulario_paciente_postrado.")'>Ver/Editar</button>";
            //  $opciones = "<div class='btn-group'>
            //  <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            //  Opciones 
            //  <span class='caret'></span>
            //  </button>
            //  <ul class='dropdown-menu dropdown-menu-left' role='menu' aria-labelledby='dropdownMenu1'>
            //      <li role='presentation'><a data-toggle='modal' data-target='#bannerformmodal' data-id='".$d->id_formulario_paciente_postrado."'>Ver/Editar</a></li>
            //  </ul>
            //  </div>";
            //opciones

        $response[] = [
            $opciones,
            $fecha,
            $sitio,
            
        ];
        }
        return response()->json($response);
    }


    public function edit($id)
    {
        $paciente = PacientePostrado::find($id);
        return $paciente;
        return response()->json(["datos" => $datos]);
       
    }

}

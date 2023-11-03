<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaEnfermeriaRiesgoCaida;

use Auth;
use Log;
use DB;
use Carbon\Carbon;
use View;
use App\Models\RiesgoCaida;
use PDF;
use App\Models\Establecimiento;
use App\Models\Paciente;

class EnfermeriaEscalaMacdensController extends Controller{
    
    public function ingresoEscalaMacdems (Request $request){
        try {
            DB::beginTransaction();

            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis'];
            $existe = in_array($request->tipoFormMacdems, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

            $nuevoRiesgoCaida = new HojaEnfermeriaRiesgoCaida;

            if(isset($request->id_formulario_escala_macdems)){

                $riesgoCaida = HojaEnfermeriaRiesgoCaida::where('id',$request->id_formulario_escala_macdems)
                        ->where('visible', true)
                        ->first();

                if (!$riesgoCaida) {
                    //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }

                if($riesgoCaida->edad == $request->edad && $riesgoCaida->caidas_previas == $request->caidas_previas && $riesgoCaida->antecedentes == $request->antecedentes && $riesgoCaida->criterio_compr_conciencia == $request->compr_conciencia){
                    return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                }

                $riesgoCaida->usuario_modifica = Auth::user()->id;
                $riesgoCaida->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $riesgoCaida->visible = false;
                $riesgoCaida->save();

                $nuevoRiesgoCaida->id_anterior = $riesgoCaida->id;
            }

            
            $nuevoRiesgoCaida->caso = $request->idCaso;
            $nuevoRiesgoCaida->procedencia = "Macdems";
            $nuevoRiesgoCaida->usuario_ingresa = Auth::user()->id;
            $nuevoRiesgoCaida->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevoRiesgoCaida->visible = true;
            $nuevoRiesgoCaida->edad = $request->edad;
            $nuevoRiesgoCaida->caidas_previas = $request->caidas_previas;
            $nuevoRiesgoCaida->antecedentes = $request->antecedentes;
            $nuevoRiesgoCaida->criterio_compr_conciencia = $request->compr_conciencia;
            $nuevoRiesgoCaida->total = ($request->total == 0)?0:$request->total;
            $nuevoRiesgoCaida->tipo = $request->tipoFormMacdems;
            $nuevoRiesgoCaida->save();
            DB::commit();
            return response()->json(["exito" => "El ingreso se ha realizado exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "No se ha podido realizar el ingreso"]);
        }
    }

    public function historialEscalaMacdems($caso){
        $historial = DB::table("formulario_hoja_enfermeria_riesgo_caida")
        ->where("caso",$caso)
        ->where("procedencia", "=","Macdems")
        ->where("visible",true)
        ->orderBy("fecha_creacion", "asc")->get();

        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return view::make("Gestion/gestionEnfermeria/historialMacdems")
        ->with(array(
            "caso" => $caso,
            "hist" => $historial,
            "paciente" => $nombreCompleto));
    }

    public function buscarHistorialEscalaMacdems(Request $request){
        $response = [];
        $data = HojaEnfermeriaRiesgoCaida::dataHistorialMacdems($request->idCaso);
        $response = $data;
        return response()->json($response);
    }

    public function edit($id)
    {
        $datos = HojaEnfermeriaRiesgoCaida::find($id);
        return response()->json(["datos" => $datos]);
    }

    public function pdfHistorialMacdems($caso){
        try {
            $fechaActual = Carbon::now();
            $fecha = Carbon::parse($fechaActual)->format("d-m-Y");
            $historialMacdems = HojaEnfermeriaRiesgoCaida::dataHistorialMacdems($caso);
            $idEstablecimiento = Auth::user()->establecimiento;
            $paciente = Paciente::getPacientePorCaso($caso);

		    $nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
            
            $pdf = PDF::loadView('TemplatePDF.historialMacdemsPdf',
                [
                "fecha" => $fecha,
                "response" => $historialMacdems,
                "establecimiento" => $nombreEstablecimiento,
                "infoPaciente" => $paciente,
                ]);
            return $pdf->download('Historial_Macdems_'.$fecha.'.pdf');
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HojaEnfermeriaRiesgoCaida;
use App\Models\Paciente;
use App\Models\Establecimiento;
use App\Models\IEGeneral;
use Auth;
use DB;
use Log;
use Carbon\Carbon;
use View;
use PDF;

class EnfermeriaRiesgoCaidaController extends Controller
{
    public function ingresoRiesgoCaida (Request $request){
        try {
            DB::beginTransaction();
            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis','Editar'];
            $existe = in_array($request->tipoFormRiesgoCaida, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

             //se modifica a string para guardar
             if(is_array($request->medicamentos)){
                $medicamentoArray = ["1" =>''];
                $existeVacio = in_array($request->medicamentos, $medicamentoArray);
                if($existeVacio){
                    return response()->json(array("error" => "No debe modificar Datos"));
                }
                $request->merge([
                    'medicamentos' => implode(",", $request->medicamentos),
                ]);
            }

            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->id_formulario_riesgo_caida && $request->tipoFormRiesgoCaida == 'Ingreso'){
                //Si es ingreso, Se debe buscar si tiene un formulario activo y actualizarlo para que sea reemplazado
                Log::info("Ingreso Riesgo caida");
                $riesgoCaida = HojaEnfermeriaRiesgoCaida::where('id_formulario_riesgo_caida',$request->id_formulario_riesgo_caida)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();
                
            }else if($request->id_formulario_riesgo_caida && ($request->tipoFormRiesgoCaida == 'En Curso' || $request->tipoFormRiesgoCaida == 'Editar')){
                //si es En curso, se debe consultar por el id 
                Log::info("En curso o editado Riesgo caida");
                if ($request->id_formulario_riesgo_caida) {                    
                    //buscar el id del formulario y comprobar si esta visible
                    $riesgoCaida = HojaEnfermeriaRiesgoCaida::where('id',$request->id_formulario_riesgo_caida)
                        ->where('visible', true)->first();

                    if (!$riesgoCaida) {
                        //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                        return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                    }

                    if($riesgoCaida->medicamentos == $request->medicamentos && $riesgoCaida->caidas_previas == $request->caidas_previas && $riesgoCaida->deficits_sensoriales == $request->deficit && $riesgoCaida->estado_mental == $request->mental && $riesgoCaida->deambulacion == $request->deambulacion){
                        return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                    }

                }
            }

            if (isset($riesgoCaida)) {
                //Al final, si se trae algun tipo de valor de nova, este se debe modificar, de lo contrario se omite y se asume que es nuevo
                
                $riesgoCaida->usuario_modifica = Auth::user()->id;
                $riesgoCaida->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $riesgoCaida->visible = false;
                $riesgoCaida->save();
            }

           

            $nuevoRiesgoCaida = new HojaEnfermeriaRiesgoCaida;
            $nuevoRiesgoCaida->caso = $request->idCaso;
            $nuevoRiesgoCaida->procedencia = "Formulario1";
            $nuevoRiesgoCaida->usuario_ingresa = Auth::user()->id;
            $nuevoRiesgoCaida->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevoRiesgoCaida->visible = true;
            $nuevoRiesgoCaida->medicamentos = $request->medicamentos;
            $nuevoRiesgoCaida->caidas_previas = $request->caidas_previas;
            $nuevoRiesgoCaida->deficits_sensoriales = $request->deficit;
            $nuevoRiesgoCaida->estado_mental = $request->mental;
            $nuevoRiesgoCaida->deambulacion = $request->deambulacion;
            $nuevoRiesgoCaida->total = ($request->total == 0)?0:$request->total;
            if(isset($riesgoCaida)){
                //si existe algun tipo de dato en riesgo caida, este debe significar que tenia un anterior y debe ser cambiado
                $nuevoRiesgoCaida->id_anterior = $riesgoCaida->id;
                $nuevoRiesgoCaida->tipo = $riesgoCaida->tipo;
            }else{
                $nuevoRiesgoCaida->tipo = $request->tipoFormRiesgoCaida;
            }
            $nuevoRiesgoCaida->save();

            if(isset($riesgoCaida)  && $riesgoCaida->tipo == 'Ingreso'){
                IEGeneral::where('caso', $request->idCaso)
                    ->whereNotNull('indriesgo')
                    ->update([
                        'indriesgo' => $nuevoRiesgoCaida->id,
                    ]);
            }

            DB::commit();
            return response()->json(["exito" => "El ingreso se ha realizado exitosamente"]);
        } catch (Exception $ex) {
            Log::info($ex);
            DB::rollback();
            return response()->json(["error" => "No se ha podido realizar el ingreso"]);
        }
    }

    public function historialRiesgoCaida($caso){
        $historial = DB::table("formulario_hoja_enfermeria_riesgo_caida")
        ->where("caso",$caso)
        ->where("procedencia", "=","Formulario1")
        ->orderBy("fecha_creacion", "asc")
        ->where("visible",true)->get();

        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return view::make("Gestion/gestionEnfermeria/historialRiesgoCaida")
        ->with(array(
            "caso" => $caso,
            "hist" => $historial,
            "paciente" => $nombreCompleto));
    }

    public function buscarHistorialRiesgoCaidas(Request $request){
        $response = [];
        $data = HojaEnfermeriaRiesgoCaida::dataHistorialRiesgoCaida($request->idCaso);
        $response = $data;
        return response()->json($response);
    }

    public function edit($id)
    {
        $datos = HojaEnfermeriaRiesgoCaida::find($id);
        return response()->json(["datos" => $datos]);
    }

    public function pdfHistorialRiesgoCaida($caso){
        try {
            $fechaActual = Carbon::now();
            $fecha = Carbon::parse($fechaActual)->format("d-m-Y");
            $historialRiesgoCaidas = HojaEnfermeriaRiesgoCaida::dataHistorialRiesgoCaida($caso);
            $idEstablecimiento = Auth::user()->establecimiento;
            $paciente = Paciente::getPacientePorCaso($caso);

		    $nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
            
            $pdf = PDF::loadView('TemplatePDF.historialRiesgoCaidaPdf',
                [
                "fecha" => $fecha,
                "response" => $historialRiesgoCaidas,
                "establecimiento" => $nombreEstablecimiento,
                "infoPaciente" => $paciente,
                ]);
            return $pdf->download('Historial_Riesgo_Caidas_'.$fecha.'.pdf');
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiesgoUlceras;
use App\Models\Paciente;
use App\Models\Establecimiento;
use App\Models\IEGeneral;
use Auth;
use DB;
use Carbon\Carbon;
use View;
use PDF;
use Log;

class RiesgoUlceraController extends Controller
{
    public function ingresoRiesgoUlcera (Request $request){
        try {
            DB::beginTransaction();
            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis'];
            $existe = in_array($request->tipoFormUlcera, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }
            $nuevoRiesgoUlcera = new RiesgoUlceras;
            
            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->id_formulario_riesgo_ulcera && $request->tipoFormUlcera == 'Ingreso'){
                //Si es ingreso, Se debe buscar si tiene un formulario activo y actualizarlo para que sea reemplazado
                Log::info("Ingreso");
                $riesgoUlcera = RiesgoUlceras::where('id',$request->id_formulario_riesgo_ulcera)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();
                
            }else if($request->id_formulario_riesgo_ulcera && ($request->tipoFormUlcera == 'En Curso' || $request->tipoFormUlcera == 'Editar')){
                //si es En curso, se debe consultar por el id 
                Log::info("En curso o editado");                
                //buscar el id del formulario y comprobar si esta visible
                $riesgoUlcera = RiesgoUlceras::where('id',$request->id_formulario_riesgo_ulcera)
                    ->where('visible', true)
                    ->first();

                if (!$riesgoUlcera) {
                    //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                    return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                }

                if($riesgoUlcera->percepcion_sensorial == $request->percepcion_sensorial && $riesgoUlcera->exposicion_humedad == $request->exposicion_humedad && $riesgoUlcera->actividad == $request->actividad && $riesgoUlcera->movilidad == $request->movilidad && $riesgoUlcera->nutricion == $request->nutricion && $riesgoUlcera->peligro_lesiones == $request->peligro_lesiones){
                    return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                }
            }

            if (isset($riesgoUlcera)) {
                //Al final, si se trae algun tipo de valor de riesgo ulcera, este se debe modificar, de lo contrario se omite y se asume que es nuevo
                $riesgoUlcera->usuario_modifica = Auth::user()->id;
                $riesgoUlcera->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $riesgoUlcera->visible = false;
                $riesgoUlcera->save();
            }
            
            $nuevoRiesgoUlcera->caso = $request->idCaso;
            $nuevoRiesgoUlcera->usuario_ingresa = Auth::user()->id;
            $nuevoRiesgoUlcera->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevoRiesgoUlcera->visible = true;
            $nuevoRiesgoUlcera->percepcion_sensorial = ($request->percepcion_sensorial == 0) ? 0 : $request->percepcion_sensorial;
            $nuevoRiesgoUlcera->exposicion_humedad = ($request->exposicion_humedad == 0) ? 0 : $request->exposicion_humedad;
            $nuevoRiesgoUlcera->actividad = ($request->actividad == 0) ? 0 : $request->actividad;
            $nuevoRiesgoUlcera->movilidad = ($request->movilidad == 0) ? 0 : $request->movilidad;
            $nuevoRiesgoUlcera->nutricion = ($request->nutricion == 0) ? 0 : $request->nutricion;
            $nuevoRiesgoUlcera->peligro_lesiones = ($request->peligro_lesiones == 0) ? 0 : $request->peligro_lesiones;
            $nuevoRiesgoUlcera->total = ($request->total == 0)?0:$request->total;
            if(isset($riesgoUlcera)){
                //si existe algun tipo de dato en nova, este debe significar que tenia un anterior y debe ser cambiado
                $nuevoRiesgoUlcera->id_anterior = $riesgoUlcera->id;
                $nuevoRiesgoUlcera->tipo = $riesgoUlcera->tipo;
            }else{
                $nuevoRiesgoUlcera->tipo = $request->tipoFormUlcera;
            }
            
            $nuevoRiesgoUlcera->save();

            if(isset($riesgoUlcera)  && $riesgoUlcera->tipo == 'Ingreso'){
                //Si es ulcera con formato ingreso, debe modificarse el formulario asociado en su seccion de examen fisico general del Ingreso de enfermeria en RCE
                IEGeneral::where('caso', $request->idCaso)
                    ->whereNotNull('indulcera')
                    ->update([
                        'indulcera' => $nuevoRiesgoUlcera->id,
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

    public function historialriesgoUlcera($caso){
        $historial = DB::table("formulario_riesgo_ulceras")
        ->where("caso",$caso)
        ->orderBy("fecha_creacion", "asc")
        ->where("visible",true)
        ->get();

        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        return view::make("Gestion/gestionEnfermeria/historialRiesgoUlcera")
        ->with(array(
            "caso" => $caso,
            "hist" => $historial,
            "paciente" => $nombreCompleto));
    }

    public function buscarHistorialriesgoUlceras(Request $request){
        $response = [];
        $data = RiesgoUlceras::dataHistorial($request->idCaso);
        $response = $data;
        return response()->json($response);
    }

    public function edit($id)
    {
        $datos = RiesgoUlceras::find($id);
        return response()->json(["datos" => $datos]);
    }

    public function pdfHistorialriesgoUlcera($caso){
        try {
            $fechaActual = Carbon::now();
            $fecha = Carbon::parse($fechaActual)->format("d-m-Y");
            $historialriesgoUlceras = RiesgoUlceras::dataHistorial($caso);
            $idEstablecimiento = Auth::user()->establecimiento;
            $paciente = Paciente::getPacientePorCaso($caso);

		    $nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
            
            $pdf = PDF::loadView('TemplatePDF.historialriesgoUlceraPdf',
                [
                "fecha" => $fecha,
                "response" => $historialriesgoUlceras,
                "establecimiento" => $nombreEstablecimiento,
                "infoPaciente" => $paciente,
                ]);
            return $pdf->download('Historial_Escala_Riesgo_Lesiones_Por_Presion_'.$fecha.'.pdf');
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}

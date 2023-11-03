<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IEGeneral;
use App\Models\Barthel;
use App\Models\Paciente;
use App\Models\Establecimiento;
use App\Models\EvolucionEnfermeria;
use Auth;
use DB;
use View;
use Carbon\Carbon;
use PDF;
use Log;

class EnfermeriaBarthelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function historialBarthel($caso)
    {
        $paciente = DB::table("pacientes as p") 
            ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
            ->join("casos as c", "p.id", "=", "c.paciente") 
            ->where("c.id", $caso)         
            ->first();
        //dd($paciente)
        $nombreCompleto = $paciente->nombre. " ".$paciente->apellido_paterno. " ".$paciente->apellido_materno;

        $hist = DB::table("formulario_barthel")
            ->where("caso",$caso)
            ->orderBy("fecha_creacion", "asc")
            ->where("visible",true)
            ->get();
        return View::make("Gestion/gestionEnfermeria/historialBarthel")
        ->with(array(
            "caso" => $caso,
            "hist" =>$hist,
            "nombreCompleto" => $nombreCompleto
        ));
    }


    public function buscarHistorialBarthel(Request $request){
        $response = [];
        $data = Barthel::dataHistorialBarthel($request->idCaso);
        $response = $data;
        return response()->json($response);
    }


    public function store(Request $request)
    {
        //
        try{
            DB::beginTransaction();

            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis','Editar'];
            $existe = in_array($request->tipoFormBarthel, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }


            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->id_formulario_barthel && $request->tipoFormBarthel == 'Ingreso'){
                //Si es ingreso, Se debe buscar si tiene un formulario activo y actualizarlo para que sea reemplazado
                Log::info("Ingreso");
                $barthel = Barthel::where('id_formulario_barthel',$request->id_formulario_barthel)
                    ->where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();
                
            }else if($request->id_formulario_barthel && ($request->tipoFormBarthel == 'En Curso' || $request->tipoFormBarthel == 'Editar')){
                //si es En curso, se debe consultar por el id 
                Log::info("En curso o editado");
                if ($request->id_formulario_barthel) {                    
                    //buscar el id del formulario y comprobar si esta visible
                    $barthel = Barthel::where('id_formulario_barthel',$request->id_formulario_barthel)
                        ->where('visible', true)->first();

                    if (!$barthel) {
                        //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                        return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                    }
                    
                    if($barthel->comida == $request->comida && $barthel->lavado == $request->lavado && $barthel->vestido == $request->vestido && $barthel->arreglo == $request->arreglo && $barthel->deposicion == $request->deposicion &&  $barthel->miccion == $request->miccion  &&  $barthel->retrete == $request->retrete  && $barthel->trasferencia == $request->trasferencia  && $barthel->deambulacion == $request->deambulacion &&  $barthel->escaleras == $request->escaleras){
                        return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                    }
                }

            }

            if (isset($barthel)) {
                Log::info("tien barthel");
                //Al final, si se trae algun tipo de valor de barthel, este se debe modificar, de lo contrario se omite y se asume que es nuevo                
                $barthel->usuario_modifica = Auth::user()->id;
                $barthel->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $barthel->visible = false;
                $barthel->save();
            }

            $nuevoBarthel = new Barthel;
            $nuevoBarthel->caso = $request->caso;
            $nuevoBarthel->usuario_responsable = Auth::user()->id;
            $nuevoBarthel->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevoBarthel->visible = true;
            $nuevoBarthel->comida = $request->comida;
            $nuevoBarthel->lavado = $request->lavado;
            $nuevoBarthel->vestido = $request->vestido;
            $nuevoBarthel->arreglo = $request->arreglo;
            $nuevoBarthel->deposicion =$request->deposicion;
            $nuevoBarthel->miccion = $request->miccion;
            $nuevoBarthel->retrete = $request->retrete;
            $nuevoBarthel->trasferencia = $request->trasferencia;
            $nuevoBarthel->deambulacion = $request->deambulacion; 
            $nuevoBarthel->escaleras = $request->escaleras;
            if(isset($barthel)){
                //si existe algun tipo de dato en barthel, este debe significar que tenia un anterior y debe ser cambiado
                $nuevoBarthel->id_anterior = $barthel->id_formulario_barthel;
                $nuevoBarthel->tipo = $barthel->tipo;
            }else{
                $nuevoBarthel->tipo = $request->tipoFormBarthel;
            }
            $nuevoBarthel->save();

            if(isset($barthel)  && $barthel->tipo == 'Ingreso'){
                IEGeneral::where('caso', $request->caso)
                    ->whereNotNull('indbarthel')
                    ->update([
                        'indbarthel' => $nuevoBarthel->id_formulario_barthel,
                    ]);
            }elseif(isset($barthel)  && $barthel->tipo == 'Epicrisis'){
                //Actualizar la epicrisis
                $modificar = EvolucionEnfermeria::where('caso',$request->caso)
                    ->where('visible', true)
                    ->first();
                    Log::info("EPI");
                    Log::info($modificar);
                if ($modificar) {
                    Log::info("epicrisis actualizada a ".$nuevoBarthel->id_formulario_barthe);
                    $modificar->indbarthel = $nuevoBarthel->id_formulario_barthel;
                    $modificar->save();
                }
            }

            DB::commit();
            return response()->json(array("exito" => "Formulario guardado correctamente"));
        }catch(Exception $e){
            Log::info($e);
            DB::rollBack();
            return response()->json(["error" => $e]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return  Barthel::find($id);
    }

    public function pdfHistorialBarthel($caso){
        try {
            $fechaActual = Carbon::now();
            $fecha = Carbon::parse($fechaActual)->format("d-m-Y");
            $historialBarthel = Barthel::dataHistorialBarthel($caso);
            $idEstablecimiento = Auth::user()->establecimiento;
            $paciente = Paciente::getPacientePorCaso($caso);

		    $nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
            
            $pdf = PDF::loadView('TemplatePDF.historialBarthelPdf',
                [
                "fecha" => $fecha,
                "response" => $historialBarthel,
                "establecimiento" => $nombreEstablecimiento,
                "infoPaciente" => $paciente,
                ]);
            return $pdf->download('Historial_Barthel_'.$fecha.'.pdf');
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }


}

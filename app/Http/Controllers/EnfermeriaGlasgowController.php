<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IEGeneral;
use App\Models\Glasgow;
use App\Models\HojaEnfermeriaControlSignoVital;
use Auth;
use DB;
use View;
use Log;
use Carbon\Carbon;

class EnfermeriaGlasgowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($caso)
    {
        $paciente = DB::table("pacientes as p") 
                    ->select("p.nombre", "p.apellido_paterno", "p.apellido_materno") 
                    ->join("casos as c", "p.id", "=", "c.paciente") 
                    ->where("c.id", $caso) 
                    ->first();

        return View::make("Gestion/gestionEnfermeria/glasgowIndex", array("caso"=>$caso,"paciente" => $paciente->nombre." ".$paciente->apellido_paterno." ".$paciente->apellido_materno ));
    }

    public function datosTabla($caso){
        $response=[];

        $datos = Glasgow::where("caso","=",$caso)->where("visible",true)->get();
        foreach($datos as $dato){
            /*$boton = "<div class='btn-group'>
                    <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                    Opciones 
                    <span class='caret'></span>
                    </button>
                    <ul class='dropdown-menu dropdown-menu-left' role='menu' aria-labelledby='dropdownMenu1'>
                        <li role='presentation'><a data-toggle='modal' data-target='#bannerformmodal' data-id='".$dato->id_formulario_escala_glasgow."'>Ver/Editar</a></li>
                    </ul>
                    </div>";
                    */
                    $boton = "<button class='btn btn-primary' data-toggle='modal' data-target='#bannerformmodal' data-id='".$dato->id_formulario_escala_glasgow."'>Ver/Editar</button>";

            $fecha=date("d-m-Y H:i", strtotime($dato->fecha_creacion));
            $tipo = ($dato->tipo != null)?$dato->tipo: "No posee";
            $response[]  = array($boton,$fecha, $dato->apertura_ocular, $dato->respuesta_verbal, $dato->respuesta_motora, $dato->total. " - ".$tipo);
        }
        return response()->json(["aaData" => $response]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         
        try{
            DB::beginTransaction();

            $tablaArray = ["1" =>'Ingreso','En Curso','Epicrisis','Editar'];
            $existe = in_array($request->tipoFormGlasgow, $tablaArray);
            if(!$existe){
                return response()->json(array("error" => "No debe modificar Datos"));
            }

            //Esta parte es para mostrar en caso de que sea una edicion de datos
            if($request->tipoFormGlasgow == 'Ingreso'){
                //Si es ingreso, Se debe buscar si tiene un formulario activo y actualizarlo para que sea reemplazado
                Log::info("Ingreso");
                $glasgow = Glasgow::where('visible', true)
                    ->where('tipo', 'Ingreso')
                    ->first();
                
            }else if($request->id_formulario_escala_glasgow && ($request->tipoFormGlasgow == 'En Curso' || $request->tipoFormGlasgow == 'Editar')){
                //si es En curso, se debe consultar por el id 
                Log::info("En curso o editado");
                if ($request->id_formulario_escala_glasgow) {                    
                    //buscar el id del formulario y comprobar si esta visible
                    $glasgow = Glasgow::where('id_formulario_escala_glasgow',$request->id_formulario_escala_glasgow)
                        ->where('visible', true)->first();

                    if (!$glasgow) {
                        //Si no se encontro el formuario, significa que ya fue modificado el formulario y debe indicarle error al usuario
                        return response()->json(array("info" => "Este formulario ya ha sido modificado"));
                    }
                    
                    if($glasgow->apertura_ocular == $request->apertura_ocular && $glasgow->respuesta_verbal == $request->respuesta_verbal && $glasgow->respuesta_motora == $request->respuesta_motora ){
                        return response()->json(array("info" => "Este formulario fue editado con los mismos valores"));
                    }
                }
            }

            $nuevoglasgow = new Glasgow;
            $nuevoglasgow->caso = $request->caso;
            $nuevoglasgow->usuario_responsable = Auth::user()->id;
            $nuevoglasgow->apertura_ocular = $request->apertura_ocular;
            $nuevoglasgow->respuesta_verbal = $request->respuesta_verbal;
            $nuevoglasgow->respuesta_motora = $request->respuesta_motora;
            $nuevoglasgow->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            if(isset($glasgow)){
                //si existe algun tipo de dato en glasgow, este debe significar que tenia un anterior y debe ser cambiado
                $nuevoglasgow->id_anterior = $glasgow->id_formulario_escala_glasgow;
                $nuevoglasgow->tipo = $glasgow->tipo;
            }else{
                $nuevoglasgow->tipo = $request->tipoFormGlasgow;
            }
            $nuevoglasgow->visible = true;
            $nuevoglasgow->total = $request->total;
            $nuevoglasgow->save();


            if (isset($glasgow)) {
                //Al final, si se trae algun tipo de valor de glasgow, este se debe modificar, de lo contrario se omite y se asume que es nuevo                
                $glasgow->usuario_modifica = Auth::user()->id;
                $glasgow->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
                $glasgow->visible = false;
                $glasgow->save();

                //Ademas se debe verificar que dentro de los formularios de signos vitales no se tenga asociado ningun id a estos
                $formulario_signos_vitales = HojaEnfermeriaControlSignoVital::where('indglasgow1', $glasgow->id_formulario_escala_glasgow)
                    ->first();
                
                if ($formulario_signos_vitales) {
                    //Si encontro formulario asociado al id del glasgow, este debera ser actualizado
                    $formulario_signos_vitales->indglasgow1 = $nuevoglasgow->id_formulario_escala_glasgow;
                    $formulario_signos_vitales->save();
                }
            }


            if(isset($glasgow)  && $glasgow->tipo == 'Ingreso'){
                IEGeneral::where('caso', $request->caso)
                    ->whereNotNull('indglasgow')
                    ->update([
                        'indglasgow' => $nuevoglasgow->id_formulario_escala_glasgow,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $datos = Glasgow::find($id);
        return $datos;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

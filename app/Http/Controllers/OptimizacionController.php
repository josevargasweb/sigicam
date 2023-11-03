<?php

namespace App\Http\Controllers;

use Session;
use Auth;
use TipoUsuario;
use Consultas;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Establecimiento;
use App\Models\Sala;
use App\Models\Procedencia;
use View;
use App\Models\Prevision;
use App\Models\Dieta;
use URL;
use DB;
use Illuminate\Http\Request;
use Log;
use App\Models\Paciente;
use App\Models\Caso;
use Form;
use App\Models\Derivacion;
use App\Models\HistorialOcupacion;
use App\Models\Riesgo;
use App\Models\ListaEspera;
use App\Models\HistorialDiagnostico;
use App\Models\EvolucionCaso;
use App\Models\Cama;
use Cache;
use App\Models\EstablecimientosExtrasistema;
use App\Models\Localizacion;
use App\Models\AgenteEtiologico;
use App\Models\CaracteristicasAgente;
use App\Models\ProcedimientoInvasivo;
use App\Models\Examen;
use App\Models\HospitalizacionDomiciliaria;
use App\Models\MensajeDerivacion;
use File;
use App\Models\Documento;
use App\Models\HistorialBloqueo;
use App\Models\HistorialCamasUnidades;
use App\Models\PlanDeTratamiento;
use Excel;
use App\Models\Infecciones;
use App\Models\PacientesInfeccion;
use App\Models\CVC;
use App\Models\IAAS;
use \nusoap_client;
use App\Models\Comuna;
use App\Models\Medico;
use App\Models\Complejidad_servicio;

use App\Models\Especialidades;

class OptimizacionController extends Controller
{
	public function comunas(Request $request)
    {
		$region = ($request->region == '')? 3:strip_tags($request->region);
		$comunas = DB::table('comuna')
					->select('nombre_comuna','id_comuna')
					->where('id_region','=',$region)
					->get();

		return response()->json($comunas);
	}

    public function indexOptimizacion()
    {
    	try{

        //return "OK";

    			$tipoUsuario=Auth::user()->tipo;
    			$admin=TipoUsuario::ADMIN;
    			$permisos_establecimiento = Session::get("permisos_establecimiento");
    			$motivoBloqueo=Consultas::getMotivosBloqueo();
    			$motivos = Consultas::getMotivosLiberacion();

    			$establecimiento = DB::table('establecimientos')
                        ->select('establecimientos.id')
                        ->where('establecimientos.id','=', Session::get("idEstablecimiento"))
                        ->first();


                /*este recibidos es para los del hospital y no se utiliza
    			$recibidos2 = DB::table('unidades')
                        ->select('unidades.id as id', 'unidades.nombre as nombre')
                        ->join('servicios_recibidos', 'unidades.id','=','servicios_recibidos.unidad')
                        ->join('unidades_en_establecimientos','servicios_recibidos.unidad_en_establecimiento','=','unidades_en_establecimientos.id')
                        ->join('establecimientos','unidades_en_establecimientos.establecimiento','=','establecimientos.id')
                        ->where('establecimientos.id','=', $establecimiento->id)
                        ->groupBy('unidades.id', 'unidades.nombre')
                        ->get();*/

                //este segundo recibido es para todas las unidades
                $recibidos = DB::table('unidades')
                        ->select('unidades.id as id', 'unidades.nombre as nombre')
                        ->groupBy('unidades.id', 'unidades.nombre')
                        ->get();


                //$recibido[] = array($recibidos->id,$recibidos->nombre);
    			//return response()->json( $recibidos);

    			$procedencias = [];
    			foreach(Procedencia::all() as $proc){
					if ($proc->nombre == "Otro") {
						$ultimo = [$proc->nombre , $proc->id];
					}else{
						$procedencias[$proc->id] = $proc->nombre;
					}
				}
				$procedencias[$ultimo[1]] = $ultimo[0];

    			$unidades = [];
    			foreach($recibidos as $recv){
    				$unidades[$recv->id] = $recv->nombre;
    			}
    			//return response()->json( $unidades);

                $lista_diagnostico = DB::table('cie_10')
                                    ->select('cie_10.id_cie_10', 'cie_10.nombre')
                                    ->get();


                $diagnosticos = [];
                foreach($lista_diagnostico as $key => $diagnostico){
                    $diagnosticos[$key] =  $diagnostico->id_cie_10." ".$diagnostico->nombre;
				}

				$lista_servicios = DB::table('servicios_vista')
								->where('establecimiento', '=', Auth::user()->establecimiento)
								->orderBy('alias')
								->get();

				$servicios = [];
				$atributos = [];
				foreach($lista_servicios as $key => $servicio){
					$servicios[$servicio->id_unidad] =  $servicio->alias;
					$atributos[$servicio->id_unidad] = ["data-toggle" =>"tooltip", "title"=>$servicio->tooltip];
				}

				//return response()->json( $diagnosticos);

				/* select id_unidad, alias, establecimiento, id_area_funcional, area from servicios_vista where establecimiento=8; */

				unset($motivos['traslado interno']);
    			return view('Optimizacion/IngresarPaciente', [
    				"procedencias" => $procedencias,
    				"prevision" => Prevision::getPrevisiones(),
    				"riesgo" => Consultas::getRiesgos(),
					'unidades' => $unidades,
					'comunas' => Comuna::where('id_region', '=', 3)->pluck('nombre_comuna','id_comuna'),
					'regiones' => Consultas::getRegion(),
					'servicios'	 => $servicios,
					'medicos' => Medico::getMedicos(),
					"atributos"=> $atributos,
					"fecha_hoy"=>date("d-m-Y H:i"),
					"especialidad"=>Especialidades::pluck('nombre','id')
                    //"diagnosticos" => $diagnosticos
    			]);
		}
		catch(Exception $e){
		        App::abort(404);
		}


	}

	public function ingresarHospDom(){

		$procedencias = [];
		foreach(Procedencia::all() as $proc){
			if ($proc->nombre == "Otro") {
				$ultimo = [$proc->nombre , $proc->id];
			}else if($proc->nombre == "Pabellón"){}
			else{
				$procedencias[$proc->id] = $proc->nombre;
			}
		}
		$procedencias[$ultimo[1]] = $ultimo[0];

		return view('Optimizacion/IngresarHospDom', [
			"procedencias" => $procedencias,
			"prevision" => Prevision::getPrevisiones(),
			'comunas' => Comuna::where('id_region', '=', 3)->pluck('nombre_comuna','id_comuna'),
			'regiones' => Consultas::getRegion(),
			"especialidad"=>Especialidades::pluck('nombre','id')
		]);
	}

    public function buscarInformacion(Request $request)
    {
		/*INGRESAR A CAMA*/

        /* return response()->json($request); */
    	$tipo=$request->input("tipo");
    	$idCama=$request->input("cama");
    	$rut=trim($request->input("rut"));
    	$sexo=$request->input("sexo");
    	$tipo_procedencia = $request->input("tipo-procedencia");
    	$procedencia = $request->input("input-procedencia");
    	$diagnostico = $request->input("diagnostico");
    	$diagnostico_cie10 = empty($request->input("diagnostico_cie10"))?null:$request->input("diagnostico_cie10");
    	$medico = $request->input("id_medico");
    	$nombre = $request->input("nombre");
    	$apellido_paterno = $request->input("apellidoP");
    	$apellido_materno = $request->input("apellidoM");
        $dv = $request->input("dv");
    	$caso_social = $request->input("caso_social");
    	$extranjero = $request->input("extranjero");
        $prevision = $request->input("prevision");
        $nombre_social = $request->input("nombreSocial");
        $diagnosticos = $request->input("diagnosticos");
		$hidden_diagnosticos = $request->input("hidden_diagnosticos");
		$fecha_solicitud =\Carbon\Carbon::createFromFormat("d-m-Y H:i:s", $request->input("fechaIngreso"));
		$dau = $request->input("dau");
		$ficha_clinica = $request->input("fichaClinica");
		$servicio = $request->input("servicios");
		$rango = $request->input("rango");

		if($medico == ''){
			$medico = null;
		}

        if($rut){
            $en_lista_espera = DB::table('pacientes as p')
                                ->join('casos as c', 'c.paciente','=','p.id')
                                ->join('lista_espera as l','l.caso','=','c.id')
                                ->where('p.rut',$rut)
                                ->whereNull('l.fecha_termino')
                                ->first();

            if($en_lista_espera != null){
                return response()->json(["error" => "El paciente se encuentra en lista de espera"]);
			}

			$en_lista_transito = DB::table('pacientes as p')
								->join('casos as c', 'c.paciente','=','p.id')
								->join('lista_transito as l','l.caso','=','c.id')
								->where('p.rut',$rut)
								->whereNull('l.fecha_termino')
								->first();

			if($en_lista_transito != null){
				return response()->json(["error" => "El paciente se encuentra en lista de tránsito"]);
			}
        }



        /*Si es recien nacido y solo se tiene el rut de la madre*/
    	if($rut == null){
    	    $paciente=null;
            //return response()->json("hola23");
    		$rn=$request->input("rn");
    		if($rn=='si'){
                //return response()->json("hola23");
        	   $rutMadre=$request->input("rutMadre");
        	   $dvMadre=$request->input("dvMadre");
        	   $Madre="Rn/".$rutMadre."-".$dvMadre;

        	   $nacido=DB::table( DB::raw("(SELECT p.id from pacientes as p where p.nombre='$nombre' and p.apellido_paterno='$apellido_paterno' and p.apellido_materno='$apellido_materno' and p.rn='$Madre') as res"))
        						->get()->count();
                //return response()->json($nacido);
        	   if($nacido!=0){
                    return response()->json(["error" => "El Rn posee un caso abierto"]);}

    		}
    	}else{
    		$paciente = Paciente::where("rut", "=", $rut)->first();

    	}

        if($paciente != null){

            /*existen 2 formas de ver al paciente*/
    		$idPaciente = $paciente->id;
            if (is_null($idPaciente)) {
                /*Cuando viene desde Ingresar Paciente de la optimizacion, no lee al paciente*/
                $idPaciente = $paciente["id"];
			}

    		$caso = Caso::where("paciente", "=", $idPaciente)->whereNull("fecha_termino")->first();

    		//Lista de espera
    		$lista=DB::table( DB::raw("(SELECT l.id as lis,c.id as cas FROM casos as c,lista_espera as l where c.id=l.caso and c.paciente=$idPaciente and l.fecha_termino is null) as rea"))
    					->get();

    		if(!is_null($lista)){
    			foreach ($lista as $lis){
    			    /* var_dump($lis->lis);
    			    var_dump($lis->cas);
    				return response()->json(["error" => "El paciente is dead"]);*/

    				$caso2 = Caso::find($lis->cas);
    				$caso2->fecha_termino = date("Y-m-d H:i:s");
    				$caso2->motivo_termino = "alta";
    				//	$caso2->id_cie_10=$diagnostico_cie10;
    				$caso2->save();

    				$listas=ListaEspera::find($lis->lis);
    				$listas->fecha_termino=date("Y-m-d H:i:s");
    				$listas->motivo_salida="hospitalización";
    				$listas->save();
    			}
    		}

    		/*Si el paciente tiene un caso abierto (EN CASO DE QUE NO QUIERAS QUE SE HABRA UN NUEVO CASO Y SE OMITA LA DERIVACION, ]DEBES ELIMINAR HASTA ABAJO)*/
    		if($caso != null){
                $case = $caso->id;

    			$cosa = $caso->establecimiento;
    			$deriva = Derivacion::where("caso", "=", $case)->whereNull("fecha_cierre")->first();



    			if($deriva != null){
    				$derivar=$deriva->id;
    				$derivacio=Derivacion::find($derivar);
    				$derivacio->motivo_cierre="aceptado";
    				$derivacio->fecha_cierre=date("Y-m-d H:i:s");
    				$derivacio->comentario= "cerrado por codigo 1195";
    				$derivacio->save();

    				$casa = Caso::find($case);
    				$casa->fecha_termino = date("Y-m-d H:i:s");
    				$casa->motivo_termino = "traslado externo";
    				$casa->save();

    				$Ocupa = HistorialOcupacion::where("caso", "=", $case)->whereNull("fecha_liberacion")->first();

    				if($Ocupa != null){
                        		$History=$Ocupa->id;
    					$liberaH=HistorialOcupacion::find($History);
    					$liberaH->fecha_liberacion= date("Y-m-d H:i:s");
    					$liberaH->motivo="traslado externo";
    					$liberaH->save();
    				}
    			}else{
    				if($cosa){
    					$casa = Caso::find($case);
    					$casa->fecha_termino = date("Y-m-d H:i:s");
    					$casa->motivo_termino = "alta";
    					//	$casa->id_cie_10=$diagnostico_cie10;
    					$casa->save();
    				}else{
                        if($lista == null)
                            return response()->json(["error" => "El paciente tiene un caso abierto"]);
                    }
    			}
    		}
            /*HASTA AQUI DEBES ELIMINAR*/
        }


    	if($caso_social === 'si'){
    		$caso_social = true;
    	}else{
    		$caso_social = false;
    	}
    	if($extranjero === 'si'){
    		$extranjero = true;
    	}else{
    		$extranjero = false;
    	}

    		$tiene_caso_activo = false;
			$tiene_caso_activo_en_fecha = false;

    		try{
    			if
    			($tipo_procedencia == 1){
    				$procedencia = "Servicio de urgencias";
    			}elseif($tipo_procedencia == 2){
    				$procedencia = Establecimiento::findOrFail($procedencia)->nombre;
    			}elseif($tipo_procedencia == 3){
					/* Todo ok aquí */
				}elseif($tipo_procedencia == 4){
    				/* Todo ok aquí */
    			}elseif($tipo_procedencia == 5){
    				/* Todo ok aquí */
    			}elseif($tipo_procedencia == 6){
    				/* Todo ok aquí */
    			}else{
    				throw new Exception("Tipo de procedencia inválido");
    			}

    		}catch(Exception $e){
    			throw new MensajeException("Tipo de procedencia inválido");
    		}

    		try{

				//return "a".$request->input("fechaIngreso");
				//\Carbon\Carbon::createFromFormat("d-m-Y H:i:s", $request->input("fechaIngreso"))
    			$fecha_ingreso = \Carbon\Carbon::now();
    		}catch(Exception $e){
    			$fecha_ingreso = \Carbon\Carbon::now();
    		}

    		DB::beginTransaction();

    		try{
    			/* @var $caso Caso */
    			try{
    				if($rut === null || $rut === ''){
    					throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
    				}
    				$espera = ListaEspera::whereRaw("fecha= '$fecha_ingreso'")->whereNull("fecha_termino")->orderBy("fecha", "desc")->whereHas("casos", function($q) use ($rut){
    					$q->whereHas("pacienteCaso", function($q) use ($rut) {
    						$q->where("rut", $rut);
    					});
    				})->firstOrFail();
    				$caso = $espera->casos()->firstOrFail();
    				$espera->fecha_termino = $fecha_ingreso;
    				$espera->motivo_salida = 'hospitalización';
    				$espera->comentario = "Ingresado por interfaz de mapa de camas";
    				$espera->save();

    			}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    				$caso = new Caso();
					$caso->fecha_ingreso = $fecha_ingreso;
					$caso->fecha_ingreso2 =$fecha_solicitud;
					$caso->ficha_clinica = $ficha_clinica;
					$caso->dau = $dau;
    				//$caso->diagnostico = $diagnostico;
    				//$caso->id_cie_10=$diagnostico_cie10;
    				$caso->id_medico = $medico;
					$caso->caso_social = $caso_social;
					$caso->id_usuario = Session::get('usuario')->id;
    			}

    			/* @var $pac Paciente */
    			if($rut === '' ){
    				$pac = new Paciente();
    				$rn=$request->input("rn");
    				if($rn=='si')
    				{
    					$rutMadre=$request->input("rutMadre");
    					$dvMadre=$request->input("dvMadre");

    					$Madre="Rn/".$rutMadre."-".$dvMadre;
    					$pac->rn=$Madre;
    				}

    			}else {

    				$pac = Paciente::where("rut", $rut)->first();

    				if($pac === null){
    					$pac = new Paciente();
    					$pac->rut = $rut;
    				}
    				else{
    					/* paciente ya existe */
    					$c = $pac->casoActivoEnFecha($fecha_ingreso);
                        /* si $c no es null significa que hay caso activo en la fecha, y su fechatermino es
                         null quiere decir que ese caso está abierto. */
    					if($c !== null && $c->fecha_termino === null) {
    						$e = $c->establecimiento()->first();

    						if ($e !== null && $e->id === Session::get("idEstablecimiento")) {
    							return response()->json([
                                    "error" => "El paciente se encuentra en este establecimiento. Debe hacer un traslado interno"]);
    						} elseif ($e !== null && $e->id !== Session::get("idEstablecimiento")) {
    							$c->cerrar("traslado externo", "{$fecha_ingreso}");
    							$c->fecha_termino = "{$fecha_ingreso}";
    							$c->detalle_termino = Session::get("nombreEstablecimiento");
    							$c->motivo_termino = "traslado externo";
    							$c->save();
    							try {
    								$d = $c->derivacionesActivas()->whereHas("unidadDestino", function ($q) {
    									$q->where("establecimiento", Session::get("idEstablecimiento"));
    								})->orderBy("fecha", "desc")->where("fecha", "<=", "{$fecha_ingreso}")->firstOrFail();
    								$d->cerrar("aceptado", "Aceptado automatico al ingresar", $fecha_ingreso);
    								$d->save();
    							}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){

    							}
    							$caso->procedencia = 3;
    							$caso->detalle_procedencia = "Traslado externo automático";
    						}
    					}
    					else{
    						$caso->procedencia = $tipo_procedencia;
    						$caso->detalle_procedencia = $procedencia;
    					}
    				}
    				$pac->dv = $dv;
    			}

    			/* Hay dos casos: el caso activo en la fecha y el caso no existente. */
    			$pac->nombre = $nombre;
    			$pac->apellido_paterno = $apellido_paterno;
    			$pac->apellido_materno = $apellido_materno;

                $pac->nombre_social = $nombre_social;

				$pac->sexo=$sexo;
				if($request->fechaNac == ""){
					$pac->fecha_nacimiento=null;
				}else{
					$pac->fecha_nacimiento=date("Y-m-d", strtotime(trim($request->input("fechaNac"))));
				}

                	$pac->extranjero = $extranjero;
					//$pac->id_cie_10=$diagnostico_cie10;
				$pac->calle=trim($request["calle"]);
				if(trim($request->input("numeroCalle")) == ""){
					$pac->numero=null;
				}else{
					$pac->numero=trim($request->input("numeroCalle"));
				}
				//$pac->numero=trim($request["numeroCalle"]);
				$pac->observacion=trim($request["ObservacionCalle"]);
				$pac->id_comuna= $request["comuna"] == ''? null:$request["comuna"];
				if(trim($request->input("latitud")) == ""){
					$pac->latitud=null;
				}else{
					$pac->latitud=trim($request->input("latitud"));
				}
				if(trim($request->input("longitud")) == ""){
					$pac->longitud=null;
				}else{
					$pac->longitud=trim($request->input("longitud"));
				}
				//$pac->latitud=trim($request["latitud"]);
				//$pac->longitud=trim($request["longitud"]);
				if($request->input("rango") == "seleccione"){
					$pac->rango_fecha = null;
				}else{
					$pac->rango_fecha = $request->input("rango");
				}
    			$pac->save();

                /*original*/
    			$pac->insertarCaso($caso);

                //prueba
                /* $hola = $pac->insertarCaso($caso);
				return $hola; */

    			$caso->paciente = $pac->id;
    			$caso->procedencia = $tipo_procedencia;
    			$caso->detalle_procedencia = $procedencia;
				$caso->fecha_ingreso = $fecha_ingreso;
				$caso->fecha_ingreso2 = $fecha_solicitud;
				$caso->ficha_clinica = $ficha_clinica;
				$caso->dau = $dau;
    			$caso->establecimiento = Session::get("idEstablecimiento");
    			$caso->especialidad = $request->input("especialidad");
				$caso->prevision = $request->input("prevision");
				$caso->id_usuario = Session::get('usuario')->id;
				$caso->id_unidad = $servicio;
				$caso->id_unidad = $medico;

    			$caso->save();

    			/*
    			Parche
    			 */

                foreach ($diagnosticos as $key => $value) {
					if($value != "null" ){


						$d = new HistorialDiagnostico();
						$d->caso = $caso->id;
						$d->fecha = $caso->fecha_ingreso;
						$d->diagnostico = $value;
						$d->id_cie_10 = $hidden_diagnosticos[$key];
						$d->save();
						}
					}
					//OTRO DIAGNOSTICO
					$d = new HistorialDiagnostico();
					$d->caso = $caso->id;
					$d->fecha = $caso->fecha_ingreso;
					$d->diagnostico = $diagnostico;
					$d->save();


    			$casoDiag = HistorialDiagnostico::where("caso", $caso->id)
					                ->orderBy("fecha", "desc")
					                ->first();

                if(!$casoDiag)
                {
                	$d = new HistorialDiagnostico();
    	            $d->caso = $caso->id;
    	            $d->fecha = $caso->fecha_ingreso;
    	            $d->diagnostico = $caso->diagnostico;
    	            $d->save();
                }



    			//$pac->registrarEvolucionPaciente($caso->id, $request->input("riesgo"));
    			//return response()->json($request);
                if ($request->input("riesgo") != null || $request->input("riesgo") != '') {
                    $riesgo= new Riesgo;
                    $riesgo->dependencia1 = $request->dependencia1;
                    $riesgo->dependencia2 = $request->dependencia2;
                    $riesgo->dependencia3 = $request->dependencia3;
                    $riesgo->dependencia4 = $request->dependencia4;
                    $riesgo->dependencia5 = $request->dependencia5;
                    $riesgo->dependencia6 = $request->dependencia6;
                    $riesgo->riesgo1 = $request->riesgo1;
                    $riesgo->riesgo2 = $request->riesgo2;
                    $riesgo->riesgo3 = $request->riesgo3;
                    $riesgo->riesgo4 = $request->riesgo4;
                    $riesgo->riesgo5 = $request->riesgo5;
                    $riesgo->riesgo6 = $request->riesgo6;
                    $riesgo->riesgo7 = $request->riesgo7;
                    $riesgo->riesgo8 = $request->riesgo8;
                    $riesgo->categoria = $request->input("riesgo");
                    $riesgo->save();

                    $id_riesgo = $riesgo->id;
                }else{
                    $id_riesgo = null;
                }

                $ev = new EvolucionCaso();
                $ev->caso = $caso->id;
                $ev->fecha = $caso->fecha_ingreso;
                if ($request->input("riesgo") != null || $request->input("riesgo") != '') {
                    $ev->riesgo = $request->input("riesgo");
                }else{
                    $ev->riesgo = null;
                }

                $ev->riesgo_id = $id_riesgo;
                $ev->save();

    			for($i = 0; $i < 3; $i++){
    				$cat = $request->input("cat-$i");
    				if(empty($cat)) continue;
    				try{
    					$ev = new EvolucionCaso();
    					$ev->caso = $caso->id;
    					$ev->riesgo = $cat;
    					$ev->fecha = $fecha_ingreso->copy()->addDays($i+1)->startOfDay();
    					$ev->save();
    				}
    				catch(Exception $e){
    					continue;
    				}
    			}

    			if($tipo=="ingresar"){
    				//$cama = Cama::findOrFail($idCama);
    				//$cama->asignarCaso($caso)->save();
    				$mensaje= "Se ha ingresado el paciente";

    			}elseif($tipo == "reservar"){
    				$reserva=new Reserva;
    				$reserva->cama=$request->input("cama");
    				$reserva->fecha=$fecha_ingreso;
    				$reserva->tiempo=$request->input("horas")." hours";
    				$reserva->motivo=trim($request->input("motivo"));
    				$reserva->caso=$caso->id;
    				$reserva->save();
    				$mensaje= "Se ha realizado la reserva";
    			}
    			else{
    				throw new Exception("Tipo de ingreso inválido");
    			}

    		} catch(Illuminate\Database\QueryException $ex){
    			DB::rollback();
    			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error interno"));
    		}
    		catch(MensajeException $ex){
    			DB::rollback();
    			return response()->json(array("error" => $ex->getMessage()));
    		}
    		catch(Exception $e){
    			DB::rollBack();
    			return response()->json(array("error" => $ex->getMessage()));
    		}
    		DB::commit();








    	//return response()->json($caso->id);
    	        //
		$fecha_actual = date("d-m-Y");


		if($request->fechaNac != ""){
			$separados = explode("-", $request->fechaNac);

			//dias de edad
			$feha_nacimiento=date_create($separados[2]."-".$separados[1]."-".$separados[0]);
			$actual=date_create(date('Y-m-d'));
			$diff=date_diff($feha_nacimiento,$actual);
			$dias = $diff->days;
		}else{
			$dias = -1;
		}


		//id del hospital

		$establecimiento = DB::table('establecimientos')
                        ->select('establecimientos.id','establecimientos.nombre')
                        ->where('establecimientos.id','=',  Session::get("idEstablecimiento"))
                        ->first();

		//Riesgo

        //UnidadEnEstablecimiento
        if ($request->riesgo == null ) {
            $riesgo = 'nada';
        }else{
            $riesgo = $request->riesgo;
        }


        //return response()->json( Session::get("idEstablecimiento"));
		//return $request;
		$unidades = "";
        if ($request->unidadA[0] == "0" || $request->unidadA[0] == null || $request->unidadA == "") {

        	//uniades del hospital
    		$unidades_seleccionadas = DB::table('unidades')
                ->select('unidades.id as id')
                ->join('servicios_recibidos', 'unidades.id','=','servicios_recibidos.unidad')
                ->join('unidades_en_establecimientos','servicios_recibidos.unidad_en_establecimiento','=','unidades_en_establecimientos.id')
                ->join('establecimientos','unidades_en_establecimientos.establecimiento','=','establecimientos.id')
               /*  ->where('establecimientos.id','=', Session::get("idEstablecimiento")) */
                ->groupBy('unidades.id')
				->get();

            foreach ($unidades_seleccionadas as $unidad ) {
        		$unidades .= $unidad->id.",";
        	}

        }else{

        	foreach ($request->unidad as $unidad ) {
        		$unidades .= $unidad.",";
        	}
		}

		/* if($unidades == ""){
			return response()->json(["error" => "No existe mapa de camas"]);
		} */
        //return response()->json($unidades);

		//ejectuar python con la informacion
		$rutaPython = '/usr/bin/python';
		//comprobar si existe carpeta tmp

		$tmp = file_exists(public_path().'/python/tmp');
		//return response()->json($tmp);
		if($tmp != true){
			mkdir(public_path().'/python/tmp', 0777);
		}




        $rutaOptimizacion = public_path().'/python/coreIngreso.py '.$dias.' '.$establecimiento->id.' '.$riesgo.' '.$unidades.' '.env("DB_DATABASE").' '. env("DB_USERNAME").' '.env("DB_PASSWORD").' '.env("DB_PORT").' 2>&1';


		$rutaCompleta = $rutaPython." ".$rutaOptimizacion;

		/* "/usr/bin/python /opt/lampp/htdocs/SIGICAM/public/python/coreIngreso.py 6 8 nada 154,164,148,169, sigicam_prueba gestion gestion 5432 2>&1" */
		/* return response()->json($rutaCompleta); */


		exec($rutaPython." ".$rutaOptimizacion , $output );
        //devuelve id cama y puntaje


		/* return response()->json($output); */

		$separados = explode("[", $output[40]);


		//return response()->json($separados);
		//$informacionCamas = array();
		$camas = array();
		foreach ($separados as $key=>$separado) {

			if ($separado == null) {
				continue;
			}else{

				if($separado == "]"){
					break;
				}else{
					// dd($separado);
					$comas =  explode(",", $separado);
					//return response()->json($comas);
					$informacion = DB::table('camas')
							->select('camas.id as id','camas.id_cama as nombre_cama', 'tipos_cama.nombre as tipo_cama', 'salas.nombre as nombre_sala','unidades.nombre as unidad','establecimientos.nombre as establecimiento','unidades_en_establecimientos.alias as alias')
							->leftjoin('t_historial_ocupaciones','t_historial_ocupaciones.cama','=','camas.id')
							->leftjoin('salas', 'salas.id', '=', 'camas.sala')
							->leftjoin('tipos_cama', 'tipos_cama.id', '=','camas.tipo')
							->leftjoin('unidades_en_establecimientos','unidades_en_establecimientos.id', '=', 'salas.establecimiento')
							->leftjoin('servicios_recibidos','servicios_recibidos.unidad_en_establecimiento', '=', 'unidades_en_establecimientos.id')
							->leftjoin('unidades','unidades.id', '=', 'servicios_recibidos.unidad')
							->join('establecimientos', 'establecimientos.id','=', 'unidades_en_establecimientos.establecimiento')
							->where('camas.id','=', intval($comas[0]))
							//->where('t_historial_ocupaciones.fecha_liberacion','!=' ,null)
							->groupBy('camas.id','camas.id_cama', 'tipos_cama.nombre', 'salas.nombre','unidades.nombre','establecimientos.nombre','unidades_en_establecimientos.alias')
							->get();
					// $cama->id = $comas[0];
					//return response()->json($informacion);
					$peso =explode("]", $comas[1]);

					// $informacionCamas[] = [
					//     "data" => $informacion
					// ];

					//dd($peso);
					// $cama->peso = $peso[0];
					if(intval($peso[0]) >= 0){
						$unidad = [];
						if (count($informacion) != 0) {
							foreach ($informacion as $key => $info) {
								$idCama = $info->id;
								$nombreCama = $info->nombre_cama;
								$nombreSala = $info->nombre_sala;
								$nombreEstablecimiento = $info->establecimiento;
								$tipoCama = $info->tipo_cama;
								$unidad[] = [
									"unidad" => $info->unidad
								];
								$unidad_en_hospital = $info->alias;
							}
							$camas[]=[
								"idCama"                => $idCama,
								"nombreCama"            => $nombreCama,
								"nombreSala"            => $nombreSala,
								"nombreEstablecimiento" => $nombreEstablecimiento,
								"tipoCama"              => $tipoCama,
								"unidad"                => $unidad,
								"peso"                  => $peso[0],
								"unidad_en_hospital"    => $unidad_en_hospital
							];
						}

					}
				}

			}
		}
        //return response()->json($informacionCamas);
		$max = 0 ;
		if(count($camas) > 1){
			foreach ($camas as $fila) {
			  	if ($fila['peso'] > $max) {
			  		$max = $fila['peso'];
			  	}
				$ordenamiento[] = $fila['peso'];

			}
			array_multisort($ordenamiento, SORT_DESC ,$camas);
		}else{
			return response()->json(["error" => "No se han encontrado camas"]);
		}

		//return response()->json($max);

		foreach ($camas as $cama) {
			if ($establecimiento->nombre == $cama['nombreEstablecimiento']) {
				$boton = "<button class= 'btn btn-success' type='button' onclick='trasladoInterno(".$cama['idCama'].",".$caso.")' data-cama=".$cama['idCama'].">Ingresar Paciente</button>";
			}else{
				$boton = "<button id=".$cama['idCama']." class= 'btn btn-danger' type='button' onclick='modalDerivacion(\"".$cama['nombreCama']."\",\"".$cama['nombreSala']."\",\"".$cama['unidad_en_hospital']."\",".$cama['idCama'].",".$caso.")'>Derivación</button>";
			}
			$unidades = "";
			foreach ($cama['unidad'] as $key => $unidad) {
				if ($key == 0) {
					$unidades .= $unidad['unidad'];
				}else{
					$unidades .= ", ".$unidad['unidad'];
				}

			}

			$peso = (5*$cama['peso'])/$max;

			$cama['peso'] = "
                <p style='color: #BB9A02; font-size:15px;'><input type='hidden' class='star' value='".$peso."' disabled='disabled' /></p>
                <p hidden>".$cama['peso']."</p>

                <script>
                    $('.star').rating();
                </script>";

			$response[] = array($cama['peso'],$cama['nombreEstablecimiento'],$cama['unidad_en_hospital']/* , $unidades */, $cama['nombreSala'], $cama['nombreCama'],$boton );
		}

		return response()->json($response);


	}

	public function getSir(Request $request){
		//ejectuar python con la informacion
		//return response()->json($request);
		$rutaPython = '/usr/bin/python';
		/* $n = 1000;
		$b = 0.2;
		$g = 0.1;
		$t = 160; */

		$n = $request->input('n');
		$b = $request->input('beta');
		$g = $request->input('gama');
		$t = $request->input('t');

		//$rutaOptimizacion = public_path().'/python/SIR/prueba.py '.$diff->days.' '.$establecimiento->id.' '.$riesgo.' '.$unidades.' 2>&1';
		//$rutaOptimizacion = public_path().'/python/SIR/SIR.py '.$n.' '.$b.' '.$g.' '.$t.' 2>&1';
		$rutaOptimizacion = public_path().'/python/SIR/SIR.py '.$n.' '.$b.' '.$g.' '.$t.' 2>&1';
		$rutaCompleta = $rutaPython." ".$rutaOptimizacion;

		//exec($rutaPython." ".$rutaOptimizacion , $output );
		$result = json_decode(exec($rutaPython." ".$rutaOptimizacion), true);

		//return $result;
		$resultado = array();
		$resultado = array("S"=>$result["data"][0], "I"=>$result["data"][1], "R"=>$result["data"][2]);
		return $resultado;
        //devuelve id cama y puntaje
		//return response()->json($output);

		//$separados = explode("[[", $result["data"]);
		//return array_chunk($output, "[");
		//$a = json_encode($output);
	}

    public function optimizacion(Request $request){
    	/*REASIGNACION DE CAMA*/

		$camas = array();

		$establecimiento = DB::table('casos')
                        ->select('establecimientos.nombre', 'establecimientos.id', 'establecimientos.latitud', 'establecimientos.longitud')
                        ->join('establecimientos','establecimientos.id','=','casos.establecimiento')
                        ->where('casos.id','=', $request->idCaso)
						->first();



        $establecimientos = DB::table('establecimientos')
                        ->get();

        $minutos_establecimientos = [];

        $rutaPython = '/usr/bin/python';
        $rutaOptimizacion = public_path().'/python/core.py '.$request->idCaso.' '.$establecimientos->count().' '.env("DB_DATABASE").' '. env("DB_USERNAME").' '.env("DB_PASSWORD").' '.env("DB_PORT");

		/* return response()->json($request->idCaso); */
        foreach ($establecimientos as $key => $establecimiento_x) {
			/* return response()->json('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$establecimiento->latitud.','.$establecimiento->longitud.'&destinations='.$establecimiento_x->latitud.','.$establecimiento_x->longitud.'&key=AIzaSyAeEOKLHeC8EzWrPJpqyBDJYAMbZRfV09o'); */
			$respuesta = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$establecimiento->latitud.','.$establecimiento->longitud.'&destinations='.$establecimiento_x->latitud.','.$establecimiento_x->longitud.'&key=AIzaSyAeEOKLHeC8EzWrPJpqyBDJYAMbZRfV09o'));


            $rutaOptimizacion .= " ".$establecimiento_x->id." ".$respuesta->rows[0]->elements[0]->duration->value;

        }

        $rutaOptimizacion .= ' 2>&1';

        //return response()->json($rutaOptimizacion);


        $caso =  Caso::find($request->idCaso);

		$cama_antigua = DB::table("casos as C")
						->leftjoin("t_historial_ocupaciones as  T", "T.caso", "=","C.id")
						->where('C.id','=', $request->idCaso)
						->whereNull('T.fecha_liberacion')
						->get();

		/* Antigua consulta cama_antigua
			DB::table('pacientes')
            ->select('t_historial_ocupaciones.cama')
			->join('casos','pacientes.id','=','casos.paciente')
			->join('t_historial_ocupaciones','t_historial_ocupaciones.caso','=','casos.id')
			->where('casos.id','=', $request->idCaso)
			->whereNull('casos.fecha_liberacion')
			->get(); */



        $rutaCompleta = $rutaPython." ".$rutaOptimizacion;

		exec($rutaPython." ".$rutaOptimizacion , $output );

		/* "/usr/bin/python /opt/lampp/htdocs/SIGICAM/public/python/core.py 108449 5 sigicam_prueba gestion gestion 5432 17 6086 10 6875 1 8117 11 6117 8 0 2>&1" */
        /* return response()->json($rutaCompleta); */

		/* return response()->json($output); */
		$separados = explode("[", $output[47]);


		foreach ($separados as $key=>$separado) {
			if ($separado == null) {
				continue;
			}else{

				if($separado == "]"){
					break;
				}else{

					$comas =  explode(",", $separado);

					$informacion = DB::table('camas')
							->select('camas.id as id','camas.id_cama as nombre_cama', 'tipos_cama.nombre as tipo_cama', 'salas.nombre as nombre_sala','unidades.nombre as unidad','establecimientos.nombre as establecimiento', 'unidades_en_establecimientos.alias')
							->leftjoin('t_historial_ocupaciones','t_historial_ocupaciones.cama','=','camas.id')
							->leftjoin('salas', 'salas.id', '=', 'camas.sala')
							->leftjoin('tipos_cama', 'tipos_cama.id', '=','camas.tipo')
							->leftjoin('unidades_en_establecimientos','unidades_en_establecimientos.id', '=', 'salas.establecimiento')
							->leftjoin('servicios_recibidos','servicios_recibidos.unidad_en_establecimiento', '=', 'unidades_en_establecimientos.id')
							->leftjoin('unidades','unidades.id', '=', 'servicios_recibidos.unidad')
							->leftjoin('establecimientos', 'establecimientos.id','=', 'unidades_en_establecimientos.establecimiento')
							->where('camas.id','=', intval($comas[0]))
							//->whereNull('t_historial_ocupaciones.fecha_liberacion')
							->groupBy('camas.id','camas.id_cama', 'tipos_cama.nombre', 'salas.nombre','unidades.nombre','establecimientos.nombre', 'unidades_en_establecimientos.alias')
							->get();

					$peso =explode("]", $comas[1]);
					//return response()->json($informacion);
					if(intval($peso[0]) > 0){
						$unidad = [];
						if (count($informacion) != 0) {
							foreach ($informacion as $key => $info) {
								$idCama = $info->id;
								$nombreCama = $info->nombre_cama;
								$nombreSala = $info->nombre_sala;
								$nombreEstablecimiento = $info->establecimiento;
								$tipoCama = $info->tipo_cama;
								$unidad[] = [
									"unidad" => $info->unidad
								];
												$unidad_en_hospital = $info->alias;
							}
							$camas[]=[
								"idCama"                => $idCama,
								"nombreCama"            => $nombreCama,
								"nombreSala"            => $nombreSala,
								"nombreEstablecimiento" => $nombreEstablecimiento,
								"tipoCama"              => $tipoCama,
								"unidad"                => $unidad,
								"peso"                  => $peso[0],
								"unidad_en_hospital"    => $unidad_en_hospital
							];
						}

					}

				}
			}
		}

		$max = 0;

		if(count($camas) > 1){

			foreach ($camas as $fila) {
				if ($fila['peso'] > $max) {
					$max = $fila['peso'];
				}
				$ordenamiento[] = $fila['peso'];
			}

			array_multisort($ordenamiento, SORT_DESC ,$camas);
		}else{
			return response()->json(["error" => "No se han encontrado camas"]);
		}


		foreach ($camas as $cama) {

			/* return response()->json($cama_antigua); */
			if ($establecimiento->nombre == $cama['nombreEstablecimiento']) {
				$boton = "<button class= 'btn btn-success' type='button' onclick='trasladoInterno(".$cama['idCama'].",".$caso.",".$cama_antigua[0]->cama.")' data-cama=".$cama['idCama'].">Traslado Interno</button>";
			}else{
				$boton = "<button class= 'btn btn-danger' type='button' onclick='modalDerivacion(\"".$cama['nombreCama']."\",\"".$cama['nombreSala']."\",\"".$cama['unidad_en_hospital']."\",".$cama['idCama'].",".$caso.")'>Derivación</button>";
			}
            //return response()->json($boton);
			$unidades = "";
			foreach ($cama['unidad'] as $key => $unidad) {
				if ($key == 0) {
					$unidades .= $unidad['unidad'];
				}else{
					$unidades .= ", ".$unidad['unidad'];
				}

			}

			$peso = (5*$cama['peso'])/$max;

			$cama['peso'] = "
                <p style='color: #BB9A02; font-size:15px;'><input type='hidden' class='star' value='".$peso."' disabled='disabled' /> </p>
                <p hidden>".$cama['peso']."</p>
                <script>
                    $('.star').rating();
                </script>";

		    $response[] = array($cama['peso'],$cama['nombreEstablecimiento'],$cama['unidad_en_hospital']/* , $unidades */, $cama['nombreSala'], $cama['nombreCama'],$boton );
		}
        return response()->json($response);

	}

	public function ingresarPacienteOptimizacion(Request $request){
		/*CUANDO EL PACIENTE SE ESTA INGRESANDO A UNA CAMA*/
        //return response()->json($request);
        //return response()->json($request);
		$a = $request->caso;


        $respuesta = DB::table('pacientes')
                    ->select('t_historial_ocupaciones.motivo')
                    ->leftJoin('casos','casos.paciente','=','pacientes.id')
                    ->leftJoin('t_historial_ocupaciones','t_historial_ocupaciones.caso','=','casos.id')
                    ->leftJoin('camas','t_historial_ocupaciones.cama','=','camas.id')
                    ->where('pacientes.id','=' ,$a['paciente'])
                    ->whereNotNull('camas.id')
                    ->whereNull('t_historial_ocupaciones.motivo')
                    ->groupBy('t_historial_ocupaciones.motivo')
                    ->get();

        $cantidad = $respuesta->count();
        if ($cantidad > 0) {
            //paciente existe en una cama
            $mensaje= "El paciente se encuentra en otra cama";
            return response()->json(array("exito" =>$mensaje));
        }
        else{
            DB::beginTransaction();
            //ingresar paciente
            $caso = Caso::findOrFail($a["id"]);
            $cama = Cama::findOrFail($request->cama);
            /*ORIGINAL*/
            $cama->asignarCaso($caso)->save();

            $mensaje= "Se ha ingresado el paciente";

            DB::commit();

            return response()->json(array("exito" =>$mensaje));
        }

	}

    public function registrarTrasladoOptimizacion(Request $request){
        /*CUANDO EL PACIENTE SE ESTA DERIVANDO A UNA CAMA*/
        //return response()->json($request);
        $paciente = DB::table('pacientes')
                        ->select('pacientes.id','pacientes.rut','pacientes.nombre','pacientes.apellido_paterno','pacientes.apellido_materno','pacientes.dv','pacientes.fecha_nacimiento','casos.prevision','pacientes.sexo','unidades_en_establecimientos.alias','camas.id_cama')
                        ->join('casos','casos.paciente','=','pacientes.id')
                        ->leftjoin('t_historial_ocupaciones','t_historial_ocupaciones.caso','=','casos.id')
                        ->leftjoin('camas','t_historial_ocupaciones.cama','=','camas.id')
                        ->leftjoin('salas','salas.id','=','camas.sala')
                        ->leftjoin('unidades_en_establecimientos','unidades_en_establecimientos.id','=','salas.establecimiento')
                        ->where('casos.id','=',$request->idCaso)
                        ->first();


        $hospitalDestino = DB::table('camas')
                        ->select('camas.id as idCama','salas.id as idSala','salas.nombre as nombreSala','unidades_en_establecimientos.id as idUnidad','unidades_en_establecimientos.alias', 'establecimientos.id as idHospital', 'establecimientos.nombre as nombrehospital')
                        ->join('salas','salas.id','=','camas.sala')
                        ->join('unidades_en_establecimientos','unidades_en_establecimientos.id','=','salas.establecimiento')
                        ->join('establecimientos','establecimientos.id','=','unidades_en_establecimientos.establecimiento')
                        ->where('camas.id', '=', $request->idCama)
                        ->first();
        //return response()->json($hospitalDestino);

        DB::beginTransaction();


        $destino = storage_path().'/data/derivaciones';
        try{

            $rut=trim($paciente->rut);
            //$tiene = Derivacion::tieneDerivaciones($paciente->id);

            $encontrados = DB::table('casos')
                            ->join('derivaciones', 'derivaciones.caso','=','casos.id')
                            ->where('casos.paciente','=',$paciente->id)
                            ->whereNull('casos.fecha_termino')
                            ->first();

            if(!is_null($encontrados)){
                $tiene = true;
            }else{
                $tiene = false;
            }


            //return response()->json($request);
            $idCaso = $request->idCaso;
            if(!$tiene){
                $derivar                    = new Derivacion;
                $derivar->caso              = $idCaso;
                $derivar->usuario           = Auth::user()->id;
                $derivar->establecimiento   = $request->idEstablecimiento;
                $derivar->fecha             = DB::raw("date_trunc('seconds', now())");
                $derivar->destino           = $hospitalDestino->idUnidad;
                $derivar->save();
                $idDerivacion=$derivar->id;




                $EstableciMensaje=DB::table( DB::raw("(select e.nombre from derivaciones as d,unidades_en_establecimientos as u, establecimientos as e where d.id=$idDerivacion
                and u.establecimiento=e.id and u.id=d.destino) as ra"))->get();

                    foreach ($EstableciMensaje as $Messages)
                    {
                        $establecimi=$Messages->nombre;
                    }


                $mensaje=new MensajeDerivacion;
                $mensaje->derivacion=$idDerivacion;
                $mensaje->usuario=Auth::user()->id;
                $mensaje->fecha=DB::raw("date_trunc('seconds', now())");
                $mensaje->contenido=trim($request->input("texto"));
                $mensaje->asunto=trim($request->input("asunto"));
                $mensaje->destino = $establecimi;
                $mensaje->save();

                $files=$request->file("files");
                $destino = "{$destino}/{$idCaso}/{$idDerivacion}";
                File::makeDirectory($destino, 0775, true, true);

                if($request->hasFile('files'))
                {
                    foreach($files as $file){
                        if(empty($file)) continue;
                            $filename = $file->getClientOriginalName();
                        $file->move($destino, $filename);

                        $documento=new Documento;
                        $documento->derivacion=$idDerivacion;
                        $documento->recurso="{$destino}/{$filename}";
                        $documento->save();
                    }
                }

                DB::commit();
                return response()->json(["exito" => "La derivación se ha realizado exitosamente"]);
            }
            else
            {
                return response()->json(["error"=>"El paciente ya posee una derivación"]);
            }

        }
        catch(Exception $ex){
            DB::rollback();
            return response()->json(["error"=>"Uno de los archivos excede el tamaño permitido (5Mb) ", "ex"=>$ex->getMessage()]);
        }
    }










}

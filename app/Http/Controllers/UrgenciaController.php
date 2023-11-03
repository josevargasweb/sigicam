<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListaEspera;
use Session;
use DB;
use App\Models\HospitalizacionDomiciliaria;
use App\Models\Caso;
use App\Models\THistorialOcupaciones;
use App\Models\Paciente;
use Exception;
use App\Models\HistorialDiagnostico;
use App\Models\Riesgo;
use App\Models\EvolucionCaso;
use Log;
use Consultas;
use Auth;
use App\Models\ListaTransito;
use App\Models\PreAlta;
use App\Models\HistorialOcupacion;
use App\Models\ListaDerivados;
use App\Models\ListaDerivadosComentarios;
use App\Models\Examen;
use App\Models\DomiciliariaComentario;
use Carbon\Carbon;
use View;
use App\Models\Subprocedencia;
use App\Models\ConfiguracionVisitas;
use App\Models\HistorialOcupacionesVista;

use App\Models\ListaPabellon;
use App\Models\Usuario;
use App\Models\Cama;
use App\Models\Sala;
use App\Models\TipoUsuario;
use \App\Models\Establecimiento;
use App\Models\EstablecimientosExtrasistema;
use Illuminate\Support\Str;
use Excel;
use DateTime;

use Form;
use PDF;
use Illuminate\Support\Arr;

use App\Models\EvolucionEspecialidad;
use App\Models\EvolucionAtencion;
use App\Models\EvolucionAcompanamiento;
use App\Models\Patologias;
use App\Models\PatologiaCasos;
use App\Models\FormularioDerivado;
use App\Models\Comuna;
use App\Models\UnidadEnEstablecimiento;
use App\Models\CamaTemporal;
use Illuminate\Support\Collection;

Use App\Models\Medico;
use App\Models\Procedencia;

class UrgenciaController extends Controller{

	public function obtenerListaPaciente(Request $request){
		$establecimieno =   Auth::user()->establecimiento;


		$data = DB::select(DB::raw(
				"SELECT c.id_cama, s.nombre as sala, p.nombre, p.apellido_paterno, p.apellido_materno, d.diagnostico,
					(EXTRACT(epoch FROM (now()::timestamp - thov.updated_at))/3600)::integer  as tiempo_espera
							FROM unidades_en_establecimientos uee
					JOIN area_funcional af on uee.id_area_funcional = af.id_area_funcional
					JOIN salas s on uee.id = s.establecimiento
					JOIN camas c on s.id = c.sala
					JOIN t_historial_ocupaciones_vista thov on c.id = thov.cama
					JOIN casos c2 on c2.id = thov.caso
					JOIN pacientes p on c2.paciente = p.id
					JOIN diagnosticos d on c2.id = d.caso
			WHERE url = 'urgencia'
			and thov.fecha_liberacion IS NULL
			and uee.establecimiento = ".$establecimieno."
				and now()::timestamp - thov.updated_at > interval '12 hour'"));
					return ["data" => $data];
	}

	public function registrarRiesgos(Request $request){
		try{
			DB::beginTransaction();
				foreach ($request->categorizacion as $key => $categorizacion) {	
					Log::info($request);		
					$caso = $request->categorizacion_id[$key];

					if ($categorizacion != null) {
						$pacienteUbicacion = THistorialOcupaciones::select("u.id")
							->join("camas as c","c.id","t_historial_ocupaciones.cama")
							->join("salas as s","s.id","c.sala")
							->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
							->where("caso", $caso)
							->whereNull("t_historial_ocupaciones.fecha_liberacion")
							->first();

						$riesgo = new Riesgo;
						$riesgo->categoria = strtoupper($categorizacion);
						$riesgo->save();

						$ev = new EvolucionCaso();
						$ev->caso = $caso;
						$ev->fecha = Carbon::now();
						$ev->riesgo = strtoupper($categorizacion);
						$ev->riesgo_id = $riesgo->id;
						if($pacienteUbicacion){
							$ev->id_unidad = $pacienteUbicacion->id;//ide de la unidad en que se encuentra actualmente el paciente
						}else{
							$ev->id_unidad = null;
						}
						$ev->save();
					}

					if($request->especialidad_id[$key] != [] &&  $request->especialidad_id[$key] != 9 ){
						$esp = explode(",", $request->especialidad_id[$key]);
						//comprobar que sean distintos y modificarlos en caso contrario
						$this->modificarEspecialidades($caso, $esp);
					}
					
					if(isset($request->categoria_atencion[$key]) && $request->categoria_atencion[$key] != '' ){
						//comprobar que sean distintos y modificarlos en caso contrario
						$this->modificarAtencion($caso, $request->categoria_atencion[$key]);
					}

					if(isset($request->categoria_acompanamiento[$key]) && $request->categoria_acompanamiento[$key] != '' ){
					
						//comprobar que sean distintos y modificarlos en caso contrario
						$this->modificarAcompañamiento($caso, $request->categoria_acompanamiento[$key]);
					}

				

					
				}

			DB::commit();//commit//rollback
			return response()->json(array("exito" => "El paciente ha sido categorizado con exito"));

		}catch(\Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al categorizar al paciente"));
		}

	}

	public function modificarEspecialidades($caso, $especialidades){

		$especialidads_a = EvolucionEspecialidad::select('id_especialidad')->where("id_caso", $caso)->whereNull("fecha_termino")->get();
		$especialidades_actuales = array();
		foreach($especialidads_a as $esp){
			if($esp != ''){
				$especialidades_actuales [] = $esp->id_especialidad;
			}
			
		}		

		//valores agregar
		$array_agregar = array_diff($especialidades, $especialidades_actuales);
		//valores diferentes
		$array_sacar = array_diff($especialidades_actuales, $especialidades);
		//agregar
		foreach ($array_agregar as $agregar) {
			//si no se encuentra la espcialidad  y es distinta
			$esp_agregar = new EvolucionEspecialidad();
			$esp_agregar->fecha = Carbon::now();
			$esp_agregar->id_caso = $caso;
			$esp_agregar->id_especialidad = $agregar;
			$esp_agregar->usuario_asigna = Auth::user()->id;
			$esp_agregar->save();

		}

		//quitar
		foreach ($array_sacar as $sacar) {	
			//si no se encuentra la espcialidad  y es distinta
			$esp_quitar = EvolucionEspecialidad::where("id_caso", $caso)->where("id_especialidad",$sacar)->first();
			$esp_quitar->fecha_termino = Carbon::now();
			$esp_quitar->usuario_quita = Auth::user()->id;
			$esp_quitar->save();
			
		}

	}

	public function modificarAtencion($caso, $atenciones){

		$atencions_a = EvolucionAtencion::select('tipo_atencion')
		->where("id_caso", $caso)
		->whereNull("fecha_termino")
		->first();
		if($atenciones != '')
		{
			//valores quitar
			if (isset($atencions_a->tipo_atencion) && strcmp($atenciones, $atencions_a->tipo_atencion) !== 0) {
				$atencion_quitar = EvolucionAtencion::where("id_caso", $caso)->where("tipo_atencion",$atencions_a->tipo_atencion)
				->whereNull("fecha_termino")
				->first();
				$atencion_quitar->fecha_termino = Carbon::now();
				$atencion_quitar->usuario_quita = Auth::user()->id;
				$atencion_quitar->save();

			}


		//valores agregar
		$atencion_agregar = new EvolucionAtencion();
		$atencion_agregar->fecha = Carbon::now();
		$atencion_agregar->id_caso = $caso;
		$atencion_agregar->tipo_atencion = $atenciones;
		$atencion_agregar->usuario_asigna = Auth::user()->id;
		$atencion_agregar->save();
		}

	}

	public function modificarAcompañamiento($caso, $acompanamientos){
		$acompanamiento_a = EvolucionAcompanamiento::select('tipo_acompanamiento')
		->where("id_caso", $caso)
		->whereNull("fecha_termino")
		->first();

		if($acompanamientos != ''){
			//valores quitar
			if (isset($acompanamiento_a->tipo_acompanamiento) && strcmp($acompanamientos, $acompanamiento_a->tipo_acompanamiento) !== 0) {
				$acompanamiento_quitar = EvolucionAcompanamiento::where("id_caso", $caso)
				->where("tipo_acompanamiento",$acompanamiento_a->tipo_acompanamiento)
				->whereNull("fecha_termino")
				->first();
				$acompanamiento_quitar->fecha_termino = Carbon::now();
				$acompanamiento_quitar->usuario_quita = Auth::user()->id;
				$acompanamiento_quitar->save();

			}

			//valores agregar
			$acompanamiento_agregar = new EvolucionAcompanamiento();
			$acompanamiento_agregar->fecha = Carbon::now();
			$acompanamiento_agregar->id_caso = $caso;
			$acompanamiento_agregar->tipo_acompanamiento = $acompanamientos;
			$acompanamiento_agregar->usuario_asigna = Auth::user()->id;
			$acompanamiento_agregar->save();
		}
		
	}


	public function comentarioHospDom(Request $request){
		try{	
			$tipo_profesional=$request->input("tipo_profesional");
			$tipo_profesional = implode(",", $tipo_profesional);
			$procedimientos = $request->input("procedimientos");
			$procedimientos = implode(",", $procedimientos);
			DB::beginTransaction();
			$comentarioDom = new DomiciliariaComentario;
			$comentarioDom->caso = $request->idCaso;
			$comentarioDom->id_hosp_dom = $request->idLista;
			$comentarioDom->id_usuario_comenta = Auth::user()->id;
			$comentarioDom->complejidad_patologia = strip_tags($request->complejidad_patologia);
			$comentarioDom->conserjeria = strip_tags($request->conserjeria);
			$comentarioDom->procedimientos = strip_tags($procedimientos);
			$comentarioDom->tipo_profesional = strip_tags($tipo_profesional);
			$comentarioDom->comentario = $request->comentario;
			$comentarioDom->fecha = Carbon::now();
			$comentarioDom->save();
			DB::commit();
			return response()->json(array("exito" => "Se ha añadido un comenatrio al paciente","idcaso" => $comentarioDom->caso,"idlista"=>$comentarioDom->id_hosp_dom));

		}catch(\Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al añadir comentario al paciente"));
		}
	}

	public function obtenerComentariosHospDom(Request $request){
		$response=[];
		$idCaso = $request->idCaso;

		$comentarios = DB::table('lista_comentarios_domiciliaria as dc')
		->join('hospitalizacion_domiciliaria as hd','hd.id','=','dc.id_hosp_dom')
		->where('dc.caso', $idCaso)
		->select('dc.complejidad_patologia','dc.conserjeria', 'dc.procedimientos','dc.profesional','dc.tipo_profesional','dc.comentario as comentario','dc.fecha as fecha', 'dc.id_usuario_comenta')
		->orderBy('fecha','desc')
		->get();

		foreach ($comentarios as $c) {
			$comentario = $c->comentario;
			$fecha = date("d-m-Y H:i", strtotime($c->fecha));

			$usuario = Usuario::where("id", $c->id_usuario_comenta)->first(['nombres','apellido_paterno','apellido_materno']);
			$nombre_completo = ($c->id_usuario_comenta) ? $usuario->nombres." ".$usuario->apellido_paterno." ".$usuario->apellido_materno : '--';

			$procedimientos = $c->procedimientos;
			$proc = explode(',', $procedimientos);

			$info_proc = '';
			foreach ($proc as $key => $p) {
				$info_proc .= "- ". $p ." <br>";
			}

			$tipo_profesional = $c->tipo_profesional;
			$tipo_pro = explode(',', $tipo_profesional);

			$info_tipo = '';
			foreach ($tipo_pro as $key => $p) {
				$info_tipo .= "- ". $p ." <br>";
			}

			$response[] = [
				"complejidad" => ($c->complejidad_patologia) ? $c->complejidad_patologia : '-',
				"consejeria" => ($c->conserjeria) ? $c->conserjeria : '-',
				"procedimientos" => $info_proc,
				"profesional" => ($c->profesional) ? $c->profesional : '-',
				"tipo_profesional" => $info_tipo,
				"comentario" => $comentario,
				"fecha" => $fecha,
				"usuario_comenta" => $nombre_completo
			];
		}
		return $response;
	}

	public function excelHospitalizacionDomiciliaria(Request $request){
		return HospitalizacionDomiciliaria::excelHospitalizacionDomiciliaria($request);

	}

	public function reingresarListaEspera(Request $request){
		try {
			$ymdhis = "Y-m-d H:i:s";
			$fecha = Carbon::now()->format($ymdhis);
		
			$caso = Caso::find($request->idCaso);
			$idPaciente = $caso->paciente;
			$paciente = Paciente::find($idPaciente);
			$rut = $paciente->rut;

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

			DB::beginTransaction();
			
			//cerrar caso si es necesario.
			if(!$caso->fecha_termino){
				$caso->fecha_termino = $fecha;
				$caso->save();
			}

			//cerrar hospDom
			$hospDom = HospitalizacionDomiciliaria::where('caso',$request->idCaso)->whereNull('fecha_termino')->first();
			$hospDom->fecha_termino = $fecha;
			$hospDom->motivo_salida = "rehospitalizar";
			$hospDom->usuario_alta = Auth::user()->id;
			$hospDom->save();

			//crear caso nuevo
			$procedencia = strip_tags($request->input("tipo-procedencia"));
			if($procedencia == 2 ){
				$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento);
			}else if( $procedencia == 4 || $procedencia == 3){
				$detalle_procedencia = strip_tags($request->input_procedencia);
			}

			$nuevoCaso = new Caso;
			$nuevoCaso->id_usuario = Session::get('usuario')->id;
			$nuevoCaso->paciente = $caso->paciente;
			$nuevoCaso->fecha_ingreso = DB::raw("date_trunc('seconds', now())");
			$nuevoCaso->fecha_ingreso2 = Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->input("fechaIngreso")));
			$nuevoCaso->fecha_termino=null;
			$nuevoCaso->motivo_termino = null;
			$nuevoCaso->detalle_termino = null;
			$nuevoCaso->medico = ($request->medico == '') ? '' : $request->medico;
			$nuevoCaso->id_medico = ($request->id_medico == '') ? null : $request->id_medico;
			$nuevoCaso->prevision = $caso->prevision;
			$nuevoCaso->establecimiento = Auth::user()->establecimiento;
			$nuevoCaso->caso_social = ($request->caso_social == 'si') ? true : false;
			$nuevoCaso->tipo_caso_social = strip_tags($request->input("t_caso_social"));
			$nuevoCaso->procedencia = $procedencia;
			if(isset($detalle_procedencia) != null){
				$nuevoCaso->detalle_procedencia = $detalle_procedencia;
			}
			$nuevoCaso->ficha_clinica = strip_tags($request->input("fichaClinica"));
			$nuevoCaso->dau = strip_tags($request->input("dau")); 
			$nuevoCaso->indicacion_hospitalizacion = $request->has('fecha-indicacion') ? strip_tags($request->input('fecha-indicacion')) : null;
			$nuevoCaso->requiere_aislamiento = $request->requiere_aislamiento;
			$nuevoCaso->save();

			if($request->riesgo){
				$nuevoRiesgo = new Riesgo();
				$nuevoRiesgo->dependencia1 = $request->dependencia1;
				$nuevoRiesgo->dependencia2 = $request->dependencia2;
				$nuevoRiesgo->dependencia3 = $request->dependencia3;
				$nuevoRiesgo->dependencia4 = $request->dependencia4;
				$nuevoRiesgo->dependencia5 = $request->dependencia5;
				$nuevoRiesgo->dependencia6 = $request->dependencia6;
				$nuevoRiesgo->riesgo1 = $request->riesgo1;
				$nuevoRiesgo->riesgo2 = $request->riesgo2;
				$nuevoRiesgo->riesgo3 = $request->riesgo3;
				$nuevoRiesgo->riesgo4 = $request->riesgo4;
				$nuevoRiesgo->riesgo5 = $request->riesgo5;
				$nuevoRiesgo->riesgo6 = $request->riesgo6;
				$nuevoRiesgo->riesgo7 = $request->riesgo7;
				$nuevoRiesgo->riesgo8 = $request->riesgo8;
				$nuevoRiesgo->categoria = $request->input("riesgo");
				$nuevoRiesgo->save();

				$id_riesgo = $nuevoRiesgo->id;
			}else{
				$id_riesgo = null;
			}

			$nuevaEvolucion = new EvolucionCaso;
			$nuevaEvolucion->caso = $nuevoCaso->id;
			$nuevaEvolucion->riesgo = ($request->riesgo) ? $request->riesgo : null; 
			$nuevaEvolucion->fecha = $nuevoCaso->fecha_ingreso;
			$nuevaEvolucion->riesgo_id = $id_riesgo;
			$nuevaEvolucion->id_usuario = Auth::user()->id;
			$nuevaEvolucion->save();

			//diagnostico
			$diagnosticos = $request->diagnosticos;
			$hidden_diagnosticos = $request->input("hidden_diagnosticos");
			$comentario_diagnostico = $request->input("diagnostico");

			foreach ($diagnosticos as $key => $value) {
				if($value != "null" ){
					$d = new HistorialDiagnostico();
					$d->caso = $nuevoCaso->id;
					$d->fecha = $nuevoCaso->fecha_ingreso;
					$d->diagnostico = strip_tags($value);
					$d->id_cie_10 = strip_tags($hidden_diagnosticos[$key]);
					$d->id_usuario = Auth::user()->id;
					$d->comentario = strip_tags($comentario_diagnostico[$key]);
					$d->save();
				}
			}

			//agregar a lista de espera
			$lista = new ListaEspera;
			$lista->caso = $nuevoCaso->id;
			$lista->fecha = $fecha;
			$lista->usuario = Session::get("usuario")->id;
			$lista->ubicacion = "Sin información"; 
			$lista->save();
			
			DB::commit();
	
			return response()->json(array("exito" => "El paciente ha sido reingresado a lista de espera"));
		} catch (Exception $ex) {
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al reingresar al paciente en lista de espera"));
		}

	}

	public function registarRiesgo(Request $request){
		try{
			DB::beginTransaction();
				$riesgo = new Riesgo;
				$riesgo->categoria = strtoupper($request->riesgo);
				$riesgo->save();

				$ev = new EvolucionCaso();
				$ev->caso = $request->idCaso;
				$ev->fecha = Carbon::now();
				$ev->riesgo = strtoupper($request->riesgo);
				$ev->riesgo_id = $riesgo->id;
				$ev->save();

			DB::commit();
			return response()->json(array("exito" => "El paciente ha sido categorizado con exito"));

		}catch(\Exception $ex){

			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al categorizar al paciente"));
		}

	}

	public function agregarListaEspera(Request $request){
		$casoHospDom = strip_tags(trim($request->casoHospDom));
		$rut = strip_tags(trim($request->input("rut")));

		$fecha_solicitud = Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->input("fechaIngreso")));
		$dau = strip_tags($request->input("dau"));
		$ficha_clinica = strip_tags($request->input("fichaClinica"));
		$proc_geo = strip_tags($request->input("procedencia-geo"));
		$procedencia = strip_tags($request->input("tipo-procedencia"));
		$medico = strip_tags($request->input("id_medico"));
		$fecha_indicacion = $request->has('fecha-indicacion') ? strip_tags($request->input('fecha-indicacion')) : null;
		$rn = strip_tags($request->input("rn"));
		$requiere_aislamiento = strip_tags($request->input("requiere_aislamiento"));
		$motivo_hosp = strip_tags($request->input("motivo_hosp"));		

		if($procedencia == 2 ){
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento);
		}
		else if($procedencia == 7){
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento_privado);
		}else if( $procedencia == 4 || $procedencia == 3){
			$detalle_procedencia = strip_tags($request->input_procedencia);
		}

		if($medico == ''){
			$medico = null;
		}
		$comentario_diagnostico = $request->input("diagnostico");

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


		$diagnosticos = $request->input("diagnosticos");
		$hidden_diagnosticos = $request->input("hidden_diagnosticos");

      	$caso_social = strip_tags($request->input("caso_social"));
      	if($caso_social === 'si'){
        	$caso_social = true;
        }
        else{
          	$caso_social = false;
        }

		// if(isset($request->recibe_visitas)){

		// 	$recibe_visitas = strip_tags($request->input("recibe_visitas"));
		// 	$cantidad_personas = '';
		// 	$cantidad_horas = '';
		// 	if($recibe_visitas === 'si'){
		// 	$recibe_visitas = true;
		// 	$cantidad_personas = strip_tags($request->input("cantidad_personas"));
		// 	$cantidad_horas = strip_tags($request->input("cantidad_horas"));
		// 	}
		// 	else{
		// 			$recibe_visitas = false;
		// 	}
		// }

        try{
			$rut= strip_tags(trim($request->input("rut")));



        	$sexo= strip_tags($request->input("sexo"));
	        try{
				//\Carbon\Carbon::createFromFormat("d-m-Y H:i:s", $request->input("fechaIngreso"))
	        	$fecha_ingreso = Carbon::now();
	        }
	        catch(\Exception $e){
	        	$fecha_ingreso = Carbon::now();
	        }

					$especialidades = $request->input("especialidades");

			DB::beginTransaction();


			if(empty($rut) || !Paciente::existePaciente($rut)){
				$pac=new Paciente();
				$caso = $pac->registrarPaciente($request->all());
				$caso->establecimiento = Session::get("idEstablecimiento");
		        $caso->caso_social = $caso_social;
		      	$caso->procedencia = $procedencia;
		      	$caso->ficha_clinica = $ficha_clinica;
				$caso->dau = $dau;
				$caso->tipo_caso_social = strip_tags($request->input("t_caso_social"));
		      	$caso->fecha_termino=null;
				$caso->fecha_ingreso2 =$fecha_solicitud;
				$caso->motivo_hospitalizacion = $motivo_hosp;
				if(isset($detalle_procedencia) != null){
					$caso->detalle_procedencia = $detalle_procedencia;
				}
				$caso->id_usuario = Session::get('usuario')->id;
				$caso->procedencia_geografica = $proc_geo;
				$caso->id_medico = $medico;
				$caso->indicacion_hospitalizacion = $fecha_indicacion;
				$caso->requiere_aislamiento = $requiere_aislamiento;
				$caso->save();
				$idCaso = $caso->id;

			}else{
				//aqui agregar los campos para actualizar los datos
				$pac=Paciente::where("rut", $rut)->first();

				$pac->sexo=$sexo;

				if($request->input("fechaNac") == ""){
					$pac->fecha_nacimiento=null;
				}else{
					$pac->fecha_nacimiento=date("Y-m-d", strip_tags(strtotime(trim($request->input("fechaNac")))));
				}

				$pac->save();

				$caso = $pac->registrarCasoPaciente($request->all());
				$caso->establecimiento = Session::get("idEstablecimiento");
				$caso->caso_social = $caso_social;
				$caso->procedencia = $procedencia;
				$caso->ficha_clinica = $ficha_clinica;
				$caso->dau = $dau;
				$caso->tipo_caso_social = strip_tags($request->input("t_caso_social"));
				$caso->fecha_termino=null;
				$caso->fecha_ingreso2 =$fecha_solicitud;
				if(isset($detalle_procedencia) != null){
					$caso->detalle_procedencia = strip_tags($detalle_procedencia);
				}
				$caso->id_usuario = Session::get('usuario')->id;
				$caso->procedencia_geografica = $proc_geo;
				$caso->id_medico = $medico;
				$caso->indicacion_hospitalizacion = $fecha_indicacion;
				$caso->requiere_aislamiento = $requiere_aislamiento;
				$caso->motivo_hospitalizacion = $motivo_hosp;
				$caso->save();
				$idCaso = $caso->id;

				if ($request->input("riesgo") != null || $request->input("riesgo") != '') {
					$riesgo= new Riesgo;
					$riesgo->dependencia1 = strip_tags($request->dependencia1);
					$riesgo->dependencia2 = strip_tags($request->dependencia2);
					$riesgo->dependencia3 = strip_tags($request->dependencia3);
					$riesgo->dependencia4 = strip_tags($request->dependencia4);
					$riesgo->dependencia5 = strip_tags($request->dependencia5);
					$riesgo->dependencia6 = strip_tags($request->dependencia6);
					$riesgo->riesgo1 = strip_tags($request->riesgo1);
					$riesgo->riesgo2 = strip_tags($request->riesgo2);
					$riesgo->riesgo3 = strip_tags($request->riesgo3);
					$riesgo->riesgo4 = strip_tags($request->riesgo4);
					$riesgo->riesgo5 = strip_tags($request->riesgo5);
					$riesgo->riesgo6 = strip_tags($request->riesgo6);
					$riesgo->riesgo7 = strip_tags($request->riesgo7);
					$riesgo->riesgo8 = strip_tags($request->riesgo8);
					$riesgo->categoria = strip_tags($request->input("riesgo"));
					$riesgo->save();

                }


			}

			//Nolazko
			$evolucion = EvolucionCaso::where("caso", $idCaso)
				->orderBy("updated_at", "desc")
				->first();

			if($request->servicios != null){
				$evolucion->id_complejidad_area_funcional = strip_tags($request->servicios);
				if(Auth::user()->tipo == "usuario"){
					$evolucion->urgencia = true;
				}else{
					$evolucion->urgencia = false;
				}
				$evolucion->save();
			}


			if(!ListaEspera::existeEnlistaPorCaso($idCaso)){

				if($casoHospDom){
					$cerrarHospDom = HospitalizacionDomiciliaria::where('caso',$casoHospDom)->first();
					$cerrarHospDom->fecha_termino = $fecha_ingreso->format("Y-m-d H:i:s");
					if($medico){
						$cerrarHospDom->id_medico_alta = $medico;
					}
					$cerrarHospDom->motivo_salida = "rehospitalizar";
					$cerrarHospDom->usuario_alta = Auth::user()->id;
					$cerrarHospDom->save();
				}

				$lista=new ListaEspera;
				$lista->caso=$idCaso;
				$lista->fecha=$fecha_ingreso->format("Y-m-d H:i:s");
				$lista->usuario = Session::get("usuario")->id;
				$lista->ubicacion = "Sin información";
				$lista->save();


				foreach ($diagnosticos as $key => $value) {
					if($value != "null" ){
						$d = new HistorialDiagnostico();
						$d->caso = $caso->id;
						$d->fecha = $caso->fecha_ingreso;
						$d->diagnostico = strip_tags($value);
						$d->id_cie_10 = strip_tags($hidden_diagnosticos[$key]);
						$d->id_usuario = Auth::user()->id;
						$d->comentario = strip_tags($comentario_diagnostico[$key]);
						$d->save();
					}
				}

				foreach ($especialidades as $key => $value) {
				$especialidad = new EvolucionEspecialidad();
				$especialidad->fecha = Carbon::now();
				$especialidad->id_caso = $idCaso;
				$especialidad->id_especialidad = $value;
				$especialidad->usuario_asigna = Auth::user()->id;
				$especialidad->save();
				}
			}

			DB::commit();
			return response()->json(array("exito" => "El paciente ha sido enviado a la lista de espera"));

		}catch(\Exception $ex){
			log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al enviar el paciente a la lista de espera"));
		}
	}

	public function agregarHospDom(Request $request){

		$especialidades = $request->input("especialidades");
		$rut = strip_tags(trim($request->input("rut")));
		$dv = strip_tags(trim($request->input("dv")));
		$dv = ($dv == 'k' || $dv == 'K') ? 10 : $dv;
		$fecha_solicitud = Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->input("fechaIngreso")));
		$procedencia = strip_tags($request->input("tipo-procedencia"));
		$requiere_aislamiento = strip_tags($request->input("requiere_aislamiento"));

		if($procedencia == 2 ){
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento);
		}
		else if($procedencia == 7)
		{
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento_privado);
		}else if( $procedencia == 4 || $procedencia == 3){
			$detalle_procedencia = strip_tags($request->input_procedencia);
		}

		$diagnosticos = $request->input("diagnosticos");
		$hidden_diagnosticos = $request->input("hidden_diagnosticos");
		$comentario_diagnostico = $request->input("diagnostico");

		$caso_social = strip_tags($request->input("caso_social"));

		$caso_social = ($caso_social == 'si') ? true : false;

		$patologias= strip_tags($request->input("patologias"));

		if($rut){
			$en_lista_espera = DB::table('pacientes as p')
								->join('casos as c', 'c.paciente','=','p.id')
								->join('lista_espera as l','l.caso','=','c.id')
								->where('p.rut',$rut)
								->whereNull('l.fecha_termino')
								->first();

			if($en_lista_espera != null){
				return response()->json(["error" => "El paciente se encuentra en lista de Espera"]);
			}

			$en_lista_transito = DB::table('pacientes as p')
								->join('casos as c', 'c.paciente','=','p.id')
								->join('lista_transito as l','l.caso','=','c.id')
								->where('p.rut',$rut)
								->whereNull('l.fecha_termino')
								->first();

			if($en_lista_transito != null){
				return response()->json(["error" => "El paciente se encuentra en lista de Tránsito"]);
			}

			$en_hosp_dom = DB::table('pacientes as p')
			->join('casos as c', 'c.paciente','=','p.id')
			->join('hospitalizacion_domiciliaria as l','l.caso','=','c.id')
			->where('p.rut',$rut)
			->whereNull('l.fecha_termino')
			->first();

			if($en_hosp_dom != null){
				return response()->json(["error" => "El paciente ya se encuentra en Hospitalización Domiciliaria"]);
			}

		}
		try {
			DB::beginTransaction();

			if(empty($rut) || !Paciente::existePaciente($rut)){
				//crear paciente en caso de que no exista
				$pac=new Paciente();
				$caso = $pac->registrarPaciente($request->all());
				
			}else{
				//el paciente fue encontrado y se requiere su informacion
				$pac = Paciente::where("rut",$rut)->first();
				$caso = Paciente::registrarCasoPacienteHospDom($pac);
				$caso->establecimiento = Session::get("idEstablecimiento");
			}

			$caso->caso_social = $caso_social;
			$caso->procedencia = $procedencia;
			$caso->tipo_caso_social = strip_tags($request->input("t_caso_social"));
			$caso->fecha_termino = '1800-10-10 12:00:00';
			$caso->fecha_ingreso = '1800-10-10 12:00:00';
			$caso->motivo_termino = 'hospitalización domiciliaria';
			$caso->detalle_termino = 'Hospitalizado en domicilio por sistema';
			if(isset($detalle_procedencia) != null){
				$caso->detalle_procedencia = $detalle_procedencia;
			}
			$caso->id_usuario = Session::get('usuario')->id;
			$caso->requiere_aislamiento = $requiere_aislamiento;
			$caso->save();

			$hospDom = new HospitalizacionDomiciliaria();
			$hospDom->caso = $caso->id;
			$hospDom->fecha = $fecha_solicitud;
			$hospDom->usuario = Session::get('usuario')->id;
			$hospDom->save();

			foreach ($diagnosticos as $key => $value) {
				if($value != "null" ){
					$d = new HistorialDiagnostico();
					$d->caso = $caso->id;
					$d->fecha = $fecha_solicitud;
					$d->diagnostico = strip_tags($value);
					$d->id_cie_10 = strip_tags($hidden_diagnosticos[$key]);
					$d->id_usuario = Auth::user()->id;
					$d->comentario = strip_tags($comentario_diagnostico[$key]);
					$d->save();
				}
			}

			foreach ($especialidades as $value) {
				$especialidad = new EvolucionEspecialidad();
				$especialidad->fecha = Carbon::now();
				$especialidad->id_caso = $caso->id;
				$especialidad->id_especialidad = $value;
				$especialidad->usuario_asigna = Auth::user()->id;
				$especialidad->save();
			}

			
			if($patologias != "null" ){
				$d = new PatologiaCasos();
				$d->caso = $caso->id;
				$d->patologia = strip_tags($patologias);
				$d->fecha_creacion = carbon::now()->format("Y-m-d H:i:s");
				$d->visible = true;
				$d->save();
			}

			DB::commit();
			return response()->json(array("exito" => "El paciente ha sido enviado a Hospitalización Domiciliaria"));

		} catch (Exception $ex) {
			log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al enviar el paciente a Hospitalización Domiciliaria"));
		}
	}

	public function pacienteEnListaEspera(Request $request){
		$rut=$request->input("rut");
		$paciente=Paciente::where("rut", "=", $rut)->first();
		if(is_null($paciente)) return response()->json(["valid" => true]);
		$existe=ListaEspera::pacienteEnListaEspera($paciente->id);
		if($existe) return response()->json(["valid" => false]);
		return response()->json(["valid" => true]);
	}

	public function obtenerListaEspera(Request $request){
		Log::info("El usuario -> ".Auth::user()->nombres." con id ".Auth::user()->id." esta observando lista de espera");
		$motivos = Consultas::getMotivosLiberacion();

		return json_encode(["aaData" => ListaEspera::obtenerListaEspera(Session::get('idEstablecimiento'), $request->procedencia), "motivo"=>$motivos]);
	}

	public function excelListaEspera($procedencia){
		Excel::create('ListaEspera', function($excel) use ($procedencia){
			$excel->sheet('ListaEspera', function($sheet) use ($procedencia){

				$sheet->mergeCells('A1:K1');
				$sheet->setAutosize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row){

					$row->setBackground('#1B9966');
					$row->setFontColor('#FFFFFF');
					$row->setAlignment('center');
				});
				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$data = ListaEspera::dataListaEspera($idEstablecimiento, $procedencia);
				
				if($procedencia == '' || $procedencia == 'x'){
					$procedencia = 'Todos';
				}else{
					$procedencia = Procedencia::find($procedencia,'nombre');
					$procedencia = $procedencia->nombre;
				}
				
				$sheet->loadview('Estadisticas.ListaEspera.excelListaEspera', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data,
					"procedencia" => $procedencia
				]);
			});
		})->download('xls');
	}

	public function pdfListaEspera($procedencia){
		$fechaActual = Carbon::now();
		$fecha = Carbon::parse($fechaActual)->format("d-m-Y");
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		$data = ListaEspera::dataListaEspera($idEstablecimiento, $procedencia);
		$procedencia = ($procedencia != "x")?Procedencia::find($procedencia,'nombre'):"Todos";
		try {
			$pdf = PDF::loadView('Estadisticas.ListaEspera.pdfListaEspera', [
				"fecha" => $fecha,
				"establecimiento" => $nombreEstablecimiento,
				"response" => $data,
				"procedencia" => ($procedencia != "Todos")?$procedencia->nombre:"Todos"
			]);
			return $pdf->setPaper('legal', 'landscape')->download('Pacientes_lista_espera_'.$fecha.'.pdf');
		} catch (Exception $ex) {
			return $ex->getMessage();
		}
	}

	public function excelListaEsperaHosp($procedencia){
		Excel::create('ListaEsperaHospitalización', function($excel) use ($procedencia){
			$excel->sheet('ListaEsperaHospitalización', function($sheet) use ($procedencia){

				$sheet->mergeCells('A1:G1');
				$sheet->setAutosize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row){

					$row->setBackground('#1B9966');
					$row->setFontColor('#FFFFFF');
					$row->setAlignment('center');
				});
				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$data = ListaTransito::obtenerListaTransito(Session::get('idEstablecimiento'), $procedencia);
				
				if($procedencia == '' || $procedencia == 'x'){
					$procedencia = 'Todos';
				}else{
					$procedencia = Procedencia::find($procedencia,'nombre');
					$procedencia = $procedencia->nombre;
				}

				$sheet->loadview('Estadisticas.ListaEsperaHospitalizacion.excelListaEsperaHosp', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data,
					"procedencia" => $procedencia
				]);
			});
		})->download('xls');
	}

	public function RiesgoDependencia(Request $request){
		return Consultas::getRiesgoDependencia($request->idCaso);
	}

	public function obtenerListaTransito(Request $request){
		Log::info("El usuario -> ".Auth::user()->nombres." con id ".Auth::user()->id." esta observando lista de hospitalizacion");
		$motivos = Consultas::getMotivosLiberacion();

		return response()->json(["aaData" => ListaTransito::obtenerListaTransito(Session::get('idEstablecimiento'), $request->procedencia), "motivo"=>$motivos]);
	}

	public function obtenerSalidaUrgencia(){
		Log::info("El usuario -> ".Auth::user()->nombres." con id ".Auth::user()->id." esta observando lista de transito a piso");
		$motivos = Consultas::getMotivosLiberacion();
		return response()->json(["aaData" => ListaTransito::obtenerSalidaUrgencia(Session::get('idEstablecimiento')), "motivo"=>$motivos]);
	}
	public function obtenerListaPreAlta(){
		Log::info("El usuario -> ".Auth::user()->nombres." con id ".Auth::user()->id." esta observando lista de pre alta");
		$motivos = Consultas::getMotivosLiberacion();
		return response()->json(["aaData" => PreAlta::obtenerPreAlta(Session::get('idEstablecimiento')), "motivo"=>$motivos]);
	}

	public function ingresarACama(Request $request){

		try{
			$idCaso=$request->input("idCaso");
			$cama=$request->input("cama");
			$idLista=$request->input("idLista");

			$cama_disponible = DB::table('t_historial_ocupaciones as th')
							->select('fecha_liberacion')
							->where('cama', '=', $cama)
							->whereNull('fecha_liberacion')
							->orderBy('fecha', 'desc')
							->first();

			if($cama_disponible){
				return response()->json(["error"=>"Error, la cama ha sido ocupada"]);
			}

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,'lista_espera');
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			$caso = Caso::findOrFail($idCaso,['paciente']);
			$paciente = Paciente::findOrFail($caso->paciente,['rut']);
			$rut = $paciente->rut;

			$en_cama=HistorialOcupacionesVista::where("rut", "=", $rut)
				->whereNull("fecha_liberacion")
				->orderBy("id", "desc")
				->first();

			if($en_cama && $rut != ""){
				return response()->json(["hospitalizado"=>"Error, el paciente ya fue hospitalizado"]);
			}

			DB::beginTransaction();

			$hOcupacion= new HistorialOcupacion;
			$hOcupacion->cama=$cama;
			$hOcupacion->caso=$idCaso;
			$hOcupacion->fecha= Carbon::now()->format('Y-m-d H:i:s');
			$hOcupacion->save();

			$lista=ListaEspera::find($idLista);
			$lista->fecha_termino= Carbon::now()->format('Y-m-d H:i:s');
			$lista->motivo_salida=NULL;
			$lista->save();

			$transito = new ListaTransito();
			$transito->caso = $idCaso;
			$transito->fecha = Carbon::now()->format('Y-m-d H:i:s');
			$transito->id_usuario_ingresa = Session::get('usuario')->id;
			$transito->save();

			DB::commit();
			return response()->json(array("exito" => "Se le ha asignado la cama al paciente"));
		}catch(Exception $ex){
			log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al asignar cama al paciente"));
		}
	}

	public function ingresarACamaReal(Request $request){ Log::info($request);
		try{
			//validaciones
			$idCaso=$request->input("idCaso");

			$validacion = Consultas::validacionHospitalizacion($idCaso);
			if($validacion != ""){
				return response()->json(array("error" => $validacion));
			}

			$respuesta = Consultas::puedeHacer($idCaso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			
			
			//validaciones

			DB::beginTransaction();
			
			$idLista=$request->input("idLista");
			$tiempo = Carbon::now()->format("Y-m-d H:i:s");
			$id_usuario = Auth::user()->id;

			//Cerrar configuraciones existentes
			$configuracion_antigua = ConfiguracionVisitas::where('id_caso', $idCaso)->where("visible",true)->first();
			if ($configuracion_antigua) {
				$configuracion_antigua->update(['visible' => false]);
			}		
			
			//Añadir configuracion de visitas
			$configuracion_nueva = new ConfiguracionVisitas;
			$configuracion_nueva->fecha = $tiempo;
			$configuracion_nueva->fecha_creacion = $tiempo;		
			$configuracion_nueva->recibe_visitas = ($request->recibe_visitas == "si")?true:false;
			if ($request->recibe_visitas == "si") {
				$configuracion_nueva->num_personas_visitas = $request->cantidad_personas;
				$configuracion_nueva->cant_horas_visitas = $request->cantidad_horas;
			}else{
				$configuracion_nueva->comentario_visitas = strip_tags($request->comentario_visitas);
			}
			$configuracion_nueva->usuario_asigna = $id_usuario;
			$configuracion_nueva->visible = true;
			$configuracion_nueva->id_caso = $idCaso;
			$configuracion_nueva->save();

			$lista=ListaTransito::find($idLista);
			$lista->fecha_termino=DB::raw("date_trunc('seconds', now())");
			$lista->motivo_salida=NULL;
			$lista->save();

			$ocupacion = THistorialOcupaciones::where("caso","=",$idCaso)->whereNull("fecha_liberacion")->first();
			$ocupacion->fecha_ingreso_real = Carbon::now();
			$ocupacion->id_usuario_ingresa = Session::get('usuario')->id;

			$ocupacion->save();

			DB::commit();
			return response()->json(array("exito" => "El paciente ha sido hospitalizado"));
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al hospitalizar al paciente"));
		}
	}

	public function darAlta(Request $request){
		try{
			$idLista= strip_tags($request->input("idLista"));
			$idCaso= strip_tags($request->input("idCaso"));
			$motivo= strip_tags(strtolower($request->input("motivo")));
			$ficha = strip_tags($request->input("ficha"));
			$medico_alta = strip_tags($request->input("id_medico"));
			$fallec = strip_tags($request->input("fechaFallecimiento"));
			$detalle = "";
			$inputProcedencia = strip_tags($request->input("inputProcedencia"));

			$inputProcedenciaExtra = strip_tags($request->input("inputProcedenciaExtra"));
			$input_alta = strip_tags($request->input("input-alta"));

			$caso = Caso::findOrFail($idCaso);

			$paciente = Paciente::where("id",$caso->paciente)->first();

			$fechaEgreso_dato = strip_tags($request->input("fechaEgreso"));
			try{
				$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");
			}catch(Exception $e){
				$fecha_egreso = Carbon::now()->format("Y-m-d H:i:s");
			}

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,'lista_espera');
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			DB::beginTransaction();
			if($motivo=='hospitalización'){
				$motivoCaso = 'hospitalización domiciliaria';

				$Hdom=new HospitalizacionDomiciliaria;
				$Hdom->caso=$idCaso;
				$Hdom->fecha=$fecha_egreso;
				$Hdom->usuario = Session::get("usuario")->id;
				$Hdom->save();

			}elseif($motivo=='fuga'){
				$motivoCaso = 'Fuga';
			}elseif($motivo=='liberación de responsabilidad'){
				$motivoCaso = 'Liberación de responsabilidad';
			}elseif($motivo == "derivación"){
				$detalle=trim($inputProcedencia);
			}elseif($motivo == "traslado extra sistema"){
				$detalle=trim($inputProcedenciaExtra);
			}elseif($motivo == "derivacion otra institucion" || $motivo == "otro"){
				$detalle=trim($input_alta);
			}elseif($motivo == "fallecimiento"){
				$detalle = "Fallecimiento";
				$paciente->fecha_fallecimiento = Carbon::createFromFormat("d-m-Y H:i", $fallec)->format("Y-m-d H:i:s");
				$paciente->save();
			}
			else{
				if($motivo == "alta"){
					$detalle="Alta a domicilio";
				}else{
					$detalle=ucwords($motivo);
				}
			}
			$lista=ListaEspera::find($idLista);
			$lista->fecha_termino=$fecha_egreso;
			$lista->motivo_salida=$motivo;
			$lista->id_usuario_alta=Auth::user()->id;
			$lista->comentario=$detalle;
			$lista->save();

			$caso->fecha_termino=$fecha_egreso;
			if($motivo == 'hospitalización' || $motivo == 'fuga' || $motivo == 'liberación de responsabilidad' ){
				$caso->motivo_termino = $motivoCaso;
			}else{
				$caso->motivo_termino = $motivo;
			}
			$caso->detalle_termino = $detalle;
			$caso->ficha_clinica = $ficha;
			$caso->id_medico_alta = $medico_alta;

			if(isset($request->parto)){
				$caso->parto = ($request->parto == 'no') ? false : true;
			}

			$caso->save();

			DB::commit();
			return response()->json(array("exito" => "El paciente ha egresado"));

		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al egresar al paciente"));
		}
	}

	public function darAltaTransito(Request $request){
		try{
			$idLista= strip_tags($request->input("idLista"));
			$idCaso= strip_tags($request->input("idCaso"));

			$motivo=strtolower(strip_tags($request->input("motivo")));
			$detalle = "";
			$ficha = strip_tags($request->input("ficha"));
			$medico_alta = strip_tags($request->input("id_medico"));
			$fallec = strip_tags($request->input("fechaFallecimiento"));
			$inputProcedencia = strip_tags($request->input("inputProcedencia"));
			$inputProcedenciaExtra = strip_tags($request->input("inputProcedenciaExtra"));
			$input_alta = strip_tags($request->input("input-alta"));
			$caso=Caso::find($idCaso);

			$caso = Caso::findOrFail($idCaso);

			$paciente = Paciente::where("id",$caso->paciente)->first();

			$fechaEgreso_dato = strip_tags($request->input("fechaEgreso"));
			try{
				$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");
			}catch(Exception $e){
				$fecha_egreso = Carbon::now()->format("Y-m-d H:i:s");
			}

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			DB::beginTransaction();

			if($motivo=='hospitalización'){
				$motivoCaso = 'hospitalización domiciliaria';

				$Hdom=new HospitalizacionDomiciliaria;
				$Hdom->caso=$idCaso;
				$Hdom->fecha=$fecha_egreso;
				$Hdom->usuario = Session::get("usuario")->id;
				$Hdom->save();

			}elseif($motivo=='fuga'){
				$motivoCaso = 'Fuga';
			}elseif($motivo=='liberación de responsabilidad'){
				$motivoCaso = 'Liberación de responsabilidad';
			}elseif($motivo == "derivación"){
				// En ambas listas es igual
				$detalle=trim($inputProcedencia);
			}elseif($motivo == "traslado extra sistema"){
				// Ambas listas el mismo motivo
				$detalle=trim($inputProcedenciaExtra);
			}elseif($motivo == "derivacion otra institucion" || $motivo == "otro"){
				// Ambas listas el mismo motivo
				$detalle=trim($input_alta);
			}
			elseif($motivo == "fallecimiento"){
				// Ambas listas el mismo motivo
				$detalle = "Fallecimiento";
				$paciente->fecha_fallecimiento = Carbon::parse($fallec)->format("Y-m-d H:i:s");
				$paciente->save();
			}else{
				if($motivo == "alta"){
					$detalle="Alta a domicilio";
				}else{
					$detalle=ucwords($motivo);
				}
			}

			//Lista transito
			//motivo_salida_urgencia
			$lista=ListaTransito::find($idLista);
			$lista->fecha_termino=$fecha_egreso;
			$lista->motivo_salida=$motivo;
			$lista->comentario=$detalle;
			$lista->save();

			//Caso
			//motivos_liberacion
			$caso->fecha_termino=$fecha_egreso;
			if($motivo == 'hospitalización' || $motivo == 'fuga' || $motivo == 'liberación de responsabilidad' ){
				$caso->motivo_termino = $motivoCaso;
			}else{
				$caso->motivo_termino = $motivo;
			}
			$caso->detalle_termino = $detalle;
			$caso->ficha_clinica = $ficha;
			$caso->id_medico_alta = $medico_alta;

			if(isset($request->parto)){
				$caso->parto = ($request->parto == 'no') ? false : true;
			}

			$caso->save();

			//Historial Ocupacion
			//motivos_liberacion
			$historialocupaciones = THistorialOcupaciones::where("caso","=",$idCaso)->orderby("fecha","desc")->first();
			$historialocupaciones->fecha_liberacion = $fecha_egreso;
			if($motivo == 'hospitalización' || $motivo == 'fuga' || $motivo == 'liberación de responsabilidad' ){
				$historialocupaciones->motivo = $motivoCaso;
			}else{
				$historialocupaciones->motivo = $motivo;
			}
			$historialocupaciones->id_usuario_alta = Auth::user()->id;
			$historialocupaciones->save();

			// ListaDerivados::cerrarListaDerivado($idCaso);

			DB::commit();
			return response()->json(array("exito" => "El paciente ha egresado"));
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al egresar al paciente"));
		}
	}

	public function obtenerListaPacientes(){
		return response()->json(["aaData" => HospitalizacionDomiciliaria::obtenerListaPacientes(Session::get('idEstablecimiento'))]);
	}

	public function DomingresarACama(Request $request){
		try{
			DB::beginTransaction();

			$idCaso=$request->input("idCaso");
			$cama=$request->input("cama");
			$idLista=$request->input("idLista");

			$caso1=Caso::find($idCaso);
			$caso2=new Caso;
			$caso2->paciente=$caso1->paciente;
			$caso2->fecha_ingreso=DB::raw("date_trunc('seconds', now())");
			$caso2->medico=$caso1->medico;
			$caso2->prevision=$caso1->prevision;
			$caso2->detalle_procedencia="Hospitalizacion Domiciliaria";
			$caso2->establecimiento=$caso1->establecimiento;
			$caso2->caso_social=$caso1->caso_social;
			$caso2->save();



			$diagnostico = HistorialDiagnostico::where("caso","=",$idCaso)->select("diagnostico","id_cie_10")->first();
			$fecha_ingreso = Carbon::now();

			$d = new HistorialDiagnostico();
			$d->caso = $caso2->id;
			$d->fecha =$fecha_ingreso;
			$d->diagnostico = $diagnostico->diagnostico;
			$d->id_cie_10 = $diagnostico->id_cie_10;
			$d->save();




			$hOcupacion= new HistorialOcupacion;
			$hOcupacion->cama=$cama;
			$hOcupacion->caso=$caso2->id;
			$hOcupacion->fecha=DB::raw("date_trunc('seconds', now())");
			$hOcupacion->save();

			$lista=HospitalizacionDomiciliaria::find($idLista);
			$lista->fecha_termino=DB::raw("date_trunc('seconds', now())");
			$lista->motivo_salida="hospitalización";
			$lista->usuario_alta = Auth::user()->id;
			$lista->nuevo_caso = $caso2->id;
			$lista->save();
			DB::commit();
		}catch(Exception $ex){
			DB::rollback();
			return $ex;
		}
	}


	public function darAltaDom(Request $request){
		try{
			$idLista= strip_tags($request->input("idLista"));
			$idCaso=strip_tags($request->input("idCaso"));
			$detalle=strip_tags(trim($request->input("detalle")));
			$motivo=strip_tags($request->input("motivo"));
			$medico_alta = strip_tags($request->input("id_medico"));
			$fallec = strip_tags($request->input("fechaFallecimiento"));
			$caso=Caso::find($idCaso);

			$fechaEgreso_dato = strip_tags($request->input("fechaEgreso"));
			$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");

			DB::beginTransaction();

			$lista=HospitalizacionDomiciliaria::find($idLista);
			$lista->fecha_termino=$fecha_egreso;
			$lista->motivo_salida=$motivo;
			$lista->comentario=$detalle;
			$lista->id_medico_alta=$medico_alta;
			$lista->usuario_alta = Auth::user()->id;
			$lista->save();

			if($motivo == "fallecimiento"){
				$paciente = Paciente::where("id",$caso->paciente)->first();
				$paciente->fecha_fallecimiento = Carbon::createFromFormat("d-m-Y H:i", $fallec)->format("Y-m-d H:i:s");
				$paciente->save();
			}

			if(isset($request->parto)){
				$caso->parto = ($request->parto == 'no') ? false : true;
				$caso->save();
			}

			DB::commit();
			return response()->json(array("exito" => "El paciente ha egresado"));
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al egresar al paciente"));
		}
	}

	public function cronCategorizar(){
		
		try{
			DB::beginTransaction();
			//Camas con fecha de ingreso real
			$casos = Caso::select('casos.id')
				->join("t_historial_ocupaciones as t", "t.caso","casos.id")
				->where(function($q) {
					$q->whereNotNull("t.fecha_ingreso_real")
						->whereNull("t.fecha_liberacion");
				})
				->whereNull('casos.fecha_termino')				
				->get();
				
			foreach($casos as $caso){
				$riesgo = new EvolucionCaso();
				$riesgo->caso = $caso->id;
				$riesgo->riesgo = null;
				$riesgo->riesgo_id = null;
				$riesgo->save();
			}

			//Camas volantes o temporales que no sean camas en espera de hospitalizacion
			$casos_camas_temporales = CamaTemporal::select("t.caso")
				->join("t_historial_ocupaciones as t", "t.id","camas_temporales.id_historial_ocupaciones")
				->join("casos as c", "c.id","t.caso")
				->where("camas_temporales.visible" , true)
				->whereNotNull("t.fecha_ingreso_real")
				->whereNull('c.fecha_termino')				
				->get();
				
			foreach($casos_camas_temporales as $ct){
				$riesgo_ct = new EvolucionCaso();
				$riesgo_ct->caso = $ct->caso;
				$riesgo_ct->riesgo = null;
				$riesgo_ct->riesgo_id = null;
				$riesgo_ct->save();
			}

			$fecha = date("d-m-Y H:i:s");

			DB::commit();
			return response()->json(array("exito" => "Se han actualizados los riesgos", "fecha" => $fecha));
		}catch(Exception $ex){
			Log::info($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Ha ocurrido un error"));
		}
	}


	public function getTipoTransito(Request $request){
		try{
			$idLista = $request->input("idLista");


			$lista_transito = ListaTransito::find($idLista);



			DB::commit();
			return response()->json(array("tipo_transito"=>$lista_transito->tipo_transito));
		}
		catch(Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Ha ocurrido un error"));
		}
	}

	public function agregarComentario(Request $request){
		try{
			$idLista = $request->input("idLista");
			$comentario  =$request->input("comentario");

			DB::beginTransaction();

			$lista=ListaEspera::find($idLista);

			//validaciones
			$respuesta = Consultas::puedeHacer($lista->caso,'lista_espera');
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			$lista->comentario_lista = $comentario;
			$lista->save();

			DB::commit();
			return response()->json(array("exito" => "Se ha agregado el comentario"));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al agregar el comentario"));
		}

	}

	public function agregarUbicacion(Request $request){
		try{
			$idLista = $request->input("idLista");
			$ubicacion  =$request->input("ubicacion");

			DB::beginTransaction();

			$lista=ListaEspera::find($idLista);
			$lista->ubicacion = $ubicacion;
			$lista->save();

			DB::commit();
			return response()->json(array("exito" => "Se ha agregado la ubicación"));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al agregar la ubicación"));
		}

	}

	public function cambiarFechaTrasladoUnidadHosp(Request $request){
		//////////////////////////////////////////////////////////////
		// Ahora guarda la fecha y hora y ademas el tip ode transito//
		//////////////////////////////////////////////////////////////

		try{
			$fecha = $request->input("fecha-indicacion");
			$idLista = $request->input("idLista");
			$tipo_transito = $request->input("tipo_transito");

			DB::beginTransaction();

			$lista= ListaTransito::find($idLista);
			$lista->tipo_transito = $tipo_transito;
			$lista->traslado_unidad_hospitalaria = $fecha;
			$lista->id_usuario_salida_urgencia = Auth::user()->id;
			$lista->save();

			DB::commit();
			return response()->json(array("exito" => "Se ha agregado la fecha y tipo de transito"));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Falta agregar la fecha o se encuentra incompleta"));
		}

	}


	public function getFechaTrasladoUnidadHosp(Request $request){
		Log::info($request);
		try{
			$idLista = $request->input("idLista");

			$lista = ListaTransito::find($idLista);

			//validaciones
			$respuesta = Consultas::puedeHacer($request->idCaso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones


			$traslado_unidad_hospitalaria = "";
			if($lista->traslado_unidad_hospitalaria){
				$traslado_unidad_hospitalaria = date("d-m-Y H:i:s", strtotime($lista->traslado_unidad_hospitalaria));
			}

			return response()->json(array("fecha" => $traslado_unidad_hospitalaria));
		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al agregar el comentario"));
		}
	}

	public function obtenerListaPabellon(){
		$response=[];

		$data = ListaPabellon::infoPacienteEnPabellon();

			foreach($data as $d){
				$apellido=$d->apellidoP." ".$d->apellidoM;
				$nombre_completo = $d->nombre." ".$apellido;
				$dv=($d->dv == 10) ? 'K' : $d->dv;
				$rut = (empty($d->rut)) ? "-" : $d->rut."-".$dv;
				$fecha_ingreso = date("d-m-Y", strtotime($d->fechaIngreso));

				$id_medico = $d->id_usuario_solicito_pabellon;
				$medico = Usuario::find($id_medico);
				$nombres_medico = $medico->nombres. " ".$medico->apellido_paterno. " ".$medico->apellido_materno;


				$diag = HistorialDiagnostico::where("caso","=",$d->idCaso)->orderby("fecha","desc")->select("diagnostico", "comentario as co")->first();
				$d->diagnostico = $diag->diagnostico;
			    $d->co = $diag->co;
			    if($d->co == ""){
				$d->co = " Sin detalle";
				}

				$opciones = View::make("Urgencia/OpcionesListaPabellon", ["idCaso" => $d->idLista, "caso" => $d->idCaso, "nombreCompleto" => $nombre_completo])->render();

				$response[] = [
					"rut" => $rut,
					"nombre_completo" => $nombre_completo,
					"fecha_ingreso" => $fecha_ingreso,
					"opciones" => $opciones,
					"comentario" => $d->comentario,
					"diagnostico" => $d->diagnostico.". <label> Comentario: </label>".$d->co,
					"usuario_solicito" => $nombres_medico
				];
			}
			return $response;
	}

	public function pdfPacientesPabellonPorUnidad(){

		$datos = [];
		$fecha = date("d-m-Y");
		$datos = ListaPabellon::generarDataExportar();

		$html = PDF::loadView("Estadisticas/ListaPabellon/pdfListaPabellonPorServicio", [
			"datos" => $datos
		]);
		return $html->setPaper('legal', 'portrait')->download('ListaPabellonPorServicio' .$fecha.'.pdf');

	}

	public function pdfPacientesPabellon(){

		$datos = [];
		$fecha = date("d-m-Y");
		$datos = $this->obtenerListaPabellon();
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		$html = PDF::loadView("Estadisticas/ListaPabellon/pdfListaPabellon", [
			"datos" => $datos, "fecha" => $fecha, "establecimiento" => $nombreEstablecimiento->nombre
		]);
		return $html->setPaper('legal', 'portrait')->download('ListaPabellon' .$fecha.'.pdf');

	}

	public function excelListaPabellonPorUnidad(){
		Excel::create('ListaPabellon', function($excel) {
			$excel->sheet('ListaPabellon', function($sheet){

				$sheet->mergeCells('A1:E1');
				$sheet->setAutosize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row){

					$row->setBackground('#1B9966');
					$row->setFontColor('#FFFFFF');
					$row->setAlignment('center');
				});
				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$data = ListaPabellon::generarDataExportar();
				$sheet->loadview('Estadisticas.ListaPabellon.excelListaPabellonPorServicio', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data
				]);
			});
		})->download('xls');
	}

	public function excelListaPabellon(){
		Excel::create('ListaPabellon', function($excel) {
			$excel->sheet('ListaPabellon', function($sheet){

				$sheet->mergeCells('A1:E1');
				$sheet->setAutosize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row){

					$row->setBackground('#1B9966');
					$row->setFontColor('#FFFFFF');
					$row->setAlignment('center');
				});
				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$data = $this->obtenerListaPabellon();
				$sheet->loadview('Estadisticas.ListaPabellon.excelListaPabellon', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data
				]);
			});
		})->download('xls');
	}

	//test
	public function probando(Request $request){
		$idCaso = (int)$request->caso;
		$camas = THistorialOcupaciones::select('cama')->where("caso",$idCaso)->latest()->first();
		$idCama = $camas->cama;

		$salas = Cama::find($idCama, ['sala']);

		$idSalaP = $salas->sala;
		$sala = Sala::find($idSalaP, ['id_sala']);
		$idSala = $sala->id_sala;

		return response()->json(array("idCaso" => $idCaso, "idCama" => $idCama, "idSala" => $idSala));
	}
	//test

	public function enviarDerivado(Request $request){
		$idCaso = $request->input("idCaso");
		$comprobador = DB::table('lista_derivados')
		->select('caso', 'fecha_egreso_lista')
		->where('caso',$idCaso)
		->get();

		foreach ($comprobador as $c){
			$caso = $c->caso;
			$fecha = $c->fecha_egreso_lista;
		}
		$idCaso = strip_tags($request->input("idCaso"));
		if($comprobador == '[]'){
			$fechaIngreso = Carbon::now();
			$usuarioIngresa = Auth::user()->id;

			DB::beginTransaction();

			$listaDerivados = new ListaDerivados();
			$listaDerivados->caso = $idCaso;
			$listaDerivados->fecha_ingreso_lista = $fechaIngreso;
			$listaDerivados->id_usuario_ingresa = $usuarioIngresa;
			$listaDerivados->fecha = Carbon::now();
			$listaDerivados->estado = 'En proceso de gestión';
			$listaDerivados->save();

			$comentarioDerivado = new ListaDerivadosComentarios();
			$comentarioDerivado->caso = $idCaso;
			$comentarioDerivado->fecha = Carbon::now();
			$comentarioDerivado->id_lista_derivados = $listaDerivados->id_lista_derivados;
			$comentarioDerivado->comentario = 'Enviado a la lista de derivación';
			$comentarioDerivado->id_usuario_comenta = Auth::user()->id;
			$comentarioDerivado->save();

			$this->ingresarFormularioDerivacion($request,$idCaso,$listaDerivados->id_lista_derivados);

			DB::commit();
			return response()->json(array("exito" => "Se ha enviado el paciente a derivación"));
		}else{
			if($caso != '' && $fecha !=''){
				$fechaIngreso = Carbon::now();
				$usuarioIngresa = Auth::user()->id;

				DB::beginTransaction();

				$listaDerivados = new ListaDerivados();
				$listaDerivados->caso = $idCaso ;
				$listaDerivados->fecha_ingreso_lista = $fechaIngreso;
				$listaDerivados->id_usuario_ingresa = $usuarioIngresa;
				$listaDerivados->fecha = Carbon::now();
				$listaDerivados->estado = 'En proceso de gestión';
				$listaDerivados->save();

				$comentarioDerivado = new ListaDerivadosComentarios();
				$comentarioDerivado->caso = $idCaso ;
				$comentarioDerivado->fecha = Carbon::now();
				$comentarioDerivado->id_lista_derivados = $listaDerivados->id_lista_derivados;
				$comentarioDerivado->comentario = 'Paciente en proceso de derivación';
				$comentarioDerivado->id_usuario_comenta = Auth::user()->id;
				$comentarioDerivado->save();

				$this->ingresarFormularioDerivacion($request,$idCaso,$listaDerivados->id_lista_derivados);

				DB::commit();
				return response()->json(array("exito" => "Se ha enviado el paciente a lista de derivación"));
			}else{
				try {
					if($caso != '' && $fecha == ''){
						DB::rollback();
						return response()->json(array("error" => "El paciente ya se encuentra en la lista de derivación"));
					}else{
						DB::rollback();
						return response()->json(array("error" => "Error al enviar el paciente a lista de derivación"));
					}	//code...
				} catch (Exception $ex) {
					Log::info($ex);
				}
				
			}
		}
	}

	public function ingresarFormularioDerivacion($formulario, $idCaso, $id_lista_derivados){
		try {
			DB::beginTransaction();
			$form_derivacion = new FormularioDerivado();
			$form_derivacion->caso = $idCaso;
			$form_derivacion->id_lista_derivados = (int) $id_lista_derivados;
			$form_derivacion->fecha_creacion = Carbon::createFromFormat("d-m-Y H:i", strip_tags($formulario->fechaDerivacion))->format("'Y-m-d H:i:s'");
			if($formulario->idUnidadFuncional){
				$form_derivacion->id_unidad_deriva = strip_tags($formulario->idUnidadFuncional);
			}			
			$form_derivacion->tipo_traslado = strip_tags($formulario->tipo_traslado);
			$form_derivacion->motivo_derivacion = $formulario->motivo_derivacion;
			$form_derivacion->tipo_cama = ($formulario->motivo_derivacion != 15) ? $formulario->tipo_cama :0;
			$form_derivacion->detalle_derivacion = ($formulario->motivo_derivacion == 15) ? $formulario->detalle_derivacion : 0;
			$form_derivacion->id_medico = ($formulario->id_medico) ? strip_tags($formulario->id_medico) : null;
			$form_derivacion->ges = ($formulario->ges == 'si') ? true : false;
			$form_derivacion->ugcc = ($formulario->ugcc == 'si') ? true : false;
			$form_derivacion->t_ugcc = ($formulario->ugcc == 'si') ? $formulario->t_ugcc : 0;
			$form_derivacion->tipo_centro = strip_tags($formulario->tipo_centro);
			$form_derivacion->red_publica = ($formulario->tipo_centro == "derivacion")?$formulario->red_publica:null;
			$form_derivacion->red_privada = ($formulario->tipo_centro == "traslado extra sistema")?$formulario->red_privada:null;
			$form_derivacion->otro_derivacion = ($formulario->red_publica == 0) ? strip_tags($formulario->otro_derivacion) : '';
			$form_derivacion->via_traslado = $formulario->via_traslado;
			$form_derivacion->detalle_via = ($formulario->via_traslado == 2) ? $formulario->detalle_via : 0;
			$form_derivacion->tramo = strip_tags($formulario->tramo);
			if ($formulario->tramo == 'ida') {
				$form_derivacion->fecha_ida = ($formulario->fechaIda) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($formulario->fechaIda))->format("'Y-m-d H:i:s'"): null;
				$form_derivacion->fecha_rescate = null;
			}else if($formulario->tramo =='ida-rescate'){
				$form_derivacion->fecha_ida = ($formulario->fechaIda) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($formulario->fechaIda))->format("'Y-m-d H:i:s'"): null;
				$form_derivacion->fecha_rescate = ($formulario->fechaRescate) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($formulario->fechaRescate))->format("'Y-m-d H:i:s'"): null;
			}else{
				$form_derivacion->fecha_ida = null;
				$form_derivacion->fecha_rescate = null;
			}
			$form_derivacion->comuna_origen = $formulario->comuna_origen;
			$form_derivacion->comuna_destino = $formulario->comuna_destino;
			$form_derivacion->estado_paciente = $formulario->estado_paciente;
			$form_derivacion->movil = $formulario->movil;
			$form_derivacion->compra_servicio = ($formulario->movil == 2) ? $formulario->compra_servicio : null;
			$form_derivacion->compra_servicio_otro = ($form_derivacion->compra_servicio != null) ? strip_tags($formulario->compra_servicio_otro) : '';
			$form_derivacion->comentarios = strip_tags($formulario->comentarios);
			$form_derivacion->save();
			DB::commit();
		} catch (Exception $ex) {
			log::info($ex);
			DB::rollback();
		}
	}

	public function excelListaDerivados(Request $request){
		return ListaDerivados::excelListaDerivados($request);

	}

	public function enviarPabellon(Request $request){
		$idCaso = strip_tags($request->idCaso);
		$comentario = strip_tags($request->comentario);
		$comprobador = DB::table('lista_pabellon')
			->select('id_caso', 'fecha_salida')
			->where('id_caso',$idCaso)
			->get();

		$caso = '';
		$fecha = '';
		foreach ($comprobador as $c){
			$caso = $c->id_caso;
			$fecha = $c->fecha_salida;
		}

		if($comprobador == '[]' || ($caso != '' && $fecha !='')){
			$idCaso = $request->input("idCaso");
			$fechaIngreso = Carbon::now();
			$usuarioIngresa = Auth::user()->id;

			DB::beginTransaction();

			$listaPabellon = new ListaPabellon();
			$listaPabellon->id_caso = $idCaso;
			$listaPabellon->fecha_ingreso = $fechaIngreso;
			$listaPabellon->id_usuario_solicito_pabellon = $usuarioIngresa;
			$listaPabellon->comentario = $comentario;
			$listaPabellon->save();

			DB::commit();
			return response()->json(array("exito" => "Se ha enviado el paciente a pabellón"));
		}else{
			if($caso != '' && $fecha == ''){
				DB::rollback();
				return response()->json(array("error" => "El paciente ya se encuentra en la lista"));
			}else{
				DB::rollback();
				return response()->json(array("error" => "Error al enviar al paciente a derivados"));
			}
		}
	}

	public function quitarDerivado(Request $request){
		DB::beginTransaction();
		try {
			//nunca deberia tener mas de una derivacion creada con el mismo caso

			$fechaEgreso_dato = strip_tags($request->input("fechaTerminoDerivacion"));
			try{
				$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");
			}catch(Exception $e){
				$fecha_egreso = Carbon::now()->format("Y-m-d H:i:s");
			}

			$idLista = $request->idLista;

			$derivado = ListaDerivados::findOrFail($idLista);

			$idUsuarioEgreso = Auth::user()->id;
			$derivado->id_usuario_egresa = $idUsuarioEgreso;
			$derivado->fecha_egreso_lista = $fecha_egreso;
			$derivado->estado = 'Realizada';
			$derivado->save();

			$comentarioDerivado = new ListaDerivadosComentarios();
			$comentarioDerivado->caso = $derivado->caso;//$request->input("idCaso")
			$comentarioDerivado->fecha = $fecha_egreso;
			$comentarioDerivado->id_lista_derivados = $derivado->id_lista_derivados;
			$comentarioDerivado->comentario = 'El Paciente ha sido derivado exitosamente';
			$comentarioDerivado->id_usuario_comenta = Auth::user()->id;
			$comentarioDerivado->save();
			
			$formulario = FormularioDerivado::where('id_lista_derivados',$idLista)->whereNull('fecha_cierre')->first();
			if($formulario){
				$formulario->fecha_cierre = $fecha_egreso;
				$formulario->save();
			}				

			DB::commit();
			return response()->json(array("exito" => "El Paciente ha sido derivado exitosamente"));
		} catch (Exception $ex) {
			Log::error($ex);
			DB::rollback();
			return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al derivar al paciente"));
		}
	}

	public function quitarPabellonCamas(Request $request){
		try {
			$idCaso = $request->idCaso;
			$idLista = 0;

			$caso = DB::table('lista_pabellon')
			->select('id_pabellon')
			->where('id_caso', $idCaso)
			->orderBy('fecha_ingreso','desc')
			->get();

			foreach ($caso as $c){
				$idLista = $c->id_pabellon;

				DB::beginTransaction();

				$pabellon = ListaPabellon::findOrFail($idLista);

				$idUsuarioEgreso = Auth::user()->id;
				$pabellon->id_usuario_solicito_pabellon = $idUsuarioEgreso;
				$pabellon->fecha_salida = Carbon::now();
				$pabellon->save();
				DB::commit();
				return response()->json(array("exito" => "El paciente ha sido retirado de la lista"));
			}
		} catch (Exception $ex) {
				DB::rollback();
				return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al retirar al paciente de la lista"));
		}
	}

	public function quitarPabellon(Request $request){
		try {
			$idCaso = $request->idCaso;
			$caso = ListaPabellon::find($idCaso);
			$caso->id_usuario_saco_pabellon = Auth::user()->id;
			$caso->fecha_salida = Carbon::now();
			$caso->save();
				DB::commit();
				return response()->json(array("exito" => "El paciente ha sido retirado de la lista de pabellón"));

		} catch (Exception $ex) {
				DB::rollback();
				return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error al retirar al paciente de la lista"));
		}
	}

	public function obtenerComentariosListaDerivado(Request $request){
		$response=[];
		$idCaso = $request->idCaso;
		$idLista = $request->idLista;

		$comentarios = DB::table('lista_derivados_comentarios')
		->where('caso', $idCaso)
		->where('id_lista_derivados',$idLista)
		->select('comentario','fecha')
		->orderBy('fecha','desc')
		->get();

		$inicioLabel = "<label hidden>";
		$finLabel = "</label>";

		foreach ($comentarios as $c) {
			$comentario = $c->comentario;
			$fecha = date("d-m-Y H:i", strtotime($c->fecha));

			$response[] = [
				"comentario" => $comentario,
				"fecha" => $inicioLabel.$c->fecha.$finLabel.$fecha
			];
		}
		return $response;
	}

	public function agregarComentarioListaDerivado(Request $request){
		try {
		$idCaso = $request->idCaso;
		$comentario = $request->comentario;

		$idLista = DB::table('lista_derivados')
			->select('id_lista_derivados')
			->where('caso', $idCaso)
			->orderBy('fecha_ingreso_lista','desc')
			->get();
		$idListaDerivado = $idLista[0]->id_lista_derivados;

		$comentarioDerivado = new ListaDerivadosComentarios();
		$comentarioDerivado->caso = $idCaso;
		$comentarioDerivado->fecha = Carbon::now();
		$comentarioDerivado->id_lista_derivados = $idListaDerivado;
		$comentarioDerivado->comentario = $comentario;
		$comentarioDerivado->id_usuario_comenta = Auth::user()->id;
		$comentarioDerivado->save();
		return response()->json(array("exito" => "El comentario ha sido agregado"));
		} catch (Exception $ex) {
		return response()->json(array("error" => "Error al ingresar comentario"));
		}
	}

	public function datosParaDerivacion(Request $request){
		$idPaciente = Caso::find($request->caso, ['paciente']);
		$fecha_termino = Caso::find($request->caso, ['fecha_termino']);
		$paciente = Paciente::find($idPaciente, ['rut','dv','nombre','apellido_paterno','apellido_materno','fecha_nacimiento']);
		$nombreCompleto = $paciente[0]["nombre"] ." ".$paciente[0]["apellido_paterno"] ." ".$paciente[0]["apellido_materno"];
		$rut = ($paciente[0]["rut"]) ? ($paciente[0]["rut"]) : "";
		$dv = ($paciente[0]["dv"] == 10) ? "K" : $paciente[0]["dv"];
		$rutDv = $rut . "-" .$dv; 

		$fecha_naci = $paciente[0]["fecha_nacimiento"];
		$fecha1 = new DateTime($fecha_naci);
		$fecha2 = new DateTime();
		$fechaF = $fecha1->diff($fecha2);
		$diferencia = '';
		$grupoEtareo = 'Sin información';

		$fecha_nacimiento = ($paciente[0]["fecha_nacimiento"]) ? date("d-m-Y", strtotime($paciente[0]["fecha_nacimiento"])) : 'Sin Especificar';

		if($fechaF->y == 0){
			$diferencia = $fechaF->format('%m meses %a dias');
		}else{
			$diferencia = $fechaF->format('%y años %m meses');
		}

		if($fecha_naci != null){
			$years = Carbon::parse($fecha_naci)->age;
			if($years >= 15){
				$grupoEtareo = 'Adulto'; 
			}else{
				$grupoEtareo = 'Pediatrico';
			}
		}		

		$Fhosp = ThistorialOcupaciones::where("caso", $request->caso)->whereNotNull("fecha_ingreso_real")->first();
		$fecha_hospitalizacion = ($Fhosp) ? Carbon::parse($Fhosp->fecha_ingreso_real)->format("d-m-Y H:i:s") : 'Sin Hospitalizar';
		
		$infoUnidad = DB::table('t_historial_ocupaciones as t')
			->join("camas as c", "c.id", "=", "t.cama")
			->join("salas as s", "c.sala", "=", "s.id")
			->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
			->where("caso", "=", $request->caso)
			->orderBy("t.created_at", "desc")
			->select("uee.id","uee.alias as nombre_unidad")
			->first();

		$fecha_egreso_derivacion = "";	
		if($fecha_termino->fecha_termino != null){
			$fecha_egreso_derivacion = $fecha_termino->fecha_termino;
		}

		return [
			"nombreCompleto"=>$nombreCompleto,
			"rutDv"=>$rutDv,
			"grupoEtareo"=>$grupoEtareo,
			"fechaNacimiento"=>$fecha_nacimiento,
			"edad"=>$diferencia,
			"fechaHospitalizacion"=>$fecha_hospitalizacion,
			"nombreUnidad"=>$infoUnidad->nombre_unidad,
			"idUnidad" =>$infoUnidad->id,
			"fecha_egreso_derivacion" => $fecha_egreso_derivacion
		];
	}


	public function formularioDerivacion($idCaso,$idLista){
		//formulario derivación
		$info = FormularioDerivado::where('id_lista_derivados',$idLista)->whereNull('fecha_cierre')->first();
		$info = collect($info);

		$nombreMedico = (isset($info["id_medico"]))?Medico::nombreMedico($info["id_medico"]):"";
		$info->put('nombreMedico', $nombreMedico);

		$fecha="";
		if(isset($info["id_unidad_deriva"]) && $info["id_unidad_deriva"] != null){
			//Se tiene el id_unidad_deriva y se puede buscar 
			$unidad = UnidadEnEstablecimiento::traductorNombreUnidad($info["id_unidad_deriva"]);
			$info->put('nombreUnidad', $unidad);
		}else if(isset($info["id_unidad_deriva"]) && $info["id_unidad_deriva"] == null){
			//se tiene la fecha de derivacion, pero no la id de la unidad
			//buscar cama en que se encontraba el dia de la creacion del formulario
			$fecha = $info["fecha_creacion"];
		}else{
			//no posee informacion usar la fecha de creacion del dia en que se hizo la derivacion
			$fecha = ListaDerivados::where("caso", $idCaso)->first()->fecha_ingreso_lista;
		}

		$motivo_derivacion = 'Sin especificar';
		if(isset($info["motivo_derivacion"])){
			switch ($info["motivo_derivacion"]) {
				case '1':
					$motivo_derivacion = 'Cirugía Vascular';
					break;
				case '2':
					$motivo_derivacion = 'Cirugía cardiaca';
					break;
				case '3':
					$motivo_derivacion = 'Electrofisiología';
					break;
				case '4':
					$motivo_derivacion = 'Hemodinamia';
					break;
				case '5':
					$motivo_derivacion = 'Gran Quemado';
					break;
				case '6':
					$motivo_derivacion = 'Hematología';
					break;
				case '7':
					$motivo_derivacion = 'Oncología';
					break;
				case '8':
					$motivo_derivacion = 'Hemato-oncología';
					break;
				case '9':
					$motivo_derivacion = 'Caso social';
					break;
				case '10':
					$motivo_derivacion = 'UCI Pediatrica';
					break;
				case '11':
					$motivo_derivacion = 'UTI Pediatrica';
					break;
				case '12':
					$motivo_derivacion = 'Neurocirugía';
					break;
				case '13':
					$motivo_derivacion = 'Cardiocirugía infantil';
					break;
				case '14':
					$motivo_derivacion = 'Rescate Hospital de origen';
					break;
				case '15':
					$motivo_derivacion = 'Deficit de cama';
					break;
				case '16':
					$motivo_derivacion = 'Imagenología compleja';
					break;
				case '17':
					$motivo_derivacion = 'Trauma ocular grave';
					break;
				case '18':
					$motivo_derivacion = 'Neonatología';
					break;
				case '19':
					$motivo_derivacion = 'Ginecología-obstetricia';
					break;
				case '20':
					$motivo_derivacion = 'Oncología ginecológica';
					break;
			}
		}

		$tipo_cama = 'Sin especificar';
		if(isset($info['tipo_cama'])){
			if($info['tipo_cama'] == '0' || $info['tipo_cama'] == null){
				$tipo_cama = 'Básica';
			}else{
				if($info['tipo_cama'] == '1'){
					$tipo_cama = 'Básica';
				}elseif($info['tipo_cama'] == '2'){
					$tipo_cama = 'Media';
				}elseif($info['tipo_cama'] == '3'){
					$tipo_cama = 'Crítica';
				}elseif($info['tipo_cama'] == '4'){
					$tipo_cama = 'Domicilio';
				}
			}
			
		}
		$tipo_centro = 'Sin especificar';
		if(isset($info['tipo_centro'])){
			if($info['tipo_centro'] == null){
				$tipo_centro = 'Sin especificar';
			}else{
				if($info['tipo_centro'] == 'derivacion'){
					$tipo_centro = 'Derivación a otro establecimiento de la red pública';
				}elseif($info['tipo_centro'] == 'traslado extra sistema'){
					$tipo_centro = 'Derivación a institución privada';
				}elseif($info['tipo_centro'] == 'pendiente'){
					$tipo_centro = 'Pendiente';
				}
			}
			
		}

		$fecha_ida = 'Sin especificar';
		$fecha_rescate = 'Sin especificar';
		if(isset($info['tramo']) && $info['tramo'] == 'ida'){
			if($info['fecha_ida']){
				$fecha_ida = Carbon::parse($info['fecha_ida'])->format('d-m-Y H:i');
			}
		}elseif(isset($info['tramo']) && $info['tramo'] == 'ida-rescate'){
			if($info['fecha_ida']){
				$fecha_ida = Carbon::parse($info['fecha_ida'])->format('d-m-Y H:i');
			}
			if($info['fecha_rescate']){
				$fecha_rescate = Carbon::parse($info['fecha_rescate'])->format('d-m-Y H:i');
			}
		}

		$centro_derivacion = 'Sin especificar';
		if(isset($info['tipo_centro'])){
			if($info['tipo_centro'] == 'derivacion'){
				$establecimiento = Establecimiento::select('nombre')->where('id', $info['red_publica'])->first();
				$centro_derivacion = $establecimiento->nombre;
			}elseif($info['tipo_centro'] == 'traslado extra sistema'){
				$establecimiento = EstablecimientosExtrasistema::select('nombre')->where('id', $info['red_publica'])->first();
				$centro_derivacion = $establecimiento->nombre;
			}
		}

		$comuna_origen = 'Sin especificar';
		$comuna_destino = 'Sin especificar';
		$comunaOrigen = (isset($info['comuna_origen']))?Comuna::select('nombre_comuna')->where('id_comuna',$info['comuna_origen'])->first():"Sin especificar";
		$comunaDestino = (isset($info['comuna_destino']))?Comuna::select('nombre_comuna')->where('id_comuna',$info['comuna_destino'])->first():"Sin especificar";

		if(isset($comunaOrigen->nombre_comuna)){
			$comuna_origen = $comunaOrigen->nombre_comuna;
		}
		if(isset($comunaDestino->nombre_comuna)){
			$comuna_destino = $comunaDestino->nombre_comuna;
		}

		if($fecha != ""){
			$unidad = $this->infoUbicacionCasoDerviado($idCaso, $fecha);

			if(isset($unidad[0]->alias)){
				$info->put('nombreUnidad', $unidad[0]->alias);
			}else{
				//en caso que tenga fecha muy atras y no posea historial de camas
				$info->put('nombreUnidad', "Sin información");
			}
			$info->put('fecha_creacion',$fecha);	
		}

		$estado_paciente = 'sin especificar';
		if(isset($info['estado_paciente'])){
			if($info['estado_paciente'] == '1'){
				$estado_paciente = 'Paciente en curso';
			}elseif($info['estado_paciente'] == '2'){
				$estado_paciente = 'Paciente Derivado';
			}elseif($info['estado_paciente'] == '3'){
				$estado_paciente = 'Paciente Nulo';
			}elseif($info['estado_paciente'] == '4'){
				$estado_paciente = 'Cierre de caso';
			}
		}

		$movil = 'sin especificar';
		if(isset($info['movil'])){
			if($info['movil'] == '1'){
				$movil = 'Samu';
			}elseif($info['movil'] == '2'){
				$movil = 'Compra de servicios';
			}elseif($info['movil'] == '3'){
				$movil = 'Pendiente';
			}elseif($info['movil'] == '4'){
				$movil = 'Particular';
			}
		}

		$compra_servicio = 'sin especificar';
		if(isset($info['compra_servicio']) && $info['movil'] == 2){
			if($info['compra_servicio'] == '1'){
				$compra_servicio = 'Atacama SH';
			}elseif($info['compra_servicio'] == '2'){
				$compra_servicio = 'Altamira';
			}elseif($info['compra_servicio'] == '3'){
				$compra_servicio = 'otros';
			}
		}

		$compra_servicio_otro = 'sin especificar';
		if(isset($info['compra_servicio_otro']) && $info['compra_servicio'] == '3'){
				$compra_servicio_otro = $info['compra_servicio_otro'];
		}


		//info paciente
		$idPaciente = Caso::find($idCaso, ['paciente']);
		$fecha_termino = Caso::find($idCaso, ['fecha_termino']);
		$paciente = Paciente::find($idPaciente, ['rut','dv','nombre','apellido_paterno','apellido_materno','fecha_nacimiento']);
		$nombreCompleto = $paciente[0]["nombre"] ." ".$paciente[0]["apellido_paterno"] ." ".$paciente[0]["apellido_materno"];
		$rut = ($paciente[0]["rut"]) ? ($paciente[0]["rut"]) : "";
		$dv = ($paciente[0]["dv"] == 10) ? "K" : $paciente[0]["dv"];
		$rutDv = $rut . "-" .$dv; 

		$fecha_naci = $paciente[0]["fecha_nacimiento"];
		$fecha1 = new DateTime($fecha_naci);
		$fecha2 = new DateTime();
		$fechaF = $fecha1->diff($fecha2);
		$diferencia = '';
		$grupoEtareo = 'Sin información';

		$fecha_nacimiento = ($paciente[0]["fecha_nacimiento"]) ? date("d-m-Y", strtotime($paciente[0]["fecha_nacimiento"])) : 'Sin Especificar';

		if($fechaF->y == 0){
			$diferencia = $fechaF->format('%m meses %a dias');
		}else{
			$diferencia = $fechaF->format('%y años %m meses');
		}

		if($fecha_naci != null){
			$years = Carbon::parse($fecha_naci)->age;
			if($years >= 15){
				$grupoEtareo = 'Adulto'; 
			}else{
				$grupoEtareo = 'Pediatrico';
			}
		}		

		$Fhosp = ThistorialOcupaciones::where("caso", $idCaso)->whereNotNull("fecha_ingreso_real")->first();
		$fecha_hospitalizacion = ($Fhosp) ? Carbon::parse($Fhosp->fecha_ingreso_real)->format("d-m-Y H:i:s") : 'Sin Hospitalizar';
		
		$infoUnidad = DB::table('t_historial_ocupaciones as t')
			->join("camas as c", "c.id", "=", "t.cama")
			->join("salas as s", "c.sala", "=", "s.id")
			->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
			->where("caso", "=", $idCaso)
			->orderBy("t.created_at", "desc")
			->select("uee.id","uee.alias as nombre_unidad")
			->first();

		$fecha_egreso_derivacion = "Sin especificar";	
		if($fecha_termino->fecha_termino != null){
			$fecha_egreso_derivacion = Carbon::parse($fecha_termino->fecha_termino)->format("d-m-Y H:i:s");
		}	

		$establecimiento = Establecimiento::where("id",Auth::user()->establecimiento)->first();
		
		$pdf = PDF::loadView('Urgencia.DerivacionPacientePDF',
		[
		"info" => $info,
		"motivo_derivacion" => $motivo_derivacion,
		"tipo_cama" => $tipo_cama,
		"tipo_centro" => $tipo_centro,
		"centro_derivacion" => $centro_derivacion,
		"fecha_ida" => $fecha_ida,
		"fecha_rescate" =>$fecha_rescate,
		"comuna_origen" =>$comuna_origen,
		"comuna_destino" =>$comuna_destino,
		"estado_paciente" =>$estado_paciente,
		"movil" =>$movil,
		"compra_servicio" =>$compra_servicio,
		"compra_servicio_otro" =>$compra_servicio_otro,
		"nombreCompleto"=>$nombreCompleto,
		"rutDv"=>$rutDv,
		"grupoEtareo"=>$grupoEtareo,
		"fechaNacimiento"=>$fecha_nacimiento,
		"edad"=>$diferencia,
		"fechaHospitalizacion"=>$fecha_hospitalizacion,
		"nombreUnidad"=>$infoUnidad->nombre_unidad,
		"idUnidad" =>$infoUnidad->id,
		"fecha_egreso_derivacion" => $fecha_egreso_derivacion,
		"establecimiento" => $establecimiento
		]);
	return $pdf->download('DerivacionPaciente.pdf');
	//return $pdf->stream('DerivacionPaciente.pdf');
	}

	public function infoUbicacionCasoDerviado($idCaso, $fecha){
		$fecha = Carbon::parse($fecha)->format("Y-m-d H:m:s");
		return DB::select(DB::Raw("select u.id, u.alias
		from t_historial_ocupaciones as t
		join camas as c on c.id = t.cama
		join salas as s on s.id = c.sala
		join unidades_en_establecimientos as u on u.id = s.establecimiento
		where t.caso = $idCaso
		and t.motivo <> 'corrección cama'
		and 
		(
		(t.fecha < '$fecha' and t.fecha_liberacion >= '$fecha') 
		or (t.fecha < '$fecha' and t.fecha_liberacion is null)
		) 
		order by t.id asc
		limit 1"));
	}

	public function solicitarinfoFormularioDerivado(Request $request){
		$unidad = $this->infoUbicacionCasoDerviado($request->idCaso, $request->fecha);
		
		if(isset($unidad[0]->alias)){
			$ubicacion["nombre"] = $unidad[0]->alias;
			$ubicacion["id"] = $unidad[0]->id;
		}else{
			//en caso que tenga fecha muy atras y no posea historial de camas
			$ubicacion["nombre"]= "Sin información";
			$ubicacion["id"] = null;
		}
		return response()->json($ubicacion);
	}

	public function infoFormularioDerivado(Request $request){
		$info = FormularioDerivado::where('id_lista_derivados',$request->lista)->whereNull('fecha_cierre')->first();
		$info = collect($info);

		$nombreMedico = Medico::nombreMedico($info["id_medico"]);
		$info->put('nombreMedico', $nombreMedico);

		$fecha="";
		if(isset($info["id_unidad_deriva"]) && $info["id_unidad_deriva"] != null){
			//Se tiene el id_unidad_deriva y se puede buscar 
			$unidad = UnidadEnEstablecimiento::traductorNombreUnidad($info["id_unidad_deriva"]);
			$info->put('nombreUnidad', $unidad);
		}else if(isset($info["id_unidad_deriva"]) && $info["id_unidad_deriva"] == null){
			//se tiene la fecha de derivacion, pero no la id de la unidad
			//buscar cama en que se encontraba el dia de la creacion del formulario
			$fecha = $info["fecha_creacion"];
		}else{
			//no posee informacion usar la fecha de creacion del dia en que se hizo la derivacion
			$fecha = ListaDerivados::where("caso", $request->lista)->first()->fecha_ingreso_lista;
		}

		if($fecha != ""){
			$unidad = $this->infoUbicacionCasoDerviado($request->caso, $fecha);

			if(isset($unidad[0]->alias)){
				$info->put('nombreUnidad', $unidad[0]->alias);
			}else{
				//en caso que tenga fecha muy atras y no posea historial de camas
				$info->put('nombreUnidad', "Sin información");
			}
			$info->put('fecha_creacion',$fecha);	
		}
		return $info;
	}

	public function editarFormDerivado(Request $request){
		try {
			$derivado = FormularioDerivado::where('id_lista_derivados',$request->idLista)->whereNull('fecha_cierre')->first();
			$idCaso = $request->idCaso;
			DB::beginTransaction();
			if($derivado){
				$derivado->fecha_creacion = Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->fechaDerivacion))->format("'Y-m-d H:i:s'");
				$derivado->tipo_traslado = strip_tags($request->tipo_traslado);
				$derivado->motivo_derivacion = $request->motivo_derivacion;
				$derivado->id_unidad_deriva = $derivado->idUnidadFuncional;
				$derivado->tipo_cama = ($request->motivo_derivacion != 15) ? $request->tipo_cama :0;
				$derivado->detalle_derivacion = ($request->motivo_derivacion == 15) ? $request->detalle_derivacion : 0;
				$derivado->id_medico = ($request->id_medico) ? strip_tags($request->id_medico) : null;
				$derivado->ges = ($request->ges == 'si') ? true : false;
				$derivado->ugcc = ($request->ugcc == 'si') ? true : false;
				$derivado->t_ugcc = ($request->ugcc == 'si') ? $request->t_ugcc : 0;
				$derivado->tipo_centro = strip_tags($request->tipo_centro);
				$derivado->red_publica = ($request->tipo_centro == "derivacion")?$request->red_publica:null;
				$derivado->red_privada = ($request->tipo_centro == "traslado extra sistema")?$request->red_privada:null;
				$derivado->otro_derivacion = ($request->red_publica == 0) ? strip_tags($request->otro_derivacion) : '';
				$derivado->via_traslado = strip_tags($request->via_traslado);
				$derivado->detalle_via = ($request->via_traslado == 2) ? $request->detalle_via : 0;
				$derivado->tramo = strip_tags($request->tramo);
				if ($request->tramo == 'ida') {
					$derivado->fecha_ida = ($request->fechaIda) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->fechaIda))->format("'Y-m-d H:i:s'"): null;
					$derivado->fecha_rescate = null;
				}else if($request->tramo =='ida-rescate'){
					$derivado->fecha_ida = ($request->fechaIda) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->fechaIda))->format("'Y-m-d H:i:s'"): null;
					$derivado->fecha_rescate = ($request->fechaRescate) ? Carbon::createFromFormat("d-m-Y H:i", strip_tags($request->fechaRescate))->format("'Y-m-d H:i:s'"): null;
				}else{
					$derivado->fecha_ida = null;
					$derivado->fecha_rescate = null;
				}
				$derivado->comuna_origen = strip_tags($request->comuna_origen);
				$derivado->comuna_destino = strip_tags($request->comuna_destino);
				$derivado->estado_paciente = strip_tags($request->estado_paciente);
				$derivado->movil = strip_tags($request->movil);
				$derivado->compra_servicio = ($request->movil == 2) ? $request->compra_servicio : null;
				$derivado->compra_servicio_otro = ($derivado->compra_servicio != null) ? strip_tags($request->compra_servicio_otro) : '';
				$derivado->comentarios = strip_tags($request->comentarios);
				$derivado->save();
			}else{
				if ($request->idLista) {
					$this->ingresarFormularioDerivacion($request,$idCaso,$request->idLista);
				}
			}
			DB::commit();
			return response()->json(array("exito" => "Formulario Editado correctamente"));
		} catch (Exception $ex) {
			Log::error($ex);
			DB::rollback();
			return response()->json(array("error" => "Error al editar el formulario"));
		}
	}

	public function resumenHospDom(){
		$idEst = Session::get('idEstablecimiento');
		$pacientes_hosp_dom = DB::select(DB::Raw("
			select
			p.id as idPaciente,
			c.id as idCaso,
			p.sexo,
			p.fecha_nacimiento
			from hospitalizacion_domiciliaria as l  
			inner join casos as c on c.id = l.caso
			inner join pacientes as p on p.id = c.paciente
			left join 
			(select 
				max(e.id) as idEvolucion,
				c.id idCaso
				from hospitalizacion_domiciliaria as l  
				left join casos as c on l.caso = c.id
				left join t_evolucion_casos as e on e.caso = c.id
				left join riesgos as r on e.riesgo_id = r.id
				where c.establecimiento = $idEst
				and l.fecha_termino is null
				and e.riesgo_id is not null
				group by (c.id)
			) as e on e.idCaso= c.id 
			left join comuna as co on co.id_comuna = p.id_comuna
			where l.fecha_termino is null
		"));

		$coleccion_pacientes_hosp_dom = collect($pacientes_hosp_dom);

		$total_hospitalizados = count($pacientes_hosp_dom);
		
		$total_hombres = (count($coleccion_pacientes_hosp_dom)) ? count($coleccion_pacientes_hosp_dom->where("sexo", 'masculino')) : 0;
		
		$total_mujeres = (count($coleccion_pacientes_hosp_dom)) ? count($coleccion_pacientes_hosp_dom->where("sexo", 'femenino')) : 0;
		$total_0_19 = $coleccion_pacientes_hosp_dom->filter(function ($item) { 
			return $item->fecha_nacimiento != null && carbon::parse($item->fecha_nacimiento)->age <= 19;
		})->count();

		$total_20_64 = $coleccion_pacientes_hosp_dom->filter(function ($item) { 
			return $item->fecha_nacimiento != null && carbon::parse($item->fecha_nacimiento)->age >= 20 && carbon::parse($item->fecha_nacimiento)->age <= 64;
		})->count();

		$total_mayor_65 = $coleccion_pacientes_hosp_dom->filter(function ($item) { 
			return $item->fecha_nacimiento != null && carbon::parse($item->fecha_nacimiento)->age >= 65;
		})->count();

		$data = array(
			"total_hospitalizados" => $total_hospitalizados,
			"total_hombres" => $total_hombres,
			"total_mujeres" => $total_mujeres,
			"total_0_19" => $total_0_19,
			"total_20_64" => $total_20_64,
			"total_mayor_65" => $total_mayor_65
		);

		return response()->json(array("data"=>$data));
	}
}

?>

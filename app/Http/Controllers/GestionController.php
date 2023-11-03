<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Consultas;
use App\Models\UnidadEnEstablecimiento;
use App\Models\Establecimiento;
use App\Models\Sala;
use App\Models\Procedencia;
use App\Models\Unidad;
use App\Models\Indicacion;
use App\Models\Prevision;
use App\Models\Dieta;
use App\Models\Paciente;
use App\Models\Caso;
use App\Models\Derivacion;
use App\Models\HistorialOcupacion;
use App\Models\Riesgo;
use App\Models\ListaEspera;
use App\Models\HistorialDiagnostico;
use App\Models\EvolucionCaso;
use App\Models\Cama;
use App\Models\EstablecimientosExtrasistema;
use App\Models\Localizacion;
use App\Models\AgenteEtiologico;
use App\Models\CaracteristicasAgente;
use App\Models\ProcedimientoInvasivo;
use App\Models\Examen;
use App\Models\HospitalizacionDomiciliaria;
use App\Models\MensajeDerivacion;
use App\Models\ListaDerivados;
use App\Models\Documento;
use App\Models\HistorialBloqueo;
use App\Models\HistorialCamasUnidades;
use App\Models\PlanDeTratamiento;
use App\Models\Infecciones;
use App\Models\PacientesInfeccion;
use App\Models\CVC;
use App\Models\IAAS;
use App\Models\DerivacionesExtrasistema;
use App\Models\Reserva;
use App\Models\Comuna;
use App\Models\HistorialOcupacionesVista;
use App\Models\Usuario;
use App\Models\ListaTransito;
use App\Models\THistorialOcupaciones;
use App\Models\Medico;
use App\Models\DocumentoDerivacionCaso;
use App\Models\AreaFuncional;
use App\Models\ListaPabellon;
use App\Models\Sesion;
use App\Models\ListaDerivadosComentarios;
use App\Models\HojaCuraciones;
use App\Models\TipoCuidado;
use App\Models\InformeEpicrisis;
use App\Models\ApiConsultas;
use App\Models\PreAlta;

use App\Models\Especialidades;
use App\Models\EvolucionEspecialidad;
use App\Models\SolicitudTrasladoInterno;

use App\Models\Telefono;
use App\Models\TipoUnidad;

use App\Models\ConfiguracionVisitas;

use App\Models\EvolucionAcompanamiento;
use App\Models\EvolucionAtencion;



use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use View;
use Session;
use Auth;
use TipoUsuario;
use URL;
use DB;
use Log;
use Form;
use Cache;
use File;
use Excel;
use \nusoap_client;
use Response;
use Mail;
use Exception;
use UploadHandler;
use DateTime;
use Funciones;
use PDF;
use Crypt;
use Illuminate\Support\Collection;
use App\Models\HistorialSubcategoriaUnidad;
use App\Models\ArsenalFarmacia;

require_once app_path()."/lib/nusoap.php";

class GestionController extends Controller {

	public function __construct(){
		$this->unidad=0;

		$this->middleware(function ($request, $next) {
            $this->idEstablecimiento = Session::get('idEstablecimiento');

            return $next($request);
        });
	}

	public function rnInfo(Request $request){

		$caso = DB::table("casos")->where('id', $request->idCaso)->first();
		$paciente = DB::table("pacientes")->select('dv_madre','rut_madre','identificacion', 'rut', 'dv', 'rn')->where('id', $caso->paciente)->first();

		return response()->json($paciente);
	}

	public function camas($unidad){
		/* Comentado mientars */
		/* if(Auth::user()->tipo == 'medico'){
			return redirect()->route('indexPrincipal');
		} */


		$paciente = "";
		$id_sala = "";
		$id_cama = "";
		$caso = "";


		if(isset($_GET["paciente"])){
			$paciente = $_GET["paciente"];
		}
		if(isset($_GET["id_sala"])){
			$id_sala = $_GET["id_sala"];
		}
		if(isset($_GET["id_cama"])){
			$id_cama = $_GET["id_cama"];
		}
		if(isset($_GET["caso"])){
			$caso = $_GET["caso"];
		}
		$tipoUsuario=Auth::user()->tipo;
		
		$uni = UnidadEnEstablecimiento::select('id','cama_temporal')->where([['url',"=",$unidad], ["establecimiento", Auth::user()->establecimiento]] )->first();
		
		$id_unidad = $uni->id;
		
		$restriccion_usuario = Consultas::restriccionPersonal($id_unidad);

		if($restriccion_usuario != false){
			return View::make("Errors/NoAccess", [
				"error" => "Este servicio se encuentra restringido"
				]);
		}

		$admin=TipoUsuario::ADMIN;
		$permisos_establecimiento = Session::get("permisos_establecimiento");
		$motivoBloqueo=Consultas::getMotivosBloqueo();
		$alias=UnidadEnEstablecimiento::getAliasPorUrl($unidad);
		$idEn=UnidadEnEstablecimiento::getIdEstablecimiento($this->idEstablecimiento, $unidad);
		$servicios=UnidadEnEstablecimiento::getServiciosEstablecimientosDistintosA($unidad);
		$salas=Sala::getSalasEstablecimientoSelect($idEn);
		$motivos = Consultas::getMotivosLiberacion();
		$unidad_obj = Session::get("unidades")->KeyBy("alias")->get($unidad);
		$unidad_objetivo = UnidadEnEstablecimiento::where("url",$unidad)->first()->id;
		$cama_temporal = $uni->cama_temporal;
		$unidad_id = UnidadEnEstablecimiento::where("url",$unidad)->first()->id;
		$subcategoria = HistorialSubcategoriaUnidad::select("id_subcategoria")->where('id_unidad',$unidad_id)->where('visible',true)->first();

		$procedencias = [];
		foreach(Procedencia::all() as $proc){
			if ($proc->nombre == "Otro") {
				$ultimo = [$proc->nombre , $proc->id];
			}else{
				$procedencias[$proc->id] = $proc->nombre;
			}
		}
		$procedencias[$ultimo[1]] = $ultimo[0];

		/* $unidad_obj->id  iba dentro*/
		$categorizaciones = DB::select(DB::Raw("select caso as id
        from historial_ocupaciones_vista
        where id_servicio = $id_unidad
        and rk = 1
        and fecha_liberacion is null"));

		/* inner join t_evolucion_casos as tc on tc.caso = c.id
		and tc.riesgo_id is not null */
		$response_categoria = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

		$basica = 0;
		$media = 0;
		$critica = 0;
		foreach($categorizaciones as $categorizacion){

			$categoria = DB::table("t_evolucion_casos as tc")
							->leftjoin("riesgos as r", "r.id","=","tc.riesgo_id")
							//->whereNotNull("tc.riesgo_id")
							->where("tc.caso", $categorizacion->id)
							->orderBy("tc.fecha","desc")
							->first();

			$ultima_ocupacion = THistorialOcupaciones::where('t_historial_ocupaciones.caso', $categorizacion->id)
                ->orderBy('t_historial_ocupaciones.id', "desc")
				->first();



			if(is_null($ultima_ocupacion->fecha_ingreso_real)){
				$response_categoria[14] += 1;

			}else{

				$respuesta2 = Consultas::restriccionCategorizacionCama($categorizacion->id);

				$restriccion_tiempo = $respuesta2->original["restriccion"];
				if(!is_null($categoria)) {
					if ($categoria->riesgo_id != '') {

						if ($categoria->urgencia != true) {
							if ("A1" == $categoria->categoria) {
								$response_categoria[0] += 1;
								$critica ++;

							} else if ("A2" == $categoria->categoria) {
								$response_categoria[1] += 1;
								$critica ++;

							} else if ("A3" == $categoria->categoria) {
								$response_categoria[2] += 1;
								$critica ++;
							} else if ("B1" == $categoria->categoria) {
								$response_categoria[3] += 1;
								$critica ++;

							} else if ("B2" == $categoria->categoria) {
								$response_categoria[4] += 1;
								$critica ++;

							} else if ("B3" == $categoria->categoria) {
								$response_categoria[5] += 1;
								$media ++;

							} else if ("C1" == $categoria->categoria) {
								$response_categoria[6] += 1;
								$media ++;

							} else if ("C2" == $categoria->categoria) {
								$response_categoria[7] += 1;
								$media ++;

							} else if ("C3" == $categoria->categoria) {
								$response_categoria[8] += 1;
								$basica ++;

							} else if ("D1" == $categoria->categoria) {
								$response_categoria[9] += 1;
								$basica ++;

							} else if ("D2" == $categoria->categoria) {
								$response_categoria[10] += 1;
								$basica ++;

							} else if ("D3" == $categoria->categoria) {
								$response_categoria[11] += 1;
								$basica ++;

							}
						}else{
							if($restriccion_tiempo == false){
								$response_categoria[13] += 1;
							}else{
								$response_categoria[14] += 1;
							}
							$basica +=1;
						}
					}else{
						if($restriccion_tiempo == false){
							$response_categoria[13] += 1;
						}else{
							$response_categoria[14] += 1;
						}
						$basica +=1;
					}
				}else{
					if($restriccion_tiempo == false){
						$response_categoria[13] += 1;
					}else{
						$response_categoria[14] += 1;
					}
					$basica +=1;
				}
			}
			$response_categoria[12] += 1;


		}
		unset($motivos['traslado interno']);

		$area_funcional = AreaFuncional::join('unidades_en_establecimientos','area_funcional.id_area_funcional','=','unidades_en_establecimientos.id_area_funcional')
		->select('area_funcional.nombre')
		->where('unidades_en_establecimientos.establecimiento', Session::get('idEstablecimiento'))
		->where('area_funcional.id_area_funcional',$unidad_obj->id_area_funcional)
		->first();

		$nombre_area = "";
		$nombre_area = $area_funcional->nombre;
		$array_nombre_area = explode(" ",$nombre_area);
		$eliminado = array_pop($array_nombre_area);
		$nuevo_area = implode(" ", $array_nombre_area);

		$lista_servicios = DB::table('servicios_vista')
							->select('id_unidad','alias','tooltip')
							->where('establecimiento', '=', Auth::user()->establecimiento)
							->orderBy('alias')
							->get();
		$atributos = [];
			foreach($lista_servicios as $key => $servicio){
				$servicios[$servicio->id_unidad] =  $servicio->alias;
				$atributos[$servicio->id_unidad] = ["data-toggle" =>"tooltip", "title"=>$servicio->tooltip];
		}

		//Calcular dotacion
		$dotacion = Unidad::calcularDotacion($unidad_obj->id,$basica,$media,$critica);
		//para solicitud de traslado internos
		$establecimiento = $this->idEstablecimiento;
		$unidades = UnidadEnEstablecimiento::selectGenerarMapaServicios();

		return View::make("Gestion/Camas", [
			"unidad" => $unidad,
			"motivo" => $motivos,
			"alias" => $alias,
			"salas" => $salas,
			"riesgo" => Consultas::getRiesgos(),
			"tipoUsuario" => $tipoUsuario,
			"admin" => $admin,
			"motivoBloqueo" => $motivoBloqueo,
			"servicios" => $servicios,
			"atributos"=> $atributos,
			"prevision" => Prevision::getPrevisiones(),
			"permisos_establecimiento" => $permisos_establecimiento,
			"dieta" => Dieta::getDietas(),
			"procedencias" => $procedencias,
			//"some" => $some,
			"id_unidad"=> $id_unidad,
			"area_funcional" => $nuevo_area,
			"detalle_area" => $eliminado,
			'comunas' => Comuna::where('id_region', '=', 3)->pluck('nombre_comuna','id_comuna'),
			'regiones' => Consultas::getRegion(),
			'paciente' => $paciente,
			'sala' => $id_sala,
			'cama' => $id_cama,
			'caso_id' => $caso,
			'medicos' => Medico::getMedicos(),
			'categorizacion' =>$response_categoria,
			'unidades' => $unidades,
			'dotacion' => $dotacion,
			'especialidad' => Especialidades::pluck('nombre','id'),
			"sub_categoria" => ($subcategoria) ? $subcategoria->id_subcategoria : null,
			"cama_temporal" => $cama_temporal
		]);
	}



	public function unidad($unidad){
		return $this->getCamas($unidad);
	}

	public function categorizacion($ocupacion){
		$restriccion_tiempo=false;
		$imagen = "SIN_CATEGORIZACION.png";
		///////////////////////////
		//Si tiene mas de 8 horas//
		///////////////////////////

		$fecha_ingreso_real = Carbon::parse($ocupacion->fecha_ingreso_real);

		$fecha_ingreso_real_hr = $fecha_ingreso_real->format("H");
		$dia_actual = Carbon::now()->format("Y-m-d");
		$tiempo_estadia = $fecha_ingreso_real->diffInHours(Carbon::now());
		$dia_hospitalizacion_mas_2 = $fecha_ingreso_real->addDays(2)->format("Y-m-d");


		/////////////////////////////////////////////////
		//Si tiene menos de 8 hrs, no puede categorizar//
		/////////////////////////////////////////////////

		if($tiempo_estadia <= 8){
			////////////////
			//bloqueo cama//
			////////////////
			$imagen = "SIN_CATEGORIZAR_candado.png";
			$restriccion_tiempo=true;
		}

		if($fecha_ingreso_real_hr >= 16 && ($tiempo_estadia >= 8)){
			////////////////
			//bloqueo cama//
			////////////////

			$imagen = "SIN_CATEGORIZAR_candado.png";
			$restriccion_tiempo=true;

			if($dia_actual >= $dia_hospitalizacion_mas_2){
				///////////////////
				//desbloqueo cama//
				///////////////////
				$restriccion_tiempo=false;
				$imagen = "SIN_CATEGORIZACION.png";
			}
		}
		return array($imagen, $restriccion_tiempo);
	}

	public function getCamas($unidad){

		$id_unidad = UnidadEnEstablecimiento::select('id')->where([['url',"=",$unidad], ["establecimiento", Auth::user()->establecimiento]] )->first()->id;

		$restriccion_usuario = Consultas::restriccionPersonal($id_unidad);
		if($restriccion_usuario != false){
			return View::make("Errors/NoAccess", [
				"error" => "Este servicio se encuentra restringido"
				]);
		}

		DB::connection()->enableQueryLog();

		$url = URL::to('/');
		$tiene=false;
		$camas=array();
		$nombres = array();
        $unidad_obj =  Session::get("unidades")->KeyBy("alias")->get($unidad);
        $no_some =  $unidad_obj->some === null && Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO && Session::get("usuario")->tipo !== TipoUsuario::IAAS;
		$consulta = Consultas::ultimoEstadoCamas();
		$consulta = Consultas::addTiempoBloqueo($consulta);
		$consulta = Consultas::addTiempoReserva($consulta);

		$consulta = Consultas::addTiemposOcupaciones($consulta);
		$consulta = $consulta->addSelect("s.visible");

		$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
		->where("est.id", "=", $this->idEstablecimiento)
		->where("ue.url", "=", $unidad)
		->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
		->whereNotNull("id_sala")
		->where("s.visible", true)
		->orderBy("s.nombre", "asc")
		->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc"); // esta linea desordena todo;

		$ocupacionesCamaSala = $consulta->get();

		$i = 0;
		$ind = [];
		foreach($ocupacionesCamaSala as $ocupacion){
			$ind[$ocupacion->id_sala] = $i;
			$i++;
		}
		
		foreach($ocupacionesCamaSala as $ocupacion){

			$nombre_sala = "Sala sin nombre ({$ocupacion->id_sala})";
			if(!empty($ocupacion->nombre_sala)){
				$nombre_sala = $ocupacion->nombre_sala;
				if($ocupacion->descripcion_sala != null){
					$nombre_sala .= " ".$ocupacion->descripcion_sala;
				}

			}
			$nombres[$ind[$ocupacion->id_sala]] = $nombre_sala;
			$imagen="SIN_PACIENTE.png";

			$descripcionCama = ($ocupacion->cama_descripcion)?" (<strong style='color:black'>$ocupacion->cama_descripcion</strong>)":"";

			$reconvertida = "nada.png";
			$sexo = "nada.png";
			$estadia_promedio = "nada.png";
			$alta_clinica = "nada.png";
			$iaas_img = "nada.png";
			$derivado = "nada.png";
			$pabellon = "nada.png";


			//si cama esta desocupada
			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);

					if(empty($horas)) $horas="<br><br>";
					$renovada=($ocupacion->renovada) ? 1 : 0;
					$click="";
					if(Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ){


					$click="onclick='marcarCama(-1, -1, false);getPacienteReserva(\"$ocupacion->id_paciente\",\"$ocupacion->id_sala\",\"$ocupacion->id_cama_unq\",\"$ocupacion->id_caso\", $renovada, \"$ocupacion->id_paciente\")' data-id= $ocupacion->id_paciente";
					}
					$opcion="<a $class $click>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama $descripcionCama</figcaption> </figure> </a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "cama_bloqueada.png";
					$horas=Consultas::formatTiempoBloqueo($ocupacion->fecha_bloqueo);
					if(empty($horas)) $horas="<br><br>";

					$class= "class='cursor'" ;
					$click="onclick='abrirDesbloquear(\"$ocupacion->id_cama_unq\");'";
					$opcion="<a $class $click><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama $descripcionCama</figcaption> </figure></a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){



					$imagen = "SIN_PACIENTE.png";
					$reconvertida = "reconvertida.png";
					$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual) ." - ". $ocupacion->id_cama." ".$descripcionCama;
					$click="";
					$class="";

					if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::MASTER  || Session::get("usuario")->tipo == TipoUsuario::MASTERSS){
						$click="onclick='marcarCama(\"$ocupacion->id_sala\", \"$ocupacion->id_cama_unq\", true)' data-id= $ocupacion->id_paciente";
					}

					$opcion="<a $class $click><figure> <img src='$url/img/$imagen' class='imgCama' />
					<figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
					<img src='$url/img/$derivado' class='imgPunto' />
					<img src='$url/img/$pabellon' class='imgPunto' />
					<br>
					$nombre
					</figcaption>
					</figure></a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}
				else{


					//camaVerde.png
					$tiene=true;
					$click="";
					if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA){
						$click="onclick='marcarCama(\"$ocupacion->id_sala\", \"$ocupacion->id_cama_unq\", false)' data-id= $ocupacion->id_paciente";
					}

					$class=($no_some) ? "class='cursor $ocupacion->id_paciente'" : "";
					$opcion="<a $class $click>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$ocupacion->id_cama $descripcionCama</figcaption> </figure> </a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
					}
			} //fin cama desocupada
			// inicio if SI ESTA OCUPADA
			else{

				$derivados = DB::table("lista_derivados")
					->where("caso", $ocupacion->id_caso)
					->whereNull("fecha_egreso_lista")
					->first(['id_lista_derivados']);

				$estaEnPabellon = DB::table("lista_pabellon")
				->where	("id_caso", $ocupacion->id_caso)
				->whereNull("fecha_salida")
				->first(['id_pabellon']);

				if($estaEnPabellon){
					$pabellon = "pabellon2.png";
				}

				if($derivados){
					$derivado = "derivado.png";
				}

				if($ocupacion->sexo == "masculino"){
					$sexo = "hombre.png";
				}
				if($ocupacion->sexo == "femenino"){
					$sexo = "mujer.png";
				}
				if($ocupacion->sexo == "indefinido"){
					$sexo = "indefinido.png";
				}
				if($ocupacion->sexo == "desconocido"){
					$sexo = "desconocido.png";
				}
				if($ocupacion->ocupado !== null){
					$nombre="";
					if($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
						$nombre="(".UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual).") ";
						$reconvertida = "reconvertida.png";
					}

					$horas=Consultas::formatTiempoOcupacion($ocupacion->fecha_ingreso_real, $ocupacion->fecha_liberacion);


					$caption = (empty($ocupacion->riesgo)) ? $horas : $ocupacion->riesgo ." - ". $horas;
					$caption=$nombre.$caption." - ".$ocupacion->id_cama." ".$descripcionCama;
					// $click="onclick='marcarCama(-1, -1, false);getPaciente(\"$ocupacion->id_paciente\",\"$ocupacion->id_sala\",\"$ocupacion->id_cama_unq\",\"$ocupacion->id_caso\", \"$ocupacion->id_paciente\", this)' data-id= $ocupacion->id_paciente";
					// $class="class='cursor $ocupacion->id_paciente'";

					$infeccion=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$ocupacion->id_caso and i.caso=$ocupacion->id_caso and i.fecha_termino is null) as re"
         			))->get();
					if(count($infeccion))
					{
						$iaas_img = "iaas.png";
					}
					//cama sin riesgo (gris)
					if($ocupacion->riesgo == null){

						//Ultima Usada
						$respuesta2 = Consultas::restriccionCategorizacionCama($ocupacion->id_caso);
                        $resultado = $respuesta2->original;

                        $imagen = $resultado["imagen"];
                        $restriccion_tiempo = $resultado["restriccion"];
					}

					//cama ANARILLA
					elseif($ocupacion->riesgo == "B3" || $ocupacion->riesgo == "C1" || $ocupacion->riesgo == "C2"){
						$imagen = "RIESGO_B.png";
					}

					//Cama VERDE
					elseif($ocupacion->riesgo[0]=="D" || $ocupacion->riesgo == "C3"){
						$imagen = "RIESGO_D.png";
					}

					//Cama ROJA
					elseif($ocupacion->riesgo[0]=="A" ||$ocupacion->riesgo == "B1" || $ocupacion->riesgo == "B2"){
						$imagen = "RIESGO_A.png";
					}

					//demas usuarios no ven los riesgos que ponen los de urgencia
					if($ocupacion->id_usuario && Session::get('usuario')->tipo != 'usuario'){

						$tipo_usuario = Usuario::find($ocupacion->id_usuario,['tipo']);
						if($tipo_usuario->tipo == 'usuario'){
							$imagen = "SIN_CATEGORIZACION.png";
							$caption = $horas;
							$caption = $nombre.$caption." - ".$ocupacion->id_cama." ".$descripcionCama;
						}
					}

					//cama naranja
					if($ocupacion->fecha_ingreso_real == null){
						$imagen = "cama_reservada.png";
					}

					if($ocupacion->fecha_liberacion == null && $ocupacion->fecha_alta != null){
						$alta_clinica = "alta_clinica.png";
					}

					$cie10=DB::table(DB::raw("(SELECT id_cie_10 FROM diagnosticos WHERE caso=$ocupacion->id_caso AND id_cie_10 IS NOT NULL ORDER BY fecha ASC LIMIT 1) as a"))->first();
					if($cie10)
					{
						$estadia_promedio_=DB::table(DB::raw("(SELECT estadia_promedio FROM cie_10 WHERE id_cie_10='$cie10->id_cie_10') as a"))->first();
						if($estadia_promedio_)
						{
							if (strstr($horas, 'd', true) > $estadia_promedio_->estadia_promedio) {
								$estadia_promedio = "sobre_promedio.png";
							}
						}

					}


					$click="onclick='marcarCama(-1, -1, false);getPaciente(\"$ocupacion->id_paciente\",\"$ocupacion->id_sala\",\"$ocupacion->id_cama_unq\",\"$ocupacion->id_caso\", \"$ocupacion->id_paciente\", this)' data-id= $ocupacion->id_paciente data-cama=$imagen";
					$class="class='cursor $ocupacion->id_paciente'";
					$opcion="<a $click $class>  <figure> <img src='$url/img/$imagen' class='imgCama' />

					<figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
					<img src='$url/img/$derivado' class='imgPunto' />
					<img src='$url/img/$pabellon' class='imgPunto' />
						<br>
						$caption
					</figcaption> </figure> </a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}

				elseif($ocupacion->reservado !== null){
					$imagen = "camaAmarillo.png";
					$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);
					if(empty($horas)) $horas="<br><br>";
					$renovada=($ocupacion->renovada) ? 1 : 0;
					$click="";
					if($no_some) $click="onclick='marcarCama(-1, -1, false);getPacienteReserva(\"$ocupacion->id_paciente\",\"$ocupacion->id_sala\",\"$ocupacion->id_cama_unq\",\"$ocupacion->id_caso\", $renovada, \"$ocupacion->id_paciente\")' data-id= $ocupacion->id_paciente";

					$opcion="<a class='cursor $ocupacion->id_paciente' $click>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama $descripcionCama</figcaption> </figure> </a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "camaNegra.png";
					$horas=Consultas::formatTiempoBloqueo($ocupacion->fecha_bloqueo);
					if(empty($horas)) $horas="<br><br>";
						$click="onclick='abrirDesbloquear(\"$ocupacion->id_cama_unq\");'";
						$class="class='cursor'";
						$opcion="<a $class $click><figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$horas - $ocupacion->id_cama $descripcionCama</figcaption> </figure></a>";
						$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);

				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$imagen = "camaAzul.png";
					$reconvertida = "reconvertida.png";
					$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual) ." - ". $ocupacion->id_cama." ".$descripcionCama;
					$click="";

					$click="onclick='marcarCama(\"$ocupacion->id_sala\", \"$ocupacion->id_cama_unq\", true)' data-id= $ocupacion->id_paciente";
					$opcion="<a $class $click> <figure> <img src='$url/img/$imagen'class='imgCama' />
					<figcaption>
					<img src='$url/img/$sexo' class='imgPunto' />
					<img src='$url/img/$reconvertida' class='imgPunto' />
					<img src='$url/img/$estadia_promedio' class='imgPunto' />
					<img src='$url/img/$alta_clinica' class='imgPunto' />
					<img src='$url/img/$iaas_img' class='imgPunto' />
					<img src='$url/img/$derivado' class='imgPunto' />
					<br>
					$nombre</figcaption> </figure></a>";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno);
				}
			}

		}

		$response=array("nombres" => $nombres, "salas" => $camas, "tiene"=>$tiene, /* "sql"=>$ocupacionesCamaSala, */ "diagnNull");
		return response()->json($response);
	}

	public function obtenerCamasLista($unidad=null, $est=null){
		$response = array();
		$est = ($est === null) ? $this->idEstablecimiento : $est;
		$ocupacionesCamaSala = Consultas::ultimoEstadoCamas();

		if($unidad  == "TODOS" || $unidad === null){
			$unidad = null;
		}

		$ocupacionesCamaSala = Consultas::addTiempoBloqueo($ocupacionesCamaSala);
		$ocupacionesCamaSala = Consultas::addTiempoReserva($ocupacionesCamaSala);
		$ocupacionesCamaSala = Consultas::addTiemposOcupaciones($ocupacionesCamaSala);
		$ocupacionesCamaSala = $ocupacionesCamaSala

			->leftJoin("tipos_cama as ttc", "ttc.id", "=", "cm.tipo")
			->addSelect("s.visible")
			->addSelect("cs.id as caso_id")
			->addSelect("cs.fecha_ingreso", "ttc.nombre as tipo")
			->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
			->addSelect("ueactual.tipo_unidad")
			->where("est.id", "=", $est)
			//->where("ue.url", "=", $unidad)
			->when($unidad, function($query,$unidad){
					return $query->where("ue.url","=",$unidad);
				})
			->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
			->whereNotNull("id_sala")
			->where("s.visible", "=", true)
			->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
			->get();

		foreach($ocupacionesCamaSala as $ocupacion){

			$servicio_a_cargo = '';
			$area_funcional_a_cargo = '';
			if($ocupacion->id_caso != null){
				$evolucion = EvolucionCaso::where("caso", $ocupacion->id_caso)
				->orderBy("updated_at", "desc")
				->first();
				if(isset($evolucion)){
					if($evolucion->id_complejidad_area_funcional != null){
						$servicio_a_cargo = $evolucion->complejidad_area_funcional->servicios->nombre_servicio;
						$area_funcional_a_cargo = $evolucion->complejidad_area_funcional->area->nombre;
					}
				}


			}

			$nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
			$estado = "";
			$nombre = $ocupacion->nombrePaciente." ".$ocupacion->apellidoPaterno." ".$ocupacion->apellidoMaterno;
			$rut = (empty($ocupacion->rut)) ? "" : Paciente::formatearRut($ocupacion->rut, $ocupacion->dv);


			$hd = HistorialDiagnostico::select("diagnostico","comentario", "id_cie_10")
					->where("caso","=",$ocupacion->caso_id)->orderBy("id","desc")->first();
			$diagnostico = ucwords($hd["diagnostico"]);
			$comentario_diagnostico = $hd["comentario"];
			$cie10_diagnostico = $hd["id_cie_10"];

			//con esto se ve que categoria tiene el paciente
			$categorizacion = DB::table('t_evolucion_casos as e')
								->select("e.fecha as fecha", "r.categoria as categoria")
								->select('r.categoria')
								->leftjoin('riesgos as r', 'r.id','=','e.riesgo_id')
								->orderby('e.fecha','asc')
								->where('e.caso','=',$ocupacion->caso_id)
								->whereIn('e.urgencia', [false, null])
								->first();

			$categorizacion_2 = DB::table('t_evolucion_casos as e')
								->select("e.fecha as fecha", "r.categoria as categoria")
								->select('r.categoria')
								->leftjoin('riesgos as r', 'r.id','=','e.riesgo_id')
								->orderby('e.fecha','desc')
								->where('e.caso','=',$ocupacion->caso_id)
								->whereIn('e.urgencia', [false, null])
								->first();

			$primera_categorizacion = "";
			$ultima_categorizacion = "";
			if($categorizacion != null){
				$primera_categorizacion = $categorizacion->categoria;
				$ultima_categorizacion = $categorizacion_2->categoria;
			}

			$fechas_paciente = DB::table("casos as c")
									->select("c.fecha_ingreso2 as solicitud", "t.fecha as asignacion")
									->leftjoin("t_historial_ocupaciones as t", "t.caso", "=", "c.id")
									->where("c.id",$ocupacion->caso_id)
									->first();

			$solicitud = "";//fecha solicitud de cama dentro del solicitar cama
			$asignacion = ""; //fecha en que se asigno la cama la priemra vez, no importa cuando se cambio
			$hospitalizacion = "";	//fecha de ingreso real	cuando un paciente se hospitalizo
			if($fechas_paciente != null){
				if($fechas_paciente->solicitud != null){
					$solicitud = $fechas_paciente->solicitud;
				}
				if($fechas_paciente->asignacion != null){
					$asignacion = $fechas_paciente->asignacion;
				}
			}

			$hospitalizado = DB::table("t_historial_ocupaciones as t")
					->select("t.fecha_ingreso_real as hospitalizacion")
					->where("t.caso",$ocupacion->caso_id)
					->whereNotNull("t.fecha_ingreso_real")
					->first();
			if($hospitalizado != null){
				$hospitalizacion = $hospitalizado->hospitalizacion;
			}

			$horas=Consultas::formatTiempoReserva($ocupacion->reserva_queda);
			$segundos = $this->tiempoEstada($ocupacion->reserva_queda);
			$cama = $ocupacion->id_cama;
			$nombreUnidad = $ocupacion->unidad;

			$edad=Paciente::edad($ocupacion->fecha_nacimiento);
			$n_cama = $ocupacion->xx;
			$opciones2 = '';
			if(Session::get('usuario')->tipo != 'director' && Session::get('usuario')->tipo != 'medico_jefe_servicio'){
				$opciones2="<li><a role='menuitem' tabindex='-1' onclick='plan($ocupacion->id_paciente)' class='cursor'>Ingresar</a></li>";
			}
			  $opciones=(empty($ocupacion->nombrePaciente)) ? "" :"<div class='dropdown'>
			    <button class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown' aria-expanded='false'>Plan de tratamiento
			    <span class='caret'></span></button>
			    <ul class='dropdown-menu'>
			      ".$opciones2."
			      <li><a role='menuitem' tabindex='-1' onclick='getPlanTratamiento($ocupacion->id_paciente)' class='cursor'>Ver histórico</a></li>
			    </ul>
			  </div>";
			$tipo_cama = $ocupacion->tipo;
			if ($ocupacion->fecha === null){
				if($ocupacion->reservado !== null){
					$estado = "Reservada";
				}
				elseif($ocupacion->bloqueado !== null){
					$estado = "Bloqueada";
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$estado = "Reconvertida";
				}
				else{
					$estado = "Libre";
				}
			}
			else{
				if($ocupacion->ocupado !== null){
					$estado = "Ocupada";
					$horas=Consultas::formatTiempoOcupacion($ocupacion->fecha_ingreso, $ocupacion->fecha_liberacion);
					$segundos = $this->tiempoEstada($ocupacion->fecha_ingreso, $ocupacion->fecha_liberacion);
				}

				elseif($ocupacion->reservado !== null){
					$estado = "Reservada";
				}
				elseif($ocupacion->bloqueado !== null){
					$estado = "Bloqueada";
				}
				elseif($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
					$estado = "Reconvertida";
				}
			}

			if($estado == "Libre" || $estado == "Bloqueada"){
				$edad = "";
				$primera_categorizacion = "";
				$ultima_categorizacion = "";
			}

			//grupo etario
			$info = TipoUnidad::descripcion($ocupacion->tipo_unidad);
			$grupo_etario = ($info->descripcion) ? $info->descripcion : "Sin Información";

			$response[]=array(
				$nombreUnidad, $nombre_sala, $cama, $tipo_cama, $diagnostico, $nombre, $rut, $primera_categorizacion, $solicitud, $estado, $horas, $segundos, $n_cama,$edad,$opciones, $ultima_categorizacion, $asignacion,$hospitalizacion, $servicio_a_cargo, $area_funcional_a_cargo,$comentario_diagnostico, $cie10_diagnostico,$grupo_etario);
		}
		
		return response()->json($response);
	}

	public function obtenerCamasCenso($unidad = null, $est = null){

        $response = [];
        $est = ($est === null) ? $this->idEstablecimiento : $est;

        if ($unidad == "TODOS" || $unidad === null) {
            $unidad = null;
            return response()->json($response);
        }

        $establecimiento= DB::table("establecimientos as e")
                            ->select("e.id")
                            ->join("unidades_en_establecimientos as u","e.id","=", "u.establecimiento")
                            ->where("u.url",$unidad)
                            ->first()->id;

        //(total camas servicio, camas disponibles servicio, camas no disponible servicio, total camas hospital, camas total hospital, camas no disponibles hospital)

        //Total Camas servicio
        $camas = DB::table("camas_habilitadas_vista as c")
                    ->join("salas as s", "s.id","=","c.sala")
                    ->join("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
                    ->where("u.url",$unidad)
                    ->where("u.establecimiento", "=", $est)
                    ->count();

        //camas no disponibles en servicio
        $camas_ocupadas = DB::table("t_historial_ocupaciones_vista as c")
                    ->join("unidades_en_establecimientos as u", "u.id", "=", "c.id_servicio")
                    ->where("u.url",$unidad)
                    ->where("u.establecimiento", "=", $est)
                    ->whereNull("c.fecha_liberacion")
                    ->whereNull("c.motivo")
                    ->count();

        //camas disponibles servicio()
        $camas_disponibles = $camas - $camas_ocupadas;
        $porcentaje_disponibles_ser = round($camas_disponibles/$camas*100,1);
        $porcentaje_ocupadas_ser = round($camas_ocupadas/$camas*100,1);

        //Total camas de hospital
        $camas_hospital = DB::table("camas_habilitadas_vista as c")
                    ->join("salas as s", "s.id","=","c.sala")
                    ->join("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
					->where("u.establecimiento",$establecimiento)
					->where("s.visible", true)
					->where("u.visible", true)
                    ->count();
        //Total camas no disponibles en hospital
        $camas_ocupadas_hospital = DB::table("t_historial_ocupaciones_vista as c")
                    ->join("unidades_en_establecimientos as u", "u.id", "=", "c.id_servicio")
                    ->where("u.establecimiento",$establecimiento)
                    ->whereNull("c.fecha_liberacion")
                    ->whereNull("c.motivo")
                    ->count();

        //camas disponibles hospital
        $camas_disponibles_hospital = $camas_hospital - $camas_ocupadas_hospital;
        $porcentaje_disponibles_hos = round($camas_disponibles_hospital/$camas_hospital*100,1);
        $porcentaje_ocupadas_hos = round($camas_ocupadas_hospital/$camas_hospital*100,1);

        $response = [$camas,$camas_disponibles." (".$porcentaje_disponibles_ser."%)",$camas_ocupadas." (".$porcentaje_ocupadas_ser."%)",$camas_hospital,$camas_disponibles_hospital." (".$porcentaje_disponibles_hos."%)",$camas_ocupadas_hospital." (".$porcentaje_ocupadas_hos."%)"];

        return response()->json($response);
    }

    public function obtenerUsoCamasCenso($unidad = null, $est = null){

        $response = [];
        $est = ($est === null) ? $this->idEstablecimiento : $est;


        if ($unidad == "TODOS" || $unidad === null) {
            $unidad = null;
            return response()->json($response);
        }


        //(pieza, cama, ficha, nombre paciente)

        //camas en uso del servicio
        $camas_ocupadas = DB::table("t_historial_ocupaciones_vista as t")
                    ->select("s.nombre as pieza", "c.id_cama as cama", "f.ficha_clinica as ficha", "p.nombre as nombre", "p.apellido_paterno as paterno", "p.apellido_materno as materno", "p.rut", "p.dv", "f.id as caso","f.id_medico","p.fecha_nacimiento")
                    ->join("casos as f", "f.id", "=", "t.caso")
                    ->join("pacientes as p", "p.id", "=", "f.paciente")
                    ->join("camas as c", "c.id", "=", "t.cama")
                    ->join("salas as s", "s.id", "=", "c.sala")
                    ->join("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
                    ->where("u.url",$unidad)
                    ->where("u.establecimiento", "=", $est)
                    ->whereNull("t.fecha_liberacion")
                    ->whereNull("t.motivo")
                    ->orderBy("s.nombre", "asc")
                    ->orderBy("c.id_cama", "asc")
                    ->get();

	

		$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");

        foreach($camas_ocupadas as $cama_ocupada){
            $rut = $cama_ocupada->rut;
            $dv = ($cama_ocupada->dv == 10)?"K":$cama_ocupada->dv;

			$medicoTratante = Medico::nombreMedico($cama_ocupada->id_medico);

			//ultimo diagnostico
			$diagnostico = HistorialDiagnostico::ultimoDiagnostico($cama_ocupada->caso);

			$categorizacion_2 = DB::table('t_evolucion_casos as e')
			->select("e.fecha as fecha", "r.categoria as categoria")
			->select('r.categoria')
			->leftjoin('riesgos as r', 'r.id','=','e.riesgo_id')
			->orderby('e.fecha','desc')
			->where('e.caso','=',$cama_ocupada->caso)
			->whereIn('e.urgencia', [false, null])
			->first();

			$edad = '';
			if($cama_ocupada->fecha_nacimiento != null){
				$edad=Paciente::edad($cama_ocupada->fecha_nacimiento);
			}

			$atencion_paciente = '';
			$atencion = EvolucionAtencion::atencionesFecha($cama_ocupada->caso,$fecha_actual);
			if(!empty($atencion)){
				$atencion_paciente = $atencion->tipo_atencion;
			}
			
			$acompañamiento_paciente = '';
			if($edad <= 15 || $edad == ''){
				$acompañamiento =EvolucionAcompanamiento::acompanamientosFecha($cama_ocupada->caso,$fecha_actual);
				if(!empty($acompañamiento)){
					$acompañamiento_paciente = $acompañamiento->tipo_acompanamiento;
				}
			}  

            $response [] = [
                $cama_ocupada->pieza,
                $cama_ocupada->cama,
                $cama_ocupada->ficha,
                $rut."-".$dv,
                strtoupper($cama_ocupada->nombre)." ".strtoupper($cama_ocupada->paterno)." ".strtoupper($cama_ocupada->materno),
                "",
				$medicoTratante,
				$diagnostico,
				(isset($categorizacion_2->categoria))?$categorizacion_2->categoria:"",
				$atencion_paciente,
				$acompañamiento_paciente
            ];
        }

        return response()->json($response);
    }

	private function tiempoEstada($ingreso, $liberacion = null){
		if(is_null($ingreso)) return "";
		if(is_null($liberacion)) $f_liberacion = \Carbon\Carbon::now();
		else $f_liberacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $liberacion);
		$f_ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ingreso);
		/* @var $diff \Carbon\Carbon */
		return $f_liberacion->diffInSeconds($f_ingreso);
	}

	public function getCamasDisponiblesVerdes(Request $request,$unidad){
		$idCaso=$request->input("idCaso");
		$url = URL::to('/');
		$tiene=true;
		$camas=array();
		$nombres = array();
        $unidad_obj = Session::get("unidades")->KeyBy("alias")->get($unidad);
		$consulta = Consultas::ultimoEstadoCamas();
		$consulta = Consultas::addTiempoBloqueo($consulta);
		$consulta = Consultas::addTiempoReserva($consulta);
		$consulta = Consultas::addTiemposOcupaciones($consulta);
		$consulta = $consulta->addSelect("s.visible", "ue.visible");
		$consulta->addSelect(DB::raw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int AS xx"))
		->where("est.id", "=", $this->idEstablecimiento)
		->where("ue.url", "=", $unidad)
		->whereRaw("cm.id NOT IN (SELECT cama FROM historial_eliminacion_camas)")
		->whereNotNull("id_sala")
		->where("s.visible", true)
        ->where("ue.visible", true)
		// ->orderByRaw("NULLIF(regexp_replace(cm.id_cama, E'\\\\D', '', 'g'), '')::int asc")
		->orderBy("s.nombre", "asc");
		// ->orderBy("cm.id_cama", "asc");
		$ocupacionesCamaSala = $consulta->get();
		$i = 0;
		$ind = [];
		foreach($ocupacionesCamaSala as $ocupacion){
			$ind[$ocupacion->id_sala] = $i;
			$i++;
		}



        foreach($ocupacionesCamaSala as $ocupacion){

			$nombre_sala = empty($ocupacion->nombre_sala) ? "Sala sin nombre ({$ocupacion->id_sala})" : $ocupacion->nombre_sala;
			$nombres[$ind[$ocupacion->id_sala]] = $nombre_sala;

        	if ($ocupacion->fecha === null){

        		if($ocupacion->reservado !== null){
					$imagen = "cama_bloqueada.png";
				}
				elseif($ocupacion->bloqueado !== null){
					$imagen = "cama_bloqueada.png";
					$agr = "";
					$camas[$ind[$ocupacion->id_sala]][]=array("img" => "<a style='margin-left:5px;margin-right:5px;' class='cursor' onclick='nosepuede()'>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>{$agr} {$ocupacion->id_cama}</figcaption> </figure> </a>", "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente);

				}
                elseif ($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
                    $agr = "({$ocupacion->unidad_actual})";
                }
                else {
					$agr = "";
				$tiene=true;
				$imagen="SIN_PACIENTE.png";
				$camas[$ind[$ocupacion->id_sala]][]=array("img" => "<a style='margin-left:5px;margin-right:5px;' class='cursor' onclick='marcarCamaDisponible(event, \"{$ocupacion->id_cama_unq}\")'>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>{$agr} {$ocupacion->id_cama}</figcaption> </figure> </a>", "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente);
				}
			}



        	else{

				if($ocupacion->reservado !== null || $ocupacion->bloqueado !== null
                ){
					continue;
				}

        		if($ocupacion->ocupado !== null){

					$agr = "";
                    if ($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
                        $agr = "({$ocupacion->unidad_actual})";
                    }
					$nombre="";
					if($ocupacion->id_unidad_actual != $ocupacion->id_unidad && !is_null($ocupacion->id_unidad_actual) && !is_null($ocupacion->id_unidad)){
						$nombre=UnidadEnEstablecimiento::getNombre($ocupacion->id_unidad_actual);
					}


					//cama sin riesgo (gris)
						if($ocupacion->riesgo == null){
							$imagen = "SIN_CATEGORIZACION.png";
						}

						//cama ANARILLA
						elseif($ocupacion->riesgo == "B3" || $ocupacion->riesgo == "C1" || $ocupacion->riesgo == "C2"){
							$imagen = "RIESGO_B.png";
						}

						//Cama VERDE
						elseif($ocupacion->riesgo[0]=="D" || $ocupacion->riesgo == "C3"){
							$imagen = "RIESGO_D.png";
						}

						//Cama ROJA
						elseif($ocupacion->riesgo[0]=="A" ||$ocupacion->riesgo == "B1" || $ocupacion->riesgo == "B2"){
							$imagen = "RIESGO_A.png";
						}

						$horas=Consultas::formatTiempoOcupacion($ocupacion->fecha_ocupacion, $ocupacion->fecha_liberacion);
						$caption = (empty($ocupacion->riesgo)) ? $horas : $ocupacion->riesgo ." - ". $horas;
						$caption=$agr." ".$caption." - ".$ocupacion->id_cama;

						//demas usuarios no ven los riesgos que ponen los de urgencia
						if($ocupacion->id_usuario && Session::get('usuario')->tipo != 'usuario'){
							$tipo_usuario = Usuario::find($ocupacion->id_usuario);
							if($tipo_usuario->tipo == 'usuario'){
								$imagen = "SIN_CATEGORIZACION.png";
								$caption = $horas;
								$caption = $nombre.$caption." - ".$ocupacion->id_cama;
							}
						}

						//cama naranja
						if($ocupacion->fecha_ingreso_real == null){
							$imagen = "cama_reservada.png";
						}

					if($idCaso == null){/* $class */
						$opcion="<a onclick='nosepuede()' >  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$caption</figcaption> </figure> </a>";
					}
					else{
						/* $clas */
						$opcion="<a onclick='intercambiar({$ocupacion->id_caso}, {$idCaso})' s>  <figure> <img src='$url/img/$imagen' class='imgCama' />  <figcaption>$caption</figcaption> </figure> </a>";
					}

					$camas[$ind[$ocupacion->id_sala]][]=array("img" => $opcion, "sala" => $ocupacion->id_sala, "nombrePaciente"=>$ocupacion->nombrePaciente);

				}

        	}
        }

        $response=array("nombres" => $nombres, "salas" => $camas, "tiene"=>$tiene);

        return response()->json($response);
    }
    public function liberarInfeccion(Request $request){

    	$MiInfeccion = $request->input("MiInfeccion");
		$motivo = $request->input("motivo");
		$fechaEgreso = $request->input("fechaEgreso");
		try{
			$ingresarIn=Infecciones::find($MiInfeccion);
			$ingresarIn->motivo_termino=$motivo;
			$ingresarIn->fecha_termino=$fechaEgreso;
			$ingresarIn->save();

			$ActualizaInfeccion=IAAS::where("id_infeccion","=",$MiInfeccion)->get();
			foreach ($ActualizaInfeccion as $actualizar) {
				$actualizar->cierre='si';
				$actualizar->save();
		}


		}
		catch(Exception $e){
			return response()->json(["error" => "Error al cerrar la infección"]);
		}

		return response()->json(array("exito" => "La infección a sido cerrada"));
    }



    //con opcion de alta sin liberar cama
	public function liberarCama(Request $request){
		$caso_id = strip_tags($request->input("caso"));
		$motivo = strip_tags($request->input("motivo"));

		$detalle = strip_tags($request->input("inputProcedencia"));
		$detalleExtrasistema = strip_tags($request->input("inputProcedenciaExtra"));
		$ficha = strip_tags($request->input("ficha"));
		$medico_alta = strip_tags($request->input("id_medico"));
		$fallec = strip_tags($request->input("fechaFallecimiento"));

		$fechaEgreso_dato = strip_tags($request->input("fechaEgreso"));
		$input_alta = strip_tags($request->input("input-alta"));
		//datos recien nacido
		$dv_rn = strip_tags($request->dv_rn);
		$rut_rn = strip_tags($request->rut_rn);


		$infeccion = DB::table('casos as c')
						->select('i.id')
						->leftjoin('infecciones as i', 'i.caso','=','c.id')
						->where('c.id','=', $caso_id)
						->whereNull('i.fecha_termino')
						->get();

		$tiene_infeccion = $infeccion->first();
		try{
			$fecha_egreso = Carbon::parse($fechaEgreso_dato)->format("Y-m-d H:i:s");
		}catch(Exception $e){
			$fecha_egreso = Carbon::now()->format("Y-m-d H:i:s");
		}

		try{
			DB::beginTransaction();

			$caso = Caso::findOrFail($caso_id);

			$paciente = Paciente::where("id",$caso->paciente)->first();
			$paciente->rut = $rut_rn;
			$paciente->dv = ($dv_rn=="k" || $dv_rn=="K") ? 10 : $dv_rn;
			$paciente->save();

			$historial_ocupaciones = THistorialOcupaciones::where('t_historial_ocupaciones.caso',$caso_id)
			->leftJoin("camas_temporales","camas_temporales.id_historial_ocupaciones","=","t_historial_ocupaciones.id")
			->where(function($q){
				$q->whereNull('t_historial_ocupaciones.fecha_liberacion')
				->orWhere(function($qor){
					$qor->whereNotNull("camas_temporales.id")
					->where("camas_temporales.visible","=",true);
				});
			})
			->select("t_historial_ocupaciones.*")
			->first();
			
			$ct = new \App\Models\CamaTemporal();
			$ct->ocultarCaso($caso_id);

			if($motivo === 'alta sin liberar cama'){
				$historial_ocupaciones->id_usuario_alta_sin_liberar = Auth::user()->id;
				$historial_ocupaciones->motivo = 'alta';
			}else{
				$historial_ocupaciones->id_usuario_alta = Auth::user()->id;
				$historial_ocupaciones->fecha_liberacion = $fecha_egreso;
				$historial_ocupaciones->motivo =($motivo == 'Otro' || $motivo == 'derivacion otra institucion')? strtolower($motivo):$motivo;
			}
			$historial_ocupaciones->save();
			
			if($motivo === 'traslado extra sistema'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$caso->detalle_termino = $detalleExtrasistema;
			}elseif($motivo === 'derivación'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'traslado interno'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$detalle = "Traslado interno";
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'alta'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$detalle = "Alta a domicilio";
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'fallecimiento'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$detalle = "Fallecimiento";
				$caso->detalle_termino = $detalle;
				$paciente->fecha_fallecimiento = Carbon::parse($fallec)->format("Y-m-d H:i:s");
				$paciente->save();
			}elseif($motivo === 'Otro' || $motivo == 'derivacion otra institucion'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = strtolower($motivo);
				$detalle = $input_alta;
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'hospitalización domiciliaria'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$lista=new HospitalizacionDomiciliaria;
				$lista->caso=$caso->id;
				$lista->fecha=Carbon::now()->format("Y-m-d H:i:s");
				$lista->usuario = Session::get("usuario")->id;
				$lista->save();
				$detalle = "Hospitalización Domiciliaria";
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'alta sin liberar cama'){
				$fechaEgreso = "{$fecha_egreso}";
				//ahora si actualiza el ultimo caso del historial.
				$historial = HistorialOcupacion::where("caso", "=", $caso->id)->orderby("id", "desc")->first();
				$historial->fecha_alta = $fechaEgreso;
				$historial->save();
				$caso->motivo_termino = 'alta';
				$caso->detalle_termino = $motivo;
			}elseif($motivo === 'Fuga'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$detalle = "Fuga";
				$caso->detalle_termino = $detalle;
			}elseif($motivo === 'Liberación de responsabilidad'){
				$caso->fecha_termino = "{$fecha_egreso}";
				$caso->motivo_termino = $motivo;
				$detalle = "Liberación de responsabilidad";
				$caso->detalle_termino = $detalle;
			}
			$caso->id_medico_alta = $medico_alta;

			if(isset($tiene_infeccion->id) && $tiene_infeccion->id != null){
				foreach($infeccion as $infec){
					$ingresarIn=Infecciones::find($infec->id);
					// $ingresarIn->fecha_termino=Carbon::now()->format("Y-m-d H:i:s");
					$ingresarIn->fecha_termino=$fecha_egreso;
					$ingresarIn->motivo_termino="alta";
					$ingresarIn->save();
				}
			}

			$caso->ficha_clinica = $ficha;
			if(isset($request->parto)){
				$caso->parto = ($request->parto == 'no') ? false : true;
			}
			$caso->save();

			$lista = ListaTransito::where('caso', '=', $caso_id)->first();
			if($lista){
				// $lista->fecha_termino = date('Y-m-d H:i');
				$lista->fecha_termino = $fecha_egreso;
				$lista->save();
			}

			// ListaDerivados::cerrarListaDerivado($caso_id);

			$listaPabellon = ListaPabellon::where('id_caso', '=', $caso_id)->whereNull('fecha_salida')->first();
			if($listaPabellon){
				$listaPabellon->fecha_salida = $fecha_egreso;
				$listaPabellon->save();
			}


			DB::commit();
			return response()->json(array("exito" => "La cama ha sido liberada"));

		}catch(Exception $ex){
			DB::rollback();
			return response()->json(array("error" => "Error al liberar la cama: {$ex->getMessage()}", "msg" => $ex->getMessage()));
		}catch(Exception $e){
			return response()->json(["error" => "Error al liberar la cama."]);
		}


	}

	//liberar cama
	public function AltaSinLiberarCama(Request $request){
		$fecha_egreso = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
		$historial = THistorialOcupaciones::where("caso", $request->caso)
		->whereNull("fecha_liberacion")
		->firstOrFail();
		$caso = Caso::where("id", $request->caso)->first();

		try {

			if($request->cambiarMedico == "no"){
				$historial->fecha_liberacion = $fecha_egreso;
				$historial->id_usuario_alta = Auth::user()->id;
				$historial->save();
				$caso->fecha_termino = $fecha_egreso;
				$caso->id_medico_alta = $request->id_medicoDioAlta;
				$caso->save();
				return response()->json(["exito" => "La cama ha sido liberada exitosamente."]);
			}else{
				if($request->medicoAltaLC == null || $request->medicoAltaLC == null){
					return response()->json(["faltaMedico" => "Faltó ingresar el nombre del médico, intente nuevamente."]);
				}else{
					$historial->fecha_liberacion = $fecha_egreso;
					$historial->id_usuario_alta = Auth::user()->id;
					$historial->save();
					$caso->fecha_termino = $fecha_egreso;
					$caso->id_medico_alta = (int)$request->id_medico;
					$caso->save();
					return response()->json(["exito" => "La cama ha sido liberada exitosamente."]);
				}
			}
		} catch (Exception $ex) {
			return response()->json(["error" => "Error al liberar la cama."]);
		}
	}

	public function test(){
		//metodo que uso para hacer pruebas :B
		$pabellon = ListaPabellon::get();
		return $pabellon;
	}

	public function darAlta(Request $request){
		try{


			$caso = $request->input("caso");
			$fechaEgreso = \Carbon\Carbon::createFromFormat("d-m-Y H:i:s", $request->input("fechaEgreso"));
			$historial = HistorialOcupacion::where("caso","=",$caso)->first();
			$historial->fecha_alta = $fechaEgreso;
			$historial->save();

		}catch(\Exception $e){
			return response()->json(["error" => "Error al liberar la cama.".$e]);
		}

		return response()->json(array("exito"=>"Paciente dado de alta"));
	}

	public function liberarReserva(Request $request){
		$idEstablecimiento=Session::get("idEstablecimiento");

		$idCaso=$request->input("caso");
		$id=Consultas::getIdReservaPorCaso($idCaso);
		$reserva=Reserva::find($id);
		$reserva->tiempo="0 hours";
		$reserva->save();
	}

	public function acostarPaciente(Request $request){
		/* Log::info($request);
		return 1; */
		try{
			$idCaso = $request->input("idCasoVisitas");
			$tiempo = Carbon::now()->format("Y-m-d H:i:s");
			$id_usuario = Auth::user()->id;
			//Validar que el paciente no ente hospitalizado, egresado o en otra cama
			//validaciones
			$respuesta = Consultas::validacionHospitalizacion($idCaso);
			if($respuesta != ""){
				return response()->json(array("error" => $respuesta));
			}

			DB::beginTransaction();

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

			$hOcupacion = THistorialOcupaciones::where('caso', '=', $idCaso)
			->whereNull('fecha_liberacion')
			->first();

			if(!is_null($hOcupacion)){
				$hOcupacion->fecha_ingreso_real = Carbon::now()->format('Y-m-d H:i:s');
				$hOcupacion->id_usuario_ingresa = Session::get('usuario')->id;
				$hOcupacion->save();
			}
			$transito = ListaTransito::where('caso', '=', $idCaso)
			->whereNull('fecha_termino')
			->first();

			if(!is_null($transito)){
				$transito->fecha_termino = Carbon::now()->format('Y-m-d H:i:s');
				$transito->save();
			}
			DB::commit();
			return response()->json(array("exito"=>"Paciente dado de alta"));

		}catch(\Exception $e){
			Log::info($e);
			DB::rollback();
			return response()->json(["error" => "Error al acostar paciente.".$e]);
		}


	}

	public function regresarTransito(Request $request){
		try{

			DB::beginTransaction();
			$idCaso = $request->input("idCaso");
			$hOcupacion = THistorialOcupaciones::where('caso', $idCaso)
				->whereNull('fecha_liberacion')
				->first();
			$hOcupacion->fecha_ingreso_real = NULL;
			$hOcupacion->id_usuario_ingresa = NULL;
			$hOcupacion->save();

			$transito = ListaTransito::where('caso', '=', $idCaso)
				->whereNotNull('fecha_termino')
				->orderBy('id_lista_transito', 'desc')
				->first();
			$transito->fecha_termino = NULL;
			$transito->id_usuario_salida_urgencia = Auth::user()->id;
			$transito->save();
			//return response()->json($transito);
			DB::commit();
			return response()->json(array("exito"=>"Paciente regresado a lista de transito"));
			
		}catch(\Exception $e){
			Log::info($e);
			DB::rollBack();
			return response()->json(["error" => "Error al regresar a lista de transito".$e]);
		}

		
	}

	public function paciente($unidad){
		return View::make("Gestion/Paciente")->with("unidad", $unidad)->with("riesgo", Riesgo::getRiesgos())->with("prevision", Prevision::getPrevisiones());
	}

	public function ingresarinfeccion(Request $request){
		//Datos Paciente
		$diabetes      = false;
	    $hipertension  = false;
	    $enfermedad    = false;
	    $MorbidoOtros  = false;
	    $MiInfeccion=0;

		$morbidos = $request->input("morbidos");
		$reingreso=$request->input("reingreso");
		$sala=$request->input("sala");
		$numero_ficha=$request->input("numero_ficha");
		$peso_naci=$request->input("peso_naci");
		$numero_reingreso=$request->input("numero_reingreso");
		$aislamiento=$request->input("aislamiento");
		$fallecimiento = $request->input("fallecimiento");
		$muerte =$request->input("muerte");
		$fechaMuerte = $request->input("fechaMuerte");
		$categoria = $request->input("categoria");

		if(empty($aislamiento)){
			return response()->json(array("error" => "falta seleccionar tipo de aislamiento en la sección de Datos Paciente"));
		}

		if(count($aislamiento) != 0){
				$aislamiento = "{".implode(",", $aislamiento)."}";
			}

		$aislamiento=trim($aislamiento,"{}");


		if(empty($morbidos)){
			return response()->json(array("error" => "falta elegir opciones en Antecedentes Morbidos en la sección de Datos Paciente"));
		}
		for($i=0; $i<count($morbidos); $i++){
	        if($morbidos[$i]=='diabetes')      $diabetes 	 = true;
	        if($morbidos[$i]=='hipertension')  $hipertension = true;
	        if($morbidos[$i]=='enfermedad')    $enfermedad   = true;
	        if($morbidos[$i]=='MorbidoOtros'){
				$MorbidoOtros = $request->input("Otro");
				if($MorbidoOtros == null){
					return response()->json(array("error" => "falta ingresar motivo en Antecedentes Morbidos en la sección de Datos Paciente"));
				}
	        }
      	}

	    //tabla infecciones
		$caso = $request->input("caso2");
		$fecha=date("Y-m-d H:i:s");
		$cirujano = $request->input("cirujano");
		$electiva = $request->input("electiva");
		$urgencia = $request->input("urgencia");
		$cesaria = $request->input("cesaria");
		$reintervencion = $request->input("reintervencion");
		$tipo_herida = $request->input("tipo_herida");
		$VM = $request->input("VM");
		$porta_cath = $request->input("porta_cath");
		$umbilical = $request->input("umbilical");
		$central_periferico = $request->input("central_periferico");
		$periferico = $request->input("periferico");
		$npt = $request->input("npt");
		$CUP = $request->input("CUP");
		$shunt = $request->input("shunt");
		$swan = $request->input("swan");
		$chcd = $request->input("chcd");
		$CHLD = $request->input("CHLD");
		$arterial = $request->input("arterial");
		$criterio_iaas = $request->input("criterio_iaas");
		$cambio_atb = $request->input("cambio_atb");


		$infeccion=DB::table( DB::raw("(select i.id from casos as c,infecciones as i where c.id=i.caso and c.id=$caso and i.caso=$caso) as re"
         			))->get();

		foreach($infeccion as $infec)
		{

			$MiInfeccion=$infec->id;
		}

		//tabla iaas
		$localizacion=$request->input("localizacion");
		$fechaIngreso = $request->input("fechaIngreso");

		$fechaInicio = $request->input("fechaInicio");

		$servicioIAAS = $request->input("servicioIAAS");
		$procedimiento=$request->input("procedimiento");
		$agente1=$request->input("agente1");

		$sensibilidad1 = $request->input("sensibilidad1");
		$intermedia1 = $request->input("intermedia1");
		$resistencia1 = $request->input("resistencia1");

		$sensibilidad2 = $request->input("sensibilidad2");
		$intermedia2 = $request->input("intermedia2");
		$resistencia2 = $request->input("resistencia2");

		$sensibilidad3 = $request->input("sensibilidad3");
		$intermedia3 = $request->input("intermedia3");
		$resistencia3 = $request->input("resistencia3");

		$sensibilidad4 = $request->input("sensibilidad4");
		$intermedia4 = $request->input("intermedia4");
		$resistencia4 = $request->input("resistencia4");

		$sensibilidad5 = $request->input("sensibilidad5");
		$intermedia5 = $request->input("intermedia5");
		$resistencia5 = $request->input("resistencia5");

		$sensibilidad6 = $request->input("sensibilidad6");
		$intermedia6 = $request->input("intermedia6");
		$resistencia6 = $request->input("resistencia6");

		$agente2=$request->input("agente2");

		$sensibilidad7 = $request->input("sensibilidad7");
		$intermedia7 = $request->input("intermedia7");
		$resistencia7 = $request->input("resistencia7");

		$sensibilidad8 = $request->input("sensibilidad8");
		$intermedia8 = $request->input("intermedia8");
		$resistencia8 = $request->input("resistencia8");

		$sensibilidad9 = $request->input("sensibilidad9");
		$intermedia9 = $request->input("intermedia9");
		$resistencia9 = $request->input("resistencia9");

		$sensibilidad10 = $request->input("sensibilidad10");
		$intermedia10 = $request->input("intermedia10");
		$resistencia10 = $request->input("resistencia10");

		$sensibilidad11 = $request->input("sensibilidad11");
		$intermedia11 = $request->input("intermedia11");
		$resistencia11 = $request->input("resistencia11");

		$sensibilidad12 = $request->input("sensibilidad12");
		$intermedia12 = $request->input("intermedia12");
		$resistencia12 = $request->input("resistencia12");
		//tabla cvc
		$CVC=$request->input("cvc");

		   /**************** INGRESOS A TABLAS*****************/

		   $vacio=0;
		   $vacio2=0;
		   foreach ($fechaIngreso as $key => $fingreso) {
				if($fingreso == null){
					$vacio++;
				}
				if($key == 0){
				   if($fingreso == null){
					   return response()->json(array("error" => "Falta ingresar Fecha de notificación de IAAS"));
				   }
			    }
			}

		   foreach ($fechaInicio as $key => $finicio) {
			if($finicio == null){
				   $vacio2++;
			}
			if($key == 0){
				if($finicio == null){
					return response()->json(array("error" => "Falta ingresar Fecha Inicio IAAS"));
				}
			}
		   }


   	if($vacio!=2 && $vacio2!=2){
	//ingresar infecciones
   		if($MiInfeccion!=0)
   		{
   			$ingresarIn=Infecciones::find($MiInfeccion);
   			$ingresarIn->fecha_termino=null;
			$ingresarIn->motivo_termino=null;

			$paciente_infeccion=DB::table( DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
         			))->get();

			foreach($paciente_infeccion as $paciente_infectado){

					$MiPaciente=$paciente_infectado->id;
			}

   			$pacienteInfeccion=PacientesInfeccion::find($MiPaciente);
   		}
   		else
   		{
   			$ingresarIn=new Infecciones();
   			$pacienteInfeccion=new PacientesInfeccion();
   		}

		$ingresarIn->fecha=$fecha;
		$ingresarIn->caso=$caso;
		$ingresarIn->criterio_iaas=$criterio_iaas;
		$ingresarIn->cambio_atb=$cambio_atb;
		$ingresarIn->cirujano=$cirujano;
		$ingresarIn->electiva=$electiva;
		$ingresarIn->urgencia=$urgencia;
		$ingresarIn->cesaria=$cesaria;
		$ingresarIn->reintervencion=$reintervencion;
		$ingresarIn->tipo_herida=$tipo_herida;
		$ingresarIn->vm=$VM;
		$ingresarIn->swan=$swan;
		$ingresarIn->chcd=$chcd;
		$ingresarIn->chld=$CHLD;
		$ingresarIn->arterial=$arterial;
		$ingresarIn->porta_cath=$porta_cath;
		$ingresarIn->umbilical=$umbilical;
		$ingresarIn->central_periferico=$central_periferico;
		$ingresarIn->periferico=$periferico;
		$ingresarIn->npt=$npt;
		$ingresarIn->cup=$CUP;
		$ingresarIn->shunt_vp=$shunt;
		$ingresarIn->fallecimiento=$fallecimiento;
		$ingresarIn->motivo_fallecimiento=$muerte;
		$ingresarIn->save();

		$idInfeccion=$ingresarIn->id;



      	$pacienteInfeccion->id_infeccion=$idInfeccion;
      	$pacienteInfeccion->reingreso=$reingreso;
      	$pacienteInfeccion->peso_nacimiento=$peso_naci;
      	$pacienteInfeccion->servicio_ingreso=$sala;
      	$pacienteInfeccion->numero_ficha=$numero_ficha;
      	$pacienteInfeccion->dias_reingreso=$numero_reingreso;
      	$pacienteInfeccion->aislamiento=$aislamiento;
      	$pacienteInfeccion->diabetes=$diabetes;
      	$pacienteInfeccion->hipertension=$hipertension;
      	$pacienteInfeccion->otro=$MorbidoOtros;
      	$pacienteInfeccion->enfermedad_autoinmune=$enfermedad;
   		$pacienteInfeccion->fallecimiento=$fallecimiento;
      	$pacienteInfeccion->motivo_fallecimiento=$muerte;
      	if($fechaMuerte!=null)$pacienteInfeccion->fecha_fallecimiento=$fechaMuerte;
		$pacienteInfeccion->categoria=$categoria;
      	$pacienteInfeccion->save();


		for($i=0; $i<count($CVC); $i++){
			$venosa=new CVC();
			$venosa->id_infeccion=$idInfeccion;
			$venosa->cvc_dias=$CVC[$i];
			if($CVC[$i]!=null)$venosa->save();
		}


		for($i=0; $i<count($localizacion); $i++){
		$localInfeccion=new IAAS();
		$localInfeccion->id_infeccion=$idInfeccion;

		if($localizacion[$i]=="Otro"){$localizacion[$i]=$request->input("OtroLocal");}

		$localInfeccion->localizacion=$localizacion[$i];
		$localInfeccion->agente1=$agente1[$i];
		$localInfeccion->fecha_inicio=$fechaIngreso[$i];
		$localInfeccion->fecha_iaas=$fechaInicio[$i];
		$localInfeccion->servicioiaas=$servicioIAAS[$i];


		if($procedimiento[$i]=="Otro"){$procedimiento[$i]=$request->input("OtroProcedu");}
		$localInfeccion->procedimiento_invasivo=$procedimiento[$i];

		if($fechaIngreso[$i]!=null){
		$localInfeccion->sensibilidad1=$sensibilidad1[$i];
		$localInfeccion->intermedia1=$intermedia1[$i];
		$localInfeccion->resistencia1=$resistencia1[$i];

		$localInfeccion->sensibilidad2=$sensibilidad2[$i];
		$localInfeccion->intermedia2=$intermedia2[$i];
		$localInfeccion->resistencia2=$resistencia2[$i];

		$localInfeccion->sensibilidad3=$sensibilidad3[$i];
		$localInfeccion->intermedia3=$intermedia3[$i];
		$localInfeccion->resistencia3=$resistencia3[$i];

		$localInfeccion->sensibilidad4=$sensibilidad4[$i];
		$localInfeccion->intermedia4=$intermedia4[$i];
		$localInfeccion->resistencia4=$resistencia4[$i];

		$localInfeccion->sensibilidad5=$sensibilidad5[$i];
		$localInfeccion->intermedia5=$intermedia5[$i];
		$localInfeccion->resistencia5=$resistencia5[$i];

		$localInfeccion->sensibilidad6=$sensibilidad6[$i];
		$localInfeccion->intermedia6=$intermedia6[$i];
		$localInfeccion->resistencia6=$resistencia6[$i];

		$localInfeccion->agente2=$agente2[$i];

		$localInfeccion->sensibilidad7=$sensibilidad7[$i];
		$localInfeccion->intermedia7=$intermedia7[$i];
		$localInfeccion->resistencia7=$resistencia7[$i];

		$localInfeccion->sensibilidad8=$sensibilidad8[$i];
		$localInfeccion->intermedia8=$intermedia8[$i];
		$localInfeccion->resistencia8=$resistencia8[$i];

		$localInfeccion->sensibilidad9=$sensibilidad9[$i];
		$localInfeccion->intermedia9=$intermedia9[$i];
		$localInfeccion->resistencia9=$resistencia9[$i];

		$localInfeccion->sensibilidad10=$sensibilidad10[$i];
		$localInfeccion->intermedia10=$intermedia10[$i];
		$localInfeccion->resistencia10=$resistencia10[$i];

		$localInfeccion->sensibilidad11=$sensibilidad11[$i];
		$localInfeccion->intermedia11=$intermedia11[$i];
		$localInfeccion->resistencia11=$resistencia11[$i];

		$localInfeccion->sensibilidad12=$sensibilidad12[$i];
		$localInfeccion->intermedia12=$intermedia12[$i];
		$localInfeccion->resistencia12=$resistencia12[$i];

		$localInfeccion->save();
	}

		}

		//$mensaje= "Se ha ingresado la infeccion";
		DB::commit();
		return response()->json(array("exito" =>"Se ha ingresado la infeccion"));
	} // fin if
	else{
		$mensaje= "Falta ingresar Fechas en la nueva localización agregada";
		DB::rollback();
		return response()->json(array("error" =>$mensaje));
		}

	}

	public function Veringresarinfeccion(Request $request){
		$updated_at=date("Y-m-d H:i:s");
		$MiInfeccion= $request->input("MiInfeccion");
		$MiPaciente= $request->input("MiPaciente");
		$aislamiento=$request->input("aislamiento");

		if($aislamiento == null){
		}else{
			$aislamiento = "{".implode(",", $aislamiento)."}";
			$aislamiento=trim($aislamiento,"{}");
		}

		$VM = $request->input("VM");
		$porta_cath = $request->input("porta_cath");
		$umbilical = $request->input("umbilical");
		$central_periferico = $request->input("central_periferico");
		$periferico = $request->input("periferico");
		$npt = $request->input("npt");
		$CUP = $request->input("CUP");
		$shunt = $request->input("shunt");
		$swan = $request->input("swan");
		$chcd = $request->input("chcd");
		$CHLD = $request->input("CHLD");
		$arterial = $request->input("arterial");
		$fallecimiento = $request->input("fallecimiento");
		$muerte =$request->input("muerte");
		$fechaMuerte = $request->input("fechaMuerte");

		//tabla iaas
		$localizacion=$request->input("localizacion");
		$fechaIngreso = $request->input("fechaIngreso");
		$fechaInicio = $request->input("fechaInicio");
		$servicioIAAS = $request->input("servicioIAAS");
		$procedimiento=$request->input("procedimiento");
		$agente1=$request->input("agente1");

		$sensibilidad1 = $request->input("sensibilidad1");
		$intermedia1 = $request->input("intermedia1");
		$resistencia1 = $request->input("resistencia1");

		$sensibilidad2 = $request->input("sensibilidad2");
		$intermedia2 = $request->input("intermedia2");
		$resistencia2 = $request->input("resistencia2");

		$sensibilidad3 = $request->input("sensibilidad3");
		$intermedia3 = $request->input("intermedia3");
		$resistencia3 = $request->input("resistencia3");

		$sensibilidad4 = $request->input("sensibilidad4");
		$intermedia4 = $request->input("intermedia4");
		$resistencia4 = $request->input("resistencia4");

		$sensibilidad5 = $request->input("sensibilidad5");
		$intermedia5 = $request->input("intermedia5");
		$resistencia5 = $request->input("resistencia5");

		$sensibilidad6 = $request->input("sensibilidad6");
		$intermedia6 = $request->input("intermedia6");
		$resistencia6 = $request->input("resistencia6");

		$agente2=$request->input("agente2");

		$sensibilidad7 = $request->input("sensibilidad7");
		$intermedia7 = $request->input("intermedia7");
		$resistencia7 = $request->input("resistencia7");

		$sensibilidad8 = $request->input("sensibilidad8");
		$intermedia8 = $request->input("intermedia8");
		$resistencia8 = $request->input("resistencia8");

		$sensibilidad9 = $request->input("sensibilidad9");
		$intermedia9 = $request->input("intermedia9");
		$resistencia9 = $request->input("resistencia9");

		$sensibilidad10 = $request->input("sensibilidad10");
		$intermedia10 = $request->input("intermedia10");
		$resistencia10 = $request->input("resistencia10");

		$sensibilidad11 = $request->input("sensibilidad11");
		$intermedia11 = $request->input("intermedia11");
		$resistencia11 = $request->input("resistencia11");

		$sensibilidad12 = $request->input("sensibilidad12");
		$intermedia12 = $request->input("intermedia12");
		$resistencia12 = $request->input("resistencia12");

		$CVC2=$request->input("cvc");


		$ingresarIn=Infecciones::find($MiInfeccion);
		$ingresarIn->updated_at=$updated_at;
		$ingresarIn->fallecimiento=$fallecimiento;
		$ingresarIn->motivo_fallecimiento=$muerte;
		$ingresarIn->save();

		$ingresarPac=PacientesInfeccion::find($MiPaciente);
    	if($fechaMuerte!=null)$ingresarPac->fecha_fallecimiento=$fechaMuerte;
		$ingresarPac->fallecimiento=$fallecimiento;
		$ingresarPac->motivo_fallecimiento=$muerte;
		if($aislamiento!=null)$ingresarPac->aislamiento=$aislamiento;
		$ingresarPac->save();

		$cerrar=$request->input("cerrar");
		$ActualizaInfeccion=IAAS::where("id_infeccion","=",$MiInfeccion)->where("cierre","=",'no')->get();
		$j=0;
		foreach ($ActualizaInfeccion as $actualizar) {
			$actualizar->cierre=trim($cerrar[$j]);
			$j=$j+1;
			$actualizar->save();
		}

		$cont = count($fechaIngreso);
		$llaves = array_keys($fechaIngreso);
		$ultima = $llaves[$cont-1];
		$contFechasIngreso = count($fechaIngreso)-1;
		if($contFechasIngreso > 0){
			foreach ($fechaIngreso as $key => $fingreso) {
				if($key == $ultima){
				}else{
					if($fingreso == null){
					$key = $key+1;
					return response()->json(array("error" => "Falta ingresar Fecha de notificación de IAAS en Localización ".$key));
					break;
					}
				}
			}
		}

		$cont2 = count($fechaInicio);
		$llaves2 = array_keys($fechaInicio);
		$ultima2 = $llaves2[$cont2-1];
		$contFechasInicio = count($fechaInicio)-1;
		if($contFechasInicio > 0){
			foreach ($fechaInicio as $key => $finicio) {
				if($key == $ultima2){
				}else{
					if($finicio == null){
					$key = $key+1;
					return response()->json(array("error" => "Falta ingresar Fecha Inicio IAAS en Localización ".$key));
					break;
					}
				}
			}
		}

		for($i=0; $i<count($localizacion); $i++){
		$localInfeccion=new IAAS();
		$localInfeccion->id_infeccion=$MiInfeccion;
		$localInfeccion->localizacion=$localizacion[$i];
		$localInfeccion->agente1=$agente1[$i];
		$localInfeccion->procedimiento_invasivo=$procedimiento[$i];
		$localInfeccion->fecha_inicio=$fechaIngreso[$i];
		$localInfeccion->fecha_iaas=$fechaInicio[$i];
		$localInfeccion->servicioiaas=$servicioIAAS[$i];

		if($fechaIngreso[$i]!=null){
		$localInfeccion->sensibilidad1=$sensibilidad1[$i];
		$localInfeccion->intermedia1=$intermedia1[$i];
		$localInfeccion->resistencia1=$resistencia1[$i];

		$localInfeccion->sensibilidad2=$sensibilidad2[$i];
		$localInfeccion->intermedia2=$intermedia2[$i];
		$localInfeccion->resistencia2=$resistencia2[$i];

		$localInfeccion->sensibilidad3=$sensibilidad3[$i];
		$localInfeccion->intermedia3=$intermedia3[$i];
		$localInfeccion->resistencia3=$resistencia3[$i];

		$localInfeccion->sensibilidad4=$sensibilidad4[$i];
		$localInfeccion->intermedia4=$intermedia4[$i];
		$localInfeccion->resistencia4=$resistencia4[$i];

		$localInfeccion->sensibilidad5=$sensibilidad5[$i];
		$localInfeccion->intermedia5=$intermedia5[$i];
		$localInfeccion->resistencia5=$resistencia5[$i];

		$localInfeccion->sensibilidad6=$sensibilidad6[$i];
		$localInfeccion->intermedia6=$intermedia6[$i];
		$localInfeccion->resistencia6=$resistencia6[$i];

		$localInfeccion->agente2=$agente2[$i];

		$localInfeccion->sensibilidad7=$sensibilidad7[$i];
		$localInfeccion->intermedia7=$intermedia7[$i];
		$localInfeccion->resistencia7=$resistencia7[$i];

		$localInfeccion->sensibilidad8=$sensibilidad8[$i];
		$localInfeccion->intermedia8=$intermedia8[$i];
		$localInfeccion->resistencia8=$resistencia8[$i];

		$localInfeccion->sensibilidad9=$sensibilidad9[$i];
		$localInfeccion->intermedia9=$intermedia9[$i];
		$localInfeccion->resistencia9=$resistencia9[$i];

		$localInfeccion->sensibilidad10=$sensibilidad10[$i];
		$localInfeccion->intermedia10=$intermedia10[$i];
		$localInfeccion->resistencia10=$resistencia10[$i];

		$localInfeccion->sensibilidad11=$sensibilidad11[$i];
		$localInfeccion->intermedia11=$intermedia11[$i];
		$localInfeccion->resistencia11=$resistencia11[$i];

		$localInfeccion->sensibilidad12=$sensibilidad12[$i];
		$localInfeccion->intermedia12=$intermedia12[$i];
		$localInfeccion->resistencia12=$resistencia12[$i];

		$localInfeccion->save();
	}

		}

		$mensaje= "Se ha actualizado la infeccion del paciente";
		DB::commit();
		return response()->json(array("exito" =>$mensaje));

	}


	public function asignarCama(Request $request){
		$tipo_telefono = $request->input("tipo_telefono");
		$telefono = $request->input("telefono");
		$especialidades = $request->input("especialidades");
		$casoHospDom = strip_tags(trim($request->casoHospDom));
		$tipo= strip_tags($request->input("tipo"));
		$idCama= strip_tags($request->input("cama"));
		$rut= strip_tags(trim($request->input("rut")));
		$sexo= strip_tags($request->input("sexo"));
		$procedencia = strip_tags($request->input("tipo-procedencia"));
		$motivo_hosp = strip_tags($request->input("motivo_hosp"));	

		if($procedencia == 2 ){
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento);
		}
		else if($procedencia == 7){
			$detalle_procedencia = strip_tags($request->input_procedencia_establecimiento_privado);
		}else if( $procedencia == 4 || $procedencia == 3){
			$detalle_procedencia = strip_tags($request->input_procedencia);
		}
		/* arrays que deben ser verificados luego */
		$diagnostico = $request->input("diagnostico");
		$diagnostico_cie10 = empty($request->input("diagnostico_cie10"))?null:$request->input("diagnostico_cie10");

		$diagnosticos = $request->input("diagnosticos");
		$hidden_diagnosticos = $request->input("hidden_diagnosticos");

		$nombre = strip_tags($request->input("nombre"));
		$apellido_paterno = strip_tags($request->input("apellidoP"));
		$apellido_materno = strip_tags($request->input("apellidoM"));
		$dv = strip_tags($request->input("dv"));
        $caso_social = strip_tags($request->input("caso_social"));
        $extranjero = strip_tags($request->input("extranjero"));
		$prevision = strip_tags($request->input('prevision'));
		$servicio = $request->input("servicios2") ? strip_tags($request->input("servicios2")): null;
		$nombre_social = strip_tags($request->input("nombreSocial"));

		$fecha_solicitud = strip_tags($request->input("fechaIngreso"));
		$dau = strip_tags($request->input("dau"));
		$ficha_clinica = strip_tags($request->input("fichaClinica"));
		$rango = strip_tags($request->input("rango"));
		$tipo_caso_social = strip_tags($request->input("t_caso_social"));
		// $telefono = strip_tags($request->input("telefono"));
		$proc_geo = strip_tags($request->input("procedencia-geo"));
		$medico = strip_tags($request->input("id_medico"));
		$unidad = strip_tags($request->input("unidad"));
		$comentario_riesgo = strip_tags($request->input("comentario-riesgo"));
		$id_unidad = strip_tags($request->input("id_unidad"));
		$rut_madre = strip_tags($request->input("rutMadre"));
		$dv_madre = strip_tags($request->input("dvMadre"));
		$rn = strip_tags($request->input("rn"));
		$especialidad = strip_tags($request->input("especialidad"));

		$cama_disponible = DB::table('t_historial_ocupaciones as th')
						->select('fecha_liberacion')
						->where('cama', '=', $idCama)
						->whereNull('fecha_liberacion')
						->orderBy('fecha', 'desc')
						->first();

		if($cama_disponible){
			return response()->json(["error"=>"Error, la cama ha sido ocupada"]);
		}

		if($medico == ''){
			$medico = null;
		}

		$cantidad =0;
		if($rut){
			$respuesta = DB::table('pacientes')
				->select('t_historial_ocupaciones.motivo')
				->leftJoin('casos','casos.paciente','=','pacientes.id')
				->leftJoin('t_historial_ocupaciones','t_historial_ocupaciones.caso','=','casos.id')
				->leftJoin('camas','t_historial_ocupaciones.cama','=','camas.id')
				->where('pacientes.rut','=' ,$rut)
				->whereNotNull('camas.id')
				->whereNull('t_historial_ocupaciones.motivo')
				->groupBy('t_historial_ocupaciones.motivo')
				->get();

			$cantidad = $respuesta->count();
		}



		if ($cantidad > 0) {
			return response()->json(array("error" => "El paciente se encuentra actualmente en una cama"));
		}else{

            //ingresar paciente
			if($rut == null){
				$paciente=null;

				$rn= strip_tags($request->input("rn"));
				if($rn=='si'){
					$rutMadre= strip_tags($request->input("rutMadre"));
					$dvMadre= strip_tags($request->input("dvMadre"));
					$Madre="Rn/".$rutMadre."-".$dvMadre;

					$nacido=DB::table( DB::raw(
						"(SELECT p.id from pacientes as p where p.nombre='$nombre' and p.apellido_paterno='$apellido_paterno' and p.apellido_materno='$apellido_materno' and p.rn='$Madre') as res"
					))->get()->count();

					if($nacido!=0){
						return response()->json(["error" => "El Rn posee un caso abierto"]);
					}
				}
			}else{
				$paciente = Paciente::where("rut", "=", $rut)->first();
			}

			if($paciente != null){
				$idPaciente = $paciente->id;
				$caso = Caso::where("paciente", "=", $idPaciente)->whereNull("fecha_termino")->first();
				//Lista de espera
				$lista=DB::table( DB::raw("(SELECT l.id as lis,c.id as cas FROM casos as c,lista_espera as l where c.id=l.caso and c.paciente=$idPaciente and l.fecha_termino is null) as rea"))->get();

				if(!is_null($lista)){
					foreach ($lista as $lis){
						$caso2 = Caso::find($lis->cas);
						$caso2->fecha_termino = date("Y-m-d H:i:s");
						$caso2->motivo_termino = "alta";
						$caso2->save();

						$listas=ListaEspera::find($lis->lis);
						$listas->fecha_termino=date("Y-m-d H:i:s");
						$listas->motivo_salida="hospitalización";
						$listas->save();
					}
				}

				if($caso != null){
					$case = $caso->id;
					$cosa=$caso->establecimiento;
					$deriva = Derivacion::where("caso", "=", $case)->whereNull("fecha_cierre")->first();

					if($deriva != null){
						$derivar=$deriva->id;
						$derivacio=Derivacion::find($derivar);
						$derivacio->motivo_cierre="cancelado";
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
							if($lista == null)
								return response()->json(["error" => "El paciente tiene un caso abierto"]);
						}
					}
				}
			}

			if($caso_social === 'si'){
				$caso_social = true;
			}else{
				$caso_social = false;
			}

			if($extranjero === 'si'){
				$extranjero = true;
				$n_identificacion = ($request->n_pasaporte) ? strip_tags($request->n_pasaporte) : null;
				$identificacion = 'pasaporte';
			}else{
				$extranjero = false;
				$identificacion = 'run';
				$n_identificacion = null;
			}

			$tiene_caso_activo = false;
			$tiene_caso_activo_en_fecha = false;

			$fecha_ingreso = Carbon::now();

			DB::beginTransaction();
			try{
				/* @var $caso Caso */
				try{
					if($rut === null || $rut === ''){
						throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
					}
					$espera = ListaEspera::whereRaw("fecha= '$fecha_ingreso'")
								->whereNull("fecha_termino")
								->orderBy("fecha", "desc")
								->whereHas("casos", function($q) use ($rut){
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
					$caso->fecha_ingreso2 = $fecha_solicitud;
					$caso->motivo_hospitalizacion = $motivo_hosp;
					$caso->ficha_clinica = $ficha_clinica;
					$caso->dau = $dau;
					$caso->tipo_caso_social = $tipo_caso_social;
					$caso->id_complejidad_area_funcional = $servicio;
					$caso->caso_social = $caso_social;
					$caso->procedencia_geografica = $proc_geo;
					$caso->id_usuario = Session::get('usuario')->id;
					$caso->id_medico = $medico;
					$caso->id_unidad = $id_unidad;
				}

				
				/* @var $pac Paciente */
				if($rut === '' ){
					$pac = new Paciente();
					$rn= strip_tags($request->input("rn"));
					if($rn=='si')
					{
						$rutMadre= strip_tags($request->input("rutMadre"));
						$dvMadre= strip_tags($request->input("dvMadre"));

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
									"error" => "",
									"msg" => "El paciente se encuentra en este establecimiento. Debe hacer un traslado interno"]);
							} elseif ($e !== null && $e->id !== Session::get("idEstablecimiento")) {
								$c->cerrar("traslado externo", "{$fecha_ingreso}");
								$c->fecha_termino = "{$fecha_ingreso}";
								$c->detalle_termino = Session::get("nombreEstablecimiento");
								$c->motivo_termino = "traslado externo";
								$c->save();

								$d = $c->derivacionesActivas()
									->whereHas("unidadDestino", function ($q) {
										$q->where("establecimiento", Session::get("idEstablecimiento"));
									})->orderBy("fecha", "desc")
									->where("fecha", "<=", "{$fecha_ingreso}")
									->firstOrFail();
								$d->cerrar("aceptado", "Aceptado automatico al ingresar", $fecha_ingreso);
								$d->save();

								$caso->procedencia = 3;
								$caso->detalle_procedencia = "Traslado externo automático";
							}
						}
						else{
							$caso->procedencia = $procedencia;
							if(isset($detalle_procedencia) != null){
								$caso->detalle_procedencia = $detalle_procedencia;
							}
						}
					}
					$pac->dv = $dv;
				}
				/* Hay dos casos: el caso activo en la fecha y el caso no existente. */
				$pac->nombre = $nombre;
				$pac->apellido_paterno = $apellido_paterno;
				$pac->apellido_materno = $apellido_materno;

				$pac->nombre_social = $nombre_social;
				// $pac->telefono = $telefono;

				$pac->sexo=$sexo;
				if($request->fechaNac == ""){
					$pac->fecha_nacimiento=null;
				}else{
					$pac->fecha_nacimiento=date("Y-m-d", strip_tags(strtotime(trim($request->input("fechaNac")))));
				}
				$pac->extranjero = $extranjero;
				$pac->n_identificacion = $n_identificacion;
				$pac->identificacion = $identificacion;

				$pac->calle= strip_tags(trim($request->input("calle")));
				if(trim($request->input("numeroCalle")) == ""){
					$pac->numero=null;
				}else{
					$pac->numero= strip_tags(trim($request->input("numeroCalle")));
				}
				$pac->observacion= strip_tags(trim($request->input("observacionCalle")));
				$pac->id_comuna= strip_tags(trim($request->input("comuna")));
				if(trim($request->input("latitud")) == ""){
					$pac->latitud=null;
				}else{
					$pac->latitud= strip_tags(trim($request->input("latitud")));
				}
				if(trim($request->input("longitud")) == ""){
					$pac->longitud=null;
				}else{
					$pac->longitud= strip_tags(trim($request->input("longitud")));
				}

				if($request->input("rango") == "seleccione"){
					$pac->rango_fecha = null;
				}else{
					$pac->rango_fecha = strip_tags($request->input("rango"));
				}
				$pac->rn = $rn;

				if($rn == "si"){
					if($rut_madre && $dv_madre){
						$pac->rut_madre = $rut_madre;
						$pac->dv_madre = ($dv_madre == "K" || $dv_madre == "k")?10:$dv_madre;//arreglar aqui
					}else{
						$pac->rut_madre = null;
						$pac->dv_madre = null;
					}
				}

				$pac->save();

				$pac->insertarCaso($caso);

				$caso->paciente = $pac->id;
				$caso->procedencia = $procedencia;
				if(isset($detalle_procedencia) != null){
					$caso->detalle_procedencia = $detalle_procedencia;
				}
				$caso->fecha_ingreso = $fecha_ingreso;
				$caso->fecha_ingreso2 = $fecha_solicitud;
				$caso->motivo_hospitalizacion = $motivo_hosp;
				$caso->ficha_clinica = $ficha_clinica;
				$caso->dau = $dau;
				$caso->id_complejidad_area_funcional = $servicio;
				$caso->tipo_caso_social = $tipo_caso_social;
				$caso->establecimiento = Session::get("idEstablecimiento");
				$caso->especialidad = strip_tags($request->input("especialidad"));
				$caso->prevision = $prevision;
				$caso->id_usuario = Session::get('usuario')->id;
				$caso->procedencia_geografica = $proc_geo;
				$caso->id_medico = $medico;
				$caso->id_unidad = $id_unidad;

				$caso->save();

				$transito = new ListaTransito();
				$transito->caso = $caso->id;
				$transito->fecha = date('Y-m-d H:i:s');
				$transito->id_usuario_ingresa = Session::get('usuario')->id;
				$transito->save();
				/*
				Parche
				*/

				foreach ($diagnosticos as $key => $value) {
					if($value != "null" ){
						$d = new HistorialDiagnostico();
						$d->caso = $caso->id;
						$d->fecha = $caso->fecha_ingreso;
						$d->diagnostico = strip_tags($value);
						$d->id_cie_10 = strip_tags($hidden_diagnosticos[$key]);
						$d->id_usuario = Auth::user()->id;
						$d->comentario = strip_tags($diagnostico);
						$d->save();
					}
				}

				foreach ($especialidades as $key => $value) {
					$especialidad = new EvolucionEspecialidad();
					$especialidad->fecha = Carbon::now();
					$especialidad->id_caso = $caso->id;
					$especialidad->id_especialidad = $value;
					$especialidad->usuario_asigna = Auth::user()->id;
					$especialidad->save();
				}
				if($casoHospDom){
					$cerrarHospDom = HospitalizacionDomiciliaria::where('caso',$casoHospDom)->first();
					$cerrarHospDom->fecha_termino = $fecha_ingreso->format("Y-m-d H:i:s");
					if($medico){
						$cerrarHospDom->id_medico_alta = $medico;
					}
					$cerrarHospDom->usuario_alta = Auth::user()->id;
					$cerrarHospDom->motivo_salida = "rehospitalizar";
					$cerrarHospDom->save();
				}

				foreach ($tipo_telefono as $key => $tip) {
					if($telefono[$key] != null){
						$nuevo_telefono = new Telefono;
						$nuevo_telefono->id_paciente = $pac->id;
						$nuevo_telefono->tipo = $tip;
						$nuevo_telefono->telefono = $telefono[$key];
						$nuevo_telefono->save();
					}
				}

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

					$id_riesgo = $riesgo->id;

				}else{
					$id_riesgo = null;
				}

				$ev = new EvolucionCaso();
				$ev->caso = $caso->id;
				$ev->fecha = $caso->fecha_ingreso;
				if ($request->input("riesgo") != null || $request->input("riesgo") != '') {
					$ev->riesgo = strip_tags($request->input("riesgo"));
				}else{
					$ev->riesgo = null;
				}

				/*guardar complejidad desde asignar modal Nolazko*/
				if($request->servicios2 != null){
					$ev->id_complejidad_area_funcional = strip_tags($request->servicios2);
				}

				$ev->riesgo_id = $id_riesgo;
				$ev->comentario = $comentario_riesgo;
				$ev->save();


				for($i = 0; $i < 3; $i++){
					$cat = strip_tags($request->input("cat-$i"));
					if($cat != null){
						$ev = new EvolucionCaso();
						$ev->caso = $caso->id;
						$ev->riesgo = $cat;
						$ev->fecha = $fecha_ingreso->copy()->addDays($i+1)->startOfDay();
						$ev->save();
					}
				}

				
				if(isset($request->recibe_visitas)){
					$recibe_visitas = strip_tags($request->input("recibe_visitas"));
					$cantidad_personas = '';
					$cantidad_horas = '';
					  if($recibe_visitas === 'si'){
						$recibe_visitas = true;
						$cantidad_personas = strip_tags($request->input("cantidad_personas"));
						$cantidad_horas = strip_tags($request->input("cantidad_horas"));
					}
					else{
						  $recibe_visitas = false;
					}
				
					$visitas = new ConfiguracionVisitas();
					$visitas->id_caso = $caso->id;
					$visitas->recibe_visitas = $recibe_visitas;
					$visitas->num_personas_visitas = ($cantidad_personas != '')?$cantidad_personas:null;
					$visitas->cant_horas_visitas = ($cantidad_horas != '')?$cantidad_horas:null;
					$visitas->usuario_asigna = Session::get('usuario')->id;
					$visitas->fecha_creacion = Carbon::now()->format("d-m-Y H:i:s");
					$visitas->visible = true;
					$visitas->save();
				}
				

				if($tipo=="ingresar"){
					$cama = Cama::findOrFail($idCama);
					$cama->asignarCaso($caso)->save();
					$mensaje= "Se ha asignado el paciente a la cama";

				}elseif($tipo == "reservar"){
					$reserva=new Reserva;
					$reserva->cama= strip_tags($request->input("cama"));
					$reserva->fecha=$fecha_ingreso;
					$reserva->tiempo= strip_tags($request->input("horas"))." hours";
					$reserva->motivo= strip_tags(trim($request->input("motivo")));
					$reserva->caso=$caso->id;
					$reserva->save();
					$mensaje= "Se ha realizado la reserva";
				}
				else{
					throw new Exception("Tipo de ingreso inválido");
				}

			}catch(Illuminate\Database\QueryException $ex){
				DB::rollback();
				return response()->json(array("mgs" => $ex->getMessage(), "error" => "Error interno"));
			}
			DB::commit();
			return response()->json(array("exito" =>$mensaje));

		}
	}

	public function ingresar(Request $request){
		try{
			DB::beginTransaction();

			$idCama = $request->input("cama");
			$idCaso = $request->input("caso");

			$caso = Caso::findOrFail($idCaso);
			$establecimiento_origen = $caso->establecimientoCaso()->firstOrFail();
			$reserva = $caso->reservas()->orderBy("fecha", "desc")->firstOrFail();

			$cama = Cama::findOrFail($idCama);
			$unidad = $cama->sala()
			->firstOrFail()
			->unidadEnEstablecimiento()
			->firstOrFail();
			$establecimiento = $unidad->establecimientos()->firstOrFail();



			$reserva->tiempo = 0;
			$reserva->save();
			$now = \Carbon\Carbon::now();
			$caso->fecha_termino = $now;
			$caso->detalle_termino = "Traslado al servicio de {$unidad->alias} en {$establecimiento->nombre}";
			$caso->motivo_termino = "traslado externo";
			$caso->save();

			$h = $caso->historialOcupacion()->first();
			if($h != null){
				$h->fecha_liberacion = $now;
				$h->motivo = "traslado externo";
				$h->save();
			}

			$n_caso = new Caso;
			$n_caso->fecha_ingreso = $now;
			$n_caso->paciente = $caso->paciente;
			$n_caso->establecimiento = $establecimiento->id;
			$n_caso->detalle_procedencia = "Traslado desde {$establecimiento_origen->nombre}";
			//$n_caso->diagnostico = $caso->diagnostico;
			$n_caso->medico = $caso->medico;
			$n_caso->prevision = $caso->prevision;
			$n_caso->procedencia = 2;
			$n_caso->save();

			$hOcupacion = new HistorialOcupacion;
			$hOcupacion->cama = $idCama;
			$hOcupacion->caso = $n_caso->id;
			$hOcupacion->fecha = $now;
			$hOcupacion->save();

			DB::commit();
			return response()->json(["msg" => "El paciente ha sido ingresado a la cama"]);

		}catch(Exception $ex){
			DB::rollBack();
			return response()->json(["msg" => "Error al ingresar el paciente a la cama", "error" => $ex->getMessage()]);
		}



	}


	public function getPaciente(Request $request){

		//comprobar que al hacer click el paciente este en esa cama donde se le hizo click
		$ubicacionCama = THistorialOcupaciones::where("caso",$request->idCaso)->whereNull("fecha_liberacion")->first();
		if(isset($ubicacionCama->cama) && $ubicacionCama->cama != $request->idCama){
			Log::info("Paciente fue movido de su cama seleccionara (".$request->idCama.") hacia la cama ".$ubicacionCama->cama );
			return response()->json(["warning"=>"Paciente fue movido de esta cama"]);
		}

		if( $request->idCaso && PreAlta::where("idcaso",$request->idCaso)->whereNull("motivo_salida")->first()){
			//En caso de que el paciente se encuentre en lista de pre alta y lo envien a su ultima ubicacion
			return response()->json(["info"=>"Paciente fue movido a lista de pre alta"]);
		}

		//validaciones
		if(isset($request->ubicacion) && $request->ubicacion = "mapa_de_camas" && $request->color_cama){
			$color_cama = $request->color_cama;
			$actualizar_mapa_camas = Consultas::revisionPacienteMapaCamas($request->idCaso,$color_cama);
			if($actualizar_mapa_camas != "Exito"){
				return response()->json(array("warning" => $actualizar_mapa_camas));
			}
		}
		//validaciones

		$evoluciones = EvolucionCaso::where("caso", $request->idCaso)
			->orderBy("fecha", "desc")
			->get();

		$evolucion = $evoluciones->first();
		//dd($evolucion->complejidad_area_funcional->servicios->nombre_servicio);

		$id=$request->input("id");
		$rut=$request->input("rut");
		$dv = $request->input("dv");

		$idCaso=$request->input("idCaso");
		$MiInfeccion=0;
		$patogeno=0;
		$ubicacion=0;
		$aislamiento=0;
		$user = Auth::user();
		$esIaas=$user->iaas;
		$rn=0;
		$especialidad=0;
		$dias_aislamiento = "";
		$sugerencia_areaf = null;
		$req_aislamiento = FALSE;

		if($idCaso != null){
			Log::info("getPaciente caso -> ".$idCaso." fue consultado por usuario -> ".$user->nombres." con id-> ".$user->id);
			try{
				DB::beginTransaction();
				$ultima_sesion = Sesion::where('id_usuario',Auth::user()->id)->orderBy('id_sesion','desc')->first();
				$ultima_sesion->update(['ultimo_movimiento' => 'getPaciente '.$idCaso]);
				DB::commit();
			}
			catch(Exception $ex){
				Log::info($ex);
				DB::rollback();
				return response()->json(["error"=>"Error ", "ex"=>$ex->getMessage()]);
			}
		}

		$fecha_salida_urg = "";

		$salidaUrg = DB::table('t_historial_ocupaciones as t')
			->join("camas","camas.id","=","t.cama")
			->join("salas","salas.id","=","camas.sala")
			->join("unidades_en_establecimientos","unidades_en_establecimientos.id","=","salas.establecimiento")
			->where("t.caso",$idCaso)
			->where("unidades_en_establecimientos.alias", "=", "Urgencia")
			->whereNotNull("t.fecha_ingreso_real")
			->orderBy("t.updated_at", "asc")
			->get();

		foreach ($salidaUrg as $salidaUrgs) {

			$fecha_salida_urg = $salidaUrgs->fecha_liberacion;

			if($fecha_salida_urg == null){
				$fecha_salida_urg = "";
			}else{
				$fecha_salida_urg = date("d-m-Y H:i", strtotime($fecha_salida_urg));
			}

		}


		$datos=array(
			"rut" 			=> "",
			"nombre" 		=> "",
			"fecha" 		=> "",
			"diagnostico" 	=> "",
			"diagnostico_cie10"=> "",
			"comentario_diagnostico"=>"",
			"riesgo" 		=> "",
			"genero" 		=> "",
			"apellidoP" 	=> "",
			"apellidoM" 	=> "",
			"dv" 			=> "{$dv}",
			"rutSin" 		=> "{$rut}",
			"prevision" 	=> "",
			"dieta" 		=> "",
			"edad"			=> "",
			"fechaSolicitud"	=> "",
            "caso_social"   => "",
            "extranjero"    => "",
            "patogeno"   	=> "",
            "ubicacion"   	=> "",
      		"aislamiento"  	=> "",
      		"esIaas"  		=> "",
      		"rn"  			=> "",
      		"especialidad"  => "",
      		"nombreSocial"	=> "",
			"dias_aislamiento" => "",
			"fecha_ingreso_real" => "",
			"fecha_salida_urg" => "",
			"cant_examenes" => 0,
			"ficha" => "",
			"comentario_lista" => ""
		);

		if(!is_null($idCaso)){
			$infeccion=DB::table( DB::raw("(select i.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
	         			))->get();

			foreach($infeccion as $infec)
			{
				$MiInfeccion=$infec->id;
			}

			$iaas2=DB::table( DB::raw("(select * from iaas as i where i.id_infeccion=$MiInfeccion) as ru"
	         			))->first();

			if($iaas2!=null){
				$patogeno =$iaas2->agente1;
				$ubicacion=$iaas2->localizacion;
				}

			$paciente_infeccion=DB::table( DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
	         			))->get();

			foreach($paciente_infeccion as $paciente_infec)
			{
				$aislamiento=$paciente_infec->aislamiento;
			}

			$dias_casos=DB::select(DB::raw("SELECT tab.caso, fecha, fecha_termino, date_part('day', duracion_iaas)||' días'||' '|| date_part('hour', duracion_iaas)||' horas' as duracion_iaas FROM
				(SELECT caso, fecha, fecha_termino, now(),
				(CASE WHEN fecha_termino IS NULL THEN (now()-fecha) ELSE (fecha_termino-fecha) END) AS duracion_iaas
				FROM infecciones)tab"));

			foreach ($dias_casos as $value) {
				if($value->caso == $idCaso){
					$dias_aislamiento = $value->duracion_iaas;
				}
			}

			if(Auth::user()->tipo != "admin_ss"){
				##CONSULTA PARA OBTENER SUGERENCIA DE AREA FUNCIONAL###
				$sugerencia_areaf_query =DB::table('casos')
									->leftjoin('complejidad_area_funcional', 'casos.id_complejidad_area_funcional', '=', 'complejidad_area_funcional.id_complejidad_area_funcional')
									->leftjoin('area_funcional', 'complejidad_area_funcional.id_area_funcional', '=', 'area_funcional.id_area_funcional')
									->where('casos.paciente', '=' , $id)
									->select('area_funcional.nombre')
									->get();



				foreach($sugerencia_areaf_query as $value)
				{
					$sugerencia_areaf = $value->nombre;
				}
				//return $dias_aislamiento;
				$req_aislamiento_query= DB::select(DB::raw("SELECT casos.requiere_aislamiento FROM casos WHERE casos.id =$idCaso"));

				foreach($req_aislamiento_query as $value)
				{
					$req_aislamiento = $value->requiere_aislamiento;
				}
			}

		}
		if(!$idCaso){
			if($rut)
			{
				$caso_=DB::table( DB::raw("(SELECT DISTINCT(c.id_caso) FROM casos_con_datos_paciente_vista AS c LEFT JOIN pacientes AS p ON c.paciente=p.id WHERE p.rut=$rut)AS a"))->first();
				if($caso_)
					$idCaso=$caso_->id_caso;
			}
			else if($id)
			{
				$caso_=DB::table( DB::raw("(SELECT DISTINCT(c.id_caso) FROM casos_con_datos_paciente_vista AS c LEFT JOIN pacientes AS p ON c.paciente=p.id WHERE p.id=$id)AS a"))->first();
				if($caso_)
					$idCaso=$caso_->id_caso;
			}



		}
		$diagnosticos=null;
		$riesgo=null;
		$diagnostico=null;
		$en_cama=false;
		$detalle_cama = "";
		$enHospDom = "";

		if($idCaso){
			//DIAGNOSTICOS
			$diagnosticos = HistorialDiagnostico::where('caso', $idCaso)
								->where('id_cie_10', '!=', null)
								->get();


			//riesgo
			$riesgo = DB::table('t_evolucion_casos as e')
						->leftjoin('riesgos as r', 'r.id', '=', 'e.riesgo_id')
						->where('caso', $idCaso)
						->orderby('e.id','desc')
						->first();

			/* FECHA HISTORIAL OCUPACIONES */
			$fecha_ingreso_historial = Caso::find($idCaso);
			$fecha_ingreso_historial = $fecha_ingreso_historial->fecha_ingreso2;

			//comentario diagnostico // ahora es nombre de cie_10
			$diagnostico = DB::table(DB::raw("(SELECT d.id_cie_10, d.comentario, c.nombre FROM diagnosticos AS d INNER JOIN cie_10 AS c ON d.id_cie_10=c.id_cie_10 WHERE d.caso=$idCaso AND d.id_cie_10 IS NOT NULL ORDER BY d.fecha DESC LIMIT 1)AS a"))->first();

			//Ficha clinica
			$casoPaciente = DB::table('casos as c')
						->leftjoin("usuarios as u", 'u.id', '=','c.id_usuario')
						->where('c.id', $idCaso)
						->first();

			$en_cama=HistorialOcupacionesVista::where("rut", "=", $rut)
				->whereNull("fecha_liberacion")
				->orderBy("id", "desc")
				->first();

			if($en_cama && $rut != ""){
				$detalle_cama = DB::select(DB::raw("select hv.nombre_establecimiento, c.id_cama, s.nombre, ue.alias
					from historial_ocupaciones_vista hv,
						camas c,
						salas s,
						unidades_en_establecimientos ue
					where hv.rut = $rut
					and c.id = $en_cama->cama
					and c.sala = s.id
					and s.establecimiento = ue.id limit 1;"));
			}

			$enHospDom = HospitalizacionDomiciliaria::join("casos as c", "c.id", "=", "hospitalizacion_domiciliaria.caso")
			->join("pacientes as p", "p.id", "=", "c.paciente")
				->whereNull('hospitalizacion_domiciliaria.fecha_termino')
				->where('p.rut',$rut)
				->first();

		}
		
		$consulta = Consultas::joinUltimoEstadoCamas()
            ->leftJoin("diagnosticos AS diag", "diag.caso", "=", "cs.id")
			->select("pac.rn","pac.rut", "pac.id", "pac.nombre", "pac.dv", "pac.fecha_nacimiento", "uep.riesgo", "pac.sexo", "diag.diagnostico","diag.id_cie_10", "pac.apellido_paterno", "pac.apellido_materno", "cs.prevision", "udt.dieta", "cs.fecha_ingreso", "cs.caso_social","cs.especialidad", "pac.extranjero", "pac.n_identificacion", "pac.nombre_social", "uev.fecha_ingreso_real", "pac.telefono","pac.calle", "pac.numero", "pac.observacion", "pac.latitud", "pac.longitud", "pac.id_comuna", "cs.ficha_clinica","pac.rut_madre", "pac.dv_madre")
            ->orderBy("diag.fecha", "desc");

		if($rut == false){			
			$paciente = $consulta->where("pac.id", $id)/* ->whereNotNUll("diag.id_cie_10") */->first();
		}else{
			$paciente = $consulta->where("pac.rut", $rut)->first();
		}
		//En caso de que el paciente exista en el sistema se rescatan los datos guardados
		if(!is_null($paciente)) {
			$dv=($paciente->dv=="10") ? "K" : $paciente->dv;
			$rut = $paciente->rut ? "{$paciente->rut}-{$dv}" : "Run no disponible";
			$rut_madre = "";
			if(!is_null($paciente->dv_madre)){
				$dv_madre=($paciente->dv_madre=="10") ? "K" : $paciente->dv_madre;
				$rut_madre = $paciente->rut_madre ? "{$paciente->rut_madre}-{$dv_madre}" : "";
			}



			if($idCaso){
				if($casoPaciente->dau != '')
					$dau = $casoPaciente->dau;
				else
					$dau = "Sin información";

				if($casoPaciente->ficha_clinica != ''){
					$ficha = $casoPaciente->ficha_clinica;
				}else{
					$ficha = "Sin información";
				}
			}else{
				$dau = "No disponible";
				$ficha = "No disponible";
			}

			if($paciente->id_comuna != null){
				$region = Comuna::getRegion($paciente->id_comuna)->id_region;
				$comuna = $paciente->id_comuna;
			}

			$calle=(is_null($paciente->calle)) ? "" : ucwords($paciente->calle);
			$numero=(is_null($paciente->numero)) ? "" : ucwords($paciente->numero);
			$observacion_calle=(is_null($paciente->observacion)) ? "" : ucwords($paciente->observacion);
			$apellidoP=(is_null($paciente->apellido_paterno)) ? "" : ucwords($paciente->apellido_paterno);
			$apellidoP=(is_null($paciente->apellido_paterno)) ? "" : ucwords($paciente->apellido_paterno);
			//$diagnostico = (is_null($diagnostico)) ? "" : $diagnostico;//ucwords($diagnostico->diagnostico);
			/* $riesgo = (empty($paciente->riesgo)) ? "No disponible" : $paciente->riesgo; */
			//$edad=Paciente::edad($paciente->fecha_nacimiento);
			$fecha1 = new DateTime($paciente->fecha_nacimiento);
			$fecha2 = new DateTime();
			$fechaF = $fecha1->diff($fecha2);
			$edad = '';
			//return response()->json($fechaF);
			if($fechaF->y == 0){
				$edad = $fechaF->format('%m meses %a dias');
			}else{
				$edad = $fechaF->format('%y años %m meses');
			}
			$apellidoP=(is_null($paciente->apellido_paterno)) ? "" : ucwords($paciente->apellido_paterno);
			$apellidoM=(is_null($paciente->apellido_materno)) ? "" : ucwords($paciente->apellido_materno);
			$fechaIngreso=(is_null($paciente->fecha_ingreso)) ? "" : date("d-m-Y H:i", strtotime($paciente->fecha_ingreso));
			$fecha_ingreso_historial= (is_null($fecha_ingreso_historial)) ? "" : date("d-m-Y H:i", strtotime($fecha_ingreso_historial));
			$fechaIngresoReal=(is_null($paciente->fecha_ingreso_real)) ? "" : date("d-m-Y H:i", strtotime($paciente->fecha_ingreso_real));
			$fechaReal= (is_null($fecha_ingreso_historial)) ? "" : date("d-m-Y", strtotime($fecha_ingreso_historial));

			$usuario_ingreso = (is_null($casoPaciente)) ? "Sin información" : ucwords($casoPaciente->nombres)." ".ucwords($casoPaciente->apellido_paterno)." ".ucwords($casoPaciente->apellido_materno);

            $caso_social = $paciente->caso_social === null? false:$paciente->caso_social;
            $extranjero = $paciente->extranjero;
            $rn=$paciente->rn;
            $especialidad=$paciente->especialidad;
			$nombre_social=(is_null($paciente->apellido_paterno)) ? "Sin información" : ucwords($paciente->nombre_social);
			$telefono = 'No posee';
			$copy_t_telefonos = '';

			$t_telefono = Telefono::where('id_paciente',$paciente->id)->select('tipo','telefono')->get();
			if(count($t_telefono) > 0){
				$copy_t_telefonos = $t_telefono->toJson();
				foreach ($t_telefono as $key => $tele) {
					if($key == 0){
						$telefono = "<br>(".$tele->tipo.") ".$tele->telefono ."<br>";
					}else{
						$telefono .= "(".$tele->tipo.") ".$tele->telefono ."<br>";
					}
				}
			}else{
				if( !is_null($paciente->telefono) && $paciente->telefono != '' && $paciente->telefono != '-'){
					$telefono = "<br> (Casa)".$paciente->telefono;
				}
			}

            try{
                $fecha_nac = \Carbon\Carbon::createFromFormat("Y-m-d", $paciente->fecha_nacimiento);
                $fecha_nac = "{$fecha_nac->format("d-m-Y")}";
            }
            catch(Exception $e){
                $fecha_nac = "No disponible";
			}

			$examenes = Examen::where('pendiente', '=', true)
			->where('caso', '=', $idCaso)
			->where('visible',true)
			->count();

			$comentario = '';
			if($diagnostico->comentario != null){
				$comentario = $diagnostico->comentario;
			}

			$comentario_lista = '';
			$lista = ListaEspera::select('comentario_lista')->where('caso', '=', $idCaso)->first();
			if($lista){
				$comentario_lista = $lista->comentario_lista;
			}


			if(is_null($evolucion)){
				$servicio = "Sin servicio";
				$area = "Sin área";
			}else{
				$servicio = ($evolucion->complejidad_area_funcional == null) ? "Sin servicio" : $evolucion->complejidad_area_funcional->servicios->nombre_servicio;
				$area = ($evolucion->complejidad_area_funcional == null) ? "Sin área" : $evolucion->complejidad_area_funcional->area->nombre;
			}

			////////////////////////////////////
			//restriccion por fecha de ingreso//
			////////////////////////////////////
			/* $resultado = $this->categorizacion($paciente);
			$restriccion_tiempo = $resultado[1]; */

			$respuesta2 = Consultas::restriccionCategorizacionCama($idCaso);
            //$restriccion_tiempo = $resultado[1];
            $restriccion_tiempo = $respuesta2->original["restriccion"];
			$t_estadia_restriccion = $respuesta2->original["sin_categorizar"];

			$pabellon = ListaPabellon::where("id_caso",$idCaso)->whereNull('fecha_salida')->get();
			if($pabellon != "[]"){
				$enPabellon = "si";
			}else{
				$enPabellon = "no";
			}
			$derivacion = ListaDerivados::casoDerivado($idCaso);

			$tmpAsigno = DB::select(DB::Raw("select u.nombres,u.apellido_paterno,u.apellido_materno
								from lista_transito l
								join usuarios u on u.id = l.id_usuario_ingresa
								where l.caso = $idCaso"));

			$tmpHospitalizo = DB::select(DB::Raw("select u.nombres,u.apellido_paterno,u.apellido_materno
			from t_historial_ocupaciones t
			join usuarios u on u.id = t.id_usuario_ingresa
			where t.caso = $idCaso
			order by t.id desc"));

			if($tmpAsigno){
				//significa que el paciente estubo en lista de espera
				$asigno = ($tmpAsigno)? $tmpAsigno[0]->nombres." ".$tmpAsigno[0]->apellido_paterno." ".$tmpAsigno[0]->apellido_materno:null;
			}else if($tmpHospitalizo && !$tmpAsigno){
				//si el paciente fue asignado desde mapa de camas significa que quien hospitalizo tambien asigno la cama
				$asigno = $tmpHospitalizo[0]->nombres." ".$tmpHospitalizo[0]->apellido_paterno." ".$tmpHospitalizo[0]->apellido_materno;
			}else{
				$asigno = null;
			}

			$hospitalizo = ($tmpHospitalizo)? $tmpHospitalizo[0]->nombres." ".$tmpHospitalizo[0]->apellido_paterno." ".$tmpHospitalizo[0]->apellido_materno:null;

			$fecha_nac2 = ($fecha_nac && $fecha_nac!="No disponible")?Carbon::parse($fecha_nac)->format("d/m/Y"):"";
			$edad2 = ($fecha_nac2  && $fecha_nac!="No disponible")?Funciones::calcularEdad($fecha_nac2):"";
			$rango = ($edad2  && $fecha_nac!="No disponible")?Funciones::calcularRangoEdad($edad2):"";

			//Rescatar la ultima derivacion, en caso de tenerla
			$idLista = null;
			if($derivacion){
				$idLista = ListaDerivados::where("caso",$idCaso)->first()->id_lista_derivados;
			}

			##############################################################################################
            ########## INICIO DE SECCION PARA VERIFICAR  SI HAY SOLICITUD DE TRASLADO INTERNO#############

            $existeSolicitud = "null";
          
            if(SolicitudTrasladoInterno::where("caso","=", $idCaso)->first() !== null){
				// en esta seccion ingresan solo pacientes con solicitudes de traslado
                $solicitudQuery = SolicitudTrasladoInterno::where("caso","=", $idCaso)->orderBy("created_at", "DESC")->first();
           
                ##Para entrar en el procedimiento se comprueba que:  
                ## la solicitud exista, la solicitud tenga fecha de traslado nulo y la solicitud esté aceptada

                if($solicitudQuery!==null && $solicitudQuery->fecha_traslado == null && $solicitudQuery->solicitud_aceptada == "VERDADERO"){
					//el historial actual tendra fecha de ingreso real null   

                    $historialActual = THistorialOcupaciones::where('caso','=',$idCaso)->orderBy('id','DESC')->first();
					//el ultimo historial ser el ultimo con histoial que no tenga fecha de ingreso real
                    $historialAnterior = THistorialOcupaciones::where('id','=',$solicitudQuery->id_historial_ocupaciones)->first();
                
                    if($solicitudQuery->fecha_traslado == null && $historialActual->fecha_ingreso_real == null){
						$existeSolicitud = false; 
                    }else{
                        if($solicitudQuery->fecha_traslado == null){
                            $existeSolicitud =true;
                        }
                        else{
                            $existeSolicitud = "null";
                        }
                    }
                }
            }

            ############TERMINO DE SECCION DE TRASLADO INTERNO########
            ##########################################################
			$datos=array(
				"derivacion" => $derivacion,
				"pabellon" => $enPabellon,
				"rut" => $rut,
				"rut_madre" => $rut_madre,
				"nombre" => ucwords($paciente->nombre),
				"fecha" => $fecha_nac,
				"diagnosticos" => $diagnosticos,
				"diagnostico" => $diagnostico,
				"comentario_diagnostico" => $comentario,
				"riesgo" => $riesgo,
				"genero" => ($paciente->sexo == null) ? "" : $paciente->sexo,
				"edad" => $edad,
				"rango" => $rango,
				"apellidoP" => $apellidoP,
				"apellidoM" => ucwords($apellidoM),
				"dv" => $dv,
				"rutSin" => $paciente->rut,
				"prevision" => $paciente->prevision,
				"dieta" => $paciente->dieta,
				"fechaSolicitud" => $fechaIngreso,
                "caso_social" => $caso_social,
				"extranjero" => $extranjero,
				"n_pasaporte" => $paciente->n_identificacion,
                "patogeno"   	=>$patogeno,
            	"ubicacion"   	=> $ubicacion,
      			"aislamiento"  	=> $aislamiento,
      			"esIaas"  		=> $esIaas,
      			"rn"  			=> $rn,
      			"especialidad"  => $especialidad,
      			"nombreSocial" => $nombre_social,
				"id_cie_10" => ($paciente->id_cie_10 == null) ? "" : $paciente->id_cie_10,
				"en_cama" => $en_cama,
				"detalle_cama" => $detalle_cama,
				"en_hosp_dom" => $enHospDom,
				"dias_aislamiento" => $dias_aislamiento,
				"fecha_ingreso_historial" => $fecha_ingreso_historial,
				"ficha" =>$ficha,
				"dau" => $dau,
				"usuario_ingreso" => $usuario_ingreso,
				"telefono" => $telefono,
				"datos_telefono" => $copy_t_telefonos,
				"fecha_ingreso_real" => $fechaIngresoReal,
				"fecha_salida_urg" => $fecha_salida_urg,
				"fechaReal" => $fechaReal,
				"calle" => $calle,
				"numero" => $numero,
				"observacion_direccion" => $observacion_calle,
				"comuna" => $comuna,
				"region" => $region,
				"cant_examenes" => $examenes,
				"ficha" => $paciente->ficha_clinica,
				"comentario_lista" => $comentario_lista,
				//"sugerencia_areaf" => ($sugerencia_areaf == null) ? "No hay sugerencias" : $sugerencia_areaf,
				"idCaso" => $idCaso,
				"idLista" => $idLista,
				"restriccion" => $restriccion_tiempo,
				"t_estadia_restriccion" => $t_estadia_restriccion,
				"servicio" => $servicio,
				"area" => $area,
				"asignoCama" => $asigno,
				"hospitalizoPaciente" => $hospitalizo,
				"existeSolicitud" => $existeSolicitud,
				"idCasoEncrypt" => base64_encode($idCaso),
				//"req_aislamiento" => ($req_aislamiento == FALSE) ? "No requiere aislamiento" : "Sí requiere aislamiento",

			);

			if(Auth::user()->tipo != "admin_ss"){
				$datos ["sugerencia_areaf"] = ($sugerencia_areaf == null) ? "No hay sugerencias" : $sugerencia_areaf;
				$datos ["req_aislamiento"] = ($req_aislamiento == FALSE) ? "No requiere aislamiento" : "Sí requiere aislamiento";
			}

			//alta sin liberar cama
			$caso = Caso::find($idCaso);
			$id_medico_alta = $caso->id_medico_alta;
			if($id_medico_alta){
			$medico = Medico::find($caso->id_medico_alta,['id_medico','nombre_medico','apellido_medico']);
			$nombre_medico_alta = $medico->nombre_medico . " " .$medico->apellido_medico;
			$datos ["nombre_medico_alta"] = $nombre_medico_alta;
			$datos ["id_medico_alta"] = $id_medico_alta;
			}else{

			}


		}else{
			// paciente no esta en el sistema y se busca por la api MINSAL

			$client = new Client(); //GuzzleHttp\Client

			$dvCalculado = Funciones::calcularDv($rut);

			/*
			//priemra version que usan para busqueda de informacion de personas en sigicam


            $response = curl_exec($curl);
            $err = curl_error($curl);
			*/

			$nombreJson = "";
            $fechaJson = "";
            $generoJson = "";
            $apellidoPJson = "";
            $apellidoMJson = "";
            $sexoJson = "";
            $rango = "";
            $direccion = "";
            $cdgComuna = "";
            $cdgRegion = "";

			$prevision = "";
			$prevision_isapre = "";
			$prevision_prais = "";
			$direccion = "";
			$edad= 0;

			//Esta es la ultima version que se esta usando en sigicam
			$token2 = $this->getToken2();
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.minsal.cl/v1/certificadores/previsionales/fonasa/certificado?runPersona=".$rut."&dvPersona=".$dvCalculado."&algo=",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{\n  \"runPersona\" : \"" . $rut . "\",\n  \"dvPersona\" : \"" . $dvCalculado . "\",\n  \"userCreacion\" : \"usuarioBUS\"\n}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $token2,
                    "Content-Type: application/json",
                    "cache-control: no-cache"
                ),
            ));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			$json = json_decode($response, true);

			$validacion = "true";
			if(!$err){
				//Ingresar validaciones en este modelo
				$validacion = ApiConsultas::validar($json);
			}



			if($err || $validacion == "false"){
				//Esta es la version que usan en raven para la busqueda de informacion de personas
				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.minsal.cl/v2/personas/datos/basicos/run?runPersona=".$rut."&dvPersona=".$dvCalculado,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer " . $token2,
						"Content-Type: application/json",
						"cache-control: no-cache"
					),
				));

				$response2 = curl_exec($curl);
				$err2 = curl_error($curl);

				$json2 = json_decode($response2, true);

				if ( $json2["respuesta"]["estado"]["codigo"] == "OK"   && !$err2) {
					//En caso de que no haya funcionado el primero, se busca en la siguiente api usada en raveno
					$rut = $json2["respuesta"]["resultado"]["runPersona"];
					$dv = $json2["respuesta"]["resultado"]["dvPersona"];

					$nombreJson = $json2["respuesta"]["resultado"]["nombresPersona"];
					$apellidoPJson = $json2["respuesta"]["resultado"]["primerApellidoPersona"];
					$apellidoMJson = $json2["respuesta"]["resultado"]["segundoApellidoPersona"];


					if($json2["respuesta"]["resultado"]["glosaSexo"] == 'MUJER'){
						$sexoJson = "femenino";
					}elseif($json2["respuesta"]["resultado"]["glosaSexo"] == 'HOMBRE'){
						$sexoJson = "masculino";
					}else{
						$sexoJson = "indefinido";
					}

					$nacionalidad = $json2["respuesta"]["resultado"]["codPaisOrigen"];
					$fechaNac = $json2["respuesta"]["resultado"]["fechaNacimiento"];
					$cdgRegion = 99;
					$cdgComuna = 99999;

					$fechaJson = ($fechaNac)?Carbon::createFromFormat('d/m/Y',$fechaNac)->format("d-m-Y"):"";

					$edad = ($fechaNac)?Funciones::calcularEdad($fechaNac):"";
					$prevision = "DESCONOCIDO";
				}

				if(($err || ApiConsultas::validar($json) == "false") && $err2){
					//Esta es la version que usan en raven para la busqueda de informacion de personas
					$token = $this->getToken();
					curl_setopt_array($curl, array(
						CURLOPT_URL => "https://apiqa.minsal.cl/v1/personas/basico/run",
						//CURLOPT_URL => "https://google.cl",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "PUT",
						CURLOPT_POSTFIELDS => "{\n  \"runPersona\" : \"" . $rut . "\",\n  \"dvPersona\" : \"" . $dvCalculado . "\",\n  \"userCreacion\" : \"usuarioBUS\"\n}",
						CURLOPT_HTTPHEADER => array(
							"Authorization: Bearer " . $token,
							"Content-Type: application/json",
							"cache-control: no-cache"
						),
					));

					$response3 = curl_exec($curl);
					$err3 = curl_error($curl);

					$json3 = json_decode($response3, true);

					if ( $json3["respuesta"]["estado"]["codigo"] == "OK"   && !$err3) {
						//En caso de que no haya funcionado el primero, se busca en la siguiente api usada en raveno
						$rut = $json3["respuesta"]["resultado"]["runPersona"];
						$dv = $json3["respuesta"]["resultado"]["dvPersona"];

						$nombreJson = $json3["respuesta"]["resultado"]["nombresPersona"];
						$apellidoPJson = $json3["respuesta"]["resultado"]["primerApellidoPersona"];
						$apellidoMJson = $json3["respuesta"]["resultado"]["segundoApellidoPersona"];


						if($json3["respuesta"]["resultado"]["glosaSexo"] == 'MUJER'){
							$sexoJson = "femenino";
						}elseif($json3["respuesta"]["resultado"]["glosaSexo"] == 'HOMBRE'){
							$sexoJson = "masculino";
						}else{
							$sexoJson = "indefinido";
						}

						$nacionalidad = $json3["respuesta"]["resultado"]["codPaisOrigen"];
						$fechaNac = $json3["respuesta"]["resultado"]["fechaNacimiento"];

						$fechaJson = ($fechaNac)?Carbon::createFromFormat('d/m/Y',$fechaNac)->format("d-m-Y"):"";

						$edad = ($fechaNac)?Funciones::calcularEdad($fechaNac):"";
						$prevision = "DESCONOCIDO";
					}
				}
			}
			//Ordenando datos

			//Para parte 1
			if (isset($json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]) && !($err || ApiConsultas::validar($json) == "false")) {
                $nombreJson = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["nombres"];
                $fechaJson = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["fechaNacimiento"];
                $sexoJson = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["genero"];
                $apellidoPJson = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["apell1"];
                $apellidoMJson = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["apell2"];
                $fechaJson = new Carbon($fechaJson);
                $fechaJson = $fechaJson->format("d/m/Y");
                $edad = Funciones::calcularEdad($fechaJson);

                //FONASA
				$prevision = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["afiliadoTO"]["tramo"];
				$prevision_isapre = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["cdgIsapre"];
				$prevision_prais = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["descprais"];
                $direccion = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["direccion"];
                $cdgRegion = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["cdgRegion"];
                $cdgComuna = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["beneficiarioTO"]["cdgComuna"];

                if($cdgRegion == "NULL" || $cdgRegion < 1 || $cdgRegion > 16 ){
					$cdgRegion = 99;
                }else{
                    $cdgRegion = (int) $cdgRegion;
					$cdgComuna = (int ) $cdgComuna;
                }

                if($direccion == "NULL"){
                    $direccion = "";
                }

                if($sexoJson == "F"){
                    $sexoJson = "femenino";
                }
                elseif($sexoJson == "M"){
                    $sexoJson = "masculino";
                }
                else{
                    $sexoJson = "indefinido";
                }

				if($prevision == "A"){
					$prevision = "FONASA A";
				}
				elseif($prevision == "B"){
					$prevision = "FONASA B";
				}
				elseif($prevision == "C"){
					$prevision = "FONASA C";
				}
				elseif($prevision == "D"){
					$prevision = "FONASA D";
				}
				else{

					$cadena_de_texto = $json["respuesta"]["resultado"]["getCertificadoPrevisionalResponse"]["getCertificadoPrevisionalResult"]["coddesc"];

					if ($prevision_isapre != "NULL"){
						$prevision = "ISAPRE";
					}elseif(preg_match("/CAPREDENA/", $cadena_de_texto)){
						$prevision = "CAPREDENA";
					}elseif(preg_match("/DIPRECA/", $cadena_de_texto)){
						$prevision = "DIPRECA";
					}elseif($prevision_prais != "NULL"){
						$prevision = "PRAIS";
					}else{
						$prevision = "DESCONOCIDO";
					}

				}

            }

			if (($edad >= 0) && ($edad <= 9)) {
				$rango = "0-9";
			} elseif (($edad >= 10) && ($edad <= 19)) {
				$rango = "10-19";
			} elseif (($edad >= 20) && ($edad <= 29)) {
				$rango = "20-29";
			} elseif (($edad >= 30) && ($edad <= 39)) {
				$rango = "30-39";
			} elseif (($edad >= 40) && ($edad <= 49)) {
				$rango = "40-49";
			} elseif (($edad >= 50) && ($edad <= 59)) {
				$rango = "50-59";
			} elseif (($edad >= 60) && ($edad <= 69)) {
				$rango = "60-69";
			} elseif (($edad >= 70) && ($edad <= 79)) {
				$rango = "70-79";
			} elseif (($edad >= 80) && ($edad <= 89)) {
				$rango = "80-89";
			} elseif (($edad >= 90) && ($edad <= 99)) {
				$rango = "90-99";
			} elseif (($edad >= 100) && ($edad <= 109)) {
				$rango = "100-109";
			} elseif (($edad >= 110) && ($edad <= 119)) {
				$rango = "110-119";
			} elseif (($edad >= 120) && ($edad <= 129)) {
				$rango = "120-129";
			}

			$enHospDom = HospitalizacionDomiciliaria::join("casos as c", "c.id", "=", "hospitalizacion_domiciliaria.caso")
			->join("pacientes as p", "p.id", "=", "c.paciente")
				->whereNull('hospitalizacion_domiciliaria.fecha_termino')
				->where('p.rut',$rut)
				->first();

			$datos=array(
				"tipo" => "API",
				"rut" => $rut,
				"nombre" => $nombreJson,
				"fecha" => $fechaJson,
				"diagnostico" => "",
				"riesgo" => "",
				"genero" => $sexoJson,//($sexoJson == null) ? "" : ($sexoJson=="HOMBRE"?"masculino":"femenino")
				"edad" => "",
				"apellidoP" => $apellidoPJson,
				"apellidoM" => $apellidoMJson,
				"dv" => $dv,
				"rutSin" => $rut,
				"prevision" => $prevision,
				"dieta" => "",
				"fechaSolicitud" => "",
                "caso_social" => "",
                "extranjero" => "",
                "patogeno" => "",
                "ubicacion" => "",
                "aislamiento" => "",
                "esIaas" => "",
                "rn" => "",
                "especialidad" => "",
                "dias_aislamiento" => "",
                "cant_examenes" => 0,
                "ficha" => "",
				"fecha_ingreso_real" => null,
				"fecha_salida_urg" => null,
                "rango" => $rango,
                "observacion_direccion" => $direccion,
                "comuna" => $cdgComuna,
				"region" => $cdgRegion,
				"en_hosp_dom" => $enHospDom,
			);
		}

		return response()->json($datos);
	}

	public function trasladar($unidad = null, $idCaso=null){


		$establecimiento=EstablecimientosExtrasistema::getEstablecimiento();
		$prevision = Prevision::getPrevisiones();

		$MiInfeccion=0;
		$patogeno=0;
		$ubicacion=0;
		$aislamiento=0;

		/* @var $caso Caso */
		//return $idCaso;
		if ($unidad){
			Session::forget("trasladar_caso");
			Session::forget("trasladar_unidad");
			$unidad = UnidadEnEstablecimiento::where("url", $unidad)->firstOrFail();
			$caso = Caso::find($idCaso);
			$paciente = ($caso == null ) ? null : $caso->paciente()->first();
		}
		else{
			$caso = null;
			$paciente = null;
		}
		if(!is_null($idCaso)){

				$infeccion=DB::table( DB::raw("(select i.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
		         			))->get();

				foreach($infeccion as $infec)
				{
					$MiInfeccion=$infec->id;
				}

				$iaas2=DB::table( DB::raw("(select * from iaas as i where i.id_infeccion=$MiInfeccion) as ru"
		         			))->first();

				if($iaas2!=null){
					$patogeno =$iaas2->agente1;
					$ubicacion=$iaas2->localizacion;
					}

				$paciente_infeccion=DB::table( DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
		         			))->get();

				foreach($paciente_infeccion as $paciente_infec)
				{
					$aislamiento=$paciente_infec->aislamiento;
				}
		}
		Session::put("trasladar_caso",$caso);
		Session::put("trasladar_unidad", $unidad);
		return View::make("Gestion/Trasladar")->with("riesgo", Riesgo::getRiesgos())
		->with("unidad_obj", $unidad)->with("prevision", $prevision)
		->with("establecimiento", $establecimiento)->with("paciente", $paciente)->with("caso", $caso)
		->with("patogeno", $patogeno)->with("ubicacion", $ubicacion)->with("aislamiento", $aislamiento);
	}

public function historial2($idCaso=null){

		$prevision 			   = Prevision::getPrevisiones();
		$localizacion 		   = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento 		   = ProcedimientoInvasivo::getlocalizacion();




			$caso = Caso::find($idCaso);
			$caso2=$caso->id;
			$casoPaciente=$caso->paciente;
			$paciente = ($caso == null ) ? null : $caso->paciente()->first();


		$user = Auth::user();
		$esIaas=$user->iaas;



							$DatosHistoricos=DB::table( DB::raw("(select c.id,p.rut,p.dv,p.nombre as nombrep,p.apellido_materno,p.apellido_paterno,c.fecha_ingreso,c.fecha_termino,c.establecimiento,i.id as IAAS, d.diagnostico, d.id_cie_10, e.nombre from casos as c
							inner join pacientes as p on p.id=c.paciente
							left join infecciones as i on i.caso=c.id
							INNER JOIN diagnosticos as d on d.caso = c.id
							INNER JOIN establecimientos as e on e.id = c.establecimiento
							where p.id=$casoPaciente
							order by c.id desc) as re"
					         			))->get();

		//return response()->json($DatosHistoricos);
		return View::make("Gestion/Historial")->with("DatosHistoricos", $DatosHistoricos);
	}


public function historial($unidad = null, $idCaso=null){
		$establecimiento 	   =EstablecimientosExtrasistema::getEstablecimiento();
		$prevision 			   = Prevision::getPrevisiones();
		$localizacion 		   = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento 		   = ProcedimientoInvasivo::getlocalizacion();
		$Miunidad=$unidad;


		$datos_ocupacion = DB::table("t_historial_ocupaciones as h")
		->select("u.url", "s.id as idSala","c.id as idCama")
		->leftjoin("camas as c", "c.id", "=", "h.cama")
		->leftjoin("salas as s", "s.id", "=", "c.sala")
		->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
		->where("h.caso", $idCaso)
		->whereNull("h.fecha_liberacion")
		->first();

		$cama= "";
		$sala= "";
		$url= "";
 		if(isset($datos_ocupacion)){
			$cama = $datos_ocupacion->idCama;
			$sala = $datos_ocupacion->idSala;
			$url = $datos_ocupacion->url;
		}else{
			$url = "error";
		}
 		/* @var $caso Caso */
		if ($unidad){
			Session::forget("trasladar_caso");
			Session::forget("trasladar_unidad");
			$unidad = UnidadEnEstablecimiento::where("url", $unidad)->firstOrFail();
			$caso = Caso::find($idCaso);
			$caso2=$caso->id;
			$casoPaciente=$caso->paciente;
			$paciente = ($caso == null ) ? null : $caso->paciente()->first();
		}
		else{
			$caso = null;
			$paciente = null;
		}

		Session::put("trasladar_caso",$caso);
		Session::put("trasladar_unidad", $unidad);
		$user = Auth::user();
		$esIaas=$user->iaas;

		$DatosHistoricos=DB::table( DB::raw("(select c.id,
											p.id as id_paciente,
											p.rut,
											p.dv,
											p.nombre as nombrep,
											p.apellido_materno,
											p.apellido_paterno,
											c.fecha_ingreso,
											c.fecha_termino,
											c.establecimiento,
											i.id as IAAS,
											d.diagnostico,
											d.id_cie_10,
											e.nombre
											from casos as c
		inner join pacientes as p on p.id=c.paciente
		left join infecciones as i on i.caso=c.id
		INNER JOIN diagnosticos as d on d.caso = c.id
		INNER JOIN establecimientos as e on e.id = c.establecimiento
		where p.id=$casoPaciente
		order by c.id desc) as re"
         			))->get();


		//return response()->json($DatosHistoricos);
		return View::make("Gestion/Historial")->with("DatosHistoricos", $DatosHistoricos)
			->with("Miunidad", $Miunidad)
			->with("sala", $sala)
			->with("cama", $cama)
			->with("paciente", $paciente->id)
			->with("idCaso", $idCaso);
			 /**->with("paciente", $paciente->id); */

	}

	public function verinfecciones($unidad = null, $idCaso=null){
		$establecimiento=EstablecimientosExtrasistema::getEstablecimiento();
		$prevision = Prevision::getPrevisiones();
		$localizacion = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento = ProcedimientoInvasivo::getlocalizacion();
		$Miunidad=$unidad;

		/* @var $caso Caso */
		if ($unidad){
			Session::forget("trasladar_caso");
			Session::forget("trasladar_unidad");
			$unidad = UnidadEnEstablecimiento::where("url", $unidad)->firstOrFail();
			$caso = Caso::find($idCaso);
			$caso2=$caso->id;
			$infeccion2=DB::table( DB::raw("(select * from casos as c,infecciones as i where c.id=i.caso and c.id=$caso2 and i.caso=$caso2 and i.fecha_termino is null) as re"
         			))->get();

			foreach($infeccion2 as $infec){

					$MiInfeccion=$infec->id;
			}


			$VenaCentral=DB::table( DB::raw("(select * from cvc as i where i.id_infeccion=$MiInfeccion) as ra"
         			))->get();

			$iaas2=DB::table( DB::raw("(select * from iaas as i where i.id_infeccion=$MiInfeccion) as ru"
         			))->get();

			$paciente_infeccion=DB::table( DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
         			))->get();

			foreach($paciente_infeccion as $paciente_infectado){

					$MiPaciente=$paciente_infectado->id;
			}


			$paciente = ($caso == null ) ? null : $caso->paciente()->first();
		}
		else{
			$caso = null;
			$paciente = null;
		}
		Session::put("trasladar_caso",$caso);
		Session::put("trasladar_unidad", $unidad);
		$user = Auth::user();
		$esIaas=$user->iaas;

		$UnidadesIAAS=array();

		$esta=Session::get('idEstablecimiento');

		$Alias=DB::table( DB::raw("(select alias from unidades_en_establecimientos where establecimiento=$esta and visible!=FALSE order by id) as re"
         			))->get();

		foreach ($Alias as $UnidadAlia) {
			$UnidadesIAAS[$UnidadAlia->alias]=$UnidadAlia->alias;

		}

		return View::make("Gestion/VerInfecciones")->with("riesgo", Riesgo::getRiesgos())
		->with("unidad_obj", $unidad)->with("prevision", $prevision)->with("localizacion", $localizacion)
		->with("establecimiento", $establecimiento)->with("paciente", $paciente)->with("caso", $caso)->with("caso2", $caso2)->with("caso2", $caso2)
		->with("Miunidad", $Miunidad)->with("infeccion2",$infeccion2)->with("VenaCentral",$VenaCentral)->with("iaas2",$iaas2)->with("MiInfeccion",$MiInfeccion)
		->with("esIaas", $esIaas)->with("procedimiento", $procedimiento)->with("paciente_infeccion", $paciente_infeccion)->with("CaracteristicasAgente", $CaracteristicasAgente)
		->with("AgenteEtiologico", $AgenteEtiologico)->with("MiPaciente", $MiPaciente)->with("UnidadesIAAS", $UnidadesIAAS);
	}

public function verinfecciones2($idCaso=null,$idEstablecimiento){
		$establecimiento=null;
		$prevision = Prevision::getPrevisiones();
		$localizacion = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento = ProcedimientoInvasivo::getlocalizacion();


		/* @var $caso Caso */
			$caso = Caso::find($idCaso);
			$caso2=$caso->id;
			$paciente = ($caso == null ) ? null : $caso->paciente()->first();
			$infeccion2=DB::table( DB::raw("(select * from casos as c,infecciones as i where c.id=i.caso and c.id=$caso2 and i.caso=$caso2 and i.fecha_termino is null) as re"
         			))->get();

			foreach($infeccion2 as $infec){

					$MiInfeccion=$infec->id;
			}


			$VenaCentral=DB::table( DB::raw("(select * from cvc as i where i.id_infeccion=$MiInfeccion) as ra"
         			))->get();

			$iaas2=DB::table( DB::raw("(select * from iaas as i where i.id_infeccion=$MiInfeccion) as ru"
					 ))->get();

			$paciente_infeccion=DB::table( DB::raw("(select * from pacientes_infeccion as i where i.id_infeccion=$MiInfeccion) as ri"
					 ))->get();

			foreach($paciente_infeccion as $paciente_infectado){

					$MiPaciente=$paciente_infectado->id;
			}



		$user = Auth::user();
		$esIaas=$user->iaas;

		$UnidadesIAAS=array();

		$esta=Auth::user()->establecimiento;
		//$idEstablecimiento;

		$Alias=DB::table( DB::raw("(select alias from unidades_en_establecimientos where establecimiento=$esta and visible!=FALSE order by id) as re"
         			))->get();

		foreach ($Alias as $UnidadAlia) {
			$UnidadesIAAS[$UnidadAlia->alias]=$UnidadAlia->alias;

		}

		return View::make("Gestion/VerInfecciones2")->with("riesgo", Riesgo::getRiesgos())
        ->with("prevision", $prevision)->with("localizacion", $localizacion)
		->with("establecimiento", $establecimiento)->with("paciente", $paciente)->with("caso", $caso)->with("caso2", $caso2)->with("caso2", $caso2)
        ->with("infeccion2",$infeccion2)->with("VenaCentral",$VenaCentral)->with("iaas2",$iaas2)->with("MiInfeccion",$MiInfeccion)
		->with("esIaas", $esIaas)->with("procedimiento", $procedimiento)->with("paciente_infeccion", $paciente_infeccion)->with("CaracteristicasAgente", $CaracteristicasAgente)
		->with("AgenteEtiologico", $AgenteEtiologico)->with("MiPaciente", $MiPaciente)->with("UnidadesIAAS", $UnidadesIAAS)->with("esta", $esta);
	}


	public function infecciones2($idCaso=null,$idEstablecimiento){
		$establecimiento 	   =null;
		$prevision 			   = Prevision::getPrevisiones();
		$localizacion 		   = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento 		   = ProcedimientoInvasivo::getlocalizacion();



		$caso = Caso::find($idCaso);
		$caso2=$caso->id;
		$paciente = ($caso == null ) ? null : $caso->paciente()->first();

		$user = Auth::user();
		$esIaas=$user->iaas;

		$UnidadesIAAS=array();
		$esta=$idEstablecimiento;

		$Alias=DB::table( DB::raw("(select alias from unidades_en_establecimientos where establecimiento=$esta and visible!=FALSE order by id) as re"
         			))->get();

		foreach ($Alias as $UnidadAlia) {
			$UnidadesIAAS[$UnidadAlia->alias]=$UnidadAlia->alias;

		}


		return View::make("Gestion/infecciones2")->with("riesgo", Riesgo::getRiesgos())
		->with("prevision", $prevision)->with("localizacion", $localizacion)
		->with("establecimiento", $establecimiento)->with("paciente", $paciente)->with("caso", $caso)->with("caso2", $caso2)
		->with("esIaas", $esIaas)->with("procedimiento", $procedimiento)->with("CaracteristicasAgente", $CaracteristicasAgente)
		->with("AgenteEtiologico", $AgenteEtiologico)->with("UnidadesIAAS", $UnidadesIAAS)->with("esta", $esta);
	}


	public function infecciones($unidad = null, $idCaso=null){
		$establecimiento 	   =EstablecimientosExtrasistema::getEstablecimiento();
		$prevision 			   = Prevision::getPrevisiones();
		$localizacion 		   = Localizacion::getlocalizacion();
		$AgenteEtiologico 	   = AgenteEtiologico::getlocalizacion();
		$CaracteristicasAgente = CaracteristicasAgente::getlocalizacion();
		$procedimiento 		   = ProcedimientoInvasivo::getlocalizacion();
		$Miunidad=$unidad;

		/* @var $caso Caso */
		if ($unidad){
			Session::forget("trasladar_caso");
			Session::forget("trasladar_unidad");
			$unidad = UnidadEnEstablecimiento::where("url", $unidad)->firstOrFail();
			$caso = Caso::find($idCaso);
			$caso2=$caso->id;
			$paciente = ($caso == null ) ? null : $caso->paciente()->first();
		}
		else{
			$caso = null;
			$paciente = null;
		}
		Session::put("trasladar_caso",$caso);
		Session::put("trasladar_unidad", $unidad);
		$user = Auth::user();
		$esIaas=$user->iaas;

		$UnidadesIAAS=array();
		$esta=Session::get('idEstablecimiento');

		$Alias=DB::table( DB::raw("(select alias from unidades_en_establecimientos where establecimiento=$esta and visible!=FALSE order by id) as re"
         			))->get();

		foreach ($Alias as $UnidadAlia) {
			$UnidadesIAAS[$UnidadAlia->alias]=$UnidadAlia->alias;

		}


		return View::make("Gestion/infecciones")
				->with("riesgo", Riesgo::getRiesgos())
				->with("unidad_obj", $unidad)
				->with("prevision", $prevision)
				->with("localizacion", $localizacion)
				->with("establecimiento", $establecimiento)
				->with("paciente", $paciente)
				->with("caso", $caso)
				->with("caso2", $caso2)
				->with("Miunidad", $Miunidad)
				->with("esIaas", $esIaas)
				->with("procedimiento", $procedimiento)
				->with("CaracteristicasAgente", $CaracteristicasAgente)
				->with("AgenteEtiologico", $AgenteEtiologico)
				->with("UnidadesIAAS", $UnidadesIAAS);
	}



	public function getCamasDisponibles(Request $request){
		$url_unidad=$request->input("unidad");
	    $unidad = UnidadEnEstablecimiento::where("establecimiento", Session::get("idEstablecimiento"))
	    ->where("url", $url_unidad)->firstOrFail();
		$apendice = Session::get("complejidad") == "baja"?
			"Puede crear igualmente una solicitud.":
			"Se recomienda trasladar al extrasistema, o puede crear igualmente<br>una solicitud a una institución dentro del Servicio.";
		$servicios = $unidad->unidadesDerivables()->get();
		if(empty($servicios)){
			$msg = "No se han encontrado instituciones que acepten derivaciones de {$unidad->alias} dentro del Servicio.<br>{$apendice}";
			return response()->json(array(
				"datos" => [],
				"mensaje" => $msg,
			));
		}
	    $camas=array();
	    foreach($servicios as $servicio){
	        $estabs=Consultas::getEstablecimientoConCamas($servicio->id);
	        foreach ($estabs as $est) {
				if($est->cantidad == 0) continue;
	            $camas[]=array($est->nombre, $est->alias, $est->cantidad, "<a class='cursor' onclick='abrirSolicitar({$est->destino})'>Solicitar</a>");
	        }
	    }
		if(empty($camas)){
			return response()->json(array(
				"datos" => [],
				"mensaje" => "No hay camas disponibles en los servicios que acepten derivaciones de {$unidad->alias}.<br>{$apendice}",
			));
		}
	    return response()->json(array(
			"datos" => $camas,
			"mensaje" => "OK"
		));
	}

	/*public function tieneCamaDisponible(){
		$id=Session::get("idEstablecimiento");
		$total=Consultas::getCamasPorEstablecimiento($id);
		$tiene=($total == 0) ? false : true;
		return response()->json($tiene);
	}*/

	public function _registrarPaciente(Request $request){

		$rut=trim($request->input("rut"));
		$diagnostico = $request->input("diagnostico");


		if(!$rut || !Paciente::existePaciente($rut)){
			/* Si el paciente no existe... */
			$pac=new Paciente();
			/* Esta funcion le crea un caso y una evolucion de riesgo*/
			/* $paso =  */

			//aca caga 4
			$caso =  $pac->registrarPaciente($request->all());
			/* return $caso; */
			/* return response()->json($caso); */
			$caso->establecimiento = Session::get("idEstablecimiento");

			$caso->save();

			$diagnosticos = $request->input("diagnosticos");

			$hidden_diagnosticos = $request->input("hidden_diagnosticos");

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
            /* $d = new HistorialDiagnostico();
            $d->caso = $caso->id;
            $d->fecha = $caso->fecha_ingreso;
            $d->diagnostico = $diagnostico;
            $d->save(); */

			return $pac;

		}
		else{

			/*AQUI SE GUARDAN LOS DATOS AL REALIZAR TARSLADO EXTERNO DE UN PACIENTE DE TRASLADO EXTERNO EN LA SECCION DE SELECCIONAR POR CUPO*/

			$paciente=Paciente::where("rut", "=", $rut)->first();
			$paciente->sexo=$request->input("sexo");
			$paciente->nombre=trim($request->input("nombre"));
			$paciente->apellido_paterno=trim($request->input("apellidoP"));
			$paciente->apellido_materno=trim($request->input("apellidoM"));
			$paciente->nombre_social=trim($request->input("nombreSocial"));
			$diagnosticos = $request->input("diagnosticos");

			$hidden_diagnosticos = $request->input("hidden_diagnosticos");

			$paciente->save();
			try{
				$caso = $paciente->casoActual()->firstOrFail();
			}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
				$caso = $paciente->registrarCasoPaciente($request->all());
			}
			$caso->establecimiento = Session::get("idEstablecimiento");
			$caso->save();

			$riego = $paciente->registrarEvolucionPaciente($caso->id, $request);

			foreach ($diagnosticos as $key => $value) {

                if($hidden_diagnosticos[$key] != "null" && $hidden_diagnosticos[$key] != "" ){


					$d = new HistorialDiagnostico();
					$d->caso = $caso->id;
					$d->fecha = $caso->fecha_ingreso;
					$d->diagnostico = $value;
					$d->id_cie_10 = $hidden_diagnosticos[$key];
					$d->save();

            	}
			}

			/* return $diagnosticos; */
            //OTRO DIAGNOSTICO
            /* $d = new HistorialDiagnostico();
            $d->caso = $caso->id;
            $d->fecha = $caso->fecha_ingreso;
            $d->diagnostico = $diagnostico;
            $d->save(); */


			return $paciente;
		}
	}

	public function registrarPaciente(){

		/* return "registrarPaciente"; */
		try{

			$this->_registrarPaciente(new Request);

			return response()->json(array("exito" => "El paciente ha sido registrado"));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al registrar paciente", "msg" => $ex->getMessage()));
		}

	}

	public function registrarSoloPaciente(Request $request){
	    try{
	        $pac=Paciente::whereRut($request->input("rut"))->get();
	        if($pac->isEmpty()){
	        	$pac = new Paciente();
	        }
	        else{
	        	$pac = $pac->first();
	        }
	        $pac->registrarNuevoPaciente(Input::all());
	        return response()->json(array("exito" => $pac->id));
	    }catch(Exception $ex){
	        return response()->json(array("error" => "Error al registrar paciente", "msg" => $ex->getMessage()));
	    }
	}


public function derivarPaciente(Request $request){
		//try{
			$rut=trim($request->input("rut"));

			if(!Paciente::existePaciente($rut)){
				$pac=new Paciente();
				$caso = $pac->registrarPaciente(Input::all());
				$caso->establecimiento = Session::get("idEstablecimiento");
				$caso->save();
			}

			$idPaciente=Paciente::getIdPaciente($rut);
			$tiene=Derivacion::tieneDerivaciones($idPaciente);
			if(!$tiene){
				$derivar=new Derivacion;
				$derivar->caso=Paciente::getIDCasoPaciente($rut);
				$derivar->fecha=DB::raw("date_trunc('seconds', now())");
				$derivar->destino=$request->input("idEstablecimiento");
				$derivar->save();

				$idDerivacion=$derivar->id;
				$DerivaDest=$derivar->destino;


				$EstableciMensaje=DB::table( DB::raw("(select e.nombre from unidades_en_establecimientos as u, establecimientos as e where u.establecimiento=e.id and u.id=$DerivaDest) as ra"))->get();

        	 	foreach ($EstableciMensaje as $Messages) {
        	 		$establecimi=$Messages->destino;
        	 	}

				$mensaje=new MensajeDerivacion;
				$mensaje->derivacion=$idDerivacion;
				$mensaje->fecha=DB::raw("date_trunc('seconds', now())");
				$mensaje->establecimiento_envio=Session::get("idEstablecimiento");
				$mensaje->mensaje=trim($request->input("texto"));
				$mensaje->asunto=trim($request->input("asunto"));
				$mensaje->destino=$establecimi;
				$mensaje->save();

				return response()->json(array("exito" => "El traslado interno se ha realizado exitosamente"));
			}
			return response()->json(array("error" => "El paciente con run $rut ya posee una solicitud de reserva", "msg" => ""));
		//}catch(Exception $ex){
		//	return response()->json(array("error" => "Error al registrar la derivación", "msg" => $ex->getMessage()));
		//}
	}

	public function renovar(Request $request){
		$idCaso=$request->input("idCaso");
		$hora=$request->input("hora");
		$idReserva=Consultas::getIdReservaPorCaso($idCaso);
		$reserva=Reserva::find($idReserva);
		$tiempo=$reserva->tiempo;
		$reserva->tiempo=date("H:i:s", (strtotime($tiempo)+3600*$hora));
		$reserva->renovada=true;
		$reserva->save();
	}

	public function getUnidades(Request $request){
		/*
		 * Solo se envía cuando se usan las camas temporales
		 */
		$unidad = $request->unidad;
		
		$id=$request->input("id");
		if(empty($id)) $id=Session::get("idEstablecimiento");
        $est = Establecimiento::with(["unidades" => function($q)use($unidad){
            //$q->whereHas("camas", function($q){
            $q->where("visible", true)->orderBy("alias")->has("camasLibres")->with("camasLibres");
			
			if($unidad){
				$q->where("id","=",$unidad);
			}
            //});
		}])->find($id);

        foreach($est->unidades as $k => $unidad){
            $unidad->cupo = $unidad->camasLibres->count();
        }
		return response()->json($est->unidades);
	}

	public function getUnidadesIndex(Request $request){
		$id = $request->input("id");
		if(empty($id)) $id = Session::get("idEstablecimiento");
		$u = UnidadEnEstablecimiento::where("visible", true)->whereHas("camas", function($q){
			$q->vigentes();
		})->select("alias as nombre", "url as alias", "id", "id_area_funcional")
		->where("establecimiento", $id)->get()->toJson();

		$infectados=DB::select(DB::raw("select id_establecimiento, nombre_establecimiento, id_servicio, nombre_servicio from (select * from casos_iaas ci
			where ci.fecha_termino is null) ci, ultimas_ocupaciones_vista u
			where
			ci.caso=u.caso
			group by id_establecimiento, nombre_establecimiento, id_servicio, nombre_servicio"));

		$estab_infec = [];
		foreach($infectados as $inf){
			if($inf->id_establecimiento == $id){
				$estab_infec[] = $inf->id_servicio;
			}
		}

		return response()->json(array(json_decode($u), $estab_infec));

	}

	public function getUnidadesSelect(Request $request){
		return response()->json( Establecimiento::findOrFail($request->input("selectEstablecimiento"))->getUnidades() );
	}

	public function reasignar(Request $request){
		$casoOld=$request->input("casoOld");
		$camaNew=$request->input("camaNew");

		$cama_naranja = DB::table('t_historial_ocupaciones')
						->where('caso',$casoOld)
						->where('cama',$request->camaOld)
						->whereNull('fecha_ingreso_real')
						->get();

		$cama_disponible = DB::table('t_historial_ocupaciones as th')
						->select('fecha_liberacion')
						->where('cama', '=', $camaNew)
						->whereNull('fecha_liberacion')
						->orderBy('fecha', 'desc')
						->first();

		if($cama_disponible){
			return response()->json(["error"=>"Error, la cama ha sido ocupada"]);
		}

        if(Cache::has("reasignar-{$casoOld}-{$camaNew}")) {
            return response()->json(["error" => "Intente el traslado nuevamente"]);
        }


        Caso::findOrFail($casoOld)->reasignarCama($camaNew,$cama_naranja,null,$request->id_paciente);

        Cache::add("reasignar-{$casoOld}-{$camaNew}", true, 1);
		/*
		 * Quita el caso de la cama temporal, si lo hay
		 */
		$ct = new \App\Models\CamaTemporal();
		$ct->ocultarCaso($casoOld);
        return response()->json(["msg" => "Se ha realizado el traslado interno"]);

	}

	public function reasignarDesdePabellon(Request $request){

        $casoOld=$request->input("casoOld");
		$camaNew=$request->input("camaNew");

		$cama_naranja = DB::table('t_historial_ocupaciones')
						->where('caso',$casoOld)
						->where('cama',$request->camaOld)
						->whereNull('fecha_ingreso_real')
						->get();

		$cama_disponible = DB::table('t_historial_ocupaciones as th')
						->select('fecha_liberacion')
						->where('cama', '=', $camaNew)
						->whereNull('fecha_liberacion')
						->orderBy('fecha', 'desc')
						->first();

		if($cama_disponible){
			return response()->json(["error"=>"Error, la cama ha sido ocupada"]);
		}

        if(Cache::has("reasignar-{$casoOld}-{$camaNew}")) {
            return response()->json(["error" => "Intente el traslado nuevamente"]);
        }

		Caso::findOrFail($casoOld)->reasignarCama($camaNew,$cama_naranja);
        // Caso::findOrFail($casoOld)->reasignarCama($camaNew);

        Cache::add("reasignar-{$casoOld}-{$camaNew}", true, 1);
        return response()->json(["msg" => "Se ha realizado el traslado interno"]);

    }



    public function registrarTraslado(Request $request){


    	DB::beginTransaction();
		$destino = storage_path().'/data/derivaciones';
		try{

			//
			//return $request->all();
			$idUnidadEstablecimiento = UnidadEnEstablecimiento::findOrFail($request->input("idEstablecimiento"))->id;

			$rut=trim($request->input("rut"));
			//return date("H");
			$paciente = $this->_registrarPaciente($request);

			$tiene = Derivacion::tieneDerivaciones($paciente->id);

			$caso = $paciente->casoActual(\Carbon\Carbon::tomorrow())->first();
			/* return $caso; */
			$idCaso = $caso->id;

			if(!$tiene){
				$derivar=new Derivacion;
				$derivar->caso=$idCaso;
				$derivar->usuario=Auth::user()->id;
				$derivar->establecimiento = Session::get("idEstablecimiento");
				$derivar->fecha=DB::raw("date_trunc('seconds', now())");
				$derivar->destino=$idUnidadEstablecimiento;
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



    public function registrarTrasladoFun(){

		$destino = storage_path().'/data/derivaciones';
    	//try{
		return DB::transaction( function() use($destino) {

			//trigger_error("xxxxxxxxxxxxxxxxxxx". $request->input("idEstablecimiento"));
			$idUnidadEstablecimiento = UnidadEnEstablecimiento::findOrFail($request->input("idEstablecimiento"))->id;

			$rut=trim($request->input("rut"));

			$paciente = $this->_registrarPaciente();
			$tiene = Derivacion::tieneDerivaciones($paciente->id);
			//return $paciente;

			$caso = $paciente->casoActual(\Carbon\Carbon::tomorrow())->first();
			//return $caso;
			$idCaso = $caso->id;



			if(!$tiene){
				$derivar=new Derivacion;
				$derivar->caso=$idCaso;
				$derivar->usuario=Auth::user()->id;
				$derivar->establecimiento = Session::get("idEstablecimiento");
				$derivar->fecha=DB::raw("date_trunc('seconds', now())");
				$derivar->destino=$idUnidadEstablecimiento;
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

				$files=Input::file("files");
				$destino = "{$destino}/{$idCaso}/{$idDerivacion}";
				File::makeDirectory($destino, 0775, true, true);
				foreach($files as $file){
					if(empty($file)) continue;
					$filename = $file->getClientOriginalName();
					$file->move($destino, $filename);

					$documento=new Documento;
					$documento->derivacion=$idDerivacion;
					$documento->recurso="{$destino}/{$filename}";
					$documento->save();
				}

				return response()->json(["exito" => "La derivación se ha realizado exitosamente"]);
			}
			return response()->json([
				"error" => "El paciente con run {$rut} ya posee una solicitud de reserva",
				"msg" => ""
			]);
    	});
    }

    public function obtenerMensajeBloqueo(Request $request){
    	$idCama=$request->input("idCama");
    	$historial=HistorialBloqueo::where("cama", "=", $idCama)->orderBy("fecha", "desc")->first();
    	if($historial == null) return response()->json(["motivo" => ""]);
    	return response()->json(["motivo" => ucwords($historial->motivo)]);
    }

	public function bloquearCama(Request $request){
		try{
			$idCama=$request->input("idCama");
			$motivo=$request->input("motivo");
			if($motivo == 'otros') $motivo = $request->input("otro_motivo");

			$bloqueada=HistorialBloqueo::estaCamaBloqueada($idCama);

			if($bloqueada){
				$historial=new HistorialBloqueo;
				$historial->cama=$idCama;
				$historial->fecha=DB::raw("date_trunc('seconds', now())");
				$historial->motivo=$motivo;
				$historial->save();
			}

			return response()->json(array("exito" => "Se ha bloqueado la cama", "b" => $bloqueada));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al bloquear la cama", "msg" => $ex->getMessage()));
		}
	}

	public function desbloquearCama(Request $request){
		try{
			if(Auth::user()->tipo == TipoUsuario::USUARIO){
				throw new Exception();
			}
			$idCama=$request->input("idCama");
			$motivo=$request->input("motivo");

			$historial=HistorialBloqueo::where("cama", "=", $idCama)->orderBy("fecha", "desc")->first();
			$historial->fecha_habilitacion=\Carbon\Carbon::now();
			$historial->motivo_habilitacion=$motivo;
			$historial->save();

			return response()->json(array("exito" => "Se ha desbloqueado la cama"));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al desbloquear la cama", "msg" => $ex->getMessage()));
		}
	}

	public function getSalas(Request $request){
		$idEstablecimiento=$request->input("idEstablecimiento");
		return response()->json(Sala::getSalasEstablecimiento($idEstablecimiento));
	}

	public function reconvertir(Request $request){
		try{
			$sala=$request->input("sala");
			$servicio=$request->input("servicio");
			$cama=$request->input("cama");

			$historial=new HistorialCamasUnidades;
			$historial->cama=$cama;
			$historial->unidad=$servicio;
			$historial->fecha=DB::raw("date_trunc('seconds', now())");
			$historial->save();

			return response()->json(array("exito" => "Se ha reconvertido la cama"));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al reconvertir la cama", "msg" => $ex->getMessage()));
		}
	}

	public function registrarExtraSistema(Request $request){

		/* return "hola registrarPaciente"; */
		return DB::transaction(function() use ($request) {
			/* @var $pac Paciente*/

			$establecimiento = $request->input("estabsExterno");

			$rut = trim($request->input("rut"));
			$idPaciente = trim($request->input("idPaciente"));

			if ($rut !== ''){
				if(!Paciente::existePaciente($rut)) {
					$pac = new Paciente();
					$pac->registrarNuevoPaciente($request->all());

				}
				else{
					$pac = Paciente::where("rut", $rut)->first();
				}
			} else {
				if($idPaciente !== ''){
					$pac = Paciente::find($idPaciente);
				}
				else{
					$pac = new Paciente();
					$pac->registrarNuevoPaciente($request->all());
				}
			}
			$idPaciente = $pac->id;
			$tiene = Derivacion::tieneTrasladoExtraSistema($idPaciente);

			if (!$tiene) {
				try {
					$caso = $pac->casoActual()->firstOrFail();
				}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
					$caso = $pac->registrarCasoPaciente($request->all());
					$caso->establecimiento = Session::get("idEstablecimiento");
					$caso->save();
				}
				try{
					$idCaso = $caso->id;
					$caso->liberar("traslado externo");
					/* Cuando el motivo es traslado externo, se libera la cama, pero el caso sigue activo.*/
				}
				catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
					/* Si no tenia historial de cama, no importa. */
				}

				$externa = new DerivacionesExtrasistema;
				$externa->establecimiento_extrasistema = $establecimiento;
				$externa->caso = $idCaso;
				$externa->fecha = DB::raw("date_trunc('seconds', now())");
				$externa->usuario = Auth::user()->id;
				$externa->save();

				return response()->json(array("exito" => "Se ha realizado la derivación extra sistema"));
			}
			else {
				return response()->json(array("error" => "El paciente con run $rut ya posee un traslado extra sistema", "msg" => ""));
			}
		});
		//}catch(Exception $ex){
		//	return response()->json(array("error" => "Error al realizar la derivación extra sistema", "msg" => $ex->getMessage()));
		//}

	}

	public function nuevoEstablecimientoExtrasistema(Request $request){
		$nombre = trim($request->input("estabExterno"));
		if (!empty($nombre) && !EstablecimientosExtrasistema::existeEstablecimiento($nombre)) {
			$estab = new EstablecimientosExtrasistema;
			$estab->nombre = $nombre;
			$estab->save();
			$id = $estab->id;
		}
		else{
			$id = null;
		}
		$ret = Form::select('estabsExterno', EstablecimientosExtrasistema::getEstablecimiento(), $id, array('id' => 'estabsExterno', 'class' => 'form-control'));

		return response()->json(["exito" => $ret]);
	}

	public function validarParaTraslado(Request $request){
		try{
			$pac = Paciente::where("rut", $request->input("rut"))->firstOrFail();
			$caso_actual = $pac->casoActual()->firstOrFail();
			$derivacion = $caso_actual->tieneDerivacion();
			$cama = $caso_actual->tieneCama();
			/* Si se encuentran derivaciones */
			if($derivacion){
				$est_destino = $derivacion->establecimientoDestino()->first();
				$est_origen  = $derivacion->establecimientoOrigen()->first();
				/* Si la derivacion está cerrada: */
				if($derivacion->fecha_cierre){
					/* Si el destino de la derivacion era este establecimiento, si está en cama está válido,
					si no tiene cama significa que fue aceptada su derivacion pero aun no llega. */
					if($est_destino->id == Session::get("idEstablecimiento")){
						if($cama){
							return response()->json([
								"valid" => true
							]);
						}
						else{
							return response()->json([
								"valid" => false,
								"message" => "El paciente tiene ingreso pendiente"
							]);
						}
					}
					/* Si el destino es otro establecimiento y está cerrada quiere decir
					que el paciente está a punto de ser derivado.*/
					elseif($est_origen->id == Session::get("idEstablecimiento")){
						return response()->json([
								"valid" => true
							]);
					}
					/* Si tiene derivacion, pero el destino y el origen no corresponde a nada...*/
					else{
						return response()->json([
							"valid" => false,
							"message" => "El paciente tiene un caso activo en otro establecimiento"
						]);
					}
				}
				else{
					if($est_origen->id == Session::get("idEstablecimiento")){
						return response()->json([
								"valid" => true
							]);
					}
					else{
						return response()->json([
							"valid" => false,
							"message" => "El paciente tiene un caso activo en otro establecimiento"
						]);
					}
				}
			}
			$extrasistema = $caso_actual->tieneDerivacionExtrasistema();
			if($extrasistema){
				$est = $extrasistema->establecimiento()->first();
				if($est->id == Session::get("idEstablecimiento")){
					$msg = "El paciente tiene una derivación pendiente al extra sistema";
				}
				else{
					$msg = "El paciente tiene un caso activo en otro establecimiento";
				}
				return response()->json(array("valid" => false, "message" => $msg));
			}
			if($cama) {
				$est = $cama->establecimiento()->first();
				if($est->id != Session::get("idEstablecimiento")) {
					return response()->json(array("valid" => false, "message" => "El paciente tiene un caso activo en otro establecimiento"));
				}
				else{
					return response()->json(array("valid" => true));
				}
			}
			return response()->json(array("valid" => true));
		}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			return response()->json(array("valid" => true));
		}
	}

	public function validarParaIngreso(){
		try{
			/* @var $pac Paciente
			 * @var $casoActual Caso
			 * @var $cama HistorialOcupacion */

			$pac = Paciente::where("rut", $request->input("rut"))->firstOrFail();
			$casoActual = $pac->casoActual()->firstOrFail();
			$cama = $casoActual->tieneCama();

			/* Debe chequear lista espera, cama, derivacion */

			if($cama) {
				$est = $cama->establecimiento()->first();
				if($est->id != Session::get("idEstablecimiento")) {
					return response()->json(array("valid" => false, "message" => "El paciente tiene un caso activo en otro establecimiento"));
				}
				else{
					return response()->json(array("valid" => false, "message" => "El paciente ya tiene un caso activo en este establecimiento"));
				}
			}
			$derivacion = $casoActual->tieneDerivacion();
			if($derivacion){
				$est = $derivacion->establecimiento()->first();
				if ($est->id == Session::get("idEstablecimiento")) {
					$msg = "El paciente tiene una derivación pendiente. Cancele la derivación primero.";
				} else {
					$msg = "El paciente tiene un caso activo en otro establecimiento";
				}
				return response()->json(array("valid" => false, "message" => $msg));
			}
			return response()->json(array("valid" => true ));
		}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			return response()->json(array("valid" => true));
		}
	}

	public function validarFechaIngreso(Request $request){
		$now = \Carbon\Carbon::now();
		try {
			$fecha_ingreso = \Carbon\Carbon::createFromFormat("d-m-Y H:i", $request->input("fechaIngreso"))->addMinute(2);
		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}

		if($request->input('rut') == ''){
			return response()->json(["valid" => true]);
		}
		try{
			/* @var $pac Paciente
			 * @var $casoActual Caso
			 * @var $cama HistorialOcupacion */

			$pac = Paciente::where("rut", $request->input("rut"))->firstOrFail();
			$casoActual = $pac->casos()->firstOrFail();

			$cama = $casoActual->historialOcupacion()->firstOrFail();

			if(!isset($cama->fecha_liberacion)){
				return response()->json(["valid" => true, "message" => "El paciente tiene un caso activo"]);
			}

			$fecha_liberacion = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cama->fecha_liberacion);
			if($now->diffInDays($fecha_ingreso) > 3){
				return response()->json(["valid" => false, "message" => "La fecha y hora de ingreso no debe superar los 3 días"]);
			}
			if($fecha_ingreso->gt($fecha_liberacion)){
				return response()->json(["valid" => true]);
			}

			else{
				return response()->json(["valid" => false, "message" => "La cama está ocupada para la fecha de ingreso dada. Debe ser superior a {$fecha_liberacion->format("d-m-Y H:i:s")}"]);
			}

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			return response()->json(["valid" => true ]);
		}
	}

	public function reconvertirOriginal(Request $request){
		try{
			$idCama=$request->input("idCama");
			Cama::findOrFail($idCama)->reconvertirOriginal();


			return response()->json(array("exito" => "La cama ha vuelto a su unidad original."));
		}catch(Exception $ex){
			return response()->json(array("error" => "Error al realizar la reconversión", "msg" => $ex->getMessage()));
		}
	}

	public function detallesCasoP($caso,$excell){

        $caso_obj = Caso::find($caso);

			$pac = Paciente::find($caso_obj->paciente);

		$edad = '';
		if($pac->fecha_nacimiento != null){
			$edad=Paciente::edad($pac->fecha_nacimiento);
		}
        try{
			$unidad = $caso_obj->camas()->firstOrFail()->sala()->firstOrFail()->unidadEnEstablecimiento()->firstOrFail();
			$some = $unidad->some !== null;
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			$some = false;
        }

		if($excell == "no"){
				$evoluciones = EvolucionCaso::select("riesgo","fecha","riesgo_id","urgencia","id_complejidad_area_funcional","id_unidad")
				->where("caso", $caso)
				->where("riesgo_id","!=", null)
				->orderBy("fecha", "desc")
				->take(30)
				->get();
		}else{
			$evoluciones = EvolucionCaso::select("riesgo","fecha","riesgo_id","urgencia","id_complejidad_area_funcional","id_unidad")
			->where("caso", $caso)
			->where("riesgo_id","!=", null)
			->orderBy("fecha", "desc")
			->get();
		}
		if($evoluciones && !$evoluciones->isEmpty()){
			$riesgos = Consultas::getRiesgos($evoluciones->first()->riesgo);
		}else{
			$riesgos = Consultas::getRiesgos();
		}

		$dietas = Dieta::where("caso", $caso)
			->orderBy("fecha", "desc")
			->get();
		if($dietas && !$dietas->isEmpty()){
			$opciones_dieta = Dieta::getDietas($dietas->first()->dieta);
		}else{
			$opciones_dieta = Dieta::getDietas();
		}

		$lista_servicios = DB::table('servicios_vista')
						->where('establecimiento', '=', Auth::user()->establecimiento)
						->orderBy('alias')
						->get();

		$servicios = [];
		$atributos = [];
		foreach($lista_servicios as $servicio){
			$servicios[$servicio->id_unidad] =  $servicio->alias;
			$atributos[$servicio->id_unidad] = ["data-toggle" =>"tooltip", "title"=>$servicio->tooltip];
		}

		$evol = [];
		foreach($evoluciones as $evolucion){
			$riesgo = "";
			if($evolucion->riesgo != null){
				$riesgo = ($evolucion->urgencia)?$evolucion->riesgo." (Urgencia)":$evolucion->riesgo;

			}else if($evolucion->riesgo_id != null){
				$riesgo_evo = Riesgo::where("id",$evolucion->riesgo_id)->first()->categoria;

				$riesgo = ($evolucion->urgencia)?$riesgo_evo." (Urgencia)":$riesgo_evo;
			}
			//ubicacion
			if($evolucion->complejidad_area_funcional['servicios']['nombre_servicio'] != null){
				$unidad_ubicacion = $evolucion->complejidad_area_funcional['servicios']['nombre_servicio'];
				$area_ubicacion = $evolucion->complejidad_area_funcional['area']['nombre'];
			}else{
				$unidad_ubicacion = $evolucion->unidad["alias"];
				$area_ubicacion =$evolucion->unidad['area']['nombre'];
			}


			$esp_tmp = "";
			$esp_tmp_excel = "";
			$fecha = Carbon::parse($evolucion->fecha)->format("d-m-Y H:i:s");
			foreach(EvolucionEspecialidad::especialidadesFecha($caso,$fecha) as $especialidad){
				$esp_tmp .= "<h4 style='display: inline-block; margin-right:5px;'><span class='label label-info'>".$especialidad->nombre."</span></h4>";
				$esp_tmp_excel .= $especialidad->nombre.' ';
			}

			$atencion_tmp = "";
			$atencion_tmp_excel = "";
			
			$atencion = EvolucionAtencion::atencionesFecha($caso,$fecha);
			if(!empty($atencion)){
				$atencion_tmp = "<h4 style='display: inline-block; margin-right:5px;'><span class='label label-info'>".$atencion->tipo_atencion."</span></h4>";
				$atencion_tmp_excel = $atencion->tipo_atencion.' ';
			}
		
			$acompañamiento_tmp = "";
			$acompañamiento_tmp_excell = "";
			if($edad <= 15 || $edad == ''){
				$acompañamiento =EvolucionAcompanamiento::acompanamientosFecha($caso,$fecha);
				if(!empty($acompañamiento)){
					$acompañamiento_tmp = "<h4 style='display: inline-block; margin-right:5px;'><span class='label label-info'>".$acompañamiento->tipo_acompanamiento."</span></h4>";
					$acompañamiento_tmp_excell = $acompañamiento->tipo_acompanamiento.' ';
				}
			}  

			if($riesgo != ""){

				$evol [] = [
					"riesgo" => $riesgo,
					"fecha" => $fecha,
					"riesgo_id" => $evolucion->riesgo_id,
					"servicios" => $unidad_ubicacion,
					"area" => $area_ubicacion,
					"especialidades" => $esp_tmp,
					"especialidades_excel" => $esp_tmp_excel,
					"atencion" => $atencion_tmp,
					"atencion_excel" => $atencion_tmp_excel,
					"acompañamiento" => $acompañamiento_tmp,
					"acompañamiento_excel" => $acompañamiento_tmp_excell
				];
				
				
			}
		}

		$inicio = EvolucionEspecialidad::fechaInicioEspecialidad($caso);
		$fin = EvolucionEspecialidad::fechaFinEspecialidad($caso);

		$esp = [];
		if($inicio != "false"){
			for($inicio; $inicio <= $fin;$inicio->addDay()){
				$esp_tmp = "";
				foreach(EvolucionEspecialidad::especialidadesFecha($caso,$inicio) as $especialidad){
					$esp_tmp .= "<h4 style='display: inline-block; margin-right:5px;'><span class='label label-info'>".$especialidad->nombre."</span></h4>";
				}
				$esp [] = [
					"fecha" => Carbon::parse($inicio)->format("d-m-Y"),
					"especialidades" => $esp_tmp
				];
			}
		}


	

		return	[
			"dietas" => $dietas,
			"evoluciones" => $evol,
			"especialidades" => $esp,
			"riesgos" => $riesgos,
			"opciones_dieta" => $opciones_dieta,
			"some" => $some,
			"servicios" => $servicios,
			"atributos"=> $atributos,
			"caso" => $caso,
			"unidad" => $unidad,
			"lista_especialidades" => Especialidades::pluck('nombre','id'),
			"edad"=>$edad
		] ;


	}

	public function detallesCaso(Request $request,$caso = null){
		if(!$caso) {
			$caso = $request->input("caso");
		}
		$excell="no";
		$resp= $this->detallesCasoP($caso, $excell);
		$resp = View::make("Gestion/VerEvolucion", $resp)->render();
		Session::put("caso_a_actualizar", $caso);
		return response()->json(["contenido"=>$resp]);
	}

	public function descargarExceldetallesCaso($caso, $unidad){

		$excell="si";

		$datos = [];
		$informacion = $this->detallesCasoP($caso, $excell);
		
		foreach ($informacion['evoluciones'] as $unidad => $info){
			$fecha = $info['fecha'];
			$riesgos = $info['riesgo'];
			$servicios = $info['servicios'];
			$areas = $info['area'];
			$atencion = $info['atencion_excel'];
			$especialidades = $info['especialidades_excel'];
			$acompañamiento = $info['acompañamiento_excel'];

			$datos[] = [
				$fecha,
				$riesgos,
				$servicios,
				$areas,
				$atencion,
				$especialidades,
				$acompañamiento
			];

		
		}

		try {
			$html = [
				"informacion" => $datos,
				"edad"=> $informacion['edad']
			];

			Excel::create('Evolución_riesgo_dependencia', function ($excel) use ($html){
				$excel->sheet('Evolución_riesgo_dependencia', function ($sheet) use ($html){

					$sheet->mergeCells('A1:E1');
					$sheet->setAutoSize(true);

					$sheet->setHeight(1, 50);
					$sheet->row(1, function ($row) {

						// call cell manipulation methods
						$row->setBackground('#1E9966');
						$row->setFontColor("#FFFFFF");
						$row->setAlignment("center");

					});

					$sheet->loadView('ExceldetallesCaso', ["html" => $html]);
				});
			})->download('xls');
		}catch (Exception $e) {
		 	return response()->json($e->getMessage());
		}
	}

	public function detallesCasoHospDom(Request $request, $caso = null){
		if(!$caso) {
			$caso = $request->input("caso");
		}

		$evoluciones = EvolucionCaso::select("riesgo","fecha","riesgo_id","urgencia","id_complejidad_area_funcional")
		->where("caso", $caso)
		->orderBy("fecha", "desc")
		->get();

		if($evoluciones && !$evoluciones->isEmpty()){
			$riesgos = Consultas::getRiesgos($evoluciones->first()->riesgo);
		}
		else{
			$riesgos = Consultas::getRiesgos();
		}

		$evol = [];
		foreach($evoluciones as $evolucion){
			$riesgo = "";
			if($evolucion->riesgo != null){
				$riesgo = ($evolucion->urgencia == true)?$evolucion->riesgo." (Urgencia)":$evolucion->riesgo;

			}else if($evolucion->riesgo_id != null){
				$riesgo_evo = Riesgo::where("id",$evolucion->riesgo_id)->first()->categoria;

				$riesgo = ($evolucion->urgencia == true)?$riesgo_evo." (Urgencia)":$riesgo_evo;
			}

			if($riesgo != ""){
				$evol [] = [
					"riesgo" => $riesgo,
					"fecha" => Carbon::parse($evolucion->fecha)->format("d-m-Y H:i:s"),
					"riesgo_id" => $evolucion->riesgo_id
				];
			}
		}

		$resp = View::make("Gestion/VerEvolucionHospDom", [
			"evoluciones" => $evol,
			"riesgos" => $riesgos,
			"caso" => $caso,
		] )->render();

		Session::put("caso_a_actualizar", $caso);

		return response()->json(array("contenido" => $resp));
	}

	public function detallesCasoDieta(Request $request,$caso = null){

		if(!$caso) {
			$caso = $request->input("caso");
		}
        /* @var $caso_obj Caso */
        $caso_obj = Caso::find($caso);

        try{
            $unidad = $caso_obj->camas()->firstOrFail()->sala()->firstOrFail()->unidadEnEstablecimiento()->firstOrFail();
            $some = $unidad->some !== null;
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $some = false;
        }


		$evoluciones = EvolucionCaso::where("caso", $caso)
		->orderBy("fecha", "desc")
		->get();


		if($evoluciones && !$evoluciones->isEmpty()){
			$riesgos = Consultas::getRiesgos($evoluciones->first()->riesgo);
		}
		else{
			$riesgos = Consultas::getRiesgos();
		}

		$dietas = Dieta::where("caso", $caso)
		->orderBy("fecha", "desc")
		->get();
		if($dietas && !$dietas->isEmpty()){
			$opciones_dieta = Dieta::getDietas($dietas->first()->dieta);
		}
		else{
			$opciones_dieta = Dieta::getDietas();
		}

		$resp = View::make("Gestion/VerEvolucionDieta", [
			"dietas" => $dietas,
			"evoluciones" => $evoluciones,
			"riesgos" => $riesgos,
			"opciones_dieta" => $opciones_dieta,
			"some" => $some,
			"caso" => $caso
		] )->render();
		Session::put("caso_a_actualizar", $caso);

		return response()->json(array("contenido" => $resp) );
	}

    public function diagnosticosCaso(Request $request,$caso = null ){
        if(!$caso) {
            $caso = $request->input("caso");
        }
        /* @var $caso_obj Caso */
        $caso_obj = Caso::find($caso);

		//validaciones
		if($request->ubicacion != 'mapa_de_camas' && $request->ubicacion != 'hosp_dom'){
			$respuesta = Consultas::puedeHacer($caso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
		}
		//validaciones

        try{
            $unidad = $caso_obj->camas()->firstOrFail()->sala()->firstOrFail()->unidadEnEstablecimiento()->firstOrFail();
            $some = $unidad->some !== null;
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $some = false;
        }

        $diagnosticos = HistorialDiagnostico::where("caso", $caso)
            ->orderBy("fecha", "desc")
            ->get();

        $resp = View::make("Gestion/VerDiagnosticos", [
            //"dietas" => $dietas,
            "diagnosticos" => $diagnosticos,
            //"riesgos" => $riesgos,
            //"opciones_dieta" => $opciones_dieta,
            "caso_obj" => $caso_obj,
			"some" => $some,
			"caso" => $caso
        ] )->render();
        Session::put("caso_a_actualizar", $caso);
        Session::put("caso_a_actualizar_obj", $caso);

        return response()->json(array("contenido" => $resp) );
    }

    public function quitarDiagnostico(Request $request){
        $idDiag = $request->input("diagnostico");
    }

    public function ingresarDiagnostico(Request $request){
		$caso = Session::get("caso_a_actualizar");
		$caso_vista = $request->caso;


		$diagnosticos = $request->input("diagnosticos");
        $hidden_diagnosticos = $request->input("hidden_diagnosticos");
		$fecha_ingreso = \Carbon\Carbon::now();
		$comentario_diagnostico = $request->input("nuevo-diagnostico");

		$r=DB::table(DB::raw("(SELECT * FROM diagnosticos
		WHERE caso=$caso_vista
		AND fecha::DATE=CURRENT_DATE)AS a
		order by fecha desc"))->first();

		if($r && $request->motivo == ''){
			return response()->json(array("error"=>"Ya se ha guardado un diagnóstico hoy, debe ingresar el motivo"));
		}

		foreach ($diagnosticos as $key => $value) {
			if($value != "null" ){


				$d = new HistorialDiagnostico();
				$d->caso = $caso;
				$d->fecha =$fecha_ingreso;
				$d->diagnostico = $value;
				$d->id_cie_10 = $hidden_diagnosticos[$key];
				$d->id_usuario = Auth::user()->id;
				$d->comentario = $comentario_diagnostico[$key];
				$d->save();
			}
		}



        return $this->diagnosticosCaso($request, $caso);
    }

    public function cambiarCasoSocial(Request $request){
        return DB::transaction(function() use ($request) {
            $caso_social = trim($request->input("caso_social"));
            $caso = Session::get("caso_a_actualizar");
            $caso_obj = Caso::findOrFail($caso);
            ob_start();
            var_dump($caso_social === 'si');
            $f = ob_get_clean();
            $caso_obj->caso_social = ($caso_social === 'si');
            $caso_obj->save();
            return response()->json(["caso_social" => $caso_obj->caso_social ]);
        });

    }

	public function cambiarRiesgo(Request $request){
	
		$nuevo_riesgo = trim($request->categoria);
		$caso = Session::get("caso_a_actualizar");
		$comentario_riesgo = trim($request->comentario_riesgo);

		if($nuevo_riesgo !== '' && $nuevo_riesgo !== null && $nuevo_riesgo !== 0 && $nuevo_riesgo !== '0') {

			if($nuevo_riesgo == 'D2' || $nuevo_riesgo == 'D3'){
				if($comentario_riesgo == ''){
					return response()->json(array("error"=>"Falta agregar el comentario de riesgo obligatorio para D2 y D3", "tipo" => "100"));
				}
			}
			//busca la ultima evolucion de riesgo
			$r = DB::table("t_evolucion_casos as tec")
				->select("r.id","tec.id as tec_id")
				->join("riesgos as r","r.id","=","tec.riesgo_id")
				->where("tec.caso",$caso)
				->where("tec.fecha",">=",Carbon::now()->startOfDay()->format("Y-m-d H:i:s"))
				->orderBy("tec.id","desc")
				->first();

			$riesgo=null;
			if($r)
			{
				//////////////////////////////////////////////////////
				//Falta validar el motivo que no lo toma ne la vista//
				//////////////////////////////////////////////////////
				if($request->motivo == ''){
					return response()->json(array("error"=>"Ya se ha guardado una categoría hoy, debe ingresar el motivo", "tipo" => "101"));
				}else{
					//correccion de riesgo
					$riesgo=Riesgo::find($r->id);
					$ev=EvolucionCaso::find($r->tec_id);
					$ev->motivo = $request->motivo;

					EvolucionEspecialidad::correccionEspecialidad($caso);
					EvolucionAtencion::correccionAtencion($caso);
					EvolucionAcompanamiento::correccionAcompanamiento($caso);
				}
			}
			else
			{
				$riesgo= new Riesgo;
				$ev=new EvolucionCaso();
			}

			if($request->input("servicios") == 'saludmentalapace' || $request->input("servicios") == 'saludmentalapiace'){
				$riesgo->dependencia1 = $request->dependencias2[0];
				$riesgo->dependencia2 = $request->dependencias2[1];
				$riesgo->dependencia3 = $request->dependencias2[2];
				$riesgo->dependencia4 = $request->dependencias2[3];
				$riesgo->dependencia5 = $request->dependencias2[4];
				$riesgo->riesgo1 = $request->riesgos2[0];
				$riesgo->riesgo2 = $request->riesgos2[1];
				$riesgo->riesgo3 = $request->riesgos2[2];
				$riesgo->riesgo4 = $request->riesgos2[3];
				$riesgo->riesgo5 = $request->riesgos2[4];
				$riesgo->riesgo6 = $request->riesgos2[5];
				$riesgo->riesgo7 = $request->riesgos2[6];
				$riesgo->riesgo8 = $request->riesgos2[7];
				$riesgo->riesgo9 = $request->riesgos2[8];
				$riesgo->categoria = $nuevo_riesgo;
				$riesgo->save();
			}
			else{
				$riesgo->dependencia1 = $request->dependencias[0];
				$riesgo->dependencia2 = $request->dependencias[1];
				$riesgo->dependencia3 = $request->dependencias[2];
				$riesgo->dependencia4 = $request->dependencias[3];
				$riesgo->dependencia5 = $request->dependencias[4];
				$riesgo->dependencia6 = $request->dependencias[5];
				$riesgo->riesgo1 = $request->riesgos[0];
				$riesgo->riesgo2 = $request->riesgos[1];
				$riesgo->riesgo3 = $request->riesgos[2];
				$riesgo->riesgo4 = $request->riesgos[3];
				$riesgo->riesgo5 = $request->riesgos[4];
				$riesgo->riesgo6 = $request->riesgos[5];
				$riesgo->riesgo7 = $request->riesgos[6];
				$riesgo->riesgo8 = $request->riesgos[7];
				$riesgo->categoria = $nuevo_riesgo;
				$riesgo->save();
			}

			$ev->caso = $caso;
			$ev->fecha = date("Y-m-d H:i:s");
			$ev->riesgo = $nuevo_riesgo;
			$ev->riesgo_id = $riesgo->id;
			$ev->id_usuario = Session::get("usuario")->id;
			$ev->comentario = $comentario_riesgo;

			//ver ubicacion del paciente y añadir servicio en el que seencuentra
			$pacienteUbicacion = THistorialOcupaciones::select("u.id")
				->join("camas as c","c.id","t_historial_ocupaciones.cama")
				->join("salas as s","s.id","c.sala")
				->join("unidades_en_establecimientos as u","u.id","s.establecimiento")
				->where("caso", $caso)
				->whereNull("fecha_liberacion")
				->first();

			if($pacienteUbicacion){
				$ev->id_unidad = $pacienteUbicacion->id;//ide de la unidad en que se encuentra actualmente el paciente
			}else{
				$ev->id_unidad = null;
			}

			if(Session::get("usuario")->tipo == TipoUsuario::USUARIO){
				$ev->urgencia = true;
			}
			else{
				$ev->urgencia = false;
			}

			$ev->save();


			//actualizar atencion , acompañamiento y especialidad 
			if($request->categoria_atencion && $request->categoria_atencion != ''){
				EvolucionAtencion::agregarAtenciones($caso,$request->categoria_atencion);
			}

			if($request->categoria_acompañamiento  && $request->categoria_acompañamiento != ''){
				EvolucionAcompanamiento::agregarAcompanamientoes($caso,$request->categoria_acompañamiento);
			}

			if($request->especialidad){
				EvolucionEspecialidad::agregarEspecialidades($caso,$request->especialidad);
			}


			return response()->json($ev);
		}

		return $this->detallesCaso($request, $caso);
	}

	public function riesgoActual(Request $request){
		$riesgo = DB::table('casos')
					->select('casos.id', 't_evolucion_casos.riesgo', 't_evolucion_casos.fecha','riesgos.riesgo1','riesgos.riesgo2','riesgos.riesgo3','riesgos.riesgo4','riesgos.riesgo5','riesgos.riesgo6','riesgos.riesgo7','riesgos.riesgo8','riesgos.dependencia1','riesgos.dependencia2','riesgos.dependencia3','riesgos.dependencia4','riesgos.dependencia5','riesgos.dependencia6', "p.nombre", 'p.apellido_paterno', 'p.apellido_materno',"t_evolucion_casos.id_unidad")
					->join('t_evolucion_casos', 't_evolucion_casos.caso', '=', 'casos.id')
					->leftjoin('riesgos', 'riesgos.id', '=', 't_evolucion_casos.riesgo_id')
					->leftjoin("pacientes as p", 'p.id', '=', 'casos.paciente')
					->where('casos.id', $request->caso)
					->orderBy('t_evolucion_casos.fecha', 'desc')
					->first();

		return response()->json($riesgo);
	}

	public function nuevoRiesgoActual(Request $request){
		$riesgo = DB::table('hospitalizacion_domiciliaria as l')
				->select(DB::Raw('max(e.id) as idevolucion'), 'c.id as idcaso')
				->leftjoin("casos as c", 'l.caso', '=', 'c.id')
				->leftjoin('t_evolucion_casos as e', 'e.caso', '=', 'c.id')
				->leftjoin('riesgos as r', 'e.riesgo_id', '=', 'r.id')
				->where('c.id', $request->caso)
				->whereNull('l.fecha_termino')
				->whereNotNull('e.riesgo_id')
				->groupBy('c.id')
				->first();

		$categoria = "";
		if(isset($riesgo) && $riesgo->idevolucion != NULL){
			$categoria_n = EvolucionCaso::select("categoria", "fecha")
			->join("riesgos as r", "r.id", "=", "t_evolucion_casos.riesgo_id")
			->where("t_evolucion_casos.id", "=", $riesgo->idevolucion)->first();
			$categoria = (isset($categoria_n))?$categoria_n->categoria:"";
		}
		return response()->json($categoria);
	}

	public function cambiarDieta(Request $request){
		$caso = Session::get("caso_a_actualizar");

		$d=DB::table(DB::raw("(SELECT id FROM t_dietas_pacientes AS tdp
WHERE tdp.caso=$caso
AND fecha::DATE=CURRENT_DATE)AS a"))->first();
		$dt=null;
		if($d)
		{
			$dt=Dieta::find($d->id);
		}
		else{
			$dt = new Dieta;
		}

		$dt->caso = $caso;
		$dt->fecha = date("Y-m-d H:i:s");
		$dt->dieta = $request->input("nueva-dieta");
		$dt->save();
		return $this->detallesCasoDieta($request, $caso);
	}

	public function exportar($unidad){
		$this->unidad=$unidad;
		Excel::create('Camas', function($excel) {
			$excel->sheet('Camas', function($sheet) {

				$sheet->mergeCells('A1:P1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});
				#
				$camas=json_decode($this->obtenerCamasLista($this->unidad)->getContent());

				//modo pedido para censo de camas
                $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

                //resumen camas segun informe censo de camas
                $camas_censo= [];
                $area = "TODAS";
                $unidad = "TODAS";
                $fecha = Carbon::now()->format("d/m/Y H:m");
                if($this->unidad != "TODOS"){
                    //lista de pacientes que estan utilizando las camas
                    $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
                    $area = AreaFuncional::areaUnidad($this->unidad);
                    $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
                }


                $sheet->loadView('Gestion.ListaCamas', [
					"camas" => $camas,
					"camas_resumen" => $camas_resumen,
					"camas_censo" => $camas_censo,
					"fecha" => $fecha,
					"area" => $area ,
					"unidad" => $unidad
				]);
			});
		})->download('xls');

	}

	public function exportarpacientesUrgencias(){

		$unidad = "urgencia";
		$this->unidad=$unidad;

		Excel::create('Pacientes', function($excel) {
			$excel->sheet('Pacientes', function($sheet) {

				$sheet->mergeCells('A1:E1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});
				#
				$camas=json_decode($this->obtenerCamasLista($this->unidad)->getContent());

				//modo pedido para censo de camas
                $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

                //resumen camas segun informe censo de camas
                $camas_censo= [];
                $area = "TODAS";
                $unidad = "TODAS";
                $fecha = Carbon::now()->format("d/m/Y H:m");
				if($this->unidad != "TODOS"){
                    //lista de pacientes que estan utilizando las camas
                    $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
                    $area = AreaFuncional::areaUnidad($this->unidad);
                    $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
                }


                $sheet->loadView('Gestion.ListaPacientes', ["camas" => $camas, "camas_resumen" => $camas_resumen, "camas_censo" => $camas_censo, "fecha" => $fecha, "area" => $area , "unidad" => $unidad]);
			});
		})->download('xls');

	}


	public function exportarpacientes($unidad){

		$this->unidad=$unidad;

		Excel::create('Pacientes', function($excel) {
			$excel->sheet('Pacientes', function($sheet) {

				$sheet->mergeCells('A1:E1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});
				#
				$camas=json_decode($this->obtenerCamasLista($this->unidad)->getContent());

				//modo pedido para censo de camas
                $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

                //resumen camas segun informe censo de camas
                $camas_censo= [];
                $area = "TODAS";
                $unidad = "TODAS";
                $fecha = Carbon::now()->format("d/m/Y H:m");
				if($this->unidad != "TODOS"){
                    //lista de pacientes que estan utilizando las camas
                    $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
                    $area = AreaFuncional::areaUnidad($this->unidad);
                    $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
                }


                $sheet->loadView('Gestion.ListaPacientes', ["camas" => $camas, "camas_resumen" => $camas_resumen, "camas_censo" => $camas_censo, "fecha" => $fecha, "area" => $area , "unidad" => $unidad]);
			});
		})->download('xls');

	}

	public function exportarPdf($unidad)
    {
		$this->unidad = $unidad;

        try {
            //modo antiguo de sigicam exportar excel de mapa de camas
            $camas = json_decode($this->obtenerCamasLista($this->unidad)->getContent());

            //modo pedido para censo de camas
            $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

            //resumen camas segun informe censo de camas
            $camas_censo= [];
            $hospital = Establecimiento::getNombre(Auth::user()->establecimiento);

            $fecha = Carbon::now()->format("d/m/Y H:m");
            $fecha_titulo = Carbon::now()->format("dmY_H_i");
            $area = [];
            $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
            $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
            $area = AreaFuncional::areaUnidad($this->unidad);


            $html = PDF::loadView("Gestion/ListaCamasPdf", [
                "camas" => $camas,
                "camas_resumen" => $camas_resumen,
                "camas_censo" => $camas_censo,
                "fecha" => $fecha,
                "area" => $area ,
                "unidad" => $unidad,
                "hospital" => $hospital
            ]);
			return $html->setPaper('legal', 'landscape')->download('censo'.$fecha_titulo.'.pdf');
        } catch (Exception $e) {
           return response()->json($e->getMessage());
        }

	}

	public function exportarpacientesUrgenciasPdf()
    {
		$unidad = "urgencia";
        $this->unidad = $unidad;
        try {
            //modo antiguo de sigicam exportar excel de mapa de camas
            $camas = json_decode($this->obtenerCamasLista($this->unidad)->getContent());

            //modo pedido para censo de camas
            $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

            //resumen camas segun informe censo de camas
            $camas_censo= [];
            $hospital = Establecimiento::getNombre(Auth::user()->establecimiento);

            $fecha = Carbon::now()->format("d/m/Y H:m");
            $area = [];
            $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
            $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
            $area = AreaFuncional::areaUnidad($this->unidad);


            $pdf = \Barryvdh\DomPDF\Facade::loadView("Gestion/ListaPacientesPdf", [
                "camas" => $camas,
                "camas_resumen" => $camas_resumen,
                "camas_censo" => $camas_censo,
                "fecha" => $fecha,
                "area" => $area ,
                "unidad" => $unidad,
                "hospital" => $hospital
            ]);

			return $pdf->setPaper('legal', 'portrait')->download('Pacientes Urgencia.pdf');
		} catch (Exception $e) {
			return response()->json($e->getMessage());
		}
	}

	public function exportarpacientesPdf($unidad)
    {
        $this->unidad = $unidad;
        try {
            //modo antiguo de sigicam exportar excel de mapa de camas
            $camas = json_decode($this->obtenerCamasLista($this->unidad)->getContent());

            //modo pedido para censo de camas
            $camas_resumen = json_decode($this->obtenerCamasCenso($this->unidad)->getContent());

            //resumen camas segun informe censo de camas
            $camas_censo= [];
            $hospital = Establecimiento::getNombre(Auth::user()->establecimiento);

            $fecha = Carbon::now()->format("d/m/Y H:m");
            $area = [];
            $unidad = UnidadEnEstablecimiento::nombreUnidad($this->unidad);
            $camas_censo = json_decode($this->obtenerUsoCamasCenso($this->unidad)->getContent());
            $area = AreaFuncional::areaUnidad($this->unidad);


            $pdf = \Barryvdh\DomPDF\Facade::loadView("Gestion/ListaPacientesPdf", [
                "camas" => $camas,
                "camas_resumen" => $camas_resumen,
                "camas_censo" => $camas_censo,
                "fecha" => $fecha,
                "area" => $area ,
                "unidad" => $unidad,
                "hospital" => $hospital
            ]);

			return $pdf->setPaper('legal', 'portrait')->download('Pacientes Urgencia.pdf');
		} catch (Exception $e) {
			return response()->json($e->getMessage());
		}
	}

	public function infoListaEspera(){
		$idEst = Auth::user()->establecimiento;
		$datos = DB::table("lista_espera as l")
		->join("casos as c", "c.id", "=", "l.caso")
		->join("pacientes as p", "p.id", "=", "c.paciente")
		->join("usuarios as u", "l.usuario", "=", "u.id")
		->join("procedencias as pro", "pro.id", "=", "c.procedencia")
		->whereNull("l.fecha_termino")
		->where("u.establecimiento", $idEst)
		->where("c.procedencia","1")
		->select("p.nombre as nombre",
			"p.apellido_paterno as apellidoP",
			"p.apellido_materno as apellidoM",
			"p.rut as rut",
			"p.dv as dv",
			"l.fecha as fecha",
			"c.indicacion_hospitalizacion",
			"pro.nombre as procedencia",
			"c.detalle_procedencia as subprocedencia",
			"c.fecha_ingreso2 as solicitud",
			"c.motivo_hospitalizacion")->get();
		$response = [];

		foreach($datos as $dato){
			$rut = $dato->rut;
			$rut .= ($dato->dv == 10)?"-K":"-".$dato->dv;

			$fecha=date("d-m-Y H:i", strtotime($dato->solicitud));

			$hoy = Carbon::now();
			$fechaCarbon = Carbon::parse($fecha);
			$diff = $hoy->diffInHours($fechaCarbon);

			$response [] = [
				$dato->nombre." ".$dato->apellidoP." ".$dato->apellidoM,
				$rut,
				$diff
			];
		}
		return $response;
	}

	public function exportarExcelListaEspera()
    {

		Excel::create('Camas', function($excel) {
			$excel->sheet('Camas', function($sheet) {

				$sheet->mergeCells('A1:P1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});
				#
				$idEst = Auth::user()->establecimiento;
				$establecimiento = Establecimiento::where("id",$idEst)->first();
				$response = $this->infoListaEspera();
                $hoy = Carbon::now();

                $sheet->loadView('Gestion.ReporteListaEsperaExcel', [
					"datos" => $response,
					"hospital" => $establecimiento->nombre,
					"fecha" => $hoy->format("d/m/Y")
					]
				);
			});
		})->download('xls');

	}

	public function exportarPdfListaEspera()
    {
		$idEst = Auth::user()->establecimiento;
		$establecimiento = Establecimiento::where("id",$idEst)->first();
        try {
			$response = $this->infoListaEspera();
			$hoy = Carbon::now();

            $html = PDF::loadView("Gestion/ReporteListaEsperaPdf", [
				"datos" => $response,
				"hospital" => $establecimiento->nombre,
				"fecha" => $hoy->format("d/m/Y")
            ]);

			return $html->setPaper('legal', 'portrait')->stream('f.pdf');
        } catch (Exception $e) {
           return response()->json($e->getMessage());
        }

    }

	public function validarTraslado(Request $request){

		$idCaso=$request->input("idCaso");
		$idPaciente=Caso::find($idCaso)->paciente;
		$rut=Paciente::find($idPaciente)->rut;
		$pac = Paciente::where("rut", $rut)->firstOrFail();
		try{
            $caso_actual = $pac->casoActual()->firstOrFail();
        }
        catch(Exception $e){
            return response()->json([]);
        }
		$derivacion = $caso_actual->tieneDerivacion();

		if($derivacion){
			$est_origen  = $derivacion->establecimientoOrigen()->first();
			if($derivacion->fecha_cierre){
				if($est_origen->id == Session::get("idEstablecimiento")){
					return response()->json(["error" => "El paciente tiene una derivación pendiente"]);
				}
			}
			else{
				if($est_origen->id == Session::get("idEstablecimiento")){
					return response()->json(["error" => "El paciente tiene una derivación pendiente"]);
				}
			}
		}

		return response()->json([]);
	}

	public function validarTraslado2(Request $request){
		$idCaso=$request->input("idCaso");
		$idPaciente=Caso::find($idCaso)->paciente;
		$rut=Paciente::find($idPaciente)->rut;
		$pac = Paciente::where("rut", $rut)->firstOrFail();
		try{
            $caso_actual = $pac->casoActual()->firstOrFail();
        }
        catch(Exception $e){
            return response()->json([]);
        }
		$caso_actual->tieneDerivacion();
		$caso_actual->tieneCama();

		$infeccion2=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
         			))->get();

		if(count($infeccion2))
		{
			return response()->json(["error" => "El paciente ya registra Infecciones Intrahospitalaria, vaya a la sección ver/editar infección para modificaciones."]);
		}

		return response()->json([]);
	}

	public function validarTraslado3(Request $request){
		$idCaso=$request->input("idCaso");
		$idPaciente=Caso::find($idCaso)->paciente;
		$rut=Paciente::find($idPaciente)->rut;
		$pac = Paciente::where("rut", $rut)->firstOrFail();
		try{
            $caso_actual = $pac->casoActual()->firstOrFail();
        }
        catch(Exception $e){
            return response()->json([]);
        }

		$caso_actual->tieneDerivacion();
		$caso_actual->tieneCama();

		$infeccion2=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
         			))->get();

		if(count($infeccion2)==0)
		{
			return response()->json(["error" => "El paciente no registra Infecciones Intrahospitalarias"]);
		}

		return response()->json([]);
	}

	public function validarTraslado4(Request $request){
		$idCaso=$request->input("idCaso");

		$caso_paciente=Caso::find($idCaso);
		$rut="";
		$idPaciente=null;


		try{
			if($caso_paciente)
			{
				$idPaciente=$caso_paciente->paciente;
			}
			if($idPaciente)
			{
				$p=Paciente::find($idPaciente);
				if($p)
				{
					$rut=$p->rut;
				}

			}

			$pac = Paciente::where("rut", $rut)->firstOrFail();
            $caso_actual = $pac->casoActual()->firstOrFail();
        }
        catch(Exception $e){
            return response()->json([]);
        }

		$derivacion = $caso_actual->tieneDerivacion();
		$cama = $caso_actual->tieneCama();

		$infeccion2=DB::table( DB::raw("(select c.id from casos as c,infecciones as i where c.id=i.caso and c.id=$idCaso and i.caso=$idCaso and i.fecha_termino is null) as re"
         			))->get();

		return response()->json([]);
	}



	public function selectorCategorizacion(Request $request){
		$desde = $request->input("inicio");
		$hasta = $request->input("fin");

		$desde = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $desde);
		$hasta = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $hasta);
		$now = \Carbon\Carbon::now();
		$hastaDiffNow = $now->diffInMinutes($hasta);
		$hastaDiffDesde = $hasta->diffInDays($desde);
		return response()->json([View::make("Gestion/Categorizacion", ["dias" => $hastaDiffDesde, "desde" => $desde])->render()]);
		if($hastaDiffNow > 30){
			/* Si han pasado 30 minutos desde que se puso la hora y se hizo el envio*/
			return response()->json([]);
		}
		if($hastaDiffDesde > 3){
			return response()->json([]);
		}

		for($i = 0; $i < $hastaDiffDesde; $i++ ){
			$f = $desde->copy()->addDay($i)->startOfDay();
			Session::flash("cat-$i", $f);
		}

	}

	public function especificarProcedencia(Request $request){
		$end = "</div></div>";
		$row = '<div class="form-group col-md-12 ocultar"><label for="procedencia" class="col-sm-2 control-label">Especificar origen: </label><div class="col-sm-10" id="input_procedencia">';
		$tipo = $request->input("tipo-procedencia");

		if
		($tipo == 1){
			$row = Form::hidden("input_procedencia", "Servicio de urgencia", ["class" => "form-control"]);
		}
		elseif
		($tipo == 3){
			$row.= Form::input("text", "input_procedencia", null, ["class" => "form-control"]).$end;
		}
		elseif
		($tipo == 4){
			$row.= Form::input("text", "input_procedencia", null, ["class" => "form-control"]).$end;
		}
		elseif
		($tipo == 5){
			$row= "";
		}
		elseif
		($tipo == 6){
			$row= "";
		}
		elseif
		($tipo == 8){
			$row= "";
		}
		elseif
		($tipo == 0){
			$row = "";
		}

		return response()->json(["data" => $row]);
	}

	public function especificarAlta(Request $request){
		$tipo = $request->input("tipo-alta");
		$end = "</div>";
		$row = '<label for="otroMotivoBloqueo" class="col-sm-2 control-label">Especifique: </label>
						<div class="col-sm-10">';

		if(		$tipo == "otro" ||
				$tipo == "Otro" ||
				$tipo == "traslado extrasistema" ||
				$tipo == "derivacion otra institucion"){
			$row.= Form::textarea("input-alta", null, ["class" => "form-control", 'rows' => 2]).$end;
		}
		elseif( $tipo == "derivación" ||
				$tipo == 1){
			$row.= Form::select("input-procedencia", [0 => "Seleccione establecimiento"] + Establecimiento::getEstablecimientosSinTodos(), 0, ["class" => "form-control"]).$end;
		}elseif( $tipo == "traslado extra sistema" ||
				 $tipo == 2){
			$row.= Form::select("input-procedencia", [0 => "Seleccione establecimiento"] + EstablecimientosExtrasistema::getEstablecimiento(), 0, ["class" => "form-control"]).$end;
		}else{
			$row = "";
		}


		return response()->json(["data" => $row]);
	}

	//establecimientos
	public function consulta_establecimientos($palabra)
	{
		$datos = DB::select(DB::raw(
		"select
        e.id as id_establecimiento,
        e.nombre as nombre_establecimiento,
        r.nombre_region as region_nombre,
        e.id_region as region_establecimiento
        from establecimientos as e
        left join region as r on e.id_region=r.id_region
        where e.nombre ilike '%".$palabra."%'
        or r.nombre_region ILIKE '%".$palabra."%'
        order by e.id asc, r.id_region asc"
		));
		return response()->json($datos);
	}
	
	public function consulta_establecimientos_privados($palabra)
	{
		$datos = DB::select(DB::raw(
			"select
        e.id as id_establecimiento,
        e.nombre as nombre_establecimiento,
        r.nombre_region as region_nombre,
        e.id_region as region_establecimiento
        from establecimientos_extrasistema as e
        left join region as r on e.id_region=r.id_region
        where e.nombre ilike '%".$palabra."%'
        or r.nombre_region ILIKE '%".$palabra."%'
        order by e.id asc, r.id_region asc"
			));
		return response()->json($datos);
	}

	public function consulta_extrasistema($palabra)
	{
		$datos = DB::select(DB::raw(
		"select
        e.id as id_establecimiento,
        e.nombre as nombre_establecimiento,
        r.nombre_region as region_nombre,
        e.id_region as region_establecimiento
        from establecimientos_extrasistema as e
        left join region as r on e.id_region=r.id_region
        where e.nombre ilike '%".$palabra."%'
        or r.nombre_region ILIKE '%".$palabra."%'
        order by e.id asc, r.id_region asc"
		));
		return response()->json($datos);
	}


	//establecimientos


	public function intercambiar(Request $request){
		$idCaso = $request->input("idCaso");
		$idCasoOriginal = $request->input("idCasoOriginal");
		$casoOriginal = Caso::findOrFail($idCasoOriginal);
		$caso = Caso::findOrFail($idCaso);
		$cama = $casoOriginal->intercambiarCama($caso);
		return response()->json(["exito" => "Se ha intercambiado al paciente con la cama {$cama->id_cama}"]);
	}

	public function gestionIaas(){
		return View::make("Gestion/GestionIaas");
	}

	public function agregarLocalizacion(Request $request){
		$nombre = $request->input("localizacion");
		DB::table('localizacion_infeccion')->insert( ['nombre' => $nombre]);

		return response()->json(["data" => "Localización registrada"]);
	}

	public function agregarInvasivo(Request $request){
		$nombre = $request->input("invasivo");
		DB::table('procedimiento_invasivo')->insert( ['nombre' => $nombre]);

		return response()->json(["data" => "Procedimiento invasivo registrado"]);
	}

	public function agregarEtiologia(Request $request){
		$nombre = $request->input("etiologico1");
		DB::table('agente_etiologico')->insert( ['nombre' => $nombre]);

		return response()->json(["data" => "Agente Etiológico registrado"]);
	}

	public function agregarCaracteristicaAgente(Request $request){
		$nombre = $request->input("caracteristica");
		DB::table('caracteristicas_agente')->insert( ['nombre' => $nombre]);

		return response()->json(["data" => "Característica Agente registrada"]);
	}
	public function planTratamiento(Request $request){
		$detalle 	   = $request->input("detalle");
		$paciente_plan = $request->input("paciente_plan");
		try{

				$plan=new PlanDeTratamiento;
				$plan->fecha=date("Y-m-d H:i:s");
				$plan->detalle=$detalle;
				$plan->paciente=$paciente_plan;
				$plan->save();
		}
		catch(Exception $e){
			return response()->json(["error" => "Error al ingresar Plan de Tratamiento"]);
		}

		return response()->json(array("exito" => "Plan de Tratamiento ingresado"));
    }

	public function getPlanTratamiento(Request $request){
		$id=$request->input("id");
		$detalle=array();

		$plan=PlanDeTratamiento::where("paciente","=",$id)->get();

		foreach ($plan as $planTratamiento) {
			$detalle[]=$planTratamiento->detalle;
		}

		$datos=array("detalle" => $detalle);

		return response()->json($datos);
	}

	public function modificarExamenImagen(Request $request){
        try{
            DB::beginTransaction();

            /* se modifica el actual */
            $modificar = Examen::where("id", $request->id)->first();
            $modificar->usuario_modifica = Auth::user()->id;
            $modificar->updated_at = Carbon::now();
			$modificar->visible = false;
			$modificar->tipo_modificacion = 'Editado';
            $modificar->save();

			/* crear nuevo examen */
			$examenEEP = new Examen;
			$examenEEP->caso = $modificar->caso;
			$examenEEP->examen = $request->examen;
			$examenEEP->fecha = $modificar->fecha;
			$examenEEP->pendiente = $request->pendiente;
			$examenEEP->tipo = $request->tipo;
			$examenEEP->visible = true;
			$examenEEP->usuario = Auth::user()->id;
            $examenEEP->save();

            DB::commit();
            return response()->json(["exito" => "Se ha modificado un examen exitosamente"]);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al modificar el examen"]);
        }

    }

	public function eliminarEEP(Request $request){
        try{
            DB::beginTransaction();

            /* se modifica el actual */
            $eliminar = Examen::where("id", $request->id)->first();
            $eliminar->usuario_modifica = Auth::user()->id;
            $eliminar->updated_at = Carbon::now();
            $eliminar->tipo_modificacion = 'Eliminado';
            $eliminar->visible = false;
            $eliminar->save();

            DB::commit();
            return response()->json(["exito" => "Se ha eliminado examen exitosamente"]);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al eliminar examen"]);
        }
    }

	public function obtenerEEP($caso){
		$resultado = [];
		$examenes = DB::select(DB::raw("select
			e.id,
			u.nombres,
			u.apellido_paterno,
			u.apellido_materno,
			e.fecha,
			e.examen,
			e.pendiente,
			e.tipo,
			e.usuario,
			e.visible
			from examenes as e
			left join usuarios as u on u.id = e.usuario
			where
			e.caso = $caso and e.visible = 'true'
			"));

		$tipos_examen = Consultas::obtenerEnum("tipo_examen");

		foreach ($examenes as $key => $examen) {

			$html2 = "<select class='form-control' id='pendiente".$key."'>";

			/* select pendiente */
			if($examen->pendiente == true){
				$html2.="<option value='true' selected>Si</option>
				<option value='false'>No</option>";
			}else{
				$html2.="<option value='true'>Si</option>
				<option value='false'selected>No</option>";
			}
			$html2.="</select>";

			/* select tipo de examen */
			$html3 = "<select class='form-control' id='tipo".$key."'>";

			foreach($tipos_examen as $key2 => $tipo){
				if(strtolower($tipo) == $examen->tipo){
					$html3.="<option value='".strtolower($tipo)."' selected>$tipo</option>";
				}else{
					$html3.="<option value='".strtolower($tipo)."'>$tipo</option>";
				}
			}

			$html3.="</select>";
			if(Auth::user()->tipo != 'director' && Auth::user()->tipo != 'medico_jefe_servicio' && Auth::user()->tipo != 'estadisticas' && Auth::user()->tipo != 'censo'){
				$resultado [] = [
					"Creado el día:<b>".Carbon::parse($examen->fecha)->format("d-m-Y H:i")."</b><br><b><input class='form-control calcularTotal' id='nombreEEP".$key."' data-id='".$key."' type='text' value='".$examen->examen."'></b>  ",
					$html2,
					$html3,
					$examen->nombres." ".$examen->apellido_paterno." ".$examen->apellido_materno,
					"<div class='row'>
					<div class='col-md-5'>
						<button type='button' class='btn-xs btn-warning' onclick='modificarEEP(".$examen->id.",".$key.")'>Modificar</button>
					</div>
					<div class='col-md-5'>
						<button type='button' class='btn-xs btn-danger' onclick='eliminarEEP(".$examen->id.")'>Eliminar</button>
					</div>
					</div>"
				];
			}else{
				$pen_ = ($examen->pendiente == true)?"Si":"No";
				$resultado [] = [
					"<b>".ucwords($examen->examen)."</b><br>Creado el día:<b>".Carbon::parse($examen->fecha)->format("d-m-Y H:i")."</b>",
					$pen_,
					ucwords($examen->tipo),
					$examen->nombres." ".$examen->apellido_paterno." ".$examen->apellido_materno
				];
			}

		}

		return response()->json(["aaData" => $resultado]);
	}

	public function examenesCaso(Request $request,$caso = null){

        if(!$caso) {
            $caso = $request->input("caso");
        }
        /* @var $caso_obj Caso */
        $caso_obj = Caso::find($caso);

        try{
            $unidad = $caso_obj->camas()->firstOrFail()->sala()->firstOrFail()->unidadEnEstablecimiento()->firstOrFail();
            $some = $unidad->some !== null;
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $some = false;
		}



        $examenes = Examen::where("caso", $caso)
            ->orderBy("fecha", "desc")
			->get();

		$tipos_examen = Consultas::obtenerEnum("tipo_examen");

        $resp = View::make("Gestion/verExamenes", [
            "caso" => $caso,
            /* "examenes" => $examenes, */
            //"riesgos" => $riesgos,
            //"opciones_dieta" => $opciones_dieta,
            "caso_obj" => $caso_obj,
			"some" => $some,
			"tipos_examen" => $tipos_examen
        ] )->render();
        Session::put("caso_a_actualizar", $caso);

        return response()->json(array("contenido" => $resp) );
    }

	public function updatePendiente(Request $request)
    {
        $examen = $request->id_examen;
        $pendiente = $request->estado;

        if ($examen !== '' && $examen !== null) {
            $diag = Examen::find($examen);
            $diag->pendiente = $pendiente;
            $diag->save();
        }

        return "listo";
	}

	public function actualizarEstado(Request $request)
    {
		try {
			DB::beginTransaction();
			if ($request->id !== '' && $request->id !== null) {
				$derivado = ListaDerivados::find($request->id);
				$derivado->estado = $request->estado;
				$derivado->save();

				/* */
				$comentarioDerivado = new ListaDerivadosComentarios();
				$comentarioDerivado->caso = $derivado->caso;
				$comentarioDerivado->fecha = Carbon::now();
				$comentarioDerivado->id_lista_derivados = $derivado->id_lista_derivados;
				if($derivado->estado == 'Realizada'){
					$comentarioDerivado->comentario = 'La Derivación a sido realizada';
				}elseif($derivado->estado == 'No procesada'){
					$comentarioDerivado->comentario = 'La Derivación a cambiado a No procesada';
				}else{
					$comentarioDerivado->comentario = 'La Derivación se encuentra en proceso de gestión';
				}
				$comentarioDerivado->id_usuario_comenta = Auth::user()->id;
				$comentarioDerivado->save();
				/* */
				DB::commit();
				return response()->json(array("exito" => "Estado de derivación actualizado correctamente."));
			}
		} catch (Exception $ex) {
			DB::rollback();
			return response()->json(array("error" => "No se ha podido realizar la actualización."));
		}
	}

    public function ingresarExamen(Request $request){

		$examen = strip_tags(trim($request->input("nuevo-examen")));
		$pendiente = strip_tags($request->input("pendiente_examen"));
		$tipo_examen = strip_tags($request->input("tipo_examen"));

		/* Nuevo ingresar paciente */
		try{
            DB::beginTransaction();

            /* se modifica el actual */
            $examenEEP = new Examen;
			$examenEEP->caso = strip_tags($request->caso);
			$examenEEP->examen = $examen;
			$examenEEP->fecha = Carbon::now();
			$examenEEP->pendiente = $pendiente;
			$examenEEP->tipo = $tipo_examen;
			$examenEEP->visible = true;
			$examenEEP->usuario = Auth::user()->id;
            $examenEEP->save();

            DB::commit();
            return response()->json(["exito" => "Se ha ingresado e examen exitosamente"]);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(["error" => "Error al ingresar la planificación de cuidados"]);
		}

    }

    public function notificaciones(Request $request){

    	$esperas =  DB::table('lista_espera')
    	            ->join('casos', 'lista_espera.caso','=','casos.id')
    	            ->join('pacientes', 'casos.paciente','=', 'pacientes.id')
    	            ->join('usuarios', 'lista_espera.usuario','=','usuarios.id')
    				->whereNull('lista_espera.fecha_termino')
    				->where('usuarios.establecimiento', '=',$request->establecimiento)
    				->count();
		if($esperas){
			return Response::json(["exito"=>$esperas]);
		}else{
			return Response::json(["error"=>$esperas]);
		}
	}

	public function getInfectados(Request $request){
		$estab = $request->input("estab");
		$infectados=DB::select(DB::raw("select id_establecimiento, nombre_establecimiento, id_servicio, nombre_servicio from (select * from casos_iaas ci
			where ci.fecha_termino is null) ci, ultimas_ocupaciones_vista u
			where
			ci.caso=u.caso
			group by id_establecimiento, nombre_establecimiento, id_servicio, nombre_servicio"));

		$estab_infec = [];
		foreach($infectados as $inf){
			if($inf->id_establecimiento == $estab){
				$estab_infec[] = $inf->id_servicio;
			}
		}

		return response()->json($estab_infec);
	}
	//antes esto era notificador.php
	public function verificar($job, $data){
		$_cupos_servicios = Establecimiento::_cuposParaExtrasistema()
		->select("est.id as id_est",
			"est.nombre as nombre_est",
			"servicio.nombre as nombre_unidad",
			DB::raw("count(cm.id) as cantidad")
		)
		->groupBy("est.id", "est.nombre", "servicio.nombre")
		->orderBy("est.nombre", "asc")->orderBy("servicio.nombre", "asc")
		->get();

		/* Quitar de la lista de cupos aquellos que tienen cero */
		/* Aquí deberían ingresarse sólo aquellos que no han sido notificados dentro del día. */
		$date = Carbon::now()->endOfDay()->addHours(5);
		$_cupos_servicios = array_filter($_cupos_servicios->toArray(), function($i) use ($date){
			return $i->cantidad > 0;
		});
		/* Si depués del filtro la lista resulta vacía, terminar con el script. */
		if ( empty($_cupos_servicios) ){
			return;
		}

		$arr_ss = array();

		foreach($_cupos_servicios as $cupo){
			$arr_ss[$cupo->id_est][] = $cupo;
		}

		$_cupos_unidades = Establecimiento::cuposTotalesParaExtrasistema()->get();

		$arr_est = array();

		foreach($_cupos_unidades as $cupo){
			$arr_est[$cupo->id_est][] = $cupo;
		}

		$msg_ss = $this->notificar_ss($arr_est);
		$mensajes = $this->notificar_est($arr_est);

		return $this->enviarNotificaciones($msg_ss, $mensajes);

	}

	public function notificar_ss($arr_est){
		$msg = "";
		foreach($arr_est as $est){
			foreach($est as $cupo){
				$rut = Paciente::formatearRut($cupo->rut, $cupo->dv);
				$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$rut}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->paciente} {$cupo->apellido_paterno} {$cupo->apellido_materno}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_est}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->est_ex}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_unidad}</td>";
				$msg.= "</tr>";
			}
		}

		return $msg;

	}

	public function notificar_est($arr_est){
		$mensajes = array();
		foreach($arr_est as $est){
			$msg = "";
			foreach($est as $cupo){
				$rut = Paciente::formatearRut($cupo->rut, $cupo->dv);
				$msg.= "<tr style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$rut}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->paciente} {$cupo->apellido_paterno} {$cupo->apellido_materno}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_est}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->est_ex}</td>";
				$msg.= "<td style='border-collapse: collapse;border: 1px solid gray ;padding:5px;'>{$cupo->nombre_unidad}</td>";
				$msg.= "</tr>";
			}

			$mensajes[$cupo->id_est] = $msg;
			return $mensajes;
		}
	}

	public function enviarNotificaciones($msg_ss, $mensajes){
		$admins_ss = Usuario::whereNotNull("email")->where(function($q) {
			$q->where("tipo", "=", TipoUsuario::ADMINSS)
				->orWhere("tipo", "=", TipoUsuario::MONITOREO_SSVQ);
		})->get();
		$admins = Usuario::whereNotNull("email")->where("tipo", "=", TipoUsuario::ADMIN)->get();
		$admins_ss->each(function($i) use ($msg_ss){
			$email = trim($i->email);
            if($email === '' || $email === null || !filter_var($email, FILTER_VALIDATE_EMAIL) || Cache::has("notifss{$email}")){
				return;
			}
			$destinatario = "{$i->nombres} {$i->apellido_paterno}";
			Mail::send("emails.notificacion_ss", array("nombre" => $destinatario, "contenido" => $msg_ss), function($message) use ($email, $destinatario) {
				$message->to("{$email}", $destinatario)->subject("Notificación SSVQ");
			});
			$expiracion = Carbon::now()->addHours(24);
			Cache::add("notifss{$email}", true, $expiracion);
		});
		$admins->each(function($i) use ($mensajes){
			if(isset($mensajes[$i->establecimiento])){
				$email = trim($i->email);
                if($email === '' || $email === null || !filter_var($email, FILTER_VALIDATE_EMAIL) || Cache::has("notifad{$email}")){
					return;
				}
				$destinatario = "{$i->nombres} {$i->apellido_paterno}";
				Mail::send("emails.notificacion_ss", array("nombre" => $destinatario, "contenido" => $mensajes[$i->establecimiento]), function($message) use ($email, $destinatario) {
					$message->to("{$email}", $destinatario)->subject("Notificación SSVQ");
				});
                $expiracion = Carbon::now()->addHours(24);
				Cache::add("notifad{$email}", true, $expiracion);
			}
		});

		return "exito";
	}

	public function documentosDerivacion(Request $request, $caso = null){

		if(!$caso) {
            $caso = $request->input("caso");
		}

		$caso_obj = Caso::find($caso);

		$documentos = DocumentoDerivacionCaso::where("caso","=",$caso)->where("visible",true)->get();

		$resp = View::make("Gestion/VerDocumentosDerivacion", [
            //"dietas" => $dietas,
            "documentos" => $documentos,
            //"riesgos" => $riesgos,
            //"opciones_dieta" => $opciones_dieta,
            "caso_obj" => $caso_obj
            //"some" => $some
		] )->render();

		Session::put("caso_a_actualizar", $caso);
        Session::put("caso_a_actualizar_obj", $caso);

		return response()->json(array("contenido"=>$resp));
	}

	public function quitarDocumentoDerivacion($id){

		try{
			DB::beginTransaction();
				$documentos = DocumentoDerivacionCaso::where("id_documento_derivacion_caso","=",$id)->where("visible",true)->first();
				$documentos->visible = false;
				$documentos->save();

				$request = new Request([
					'caso'   => $documentos->caso
				]);

			DB::commit();

			return response()->json([
				"exito" => "Documento eliminado exitosamente",
				"contenido" => $this->documentosDerivacion($request, $documentos->caso)
			]);

		}catch(\Exception $e){
			DB::rollBack();
			Log::info($e);
			return response()->json(["error" => "Error al eliminar documento"]);
		}

	}

	public function fileupload(){

		 $up = new UploadHandler;
	}

	public function ingresarDoducmentoDerivacion(Request $request){

		try{
			DB::beginTransaction();

				$caso = Session::get("caso_a_actualizar");
				$documento = new DocumentoDerivacionCaso;
				$file = $request->input("files");
				$documento->caso = $caso;
				$documento->recurso = $file[0]["name"];
				$documento->fecha = Carbon::now();
				$documento->url = $file[0]["url"];
				$documento->visible = true;
				$documento->save();

			DB::commit();
			return $this->documentosDerivacion($request, $caso);

		}catch(\Exception $e){
			DB::rollBack();
			Log::info($e);
			return response()->json(["error" => "Error al agregar documento"]);
		}
	}



	public function regresarEspera($idCaso, $ubicacion){
		try{
			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,$ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			DB::beginTransaction();
			THistorialOcupaciones::where("caso","=",$idCaso)->delete();
			ListaTransito::where("caso","=",$idCaso)->delete();
			$lista_espera = ListaEspera::where("caso","=",$idCaso)->update(['fecha_termino' => null]);

			//En caso de tener un caso en espera de derivacion y este pasa a lista de espera, se debe cerrar la lista de espera de derivacion
			// ListaDerivados::cerrarListaDerivado($idCaso);

			//En caso de que no tenga lista de espera, se debe crear uno para este, en la mayoria son pacientes que se les aigno directamente una cama
			if(!$lista_espera){
				$lista=new ListaEspera;
				$lista->caso=$idCaso;
				$lista->fecha=Carbon::now()->format("Y-m-d H:i:s");
				$lista->usuario = Session::get("usuario")->id;
				$lista->ubicacion = "Sin información";
				$lista->save();
			}
			DB::commit();

			return response()->json(array("exito" => "regreso exitoso"));

		}
		catch(Exception $w){
			DB::rollBack();
			return response()->json(array("error" => "No se ha podido realizar el regreso a lista de espera"));
		}
	}

	public function validarDau(Request $request){

		$caso = Caso::where("dau","=",$request->dau)->whereNull("fecha_termino")->first();
		$valid = true;

			if($caso){
				$valid = false;
			}

		return response()->json(array("valid"=>$valid));
	}

	public function perejil(){

		return date_default_timezone_get();
	}

	public function cambiarUnidad(Request $request){


		try{

			$servicio = $request->servicios;
			$idCaso = $request->idCaso;
			$ubicacion = ($request->ubicacion) ? $request->ubicacion : '';

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso, $ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			$caso = Caso::where("id","=",$idCaso)->first();

			$caso->id_unidad = $servicio;

			$caso->save();
			return response()->json(array("exito"=>"Servicio modificado correctamente", "caso"=>$caso));

		}catch(Exception $w){
			return response()->json(array("error"=>"Error"));
		}

	}

	public function cambiarCama(Request $request){

			$cama = $request->cama;
			$idCaso = $request->idCaso;

			//validaciones
			$respuesta = Consultas::puedeHacer($idCaso,$request->ubicacion);
			if($respuesta != "Exito"){
				return response()->json(array("error" => $respuesta));
			}
			//validaciones

			$ocupacion = THistorialOcupaciones::where("caso","=",$idCaso)->first();
			$ocupacion->fecha = Carbon::now()->format("Y-m-d H:i:s");
			$ocupacion->cama = $cama;
			$ocupacion->save();

			return response()->json(array("exito"=>"Servicio modificado correctamente", "req"=>$request->all()));
	}


	public function ingresarCategorizacion(Request $request){

		try{


			if ($request->input("riesgoDependencia") != null || $request->input("riesgoDependencia") != '') {
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
				$riesgo->riesgo9 = $request->riesgo9;
				$riesgo->categoria = $request->input("riesgoDependencia");
				$riesgo->save();

				$id_riesgo = $riesgo->id;
			}else{
				$id_riesgo = null;
			}

			$ev = new EvolucionCaso();
                $ev->caso = $request->input("caso");
                $ev->fecha = \Carbon\Carbon::now();
                if ($request->input("riesgoDependencia") != null || $request->input("riesgoDependencia") != '') {
                    $ev->riesgo = $request->input("riesgoDependencia");
                }else{
                    $ev->riesgo = null;
                }

                $ev->riesgo_id = $id_riesgo;
				$ev->save();



			return response()->json(array("exito"=>"Riesgo ingresado correctamente"));
		}
		catch(Exception $e){
			return response()->json(array("error"=>"nook"));
		}

}

function getToken2() {
	$url = 'https://api.minsal.cl/oauth/token';
	$data = array('grant_type' => 'client_credentials');

	$client_id = 'Mu0FWV0PxECAGDpKD6Nw4dT4cPIt6Hj7';
	$client_secret = "ZslrIyuklGWzJ5kd";

	$str_base64 = base64_encode($client_id . ':' . $client_secret);
	//R0JMQVFETUVDcWxBWTd3cHdYWXNMY0FrVVFReVpYMTE6UUhzU00zTFpKS1VHc1o0dQ==
	//R0JMQVFETUVDcWxBWTd3cHdYWXNMY0FrVVFReVpYMTE6UUhzU00zTFpKS1VHc1o0dQ==


	$options = array(
		'http' => array(
			'header'  =>
			"Content-type: application/x-www-form-urlencoded\r\n" . "Authorization: Basic " . $str_base64,
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	if ($result === FALSE) { /* error */ }

	$authObj = json_decode($result);
	return  $authObj->access_token;
}



function getToken() {
	$url = 'https://apiqa.minsal.cl/oauth/token';
	$data = array('grant_type' => 'client_credentials');

	$client_id = 'GBLAQDMECqlAY7wpwXYsLcAkUQQyZX11';
	$client_secret = "QHsSM3LZJKUGsZ4u";

	$str_base64 = base64_encode($client_id . ':' . $client_secret);

	$options = array(
		'http' => array(
			'header'  =>
			"Content-type: application/x-www-form-urlencoded\r\n" . "Authorization: Basic " . $str_base64,
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	if ($result === FALSE) { /* error */ }

	$authObj = json_decode($result);
	return $authObj->access_token;
}

//gestion enfermeria
public function gestionEnfermeria($caso){
	$caso_id_encrypted = Crypt::encrypt($caso);

	/* arreglar o mejorar */
	$hoy = Carbon::now()->format('Y-m-d');
	$curacionesHoy = HojaCuraciones::where('caso',$caso)->where('proxima_curacion',$hoy)->get();
	$indicaciones = Indicacion::indicacionesMedicas($caso);
	$paciente = Paciente::getPacientePorCaso($caso);
	$prevision = Caso::find($caso,'prevision');
	$tiposCuidado = TipoCuidado::orderBy("tipo","asc")->pluck('tipo','id');
	$telefonos = Telefono::where('id_paciente',$paciente->id)->get();
	
	//epicrisis
	$epicrisis = [];
	$epicrisis = InformeEpicrisis::datosEpicrisis($caso);
	
	//para partograma
	$tiene_partograma = false;
	$datos_partograma = (object)[
		  "caso_id" => $caso,
		  "form_id" => null,
	];
	
	if($epicrisis["sub_categoria"] == 1){
		$pg = new \App\Helpers\FormulariosGinecologia\PartogramaHelper();
		$tiene_partograma = $pg->tienePartograma($caso);
	}
	if($epicrisis["sub_categoria"] == 5){
		$pg = new \App\Helpers\FormulariosGinecologia\PartogramaHelper();
		$datos_partograma = $pg->getPartogramaData($caso);
	}

	$medicamento = $caso;
	$medicamentos = DB::select(DB::raw("select *
		from medicamentos
		where caso =".$medicamento." and visible = true"));
		
	return View::make("Gestion/GestionEnfermeria",[
		"caso_id_encrypted" => $caso_id_encrypted,
		"caso" => $caso,
		"curacionesHoy" => $curacionesHoy,
		"indicaciones" => $indicaciones,
		"infoPaciente" => $paciente,
		"telefonos" => $telefonos,
		"prevision" => $prevision->prevision,
		"tiposCuidado" => $tiposCuidado,
		"dau" => $epicrisis["dau"],
		"unidad" => $epicrisis["nombre_unidad"],
		"area" => $epicrisis["nombre_area_funcional"],
		"fechaSolicitud" => $epicrisis["fecha_solicitud"],
		"fechaHospitalizacion" => $epicrisis["fecha_hospitalizacion"],
		"fechaEgreso" => $epicrisis["fecha_egreso"],
		"diffHospEgreso" => $epicrisis["estadia2"],
		"destinos" => $epicrisis["motivos"],
		"diagnosticos" => $epicrisis["diagnosticos"],
		"medicamentos" => $medicamentos,
		"sub_categoria" => $epicrisis["sub_categoria"],
		"tiene_partograma" => $tiene_partograma,
		"formulario_data" => $datos_partograma
	]);

}
//gestion medica
public function gestionMedica($caso){
	$caso_id_decrypted = base64_decode($caso);
	/* arreglar o mejorar */
	$hoy = Carbon::now()->format('Y-m-d');
	$paciente = Paciente::getPacientePorCaso($caso_id_decrypted);
	$prevision = Caso::find($caso_id_decrypted,'prevision');
	$telefonos = Telefono::where('id_paciente',$paciente->id)->get();
	$sueros = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
	->where('tipo','SUERO')
	->pluck('nombre_unidad','id')
	->toArray();
	$farmacos = ArsenalFarmacia::select(DB::raw("CONCAT(nombre, ' - ',unidad_medida) as nombre_unidad"), 'id')
	->whereIn('tipo',['ANTIBIOTICO','ANTIBIOTICO-ANTIFUNGICO'])
	->pluck('nombre_unidad','id')
	->toArray();

	return View::make("Gestion/GestionMedica",[
		"caso" => $caso,
		"infoPaciente" => $paciente,
		"telefonos" => $telefonos,
		"prevision" => $prevision->prevision,
		"sueros" =>	$sueros,
		"farmacos" => $farmacos
	]);

}

public function formularios($caso){
	/* arreglar o mejorar */
	$hoy = Carbon::now()->format('Y-m-d');
	$curacionesHoy = HojaCuraciones::where('caso',$caso)->where('proxima_curacion',$hoy)->get();
	$indicaciones = Indicacion::indicacionesMedicas($caso);
	$paciente = Paciente::getPacientePorCaso($caso);
	$tiposCuidado = TipoCuidado::orderBy("tipo","asc")->pluck('tipo','id');

	$tipo_unidad= DB::select(DB::raw("select ue.tipo_unidad
					from historial_ocupaciones_vista hv,
						camas c,
						salas s,
						unidades_en_establecimientos ue
					where hv.caso =  $caso
					and c.sala = s.id
					and c.id = hv.cama
					and s.establecimiento = ue.id limit 1;"));

	$tipo_unidad = $tipo_unidad[0]->tipo_unidad;

	return View::make("Gestion/Formulario",[
		"caso" => $caso,
		"formulario" => "formulario",
		"curacionesHoy" => $curacionesHoy,
		"indicaciones" => $indicaciones,
		"infoPaciente" => $paciente,
		"tiposCuidado" => $tiposCuidado,
		"tipoUnidad" => $tipo_unidad
	]);

}

public function formulariosP($caso){
	/* arreglar o mejorar */
	$hoy = Carbon::now()->format('Y-m-d');
	$curacionesHoy = HojaCuraciones::where('caso',$caso)->where('proxima_curacion',$hoy)->get();
	$indicaciones = Indicacion::indicacionesMedicas($caso);
	$paciente = Paciente::getPacientePorCaso($caso);
	$tiposCuidado = TipoCuidado::orderBy("tipo","asc")->pluck('tipo','id');

	return View::make("Gestion/FormularioPediatrico",[
		"caso" => $caso,
		"formulario" => "formulario",
		"indicaciones" => $indicaciones,
		"infoPaciente" => $paciente
	]);

}

	public function formularioRiesgoUlcera($caso){
		return View::make("Gestion.gestionEnfermeria.riesgoUlcera")->with("caso", $caso);
	}

	//formulario caida
	public function formRiesgoCaida($caso){
		return View::make("Gestion.gestionEnfermeria.riesgoCaida")->with("caso", $caso);
	}

	//formNova
	public function formNova($caso){
		return View::make("Gestion.gestionEnfermeria.nova")->with("caso", $caso);
	}

	//formGlasgow
	public function formGlasgow($caso){
		return View::make("Gestion.gestionEnfermeria.glasgow")->with("caso", $caso);
	}

	//formBarthel
	public function formBarthel($caso){
		return View::make("Gestion.gestionEnfermeria.barthel")->with("caso", $caso);
	}

	//formPacientePostrado
	public function formPacientePostrado($caso){
		return View::make("Gestion.gestionEnfermeria.pacientePostrado")->with("caso", $caso);
	}

	//formulario nuevo macdems
	public function formMacdems($caso){
		return View::make("Gestion.gestionEnfermeria.macdems")->with("caso", $caso);
	}

	//formulario nuevo macdems
	public function formRiesgoUlcera($caso){
		return View::make("Gestion.gestionEnfermeria.riesgoUlcera")->with("caso", $caso);
	}

	//formulario uso restringido
	public function formUsoRestringido($caso){
		return View::make("Gestion.gestionEnfermeria.usoRestringido")->with("caso", $caso);
	}

	public function descripcionCamas(Request $request){
		return Cama::where("id", "=", $request->idCama)->select("descripcion")->first();

	}

	public function cambiarDescripcion(Request $request){

		try{
		$cambiodescripcion = Cama::where("id", "=", $request->idCama)->first();

		$cambiodescripcion->descripcion = $request->descripcion;

		$cambiodescripcion->save();
			return response()->json(array("exito"=>"Descripción modificada correctamente"));

			}catch(Exception $w){
			return response()->json(array("error"=>"Error"));
			}

	}

	public function validarFechaEgresoThistorial(Request $request){
		//valida que la fecha de egreso no sea menor al campo fecha de la tabla t_historial_ocupaciones
		try {
			//"Y-m-d H:i:s"
			$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fechaEgreso)->format("Y-m-d H:i:s");
			$historial = THistorialOcupaciones::select('fecha')->where('caso',$request->casoLiberar)->where('cama',$request->cama)->whereNull('fecha_liberacion')->first();
			if($historial){
				$topeInicio = Carbon::parse($historial->fecha)->format("Y-m-d H:i:s");
				$topeConFormatoInferion = Carbon::parse($historial->fecha)->format('d-m-Y H:i');
			}else{
				$historial = Caso::find($request->casoLiberar,'fecha_ingreso2');
				$topeInicio = Carbon::parse($historial->fecha_ingreso2)->format("Y-m-d H:i:s");
				$topeConFormatoInferion = Carbon::parse($historial->fecha_ingreso2)->format('d-m-Y H:i');
			}
			
			// $fecha_egreso > $fecha_actual  ||   $fecha_egreso < $topeInicio
			if($fecha_egreso < $topeInicio){
				return response()->json(["valid" => false, "message" => " La fecha de egreso debe ser mayor a $topeConFormatoInferion"]);
			}else{
				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaEgresoCaso(Request $request){
		//valida que la fecha de egreso no sea menor al campo fecha de la tabla t_historial_ocupaciones
		try {
			//"Y-m-d H:i:s"
			$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fechaEgreso)->format("Y-m-d H:i:s");
			$caso = Caso::find($request->casoLiberar,'fecha_ingreso2');
			$topeInicio = Carbon::parse($caso->fecha_ingreso2)->format("Y-m-d H:i:s");
			$topeConFormatoInferion = Carbon::parse($caso->fecha_ingreso2)->format('d-m-Y H:i');
			$topeConFormatoSuperior = Carbon::now()->format('d-m-Y H:i');

			$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");		
			// $fecha_egreso > $fecha_actual  ||   $fecha_egreso < $topeInicio
			if($fecha_egreso < $topeInicio){
				return response()->json(["valid" => false, "message" => " La fecha de egreso debe ser mayor a $topeConFormatoInferion"]);
			}else{
				return response()->json(["valid" => true]);
			}	
			
		}catch(Exception $e) {
			Log::info($e);
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaHospDomCasoEgresado(Request $request){
		//valida que la fecha de termino en hosp dom no sea menor a la fecha de ingreso a hosp dom
		try {
			$fecha_salida_hosp_dom = Carbon::createFromFormat("d-m-Y H:i", $request->fechaEgreso)->format("Y-m-d H:i:s");
			$hospDom = HospitalizacionDomiciliaria::find($request->idLista,'fecha');
			$topeInicio = Carbon::parse($hospDom->fecha)->format("Y-m-d H:i:s");
			$topeConFormatoInferior = Carbon::parse($hospDom->fecha)->format('d-m-Y H:i');
			$topeConFormatoSuperior = Carbon::now()->format('d-m-Y H:i');

			$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");		
				// $fecha_salida_hosp_dom > $fecha_actual || $fecha_salida_hosp_dom < $topeInicio
			if( $fecha_salida_hosp_dom < $topeInicio){
				return response()->json(["valid" => false, "message" => " La fecha de egreso debe ser mayor a $topeConFormatoInferior"]);
			}else{
				return response()->json(["valid" => true]);
			}	
			
		}catch(Exception $e) {
			Log::info($e);
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaDerivacionRealizada(Request $request){
		try {
			$fecha_termino_derivacion = Carbon::createFromFormat("d-m-Y H:i", $request->fechaTermino)->format("Y-m-d H:i:s");
			//$derivacion = ListaDerivados::where('caso',$request->caso)->whereNull('fecha_egreso_lista')->first();
			$derivacion = ListaDerivados::where('id_lista_derivados',$request->lista)->whereNull('fecha_egreso_lista')->first();
			$topeInicio = Carbon::parse($derivacion->fecha_ingreso_lista)->format("Y-m-d H:i:s");
			$topeConFormatoInferior = Carbon::parse($derivacion->fecha)->format('d-m-Y H:i');
			$topeConFormatoSuperior = Carbon::now()->format('d-m-Y H:i');

			$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");		

			if( $fecha_termino_derivacion > $fecha_actual || $fecha_termino_derivacion < $topeInicio){
				return response()->json(["valid" => false, "message" => " La fecha de termino debe estar dentro del rango ($topeConFormatoInferior - $topeConFormatoSuperior)"]);
			}else{
				return response()->json(["valid" => true]);
			}	
			
		}catch(Exception $e) {
			Log::info($e);
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function verHistorialVisitas($idCaso){
		$visitas = DB::select(DB::raw("
		SELECT
		row_number() over() as posicion,
		fecha_entrada_visita,
		to_char( fecha_entrada_visita, 'HH24:MI' ) AS hora_ingreso,
		nombre || ' ' || apellido AS nombre_completo,
		n_identificacion,
		telefono,
		observaciones,
		usuario_responsable 
		FROM
			registro_visitas 
		WHERE
			caso = ".$idCaso."
			AND visible = TRUE
		"
		));
		
		$resultado = [];

		foreach ($visitas as $visita) {
			$usuario_responsable = Usuario::find($visita->usuario_responsable);
			$nombre_completo_usuario = ($usuario_responsable) ? "{$usuario_responsable->nombres} {$usuario_responsable->apellido_paterno} {$usuario_responsable->apellido_materno}" : "Sin información";
			$resultado[] = [
				$visita->posicion,
				Carbon::parse($visita->fecha_entrada_visita)->format("d-m-Y"),
				$visita->hora_ingreso,
				$visita->nombre_completo,
				$visita->n_identificacion,
				$visita->telefono,
				$visita->observaciones,
				$nombre_completo_usuario
			];
		}

		$paciente = DB::select(DB::raw("
		select
		REPLACE(to_char(pacientes.rut,'999G999G999G999'), ',', '.') as rut,
		pacientes.dv,
		pacientes.nombre || ' ' || pacientes.apellido_paterno AS nombre_completo,
		configuracion_visitas.recibe_visitas,
		configuracion_visitas.comentario_visitas,
		configuracion_visitas.num_personas_visitas,
		configuracion_visitas.cant_horas_visitas
		FROM casos 
		RIGHT JOIN pacientes ON pacientes.id = casos.paciente
		LEFT JOIN (
		SELECT * from configuracion_visitas
		where
		configuracion_visitas.visible IS TRUE
		)as configuracion_visitas ON configuracion_visitas.id_caso = casos.id
		WHERE casos.id = ".$idCaso." order by fecha_creacion desc
		
		"
		));

		return response()->json(["visitas" => $resultado,"paciente" => $paciente]);
	}

	public function pdfHistorialRegistroVisitas($caso){
		$fechaActual = Carbon::now();
		$fecha = Carbon::parse($fechaActual)->format("d-m-Y");
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		$datos = $this->verHistorialVisitas($caso);
		$datitos = json_decode($datos->content(), true);
		$datosPaciente = $datitos["paciente"];
		$datosVisitas = $datitos["visitas"];
		try {
			$pdf = PDF::loadView('Visitas.pdfHistorialVisitas', [
				"fecha" => $fecha,
				"establecimiento" => $nombreEstablecimiento,
				"infoPaciente" => $datosPaciente,
				"infoVisitas" => $datosVisitas
			]);
			return $pdf->setPaper('legal', 'portrait')->download('Historial_visitas_'.$fecha.'.pdf');
		} catch (Exception $ex) {
			return $ex->getMessage();
		}
	}
}

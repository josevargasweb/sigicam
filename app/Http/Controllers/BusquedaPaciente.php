<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use App\Models\Paciente;
use App\Models\ListaEspera;
use App\Models\HistorialDiagnostico;
use App\Models\Indicacion;
use App\Models\Caso;
use App\Models\Consultas;
use DB;
use Session;
use Log;
use Auth;
use App\Models\THistorialOcupaciones;
use DateTime;
use App\Models\ListaTransito;
use App\Models\ListaDerivados;
use Carbon\Carbon;
use App\Models\Examen;
use App\Models\HospitalizacionDomiciliaria;
use App\Models\EdicionFechas;

class BusquedaPaciente extends Controller{
    /* @var $ultimo_diagnostico HistorialDiagnostico*/
	protected $ultimo_diagnostico;
    /* @var $ultimo_caso Caso */
    protected $ultimo_caso;
    /* @var $paciente Paciente */
    protected $paciente;

    /* @var $todos_casos Collection */
    protected $todos_casos;

	public function busquedaGeneral(Request $request){
		try{			
			$busqueda = strtoupper($request->busqueda);
			$busqueda2 = $request->busqueda2;
			Log::info("Usuario con nombre -> ".Auth::user()->nombres." esta buscando -> ".$busqueda." o ".$busqueda2);	
			
			if($request->tipo == "rut"){
				if(!$busqueda2){
					return response()->json(["error" => "Ingrese el rut del paciente"]);
				}

				$rut_sin_digito = str_replace("-","",$busqueda2);
				$rut_sin_digito=substr($rut_sin_digito, 0, -1);
				$pacientes = Paciente::select("pacientes.id","pacientes.rut","pacientes.dv", "pacientes.nombre", "pacientes.apellido_paterno","pacientes.apellido_materno")->where("rut",$rut_sin_digito)->get();
				
			}
			elseif($request->tipo == "nombre"){
				if(!$busqueda){
					return response()->json(["error" => "Ingrese el nombre del paciente"]);
				}
				$pacientes = Paciente::similar($busqueda)->select("pacientes.id","pacientes.rut","pacientes.dv", "pacientes.nombre", "pacientes.apellido_paterno","pacientes.apellido_materno")->get();
			}
			elseif($request->tipo == "ficha"){
				if(!$busqueda){
					return response()->json(["error" => "Ingrese la ficha del paciente"]);
				}
				$pacientes = Paciente::select("pacientes.id","pacientes.rut","pacientes.dv", "pacientes.nombre", "pacientes.apellido_paterno","pacientes.apellido_materno")
					->join("casos","casos.paciente","=","pacientes.id")
					->where("casos.motivo_termino", "<>","corrección cama")// correcciones de cama no deben ser considerados
					->where("ficha_clinica","=",$busqueda)
					->get();
			}elseif($request->tipo == "nombre_apellido"){
				$nombre = trim(strtoupper($request->nombre));
				$paterno = trim(strtoupper($request->paterno));
				$materno = trim(strtoupper($request->materno));
				
				if(!$nombre && !$paterno && !$materno){
					return response()->json(["error" => "Ingrese nombre o apellidos del paciente"]);
				}

				$pacientes = Paciente::select("id","rut","dv", "nombre", "apellido_paterno","apellido_materno")
					->where(function ($q) use ($nombre, $paterno, $materno) {
						if($nombre){
							$q->Where('nombre', 'ilike', "%".$nombre."%");
						}
						if($paterno){
							$q->Where('apellido_paterno', 'ilike', "%".$paterno."%");
						}
						if($materno){
							$q->Where('apellido_materno', 'ilike', "%".$materno."%");
						}
					})->get();
			}

			/* Log::info($pacientes); */

			$general = Array();
			$opcion = '-';
			$fecha_hosp = '-';
			$servicio = "";
			$sala = "";
			$cama = "";

			foreach($pacientes as $paciente){
				
				$opcion = '-';
				$fecha_hosp = '-';
				$servicio = "-";
				$sala = "-";
				$cama = "-";

				///////////////////////////////////////////////////////////////////////////////////////////
				//Se intentara buscar un caso que tenga fecha de termino null en un primer caso, luego se//
				//buscara en orden de fechas de ingreso de los casos, para resolver el problema de casos //
				//con problemas y que tengan un caso abierto y no egresado.////////////////////////////////
				///////////////////////////////////////////////////////////////////////////////////////////

				$ultimo_caso = DB::table('pacientes as p')
					->select('c.id', 'c.fecha_termino','c.fecha_ingreso','c.fecha_ingreso2', 'e.nombre as nombre_establecimiento', 'd.diagnostico', 'e.id as id_estab','c.procedencia','c.detalle_procedencia')
					->join('casos as c', 'c.paciente','=','p.id')
					->join('establecimientos as e', 'c.establecimiento', "=",'e.id')
					->join('diagnosticos as d', 'd.caso', "=","c.id")
					->where('p.id',$paciente->id)
					/* ->orderBy("c.fecha_ingreso","desc") */
					->whereNull('c.fecha_termino')
					->first();				
				////////////////////////////////////////////////////////////////
				//Si no se encontro ningun caso abierto, este ordena los casos//
				////////////////////////////////////////////////////////////////
				if(!$ultimo_caso){
					$ultimo_caso = DB::table('pacientes as p')
						->select('c.id', 'c.fecha_termino','c.fecha_ingreso','c.fecha_ingreso2', 'e.nombre as nombre_establecimiento', 'd.diagnostico', 'e.id as id_estab','c.procedencia','c.detalle_procedencia')
						->join('casos as c', 'c.paciente','=','p.id')
						->join('establecimientos as e', 'c.establecimiento', "=",'e.id')
						->join('diagnosticos as d', 'd.caso', "=","c.id")
						->where('p.id',$paciente->id)
						->where("c.motivo_termino","<>","corrección cama")// correcciones de cama no deben ser considerados
						->orderBy("c.fecha_ingreso","desc")
						->first();
				}

				//si tiene casos (Algunos pacientes no tienen casos y nadie sabe por que)
				if($ultimo_caso){
				
					$estab = $ultimo_caso->nombre_establecimiento;		
					/* Log::info("ultimo_caso");
					Log::info( $ultimo_caso->id ); */

					if($paciente->dv == 10){
						$dv = "K";
					}else{
						$dv = $paciente->dv;
					}

					$procedencia = DB::table("procedencias")
					->where("id",$ultimo_caso->procedencia)
					->first()->nombre;

					if($ultimo_caso->procedencia == 2 || $ultimo_caso->procedencia == 3 || $ultimo_caso->procedencia == 4 || $ultimo_caso->procedencia == 7){
						$procedencia .= "<br><strong style='color:#000000'> Detalle: ".$ultimo_caso->detalle_procedencia."</strong>";
					}

					if($ultimo_caso->fecha_termino == null){ //esta en hospital
						
						//Busca el ultimo historial ocupaciones
						$ocupacion = THistorialOcupaciones::select("fecha_ingreso_real","id_cama as nombre_cama","salas.nombre as nombre_sala","uee.alias as nombre_servicio","uee.url","salas.id as id_sala","camas.id as id_cama", "uee.id as id_servicio")
								->join("camas","t_historial_ocupaciones.cama","=","camas.id")
								->join("salas","camas.sala","=","salas.id")
								->join("unidades_en_establecimientos as uee","uee.id","=","salas.establecimiento")
								->orderby("t_historial_ocupaciones.id", "desc")
								->where("caso","=",$ultimo_caso->id)
								->where(function($q) {
									$q->whereNull("t_historial_ocupaciones.motivo")
									->orWhere("t_historial_ocupaciones.motivo", "<>", "corrección cama");
								})
								//->where("t_historial_ocupaciones.motivo","<>","corrección cama")// correcciones de cama no deben ser considerados
								->first();
			
						$restriccionPersonal = false;


						if($ocupacion){
							if($ocupacion->fecha_ingreso_real == null || $ocupacion->fecha_ingreso_real == ''){
								$ultimaFecha = THistorialOcupaciones::select("fecha_ingreso_real")
								->latest("fecha_ingreso_real")
								->orderby("t_historial_ocupaciones.id", "asc")
								->where("caso","=",$ultimo_caso->id)
								->whereNotNull("fecha_ingreso_real")
								->where("t_historial_ocupaciones.motivo", "<>", "corrección cama")
								->first();

								$fecha_hosp = (empty($ultimaFecha) || (isset($ultimaFecha->fecha_ingreso_real) && $ultimaFecha->fecha_ingreso_real == null) || (isset($ultimaFecha->fecha_ingreso_real)  && $ultimaFecha->fecha_ingreso_real == ''))? '-':date("d-m-Y H:i",strtotime($ultimaFecha->fecha_ingreso_real));
							}else{

								$fecha_hosp = date("d-m-Y H:i",strtotime($ocupacion->fecha_ingreso_real));
							}

							$sala =  $ocupacion->nombre_sala;
							$cama = $ocupacion->nombre_cama;
							$servicio = $ocupacion->nombre_servicio;

							$restriccionPersonal = Consultas::restriccionPersonal($ocupacion->id_servicio);

							if($ultimo_caso->id_estab == Session::get('idEstablecimiento')){
								if(Session::get('usuario')->tipo == 'visualizador' || Session::get("usuario")->tipo == 'oirs')
								{
									$opcion="";
								}

								if(Session::get('usuario')->tipo != 'visualizador' && Session::get('usuario')->tipo != 'medico' && Session::get("usuario")->tipo != 'iaas' && Session::get("usuario")->tipo != 'oirs'){
									$opcion = "  <form style='display: hidden' action='../unidad/".$ocupacion->url."' method='GET' >
										<input hidden type='text' name='paciente' value='".$paciente->id."'>
										<input hidden type='text' name='id_sala' value='".$ocupacion->id_sala."'>
										<input hidden type='text' name='id_cama' value='".$ocupacion->id_cama."'>
										<input hidden type='text' name='caso' value='".$ultimo_caso->id."'>
										<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
									</form>  
									<a href='../paciente/editar/$paciente->id' class='btn btn-primary'>Editar paciente</a>";
								}
		
								if(Session::get('usuario')->tipo == 'censo'  || Session::get('usuario')->tipo == 'estadisticas' || Session::get('usuario')->tipo == 'admin_comercial'){
									$opcion = "  <form style='display: hidden' action='../unidad/".$ocupacion->url."' method='GET'>
										<input hidden type='text' name='paciente' value='".$paciente->id."'>
										<input hidden type='text' name='id_sala' value='".$ocupacion->id_sala."'>
										<input hidden type='text' name='id_cama' value='".$ocupacion->id_cama."'>
										<input hidden type='text' name='caso' value='".$ultimo_caso->id."'>
										<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
									</form>";
								}

								if(Session::get('usuario')->tipo == 'tens'){
									$opcion = "";
									$opcion = "  <form style='display: hidden' action='../gestionEnfermeria/".$ultimo_caso->id."' method='GET'>
										<button class='btn btn-primary' type='submit'>Ir a Gestión Enfermeria</button>						
									</form>";
								}

								if(Session::get('usuario')->tipo == 'encargado_hosp_domiciliaria'){
									$opcion="";
								}

							
							
								if(Session::get('usuario')->tipo == 'visualizador' || Session::get("usuario")->tipo == 'oirs' || Session::get("usuario")->tipo == 'master' ){
									$opcion .= "<a class='btn btn-primary' onclick='verHistorialVisitas($ultimo_caso->id)'>Historial de visitas</a>";
								}
							}


							if($ocupacion->fecha_ingreso_real == null){
								//Transito
							
								$lista_transito = ListaTransito::where("caso","=",$ultimo_caso->id)->whereNull("traslado_unidad_hospitalaria")->first();
								
								if($lista_transito){
									$estab = $estab."<br> <label>(Lista de transito)</label>";
								}
								else{
									$estab = $estab."<br> <label>(Salida de urgencia)</label>";
								}
								
							}
							else{
								//Acostado
							}

						}
						else{
							//EN espera
							$estab = $estab."<br> <label>(Lista de espera)</label>";
						}
						
						
						$general["pacientes"][] =[
							"idpaciente"       => $paciente->id,
							'rut'              => $paciente->rut.' '.$dv,
							'nombre'           => $paciente->nombre,
							'apellidos'        => $paciente->apellido_paterno." ".$paciente->apellido_materno,
							'fecha_nacimiento' => $paciente->fecha_nacimiento,
							'id_estab'         => $paciente->id_establ,
							'estab'            => $estab,
							'diagnostico'      => ($restriccionPersonal == true)?"<b><img src='../../public/img/bloquear.png' height='15px'>Tiene retricciones</b>":$ultimo_caso->diagnostico,
							'servicio'         => $servicio,
							'solicitud_cama'   => ($ultimo_caso->fecha_ingreso2 == null || $ultimo_caso->fecha_ingreso2 == '')? '-':date("d-m-Y H:i",strtotime($ultimo_caso->fecha_ingreso2)),
							'sala'             => $sala,
							'cama'             => $cama,
							'opcion'		   => $opcion,
							'fecha_hosp'       => $fecha_hosp,
							'procedencia'      => $procedencia,
						];	


					}else{

						$opcion = '-';
						if($ultimo_caso->id_estab == Session::get('idEstablecimiento')){					
							if(Session::get('usuario')->tipo == 'visualizador' || Session::get("usuario")->tipo == 'oirs' || Session::get("usuario")->tipo == 'master' ){
								$opcion = "<a class='btn btn-primary' onclick='verHistorialVisitas($ultimo_caso->id)'>Historial de visitas</a>";
							}
						}


						$estab = $estab."<br> <label>(Egresado)</label>";
						$ocupacion2 = THistorialOcupaciones::select("fecha_ingreso_real", "cama")
							->orderby("t_historial_ocupaciones.id", "desc")
							->where("caso","=",$ultimo_caso->id)
							->where("t_historial_ocupaciones.motivo","<>","corrección cama")// correcciones de cama no deben ser considerados
							->first();
						if($ocupacion2){
							if($ocupacion2 && ($ocupacion2->fecha_ingreso_real == null || $ocupacion2->fecha_ingreso_real == '')){
								$ultimo2 = THistorialOcupaciones::select("fecha_ingreso_real")
								->orderby("t_historial_ocupaciones.id", "asc")
								->where("caso","=",$ultimo_caso->id)
								->whereNotNull("fecha_ingreso_real")
								->where("t_historial_ocupaciones.motivo","<>","corrección cama")// correcciones de cama no deben ser considerados
								->first();

								$fecha_hosp ='';
								if (!empty($ultimo2)) {
									if ($ultimo2->fecha_ingreso_real != null || $ultimo2->fecha_ingreso_real != '') {
										$fecha_hosp = date("d-m-Y H:i",strtotime($ultimo2->fecha_ingreso_real));
									}
								}
							}else{
								$fecha_hosp = (
									$ocupacion2 && 
									($ocupacion2->fecha_ingreso_real != null || $ocupacion2->fecha_ingreso_real != '')
									)? date("d-m-Y H:i",strtotime($ocupacion2->fecha_ingreso_real)):'-';
							}
						}
						// esta egresado por que el caso tiene fecha de termino
						$general["egresados"][] =[
							"idpaciente"       => $paciente->id,
							'rut'              => $paciente->getRutFormateado(),
							'nombre'           => $paciente->nombre,
							'apellidos'        => "{$paciente->apellido_paterno} {$paciente->apellido_materno}",
							'fecha_nacimiento' => $paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $ultimo_caso->diagnostico,
							'solicitud_cama'    => ($ultimo_caso->fecha_ingreso2 == null || $ultimo_caso->fecha_ingreso2 == '')? '-':date("d-m-Y H:i",strtotime($ultimo_caso->fecha_ingreso2)),
							'fecha_egreso'	   => date("d-m-Y H:i",strtotime($ultimo_caso->fecha_termino)),
							'fecha_hosp'       => $fecha_hosp,
							'procedencia'	   => $procedencia,
							'opcion'		   => $opcion,
							];
					}

				}
			}
			

		
			return response()->json($general);

		}catch(Exception $Ew){
			return $eW;
		}

	}

	public function porRut(Request $request){
		$rut_sin_digito = str_replace("-","",$request->input('rut'));
		$rut_sin_digito=substr($rut_sin_digito, 0, -1);
		$general = array();
		
		$paciente = Paciente::where("rut",$rut_sin_digito)->first();
		if(!$paciente){
			return response()->json(["error"=>"No se encontraron resultados"]);
		}

		$this->paciente = $paciente;			
				

				$caso_paciente = DB::table('pacientes as p')
									->select('c.id')
									->join('casos as c', 'c.paciente','=','p.id')
									->join('t_historial_ocupaciones as t','t.caso','=','c.id')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('p.id',$this->paciente->id)
									->first();

				
				$informacion = '';

				$this->ultimo_caso = $paciente->casoActual()->whereNull("fecha_termino")->first();

				

				$this->camas =null;
				/* if($this->ultimo_caso) */
				if(count($caso_paciente) != 0)
				{
					
					$informacion = DB::table('pacientes as p')
									->select('p.id as id_paciente','p.rut','p.dv', 'p.nombre as nomb_pac','p.apellido_materno','p.apellido_paterno','p.fecha_nacimiento','e.nombre as estbl_nombre','e.id as id_establ','u.alias','c.fecha_ingreso','s.nombre as sala_nom','ca.id_cama','u.url','s.id as id_sala','ca.id as id_cama','c.id as caso_id','ca.id_cama as nombre_cama', 't.fecha_ingreso_real')
									->leftjoin('casos as c', 'c.paciente','p.id')
									->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
									->leftjoin('camas as ca','ca.id','t.cama')
									->leftjoin('salas as s','s.id','ca.sala')
									->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
									->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('c.id', $caso_paciente->id)
									->first();
									
					$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre')
										->join('cie_10 as c','c.id_cie_10','d.id_cie_10')
										->where('d.caso',$caso_paciente->id)
										->orderby('d.fecha','desc')
										->first();

					$opcion = '-';
					if($informacion->id_establ == Session::get('idEstablecimiento')){
						if(Session::get('usuario')->tipo != 'visualizador'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>  
							<a href='../paciente/editar/$informacion->id_paciente' class='btn btn-primary'>Editar paciente</a>";
						}

						if(Session::get('usuario')->tipo == 'censo'  || Session::get('usuario')->tipo == 'estadisticas'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>";
						}
					}

					if($diagnostico){
						$diagnostico_nombre = $diagnostico->nombre;
					}
					else{
						$diagnostico_nombre = "";
					}

					if($informacion->dv == 10){
						$dv = "K";
					}else{
						$dv = $informacion->dv;
					}

					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagnostico_nombre = '';
					}

					$estab = $informacion->estbl_nombre;
					$fecha_hosp =  date("d-m-Y H:i",strtotime($informacion->fecha_ingreso_real));
					if($informacion->fecha_ingreso_real == null){
						$estab = $informacion->estbl_nombre."<br> <label>(Lista de transito)</label>";
						$fecha_hosp = "-";
					}
						
					$general["pacientes"][] =[
						"idpaciente"       => $informacion->id_paciente,
						'rut'              => $informacion->rut.' '.$dv,
						'nombre'           => $informacion->nomb_pac,
						'apellidos'        => $informacion->apellido_paterno." ".$informacion->apellido_materno,
						'fecha_nacimiento' => $informacion->fecha_nacimiento,
						'id_estab'         => $informacion->id_establ,
						'estab'            => $estab,
						'diagnostico'      => $diagnostico_nombre,
						'servicio'         => $informacion->alias,
						'fecha_ingreso'    => date("d-m-Y H:i",strtotime($informacion->fecha_ingreso)),
						'sala'             => $informacion->sala_nom,
						'cama'             => $informacion->nombre_cama,
						'opcion'		   => $opcion,
						'fecha_hosp'       => $fecha_hosp,
					];

				}else{
					$egresado = 'no';
					$caso = DB::table('casos as c')
						->select('e.nombre as nombre', 'c.fecha_termino', 'c.fecha_ingreso', 'c.id as caso','t.fecha_ingreso_real')
						->join('establecimientos as e', 'e.id','=','c.establecimiento')
						->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
						->where('c.paciente', $this->paciente->id)
						->where('e.id', Auth::user()->establecimiento)
						->orderBy('c.fecha_ingreso', 'desc')
						->first();

					
				
					$estab = "Lista de espera";
					$diagn = "-";
					if($caso){
						$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre as cie_10')
										->join('cie_10 as c', 'c.id_cie_10', '=','d.id_cie_10')
										->where('d.caso', $caso->caso)
										->orderBy('d.fecha','desc')
										->first();
						if($caso->fecha_termino == null){
							$estab = $caso->nombre."<br> <label>(Lista de espera)</label>";
						}else{
							$estab = $caso->nombre."<br> <label>(Egresado)</label>";
							$egresado = 'si';
						}

						$diagn = $diagnostico->cie_10;
						
						
					}

					$opcion = "";
					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagn = '';
					}
					//$fecha=date("d-m-Y H:i", strtotime($dato->solicitud));
					if($egresado == 'si'){
						$general["egresados"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							/* 'servicio'         => "-", */
							'fecha_ingreso'    => date("d-m-Y H:i",strtotime($caso->fecha_ingreso)),
							'fecha_egreso'	   => date("d-m-Y H:i",strtotime($caso->fecha_termino)),
							'fecha_hosp'       => date("d-m-Y H:i",strtotime($caso->fecha_ingreso_real)),
							/* 'sala'             => "-",
							'cama'             => "-",
							'opcion'		   => $opcion, */
							];
					}else{
						// no ingresado actualmente
						$general["pacientes"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							'servicio'         => "-",
							'fecha_ingreso'    => "-",
							'sala'             => "-",
							'cama'             => "-",
							'opcion'		   => $opcion,
							'fecha_hosp'       => "-"
							];
					}
				}
		
	


        return response()->json($general);
	}

	public function porNombre(Request $request){
		$nombre_apellidos = $request->input("nombre");
		$arr = array();
		$general = array();
		
        foreach (Paciente::similar($nombre_apellidos)->get() as $paciente){
            try{

				$this->paciente = $paciente;
				
				

				$caso_paciente = DB::table('pacientes as p')
									->select('c.id')
									->join('casos as c', 'c.paciente','=','p.id')
									->join('t_historial_ocupaciones as t','t.caso','=','c.id')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('p.id',$this->paciente->id)
									->first();

				
				$informacion = '';

				$this->ultimo_caso = $paciente->casoActual()->whereNull("fecha_termino")->first();

				

				$this->camas =null;
				if(count($caso_paciente) != 0)
				{
					$informacion = DB::table('pacientes as p')
									->select('p.id as id_paciente','p.rut','p.dv', 'p.nombre as nomb_pac','p.apellido_materno','p.apellido_paterno','p.fecha_nacimiento','e.nombre as estbl_nombre','e.id as id_establ','u.alias','c.fecha_ingreso','s.nombre as sala_nom','ca.id_cama','u.url','s.id as id_sala','ca.id as id_cama','c.id as caso_id','ca.id_cama as nombre_cama', 't.fecha_ingreso_real')
									->leftjoin('casos as c', 'c.paciente','p.id')
									->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
									->leftjoin('camas as ca','ca.id','t.cama')
									->leftjoin('salas as s','s.id','ca.sala')
									->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
									->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('c.id', $caso_paciente->id)
									->first();
					$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre')
										->join('cie_10 as c','c.id_cie_10','d.id_cie_10')
										->where('d.caso',$caso_paciente->id)
										->orderby('d.fecha','desc')
										->first();


					$opcion = '-';
					if($informacion->id_establ == Session::get('idEstablecimiento')){

						if(Session::get('usuario')->tipo != 'visualizador'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>  
							<a href='../paciente/editar/$informacion->id_paciente' class='btn btn-primary'>Editar paciente</a>";
						}

						if(Session::get('usuario')->tipo == 'censo'  || Session::get('usuario')->tipo == 'estadisticas'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>";
						}
					}

					if($diagnostico){
						$diagnostico_nombre = $diagnostico->nombre;
					}
					else{
						$diagnostico_nombre = "";
					}

					if($informacion->dv == 10){
						$dv = "K";
					}else{
						$dv = $informacion->dv;
					}

					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagnostico_nombre = '';
					}

					$estab = $informacion->estbl_nombre;
					$fecha_hosp =  date("d-m-Y H:i",strtotime($informacion->fecha_ingreso_real));
					if($informacion->fecha_ingreso_real == null){
						$estab = $informacion->estbl_nombre."<br> <label>(Lista de transito)</label>";
						$fecha_hosp = "-";
					}
						
					$general["pacientes"][] =[
						"idpaciente"       => $informacion->id_paciente,
						'rut'              => $informacion->rut.' '.$dv,
						'nombre'           => $informacion->nomb_pac,
						'apellidos'        => $informacion->apellido_paterno." ".$informacion->apellido_materno,
						'fecha_nacimiento' => $informacion->fecha_nacimiento,
						'id_estab'         => $informacion->id_establ,
						'estab'            => $estab,
						'diagnostico'      => $diagnostico_nombre,
						'servicio'         => $informacion->alias,
						'fecha_ingreso'    => date("d-m-Y H:i",strtotime($informacion->fecha_ingreso)),
						'sala'             => $informacion->sala_nom,
						'cama'             => $informacion->nombre_cama,
						'opcion'		   => $opcion,
						'fecha_hosp'       => $fecha_hosp,
					];
						

				}else{
					$egresado = 'no';

				


					$caso = DB::table('casos as c')
						->select('e.nombre as nombre', 'c.fecha_termino', 'c.fecha_ingreso', 'c.id as caso','t.fecha_ingreso_real')
						->join('establecimientos as e', 'e.id','=','c.establecimiento')
						->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
						->where('c.paciente', $this->paciente->id)
						->when(Session::get("idEstablecimiento"), function($query){
							return $query->where('e.id', Auth::user()->establecimiento);
						})
						->orderBy('c.fecha_ingreso', 'desc')
						->first();
				
					$estab = $caso->nombre."Lista de espera";
					$diagn = "-";
					if($caso){
						$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre as cie_10')
										->join('cie_10 as c', 'c.id_cie_10', '=','d.id_cie_10')
										->where('d.caso', $caso->caso)
										->orderBy('d.fecha','desc')
										->first();
						if($caso->fecha_termino == null){
							$estab = $caso->nombre."<br> <label>(Lista de espera)</label>";
						}else{
							$estab = $caso->nombre."<br> <label>(Egresado)</label>";
							$egresado = 'si';
						}

						$diagn = $diagnostico->cie_10;
					}

					$opcion = "";
					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagn = '';
					}
					
					if($egresado == 'si'){
						$general["egresados"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							'fecha_ingreso'    => date("d-m-Y H:i",strtotime($caso->fecha_ingreso)),
							'fecha_egreso'	   => date("d-m-Y H:i",strtotime($caso->fecha_termino)),
							'fecha_hosp'       => date("d-m-Y H:i",strtotime($caso->fecha_ingreso_real))
							];
					}else{
						// no ingresado actualmente
						$general["pacientes"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							'servicio'         => "-",
							'fecha_ingreso'    => "-",
							'sala'             => "-",
							'cama'             => "-",
							'opcion'		   => $opcion,
							'fecha_hosp'       => "-"
							];
					}
				}

			}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
				$this->camas = null;
				$general["catch"] = $e;
				$general["catchm"] = $e->getMessage();
				return $general;
			}



        }
        return response()->json($general);
	}

	public function porFicha(Request $request){
		$numero_ficha = $request->input("ficha");
		$arr = array();
		$general = array();

		$fichas = Caso::select('paciente')
						->where('ficha_clinica', '=', $numero_ficha)
						->distinct('paciente')
						->get();
						
        foreach ($fichas as $ficha){
            try{
				$paciente = Paciente::find($ficha->paciente);
				$this->paciente = $paciente;
				
				

				$caso_paciente = DB::table('pacientes as p')
									->select('c.id')
									->join('casos as c', 'c.paciente','=','p.id')
									->join('t_historial_ocupaciones as t','t.caso','=','c.id')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('p.id',$this->paciente->id)
									->first();

				
				$informacion = '';

				$this->ultimo_caso = $paciente->casoActual()->whereNull("fecha_termino")->first();

				

				$this->camas =null;
				/* if($this->ultimo_caso) */
				if(count($caso_paciente) != 0)
				{
					$informacion = DB::table('pacientes as p')
									->select('p.id as id_paciente','p.rut','p.dv', 'p.nombre as nomb_pac','p.apellido_materno','p.apellido_paterno','p.fecha_nacimiento','e.nombre as estbl_nombre','e.id as id_establ','u.alias','c.fecha_ingreso','s.nombre as sala_nom','ca.id_cama','u.url','s.id as id_sala','ca.id as id_cama','c.id as caso_id','ca.id_cama as nombre_cama', 't.fecha_ingreso_real')
									->leftjoin('casos as c', 'c.paciente','p.id')
									->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
									->leftjoin('camas as ca','ca.id','t.cama')
									->leftjoin('salas as s','s.id','ca.sala')
									->leftjoin('unidades_en_establecimientos as u','u.id','s.establecimiento')
									->leftjoin('establecimientos as e', 'e.id','u.establecimiento')
									->whereNull('t.fecha_liberacion')
									->whereNull('c.fecha_termino')
									->where('c.id', $caso_paciente->id)
									->first();
					$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre')
										->join('cie_10 as c','c.id_cie_10','d.id_cie_10')
										->where('d.caso',$caso_paciente->id)
										->orderby('d.fecha','desc')
										->first();


					$opcion = '-';
					if($informacion->id_establ == Session::get('idEstablecimiento')){

						if(Session::get('usuario')->tipo != 'visualizador'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>  
							<a href='../paciente/editar/$informacion->id_paciente' class='btn btn-primary'>Editar paciente</a>";
						}

						if(Session::get('usuario')->tipo == 'censo'  || Session::get('usuario')->tipo == 'estadisticas'){
							$opcion = "  <form style='display: hidden' action='../unidad/".$informacion->url."' method='GET' id='form'>
								<input hidden type='text' name='paciente' value='".$informacion->id_paciente."'>
								<input hidden type='text' name='id_sala' value='".$informacion->id_sala."'>
								<input hidden type='text' name='id_cama' value='".$informacion->id_cama."'>
								<input hidden type='text' name='caso' value='".$informacion->caso_id."'>
								<button class='btn btn-primary' type='submit'>Ir a unidad</button>						
							</form>";
						}
					}

					if($diagnostico){
						$diagnostico_nombre = $diagnostico->nombre;
					}
					else{
						$diagnostico_nombre = "";
					}

					if($informacion->dv == 10){
						$dv = "K";
					}else{
						$dv = $informacion->dv;
					}

					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagnostico_nombre = '';
					}

					$estab = $informacion->estbl_nombre;
					$fecha_hosp =  date("d-m-Y H:i",strtotime($informacion->fecha_ingreso_real));
					if($informacion->fecha_ingreso_real == null){
						$estab = $informacion->estbl_nombre."<br> <label>(Lista de transito)</label>";
						$fecha_hosp = "-";
					}
						
					$general["pacientes"][] =[
						"idpaciente"       => $informacion->id_paciente,
						'rut'              => $informacion->rut.' '.$dv,
						'nombre'           => $informacion->nomb_pac,
						'apellidos'        => $informacion->apellido_paterno." ".$informacion->apellido_materno,
						'fecha_nacimiento' => $informacion->fecha_nacimiento,
						'id_estab'         => $informacion->id_establ,
						'estab'            => $estab,
						'diagnostico'      => $diagnostico_nombre,
						'servicio'         => $informacion->alias,
						'fecha_ingreso'    => date("d-m-Y H:i",strtotime($informacion->fecha_ingreso)),
						'sala'             => $informacion->sala_nom,
						'cama'             => $informacion->nombre_cama,
						'opcion'		   => $opcion,
						'fecha_hosp'       => $fecha_hosp,
					];

				}else{
					$egresado = 'no';
					$caso = DB::table('casos as c')
						->select('e.nombre as nombre', 'c.fecha_termino', 'c.fecha_ingreso', 'c.id as caso','t.fecha_ingreso_real')
						->join('establecimientos as e', 'e.id','=','c.establecimiento')
						->leftjoin('t_historial_ocupaciones as t','t.caso','c.id')
						->where('c.paciente', $this->paciente->id)
						->where('e.id', Auth::user()->establecimiento)
						->orderBy('c.fecha_ingreso', 'desc')
						->first();

					
				
					$estab = $caso->nombre."<br>Lista de espera";
					$diagn = "-";
					if($caso){
						$diagnostico = DB::table('diagnosticos as d')
										->select('c.nombre as cie_10')
										->join('cie_10 as c', 'c.id_cie_10', '=','d.id_cie_10')
										->where('d.caso', $caso->caso)
										->orderBy('d.fecha','desc')
										->first();
						if($caso->fecha_termino == null){
							$estab = $caso->nombre."<br> <label>(Lista de espera)</label>";
						}else{
							$estab = $caso->nombre."<br> <label>(Egresado)</label>";
							$egresado = 'si';
						}

						$diagn = $diagnostico->cie_10;
						
						
					}

					$opcion = "";
					if(Session::get('usuario')->tipo == 'censo' || Session::get('usuario')->tipo == 'visualizador'){
						$diagn = '';
					}
					
					if($egresado == 'si'){
						$general["egresados"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							'fecha_ingreso'    => date("d-m-Y H:i",strtotime($caso->fecha_ingreso)),
							'fecha_egreso'	   => date("d-m-Y H:i",strtotime($caso->fecha_termino)),
							'fecha_hosp'       => date("d-m-Y H:i",strtotime($caso->fecha_ingreso_real))
							];
					}else{
						// no ingresado actualmente
						$general["pacientes"][] =[
							"idpaciente"       => $this->paciente->id,
							'rut'              => $this->paciente->getRutFormateado(),
							'nombre'           => $this->paciente->nombre,
							'apellidos'        => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
							'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
							'id_estab'         => "-",
							'estab'            => $estab,
							'diagnostico'      => $diagn,
							'servicio'         => "-",
							'fecha_ingreso'    => "-",
							'sala'             => "-",
							'cama'             => "-",
							'opcion'		   => $opcion,
							'fecha_hosp'       => "-"
							];
					}
				}
				
			}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
				$this->camas = null;
				$general["catch"] = $e;
				$general["catchm"] = $e->getMessage();
				return $general;
			}



        }
        return response()->json($general);
	}



	public function obtenerGeneral(){
		return array(

		);
	}

	public function porId($idpaciente){
		DB::beginTransaction();
		$info = [];
		try{
			$this->paciente = Paciente::find($idpaciente);
			$info = $this->obtenerDetalles($this->paciente);
		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		DB::commit();
		
		return View::make("Busqueda/InfoPaciente")->with(array( 'info' =>  $info ));
	}

	public function obtenerDetalles($paciente){

		$this->paciente = $paciente;
		$idpaciente = $paciente->id;
		
		$fecha_salida_urg = "";

		$salidaUrg = DB::table('t_historial_ocupaciones as t')
			->leftjoin("casos","casos.id","=","t.caso")
			->leftjoin("camas","camas.id","=","t.cama")
			->leftjoin("salas","salas.id","=","camas.sala")
			->leftjoin("unidades_en_establecimientos","unidades_en_establecimientos.id","=","salas.establecimiento")
			->where("casos.paciente",$idpaciente)
			->where("unidades_en_establecimientos.alias", "=", "Urgencia")
			->Where("t.motivo", "<>", "corrección cama")//que no sea correccion de cama
			->orderBy("t.updated_at", "asc")
			->get();

		foreach ($salidaUrg as $salidaUrgs) {
			
			$fecha_salida_urg = $salidaUrgs->fecha_liberacion;

			if($fecha_salida_urg == null){
				$fecha_salida_urg = " ";
			}else{
				$fecha_salida_urg = date("d-m-Y H:i", strtotime($fecha_salida_urg));
			}
			
		}

		try{
			$this->ultimo_caso = $paciente->casoActual()->firstorFail();
		}catch(Exception $e){
			Log::info($e);
			return "a";
		}


        try{

			$this->ultimo_diagnostico = $this->ultimo_caso
				->diagnosticos()
				->orderBy("fecha", "desc")
				->firstOrFail();

        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $this->ultimo_diagnostico = new HistorialDiagnostico();
            $this->ultimo_diagnostico->caso = $this->ultimo_caso->id;
            $this->ultimo_diagnostico->diagnostico = $this->ultimo_caso->diagnostico;
            $this->ultimo_diagnostico->fecha = $this->ultimo_caso->fecha_ingreso;
            $this->ultimo_diagnostico->save();
		}
		$this->camas = $this->ultimo_caso->camas()->orderBy("fecha", "desc")->get();

		$fecha1 = new DateTime($this->paciente->fecha_nacimiento);
		$fecha2 = new DateTime();
		$fechaF = $fecha1->diff($fecha2);
		$diferencia = '';
		
		if($fechaF->y == 0){
			$diferencia = $fechaF->format('%m meses %a dias');
		}else{
			$diferencia = $fechaF->format('%y años %m meses');
		}

		$general = array(
			'idpaciente' => $this->paciente->id,
			'rut' => $this->paciente->getRutFormateado(),
			'nombre' => $this->paciente->nombre,
			'apellidos' => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
			'fecha_nacimiento' => date("d-m-Y",strtotime($this->paciente->fecha_nacimiento))." (".$diferencia.")"
		);

		$gestora = "";
		
		if(!$this->camas->isEmpty()){
			$fecha_hosp = DB::table('t_historial_ocupaciones')
				->select('fecha_ingreso_real','fecha_liberacion')
				->where('caso', '=', $this->ultimo_caso->id)
				->first();
				
			$this->ultima_cama = $this->camas->first();
			$this->sala = $this->ultima_cama->sala()->first();

			$restriccionPersonal = false;
			if($fecha_hosp->fecha_liberacion == null){
				$restriccionPersonal = Consultas::restriccionPersonal($this->sala->establecimiento);
			}
			
			$this->unidad = $this->sala->unidadEnEstablecimiento;
			$this->establecimiento = $this->sala->establecimiento();

			$fecha_hospG = "-";
			if($fecha_hosp->fecha_ingreso_real != ''){
				$fecha_hospG = $fecha_hosp->fecha_ingreso_real;
			}

			$general['id_estab']		= $this->establecimiento->id;
			$general['estab']			= $this->establecimiento->nombre;
			$general['diagnostico']		= ($restriccionPersonal == true)?"Tiene retricciones":$this->ultimo_diagnostico->diagnostico;
			$general['servicio']		= $this->unidad->alias;
			$general['salida']			= $fecha_salida_urg;
			$general['fecha_ingreso']	= $this->ultimo_caso->fecha_ingreso;
			$general['sala']			= $this->sala->nombre;
			$general['cama']			= $this->ultima_cama->id_cama;
			$general['fecha_hosp']		= $fecha_hospG;
		}
		else{

			$general['id_estab']		= $this->ultimo_caso;
			$general['estab']			= "-";
			$general['diagnostico']		= $this->ultimo_diagnostico->diagnostico;
			$general['servicio']		= "-";
			$general['salida']			= $fecha_salida_urg;
			$general['fecha_ingreso']	= $this->ultimo_caso->fecha_ingreso;
			$general['sala']			= "-";
			$general['cama']			= "-";
			$general['fecha_hosp']			= "-";
		}


		$d = "";
		try{
			////////////////////////////////////////////////////////
			//REVISAR SI TIENE CASOS ABIERTOS PARA LISTA DE ESPERA//
			////////////////////////////////////////////////////////

			$id_ultimo_caso = $this->ultimo_caso->id;
			$lista_espera = ListaEspera::where("caso", $id_ultimo_caso)
										->whereNull("fecha_termino")
										->first();
										
			
            $this->todos_casos = $this->paciente->casos()
				->where(function($q) {
					$q->whereNull("casos.motivo_termino")
					->orWhere("casos.motivo_termino", "<>", "corrección cama");
				})
                ->has("historialOcupacion")
                ->with(["historialEvolucion" => function($q){
                    $q->orderBy("fecha", "desc");
                }])
                ->with("historialOcupacion.camas.salaDeCama.unidadEnEstablecimiento.establecimientos")
                ->with(["diagnosticos" => function($q){
                    $q->orderBy("fecha", "desc");
                }])->get();
			
			$detalles = array();
			foreach($this->todos_casos as $key => $caso){
				$detalles[$caso->id] = array(
					'caso' => array(), 
					'evoluciones' => array(), 
					'traslados' => array(),
					'diagnosticos' => array(),
					'derivaciones' => array(),
					'indicaciones' => array(),
					'examenes' => array(),
					'altas' => array()
				);
			
				if($caso->fecha_termino == null){
					$estado_caso = "Caso abierto";
				}
				else{
					$estado_caso = "Caso cerrado";

					if ((Auth::user()->tipo == 'admin' || Auth::user()->tipo == 'master' || Auth::user()->tipo == 'master_ss') && $key == 0 && isset($lista_espera->id) == false) {
						$gestora = "<button onclick='reingresar(".$caso->paciente.")' class='btn btn-primary'>Reingresar </button>";
					}
				}
				//datos de historial ocupaciones
				$fecha_hosp = DB::table('t_historial_ocupaciones')
					->select('fecha_ingreso_real')
					->where('caso', '=', $caso->id)
					->first();

				$fecha_hospC = "-";
				if($fecha_hosp->fecha_ingreso_real != ''){
					$fecha_hospC = $fecha_hosp->fecha_ingreso_real;
				}

				$restriccionPersonal = false;
				if($estado_caso == "Caso abierto"){
					$restriccionPersonal = Consultas::restriccionPersonal($caso->historialOcupacion->first()->camas->salaDeCama->unidadEnEstablecimiento->id);
				}			
				
				$examenes = DB::select(DB::raw("select 
				e.id,
				u.nombres,
				u.apellido_paterno,
				u.apellido_materno,
				e.fecha,
				e.updated_at,
				e.examen,
				e.pendiente,
				e.tipo,
				e.usuario,
				e.visible
				from examenes as e 
				left join usuarios as u on u.id = e.usuario
				where 
				e.caso = $caso->id and e.visible = true
				"));
				foreach ($examenes as $exam) {
					$fecha_modificacion = ($exam->updated_at) ? $exam->updated_at : '--';
					$pendiente = ($exam->pendiente == true) ? 'Si' : 'No';
					$usuario = $exam->nombres ." ". $exam->apellido_paterno ." ". $exam->apellido_materno; 
					$detalles[$caso->id]['examenes'][] = [
						'id_caso' => $caso->id,
						'fecha_ingreso' => $exam->fecha,
						'fecha_modificacion' => $fecha_modificacion,
						'examen' => $exam->examen,
						'pendiente' => $pendiente,
						'tipo' => $exam->tipo,
						'usuario' => $usuario
					];
				}
				
				$detalles[$caso->id]['caso'] = array(
					'id_caso' => $caso->id,
					'id_estab' => $caso->historialOcupacion->first()->camas->salaDeCama->unidadEnEstablecimiento->establecimiento,
					'estab' => $caso->historialOcupacion->first()->camas->salaDeCama->unidadEnEstablecimiento->establecimientos->nombre,
					'diagnostico' => ($restriccionPersonal == true)?"Tiene retricciones":$caso->diagnosticos->first()->diagnostico,
					'fecha_ingreso' => $caso->fecha_ingreso,
					"fecha_termino" => $estado_caso,
					"fecha_hosp" => $fecha_hospC
				);
					
				foreach($caso->historialEvolucion as $evolucion){
					$detalles[$caso->id]['evoluciones'][] = [
						'fecha' => $evolucion->fecha,
						'categoria' => ($evolucion->urgencia == true)?$evolucion->riesgo." (Urgencia)":$evolucion->riesgo,
					];
				}
				foreach($caso->historialOcupacion as $traslado){
					if( $traslado->motivo != "corrección cama"){
						$cama = $traslado->camas;
						$sala = $cama->salaDeCama;
						$serv = $sala->unidadEnEstablecimiento;

						$detalles[$caso->id]['traslados'][] = [
							'fecha' => $traslado->fecha,
							'cama'	=> $cama->id_cama,
							'sala'	=> $sala->nombre,
							'serv'	=> $serv->alias,
						];
					}
				}

				/* Listado de indicaciones */

				$indicaciones = Indicacion::where("caso",$caso->id)->where("visible", true)->get();
				foreach($indicaciones as $indicacion){
					$detalles[$caso->id]['indicaciones'][] = [
						'fecha' => Carbon::parse($indicacion->fecha_creacion)->format('d-m-Y H:i:s'),
						'indicacion' => $indicacion->indicacion,
						'id' => $indicacion->id
					];          
				}

				$derivaciones = ListaDerivados::where("caso",$caso->id)->get();
				foreach ($derivaciones as $derivacion) {
					$detalles[$caso->id]['derivaciones'][] = [
						'fecha_ingreso' => $derivacion->fecha_ingreso_lista,
						'fecha_egreso' => $derivacion->fecha_egreso_lista,
						// 'usuario_ingresa' => $derivacion->id_usuario_ingresa,
						// 'usuario_egresa' => $derivacion->id_usuario_egresa,
						'estado' => $derivacion->estado,
						'id' => $derivacion->id_lista_derivados
					];
				}
				
				
				/* listado de diagnosticos */
                foreach($caso->diagnosticos as $diagnostico){
					if($restriccionPersonal == true){
						$detalles[$caso->id]['diagnosticos'][] = [
							'fecha' => "--",
							'diagnostico' => "Este paciente posee restricciones",
							'comentario' => "--",
							'id' => "-"
						];
						break;
					}else{
						$detalles[$caso->id]['diagnosticos'][] = [
							'fecha' => Carbon::parse($diagnostico->fecha)->format('d/m/Y H:i'),
							'diagnostico' => $diagnostico->diagnostico,
							'comentario' => $diagnostico->comentario,
							'id' => $diagnostico->id
						];
					}                   
                }

				$altas = HistorialDiagnostico::join('casos as c', 'diagnosticos.caso', '=', 'c.id')
				->leftJoin('pacientes as p', 'c.paciente', '=', 'p.id')
				->leftJoin('medico as m', 'c.id_medico_alta', '=', 'm.id_medico')
				->select('p.id', 'diagnosticos.diagnostico','m.nombre_medico', 'm.apellido_medico', 'c.motivo_termino', 'c.detalle_termino', 'c.fecha_termino')
				->whereNotNull('c.fecha_termino')
				->where('c.id',$caso->id)
				->orderBy('diagnosticos.fecha', 'desc')
				->get();

				foreach ($altas as $alta){
					$detalles[$caso->id]['altas'][] = [
						'fecha' => $alta->fecha_termino,
						'diagnostico' => $alta->diagnostico,
						'nombre_medico' => $alta->nombre_medico. " ".$alta->apellido_medico,
						'motivo_termino' => $alta->motivo_termino,
						'detalle_termino' => $alta->detalle_termino
					];
				}
			}	
			
		}catch(Exception $e){
			$detalles = [];
			$d = '';
			throw $e;
		}

		return array( 'general' => $general, 'detalles' => $detalles, 'd' => $d, 'gestora' => $gestora);
	}

	public function reingresar($paciente){
		
		$datos_paciente = Paciente::where("id",$paciente)->firstorFail();
		
		$ultimocaso = $datos_paciente->casoActual()->firstorFail();

		try{
			$datos_cama = DB::select("select
			camas.id as id_cama,
			salas.id as id_sala
			from t_historial_ocupaciones tho
			inner join camas on camas.id = tho.cama
			inner join salas on salas.id = camas.sala
			where tho.caso = ?",[$ultimocaso->id]);
			
			
			if($datos_cama)
			{
				$datos_cama = $datos_cama[0];
			}
			else{
				return response()->json(["error" => "El paciente nunca ha sido egresado"]);
			}

			return response()->json([
				"cambio" => "Se necesita hacer un traslado interno", 
				"nombrePaciente" => trim($datos_paciente->nombre." "
					.$datos_paciente->apellido_paterno." "
					.$datos_paciente->apellido_materno),
				"caso" => $ultimocaso->id,
				"idSala" => $datos_cama->id_sala,
				"idCama" => $datos_cama->id_cama,
				"idPaciente" => $paciente
			
			]);
			
		}catch(Exception $e){
			DB::rollBack();
			return response()->json(["error" => "Error al volver caso."]);
		}

		return response()->json();

	}

	public function buscarPaciente(){
		return View::make("Busqueda/Paciente");
	}

	public function buscarPacienteIAAS(){
		return View::make("Busqueda/PacienteIAAS");
	}

	public function porRutIaas(Request $request){
		$rut_sin_digito = $request->input('rut');
		$arr = array();
        foreach ( Paciente::whereRaw("rut::varchar % '$rut_sin_digito'")->get() as $p){
            try{

            	$infeccion=DB::table( DB::raw("(select i.id from casos as c,pacientes as p, infecciones as i where p.id=c.paciente and p.id in(select p.id from pacientes as p where rut=$p->rut)
				and c.id=i.caso and i.fecha_termino is null) as ri"
         			))->get();
            	if($infeccion!=null){
                	$arr[] = $this->obtenerInfoIaas($p);
                }
            }catch(Exception $e){
                continue;
            }
        }
        return response()->json($arr);
	}

	public function obtenerInfoIaas(Paciente $paciente){
		$this->paciente = $paciente;
		$this->ultimo_caso = $paciente->casoActual()->firstOrFail();
        try{
            $this->ultimo_diagnostico = $this->ultimo_caso
                ->diagnosticos()
                ->orderBy("fecha", "desc")
                ->firstOrFail();
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            $this->ultimo_diagnostico = new HistorialDiagnostico();
            $this->ultimo_diagnostico->caso = $this->ultimo_caso->id;
            $this->ultimo_diagnostico->diagnostico = $this->ultimo_caso->diagnostico;
            $this->ultimo_diagnostico->fecha = $this->ultimo_caso->fecha_ingreso;
            $this->ultimo_diagnostico->save();
        }
		$this->camas = $this->ultimo_caso->camas()->orderBy("fecha", "desc")->get();
		$general = array(
			'idpaciente' => $this->paciente->id,
			'rut' => $this->paciente->getRutFormateado(),
			'nombre' => $this->paciente->nombre,
			'apellidos' => "{$this->paciente->apellido_paterno} {$this->paciente->apellido_materno}",
			'fecha_nacimiento' => $this->paciente->fecha_nacimiento,
		);

		if(!$this->camas->isEmpty()){
			$this->ultima_cama = $this->camas->first();
			$this->sala = $this->ultima_cama->sala()->first();
			$this->unidad = $this->sala->unidadEnEstablecimiento;
			$this->establecimiento = $this->sala->establecimiento();

			$general['id_estab']		= $this->establecimiento->id;
			$general['estab']			= $this->establecimiento->nombre;
			$general['diagnostico']		= $this->ultimo_diagnostico->diagnostico;
			$general['servicio']		= $this->unidad->alias;
			$general['fecha_ingreso']	= $this->ultimo_caso->fecha_ingreso;
			$general['sala']			= $this->sala->nombre;
			$general['cama']			= $this->ultima_cama->id_cama;
			$general['Miunidad'] 		= $this->unidad->url;
			$general['Micaso'] 			= $this->ultimo_caso->id;
			$general['Ver'] 			= "Ver Infección";
		}
		else{
			$general['id_estab']		= '-';
			$general['estab']			= 'No ingresado actualmente';
			$general['diagnostico']		= '-';
			$general['servicio']		= '-';
			$general['fecha_ingreso']	= '-';
			$general['sala']			= '-';
			$general['cama']			= '-';
			$general['Miunidad'] 		= '-';
			$general['Micaso'] 			= '-';
			$general['Ver'] 			= '-';
		}
		return $general;
	}


	public function modificarDiagnostico(Request $request){
		DB::beginTransaction();
		try{
			$actualizar = HistorialDiagnostico::where("id",$request->idDiagn)->first();
			$actualizar->comentario = $request->comentario;
			$actualizar->save();
			
			DB::commit();	
			return response()->json(["exito"=>"Diagnóstico modificado correctamente"]);

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		return response()->json(["error"=>"Error al modificar diagnóstico"]);
	}

	public function addIndicacion(Request $request){
		DB::beginTransaction();
		try{
			
			$añadir = new Indicacion;
			$añadir->usuario = Auth::user()->id;
			$añadir->caso = $request->idCaso;
			$añadir->fecha_creacion = Carbon::parse($request->fecha)->format("Y-m-d H:i:s");
			$añadir->indicacion = $request->comentario;
			$añadir->visible =true;
			$añadir->save();
			
			DB::commit();	
			return response()->json(["exito"=>"Indicación ingresada correctamente"]);

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		return response()->json(["error"=>"Error al ingresar inidicación"]);
	}

	public function deleteIndicacion(Request $request){
		DB::beginTransaction();
		try{
			$elimianr = Indicacion::where("id", $request->idIndicacion)->first();
			$elimianr->usuario_modifica = Auth::user()->id;
			$elimianr->fecha_modificacion = Carbon::now();
			$elimianr->visible =false;
			$elimianr->save();
			
			DB::commit();	
			return response()->json(["exito"=>"Indicación eliminada correctamente"]);

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		return response()->json(["error"=>"Error al eliminar inidicación"]);
	}


	public function editIndicacion(Request $request){
		DB::beginTransaction();
		try{
			/* Terminar actual */
			$ocultar = Indicacion::where("id", $request->idIndicacion)->first();
			/* Crear nuevo */
			$nuevo = new Indicacion;
			$nuevo->usuario = Auth::user()->id;
			$nuevo->caso = $ocultar->caso;
			$nuevo->fecha_creacion = Carbon::parse($ocultar->fecha_creacion)->format("Y-m-d H:i:s");
			$nuevo->indicacion = $request->comentario;
			$nuevo->visible =true;
			$nuevo->save();

			$ocultar->usuario_modifica = Auth::user()->id;
			$ocultar->fecha_modificacion = Carbon::now();
			$ocultar->visible =false;
			$ocultar->save();
			
			DB::commit();	
			return response()->json(["exito"=>"Indicación editada correctamente"]);

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		return response()->json(["error"=>"Error al editar inidicación"]);
	}


	public function mostrarFechas($caso){
		DB::beginTransaction();
		try{
			/* Fechas a modificar */
			$datos = DB::table('casos')
			->select('casos.id as idCaso','casos.paciente','casos.fecha_ingreso2','casos.indicacion_hospitalizacion','casos.fecha_termino',DB::raw('MAX(t_historial_ocupaciones.fecha_ingreso_real) as fecha_ingreso_real'),'lista_transito.fecha','t_historial_ocupaciones.id as historialId','lista_transito.id_lista_transito as listaTransitoId','camas.id_cama as cama','salas.nombre as sala','unidades_en_establecimientos.alias as servicio')
            ->leftJoin('t_historial_ocupaciones', 'casos.id', '=', 't_historial_ocupaciones.caso')
            ->leftJoin('lista_transito', 'casos.id', '=', 'lista_transito.caso')
            ->leftJoin('camas', 't_historial_ocupaciones.cama', '=', 'camas.id')
            ->leftJoin('salas', 'camas.sala', '=', 'salas.id')
            ->leftJoin('unidades_en_establecimientos', 'salas.establecimiento', '=', 'unidades_en_establecimientos.id')
			->where('casos.id', $caso)
			->orWhere(function($query) {
                $query->whereNull('t_historial_ocupaciones.motivo')
				->where('t_historial_ocupaciones.motivo','<>', 'corrección cama');
            })
			->groupBy('casos.id','casos.paciente','casos.fecha_ingreso2','casos.indicacion_hospitalizacion','casos.fecha_termino','lista_transito.fecha','t_historial_ocupaciones.id','lista_transito.id_lista_transito','camas.id_cama','salas.nombre','unidades_en_establecimientos.alias')
			->get();
	
	
			DB::commit();	
			return response()->json($datos);

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
		return response()->json(["error"=>"Error al obtener fechas"]);
	}

	public function obtieneMayorMenorFecha($caso,$tipo){
		DB::beginTransaction();
		try{
			/* Fecha mayor o menor */
			$buscarFechaMenorMayor = '';
			if($tipo == 'mayor'){
				$buscarFechaMenorMayor = 'GREATEST';
			}elseif($tipo == 'menor'){
				$buscarFechaMenorMayor = 'LEAST';
			}else{
				return '';
			}
			//GREATEST or LEAST
			$datos = DB::table('casos')
			->select(DB::raw("$buscarFechaMenorMayor(casos.fecha_ingreso2,casos.indicacion_hospitalizacion,casos.fecha_termino,t_historial_ocupaciones.fecha_ingreso_real,lista_transito.fecha) as minMax"))
            ->leftJoin('t_historial_ocupaciones', 'casos.id', '=', 't_historial_ocupaciones.caso')
            ->rightJoin('lista_transito', 'casos.id', '=', 'lista_transito.caso')
			->where('casos.id', $caso)
			->limit(1)
			->get();
			
		
	
			DB::commit();	

			
			return (isset($datos[0]->minmax) && $datos[0]->minmax != null && $datos[0]->minmax != '')?$datos[0]->minmax:'';

		}catch(Exception $e){
			DB::rollback();
			throw $e;
		}
	}

	public function obtenerCasosMenoresMayores(Request $request){
		$ultimoCaso = Caso::select('id')
		->where('paciente',$request->paciente)
		->orderByDesc('id')
		->limit(1)
		->get();


		$casosMenores = Caso::select('id')
		->where('paciente',$request->paciente)
		->where('id','<',$request->idCaso)
		->orderByDesc('id')
		->limit(1)
		->get();

		$casosMayores = Caso::select('id')
		->where('paciente',$request->paciente)
		->where('id','>',$request->idCaso)
		->orderBy('id', 'asc')
		->limit(1)
		->get();



		return array("ultimoCaso"=>(isset($ultimoCaso[0]->id))?$ultimoCaso[0]->id:'',"casosMenores"=>(isset($casosMenores[0]->id))?$casosMenores[0]->id:'',"casosMayores" =>(isset($casosMayores[0]->id))?$casosMayores[0]->id:'');

	}

	//modificar
	public function validarAsignacion(Request $request){
		try {
			//verifica que el si es el ultimo caso sino verifica que las fechas no sean a las ultimas ingresadas
				$casosMenorMayor = $this->obtenerCasosMenoresMayores($request);
			if($casosMenorMayor != '' && $casosMenorMayor['ultimoCaso'] > $request->idCaso){
				// Asignación de cama
				if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha de solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha indicación médica de hospitalización"]);
						}
					}
						
					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha de hospitalización"]);
						}
					}
					
					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}

					// $ultimaFecha = ListaTransito::selectRaw('min(fecha) as fecha')
					// ->leftJoin('casos', 'casos.id', '=', 'lista_transito.caso')
					// ->where('lista_transito.caso','>',$request->idCaso)
					// ->where('casos.paciente',$request->paciente)->first();
					// $ultimaFechaFormatiada = Carbon::parse($ultimaFecha->fecha)->format('d-m-Y H:i:s');
					
					// if($fecha_1 > $ultimaFecha->fecha){
					// 	return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $ultimaFechaFormatiada"]);
					// }

							//verifica si tiene caso mayor
					if($casosMenorMayor['casosMayores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMayor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMayores'],'menor');
						$fechaMayor_1 =  Carbon::parse($fechaMayor)->format("Y-m-d H:i:s");
						$fechaMayorFormatiada =  Carbon::parse($fechaMayor)->format("d-m-Y H:i:s");
						if($fechaMayor != '' && $fecha_1 > $fechaMayor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaMayorFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}
				return response()->json(["valid" => true]);
			}else{
				//verifica que las fechas no sean mayores 
				// Asignación de cama
				if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
					// $ultimaFecha = ListaTransito::select('fecha')->where('caso',$request->idCaso)->first();
					// $ultimaFechaFormatiada = Carbon::parse($ultimaFecha->fecha)->format('d-m-Y H:i:s');

					// if($fecha_1 > $ultimaFecha->fecha){
					// 	return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $ultimaFechaFormatiada"]);
					// }

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha de solicitud de cama"]);
						}
					}
					
					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha indicación médica de hospitalización"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha de hospitalización"]);
						}
					}

					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}
	
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
					

				}

				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}
	public function validarModFechas(Request $request){
		try {
			//verifica que el si es el ultimo caso sino verifica que las fechas no sean a las ultimas ingresadas
			$casosMenorMayor = $this->obtenerCasosMenoresMayores($request);

			if($casosMenorMayor != '' && $casosMenorMayor['ultimoCaso'] > $request->idCaso){
				// Hospitalización 
				if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha indicación médica de hospitalización"]);
						}
					}

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha asignación de cama"]);
						}
					}
					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}

					if(isset($request->fecha_hospitalizacion_anterior) && $request->fecha_hospitalizacion_anterior != ''){

						//Se busca el primer historial valido para restringir hacias abajo
						$fecha_actual = THistorialOcupaciones::select('fecha', 't_historial_ocupaciones.id')
								->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
								->where('t_historial_ocupaciones.caso','=',$request->idCaso)
								->where(function ($query)  {
									$query->where('t_historial_ocupaciones.motivo','<>', 'corrección cama')
										->orWhereNull('t_historial_ocupaciones.motivo');
								})
								->orderBy("t_historial_ocupaciones", 'asc')
								->first();

								/* Log::info("anterior");
								Log::info($fecha_actual); */
						//Buscar fecha siguiente 
						$fecha_siguiente = THistorialOcupaciones::select('fecha')
								->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
								->where('t_historial_ocupaciones.caso','=',$request->idCaso)
								->where('t_historial_ocupaciones.id','>',$fecha_actual->id)
								->orderBy("t_historial_ocupaciones", 'asc')
								->first();
						
								/* Log::info("siguiente");
								Log::info($fecha_siguiente); */
						
						if($fecha_actual != null && $fecha_1 < $fecha_actual->fecha){
							//Tiene que ser mayor a la fecha en que se asigno cama y esta se encuentre en el primer historial -> columna fecha
							$fechaAnteriorFormatiada = Carbon::parse($fecha_actual->fecha)->format('d-m-Y H:i:s');
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaAnteriorFormatiada "]);
						}

						if($fecha_siguiente != null && $fecha_1 > $fecha_siguiente->fecha){
							//Si se encuentra un historial sobre el primer historial de hospitalizacion, este pasa a ser su restriccion superior y no puede pasar de esa fecha
							$fechaSiguienteFormatiada = Carbon::parse($fecha_siguiente->fecha)->format('d-m-Y H:i:s');
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaSiguienteFormatiada "]);
						}
					}

					// $ultimaFecha = THistorialOcupaciones::selectRaw('min(fecha_ingreso_real) as fecha')
					// ->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
					// ->where('t_historial_ocupaciones.caso','>',$request->idCaso)
					// ->where('casos.paciente',$request->paciente)->first();
					// $ultimaFechaFormatiada = Carbon::parse($ultimaFecha->fecha)->format('d-m-Y H:i:s');

					// if($fecha_1 > $ultimaFecha->fecha){
					// 	return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $ultimaFechaFormatiada"]);
					// }

			
	
					//verifica si tiene caso mayor
					if($casosMenorMayor['casosMayores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMayor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMayores'],'menor');
						$fechaMayor_1 =  Carbon::parse($fechaMayor)->format("Y-m-d H:i:s");
						$fechaMayorFormatiada =  Carbon::parse($fechaMayor)->format("d-m-Y H:i:s");
						if($fechaMayor != '' && $fecha_1 > $fechaMayor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaMayorFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}
				return response()->json(["valid" => true]);
			}else{
				//verifica que las fechas no sean mayores 
					// Hospitalización 
				if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
					
					// $ultimaFecha = THistorialOcupaciones::select('fecha_ingreso_real as fecha')->where('caso',$request->idCaso)->first();
					// $ultimaFechaFormatiada = Carbon::parse($ultimaFecha->fecha)->format('d-m-Y H:i:s');
					
					// if($fecha_1 > $ultimaFecha->fecha){
					// 	return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $ultimaFechaFormatiada"]);
					// }

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha indicación médica de hospitalización"]);
						}
					}

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha asignación de cama"]);
						}
					}
					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}
	
					if(isset($request->fecha_hospitalizacion_anterior) && $request->fecha_hospitalizacion_anterior != ''){
						//Se busca el primer historial valido para restringir hacias abajo
						$fecha_actual = THistorialOcupaciones::select('fecha', 't_historial_ocupaciones.id')
								->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
								->where('t_historial_ocupaciones.caso','=',$request->idCaso)
								->where(function ($query)  {
									$query->where('t_historial_ocupaciones.motivo','<>', 'corrección cama')
										->orWhereNull('t_historial_ocupaciones.motivo');
								})
								->orderBy("t_historial_ocupaciones", 'asc')
								->first();

						//Buscar fecha siguiente del paciente en caso de tenerlo
						$fecha_siguiente = THistorialOcupaciones::select('fecha')
								->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
								->where('t_historial_ocupaciones.caso','=',$request->idCaso)
								->where('t_historial_ocupaciones.id','>',$fecha_actual->id)
								->orderBy("t_historial_ocupaciones", 'asc')
								->first();

						if($fecha_actual != null && $fecha_1 < $fecha_actual->fecha){
							//Tiene que ser mayor a la fecha en que se asigno cama y esta se encuentre en el primer historial -> columna fecha
							$fechaAnteriorFormatiada = Carbon::parse($fecha_actual->fecha)->format('d-m-Y H:i:s');
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaAnteriorFormatiada"]);
						}

						if($fecha_siguiente != null && $fecha_1 > $fecha_siguiente->fecha){
							//Si se encuentra un historial sobre el primer historial de hospitalizacion, este pasa a ser su restriccion superior y no puede pasar de esa fecha
							$fechaSiguienteFormatiada = Carbon::parse($fecha_siguiente->fecha)->format('d-m-Y H:i:s');
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaSiguienteFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}

				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaSolicitud(Request $request){
		try {
			//verifica que el si es el ultimo caso sino verifica que las fechas no sean a las ultimas ingresadas
			$casosMenorMayor = $this->obtenerCasosMenoresMayores($request);

			if($casosMenorMayor != '' && $casosMenorMayor['ultimoCaso'] > $request->idCaso){
				// //Solicitud de cama
				if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha de indicación médica"]);
						}
					}
					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}
					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}
					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}

					//verifica si tiene caso mayor
					if($casosMenorMayor['casosMayores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMayor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMayores'],'menor');
						$fechaMayor_1 =  Carbon::parse($fechaMayor)->format("Y-m-d H:i:s");
						$fechaMayorFormatiada =  Carbon::parse($fechaMayor)->format("d-m-Y H:i:s");
						if($fechaMayor != '' && $fecha_1 > $fechaMayor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaMayorFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}
					return response()->json(["valid" => true]);
			}else{
				if(isset($request->fecha_asignacion_medica)  && $request->fecha_asignacion_medica != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
					$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
					if($fecha_1 < $fecha_asignacion_medica){
						return response()->json(["valid" => false, "message" => "Fecha debe ser mayor a fecha de indicación médica"]);
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}

					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}

					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}
				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaIndicacionMedica(Request $request){
		try {
			//verifica que el si es el ultimo caso sino verifica que las fechas no sean a las ultimas ingresadas
			$casosMenorMayor = $this->obtenerCasosMenoresMayores($request);
		

			if($casosMenorMayor != '' && $casosMenorMayor['ultimoCaso'] > $request->idCaso){
				//Fecha indicación médica de hospitalización		
				if(isset($request->fecha_asignacion_medica)  && $request->fecha_asignacion_medica != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
								
					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha de solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}

					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 >= $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}
 
					//verifica si tiene caso mayor
					if($casosMenorMayor['casosMayores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMayor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMayores'],'menor');
						$fechaMayor_1 =  Carbon::parse($fechaMayor)->format("Y-m-d H:i:s");
						$fechaMayorFormatiada =  Carbon::parse($fechaMayor)->format("d-m-Y H:i:s");
						if($fechaMayor != '' && $fecha_1 > $fechaMayor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaMayorFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}	
				return response()->json(["valid" => true]);
				
			}else{
				if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
					$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
					if($fecha_1 > $fecha_solicitud_cama){
						return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha de solicitud de cama"]);
					}

					
					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}

					if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
						$fecha_egreso = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");
						if($fecha_1 > $fecha_egreso){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha egreso"]);
						}
					}

					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}

				}

				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			Log::info($e);
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}

	public function validarFechaEgresoBPaciente(Request $request){
		try {
			//verifica que el si es el ultimo caso sino verifica que las fechas no sean a las ultimas ingresadas
			$casosMenorMayor = $this->obtenerCasosMenoresMayores($request);
			//verifica que existen mas de 1 caso
			if($casosMenorMayor != '' && $casosMenorMayor['ultimoCaso'] > $request->idCaso){
				if(isset($request->fecha_egreso) && $request->fecha_egreso != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha indicación médica de hospitalización"]);
						}
					}

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}

					
					//verifica si tiene caso mayor
					if($casosMenorMayor['casosMayores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMayor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMayores'],'menor');
						$fechaMayor_1 =  Carbon::parse($fechaMayor)->format("Y-m-d H:i:s");
						$fechaMayorFormatiada =  Carbon::parse($fechaMayor)->format("d-m-Y H:i:s");
						if($fechaMayor != '' && $fecha_1 > $fechaMayor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser menor a $fechaMayorFormatiada"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}

					$ultimaFechaAnterior = THistorialOcupaciones::selectRaw('max(t_historial_ocupaciones.fecha) as fecha')
					->leftJoin('casos', 'casos.id', '=', 't_historial_ocupaciones.caso')
					->where('t_historial_ocupaciones.caso','<=',$request->idCaso)
					->where('casos.paciente',$request->paciente)
					->whereNotNull('t_historial_ocupaciones.fecha_liberacion')->first();
					
					//verifica que exista una fecha anterior y ademas que no sea menor
					if($ultimaFechaAnterior && $ultimaFechaAnterior->fecha != ''){
						$ultimaFechaFormatiada = Carbon::parse($ultimaFechaAnterior->fecha)->format("Y-m-d H:i:s");
						$ultimaFechaFormatiada2 = Carbon::parse($ultimaFechaAnterior->fecha)->format("d-m-Y H:i:s");
						
						if($fecha_1 < $ultimaFechaFormatiada){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $ultimaFechaFormatiada2"]);
						}
					}
				}	
				
				return response()->json(["valid" => true]);
			}else{
				//verifica que exista una fecha anterior y que no sea menor
					if(isset($request->fecha_egreso)  && $request->fecha_egreso != ''){
					$fecha_1 = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_egreso)->format("Y-m-d H:i:s");

					if(isset($request->fecha_asignacion_medica) && $request->fecha_asignacion_medica != ''){
						$fecha_asignacion_medica = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion_medica)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion_medica){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha indicación médica de hospitalización"]);
						}
					}

					if(isset($request->fecha_solicitud_cama) && $request->fecha_solicitud_cama != ''){
						$fecha_solicitud_cama = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_solicitud_cama)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_solicitud_cama){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha solicitud de cama"]);
						}
					}

					if(isset($request->fecha_asignacion) && $request->fecha_asignacion != ''){
						$fecha_asignacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_asignacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_asignacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha asignación de cama"]);
						}
					}

					if(isset($request->fecha_hospitalizacion) && $request->fecha_hospitalizacion != ''){
						$fecha_hospitalizacion = Carbon::createFromFormat("d-m-Y H:i", $request->fecha_hospitalizacion)->format("Y-m-d H:i:s");
						if($fecha_1 < $fecha_hospitalizacion){
							return response()->json(["valid" => false, "message" => "Fecha debe ser menor a fecha hospitalización"]);
						}
					}
					
					$ultimaFecha = THistorialOcupaciones::select('fecha')->where('caso',$request->idCaso)->whereNotNull('fecha_liberacion')->first();
					if($ultimaFecha && $ultimaFecha->fecha != ''){
						$ultimaFechaFormatiada = Carbon::parse($ultimaFecha->fecha)->format("Y-m-d H:i:s");
						$ultimaFechaFormatiada2 = Carbon::parse($ultimaFecha->fecha)->format("d-m-Y H:i:s");

						if($fecha_1 < $ultimaFechaFormatiada){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $ultimaFechaFormatiada2"]);
						}
					}
					//verifica si tiene caso menor
					if($casosMenorMayor['casosMenores'] != ''){
						//si tiene caso menor busca la fecha mayor de dicho caso
						$fechaMenor  = $this->obtieneMayorMenorFecha($casosMenorMayor['casosMenores'],'mayor');
						$fechaMenor_1 =  Carbon::parse($fechaMenor)->format("Y-m-d H:i:s");
						$fechaMenorFormatiada =  Carbon::parse($fechaMenor)->format("d-m-Y H:i:s");
						if($fechaMenor != '' && $fecha_1 < $fechaMenor_1){
							return response()->json(["valid" => false, "message" => " La fecha debe ser mayor a $fechaMenorFormatiada"]);
						}
					}
				}	
				
				
				return response()->json(["valid" => true]);
			}

		}catch(Exception $e) {
			return response()->json(["valid" => false, "message" => "Formato de fecha inválido"]);
		}
	}



	public function addFechas(Request $request){
	
		try{
			DB::beginTransaction();
			if($request->idCaso != ''){
				$auth_user_id = Auth::user()->id;
				$fecha_actual = Carbon::now()->format("Y-m-d H:i:s");

				$casoModificado = Caso::find($request->idCaso);
				if($request->fecha_solicitud_cama != ''){

					$solicitudCamaEdicion = new EdicionFechas;
					$solicitudCamaEdicion->id_caso = $request->idCaso;
					$solicitudCamaEdicion->tipo_tabla = "solicitud_cama";
					$solicitudCamaEdicion->id_tabla = $casoModificado->id;
					$solicitudCamaEdicion->fecha_antigua = $casoModificado->fecha_ingreso2;
					$solicitudCamaEdicion->fecha_nueva = $request->fecha_solicitud_cama;
					$solicitudCamaEdicion->id_usuario_modifica = $auth_user_id;
					$solicitudCamaEdicion->fecha_modificacion = $fecha_actual;
					$solicitudCamaEdicion->save();

			
					$casoModificado->fecha_ingreso2 = $request->fecha_solicitud_cama;		
					

				}
				if($request->fecha_asignacion_medica != ''){
					$asignacionCamaEdicion = new EdicionFechas;
					$asignacionCamaEdicion->id_caso = $request->idCaso;
					$asignacionCamaEdicion->tipo_tabla = "indicacion_medica";
					$asignacionCamaEdicion->id_tabla = $casoModificado->id;
					$asignacionCamaEdicion->fecha_antigua = $casoModificado->indicacion_hospitalizacion;
					$asignacionCamaEdicion->fecha_nueva = $request->fecha_asignacion_medica;
					$asignacionCamaEdicion->id_usuario_modifica = $auth_user_id;
					$asignacionCamaEdicion->fecha_modificacion = $fecha_actual;
					$asignacionCamaEdicion->save();

					$casoModificado->indicacion_hospitalizacion = $request->fecha_asignacion_medica;			
				}
	
				if($request->fecha_egreso != ''){
					$egresoEdicion = new EdicionFechas;
					$egresoEdicion->id_caso = $request->idCaso;
					$egresoEdicion->tipo_tabla = "egreso";
					$egresoEdicion->id_tabla = $casoModificado->id;
					$egresoEdicion->fecha_antigua = $casoModificado->fecha_termino;
					$egresoEdicion->fecha_nueva = $request->fecha_egreso;
					$egresoEdicion->id_usuario_modifica = $auth_user_id;
					$egresoEdicion->fecha_modificacion = $fecha_actual;
					$egresoEdicion->save();

					$casoModificado->fecha_termino = $request->fecha_egreso;
				}
				$casoModificado->save();
		

			if($request->idHistorial != ''){
				$historialOcupacion = THistorialOcupaciones::find($request->idHistorial);

				//Buscar todos los historiales del caso con la misma fecha y cambiarselas a todos, para evitar incongruencias 
				if(isset($historialOcupacion->fecha_ingreso_real)){
					$historialesOcupaciones = THistorialOcupaciones::where('caso',$request->idCaso)
							->where('fecha_ingreso_real',$historialOcupacion->fecha_ingreso_real)
							->where(function ($query)  {
								$query->whereNull('t_historial_ocupaciones.motivo')
								->orWhere('t_historial_ocupaciones.motivo','<>', 'corrección cama');
							})
							->orderBy("id","asc")
							->get();
				}
				if($request->fecha_hospitalizacion != ''){
					foreach ($historialesOcupaciones as $key => $historial) {
						$egresoEdicion = new EdicionFechas;
						$egresoEdicion->id_caso = $request->idCaso;
						$egresoEdicion->tipo_tabla = "historial_ocupaciones";
						$egresoEdicion->id_tabla = $historial->id;
						$egresoEdicion->fecha_antigua = $historial->fecha_ingreso_real;
						$egresoEdicion->fecha_nueva = $request->fecha_hospitalizacion;
						$egresoEdicion->id_usuario_modifica = $auth_user_id;
						$egresoEdicion->fecha_modificacion = $fecha_actual;
						$egresoEdicion->save();

						$historial->fecha_ingreso_real = $request->fecha_hospitalizacion;
						$historial->save();
					}
				}

				
			}
			if($request->idListaTransito != ''){
				$listaTransito = ListaTransito::find($request->idListaTransito);
				if($request->fecha_asignacion != ''){
					$primerHistorialOcupacion = THistorialOcupaciones::where('caso',$request->idCaso)
							->where(function ($query)  {
								$query->whereNull('t_historial_ocupaciones.motivo')
								->orWhere('t_historial_ocupaciones.motivo','<>', 'corrección cama');
							})
							->orderBy("id","asc")
							->first();
					$listaTransitoEdicion = new EdicionFechas;
					$listaTransitoEdicion->id_caso = $request->idCaso;
					$listaTransitoEdicion->tipo_tabla = "asignacion_cama";
					$listaTransitoEdicion->id_tabla = $listaTransito->id_lista_transito;
					$listaTransitoEdicion->fecha_antigua = $listaTransito->fecha;
					$listaTransitoEdicion->fecha_nueva = $request->fecha_asignacion;
					$listaTransitoEdicion->id_usuario_modifica = $auth_user_id;
					$listaTransitoEdicion->fecha_modificacion = $fecha_actual;
					$listaTransitoEdicion->save();

					$primerHistorialOcupacion->fecha = $request->fecha_asignacion;
					$primerHistorialOcupacion->save();
					$listaTransito->fecha = $request->fecha_asignacion;
					$listaTransito->save();
				}
			}	
		}
			DB::commit();
			return response()->json(["exito"=>"las fechas ingresadas se han modificado con exito"]);
		
		}catch(Exception $e){
			Log::info($e);
			DB::rollback();
			return response()->json(["error"=>"Error al modificar las fechas ingresadas"]);
		}
		
	
	}

}

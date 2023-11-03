<?php

namespace App\Http\Controllers;

use App\Models\THistorialOcupaciones;
use Illuminate\Http\Request;

use App\Models\Establecimiento;
use App\Models\Comuna;
use Carbon\Carbon;
use App\Models\UnidadEnEstablecimiento;
use App\Models\DocumentoDerivacionCaso;
use App\Models\HospitalizacionDomiciliaria;
use Excel;
use Auth;
use DB;
use Funciones;
use View;
use Session;
use Mail;
use File;
use Vsmoraes\Pdf\Pdf as pdfx;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\HistorialDiagnostico;
use App\Models\Caso;
use App\Models\Examen;
use App\Models\ListaTransito;
use App\Models\ListaEspera;
use App\Models\Cama;
use App\Models\TipoCama;
use App\Models\HistorialEspecialidades;
use App\Models\Procedencia;
use Log;
use Consultas;
use DateTime;
use PDF;

use App\Http\Controllers\IndexController;

class EstadisticasCamasController extends EstadisticasController{

	private $pdf1;
	private $pdf2;
	private $pdf3;
	private $pdf4;
	private $pdf5;
	private $pdf6; //este es para cuando se muestra el pdf desde la web
	private $pdf = [];

    public function __construct(Pdfx $pdf1, Pdfx $pdf2, Pdfx $pdf3, Pdfx $pdf4, Pdfx $pdf5, Pdfx $pdf6)
    {
		$this->pdf1 = $pdf1;
		$this->pdf2 = $pdf2;
		$this->pdf3 = $pdf3;
		$this->pdf4 = $pdf4;
		$this->pdf5 = $pdf5;
		$this->pdf6 = $pdf6;
		$this->pdf = array(
			$this->pdf1,
			$this->pdf2,
			$this->pdf3,
			$this->pdf4,
			$this->pdf5
		);
	}
	public function estudioPrevalencia(){
		/* obtener datos de paciente hospitalizados hasta las 9:00 */
		$fecha = Carbon::now()->format("Y-m-d 09:00:00");
		$now = Carbon::now()->format("Y_m_d_H_i_s");
		$datosHospitalizados =DB::select(DB::raw("select 
				t2.fecha_ingreso_real as hospitalizacion, 
				p.rut,
				p.dv, 
				p.nombre,
				p.apellido_paterno,
				p.apellido_materno,
				p.fecha_nacimiento,
				p.sexo,
				c.ficha_clinica,
				u.alias as nombre_servicio,
				p.id as id_paciente,
				u.id as id_servicio,
				s.nombre as nombre_sala,
				ca.id_cama 
		from (select h.paciente, h.id
				from historial_ocupaciones_vista h
				inner join t_historial_ocupaciones t on t.id = h.id
				where
				t.fecha_ingreso_real <='2020-08-11 09:00:00'
				and (  
						(h.fecha_liberacion >= '2020-08-11 09:00:00' and h.fecha <= '2020-08-11 09:00:00') or 
						(h.fecha_liberacion is null and t.motivo is null and h.fecha <= '2020-08-11 09:00:00')
				) ) h2
		inner join t_historial_ocupaciones t2 on h2.id = t2.id
		inner join casos as c on c.id = t2.caso
		inner join camas as ca on ca.id = t2.cama
		inner join salas as s on s.id = ca.sala
		inner join unidades_en_establecimientos as u on u.id = s.establecimiento
		inner join pacientes as p on p.id = h2.paciente"));

		foreach($datosHospitalizados as $dato){
			/* calcular dias cama acumulados */
			$dato->dias_acumulado = Carbon::now()->diffInDays(Carbon::parse($dato->hospitalizacion));
			$dato->hospitalizacion = Carbon::parse($dato->hospitalizacion)->format("d-m-Y H:i:s");
			
			$dv =($dato->dv == 10)?'K':$dato->dv;
			if($dato->rut){
				$dato->rutcompleto = "$dato->rut-$dv";
			}else{
				$dato->rutcompleto = "No posee";
			}
			/* calcular edad */
			$dato->edad = ($dato->fecha_nacimiento)?Carbon::now()->diffInYears(Carbon::parse($dato->fecha_nacimiento)):"";

			/* antecedente de casos anteriores */
			$caso = Caso::where("paciente",$dato->id_paciente)->orderBy("id", "desc")->get();

			if(count($caso) > 1){
				$fecha_termino = Carbon::parse($caso[1]->fecha_termino);
				$diffenDias = Carbon::now()->diffInDays($fecha_termino);
				if($diffenDias <=30){
					$dato->ultimpo_egreso = $fecha_termino->format("d-m-Y H:i:s");
				}else{
					$dato->ultimpo_egreso = "No posee";
				}
			}else{
				$dato->ultimpo_egreso = "No posee";
			}

			/* sabe si son adulto, neo o pediatrica */
			if($dato->id_servicio == 191 || $dato->id_servicio == 192 || $dato->id_servicio == 193){
				$dato->nombre_servicio .=" Neonatologia"; 
			}else if($dato->id_servicio == 188 || $dato->id_servicio == 189){
				$dato->nombre_servicio .= " Pediatria";
			}else if($dato->id_servicio == 177 || $dato->id_servicio == 178 || $dato->id_servicio == 202){
				$dato->nombre_servicio .= " Adulto";
			}
			

		}
		
		
		/* generar excel con datos*/

		Excel::create('Camas'.$now, function($excel) use ($datosHospitalizados)  {
			$excel->sheet('Camas', function($sheet)  use ($datosHospitalizados) {
				
				$sheet->mergeCells('A1:N1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {
					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");
				
				});
			
				$sheet->loadView('Estadisticas.prevalencia', [
					"datos" => $datosHospitalizados
				]);
			});
		})->store('xls');

		
		
		//$excelPath = storage_path()."\\exports\Camas".$now.".xls";//windows
		$excelPath = storage_path()."/exports/Camas".$now.".xls";//linyx
		/* Enviar excel por correo */
		$data = array(  
			"nombre" => "NOMBRE",			
			"mensaje" => "MENSAJE",
			"correo" => "CORREO"
		);

		$correos = array("earellano.heger@gmail.com");

		$asunto= "Estudio prevalencia";
		$destinatario = "DESTINATARIO";
		
		Mail::send('emails.CorreoPrevalencia',$data, function($message) use ($correos, $destinatario, 
		$asunto, $excelPath){
			$message->to($correos)
					//->from($correos)
					->subject($asunto)
					->attach($excelPath); 
		});

		return "enviado";
		
	}

	public function calcularKmeans(Request $request){

		//$ncluster debe llegar desde la interfaz
		$ncluster = $request->grupo; //n° clusters Ingresar valor
		$a1 = 0; //index atributo 1 siempre igual
		$a2 = 1; //index atributo 2 siempre igual

		$id_cie_10 = $request->hidden_enfermedad;

		if($request->añoInicio){
			$inicio = 'first day of January '.$request->añoInicio;
			$fin= 'last day of December '.$request->año;
		}else{
			$inicio = 'first day of January '.$request->año;
			$fin= 'last day of December '.$request->año;
		}
		$inicio = Carbon::parse($inicio);
		$fin=Carbon::parse($fin);

		$query = DB::table("casos_con_datos_paciente_vista")
					->select('edad', 'tiempo_estada')
					->where('id_cie_10', $id_cie_10)
					->where('fecha_ingreso','<',$fin)
					->where('fecha_ingreso','>=',$inicio)
					/* ->where('establecimiento', $establecimiento) */
					->get();

		$datos=array();

		foreach( $query as $key => $q){
			$datos[$key][0]= $q->edad;
			$datos[$key][1]= $q->tiempo_estada;
		}

		$clusters = Funciones::kmeans($ncluster, $datos);

		for ($j=0; $j<$ncluster;$j++){
			$arrTemp= Funciones::obtener_atributo_por_cluster2($datos,$clusters,$j, $a1, $a2);
			$arr[$j]=$arrTemp;
		}

		return $arr;
	}


	public function calcularDistEsp(Request $request){


		$id_cie_10 = $request->hidden_enfermedad;

		if($request->añoInicio){
			$inicio = 'first day of January '.$request->añoInicio;
			$fin= 'last day of December '.$request->año;
		}else{
			$inicio = 'first day of January '.$request->año;
			$fin= 'last day of December '.$request->año;
		}
		$inicio = Carbon::parse($inicio);
		$fin=Carbon::parse($fin);

		$notComuna = DB::table('casos_con_comuna_vista')
						->select('id_comuna')
						->where('id_cie_10', $id_cie_10)
						->where('fecha_ingreso','<',$fin)
						->where('fecha_ingreso','>=',$inicio)
						->groupBy('id_comuna')
						->get();

		$response_notcomuna = [];
		foreach($notComuna as $no ){
			array_push($response_notcomuna, $no->id_comuna);
		}

		$query = DB::table('comuna')->select('nombre_comuna as comuna', 'id_comuna as id', DB::raw("0 as casos"))
						->whereNotIn('id_comuna', $response_notcomuna)
						->groupBy('id_comuna', 'nombre_comuna')
						->get();

		$query_union = DB::table('casos_con_comuna_vista')
						->select('nombre_comuna as comuna', 'id_comuna as id', DB::raw("COUNT(*) as casos"))
						->where('id_cie_10', $id_cie_10)
						->where('fecha_ingreso','<',$fin)
						->where('fecha_ingreso','>=',$inicio)
						->groupBy('id_comuna', 'nombre_comuna')
						/* ->union($response_query) */
						->get();

		$query_final = 	$query_union->union($query);

		$datos=array();

		foreach( $query_final as $key => $qf){

			$datos[$key][0]= $qf->comuna;
			$datos[$key][1]= $qf->id;
			$datos[$key][2]= $qf->casos;
		}

		$enf[0][0]= $id_cie_10;

		return Funciones::distribucionEspacial($datos,$enf);
	}


	public function calcularKnox(Request $request){
		//obtener datos

		if($request->añoInicio){
			$inicio = 'first day of January '.$request->añoInicio;
			$fin= 'last day of December '.$request->año;
		}else{
			$inicio = 'first day of January '.$request->año;
			$fin= 'last day of December '.$request->año;
		}
		$inicio = Carbon::parse($inicio);
		$fin=Carbon::parse($fin);
		$datos = DB::table('casos_georeferenciados_vista')
					->select('fecha_ingreso', 'longitud','latitud')
					->where('id_comuna',$request->comuna)
					->where('id_cie_10', $request->hidden_enfermedad)
					->where('fecha_ingreso','<',$fin)
					->where('fecha_ingreso','>=',$inicio)
					->get();

		$response = [];
		foreach($datos as $dato){
			$response [] =[
				"fecha_ingreso" => $dato->fecha_ingreso,
				"latitud" => $dato->latitud,
				"longitud" => $dato->longitud
			];
		}

		$distancia_espacio=0;
		$distancia_tiempo=0;
		$cercanos_en_tiempo_y_espacio=0;//A
		$cercanos_en_espacio=0;//B
		$cercanos_en_tiempo=0;//C
		$no_cercanos=0;//D

		//PARAMETROS TEMPORALES Y ESPACIALES (se pueden modificar, osea)
		$umbral_dist_espacial_max = $request->espacio;//en metros
		$umbral_dist_temporal_max = $request->tiempo;//en dias
		$a=array(); $b=array();
		$contador=0;

		for ($i=0; $i <(count($datos)) ; $i++) {

			$n=1;
			for ($k=$i; $k < (count($datos)-1); $k++) {

				//Para distancia Haversine
				$distancia_espacio = Funciones::distancia_haversine($response[$i]['latitud'], $response[$i]['longitud'], $response[$i+$n]['latitud'], $response[$i+$n]['longitud']);


				$distancia_tiempo=Funciones::distancia_temporal($response[$i]['fecha_ingreso'], $response[$i+$n]['fecha_ingreso']);

				//CONTAR LOS VALORES DE A MATRIZ
				if (($distancia_tiempo<=$umbral_dist_temporal_max) && ($distancia_espacio<=$umbral_dist_espacial_max)){
					$cercanos_en_tiempo_y_espacio++;
				}else {
				if (($distancia_tiempo<=$umbral_dist_temporal_max) && ($distancia_espacio>$umbral_dist_espacial_max)){
					$cercanos_en_tiempo++;
				}else {
					if (($distancia_tiempo>$umbral_dist_temporal_max) && ($distancia_espacio<=$umbral_dist_espacial_max)){
						$cercanos_en_espacio++;
					}else {
						$no_cercanos++;
					}
				}
				}
				$n++;
				$contador++;

			}
		}

		$numero_pares_esperados=0; //(λ)
		$a=$cercanos_en_tiempo_y_espacio;//A
		$b=$cercanos_en_espacio;//B
		$c=$cercanos_en_tiempo;//C
		$d=$no_cercanos;//D
		$probabilidad = 0;

		if( ($a+$b+$c+$d) != 0){
			//numero esperado de pares cercanos en espacio y tiempo (λ)
			$numero_pares_esperados=(($a+$b)*($a+$c))/($a+$b+$c+$d); //λ

			$numero_pares_esperados=round($numero_pares_esperados,2);

			$X=$a-1; //se resta 1 por que entrega P(X<=x) -Mismo motivo de porqué se le resta 1 en la línea de abajo-
			$Lam=$numero_pares_esperados;

			$probabilidad = Funciones::poisson($X, $Lam); // ENTREGA Poisson P(X<=x) por eso se le resta a 1, se busca Poisson(λ) ≥ X

			$probabilidad = (1-round($probabilidad,2));
		}

		return response()->json([
						"contador" 						=> $contador,
						"cercanos_en_tiempo_y_espacio" 	=> $cercanos_en_tiempo_y_espacio,
						"cercanos_en_espacio"			=> $cercanos_en_espacio,
						"cercanos_en_tiempo" 			=> $cercanos_en_tiempo,
						"no_cercanos" 					=> $no_cercanos,
						"numero_pares_esperados" 		=> $numero_pares_esperados,
						"a"								=> $a,
						"probabilidad"					=> $probabilidad
					 ]);

	}


	public function estDirector(){
		$comunas = Comuna::getComunas();
		return view("NuevasEstadisticas/Director", ["comunas" => $comunas]);
	}
	public function estEstada(){
		$dias = [
			'1' => 1,
			'2' => 2,
			'3' => 3,
			'4' => 4,
			'5' => 5,
			'6' => 6,
			'7' => 7,
			'8' => 8,
			'9' => 9,
			'10' => 10,
		];

		return view("NuevasEstadisticas/Estada", ["dias"=>$dias]);
	}

	public function estadisticaCambioTurno($fechaBusqueda){
		$fecha = $fechaBusqueda;

		$inicio = Carbon::createFromFormat('d-m-Y',$fecha);
		$inicio = $inicio->format('Y-m-d');
		$despues = Carbon::createFromFormat('d-m-Y',$fecha)->addDay();
		$despues = $despues->format('Y-m-d');

		$inicio_f_archivo =  Carbon::createFromFormat('d-m-Y',$fecha)->format('dmY');
		$fin_f_archivo =  Carbon::createFromFormat('d-m-Y',$fecha)->addDay()->format('dmY');

		//turno 1
		$turno = $this->infoTurnoUno($inicio);
		$resumenTurnoUno = $this->resumenEntregaTurnos($inicio, null); 
		Log::info("-------");
		
		$totalEsperandoCamasUno = ListaEspera::cantidadPacientesTurno($inicio, null);
		$camasBasicasDisponiblesUno = Cama::camasDisponiblesPorTipo(11, $inicio." 20:00:00");
		$camasCriticasDisponiblesUno = Cama::camasDisponiblesPorTipo(13, $inicio." 20:00:00");

		//turno 2
		$turnodos = $this->infoTurnoDos($inicio,$despues);
		$resumenTurnoDos = $this->resumenEntregaTurnos($inicio, $despues);
		
		$totalEsperandoCamasDos = ListaEspera::cantidadPacientesTurno($inicio, $despues);
		$camasBasicasDisponiblesDos = Cama::camasDisponiblesPorTipo(11, $despues." 08:00:00");
		$camasCriticasDisponiblesDos = Cama::camasDisponiblesPorTipo(13, $despues." 08:00:00");

		//estilos excel
		$ancho_columnas = array( 
			'A'     => 15,
			'B'     => 15,
			'C'		=> 15,
			'D'		=> 15,
			'E'		=> 15,
			'F'		=> 15,
			'G'		=> 15,
			'H'		=> 15,
			'I'		=> 15,
			'J'		=> 15,
			'K'		=> 15,
			'l'		=> 15,
			'M'		=> 15,
			'N'		=> 15,
			'O'		=> 15,
			'P'		=> 15,
			'R'		=> 15,
			'S'		=> 15,
			'T'		=> 15,
			'U'		=> 15,
			'V'		=> 15,
			'W'		=> 15
		);
		$alto_filas = array(
			9    	=> 45,
			10     	=> 30,
			11		=> 30,
			12		=> 30,
			13		=> 30
		);
		
		//return 'oka';
		Excel::create('Turno_'.$inicio_f_archivo.'_'.$fin_f_archivo, function($excel) use ($turno, $turnodos, $fechaBusqueda, $inicio, $despues, $resumenTurnoUno, $resumenTurnoDos, $totalEsperandoCamasUno, $totalEsperandoCamasDos, $camasBasicasDisponiblesUno, $camasCriticasDisponiblesUno, $camasBasicasDisponiblesDos, $camasCriticasDisponiblesDos,$ancho_columnas,$alto_filas) {

			$excel->sheet('Turno1', function($sheet) use ($turno, $fechaBusqueda, $inicio, $resumenTurnoUno, $totalEsperandoCamasUno, $camasBasicasDisponiblesUno, $camasCriticasDisponiblesUno,$ancho_columnas,$alto_filas) {
				$sheet->mergeCells('A1:W1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,30);
				$sheet->row(1, function($row) {
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");
				});

				$sheet->setWidth($ancho_columnas);

				$sheet->setHeight($alto_filas);

				$sheet->getStyle('A1:W200')->getAlignment()->setWrapText(true);
				
				//UNIDAD EMERGENCIA HOSPITALARIA (Pacientes en lista de espera)
				$ueh_1_adulto = ListaEspera::pacientesFecha($inicio, null,1,'ADULTO'); //SERVICIO URGENCIAS 
				$ueh_1_pediatria = ListaEspera::pacientesFecha($inicio, null, 1,'PEDIATRIA'); //SERVICIO URGENCIAS

				//UNIDA DE RECUPERACION
				$ur_1_adulto = ListaEspera::pacientesFecha($inicio, null,5,'ADULTO'); //PABELLON 
				$ur_1_pediatria = ListaEspera::pacientesFecha($inicio, null, 5,'PEDIATRIA'); //PABELLON

				//INGRESOS POLI
				$ip_1_adulto = ListaEspera::pacientesFecha($inicio, null,4,'ADULTO'); //CDT 
				$ip_1_pediatria = ListaEspera::pacientesFecha($inicio, null, 4,'PEDIATRIA'); //CDT

				//INGRESOS TOTALES DEL DÍA UEH (pacientes con cama asignada)
				$it_ueh_1_adulto = ListaTransito::pacientesFecha($inicio, null,1,'ADULTO'); //SERVICIO URGENCIAS 
				$it_ueh_1_pediatria = ListaTransito::pacientesFecha($inicio, null, 1,'PEDIATRIA'); //SERVICIO URGENCIAS

				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$sheet->loadView('Estadisticas.ReporteTurnoUnoExcel',[
					"fecha_solicitud" => $fechaBusqueda,
					"hoy" => $fechaActual,
					"ueh_1_adulto" => $ueh_1_adulto,
					"ueh_1_pediatria" => $ueh_1_pediatria,
					"ur_1_adulto" => $ur_1_adulto,
					"ur_1_pediatria" => $ur_1_pediatria,
					"ip_1_adulto" => $ip_1_adulto,
					"ip_1_pediatria" => $ip_1_pediatria,
					"it_ueh_1_adulto" => $it_ueh_1_adulto,
					"it_ueh_1_pediatria" => $it_ueh_1_pediatria,
					"establecimiento" => $nombreEstablecimiento,
					"horarioMañana" => "08:00 a 19:59 horas",
					"response" => $turno,
					"resumenTurnoUno" => $resumenTurnoUno,
					"pacientesEsperaUno" => $totalEsperandoCamasUno,
					"camasBasicasUno" => $camasBasicasDisponiblesUno,
					"camasCriticasUno" => $camasCriticasDisponiblesUno
				]);
			});

			$excel->sheet('Turno2', function($sheet2) use ($turnodos, $fechaBusqueda, $inicio, $despues, $resumenTurnoDos, $totalEsperandoCamasDos, $camasBasicasDisponiblesDos, $camasCriticasDisponiblesDos,$ancho_columnas,$alto_filas) {
				$sheet2->mergeCells('A1:W1');
				$sheet2->setAutoSize(true);
				$sheet2->setHeight(1,30);
				$sheet2->row(1, function($row2) {
					$row2->setBackground('#1E9966');
					$row2->setFontColor("#FFFFFF");
					$row2->setAlignment("center");
				});

				$sheet2->setWidth($ancho_columnas);

				$sheet2->setHeight($alto_filas);

				$sheet2->getStyle('A1:W200')->getAlignment()->setWrapText(true);
				/* $sheet2->getStyle('E9:F10')->applyFromArray(array(
					'fill' => array(
						'size'  => 40
					)
				)); */

				//UNIDAD EMERGENCIA HOSPITALARIA (pacientes en lista de espera)
				$ueh_2_adulto = ListaEspera::pacientesFecha($inicio, $despues,1,'ADULTO'); //SERVICIO URGENCIAS 
				$ueh_2_pediatria = ListaEspera::pacientesFecha($inicio, $despues, 1,'PEDIATRIA'); //SERVICIO URGENCIAS

				//UNIDA DE RECUPERACION
				$ur_2_adulto = ListaEspera::pacientesFecha($inicio, $despues,5,'ADULTO'); //PABELLON 
				$ur_2_pediatria = ListaEspera::pacientesFecha($inicio, $despues, 5,'PEDIATRIA'); //PABELLON

				//INGRESOS POLI
				$ip_2_adulto = ListaEspera::pacientesFecha($inicio, $despues,4,'ADULTO'); //CDT 
				$ip_2_pediatria = ListaEspera::pacientesFecha($inicio, $despues, 4,'PEDIATRIA'); //CDT

				//INGRESOS TOTALES DEL DÍA UEH (pacientes con cama asignada)
				$it_ueh_2_adulto = ListaTransito::pacientesFecha($inicio, $despues,1,'ADULTO'); //SERVICIO URGENCIAS 
				$it_ueh_2_pediatria = ListaTransito::pacientesFecha($inicio, $despues, 1,'PEDIATRIA'); //SERVICIO URGENCIAS

				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				$sheet2->loadView('Estadisticas.ReporteTurnoDosExcel',[
					"fecha_solicitud" => $fechaBusqueda,
					"hoy" => $fechaActual,
					"ueh_2_adulto" => $ueh_2_adulto,
					"ueh_2_pediatria" => $ueh_2_pediatria,
					"ur_2_adulto" => $ur_2_adulto,
					"ur_2_pediatria" => $ur_2_pediatria,
					"ip_2_adulto" => $ip_2_adulto,
					"ip_2_pediatria" => $ip_2_pediatria,
					"it_ueh_2_adulto" => $it_ueh_2_adulto,
					"it_ueh_2_pediatria" => $it_ueh_2_pediatria,
					"establecimiento" => $nombreEstablecimiento,
					"horarioNoche" => "20:00 a 07:59 horas",
					"responseNoche" => $turnodos,
					"resumenTurnoDos" => $resumenTurnoDos,
					"pacientesEsperaDos" => $totalEsperandoCamasDos,
					"camasBasicasDos" => $camasBasicasDisponiblesDos,
					"camasCriticasDos" => $camasCriticasDisponiblesDos
				]);
			});
		})->export('xls');
	}

	public function infoTurnoUno($inicio){
		$fecha_inicio = $inicio . " 08:00:00"; 
		$fecha_fin = $inicio . " 19:59:59";

		$hospitalizados = Consultas::pacientes_hospitalizados($fecha_inicio,$fecha_fin);

		$traslados = Consultas::pacientes_trasladados($fecha_inicio, $fecha_fin);

		$transito = Consultas::pacientes_lista_transito($fecha_inicio,$fecha_fin);
			

		return ["hospitalizados" => $hospitalizados,"traslados" => $traslados, "transito" => $transito];
	}

	public function resumenEntregaTurnos($inicio, $fin){
		$fecha_anterior = Carbon::parse($inicio)->subDay(1)->format('Y-m-d');

		if($fin){ //turno noche
			$fecha0 = $inicio. " 08:00:00"; //inicio del turno anterior
			$fecha1 = $inicio." 20:00:00"; //inicio turno noche
			$fecha2 = $fin." 07:59:59"; // fin turno noche
		}else{ //turno mañana
			$fecha0 = $fecha_anterior. " 20:00:00"; // inicio del turno anterior 
			$fecha1 = $inicio." 08:00:00"; //inicio turno larga
			$fecha2 = $inicio." 19:59:59"; //fin turno larga
		}

		$establecimiento = Auth::user()->establecimiento;

		//id de la unidad, alias o nombre del a unidad y si es visible
		//visible se utilizara para cuando las unidades estan ocultas y solo mostrar cuando poseyeron pacientes dentro
		$resumen = DB::select("SELECT distinct uee.id, uee.alias, uee.visible 
			from unidades_en_establecimientos uee 
			join salas_con_camas s on s.establecimiento = uee.id 
			and uee.establecimiento = $establecimiento 
			and uee.created_at <= '$fecha2'
		");

		foreach ($resumen as $res) {

			$consulta = DB::select("SELECT u.url, u.id_area_funcional, a.nombre as nombre_area, u.alias as nombre_servicio 
				from unidades_en_establecimientos as u 
				inner join area_funcional as a on u.id_area_funcional = a.id_area_funcional 
				where id = $res->id
			");

			$res->nombre_area = (isset($consulta[0]->nombre_area)) ? $consulta[0]->nombre_area : "Sin información";

			//**************************TURNO ANTERIOR***************************
			//OCUPADAS TURNO ANTERIOR
			$ocupadas_anterior = DB::select("SELECT distinct id_servicio as id, alias as nombre, sum (count) as ocupadas_anterior from(
				select h.id_servicio, h.nombre_servicio as alias, count(*)
				from
					(
						--select caso, cama, fecha_liberacion, fecha_ingreso_real, u.alias as nombre_servicio, u.id as id_servicio
						select cama, u.alias as nombre_servicio, u.id as id_servicio
						from t_historial_ocupaciones th
						join camas as c on c.id = th.cama
						join salas as s on s.id = c.sala
						join unidades_en_establecimientos as u on u.id = s.establecimiento
						join establecimientos as e on e.id = u.establecimiento
						where e.id = $establecimiento
							and u.created_at <= '$fecha1'
							and th.fecha <= '$fecha1' 
							and (
									th.fecha_liberacion > '$fecha1' 
									or th.fecha_liberacion is null
								) 
						group by cama, u.alias, u.id
					) h
				group by h.id_servicio, h.nombre_servicio
				union
					select id, alias, 0 as count from unidades_en_establecimientos where establecimiento = $establecimiento
					)tab
				where id_servicio = $res->id
				group by id_servicio,alias order by alias asc
			");

			$res->camas_ocupadas_recibidas = $ocupadas_anterior[0]->ocupadas_anterior;
			
			// BLOQUEADAS TURNO ANTERIOR
			$bloqueadas_anterior = DB::select("SELECT t.fecha, t.fecha_habilitacion, c.id_cama,s.nombre,u.alias, u.id 
				from t_historial_bloqueo_camas as t
				join camas as c on c.id = t.cama
				join salas as s on s.id = c.sala
				join unidades_en_establecimientos as u on u.id = s.establecimiento
				where
					u.establecimiento = $establecimiento 
					and u.id = $res->id
					and t.fecha < '$fecha1'
					and (
						t.fecha_habilitacion >= '$fecha1'
						or t.fecha_habilitacion is null
					) 
			");

			$res->camas_bloqueadas_recibidas = count($bloqueadas_anterior); 

			//DOTACION
			//TOTAL CAMAS PARA CALCULAR DOTACION
			//Falta crear un eliminacion salas y unidades, es debidoa a esto que el informe puede pioseer errores en cuanto a salas o unidades que no existian en ese momentos
			$total_camas = DB::select("SELECT distinct id_servicio, alias, sum (count) as total_camas from(
				select t.id_servicio, t.nombre_servicio as alias, count(*)
				from
				  	(select u.id as id_servicio, u.alias as nombre_servicio
					from camas as c
					join salas as s on s.id = c.sala
					join unidades_en_establecimientos as u on u.id = s.establecimiento
					left join historial_eliminacion_camas as he on he.cama = c.id
					where
						u.establecimiento = $establecimiento
						and u.created_at <= '$fecha1'
						--    and created_at >= '$fecha1'
						and c.created_at < '$fecha1'
						) t
				group by t.id_servicio, t.nombre_servicio
				union
				 	select id, alias, 0 as count from unidades_en_establecimientos where establecimiento = $establecimiento
				)tab
				where id_servicio = $res->id
				group by id_servicio,alias
			");  
			$total_camas_tmp = (isset($total_camas[0]->total_camas))?$total_camas[0]->total_camas:0;
			
			$res->camas_disponibles_recibidas = $total_camas_tmp - ($res->camas_ocupadas_recibidas + (int)$res->camas_bloqueadas_recibidas);
			$res->dotacion = $total_camas_tmp;

			$dotacion = DB::select(DB::Raw("SELECT id_servicio, dotacion 
				from dotacion_cama 
				where id_servicio = $res->id 
				and (created_at <= '$fecha1'
						or created_at is null
					)
				and (fecha_termino >= '$fecha0' 
						or fecha_termino is null
					)
				order by id desc
				limit 1 
			"));

			if(isset($dotacion[0]->dotacion)){
				$res->dotacion = $dotacion[0]->dotacion;
			}

			//N° CAMAS HABILITADAS
			// $res->camas_habilitadas = $res->dotacion - $res->camas_bloqueadas_recibidas;
			$res->camas_habilitadas = $res->camas_disponibles_recibidas + ($res->camas_ocupadas_recibidas /* + $res->camas_bloqueadas_recibidas */);
			//**************************TURNO ANTERIOR***************************

			//**************************TURNO ACTUAL***************************
			$camas_desbloqueadas = DB::select("SELECT t.fecha, t.fecha_habilitacion, c.id_cama,s.nombre,u.alias, u.id 
					from t_historial_bloqueo_camas as t
					join camas as c on c.id = t.cama
					join salas as s on s.id = c.sala
					join unidades_en_establecimientos as u on u.id = s.establecimiento
					where 
					( t.fecha_habilitacion <= '$fecha2' and t.fecha_habilitacion >= '$fecha1')
					and u.id = $res->id
			");

			$res->camas_desbloqueadas = count($camas_desbloqueadas);
			
			//$suma_total_camas_ocupadas_turno_actual += $res->ocupadas_actual;
			// BLOQUEADAS EN EL TURNO(?)

			$bloqueadas_actual = DB::select("
				select h.fecha, h.fecha_habilitacion, c.id_cama, s.nombre, u.alias, u.id
				from
					t_historial_bloqueo_camas  h
				join camas as c on c.id = h.cama
				join salas as s on s.id = c.sala
				join unidades_en_establecimientos as u on u.id = s.establecimiento
				where
					u.establecimiento = $establecimiento
					and h.fecha >= '$fecha1'
					and h.fecha <= '$fecha2'
					and u.id = $res->id
			"); 

			$res->bloqueadas_actual = count($bloqueadas_actual);

			//ALTAS
			$altas = DB::select("SELECT count(tho.id) as cant_altas
				from t_historial_ocupaciones as tho
				join camas as c on tho.cama = c.id
				join salas as s on c.sala = s.id
				join unidades_en_establecimientos as uee on s.establecimiento = uee.id
				and tho.fecha_liberacion >= '$fecha1' and tho.fecha_liberacion <= '$fecha2'
				and uee.id = $res->id
				and tho.motivo in ('alta','derivacion otra institucion','Liberación de responsabilidad','hospitalización domiciliaria','traslado extra sistema','Fuga','otro','derivación')
				and uee.establecimiento = $establecimiento
			");

			$res->altas = $altas[0]->cant_altas;

			//TRASLADOS
			$traslados = DB::table("t_historial_ocupaciones as tho")
				->select("tho.id as id_historial", "tho.caso", "uee.id as id_unidad", "p.nombre","p.apellido_paterno","p.apellido_materno")
				->join("camas as c", "tho.cama" ,"c.id")
				->join("salas as s", "c.sala", "s.id")
				->join("unidades_en_establecimientos as uee", "s.establecimiento", "uee.id")
				->join("casos as ca","ca.id","tho.caso")
				->join("pacientes as p","p.id","ca.paciente")
				->where(function($q) use ($fecha1, $fecha2){
					$q->where('tho.fecha_liberacion' ,">=", $fecha1)
					->where('tho.fecha_liberacion' ,"<=", $fecha2);
				})
				->where("uee.id", $res->id)
				->where("tho.motivo", "traslado interno")
				->where("uee.establecimiento", $establecimiento)
				->get();

			
			$traslados_tmp = 0;
			
			/* Comprobar si es que fueron traslados a otras unidades, sino no deben contrase como traslados */
			foreach ($traslados as $tras) {
				Log::info("traslados ".$tras->id_historial);	
				$continuacion = DB::select(
					"SELECT tho.id, tho.caso, uee.id as id_unidad
					from t_historial_ocupaciones as tho
					join camas as c on tho.cama = c.id
					join salas as s on c.sala = s.id
					join unidades_en_establecimientos as uee on s.establecimiento = uee.id
					where tho.id > $tras->id_historial
						and tho.caso = $tras->caso
					
					order by tho.id asc
					limit 1;
				");

				if (isset($continuacion[0]->id_unidad) && $continuacion[0]->id_unidad != $tras->id_unidad) {
					$traslados_tmp++;
					
				}
			}

			$res->traslados = $traslados_tmp;

			//INGRESOS solo pacientes hospitalizados segun claudio
			//Para mi los ingresos son los pacientes con asignación de cama en ese determinado momento

			$ingresos = DB::table("t_historial_ocupaciones as tho")
				->select("tho.id as id_historial", "tho.caso", "uee.id as id_unidad", "p.nombre","p.apellido_paterno","p.apellido_materno","tho.fecha")
				->join("camas as c", "tho.cama" ,"c.id")
				->join("salas as s", "c.sala", "s.id")
				->join("unidades_en_establecimientos as uee", "s.establecimiento", "uee.id")
				->join("casos as ca","ca.id","tho.caso")
				->join("pacientes as p","p.id","ca.paciente")
				->where(function($q) use ($fecha1, $fecha2){
					$q->where('tho.fecha' ,">=", $fecha1)
					->where('tho.fecha' ,"<=", $fecha2);
				})
				->where("uee.id", $res->id)
				->where("uee.establecimiento", $establecimiento)
				->get();

			$ingresos_tmp = 0;

			/* Comprobar que ese ingreso sea probeniente desde otro servicio y no desde el mismo */
			foreach ($ingresos as $ingr) {
				
				$anterior = DB::select(
					"SELECT tho.id, tho.caso, uee.id as id_unidad
					from t_historial_ocupaciones as tho
					join camas as c on tho.cama = c.id
					join salas as s on c.sala = s.id
					join unidades_en_establecimientos as uee on s.establecimiento = uee.id
					where tho.id < $ingr->id_historial
						and tho.caso = $ingr->caso
					
					order by tho.id desc
					limit 1;
				");

				if ((isset($anterior[0]->id_unidad) && $anterior[0]->id_unidad != $ingr->id_unidad) || !$anterior) {					
					$ingresos_tmp++;
				}
			}


			$res->ingresos = $ingresos_tmp;

			//FALLECIDOS
			$fallecidos = DB::select("SELECT count(tho.id) as cant_fallecidos 
				from t_historial_ocupaciones as tho
				join camas as c on tho.cama = c.id
				join salas as s on c.sala = s.id
				join unidades_en_establecimientos as uee on s.establecimiento = uee.id
				where tho.fecha_liberacion >= '$fecha1' and tho.fecha_liberacion <= '$fecha2'
				and uee.id = $res->id
				and tho.motivo = 'fallecimiento'
				and tho.fecha_liberacion is not null
				and uee.establecimiento = $establecimiento
			");
			$res->fallecidos = $fallecidos[0]->cant_fallecidos;
			//**************************TURNO ACTUAL***************************

			$res->test_ocupadas = $res->camas_ocupadas_recibidas - $res->altas - $res->traslados + $res->ingresos - $res->fallecidos;

			$res->test_disponibles = $res->camas_disponibles_recibidas + $res->camas_desbloqueadas - $res->bloqueadas_actual + $res->altas + $res->traslados - $res->ingresos + $res->fallecidos; 

			$res->test_bloqueadas = $res->camas_bloqueadas_recibidas - $res->camas_desbloqueadas + $res->bloqueadas_actual;
			
			$res->test_total = $res->test_ocupadas + $res->test_disponibles + $res->test_bloqueadas;
		}

		return $resumen;
	}

	public function infoTurnoDos($inicio,$despues){
		$fecha_inicio = $inicio . " 20:00:00"; 
		$fecha_fin = $despues . " 07:59:59";

		$hospitalizados = Consultas::pacientes_hospitalizados($fecha_inicio,$fecha_fin);

		$traslados = Consultas::pacientes_trasladados($fecha_inicio, $fecha_fin);

		$transito = Consultas::pacientes_lista_transito($fecha_inicio,$fecha_fin);
			

		return ["hospitalizados" => $hospitalizados,"traslados" => $traslados, "transito" => $transito];
	}

	public function estCamaBloqueada(){
		$estab = Establecimiento::getAll(0);
		return view("NuevasEstadisticas/CamaBloqueada", ["establecimiento"=>$estab]);
	}

	public function estCamasBloqueadas(Request $request){
		$camas = [];
		$camas = Cama::dataCamasBloqueadas();
		return response()->json(["aaData" => $camas]);
	}

	public function camasBloqueadasPdf(){
		$fechaActual = Carbon::now();
		$fecha = Carbon::parse($fechaActual)->format("d-m-Y");
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
		$data = Cama::dataCamasBloqueadas();
		try {
			$pdf = PDF::loadView('Estadisticas.reportesCamasBloqueadas.pdfCamasBloqueadas', [
				"fecha" => $fecha,
				"establecimiento" => $nombreEstablecimiento,
				"response" => $data
			]);
			return $pdf->setPaper('legal', 'landscape')->download('Camas_Bloqueadas_'.$fecha.'.pdf');
		} catch (Exception $ex) {
			return $ex->getMessage();
		}
	}

	public function camasBloqueadasExcel(){
		Excel::create('CamasBloqueadas', function($excel) {
			$excel->sheet('CamasBloqueadas', function($sheet){

				$sheet->mergeCells('A1:J1');
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
				$data = Cama::dataCamasBloqueadas();
				$sheet->loadview('Estadisticas.reportesCamasBloqueadas.excelCamasBloqueadas', [
					"hoy" => $fechaActual,
					"establecimiento" => $nombreEstablecimiento,
					"response" => $data
				]);
			});
		})->download('xls');
	}

	public function estadisticaEstada(Request $request){
		$dias = 6;
		$msjNoDisponible = "No disponible";
		if($request->input('dias') != ''){
			$dias = $request->input('dias');
		}

		$hoy = Carbon::now();
		$hoy = $hoy->subDays($dias);

		$lista_espera = DB::table("t_historial_ocupaciones as t")
						->select("p.nombre as nombre",
						"p.apellido_paterno as apellidoP",
						"p.apellido_materno as apellidoM",
						"p.rut as rut",
						"p.dv as dv",
						"p.fecha_nacimiento",
						"p.id as id_paciente",
						"c.id as idCaso",
						"c.dau",
						"c.id_unidad",
						"p.id as id_paciente",
						"c.ficha_clinica",
						"c.fecha_ingreso2 as fecha_solicitud",
						"t.fecha_ingreso_real as hospitalizacion",
						"t.fecha as fecha_asignacion",
						"ca.id_cama",
						"ca.id as id_camaId",
						"s.nombre as nombre_sala",
						"s.id as id_sala",
						"a.nombre as nombre_area",
						"us.nombres as nombre_usuario",
						"us.apellido_paterno as apellidop_usuario",
						"us.apellido_materno as apellidom_usuario",
						"t.id_usuario_ingresa",
						"u.url")
						->leftjoin("casos as c", "c.id" , "=", "t.caso")
						->leftjoin("pacientes as p", "p.id" , "=", "c.paciente")
						->leftjoin("camas as ca", "ca.id", "=", "t.cama")
						->leftjoin("salas as s", "s.id", "=", "ca.sala")
						->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
						->leftjoin("area_funcional as a", "a.id_area_funcional", "=","u.id_area_funcional")
						->leftjoin("usuarios as us", "us.id", "=","c.id_usuario")
						->where("t.fecha_ingreso_real", "<", $hoy)
						->where("c.establecimiento", Auth::user()->establecimiento)
						->WhereNull("t.fecha_liberacion")
						->get();

		$pacientes = [];
		foreach ($lista_espera as $key => $paciente) {

			$rut=(empty($paciente->rut)) ? "SIN RUN" : $paciente->rut."-".$paciente->dv;
			$apellido=strtoupper($paciente->apellidoP)." ".strtoupper($paciente->apellidoM);

			$paciente->diagnostico = HistorialDiagnostico::where("caso","=",$paciente->idCaso)->orderby("fecha","desc")->select("diagnostico","comentario")->first();

			$fecha_hospitalizacion=date("d-m-Y H:i", strtotime($paciente->hospitalizacion));
			$fecha_actual = Carbon::now();

			$tiempo_hospitalizacion = $fecha_actual->diffInDays($fecha_hospitalizacion);

			$fecha_hospitalizacion_ord=date("Y-m-d H:i", strtotime($fecha_hospitalizacion));

			if (Paciente::edad($paciente->fecha_nacimiento) == $msjNoDisponible) {
				$años = "";
			}else{
				$años = " AÑOS";
			}

			$opcion = "  <form style='display: block' action='../../unidad/".$paciente->url."' method='GET' id='form'>
					<input hidden type='text' name='paciente' value='".$paciente->id_paciente."'>
					<input hidden type='text' name='id_sala' value='".$paciente->id_sala."'>
					<input hidden type='text' name='id_cama' value='".$paciente->id_camaId."'>
					<input hidden type='text' name='caso' value='".$paciente->idCaso."'>
					<button class='btn btn-primary' type='submit'>Ir a unidad</button>
				</form>";

			$pacientes [] = [
				$rut, strtoupper($paciente->nombre),
				$apellido,
				Carbon::parse($paciente->fecha_nacimiento)->format("d-m-Y")." (".Paciente::edad($paciente->fecha_nacimiento)."".$años.")",
				$paciente->diagnostico->diagnostico,
				$paciente->diagnostico->comentario,
				date("d-m-Y", strtotime($paciente->fecha_solicitud)),
				date("d-m-Y", strtotime($paciente->fecha_asignacion)),
				"<div hidden>".$fecha_hospitalizacion_ord."</div>".$fecha_hospitalizacion." (".$tiempo_hospitalizacion." DÍAS)",
				$paciente->id_cama,
				$paciente->nombre_sala,
				$paciente->nombre_area,
				$opcion
			];
		}
		return response()->json(["aaData" => $pacientes]);
	}


	//funcion para generar pdf
	public function estadisticaEstadaReporte($dias, $reporte){
		
		//En caso de ver cambiado el numero de dias en la vista, esta debe ser modificada
		if($dias < 0 || !is_numeric($dias)){
			$dias = 6;
		}

		$hoy = Carbon::now();
		$hoy = $hoy->subDays($dias);//Indica el numero de dias atras que debe tomar, puede ser de 1 a 10 dias segun lo que se indica en la vista

		$lista_espera = DB::table("t_historial_ocupaciones as t")
						->select("p.nombre as nombre",
						"p.apellido_paterno as apellidoP",
						"p.apellido_materno as apellidoM",
						"p.rut as rut",
						"p.dv as dv",
						"p.id as id_paciente",
						"u.id",
						"t.fecha_ingreso_real as hospitalizacion",
						"t.id_usuario_ingresa",
						"ca.id_cama",
						"t.caso",
						"s.nombre as nombre_sala"
						)
						->leftjoin("casos as c", "c.id" , "=", "t.caso")
						->leftjoin("pacientes as p", "p.id" , "=", "c.paciente")
						->leftjoin("camas as ca", "ca.id", "=", "t.cama")
						->leftjoin("salas as s", "s.id", "=", "ca.sala")
						->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
						->leftjoin("area_funcional as a", "a.id_area_funcional", "=","u.id_area_funcional")
						->where("t.fecha_ingreso_real", "<", $hoy)
						->where("c.establecimiento", Auth::user()->establecimiento)
						->WhereNull("t.fecha_liberacion")
						->get();

		$pacientes = [];
		foreach ($lista_espera as $key => $paciente) {

			$rut=(empty($paciente->rut)) ? "SIN RUN" : $paciente->rut."-".$paciente->dv;
			$apellido=strtoupper($paciente->apellidoP)." ".strtoupper($paciente->apellidoM);

			$fecha_hospitalizacion=date("d-m-Y H:i", strtotime($paciente->hospitalizacion));
			$fecha_actual = Carbon::now();

			$tiempo_hospitalizacion = $fecha_actual->diffInDays($fecha_hospitalizacion);

			$fecha_hospitalizacion_ord=date("Y-m-d H:i", strtotime($fecha_hospitalizacion));

			$paciente->diagnostico = HistorialDiagnostico::where("caso","=",$paciente->caso)->orderby("fecha","desc")->select("diagnostico","comentario","id_cie_10")->first();

		$pacientes [] = [
				'id_servicio'=> $paciente->id,
				'id_cama' => $paciente->id_cama,
				'sala' => $paciente->nombre_sala,
				'rut'=>$rut,
				'nombre'=>strtoupper($paciente->nombre),
				'apellido'=>$apellido,
				'fecha'=>$fecha_hospitalizacion,
				'dias'=>"(".$tiempo_hospitalizacion." DÍAS)",
				'diagnostico' => "(".$paciente->diagnostico->id_cie_10.") " .$paciente->diagnostico->diagnostico,
			];
		}

		$servicios = UnidadEnEstablecimiento::generarMapaServicios();// genera los nombres de los servicios con su descripcion en caso de que estos se repitan
		$datos = [];

		foreach($servicios as $idServicio => $servicio){
			$casos2=array();
			if(isset($pacientes)){
				foreach($pacientes as $persona){
					$id_servicio = $persona["id_servicio"];
					if($idServicio == $id_servicio){
						$casos2[]= $persona;
					}
				}
			}
			$datos[] = Array("area"=>$servicio[0],"casos"=>$casos2);
		}

		$hoy = carbon::now()->format('d-m-Y');
		if ($reporte == 'pdf'){
			$html = PDF::loadView("NuevasEstadisticas/pdfestadisticaEstada", [
				"datos" => $datos
			]);
			return $html->setPaper('legal', 'landscape')->download('ReporteEstada_'.$hoy.'.pdf');
		}else if($reporte == 'excel'){
			Excel::create('ReporteEstada_'.$hoy, function($excel) use ($datos, $dias, $hoy) {
				$excel->sheet('ReporteEstada_'.$hoy, function($sheet) use ($datos, $dias, $hoy) {
	
					$sheet->mergeCells('A1:H1');
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
					$hoy = Carbon::now();
					
					$sheet->loadView('NuevasEstadisticas.ExcelEstadisticasEstada', [
						"datos" => $datos,
						"hospital" => $establecimiento->nombre,
						"fecha" => $hoy->format("d/m/Y"),
						"dias" => $dias
						]
					);
				});
			})->download('xls');
		}
	}


	public function estadisticaEstadaTotal(Request $request){
		$fecha_inicio = $request->input('fecha-inicio');
		$fecha_fin = $request->input('fecha-fin');

		if(!$request->input('fecha-inicio')){
			$fecha_inicio = date("d-m-Y");
			$fecha_fin = date("d-m-Y");
		}

		$servicios = DB::table("unidades_en_establecimientos")
					->select("id","alias")
					->where("establecimiento", Auth::user()->establecimiento)
					->where("visible", true)
					->get();

		$response = array();
		foreach($servicios as $serv){
			$lista_espera = DB::table("t_historial_ocupaciones as t")
						->select("t.fecha_ingreso_real",
								"t.fecha_liberacion")
						->leftjoin("casos as c", "c.id" , "=", "t.caso")
						->leftjoin("camas as ca", "ca.id", "=", "t.cama")
						->leftjoin("salas as s", "s.id", "=", "ca.sala")
						->leftjoin("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
						->where("c.establecimiento", Auth::user()->establecimiento)
						->where("u.id", "=", $serv->id)
						->where("t.fecha_ingreso_real", ">=", $fecha_inicio. " 00:00:00")
						->where("t.fecha_ingreso_real", "<=", $fecha_fin." 23:59:59")
						->get();

			$suma = 0;
			foreach($lista_espera as $lista){
				$fecha1 = new DateTime($lista->fecha_ingreso_real);
				if($lista->fecha_liberacion == null){
					$fecha2 = new DateTime();
				}else{
					$fecha2 = new DateTime($lista->fecha_liberacion);
				}
				$fechaF = $fecha1->diff($fecha2);

				$diferencia = ($fechaF->y*365) + ($fechaF->m*30) + $fechaF->d;
				$suma += $diferencia;
			}

			$promedio = "0 días";
			if(count($lista_espera) > 0){
				$promedio = $suma / count($lista_espera);
				$promedio = round($promedio, 1, PHP_ROUND_HALF_DOWN)." días";
			}

			$response[] = array($serv->alias, $promedio);
		}

		return response()->json(["aaData" => $response]);
	}

	public function estadisticasPacientesGeneral(Request $request){

		Carbon::setLocale('es');

		if(Session::get("idEstablecimiento")){
			$whereEstablecimiento = "id_establecimiento=".Session::get("idEstablecimiento");
			$whereEstablecimiento2 = "c.establecimiento=".Session::get("idEstablecimiento");
		}
		else{
			$whereEstablecimiento = "TRUE";
			$whereEstablecimiento2 = "TRUE";
		}
		//pacientes actualmente en lista de espera
		$urgencia = DB::table("lista_espera as l")
						->leftjoin("casos as c", "c.id", "=", "l.caso")
						->when(Auth::user()->establecimiento, function ($query){
							return $query->where("c.establecimiento", Auth::user()->establecimiento);
						})

						->whereNull("l.fecha_termino")
						->count();


		//pacientes actualmente en lista de transito

		$transito = DB::table("lista_transito as l")
						->leftjoin("casos as c", "c.id", "=", "l.caso")
						->when(Auth::user()->establecimiento, function ($query){
							return $query->where("c.establecimiento", Auth::user()->establecimiento);
						})
						->whereNull("l.fecha_termino")
						->count();

		//total camas ocupadas

		$camas_ocupadas = DB::select(DB::raw("SELECT count(*) FROM ultimas_ocupaciones_vista
		WHERE
		".$whereEstablecimiento." and
		fecha_liberacion is null and
		fecha_alta is null
		and caso not in (select caso from lista_transito where fecha_termino is null)"));

		if(is_null($camas_ocupadas)){
			$camas_ocupadas = 0;
		}else{
			$camas_ocupadas = $camas_ocupadas[0]->count;
		}

		$egresados = DB::select(DB::raw("SELECT count(*) FROM casos as c
		WHERE
		".$whereEstablecimiento2." and
		c.fecha_termino is not null and
		c.fecha_termino < '".date('Y-m-d')." 23:59:59'
		and c.fecha_termino > '".date('Y-m-d')." 00:00:00'"));

		if(is_null($egresados)){
			$egresados = 0;
		}else{
			$egresados = $egresados[0]->count;
		}

		return response()->json([

			"urgencia" => $urgencia,
			"transito" => $transito,
			"camas_ocupadas" => $camas_ocupadas,
			"egresados" => $egresados,
		]);

	}
	public function knox(){
		$comunas = Comuna::getComunas();
		return view("NuevasEstadisticas/Knox", ["comunas" => $comunas]);
	}

	public function distribucionEspacial(){
		$comunas = Comuna::getComunas();
		return view("NuevasEstadisticas/DistribucionEspacial", ["comunas" => $comunas]);
	}

	public function kmeans(){
		$comunas = Comuna::getComunas();
		return view("NuevasEstadisticas/Kmeans", ["comunas" => $comunas]);
	}

	public function Sir(){
		return view("Estadisticas/Sir");
	}


	public function pagina(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return view("Estadisticas/ReporteCamas", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	public function reporteUnidad($fecha_inicio, $fecha, $establecimiento, $unidad){
		$this->estadia_promedio = new \App\Models\EstadiaPromedioUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);

		$this->ranking_servicio = new \App\Models\RankingCategoriasUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);
		$this->estadisticaDotacion = new \App\Models\EstadisticaCamasUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);
		$this->dotacionTipos = new \App\Models\DotacionTipoCamaUnidad($establecimiento, $unidad, $fecha_inicio, $fecha);
		$this->graficoTipo = $this->graficoDotacionTipos();
		return $this->reporte($fecha_inicio, $fecha, $establecimiento, $unidad);
	}

	/*AQUI ESTOY AHORA */
	public function reporteEstablecimiento($fecha_inicio, $fecha, $establecimiento){
		$this->estadia_promedio = new \App\Models\EstadiaPromedio($establecimiento, $fecha_inicio, $fecha);
		$this->ranking_servicio = new \App\Models\RankingServicios($establecimiento, $fecha_inicio, $fecha);
		$this->estadisticaDotacion = new \App\Models\EstadisticaCamas($establecimiento, $fecha_inicio, $fecha);

		$this->dotacionTipos = new \App\Models\DotacionTipoCamaEstablecimiento($establecimiento, $fecha_inicio, $fecha);

		$this->graficoTipo = $this->graficoDotacionTipos();
		//sacar la primera unidad que se muestra en el select
		$unidad = Session::get("idEstablecimiento");
		$unidades = Establecimiento::getUnidadPorEstablecimiento($unidad);
		$unidades = UnidadEnEstablecimiento::conCamas()->whereEstablecimiento($unidad)->whereVisible(true)->get();

		return $this->reporte($fecha_inicio, $fecha, $establecimiento,0);

	}

	public function reporteTotal($fecha_inicio, $fecha){
		$this->estadia_promedio = new \App\Models\EstadiaPromedioTotal($fecha_inicio, $fecha);

		$this->ranking_servicio = new \App\Models\RankingServiciosTotal($fecha_inicio, $fecha);
		$this->estadisticaDotacion = new \App\Models\EstadisticaCamasTotal($fecha_inicio, $fecha);
		$this->dotacionTipos = new \App\Models\DotacionTipoCamaTotal($fecha_inicio, $fecha);
		$this->graficoTipo = $this->graficoDotacionTiposTotal();
		return $this->reporte($fecha_inicio, $fecha, null,null);


	}

	public function camasDisponibles($fecha_inicio, $fecha, $establecimiento, $unidad){

		if($establecimiento != 0 && $unidad != 0){
			return count(DB::select("select retornar_camas_disponibles(?,?,?)", array($fecha, $establecimiento, $unidad)));
		}else if($establecimiento != 0 && $unidad == 0){
			return count(DB::select("select retornar_camas_disponibles(?,?)", array($fecha, $establecimiento)));
		}else{
			return count(DB::select("select retornar_camas_disponibles(?)", array($fecha)));
		}
	}
	public function camasOcupadas($fecha_inicio, $fecha, $establecimiento, $unidad){
		if($establecimiento != 0 && $unidad != 0){
			return count(DB::select("select retornar_camas_ocupadas(?,?,?)", array($fecha, $establecimiento, $unidad)));
		}else if($establecimiento != 0 && $unidad == 0){
			return count(DB::select("select retornar_camas_ocupadas(?,?)", array($fecha, $establecimiento)));
		}else{
			return count(DB::select("select retornar_camas_ocupadas(?)", array($fecha)));
		}
	}
	public function camasBloqueadas($fecha_inicio, $fecha, $establecimiento, $unidad){
		if($establecimiento != 0 && $unidad != 0){
			return count(DB::select("select retornar_camas_bloqueadas(?,?,?)", array($fecha, $establecimiento, $unidad)));
		}else if($establecimiento != 0 && $unidad == 0){
			return count(DB::select("select retornar_camas_bloqueadas(?,?)", array($fecha, $establecimiento)));
		}else{
			return count(DB::select("select retornar_camas_bloqueadas(?)", array($fecha)));
		}
	}

	public function dotacionTipo($fecha_inicio, $fecha, $establecimiento, $unidad){
		if($establecimiento != 0 && $unidad != 0){
			$detalles = DB::select(DB::raw("select tc.nombre, (case when tab.tipo is null then 0 else count(*) end) as contador 			from 			tipos_cama tc			left join	(select f.tipo from retornar_camas_ocupadas('".$fecha."', ".$establecimiento.", ".$unidad.") f				union all 				select f.tipo from retornar_camas_bloqueadas('".$fecha."', ".$establecimiento.", ".$unidad.") f				union all				select f.tipo from retornar_camas_disponibles('".$fecha."', ".$establecimiento.", ".$unidad.") f				) tab on tab.tipo=tc.id			group by tab.tipo, tc.id, tc.nombre;"));

		}else if($establecimiento != 0 && $unidad == 0){
			$detalles = DB::select(DB::raw("select tc.nombre, (case when tab.tipo is null then 0 else count(*) end) as contador 			from 			tipos_cama tc			left join	(select f.tipo from retornar_camas_ocupadas('".$fecha."', ".$establecimiento.") f				union all 				select f.tipo from retornar_camas_bloqueadas('".$fecha."', ".$establecimiento.") f				union all				select f.tipo from retornar_camas_disponibles('".$fecha."', ".$establecimiento.") f				) tab on tab.tipo=tc.id			group by tab.tipo, tc.id, tc.nombre;"));
		}else{
			$detalles = DB::select(DB::raw("select tc.nombre, (case when tab.tipo is null then 0 else count(*) end) as contador 			from 			tipos_cama tc			left join	(select f.tipo from retornar_camas_ocupadas('".$fecha."') f 				union all				select f.tipo from retornar_camas_bloqueadas('".$fecha."') f				union all				select f.tipo from retornar_camas_disponibles('".$fecha."') f				) tab on tab.tipo=tc.id			group by tab.tipo, tc.id, tc.nombre;"));

		}

		$response = [];
		foreach($detalles as $detalle){
			if($detalle->nombre == "BASICA" || $detalle->nombre == "MEDIA" || $detalle->nombre == "CRITICA"){
				$response [] = [
					"nombre"	=> $detalle->nombre,
					"contador"	=> $detalle->contador,
				];
			}

		}

		//TIPO DE grafico
		$chart ['chart'] = [
			"plotBackgroundColor" 	=> null,
			"plotBorderWidth"		=> 1,
			"plotShadow"			=> false,
			"zoomType"				=> "xy"
		];

		$chart ['plotOptions']['pie'] = [
			"allowPointSelect" 		=> true,
			"cursor"				=> "pointer"
		];
		$chart ['plotOptions']['pie']['dataLabels'] = [
			"enabled"				=> true,
			"format"				=> "<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)"
		];
		$chart ['plotOptions']['pie']['dataLabels']['style'] = [
			"color"					=> "(Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'"
		];

		$chart ['series'][0] = [
			"name"					=> "momentaneo",//cambiar nombre
			"type"					=> "pie"
		];
		//datos de grafico
		for ($i = 0; $i < count($response); $i++) {

			$chart ['series'][0]['data'][$i] = [
				"0"		=> $response[$i]["nombre"],
				"1"		=> $response[$i]["contador"]
			];
		}


		$chart ['title'] = [
			"text"		=> "Dotación de camas por tipo"
		];

		$chart ['tooltip'] = [
			"pointFormat"	=> "{point.name}<br>Valor: <b>{point.y}</b> Porcentaje: <b>{point.percentage:.1f}%</b>"
		];

		$chart ['yAxis'] = [
			"allowDecimals"	=> false
		];

		return $chart;

	}

	public function dotacionCamas($fecha_inicio, $fecha, $establecimiento, $unidad){

		$disponibles = $this->camasDisponibles($fecha_inicio, $fecha, $establecimiento, $unidad);
		$ocupadas = $this->camasOcupadas($fecha_inicio, $fecha, $establecimiento, $unidad);
		$bloqueadas = $this->camasBloqueadas($fecha_inicio, $fecha, $establecimiento, $unidad);

		$chart ['chart'] = [
			"plotBackgroundColor" 	=> null,
			"plotBorderWidth"		=> 1,
			"plotShadow"			=> false,
			"zoomType"				=> "xy"
		];

		$chart ['plotOptions']['pie'] = [
			"allowPointSelect" 		=> true,
			"cursor"				=> "pointer"
		];
		$chart ['plotOptions']['pie']['dataLabels'] = [
			"enabled"				=> true,
			"format"				=> "<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)"
		];
		$chart ['plotOptions']['pie']['dataLabels']['style'] = [
			"color"					=> "(Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'"
		];

		$chart ['series'][0] = [
			"name"					=> "dotacion",
			"type"					=> "pie"
		];
		for ($i = 0; $i < 3; $i++) {
			if($i == 0){
				$nombre = "deshabilitadas";
				$valor = $bloqueadas;
			}
			if($i == 1){
				$nombre = "ocupadas";
				$valor = $ocupadas;
			}
			if($i == 2){
				$nombre = "disponibles";
				$valor = $disponibles;
			}

			$chart ['series'][0]['data'][$i] = [
				"0"					=> $nombre,
				"1"					=> $valor
			];
		}

		$chart ['title'] = [
			"text"					=> "Estado dotación de camas"
		];

		$chart ['tooltip'] = [
			"pointFormat"			=> "{point.name}<br>Valor: <b>{point.y}</b> Porcentaje: <b>{point.percentage:.1f}%</b>"
		];

		$chart ['yAxis'] = [
			"allowDecimals"					=> false
		];

		return $chart;
	}

	public function detalleDotacionCamas($fecha_inicio, $fecha, $establecimiento, $unidad){
		if($establecimiento != 0 && $unidad != 0){
			$detalles = DB::select("select nombre_establecimiento, nombre_servicio, estado, rut, nombre_paciente, fecha_solicitud::varchar, fecha_asignacion::varchar from retornar_camas_ocupadas(?,?,?)", array($fecha, $establecimiento, $unidad));
		}else if($establecimiento != 0 && $unidad == 0){
			$detalles = DB::select("select nombre_establecimiento, nombre_servicio, estado, rut, nombre_paciente, fecha_solicitud::varchar, fecha_asignacion::varchar from retornar_camas_ocupadas(?,?)", array($fecha, $establecimiento));
		}else{
			$detalles = DB::select("select nombre_establecimiento, nombre_servicio, estado, rut, nombre_paciente, fecha_solicitud::varchar, fecha_asignacion::varchar from retornar_camas_ocupadas(?)", array($fecha));
		}


		$response = [];
		foreach($detalles as $detalle){
			$response [] = [
				"establecimiento"	=> $detalle->nombre_establecimiento,
				"servicio"			=> $detalle->nombre_servicio,
				"estado_cama"		=> $detalle->estado,
				"rut" 				=> $detalle->rut,
				"nombre" 			=> $detalle->nombre_paciente,
				"fecha_solicitud"	=> $detalle->fecha_solicitud,
				"fecha_asignacion"	=> $detalle->fecha_asignacion
			];
		}

		return $response;

	}

	public function detalleTipoCamas($fecha_inicio, $fecha, $establecimiento, $unidad){
		 if($establecimiento != 0 && $unidad != 0){
			$detalles = DB::select(DB::raw("select f.nombre_establecimiento, f.nombre_servicio, (case when f.tipo is null then 'Sin tipo' else t.nombre end) as tipo_cama, f.estado, f.rut, f.nombre_paciente, f.fecha_solicitud::varchar, f.fecha_asignacion::varchar from retornar_camas_ocupadas('".$fecha."', ".$establecimiento.", ".$unidad.") f left join tipos_cama t
			on f.tipo=t.id"));
		}else if($establecimiento != 0 && $unidad == 0){
			$detalles = DB::select(DB::raw("select f.nombre_establecimiento, f.nombre_servicio, (case when f.tipo is null then 'Sin tipo' else t.nombre end) as tipo_cama, f.estado, f.rut, f.nombre_paciente, f.fecha_solicitud::varchar, f.fecha_asignacion::varchar from retornar_camas_ocupadas('".$fecha."', ".$establecimiento.") f left join tipos_cama t
			on f.tipo=t.id"));
		}else{
			$detalles = DB::select(DB::raw("select f.nombre_establecimiento, f.nombre_servicio, (case when f.tipo is null then 'Sin tipo' else t.nombre end) as tipo_cama,  f.estado, f.rut, f.nombre_paciente, f.fecha_solicitud::varchar, f.fecha_asignacion::varchar from retornar_camas_ocupadas('".$fecha."') f left join tipos_cama t
		on f.tipo=t.id"));
		}

		$response = [];
		foreach($detalles as $detalle){
			$fecha_sol = "";
			if($detalle->fecha_solicitud != ""){
				$fecha_sol = date("d-m-Y H:i", strtotime($detalle->fecha_solicitud));
			}

			$fecha_asig = "";
			if($detalle->fecha_asignacion != ""){
				$fecha_asig = date("d-m-Y H:i", strtotime($detalle->fecha_asignacion));
			}
			$response [] = [
				"establecimiento"	=> $detalle->nombre_establecimiento,
				"servicio"			=> $detalle->nombre_servicio,
				"tipo_cama"			=> $detalle->tipo_cama,
				"estado"			=> $detalle->estado,
				"rut" 				=> $detalle->rut,
				"nombre" 			=> $detalle->nombre_paciente,
				"fecha_solicitud"	=> $fecha_sol,
				"fecha_asignacion"	=> $fecha_asig
			];
		}

		return $response;

	}
	public function reporte($fecha_inicio, $fecha, $establecimiento, $unidad){
		return json_encode(array(

			"nueva_dotacion" 	=> $this->dotacionCamas($fecha_inicio, $fecha, $establecimiento, $unidad),
			"nuevo_tipos"	 	=> $this->dotacionTipo($fecha_inicio, $fecha, $establecimiento, $unidad),
			"detalles_dotacion"	=> $this->detalleDotacionCamas($fecha_inicio, $fecha, $establecimiento, $unidad),
			"detalles_tipo"		=> $this->detalleTipoCamas($fecha_inicio, $fecha, $establecimiento, $unidad),
			"g_dotacion" 		=> json_decode( $this->graficoDotacionCamas() ),
			"g_estadia"	 		=> json_decode( $this->graficoEstadiaPromedio() ),
			"g_ranking"	 		=> json_decode( $this->graficoRankingCategorias() ),
			"g_tipos"	 		=> json_decode( $this->graficoTipo ),
			"r_dotacion" 		=> array(
				/* "deshabilitadas" 		=> $this->estadisticaDotacion->deshabilitadas(),
				"reservadas"	 		=> $this->estadisticaDotacion->reservadas(),
				"ocupadas"		 		=> $this->estadisticaDotacion->ocupadas(),
				"disponibles"	 		=> $this->estadisticaDotacion->disponibles(), */
				"nueva_disponibles"		=> $this->camasDisponibles($fecha_inicio, $fecha, $establecimiento, $unidad),
				"nueva_ocupadas"		=> $this->camasOcupadas($fecha_inicio, $fecha, $establecimiento, $unidad),
				"nueva_deshabilitadas"	=> $this->camasBloqueadas($fecha_inicio, $fecha, $establecimiento, $unidad),
			),
		));
	}

	public function graficoDotacionTipos(){
		$e = new \App\Models\GraficoPastel($this->dotacionTipos);

		return $e->setTitulo("Dotación de camas por tipo")->get();
	}

	public function graficoDotacionTiposTotal(){
		$e = new \App\Models\GraficoPorUnidad($this->dotacionTipos);
		return $e->setTitulo("Dotación de camas por tipo")->get();
	}

	public function graficoDotacionCamas(){
		$this->graficoDotacion = new \App\Models\GraficoCircular();
		/* $deshabilitadas =  */

		return $this->graficoDotacion->setTitulo("Estado dotación de camas")
		->agregarSerie( array(
			array("deshabilitadas", $this->estadisticaDotacion->deshabilitadas()),
			array("reservadas",$this->estadisticaDotacion->reservadas() ),
			array("ocupadas", $this->estadisticaDotacion->ocupadas() ),
			array("disponibles", $this->estadisticaDotacion->disponibles() ),
		), "dotacion")->renderJson();
	}

	public function graficoEstadiaPromedio(){
		$e = new \App\Models\GraficoMensual($this->estadia_promedio);
		return $e->setTitulo("Estadía promedio mensual en días, últimos 12 meses")->get();
	}

	public function graficoRankingCategorias(){
		$e = new \App\Models\GraficoPorUnidad($this->ranking_servicio);
		return $e
		->setTitulo("Número de pacientes por categoría de riesgo")
		->setTituloY("Cantidad")->get();
	}


	public function generarPDF($id_establecimiento){
		$establecimiento=Establecimiento::select('nombre')
			->where("id", "=", $id_establecimiento)
			->first();
		$id_estab = $id_establecimiento;
		$fechaActual = date("Y-m-d");

		$resultados=DB::select(DB::raw("
		--fallecidos (columna A, columna B, columna G, columna H)
			select nombre_servicio, nombre_paciente, to_char(fecha_termino, 'HH24:MI') as hora, 'fallecido' as estado,
			mismo_dia  from retornar_fallecidos_censo('".$fechaActual."') where establecimiento=".$id_estab." union
		--ingresos mismo hospital (columna A, columna B, columna D, columna H)
			select nombre_servicio, nombre_paciente, to_char(fecha, 'HH24:MI') as hora, 'ingreso mismo hospital' as estado,
			mismo_dia from retornar_ingresos_mismo_hospital_censo('".$fechaActual."') where id_establecimiento=".$id_estab." union
		--egresos mismo hospital (columna A, columna B, columna F, columna H)
			select nombre_servicio, nombre_paciente, to_char(fecha_liberacion, 'HH24:MI') as hora, 'egreso mismo hospital' as estado,
			mismo_dia from retornar_egresos_mismo_hospital_censo('".$fechaActual."') where id_establecimiento=".$id_estab." union
		--ingreso desde fuera o desde otro hospital (columna A, columna B, columna C, columna H)
			select nombre_servicio, nombre_paciente, to_char(fecha, 'HH24:MI') as hora, 'ingreso' as estado,
			mismo_dia from retornar_ingresos_fuera_otro_hospital_censo('".$fechaActual."') where id_establecimiento=".$id_estab." union
		--alta al hogar o a otro hospital (columna A, columna B, columna E, columna H)
			select nombre_servicio, nombre_paciente, to_char(fecha_liberacion, 'HH24:MI') as hora, 'egreso' as estado,
			mismo_dia from retornar_egresos_fuera_otro_hospital_censo('".$fechaActual."') where id_establecimiento=".$id_estab." order by nombre_servicio, nombre_paciente, hora;
		"));

		$datos_div = array();
		$datos = array();
		$total_ingreso = 0;
		$total_ingreso_mismo = 0;
		$total_egreso = 0;
		$total_egreso_mismo = 0;
		$total_fallecido = 0;
		foreach($resultados as $key=>$resultado){
			$mismo_dia = "No";
			if($resultado->mismo_dia){
				$mismo_dia = "Si";
			}

			$ingreso = "";
			$ingreso_mismo_hospital = "";
			$egreso = "";
			$egreso_mismo_hospital = "";
			$fallecido = "";

			switch($resultado->estado){
				case "ingreso":
					$ingreso = $resultado->hora;
					$total_ingreso = $total_ingreso + 1;
					break;
				case "ingreso mismo hospital":
					$ingreso_mismo_hospital = $resultado->hora;
					$total_ingreso_mismo = $total_ingreso_mismo + 1;
					break;
				case "egreso":
					$egreso = $resultado->hora;
					$total_egreso = $total_egreso + 1;
					break;
				case "egreso mismo hospital":
					$egreso_mismo_hospital = $resultado->hora;
					$total_egreso_mismo = $total_egreso_mismo + 1;
					break;
				case "fallecido":
					$fallecido = $resultado->hora;
					$total_fallecido = $total_fallecido + 1;
					break;
			}

			$datos[]=array(
				"nombre"=>$resultado->nombre_paciente,
				"servicio"=>$resultado->nombre_servicio,
				"desde_fuera"=>$ingreso,
				"este_mismo"=>$ingreso_mismo_hospital,
				"alta_otro"=>$egreso,
				"traslado"=>$egreso_mismo_hospital,
				"fallecido"=>$fallecido,
				"ingreso_egreso"=>$mismo_dia
			);

			if(($key+1) % 20 == 0 || $key+1 == count($resultados)){
				$datos_div[] = $datos;
				$datos = [];
			}

		}

		$servicios = UnidadEnEstablecimiento::select("id", "alias")
			->where("establecimiento", "=", $id_estab)
			->where("visible", "=", TRUE)
			->orderBy("alias", "asc")
			->get();

		$detalle_estab = array();
		foreach($servicios as $servicio){

			$pacientes_servicios=DB::select(DB::raw("select
			t.nombre as nombre_establecimiento,
			t.alias as nombre_servicio,
			(select count(*)as numero_hospitalizados from retornar_camas_ocupadas('".$fechaActual."', ".$id_estab.", ".$servicio->id.")) as numero_pacientes_hospitalizados,
			(select count(*)as numero_o from retornar_camas_ocupadas('".$fechaActual."', ".$id_estab.", ".$servicio->id."))+(select count(*)as numero_d from retornar_camas_disponibles('".$fechaActual."', ".$id_estab.", ".$servicio->id.")) as numero_camas_en_trabajo
			from
			(select u.alias, e.nombre from unidades_en_establecimientos u, establecimientos e where u.id=".$servicio->id." and u.establecimiento=".$id_estab." and visible is true) t
			limit 1;"));

			$nombre_servicio = $servicio->alias;
			$numero_pacientes_hospitalizados = 0;
			$numero_camas_en_trabajo = 0;
			if($pacientes_servicios){
				$numero_pacientes_hospitalizados = $pacientes_servicios[0]->numero_pacientes_hospitalizados;
				$numero_camas_en_trabajo = $pacientes_servicios[0]->numero_camas_en_trabajo;
			}
			$detalle_estab[] = array("nombre_servicio"=>$nombre_servicio,
									"numero_pacientes_hospitalizados"=>$numero_pacientes_hospitalizados,
									"numero_camas_en_trabajo"=>$numero_camas_en_trabajo
								);

		}

		$dia = date("d");
		$mes = date("m");
		$ano = date("Y");

		$numero_camas = 10;
		$hospitalizados = 20;

		return view('Estadisticas/PDFCensoDiario', array(
			"nombreEstablecimiento"=>$establecimiento->nombre,
			"dia"=>$dia,
			"mes"=>$mes,
			"ano"=>$ano,
			"datos"=>$datos_div,
			"total_ingreso"=>$total_ingreso,
			"total_ingreso_mismo"=>$total_ingreso_mismo,
			"total_egreso"=>$total_egreso,
			"total_egreso_mismo"=>$total_egreso_mismo,
			"total_fallecido"=>$total_fallecido,
			"numero_camas"=>$numero_camas,
			"hospitalizados"=>$hospitalizados,
			"detalle_estab"=>$detalle_estab
		))->render();
	}

	public function censoDiario(){
		$id_estab = Session::get("idEstablecimiento");
		$html = $this->generarPDF($id_estab);
	}

	public function enviarCorreoCenso(){
		$establecimientos = Establecimiento::select("id")
			->orderBy("id", "asc")
			->get();

		try{
			define('BUDGETS_DIR', public_path('uploads/budgets')); // I define this in a constants.php file

			if (!is_dir(BUDGETS_DIR)){
				mkdir(BUDGETS_DIR, 0755, true);
			}

			foreach($establecimientos as $key=>$estab){
				$html = $this->generarPDF($estab->id);
				$outputName = str_random(10); // str_random is a [Laravel helper](http://laravel.com/docs/helpers#strings)
				$pdfPath = BUDGETS_DIR.'/'.$outputName.'.pdf';
				File::put($pdfPath, $this->pdf[$key]->load($html, 'letter', 'landscape')->output());

				 $data = array(
					"nombre" => "NOMBRE",
					"mensaje" => "MENSAJE",
					"correo" => "CORREO"
				);

				$admin = Usuario::select("email")
					->where("establecimiento", "=", $estab->id)
					->where("visible", "=", TRUE)
					->where("tipo", "=", "admin")
					->get();

				$correos = array();
				foreach($admin as $ad){
					$correos[] = $ad->email;
				}

				$asunto= "Reporte diario";

				Mail::send('emails.CorreoCenso',$data, function($message) use ($correos,$asunto, $pdfPath){
					$message->to($correos)
							//->from($correos)
							->subject($asunto)
							->attach($pdfPath);
				});

			}
			return "Correos enviados";

		}catch(Exception $ex){
			return $ex->getMessage();
		}

	}

	public function regresion(){
		return view("NuevasEstadisticas/Regresion");
	}

	public function randomForest(){
		return view("NuevasEstadisticas/RandomForest");
	}

	public function aplicarRegresion(){
		//ejectuar python con la informacion
		$rutaPython = '/usr/bin/python';

		$rutaCVS = public_path();

		$rutaOptimizacion = public_path().'/python/regresion/regresionAbrir.py '.$rutaCVS.' 2>&1';
		$rutaCompleta = $rutaPython." ".$rutaOptimizacion;

		return shell_exec('sh /var/www/scripts/regresion.sh '.$rutaCVS);
	}

	public function aplicarRandom(){
		//ejectuar python con la informacion
		$rutaPython = '/usr/bin/python';

		$rutaCVS = public_path();

		$rutaOptimizacion = public_path().'/python/regresion/regresionAbrir.py '.$rutaCVS.' 2>&1';
		$rutaCompleta = $rutaPython." ".$rutaOptimizacion;

		return json_decode(shell_exec('sh /var/www/scripts/regresionGrafico.sh '.$rutaCVS), true);
	}

	public function graficoCat(Request $request){
		$mes = $request->input('mes');
		$anno = $request->input('anno');
		$establecimiento = $request->input('establecimiento');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$estab = $establecimiento;
		if(Session::get('usuario')->tipo != 'admin_ss'){
			$estab = Session::get("idEstablecimiento");
		}

		$limite = array();
		$resultados = array();
		$cantidad = array();
		for($i=1; $i<=$cant_dias; $i++){
			$fecha = $anno."-".$mes."-".$i;
			$resultado=DB::select(DB::raw("SELECT * FROM retornar_casos_categorizados2 (
				".$estab.", '".$fecha."');"));
			$resultados[] = intval($resultado[0]->porcentaje);
			$cantidad[] = intval($resultado[0]->numero_pacientes);
			$limite[] = 8;
		}

		return response()->json(array("resultados"=>$resultados, "limite"=>$limite, "cantidad"=>$cantidad));
	}

	//Funcion probablemente que nunca se ha usado
	public function tiempoEstada($ingreso, $liberacion = null){
		if(is_null($ingreso)) return "";
		if(is_null($liberacion)) $f_liberacion = \Carbon\Carbon::now();
		else $f_liberacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $liberacion);
		$f_ingreso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ingreso);
		/* @var $diff \Carbon\Carbon */
		return $f_liberacion->diffInSeconds($f_ingreso);
	}

	public function estOcupacional(){
		return View::make("Estadisticas/IndiceOcupacional");
	}

	public function graficoOcupacional(Request $request){
		$mes = $request->input('mes');
		$anno = $request->input('anno');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$resultados = array();
		for($i=1; $i<=$cant_dias; $i++){
			$fecha = $anno."-".$mes."-".$i." 00:00:00";
			$fecha_fin = $anno."-".$mes."-".$i." 23:59:59";
			$resultado=DB::select(DB::raw("SELECT count(cama) cam
				from t_historial_ocupaciones_vista th, casos c, camas ca, salas s, unidades_en_establecimientos ue
				where th.caso = c.id
				and th.id_establecimiento = ".Session::get('idEstablecimiento')."
				and th.fecha_liberacion is null
				and th.fecha <= '".$fecha_fin."'
				and th.cama = ca.id
				and ca.sala = s.id
				and s.visible = true
				and s.establecimiento = ue.id
				and ue.visible = true
				and c.id not in (select cama from historial_eliminacion_camas)"));

			$total_camas=DB::select(DB::raw("select count(c.id) cam
				from camas c, salas s, unidades_en_establecimientos ue
				where c.sala = s.id
				and s.visible = true
				and s.establecimiento = ue.id
				and ue.establecimiento = ".Session::get('idEstablecimiento')."
				and ue.visible = true
				and c.created_at <= '".$fecha."'
				and c.id not in (select cama from historial_eliminacion_camas)"));

			if(intval($total_camas[0]->cam) == 0){
				$resultados[] = 0;
			}else{
				$resultados[] = round((intval($resultado[0]->cam) / intval($total_camas[0]->cam)) * 100, 2);
			}

		}

		return response()->json(array("resultados"=>$resultados));
	}

	public function graficoBloqueadas(Request $request){
		$mes = $request->input('mes');
		$anno = $request->input('anno');
		$tipo_cama = $request->input('tipo_cama');
		$establecimiento = $request->input('establecimiento');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$estab = $establecimiento;
		if(Session::get('usuario')->tipo != 'admin_ss'){
			$estab = Session::get("idEstablecimiento");
		}

		$resultados = array();
		
		$unidades_considerar = ($request->tipo_cama == "TODOS")?"11,12,13":"$tipo_cama";
		$titulo = ($request->tipo_cama == "TODOS")?"Cantidad de camas bloqueadas":"Cantidad de camas ".
		strtolower(TipoCama::traduccionTipo($tipo_cama))."s bloqueadas";
		for($i=1; $i<=$cant_dias; $i++){
			$fecha = $anno."-".$mes."-".$i;

			//Camas bloqueadas
			$resultado=DB::select(DB::raw("select count(*) as camas_bloqueadas from historial_bloqueo_camas_vista h
			left join camas_vista c on h.cama=c.id
			where
			c.id_establecimiento=".$estab." and
			c.tipo in (".$unidades_considerar.") and
			(	(h.fecha::date ='".$fecha."') or
				('".$fecha."'<=h.fecha_habilitacion::date and h.fecha<='".$fecha." 00:00:00') or
				(h.fecha_habilitacion is null and h.fecha<='".$fecha." 00:00:00')
			)"));

			//Total de camas
			$cant_camas_eliminadas = DB::select(DB::raw("
				select count(*) as camas  from camas_vista as c
				inner join salas as s on s.id = c.sala
				where c.id_establecimiento = $estab 
				and c.created_at <= '$fecha 00:00:00'
				and (c.tipo is not null and c.tipo in ($unidades_considerar) )
				and c.id not in (
					select cama from historial_eliminacion_camas
					where fecha <= '$fecha 00:00:00'
				) 
				and s.visible is true
			"));
			
			$resultados[] = $resultado[0]->camas_bloqueadas;
			$total_comparacion[] = $cant_camas_eliminadas[0]->camas;
		}

		return response()->json(array("resultados"=>$resultados, "total_comparacion"=>$total_comparacion, "titulo" => $titulo) );
	}

	//estadistica por diagnostico
	public function estadisticaDiagnostico(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return view("Estadisticas/ReporteDiagnostico", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	//datos de los diagnosticos
	public function datosDiagnostico(Request $request){

		$cie10s = DB::table('cie_10')
						->select('id_cie_10 as id', 'nombre', 'estadia_promedio' )
						->where('id_categoria_cie_10', '=', $request->hidden_diagnosticos)
						->where('visible', 1)
						->get();


		$inicio = Carbon::createFromFormat('d-m-Y', $request->inicio)->endOfDay()->format('Y-m-d H:i:s');
		$fin = Carbon::createFromFormat('d-m-Y', $request->fin)->startOfDay()->format('Y-m-d H:i:s');

		$casos = [];
		foreach( $cie10s as $key => $cie10){

			$consulta = DB::table('pacientes as p')
								->select('p.id as paciente', 'c.fecha_ingreso', 'c.fecha_termino', 'cie.id_cie_10', 'c.motivo_termino',DB::raw("DATE_PART('day', c.fecha_termino::timestamp - c.fecha_ingreso::timestamp) as diferencia_dias") )
								->join('casos as c', 'c.paciente', '=', 'p.id')
								->join('diagnosticos as d', 'd.caso', '=', 'c.id')
								->join('cie_10 as cie', 'cie.id_cie_10', '=', 'd.id_cie_10')
								->whereNotNull('c.fecha_termino')
								->where('c.fecha_termino', '<', $fin)
								->where('c.fecha_ingreso', '>', $inicio)
								->where('cie.id_cie_10', $cie10->id)
								->where('c.establecimiento', Auth::user()->establecimiento)
								->orderby('paciente')
								->get();

			$total = $consulta->count();

			//promedio
			$suma_total = 0;
			foreach( $consulta as $key2 => $caso){
				$suma_total += $caso->diferencia_dias;
			}

			if($total != 0){
				$promedio = round(($suma_total/$total),2);
			}else{
				$promedio = 0;
			}


			$casos ["cie_10"][] = [
				$cie10->id
			];

			$casos ["nombre_cie_10"][] = [
				$cie10->nombre
			];

			$casos ["promedio"] [] = [
				$promedio
			];

			$casos ["estadia_promedio"][] = [
				$cie10->estadia_promedio
			];

		}
		return response()->json($casos);

	}

	//estadistica por estadia paciente
	public function estadisticaEstadiaYCamas(){
		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");
		return view("Estadisticas/ReporteEstadiaPaciente", ["establecimiento" => $establecimiento, "fecha" => $fecha]);
	}

	//datos de camas disponibles
	public function datosEstadiaYCamas(Request $request){

		$inicio = Carbon::createFromFormat('d-m-Y', $request->inicio)->endOfDay()->format('Y-m-d H:i:s');
		$fin = Carbon::createFromFormat('d-m-Y', $request->fin)->startOfDay()->format('Y-m-d H:i:s');

		$respuesta = [];

		//Promedio días de estada
		$respuesta [] = DB::select(DB::raw("select
		EXTRACT(EPOCH FROM avg(
		CASE WHEN (h.fecha_liberacion - h.fecha ) IS NULL THEN (date_trunc('second', now()) - h.fecha) ELSE (h.fecha_liberacion - h.fecha) END) )/3600 as estadia
		from historial_ocupaciones_vista h, casos c, camas ca, salas s, unidades_en_establecimientos ue
		where
		h.caso = c.id and
		h.id_establecimiento = '".Auth::user()->establecimiento."'
		and h.fecha >= '".$inicio."'
		and h.fecha <= '".$fin."'
		and h.cama = ca.id
		and ca.sala = s.id
		and s.visible = true
		and s.establecimiento = ue.id
		and ue.visible = true
		and h.cama not in (select cama from historial_eliminacion_camas)
		"));

		//Promedio de camas disponibles
		$respuesta []= "hola" ;


		//Índice de rotación o giro de camas
		$usos = DB::table("t_historial_ocupaciones as t")
				->select(DB::raw("t.cama, ca.id_cama, sa.nombre, count(*) as uso"))
				->join("casos as c", "c.id", "=","t.caso")
				->join("camas as ca", "ca.id", "=","t.cama")
				->join("salas as sa", "sa.id", "=","ca.sala")
				->where("c.establecimiento", Auth::user()->establecimiento)
				->where("t.fecha", ">=", $inicio)
				->where("t.fecha", "<=", $fin)
				->groupBy("t.cama","ca.id_cama", "sa.nombre")
				->orderBy("sa.nombre")
				->get();

		$response = [];

		foreach($usos as $uso){
			$response [] =array(
				$uso->id_cama,
				$uso->nombre,
				$uso->uso
			);
		}

		$respuesta [] = $response;

		//Intervalo de sustitucion
		//camas del establecimiento
		$datos_camas = DB::select(DB::raw("select c.id, c.id_cama as cama, s.nombre as sala,u.alias as unidad
		from camas as c
		inner join salas as s on s.id = c.sala
		inner join unidades_en_establecimientos as u on u.id = s.establecimiento
		inner join establecimientos as e on e.id = u.establecimiento
		and e.id = '".Auth::user()->establecimiento."'

		AND c.id NOT IN(
			select historial_eliminacion_camas.cama
			from historial_eliminacion_camas)

		AND c.id NOT IN(
			select cama
			from historial_eliminacion_camas
			)
		AND c.id NOT IN(
			select cama
			from t_historial_bloqueo_camas
			where t_historial_bloqueo_camas.fecha_habilitacion is null
			)
		AND c.id NOT IN(
			select camas.id
			from camas
			join salas on salas.id = camas.sala
			join salas_con_camas on salas_con_camas.id_sala = salas.id
			where salas_con_camas.visible = false)

		AND s.visible = TRUE
		AND u.visible = TRUE
		order by id
		"));

		//tiempo camas ocupadas
		$tiempo_usadas = DB::select("select c.id, c.id_cama as cama, s.nombre as sala,u.alias as unidad ,   SUM(DATE_PART('minute', t.fecha_liberacion::timestamp - t.fecha::timestamp)) as diferencia
		from camas as c
		left join t_historial_ocupaciones as t on t.cama = c.id
		left join salas as s on s.id = c.sala
		left join unidades_en_establecimientos as u on u.id = s.establecimiento
		left join establecimientos as e on e.id = u.establecimiento
		where (t.fecha >= '".$inicio."'
		and t.fecha_liberacion <= '".$fin."')
		and e.id = '".Auth::user()->establecimiento."'
		AND c.id NOT IN(
			select historial_eliminacion_camas.cama
			from historial_eliminacion_camas)
		AND c.id NOT IN(
			select T1.cama
			from t_historial_ocupaciones T1
			where T1.fecha_liberacion IS NULL
			)
		AND c.id NOT IN(
			select cama
			from historial_eliminacion_camas
			)
		AND c.id NOT IN(
			select cama
			from t_historial_bloqueo_camas
			where t_historial_bloqueo_camas.fecha_habilitacion is null
			)
		AND c.id NOT IN(
			select camas.id
			from camas
			join salas on salas.id = camas.sala
			join salas_con_camas on salas_con_camas.id_sala = salas.id
			where salas_con_camas.visible = false)

		AND s.visible = TRUE
		group by c.id,s.nombre,u.alias
		having( SUM( DATE_PART('minute', t.fecha_liberacion::timestamp - t.fecha::timestamp) ) ) > 0
		order by diferencia
		");

		$start = DateTime::createFromFormat('Y-m-d H:i:s', $inicio);
		$ends = DateTime::createFromFormat('Y-m-d H:i:s', $fin);
		$minutos = $start->diff($ends);
		$total_minutos = ($minutos->y * 525600 ) + ($minutos->m * 43800) + ($minutos->d *1440) + ($minutos->h * 60) + $minutos->i;

		foreach( $datos_camas as $key => $d){

			foreach( $tiempo_usadas as $key => $t){
				if($d->id == $t->id){
					$valor = $total_minutos - $t->diferencia;
					$x = round(( (100 * $valor)/$total_minutos),2);
					$x = (string)$x."%";
					break;
				}else{
					$x = '100%';
				}
			}

			$tiempos [] = [
				$d->cama,
				$d->sala,
				$d->unidad,
				$x
			];

		}

		$respuesta [] = $tiempos;

		//Promedio de camas disponibles: es el número promedio de camas que estuvieron en funcionamiento cada día en un período dado. Se obtiene dividiendo los días camas disponibles en el mes por el número de días del mes.






		return response()->json($respuesta);

	}

	public function reporteListaEspera(){
		return View::make("Estadisticas/ReporteListaEspera");
    }

    public function ListaEsperaDatos(Request $request){
		$estab = "";
		if(Session::get('idEstablecimiento')){
			$estab = "and c.establecimiento = ".Session::get('idEstablecimiento');
		}

		$mes = $request->input('mes');
		$anno = $request->input('anno');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$resultados = array();
		for($i=1; $i<=$cant_dias; $i++){
			$fecha1 = $anno."-".$mes."-".$i." 00:00:00";
			$fecha2 = $anno."-".$mes."-".$i." 23:59:59";
			$resultado=DB::select(DB::raw("select count(le.id) cantidad from lista_espera le, casos c
				where le.caso = c.id
				".$estab."
				and le.fecha < '".$fecha2."'
				and (le.fecha_termino is null or
				(le.fecha_termino > '".$fecha1."'
				and le.fecha_termino < '".$fecha2."'));"));
			$resultados[] = intval($resultado[0]->cantidad);
		}

		return response()->json(array("resultados"=>$resultados));
	}

	public function reporteListaTransito(){
		return View::make("Estadisticas/ReporteListaTransito");
    }

    public function ListaTransitoDatos(Request $request){

		$estab = "";
		if(Session::get('idEstablecimiento')){
			$estab = "and c.establecimiento = ".Session::get('idEstablecimiento');
		}

		$mes = $request->input('mes');
		$anno = $request->input('anno');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$resultados = array();
		for($i=1; $i<=$cant_dias; $i++){
			$fecha1 = $anno."-".$mes."-".$i." 00:00:00";
			$fecha2 = $anno."-".$mes."-".$i." 23:59:59";
			$resultado=DB::select(DB::raw("select count(lt.id_lista_transito) cantidad from lista_transito lt, casos c
				where lt.caso = c.id
				".$estab."
				and lt.fecha < '".$fecha2."'
				and (lt.fecha_termino is null or
				(lt.fecha_termino > '".$fecha1."'
				and lt.fecha_termino < '".$fecha2."'));"));
			$resultados[] = intval($resultado[0]->cantidad);
		}

		return response()->json(array("resultados"=>$resultados));
	}

	public function informeDerivacion(){
		$documentos = Caso::select('casos.id as id_caso','pacientes.rut','pacientes.nombre','pacientes.apellido_paterno','pacientes.apellido_materno','pacientes.dv','pacientes.fecha_nacimiento','pacientes.id as paciente_id')
		->distinct("casos.id")

		->join("documento_derivacion_caso","documento_derivacion_caso.caso","=","casos.id")
		->join("pacientes","pacientes.id","=","casos.paciente")
		->get();

		$resultado=[];
		foreach($documentos as $documento){


			$diagnostico = HistorialDiagnostico::where("caso","=",$documento->id_caso)->orderby("fecha","desc")->select("diagnostico")->first();
			$resultado[] =[
				"rut" => $documento->rut,
				"nombre"=>$documento->nombre,
				"apellido_paterno"=>$documento->apellido_paterno,
				"apellido_materno"=>$documento->apellido_materno,
				"dv"=> $documento->dv,
				"fecha_nacimiento"=>date("d-m-Y", strtotime($documento->fecha_nacimiento)),
				"diagnostico" => $diagnostico,
				"paciente_id" => $documento->paciente_id,
				"id_caso" => $documento->id_caso
			];
		}

		return View::make("Estadisticas/informeDerivacion",["documentos"=>$resultado]);

	}

	public function informeDerivacionDatos(Request $request){
		$mes = $request->input('mes');
		$anno = $request->input('anno');

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);

		$fechaInicial = $anno."-".$mes."-01 00:00:00";
		$fechaFin = $anno."-".$mes."-".$numero." 23:59:59";
		$documentos = Caso::select('pacientes.id as id_paciente', 'casos.fecha_termino', 'documento_derivacion_caso.fecha', 'casos.id as id_caso','pacientes.rut','pacientes.nombre','pacientes.apellido_paterno','pacientes.apellido_materno','pacientes.dv','pacientes.fecha_nacimiento','pacientes.id as paciente_id', "th.fecha_ingreso_real")
		->distinct("casos.id")
		->join("documento_derivacion_caso","documento_derivacion_caso.caso","=","casos.id")
		->join("pacientes","pacientes.id","=","casos.paciente")
		->join("t_historial_ocupaciones as th", "th.caso", "=", "casos.id")
		->where('casos.establecimiento', '=', Session::get('idEstablecimiento'))
		->where('casos.fecha_termino', '>=', "$fechaInicial")
		->where('casos.fecha_termino', '<=', "$fechaFin")
		->get();

		$resultado=[];
		foreach($documentos as $documento){
			$diagnostico = HistorialDiagnostico::where("caso","=",$documento->id_caso)->orderby("fecha","desc")->select("diagnostico")->first();

			$fecha1 = new DateTime($documento->fecha);
			$fecha2 = new DateTime($documento->fecha_termino);
			$fechaF = $fecha1->diff($fecha2);
			$diferencia = '';

			if($fechaF->y == 0){
				$diferencia = $fechaF->format('%m meses %a dias %h horas %i minutos');
				if($fechaF->m == 0){
					$diferencia = $fechaF->format('%a dias %h horas %i minutos');
					if($fechaF->d == 0){
						$diferencia = $fechaF->format('%h horas %i minutos');
						if($fechaF->h == 0){
							$diferencia = $fechaF->format('%i minutos');
						}
					}
				}
			}else{
				$diferencia = $fechaF->format('%y años %m meses %a dias %h horas %i minutos');
			}

			$rut = '';
			if($documento->rut){
				$rut = "<a class='info-paciente' href='".url('/')."/busquedaIAAS/paciente/info/".$documento->id_paciente."'>".$documento->rut."-".$documento->dv."</a>";
			}

			$fecha_nac = '';
			if($documento->fecha_nacimiento){
				$fecha_nac = date("d-m-Y", strtotime($documento->fecha_nacimiento));
			}

			$fecha_hosp = '';
			if($documento->fecha_ingreso_real){
				$fecha_hosp = date("d-m-Y", strtotime($documento->fecha_ingreso_real));
			}

			$resultado[] =[
				$rut,
				$documento->nombre,
				$documento->apellido_paterno." ".$documento->apellido_materno,
				$fecha_nac,
				$fecha_hosp,
				$diferencia
			];
		}

		return response()->json(array("aaData"=>$resultado));

	}

	public function pacientesD2D3Datos(Request $request){

		if(Session::get('usuario')->tipo != 'admin_ss'){
			$estab = "establecimientos.id = ".Session::get("idEstablecimiento");
		}else{
			$estab = "TRUE";
		}

		if($request->fecha){
			$fecha = $request->fecha;
		}else{
			$fecha = date("Y-m-d");
		}

		$pacientes_servicios=DB::select(DB::raw("select distinct(tab.id), tab.fecha, tab.riesgo, tab.comentario
			from
			(select casos.id, max(tec.fecha) as fecha, tec.riesgo as riesgo, tec.comentario as comentario
			from casos
			inner join historial_ocupaciones_vista u on u.caso=casos.id
			inner join (select distinct(caso), fecha as fec from t_evolucion_casos where riesgo is not null and urgencia is not true) as t on t.caso = casos.id
			inner join t_evolucion_casos as tec on tec.caso = casos.id
			inner join establecimientos on casos.establecimiento = establecimientos.id
			where
			".$estab." and
			(tec.fecha::date) = '"."$fecha"."' AND
			tec.riesgo is not null and
			t.fec=tec.fecha and
			riesgo in ('D2','D3')
			group by (casos.id, tec.riesgo, tec.comentario))tab
			order by tab.id;"));
			//(tec.fecha::date) = '".date("Y-m-d")."' AND

		$response = array();
		foreach($pacientes_servicios as $pacientes){
			$datos = Caso::select("camas.id_cama", "salas.nombre as nombre_sala", "ue.alias as servicio", "af.nombre as nombre_area", "comuna.nombre_comuna",
				"p.nombre as nombre_paciente", "p.apellido_paterno", "p.apellido_materno", "p.rut", "p.dv", 'th.fecha_ingreso_real', "riesgos.categoria")
			->join("t_historial_ocupaciones_vista_aux as th", "casos.id", "=", "th.caso")
			->join("camas", "camas.id", "=", "th.cama")
			->join("salas", "salas.id", "=", "camas.sala")
			->join("unidades_en_establecimientos as ue", "ue.id", "=", "salas.establecimiento")
			->join("area_funcional as af", "af.id_area_funcional", "=", "ue.id_area_funcional")
			->join("pacientes as p", "p.id", "=", "casos.paciente")
			->join("comuna", "comuna.id_comuna", "=", "p.id_comuna")
			->join("t_evolucion_casos as tec", "casos.id", "tec.caso")
			->join("riesgos", "riesgos.id", "tec.riesgo_id")
			->where("casos.id", "=", $pacientes->id)
			->whereRaw("th.id in (select max(id) from t_historial_ocupaciones_vista_aux where caso='".$pacientes->id."' group by caso)")
			->when(Auth::user()->establecimiento, function ($query){
				return $query->where("casos.establecimiento", Auth::user()->establecimiento);
			})
			//->where("casos.establecimiento", "=", Session::get('idEstablecimiento'))
			->orderBy('tec.fecha', 'desc')
			//->whereRaw("tec.fecha > '2019-02-08' and tec.fecha < '2019-02-09'")
			//->whereRaw("extract(day from tec.fecha) = 07 and extract(month from tec.fecha) = 02")
			->whereRaw("(tec.fecha::date) = '$fecha'")
			->first();




			$pendientes = Examen::where("caso", "=", $pacientes->id)
			->where("pendiente", "=", false)
			->count();

			$diagnostico = HistorialDiagnostico::where("caso","=", $pacientes->id)->orderby("fecha","desc")->select("diagnostico", "id_cie_10")->first();

			$fecha_hospitalizacion=date("d-m-Y H:i", strtotime($datos->fecha_ingreso_real));
			$fecha_actual = Carbon::now();

			$tiempo_dias = $fecha_actual->diffInDays($fecha_hospitalizacion);

			$tiempo_horas = $fecha_actual->diffInHours($fecha_hospitalizacion);

			$horas = $tiempo_horas % 24;

			$dias = round($tiempo_dias, 0, PHP_ROUND_HALF_DOWN);

			$diferencia = "";
			if($dias > 0){
				$diferencia = $diferencia.$dias." dias, ";
			}
			if($horas > 0){
				$diferencia = $diferencia.$horas." horas";
			}else{
				$diferencia = "0 horas";
			}

			$response[] = array(
				ucwords(strtolower($datos->nombre_paciente)),
				ucwords(strtolower($datos->apellido_paterno))." ".ucwords(strtolower($datos->apellido_materno)),
				$datos->rut."-".$datos->dv,
				$datos->nombre_comuna,
				$diagnostico->diagnostico." (".$diagnostico->id_cie_10.")",
				/* $pendientes, */
				$datos->id_cama,
				$datos->nombre_sala,
				$datos->servicio,
				$datos->nombre_area,
				$pacientes->comentario,
				date("d-m-Y", strtotime($datos->fecha_ingreso_real)),
				$diferencia,
				$datos->categoria
			);
		}

		return response()->json(array("aaData"=>$response));

	}


	public function informePromedioSolicitudAsignacion(Request $request){

		return View::make("Estadisticas/informePromedioSolicitudAsignacion");
	}


	public function informeHospitalizacionDomiciliaria(Request $request){

		Carbon::setLocale('es');

		$hasta = Carbon::createFromFormat('m-Y', $request->mesF."-".$request->annoF)->endOfMonth()->format('Y-m-d H:i:s');

		$desde = Carbon::createFromFormat('m-Y', $request->mesI."-".$request->annoI)->startOfMonth()->format('Y-m-d H:i:s');

		$cie10ComunHospDom = HospitalizacionDomiciliaria::cie10MasComunHospDomiciliaria($desde, $hasta);

		$max_hosp_dom_mes = [];
		$prom_hosp_dom_mes = [];
		$min_hosp_dom_mes = [];

		for ($j=1; $j <= 12 ; $j++) {
			$max_hosp_dom_mes []= HospitalizacionDomiciliaria::promedioMesPacientesHospDom($desde, $hasta, $j)->original["max"];
			$prom_hosp_dom_mes [] = HospitalizacionDomiciliaria::promedioMesPacientesHospDom($desde, $hasta, $j)->original["prom"];
			$min_hosp_dom_mes [] = HospitalizacionDomiciliaria::promedioMesPacientesHospDom($desde, $hasta, $j)->original["min"];
		}
		$prom_estadia_domiciliaria = HospitalizacionDomiciliaria::promedioPacientesHospDom($desde, $hasta);
		$numeroPromedioDomicilio = round($prom_estadia_domiciliaria, 0);

		return response()->json([
			"max_hosp_dom_mes" => $max_hosp_dom_mes,
			"prom_hosp_dom_mes" => $prom_hosp_dom_mes,
			"min_hosp_dom_mes" => $min_hosp_dom_mes,
			"numeroPromedioDomicilio" => $numeroPromedioDomicilio,
			"cie10ComunHospDom" => $cie10ComunHospDom->original["diagnostico0"],
			"cie10ComunesHospDom" => $cie10ComunHospDom->original["diagnosticos"],
			"cie10ComunesHospDomCantidad" => $cie10ComunHospDom->original["cant_diagnostico"]
		]);

	}

	public function informeDiagnosticos(Request $request){

		$hasta = Carbon::createFromFormat('m-Y', $request->mesF."-".$request->annoF)->endOfMonth()->format('Y-m-d H:i:s');

		$desde_original = Carbon::createFromFormat('m-Y', $request->mesI."-".$request->annoI)->format('Y-m-d H:i:s');

		if(Session::get("idEstablecimiento")){
			$whereEstablecimiento2 = "c.establecimiento=".Session::get("idEstablecimiento");
		}
		else{
			$whereEstablecimiento2 = "TRUE";
		}

		//Cie 10 mas comunes en el hospital
		$cie_10s = DB::select(DB::Raw("select d.diagnostico ,d.id_cie_10, count(*) as diagnosticos
		from diagnosticos as d   left join casos as c on c.id = d.caso WHERE
		d.fecha <= '".$hasta."'
		AND d.fecha >= '".$desde_original."'
		AND d.id_cie_10 IS NOT NULL AND ".$whereEstablecimiento2."  group by d.diagnostico, d.id_cie_10 order by diagnosticos desc "));

		$lista_cie10 = [];
		$valores_cie10 = [];
		$lista_cie10_completa = [];
		$lista_cie10_pie = [];
		$total_cie10 = 0;
		$otros_diagn = 0;
		$total_cie10_pie = 0;

		foreach ($cie_10s as $key => $cie10_sumar) {
			$total_cie10 += $cie10_sumar->diagnosticos;
		}
		foreach ($cie_10s as $key => $cie10) {
			if ($key < 10) {
				$nombre_cie = DB::table('cie_10')->select('nombre')->where('id_cie_10', '=', $cie10->id_cie_10)->first();
				array_push($lista_cie10,$nombre_cie->nombre);
				array_push($lista_cie10_completa,$cie10->diagnostico);
				array_push($valores_cie10,$cie10->diagnosticos);


				$obj = new \stdClass();
				$obj->name = "$cie10->diagnostico [$cie10->id_cie_10]";
				$obj->cie10 = "[$cie10->id_cie_10]";
				if ($total_cie10 == 0) {
					$obj->y = 0;
				}else{
					$obj->y = $cie10->diagnosticos;
				}

				$total_cie10_pie += $obj->y;


				$obj->color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

				array_push($lista_cie10_pie, $obj);
			}else{
				$otros_diagn += $cie10->diagnosticos;
			}

		}


		array_push($lista_cie10,"Otros diagnósticos");
		array_push($lista_cie10_completa,"Otros diagnósticos");
		array_push($valores_cie10,$otros_diagn);

		return response()->json([

			"lista_cie10" => $lista_cie10,
			"lista_cie10_completa" => $lista_cie10_completa,
			"valores_cie10" =>  $valores_cie10,
			"total_cie10" => $total_cie10,
			"lista_cie10_pie" => $lista_cie10_pie,
			"total_cie10_pie" => $total_cie10_pie,
		]);
	}

	public function informeListaEspera(Request $request){

		$hasta = Carbon::createFromFormat('m-Y', $request->mesF."-".$request->annoF)->endOfMonth()->format('Y-m-d H:i:s');

		$desde = Carbon::createFromFormat('m-Y', $request->mesI."-".$request->annoI)->startOfMonth()->format('Y-m-d H:i:s');

		if(Session::get("idEstablecimiento")){
			$whereEstablecimiento = "id_establecimiento=".Session::get("idEstablecimiento");
			$whereEstablecimiento2 = "c.establecimiento=".Session::get("idEstablecimiento");
		}
		else{
			$whereEstablecimiento = "TRUE";
			$whereEstablecimiento2 = "TRUE";
		}

		$Lunes = [];
		$Martes = [];
		$Miercoles = [];
		$Jueves = [];
		$Viernes = [];
		$Sabado = [];
		$Domingo = [];

		$Enero= [];
		$Febrero= [];
		$Marzo= [];
		$Abril= [];
		$Mayo= [];
		$Junio= [];
		$Julio= [];
		$Agosto= [];
		$Septiembre= [];
		$Octubre= [];
		$Noviembre= [];
		$Diciembre= [];
		$algo = [];

		for ($i=Carbon::parse($desde); $i <= $hasta ; $i->addDay()) {

			$dia = $i->endOfDay()->format("Y-m-d H:i:s");
			$establecimiento = Auth::user()->establecimiento;

			$pacientes = DB::select(DB::Raw("select count(*) as num
			from lista_espera as l
			left join casos as c on c.id = l.caso
			where
			$whereEstablecimiento2
			AND ( (l.fecha <= '$dia'
			AND l.fecha_termino >= '$dia' )
			OR
			(l.fecha <= '$dia'
			AND l.fecha_termino IS NULL) )
			"));

			if ($i->format("l") == "Monday") {
				array_push($Lunes,$pacientes[0]->num);
			}elseif ($i->format("l") == "Tuesday") {
				array_push($Martes,$pacientes[0]->num);
			}elseif ($i->format("l") == "Wednesday") {
				array_push($Miercoles,$pacientes[0]->num);
			}elseif ($i->format("l") == "Thursday") {
				array_push($Jueves,$pacientes[0]->num);
			}elseif ($i->format("l") == "Friday") {
				array_push($Viernes,$pacientes[0]->num);
			}elseif ($i->format("l") == "Saturday") {
				array_push($Sabado,$pacientes[0]->num);
			}else{
				array_push($Domingo,$pacientes[0]->num);
			}


			if ($i->formatLocalized('%B') == "enero" || $i->formatLocalized('%B') == "January") {
				array_push($Enero,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "febrero" || $i->formatLocalized('%B') == "February") {
				array_push($Febrero,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "marzo" || $i->formatLocalized('%B') == "March") {
				array_push($Marzo,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "abril" || $i->formatLocalized('%B') == "April") {
				array_push($Abril,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "mayo" || $i->formatLocalized('%B') == "May") {
				array_push($Mayo,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "junio" || $i->formatLocalized('%B') == "June") {
				array_push($Junio,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "julio" || $i->formatLocalized('%B') == "July") {
				array_push($Julio,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "agosto" || $i->formatLocalized('%B') == "August") {
				array_push($Agosto,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "septiembre" || $i->formatLocalized('%B') == "September") {
				array_push($Septiembre,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "octubre" || $i->formatLocalized('%B') == "October") {
				array_push($Octubre,$pacientes[0]->num);
			}elseif ($i->formatLocalized('%B') == "noviembre" || $i->formatLocalized('%B') == "November") {
				array_push($Noviembre,$pacientes[0]->num);
			}else{
				array_push($Diciembre,$pacientes[0]->num);
			}
		}

		//promedios, minimos y maximos
		if (count($Lunes) == 0) {
			$promedio_lunes = 0;
			$min_lunes = 0;
			$max_lunes = 0;
		}else{
			$promedio_lunes = array_sum($Lunes) / count($Lunes);
			$min_lunes = min($Lunes);
			$max_lunes = max($Lunes);
		}

		if (count($Martes) == 0) {
			$promedio_martes = 0;
			$min_martes = 0;
			$max_martes = 0;
		}else{
			$promedio_martes = array_sum($Martes) / count($Martes);
			$min_martes = min($Martes);
			$max_martes = max($Martes);
		}

		if (count($Miercoles) == 0) {
			$promedio_miercoles = 0;
			$min_miercoles = 0;
			$max_miercoles = 0;
		}else{
			$promedio_miercoles = array_sum($Miercoles) / count($Miercoles);
			$min_miercoles = min($Miercoles);
			$max_miercoles = max($Miercoles);
		}

		if (count($Jueves) == 0) {
			$promedio_jueves = 0;
			$min_jueves = 0;
			$max_jueves = 0;
		}else{
			$promedio_jueves = array_sum($Jueves) / count($Jueves);
			$min_jueves = min($Jueves);
			$max_jueves = max($Jueves);
		}

		if (count($Viernes) == 0) {
			$promedio_viernes = 0;
			$min_viernes = 0;
			$max_viernes = 0;
		}else{
			$promedio_viernes = array_sum($Viernes) / count($Viernes);
			$min_viernes = min($Viernes);
			$max_viernes = max($Viernes);
		}

		if (count($Sabado) == 0) {
			$promedio_sabado = 0;
			$min_sabado = 0;
			$max_sabado = 0;
		}else{
			$promedio_sabado = array_sum($Sabado) / count($Sabado);
			$min_sabado = min($Sabado);
			$max_sabado = max($Sabado);
		}

		if (count($Domingo) == 0) {
			$promedio_domingo = 0;
			$min_domingo = 0;
			$max_domingo = 0;
		}else{
			$promedio_domingo = array_sum($Domingo) / count($Domingo);
			$min_domingo = min($Domingo);
			$max_domingo = max($Domingo);
		}

		//meses
		if (count($Enero) == 0) {
			$promedio_enero = 0;
			$min_enero = 0;
			$max_enero = 0;
		}else{
			$promedio_enero = array_sum($Enero) / count($Enero);
			$min_enero = min($Enero);
			$max_enero = max($Enero);
		}

		if (count($Febrero) == 0) {
			$promedio_Febrero = 0;
			$min_Febrero = 0;
			$max_Febrero = 0;
		}else{
			$promedio_Febrero = array_sum($Febrero) / count($Febrero);
			$min_Febrero = min($Febrero);
			$max_Febrero = max($Febrero);
		}

		if (count($Marzo) == 0) {
			$promedio_Marzo = 0;
			$min_Marzo = 0;
			$max_Marzo = 0;
		}else{
			$promedio_Marzo = array_sum($Marzo) / count($Marzo);
			$min_Marzo = min($Marzo);
			$max_Marzo = max($Marzo);
		}

		if (count($Abril) == 0) {
			$promedio_Abril = 0;
			$min_Abril = 0;
			$max_Abril = 0;
		}else{
			$promedio_Abril = array_sum($Abril) / count($Abril);
			$min_Abril = min($Abril);
			$max_Abril = max($Abril);
		}

		if (count($Mayo) == 0) {
			$promedio_Mayo = 0;
			$min_Mayo = 0;
			$max_Mayo = 0;
		}else{
			$promedio_Mayo = array_sum($Mayo) / count($Mayo);
			$min_Mayo = min($Mayo);
			$max_Mayo = max($Mayo);
		}

		if (count($Junio) == 0) {
			$promedio_Junio = 0;
			$min_Junio = 0;
			$max_Junio = 0;
		}else{
			$promedio_Junio = array_sum($Junio) / count($Junio);
			$min_Junio = min($Junio);
			$max_Junio = max($Junio);
		}

		if (count($Julio) == 0) {
			$promedio_Julio = 0;
			$min_Julio = 0;
			$max_Julio = 0;
		}else{
			$promedio_Julio = array_sum($Julio) / count($Julio);
			$min_Julio = min($Julio);
			$max_Julio = max($Julio);
		}

		if (count($Agosto) == 0) {
			$promedio_Agosto = 0;
			$min_Agosto = 0;
			$max_Agosto = 0;
		}else{
			$promedio_Agosto = array_sum($Agosto) / count($Agosto);
			$min_Agosto = min($Agosto);
			$max_Agosto = max($Agosto);
		}

		if (count($Septiembre) == 0) {
			$promedio_Septiembre = 0;
			$min_Septiembre = 0;
			$max_Septiembre = 0;
		}else{
			$promedio_Septiembre = array_sum($Septiembre) / count($Septiembre);
			$min_Septiembre = min($Septiembre);
			$max_Septiembre = max($Septiembre);
		}

		if (count($Octubre) == 0) {
			$promedio_Octubre = 0;
			$min_Octubre = 0;
			$max_Octubre = 0;
		}else{
			$promedio_Octubre = array_sum($Octubre) / count($Octubre);
			$min_Octubre = min($Octubre);
			$max_Octubre = max($Octubre);
		}

		if (count($Noviembre) == 0) {
			$promedio_Noviembre = 0;
			$min_Noviembre = 0;
			$max_Noviembre = 0;
		}else{
			$promedio_Noviembre = array_sum($Noviembre) / count($Noviembre);
			$min_Noviembre = min($Noviembre);
			$max_Noviembre = max($Noviembre);
		}

		if (count($Diciembre) == 0) {
			$promedio_Diciembre = 0;
			$min_Diciembre = 0;
			$max_Diciembre = 0;
		}else{
			$promedio_Diciembre = array_sum($Diciembre) / count($Diciembre);
			$min_Diciembre = min($Diciembre);
			$max_Diciembre = max($Diciembre);
		}

		$promedios_semana = [round($promedio_lunes,2), round($promedio_martes,2), round($promedio_miercoles,2),round($promedio_jueves,2),round($promedio_viernes,2),round($promedio_sabado,2),round($promedio_domingo,2) ] ;

		$min_semana = [$min_lunes, $min_martes, $min_miercoles, $min_jueves ,$min_viernes , $min_sabado, $min_domingo];

		$max_semana = [$max_lunes, $max_martes, $max_miercoles, $max_jueves,$max_viernes ,$max_sabado ,$max_domingo ];

		$promedios_meses = [round($promedio_enero,2), round($promedio_Febrero,2), round($promedio_Marzo,2),round($promedio_Abril,2),round($promedio_Mayo,2),round($promedio_Junio,2),round($promedio_Julio,2),round($promedio_Agosto,2),round($promedio_Septiembre,2),round($promedio_Octubre,2),round($promedio_Noviembre,2),round($promedio_Diciembre,2) ] ;

		$min_mes = [$min_enero, $min_Febrero, $min_Marzo,$min_Abril,$min_Mayo,$min_Junio,$min_Julio,$min_Agosto,$min_Septiembre,$min_Octubre,$min_Noviembre,$min_Diciembre ] ;

		$max_mes = [$max_enero, $max_Febrero, $max_Marzo,$max_Abril,$max_Mayo,$max_Junio,$max_Julio,$max_Agosto,$max_Septiembre,$max_Octubre,$max_Noviembre,$max_Diciembre ] ;

		return response()->json([
			"promedios_semana" => $promedios_semana,
			"min_semana" => $min_semana,
			"max_semana" => $max_semana,
			"promedios_meses" => $promedios_meses,
			"min_mes" => $min_mes,
			"max_mes" => $max_mes,
		]);
	}

	public function informePromedioSolicitudAsignacionDatos(Request $request){
		$mes = $request->input('mes');
		$anno = $request->input('anno');

		if($mes == 0 && $anno == 0){
			$mes = date("m");
			$anno = date("Y");
		}

		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);
		if($mes == date("m") && $anno == date("Y")){
			$cant_dias = date("d");
		}else{
			$cant_dias = $numero;
		}

		$resultados = array();


		if(Session::get("idEstablecimiento")){
			$whereEstablecimiento2 = "c.establecimiento=".Session::get("idEstablecimiento");
		}
		else{
			$whereEstablecimiento2 = "TRUE";
		}


		for($i=1; $i<=$cant_dias; $i++){
			$fecha2 = $anno."-".$mes."-".$i." 23:59:59";
			$resultado=DB::select(DB::raw("select t.solicitud::date, avg(t.espera_horas)::numeric(6,2) as promedio from
			(select
			c.fecha_ingreso2 as solicitud,
			l.fecha as asignacion,
			(EXTRACT(EPOCH FROM (l.fecha-c.fecha_ingreso2))/60)/60 as espera_horas
			from casos c
			join lista_transito l on c.id=l.caso
			where $whereEstablecimiento2
			and (fecha_ingreso2::date = '$fecha2')
			order by fecha_ingreso2 desc) t

			group by t.solicitud::date
			order by t.solicitud::date desc"));

			if(!empty($resultado)){
				$resultados[] = (float)$resultado[0]->promedio;
			}else{
				$resultados[] = 0;
			}



		}


		return response()->json(array("resultados"=>$resultados));
	}


	public function informeMensualCateg(){
		$establecimiento=Establecimiento::getEstablecimientos();
		return View::make("Estadisticas/informeMensualCateg", ["establecimiento"=>$establecimiento]);

	}

	public function informeMensualCategDatos(Request $request){

		$anno = $request->anno;
		$mes = $request->mes;
		$establecimiento = $request->establecimiento;

		if(empty($anno)){
			$anno = date("Y");
		}
		if(empty($mes)){
			$mes = date("n");
		}
		$numero = cal_days_in_month(CAL_GREGORIAN, $mes, $anno);

		$inicio = $anno."-".$mes."-01 00:00:00";
		$fin = $anno."-".$mes."-".$numero." 23:59:59";


		//CASE WHEN ta.dv=10 THEN 'K' ELSE ta.dv::varchar END,


		$resultados = DB::select(DB::raw("
		SELECT
			'mes' as mes,
			ta.area,
			ta.fecha, --fecha de categorizaci�n
			ta.fecha_hospitalizacion, --ingreso a cama
			ta.egreso, --comentar
			ta.sala,
			ta.run,
			CASE WHEN ta.dv=10 THEN 'K' ELSE ta.dv::varchar END,
			ta.nombres,
			ta.paterno,
			ta.materno,
			ta.fecha_categorizacion,
			ta.categorizacion,
			ta.servicio
		FROM(
			SELECT
				a.nombre AS AREA,
				p.categorizacion AS FECHA, --fecha de categorización
				v.fecha_ingreso_real AS FECHA_HOSPITALIZACION, --ingreso a cama
				c.fecha_termino AS EGRESO, --comentar
				s.nombre AS SALA,
				p.run AS RUN,
				p.dv AS DV,
				p.nombres AS NOMBRES,
				p.paterno AS PATERNO,
				p.materno AS MATERNO,
				p.categorizacion AS FECHA_CATEGORIZACION,
				p.riesgo AS CATEGORIZACION,
				v.nombre_servicio AS SERVICIO,
				CASE
					WHEN (v.fecha_liberacion IS NULL) AND (p.categorizacion>= v.fecha) THEN '1'
					WHEN (v.fecha_liberacion IS NOT NULL) AND (p.categorizacion>= v.fecha AND p.categorizacion<= v.updated_at) THEN '1'
				END AS k,
				v.motivo
			FROM (
				select *
				from retornar_casos_mes('$inicio', '$fin')
			) p
			JOIN t_historial_ocupaciones_vista_aux v ON v.caso=p.caso
			JOIN casos c ON v.caso=c.id
			JOIN camas ca ON v.cama=ca.id
			JOIN salas s ON s.id=ca.sala
			JOIN unidades_en_establecimientos u ON u.id=id_servicio
			JOIN area_funcional a ON a.id_area_funcional=u.id_area_funcional
			WHERE c.establecimiento=$establecimiento
			)ta
		WHERE k='1'
		ORDER BY ta.sala DESC
		"));

		//Porcentaje Mensual de categorización
		$formula1 = 0;
		$formula1_resto = 100;
			//Numero de dias camas en pacientes hospitalziados en el mes
		$countDCHospitalizados = count($resultados);

		if($countDCHospitalizados != 0){
			//Numero de dias categorizados en el mes
			$totalDiasSinCateg = 0;
			foreach($resultados as $resultado){
				if($resultado->categorizacion){
					$totalDiasSinCateg+=1;
				}
			}
			//La formula mensual que nos piden es
			$formula1 = round ( ($totalDiasSinCateg/$countDCHospitalizados)*100, 2);
			$formula1_resto =100 - $formula1 ;
		}



		Excel::create('Camas', function($excel) use ($resultados, $mes, $formula1 ,$formula1_resto)  {
			$excel->sheet('Camas', function($sheet)  use ($resultados, $mes, $formula1, $formula1_resto) {

				$sheet->mergeCells('A1:N1');
				$sheet->setAutoSize(true);

				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {

					// call cell manipulation methods
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");

				});

				$mes2 = Funciones::$meses[$mes];

				$sheet->loadView('Estadisticas.excelMensualCateg', [
					"resultado" => $resultados,
					"mes"=>$mes2,
					"formula1" => $formula1,
					"formula1_resto" => $formula1_resto
				]);
			});
		})->download('xls');

	}


	public function reporteOtrasRegiones(){
		return View::make("Estadisticas/ReporteOtrasRegiones");

	}

	public function otrasRegionesDatos(Request $request){

		$estab = "";
		if(Session::get("usuario")->tipo != "admin_ss"){
			$estab = "and establecimiento = ".Session::get('idEstablecimiento');
		}
		$resultado = [];
		$pacientes = DB::select("select
			c.id, c.fecha_ingreso, c.fecha_termino, establecimiento,
			p.id as id_paciente, p.rut, p.dv, p.nombre, p.apellido_paterno, p.apellido_materno,
			co.id_comuna, co.nombre_comuna,
			r.id_region, r.nombre_region,
			v.fecha_ingreso_real
			from casos c
			join pacientes p on c.paciente=p.id
			join comuna co on co.id_comuna=p.id_comuna
			join region r on r.id_region=co.id_region
			join (select caso, fecha_ingreso_real, max(fecha) from t_historial_ocupaciones_vista_aux group by caso, fecha_ingreso_real) v on v.caso=c.id
			where
			p.nombre not in ('xxxx','sss') and
			r.id_region not in (select id_region from establecimientos where id=8) ".$estab." and
			v.fecha_ingreso_real is not null
			order by r.id_region, c.id");

		foreach($pacientes as $paciente){
			$fecha_fin = "";
			if($paciente->fecha_termino != ""){
				$fecha_fin = date("d-m-Y", strtotime($paciente->fecha_termino));
			}
			$resultado[] = array(
				$paciente->rut."-".$paciente->dv,
				$paciente->nombre,
				$paciente->apellido_paterno." ".$paciente->apellido_materno,
				$paciente->nombre_comuna,
				$paciente->nombre_region,
				date("d-m-Y", strtotime($paciente->fecha_ingreso)),
				date("d-m-Y", strtotime($paciente->fecha_ingreso_real)),
				$fecha_fin
			);
		}

		return response()->json(array("aaData"=>$resultado));

	}


	public function reporteUrgencias(Request $request){
		return View::make("Estadisticas/reporteUrgencias");
	}

	public function reportePacienteEspera(Request $request){

		//opciones lista transito
		$tipos_transito = Consultas::obtenerEnum("tipo_transito");
		$motivos = Consultas::getMotivosLiberacion2();

		return View::make("Estadisticas/ReportePacienteEspera",array(
			"motivo" => $motivos,
			"tipos_transito" => $tipos_transito
		));
	}

	public function reporteRiesgoCategorizacion(Request $request){

		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");

		if (Session::get("usuario")->tipo == 'admin_ss'){
			return View::make("Estadisticas/reporteRiesgoCategorizacion", [
				"establecimiento" => $establecimiento,
				"fecha" => $fecha
			]);
		}

		$data = UnidadEnEstablecimiento::leftJoin("area_funcional as area","unidades_en_establecimientos.id_area_funcional","=","area.id_area_funcional")
		->select("unidades_en_establecimientos.alias as nombre_unidad","unidades_en_establecimientos.id as id","area.nombre as nombre_area")
		->where("unidades_en_establecimientos.establecimiento",Auth::user()->establecimiento)
		->where("unidades_en_establecimientos.id", "<>" , 186)
		->where("unidades_en_establecimientos.visible", true)
		->orderBy("area.nombre","asc")
		->get();


        $response_categoria = [];
        $datosTotal =      [0,0,0,0,0,0,0,0,0,0,0,0,0,0, "Total",0,""];
        $datosPorcentaje = [0,0,0,0,0,0,0,0,0,0,0,0,100,0, "Porcentaje",0,""];
        foreach($data as $dat){
            $categorizaciones = DB::select(DB::Raw("select c.id
            from casos as c
            left join t_historial_ocupaciones as th on th.caso = c.id
            left join camas as ca on ca.id = th.cama
            left join salas as s on s.id = ca.sala
            left join unidades_en_establecimientos as ue on ue.id = s.establecimiento
            where ue.id = $dat->id
            and th.fecha_liberacion is null
            and th.fecha_ingreso_real is not null
            group by c.id"));

            /* inner join t_evolucion_casos as tc on tc.caso = c.id
            and tc.riesgo_id is not null */
			$response_categoria[$dat->id] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0, $dat->nombre_unidad,0, $dat->nombre_area];

            foreach($categorizaciones as $categorizacion){
                $categoria = DB::table("t_evolucion_casos as tc")
                                ->leftjoin("riesgos as r", "r.id","=","tc.riesgo_id")
                                //->whereNotNull("tc.riesgo_id")
                                ->where("tc.caso", $categorizacion->id)
                                ->orderBy("tc.fecha","desc")
                                ->first();

                $restriccion_tiempo = Consultas::restriccionCategorizacionCama($categorizacion->id)->getData()->restriccion;
                if(!is_null($categoria)){
                    if($categoria->urgencia != true){
                        if($categoria->riesgo_id == ''){
                            if($restriccion_tiempo == false){
                                //sin categorizacion habilitada
                                $response_categoria[$dat->id][13] +=1;
                                $datosTotal[13] +=1;
                            }else{
                                //sin Categorizacion bloqueada
                                $response_categoria[$dat->id][15] +=1;
                                $datosTotal[15] +=1;
                            }
                        }else{
                            if("A1" == $categoria->categoria ){
                                $response_categoria[$dat->id][0] +=1;
                                $datosTotal[0] +=1;
                            }else if("A2" == $categoria->categoria){
                                $response_categoria[$dat->id][1] +=1;
                                $datosTotal[1] +=1;
                            }else if("A3" == $categoria->categoria){
                                $response_categoria[$dat->id][2] +=1;
                                $datosTotal[2] +=1;
                            }else if("B1" == $categoria->categoria){
                                $response_categoria[$dat->id][3] +=1;
                                $datosTotal[3] +=1;
                            }else if("B2" == $categoria->categoria){
                                $response_categoria[$dat->id][4] +=1;
                                $datosTotal[4] +=1;
                            }else if("B3" == $categoria->categoria){
                                $response_categoria[$dat->id][5] +=1;
                                $datosTotal[5] +=1;
                            }else if("C1" == $categoria->categoria){
                                $response_categoria[$dat->id][6] +=1;
                                $datosTotal[6] +=1;
                            }else if("C2" == $categoria->categoria){
                                $response_categoria[$dat->id][7] +=1;
                                $datosTotal[7] +=1;
                            }else if("C3" == $categoria->categoria){
                                $response_categoria[$dat->id][8] +=1;
                                $datosTotal[8] +=1;
                            }else if("D1" == $categoria->categoria){
                                $response_categoria[$dat->id][9] +=1;
                                $datosTotal[9] +=1;
                            }else if("D2" == $categoria->categoria){
                                $response_categoria[$dat->id][10] +=1;
                               $datosTotal[10] +=1;
                            }else if("D3" == $categoria->categoria){
                                $response_categoria[$dat->id][11] +=1;
                                $datosTotal[11] +=1;
                            }
                        }
                        $response_categoria[$dat->id][12] +=1;
                        $datosTotal[12] +=1;
                    }
                }

            }
		}

		foreach($datosTotal as $key => $datos){
			if($key != 14 || $key != 16){
				if($datos == 0){
					$datosPorcentaje[$key] = 0;
				}else{
					$datosPorcentaje[$key] = round($datos * 100 / $datosTotal[12],2);
				}
			}
        }

        $response_categoria["total"] = $datosTotal;
		$response_categoria["porcentaje"] = $datosPorcentaje;

		//return UnidadEnEstablecimiento::pluck("id","alias");
		//return  UnidadEnEstablecimiento::selectGenerarMapaServicios();
		return View::make("Estadisticas/reporteRiesgoCategorizacion", [
			"establecimiento" => $establecimiento,
			"fecha" => $fecha,
			"categorizacion" => $response_categoria,
			"unidades" => UnidadEnEstablecimiento::selectGenerarMapaServicios()
		]);
	}



	public function reporteUrgenciasGeneral(Request $request){

		// Pacientes con t° espera

		$datos=DB::table("lista_espera as l")
		->join("casos as c", "c.id", "=", "l.caso")
		->whereNull("l.fecha_termino")
		->when(Auth::user()->establecimiento, function ($query){
			return $query->where("c.establecimiento", Auth::user()->establecimiento);
		})
		->select("c.fecha_ingreso2 as solicitud")
		->get();


		$cant = 0;
		foreach($datos as $dato){

			$fecha_solicitud=date("d-m-Y H:i", strtotime($dato->solicitud));
			$hoy = Carbon::now();
			$fechaCarbon = Carbon::parse($fecha_solicitud);
			$diff = $hoy->diffInHours($fechaCarbon);


			if($hoy >= $fechaCarbon && $diff >= 12){
				$cant++;
			}

		}
		// num casos ingresados desde urgencias y que han sido hospitalizados


		$casosIngresadosHospitalizados=DB::table("casos as c")
		->join("t_historial_ocupaciones as ho", 'ho.caso',"=","c.id")
		->whereNotNull("ho.fecha_ingreso_real")
		->where("ho.fecha_ingreso_real", ">=", date("Y-m-d")." 00:00:00")
		->where("ho.fecha_ingreso_real", "<=", date("Y-m-d")." 23:59:59")
		->where("detalle_procedencia","=","Servicio de urgencias")
		->when(Auth::user()->establecimiento, function ($query){
			return $query->where("c.establecimiento", Auth::user()->establecimiento);
		})
		->distinct('c.id')
		->count('c.id');

		$casosIngresados=DB::table("casos as c")
		->where("detalle_procedencia","=","Servicio de urgencias")
		->where("c.fecha_ingreso2", ">=", date("Y-m-d")." 00:00:00")
		->where("c.fecha_ingreso2", "<=", date("Y-m-d")." 23:59:59")
		->when(Auth::user()->establecimiento, function ($query){
			return $query->where("c.establecimiento", Auth::user()->establecimiento);
		})
		//->select("c.fecha_ingreso2 as solicitud")
		->count();


		// promedio de espera lista transito

		$listaTransito = ListaTransito::whereNotNUll("fecha_termino")
			->where("fecha", ">=", date("Y-m-d")." 00:00:00")
			->where("fecha", "<=", date("Y-m-d")." 23:59:59")
			->get();
		$sumaHoras = 0;
		foreach($listaTransito as $lista){

			$fecha_inicio=date("d-m-Y H:i", strtotime($lista->fecha));
			$fecha_termino=date("d-m-Y H:i", strtotime($lista->fecha_termino));
			$fecha_inicio = Carbon::parse($fecha_inicio);
			$fecha_termino = Carbon::parse($fecha_termino);
			$diff = $fecha_termino->diffInHours($fecha_inicio);


			$sumaHoras += $diff;


		}
		if(count($listaTransito) != 0){
			$promedio = $sumaHoras/count($listaTransito);
		}else{
			$promedio = 0;
		}


		return response()->json(array(
			"dato1"=>(int)$promedio,
			"dato2"=>$cant,
			"dato3"=>$casosIngresadosHospitalizados,
			"dato4"=>$casosIngresados
		));
	}

	/*Reporte de urgencias (Lo mismo que riesgo y categorizacion pero cambiando la condicion de f a t)*/
	public function reporteUrgencias2(Request $request){

		$establecimiento=Establecimiento::getEstablecimientos();
		$fecha=date("d-m-Y");


		$response_categoria = [];
		
        $unidad_obj = Session::get("unidades")->filter( function ($unidad){
            return  $unidad->alias== "urgencia";
        });

        foreach($unidad_obj as $unid){
            $categorizaciones = DB::select(DB::Raw("select caso as id
        from historial_ocupaciones_vista
        where id_servicio = $unid->id
        and rk = 1
        and fecha_liberacion is null"));

            $response_categoria = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

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
                                } else if ("A2" == $categoria->categoria) {
                                    $response_categoria[1] += 1;
                                } else if ("A3" == $categoria->categoria) {
                                    $response_categoria[2] += 1;
                                } else if ("B1" == $categoria->categoria) {
                                    $response_categoria[3] += 1;
                                } else if ("B2" == $categoria->categoria) {
                                    $response_categoria[4] += 1;
                                } else if ("B3" == $categoria->categoria) {
                                    $response_categoria[5] += 1;
                                } else if ("C1" == $categoria->categoria) {
                                    $response_categoria[6] += 1;
                                } else if ("C2" == $categoria->categoria) {
                                    $response_categoria[7] += 1;
                                } else if ("C3" == $categoria->categoria) {
                                    $response_categoria[8] += 1;
                                } else if ("D1" == $categoria->categoria) {
                                    $response_categoria[9] += 1;
                                } else if ("D2" == $categoria->categoria) {
                                    $response_categoria[10] += 1;
                                } else if ("D3" == $categoria->categoria) {
                                    $response_categoria[11] += 1;
                                }
                            }else{

                                if($restriccion_tiempo == false){
                                    $response_categoria[13] += 1;
                                }else{
                                    $response_categoria[14] += 1;
                                }
                            }
                        }else{

                            if($restriccion_tiempo == false){
                                $response_categoria[13] += 1;
                            }else{
                                $response_categoria[14] += 1;
                            }
                        }
                    }else{

                        if($restriccion_tiempo == false){
                            $response_categoria[13] += 1;
                        }else{
                            $response_categoria[14] += 1;
                        }
                    }
                }
                $response_categoria[12] += 1;

            }


        }


		return View::make("Estadisticas/ReporteUrgencias2", [
			"establecimiento" => $establecimiento,
			"fecha" => $fecha,
			"categorizacion" => $response_categoria
		]);
	}

	public function salidaUrgencias($fecha){

		$formatoFecha = "d-m-Y H:i:s";
		$startDay = Carbon::parse($fecha)->startOfDay();
		$endDay   = $startDay->copy()->endOfDay();

		$fecha = Carbon::parse($fecha)->format("d/m/Y");

		$id_establecimiento = Auth::user()->establecimiento;

		if($id_establecimiento != null){
			$establecimiento = "u.establecimiento =".$id_establecimiento;
		}else{
			$establecimiento = "true";
		}

		$historiales = DB::select(DB::Raw("select t.id, t.caso,t.fecha_liberacion, t.fecha_ingreso_real ,t.motivo from t_historial_ocupaciones t where t.fecha_liberacion is not null
		and t.cama in (
		 select c.id
		 from unidades_en_establecimientos as u
		 inner join salas as s on s.establecimiento = u.id
		 inner join camas as c on c.sala = s.id
		 where u.url = 'urgencia' and ".$establecimiento."
		)
		and t.fecha_liberacion >= '".$startDay."'
		and t.fecha_liberacion <= '".$endDay."'"));



		$response = [];
		foreach($historiales as $historial){
			$nombrePaciente = "";
			$identificacion = "";
			$diagnostico = "";
			$fechaIngreso = "";
			$fechaSalida = "";
			$destino = "";

			//nombre del paciente
			$pacienteInfo = Paciente::select("pacientes.nombre","pacientes.apellido_paterno","pacientes.apellido_materno","pacientes.rut","pacientes.dv","pacientes.n_identificacion","pacientes.identificacion")
								->join("casos as c", "c.paciente","pacientes.id")
								->where("c.id", $historial->caso)
								->first();
			$nombrePaciente = $pacienteInfo->nombre." ".$pacienteInfo->apellido_paterno." ".$pacienteInfo->apellido_materno;

			//run del paciente
			$dv = ($pacienteInfo->dv == 10)?"K":$pacienteInfo->dv;
			$identificacion = ($pacienteInfo->identificacion == "run")?$pacienteInfo->rut."-".$dv:$pacienteInfo->n_identificacion;

			//camas de urgencia en todos los hospitales
			$camas_urgencia = UnidadEnEstablecimiento::select("c.id")
										->join("salas as s","s.establecimiento","unidades_en_establecimientos.id")
										->join("camas as c","c.sala","s.id")
										->where("unidades_en_establecimientos.url","urgencia")
										->where("unidades_en_establecimientos.establecimiento", $id_establecimiento)
										->get();
			$array_urgencia = [];
			foreach($camas_urgencia as $key => $camas){
				array_push($array_urgencia,$camas->id) ;
			}

			//si es un alta se mostraran los datos
			if($historial->motivo == "alta"){
				//diagnostico
				$diagnosticoInfo = DB::select(DB::Raw("select d.id_cie_10 as cie10, d.diagnostico from casos as c
				inner join diagnosticos as d on d.caso = c.id
				where c.id = ".$historial->caso."
				and d.fecha <= '".$historial->fecha_liberacion."'
				order by d.fecha desc
				limit 1"));
				if(isset($diagnosticoInfo[0])){
					$diagnostico = "(".$diagnosticoInfo[0]->cie10.") ".$diagnosticoInfo[0]->diagnostico;
				}

				//Fecha ingreso, salida y destino alta

				$fechaIngreso = Carbon::parse($historial->fecha_ingreso_real)->format($formatoFecha);
				$fechaSalida = Carbon::parse($historial->fecha_liberacion)->format($formatoFecha);

				$destino = "Alta";

				$response [] =[
					"nombrePaciente" => $nombrePaciente,
					"rut" => $identificacion,
					"diagnostico" => $diagnostico,//Calcular ultimo diagnostico desde du fecha de liberacion hacia atras
					"fechaIngreso" => $fechaIngreso,
					"fechaSalida" => $fechaSalida,
					"destino" => $destino
				];

			}else{
				//siguiente lugar donde fue a parar el paciente
				$sig_historial = THistorialOcupaciones::where("caso", $historial->caso)
								->where("id",">", $historial->id)
								->orderBy("id", "asc")
								->first();

				//revisar que ese traslado sea hacia otra unidad y no a la misma
				if(!in_array($sig_historial->cama, $array_urgencia)){
					//diagnostico
					$diagnosticoInfo = DB::select(DB::Raw("select d.id_cie_10 as cie10, d.diagnostico from casos as c
					inner join diagnosticos as d on d.caso = c.id
					where c.id = ".$historial->caso."
					and d.fecha <= '".$historial->fecha_liberacion."'
					order by d.fecha desc
					limit 1"));
					if(isset($diagnosticoInfo[0])){
						$diagnostico = "(".$diagnosticoInfo[0]->cie10.") ".$diagnosticoInfo[0]->diagnostico;
					}

					$fechaIngreso = Carbon::parse($historial->fecha_ingreso_real)->format($formatoFecha);
					$fechaSalida = Carbon::parse($historial->fecha_liberacion)->format($formatoFecha);

					$ubicacion = UnidadEnEstablecimiento::select("unidades_en_establecimientos.alias as unidad","s.nombre as sala","c.id_cama as cama")
									->join("salas as s","s.establecimiento","unidades_en_establecimientos.id")
									->join("camas as c","c.sala","s.id")
									->where("c.id",$sig_historial->cama)
									->first();
					$destino = $ubicacion->unidad." -> ".$ubicacion->sala." -> ".$ubicacion->cama;

					$response [] =[
						"nombrePaciente" => $nombrePaciente,
						"rut" => $identificacion,
						"diagnostico" => $diagnostico,//Calcular ultimo diagnostico desde du fecha de liberacion hacia atras
						"fechaIngreso" => $fechaIngreso,
						"fechaSalida" => $fechaSalida,
						"destino" => $destino
					];

				}

			}
		}

		$hospital = Establecimiento::getNombre($id_establecimiento);

		try {
			$snappyPdf = PDF::loadView('TemplatePDF.reporteUrgenciaDiaria',[
				"informacion" => $response,
				"fecha" => $fecha,
				"hospital" => $hospital
			]);

			return $snappyPdf->download('Salida_de_Urgencias_'.$fecha.'.pdf');

		} catch (Exception $e) {
			return response()->json($e->getMessage());
		}

	}

	public function estadiaUrgencias($mes, $anno){

		$formatoFecha = "d-m-Y H:i:s";
		$fecha = $anno."-".$mes."-1";

		$startMonth = Carbon::parse($fecha)->startOfMonth();
		$endMonth   = $startMonth->copy()->endOfMonth();

		$fecha = Carbon::parse($fecha)->format("d/m/Y");

		$id_establecimiento = Auth::user()->establecimiento;

		if($id_establecimiento != null){
			$establecimiento = "u.establecimiento =".$id_establecimiento;
		}else{
			$establecimiento = "true";
		}

		$historiales = DB::select(DB::Raw("select t.caso, t.id, t.fecha_liberacion, t.motivo, t.fecha_ingreso_real from t_historial_ocupaciones t
		where t.cama in (
		 select c.id
		 from unidades_en_establecimientos as u
		 inner join salas as s on s.establecimiento = u.id
		 inner join camas as c on c.sala = s.id
		 where u.url = 'urgencia' and ".$establecimiento."
		)
		and t.fecha_ingreso_real >= '".$startMonth."'
		and t.fecha_ingreso_real <= '".$endMonth."'
		order by id asc"));


		//camas de urgencia en todos los hospitales
		$camas_urgencia = UnidadEnEstablecimiento::select("c.id")
			->join("salas as s","s.establecimiento","unidades_en_establecimientos.id")
			->join("camas as c","c.sala","s.id")
			->where("unidades_en_establecimientos.url","urgencia")
			->where("unidades_en_establecimientos.establecimiento", $id_establecimiento)
			->get();
		$array_urgencia = [];
		foreach($camas_urgencia as $key => $camas){
			array_push($array_urgencia,$camas->id) ;
		}

		$response = [];
		$historialUrgenciaUsado = [];
		$sobre12Horas = 0;
		foreach($historiales as $historial){
			$nombrePaciente = "";
			$identificacion = "";
			$diagnostico = "";
			$fechaIngreso = "";
			$fechaSalida = "";
			$destino = "";
			$horasDiff = 0;


			if(!in_array($historial->id, $historialUrgenciaUsado)){
				//Como no se ha revisado este historial, el id_historial se revisa
				//nombre del paciente
				$pacienteInfo = Paciente::select("pacientes.nombre","pacientes.apellido_paterno","pacientes.apellido_materno","pacientes.rut","pacientes.dv","pacientes.n_identificacion","pacientes.identificacion")
				->join("casos as c", "c.paciente","pacientes.id")
				->where("c.id", $historial->caso)
				->first();
				$nombrePaciente = $pacienteInfo->nombre." ".$pacienteInfo->apellido_paterno." ".$pacienteInfo->apellido_materno;

				//run del paciente
				$dv = ($pacienteInfo->dv == 10)?"K":$pacienteInfo->dv;
				$identificacion = ($pacienteInfo->identificacion == "run")?$pacienteInfo->rut."-".$dv:$pacienteInfo->n_identificacion;

				//si es un alta se mostraran los datos
				if($historial->motivo != "traslado interno"){
					//el paciente fue dado de alta ya sea al domicilio o otra, pero esta n oes un traslado interno
					$fecha_final = ($historial->fecha_liberacion == null)? Carbon::now()->format("Y-m-d"):$historial->fecha_liberacion;

					//diagnostico
					$diagnosticoInfo = DB::select(DB::Raw("select d.id_cie_10 as cie10, d.diagnostico from casos as c
						inner join diagnosticos as d on d.caso = c.id
						where c.id = ".$historial->caso."
						and d.fecha <= '".$fecha_final."'
						order by d.fecha desc
						limit 1"));
					if(isset($diagnosticoInfo[0])){
					$diagnostico = "(".$diagnosticoInfo[0]->cie10.") ".$diagnosticoInfo[0]->diagnostico;
					}

					//Fecha ingreso, salida y destino alta
					$fechaIngreso = Carbon::parse($historial->fecha_ingreso_real);
					$fechaSalida = Carbon::parse($historial->fecha_liberacion);

					//tiempo de estadia en horas
					$horasDiff = $fechaIngreso->diffInHours($fechaSalida);

					if($horasDiff >= 12 )
						$sobre12Horas+= 1;

					$destino = "Alta";

					$response [] =[
						"nombrePaciente" => $nombrePaciente,
						"rut" => $identificacion,
						"diagnostico" => $diagnostico,//Calcular ultimo diagnostico desde du fecha de liberacion hacia atras
						"fechaIngreso" => $fechaIngreso->format($formatoFecha),
						"fechaSalida" => $fechaSalida->format($formatoFecha),
						"estadia" => $horasDiff,
						"destino" => $destino
					];


				}else{
					//el paciente se le realizo un traslado interno
					//Calcular tiempo en horas que estuvo el paciente
					$fechaIngreso = Carbon::parse($historial->fecha_ingreso_real);
					$fechaSalida = Carbon::parse($historial->fecha_liberacion);
					$historial_tmp = $historial;
					do{
						//siguiente caso
						$sig_historial = THistorialOcupaciones::where("caso", $historial_tmp->caso)
								->where("id",">", $historial_tmp->id)
								->orderBy("id", "asc")
								->first();


						//preguntar si el siguiente historial se encuentra
						if(!in_array($sig_historial->cama, $array_urgencia)){
							//se fue a otra area distinta de urgencia y no queda nada mas que guardar los datos de cuanto tiempo estubo
							//diagnostico

							$fecha_final = ($fechaSalida == null)? Carbon::now()->format("Y-m-d"):$fechaSalida;

							$diagnosticoInfo = DB::select(DB::Raw("select d.id_cie_10 as cie10, d.diagnostico from casos as c
							inner join diagnosticos as d on d.caso = c.id
							where c.id = ".$historial->caso."
							and d.fecha <= '".$fecha_final."'
							order by d.fecha desc
							limit 1"));

							if(isset($diagnosticoInfo[0])){
								$diagnostico = "(".$diagnosticoInfo[0]->cie10.") ".$diagnosticoInfo[0]->diagnostico;
							}
							//destino del paciente
							$ubicacion = UnidadEnEstablecimiento::select("unidades_en_establecimientos.alias as unidad","s.nombre as sala","c.id_cama as cama")
											->join("salas as s","s.establecimiento","unidades_en_establecimientos.id")
											->join("camas as c","c.sala","s.id")
											->where("c.id",$sig_historial->cama)
											->first();

							$destino = $ubicacion->unidad." -> ".$ubicacion->sala." -> ".$ubicacion->cama;

							//Calcular la diferencia de fecha de ingreso y la final
							$horasDiff += $fechaIngreso->diffInHours($fechaSalida);

							if($horasDiff >= 12 )
								$sobre12Horas+= 1;

							$response [] =[
								"nombrePaciente" => $nombrePaciente,
								"rut" => $identificacion,
								"diagnostico" => $diagnostico,//Calcular ultimo diagnostico desde du fecha de liberacion hacia atras
								"fechaIngreso" => Carbon::parse($historial->fecha_ingreso_real)->format($formatoFecha),
								"fechaSalida" => $fechaSalida->format($formatoFecha),
								"estadia" => $horasDiff,
								"destino" => $destino
							];
						}else{
							$fechaSalida = Carbon::parse($sig_historial->fecha_liberacion);
							$historial_tmp	= $sig_historial;

							//se contaviliza ete id historail como revisado
							array_push($historialUrgenciaUsado, $sig_historial["id"] );

						}

					}while(in_array($sig_historial->cama, $array_urgencia));

				}

			}

		}

		//Calcular porcentaje de pacientes sobre las 12 horas en servicio de urgencia
		//Formula porcentaje = (pacientes sobre 12 horas / total pacientes en urgencia) * 100
		//Porcentaje Mensual de categorización
		$formula1 = 0;
		$formula1_resto = 0;

		if(!empty($response)){
			//Numero de dias categorizados en el mes
			$formula1 = round( ($sobre12Horas / count($response))*100 ,2);

			$formula1_resto = 100 - $formula1 ;
		}

		$hospital = Establecimiento::getNombre($id_establecimiento);
		$fecha = Carbon::parse($fecha)->format("m-Y");

		try {
			$snappyPdf = PDF::loadView('TemplatePDF.reporteUrgenciaMensual',[
				"informacion" => $response,
				"fecha" => $fecha,
				"hospital" => $hospital,
				"porcentaje" => $formula1,
				"porcentaje_resto" => $formula1_resto,
			]);

			return $snappyPdf->download('Salida_de_Urgencias_'.$fecha.'.pdf');
		} catch (Exception $e) {
			return response()->json($e->getMessage());
		}

	}

	public function reporteEspecialidades(){
		$datos = HistorialEspecialidades::consultaTotalEspecialidades("0","Dia");


		return View::make("Estadisticas/ReporteEspecialidades",array(
			"datos" => $datos
		));
	}


	public function excelReporteEspecialidades($anno,$mes,$tipo){
		$dateTimeString = $anno."-".$mes."-"."01";
		$fecha = Carbon::createFromFormat('Y-m-d', $dateTimeString);
		$nombreDelMes = '';
		$meses = ["01" => "Enero","02" => "Febrero","03" => "Marzo","04" => "Abril","05" => "Mayo","06" => "Junio","07" => "Julio","08" => "Agosto","09" => "Septiembre","10" => "octubre","11" => "Noviembre","12" => "Diciembre"];
		foreach ($meses as $key => $nombreMes) {
			if($key == $mes){
				$nombreDelMes = $nombreMes;
			}
		}
		
		$datos = HistorialEspecialidades::consultaTotalEspecialidades($fecha,$tipo);

		Excel::create('ReporteEspecialidades', function($excel) use ($datos, $nombreDelMes, $anno) {
			$excel->sheet('ReporteEspecialidades', function($sheet) use ($datos, $nombreDelMes, $anno) {
				$sheet->mergeCells('A1:H1');
				$sheet->setAutoSize(true);
				$sheet->setHeight(1,50);
				$sheet->row(1, function($row) {
					$row->setBackground('#1E9966');
					$row->setFontColor("#FFFFFF");
					$row->setAlignment("center");
				});

				$fechaActual = Carbon::now();
				$idEstablecimiento = Auth::user()->establecimiento;
				$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();
				
				$sheet->loadView('Estadisticas.ReporteEspecialidadesExcel',[
					"hoy" => $fechaActual,
					"nombreMes" => $nombreDelMes,
					"anno" => $anno,
					"establecimiento" => $nombreEstablecimiento,
					"datos" => $datos
				]);
			});
		})->download('xls');
	}
}

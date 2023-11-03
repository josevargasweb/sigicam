<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establecimiento; 
use App\Models\UnidadEnEstablecimiento;
use App\Models\THistorialOcupaciones;
use App\Models\Caso;
use App\Models\Cama;
use Session;

use Auth;
use Carbon\Carbon;
use PDF;
use Log;
use DB;
use Excel;

use DateTime;
class EstadisticasController extends Controller{

	public function getUnidades(Request $request){
		$unidad=$request->input("unidad");
		$response=array();
        $unidades = UnidadEnEstablecimiento::conCamas()->whereEstablecimiento($unidad)->whereVisible(true)->orderBy("alias")->get();

		foreach ($unidades as $u) {
			$response[]=array("id" => $u->id, "alias" => $u->alias);
		}
		return response()->json($response);

	}

	public function reporteMensualEstadistico(){		
		$servicios = UnidadEnEstablecimiento::where('establecimiento', Auth::user()->establecimiento)->pluck('alias', 'id');
		return view("NuevasEstadisticas/ReporteMensual", ["servicios" => $servicios]);

	}

	public function informacionREMCamas($anno, $mes){

		$dotacion_total = 0;
		$disponible_total = 0;
		$prom_disponible_total = 0;
		$total_contador_dias_camas_ocupadas = 0;
		$total_contador_dias_camas_ocupadas_beneficiarios = 0;
		$total_dias_estada_suma = 0;
		$total_egresos_suma =0;
		$total_dias_beneficiarios_servicio = 0;
		$indice_rotacion_total =0;
		$intervalo_sustitucion_total=0;
		$porcentaje_ocupacional_total=0;
		$total_existencias = 0;
		$total_bloqueadas_servicios=0;

		$establecimiento = Auth::user()->establecimiento;
		$mes_inicio = Carbon::createFromFormat('Y-m', $anno."-".$mes)
							->startOfMonth()->toDateTimeString();
		$mes_final = Carbon::createFromFormat('Y-m', $anno."-".$mes)
							->endOfMonth()->toDateTimeString();

		//Se busca undades en establecimientos que esten visibles, en caso de que estas esten en falso, deben procurar que el update haya estado dentro del mes actual o posterior, o en caso de que sea visible true
		$servicios = DB::select(DB::Raw("select * from unidades_en_establecimientos 
			where establecimiento = $establecimiento
			and created_at <= '$mes_final'
			and (
			(visible is false and updated_at >= '$mes_inicio')
			or
			visible is true
			)
			order By alias asc") ) ;
		
		$info = [];

		
	
		$dias_mes = Carbon::createFromFormat('Y-m', $anno."-".$mes)->daysInMonth;

		foreach ($servicios as $servicio) {

			$total_bloqueada = 0;
			$total_egresos = 0;
			$contador_dias_camas_ocupadas = 0;
			$contador_dias_camas_ocupadas_beneficiarios = 0;

			$dotacion_servicio_count = DB::select(DB::Raw("select count(*) 
				from unidades_en_establecimientos as u 
				join salas as s on s.establecimiento = u.id
				join camas as c on c.sala = s.id
				left join historial_eliminacion_camas as t on t.cama = c.id
				where u.id = $servicio->id
				--Validacion Cama
				and c.created_at <= '$mes_final'
				and (
				(t.fecha is not null and t.fecha >= '$mes_inicio' )
				or 
				t.fecha is null
				) 
				--Validacion Sala
				and s.created_at <= '$mes_final'
				and (
				s.visible is true 
				or 
				(s.visible is false and (s.updated_at >= '$mes_inicio' and s.updated_at is not null))
				)"));
			$dotacion_servicio = $dotacion_servicio_count[0]->count;

			

		

			$dotacion_total += $dotacion_servicio; 
			$letalidad = DB::select(DB::raw("
				SELECT COALESCE( 
					( 
								( 
								SELECT COALESCE(Count(*)*100,0) AS total_fallecidos 
								FROM   t_historial_ocupaciones_vista_aux h 
								JOIN   casos c 
								ON     c.id=h.caso 
								WHERE  fecha_termino IS NOT NULL 
								AND    fecha_termino >= '".$mes_inicio."' 
								AND    fecha_termino <= '".$mes_final."' 
								AND    ( 
											motivo_termino='fallecimiento' 
									OR     detalle_termino='Fallecimiento') 
								AND    h.id IN 
									( 
												SELECT   Max(id) 
												FROM     t_historial_ocupaciones 
												GROUP BY caso) 
								AND    h.id_servicio=".$servicio->id." ) / 
							( 
									SELECT NULLIF( Count(DISTINCT h.caso) ,0) AS total_egresos 
									FROM   t_historial_ocupaciones_vista_aux h 
									JOIN   casos c 
									ON     c.id=h.caso 
									WHERE  fecha_liberacion IS NOT NULL 
									AND    fecha_liberacion >= '".$mes_inicio."' 
									AND    fecha_liberacion <= '".$mes_final."' 
									AND    h.id_servicio=".$servicio->id.") 
							) ,0
				) as porcentaje_letalidad"
			
			));


			$prom = "select avg(estada) as promedio";
			$total_dias = "select sum(estada) as total";
			
			$whereBeneficiarios = " c.prevision::varchar ilike '%FONASA%' AND ";

			$sql_1 = "
			from
			(select caso, sum(t_estada_null::numeric) as estada from
			(select
			h.id,
			h.cama,
			h.caso,
			h.fecha,
			h.fecha_ingreso_real,
			h.fecha_liberacion as fecha_liberacion_cama,
			c.fecha_termino as fecha_alta,
			c.prevision,
			CASE
				--caso cuando egresa el mismo mes que ingresa
				WHEN (h.contador=1 and h.fecha_liberacion is not null and (h.fecha_liberacion <= '".$mes_final."'::timestamp and h.fecha_liberacion >= '".$mes_inicio."'::timestamp)) 
					--estada = fecha de liberacion - fecha de hospitalizacion  
					THEN (EXTRACT(day FROM h.fecha_liberacion)-EXTRACT(day FROM h.fecha_ingreso_real))+1
				--caso cuando ingresa el mes actual pero aun no egresa
				WHEN (h.contador=1 and h.fecha_liberacion is null)  
					--estada = fin de mes - fecha de hospitalizacion  
					THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM h.fecha_ingreso_real))+1
				--caso cuando egresa el mismo mes que ingresa, pero ha sido cambiado de cama antes
				WHEN (h.contador>1 and h.fecha_liberacion is not null and (h.fecha_liberacion <= '".$mes_final."'::timestamp and h.fecha_liberacion >= '".$mes_inicio."'::timestamp))   
					THEN (EXTRACT(day FROM h.fecha_liberacion)-EXTRACT(day FROM h.fecha))+1
				--caso cuando ingresa el mes actual pero aun no egresa
				WHEN (h.contador>1 and h.fecha_liberacion is null)  
					THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM h.fecha))+1
			END as t_estada_null,
			h.contador
			from t_historial_ocupaciones_vista_aux h
			join casos c on c.id=h.caso
			where
			--ingresa dentro del mes
			(h.fecha_ingreso_real<= '".$mes_final."' and h.fecha_ingreso_real>= '".$mes_inicio."') and
			--solcita camas dentro del mes
			(h.fecha <= '".$mes_final."' and h.fecha >= '".$mes_inicio."') and
			--egresa antes de fin de mes o no egresa ese mes
			((h.fecha_liberacion <= '".$mes_final."' and h.fecha_liberacion >= '".$mes_inicio."') or h.fecha_liberacion is null) and
			h.id_servicio=".$servicio->id."
			union
			--caso cuando el paciente es hospitalizado en meses anteriores y egresa el mes de rem o se mantiene hospitalizado todo ese mes  
			select
			h.id,
			h.cama,
			h.caso,
			h.fecha,
			h.fecha_ingreso_real,
			h.fecha_liberacion as fecha_liberacion_cama,
			c.fecha_termino as fecha_alta,
			c.prevision,
			CASE
				WHEN (h.contador=1 and h.fecha_liberacion::timestamp is not null and (h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp))  THEN (EXTRACT(day FROM h.fecha_liberacion::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador=1 and h.fecha_liberacion::timestamp is null)  THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador>1 and h.fecha_liberacion::timestamp is not null and (h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp))  THEN (EXTRACT(day FROM h.fecha_liberacion::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador>1 and h.fecha_liberacion::timestamp is null) THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
			END as t_estada_null,
			h.contador
			from t_historial_ocupaciones_vista_aux h
			join casos c on c.id=h.caso
			where
			";
			
			$sql_2 = "
			--ingresa dentro de otro mes
			(h.fecha_ingreso_real < '".$mes_inicio."') and
			--egresa antes de fin de mes o no egresa ese mes
			((h.fecha_liberacion <= '".$mes_final."' and h.fecha_liberacion >= '".$mes_inicio."') or h.fecha_liberacion is null) and
			h.id_servicio=".$servicio->id."
			) tabla_suma
			group by caso
			) tabla_promedio";


			//Las existencias son personas que vienen del mes pasado y pueden ver sido egresados durante el mes solicitado del rem
			$sql_existencias = "SELECT count(tabla.pertenece) FROM 
			(SELECT
			   h.id,
			   h.cama,
			   h.caso,
			   h.fecha,
			   h.fecha_ingreso_real,
			   h.fecha_liberacion as fecha_liberacion_cama,
			   c.fecha_termino as fecha_alta,
			   h.id_servicio,
			   CASE 
			   	WHEN( h.contador = 1 
						and h.fecha_ingreso_real < '".$mes_inicio."'::timestamp 
						and 
						(
			(h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp 
						   and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp) 
						   or h.fecha_liberacion::timestamp is null
						)
					 )
				  THEN
					 1 
				  WHEN
					 (
						h.contador > 1 
						and h.fecha < '".$mes_inicio."'::timestamp 
						and 
						(
			(h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp 
						   and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp) 
						   or h.fecha_liberacion::timestamp is null
						)
					 )
				  THEN
					 1 
				  ELSE
					 0 
			   END
			   as pertenece, h.contador 
			from
			   t_historial_ocupaciones_vista_aux h 
			   join
				  casos c on c.id=h.caso 
			where h.id_servicio= ".$servicio->id."
			order by
			   h.cama
			) as tabla
			WHERE  tabla.pertenece = 1 ";

			$existencia_mes_anterior = DB::select(DB::Raw($sql_existencias));
			$total_existencias +=($existencia_mes_anterior[0]->count == NULL)?0:$existencia_mes_anterior[0]->count;				

			$promedio_estada = DB::select(DB::Raw($prom." ".$sql_1." ".$sql_2));

			$dias_estada_total = DB::select(DB::Raw($total_dias." ".$sql_1." ".$sql_2));

			$dias_beneficiarios_servicio = DB::select(DB::Raw($total_dias." ".$sql_1." ".$whereBeneficiarios."".$sql_2));

			$total_dias_beneficiarios_servicio += ($dias_beneficiarios_servicio[0]->total == NULL)?0:$dias_beneficiarios_servicio[0]->total;
			$total_dias_estada_suma += ($dias_estada_total[0]->total == NULL)?0:$dias_estada_total[0]->total;
			
			$inicio_ = Carbon::createFromFormat('Y-m', $anno."-".$mes)
			->startOfMonth();
			$fin_ = Carbon::createFromFormat('Y-m', $anno."-".$mes)
			->endOfMonth();

			

			for ($inicio_; $inicio_ < $fin_; $inicio_->addDay()) { 

				//inicio dias camas bloqueadas
				$startOfDay = Carbon::parse($inicio_);
				$start = $startOfDay->startOfDay();
				
				$endOfDay = Carbon::parse($inicio_);
				$end = $endOfDay->endOfDay();
				
				$camas_bloqueadas = DB::select(DB::Raw("select count(*) as cama_bloqueada 
				from t_historial_bloqueo_camas as b 
				inner join camas as c on c.id = b.cama 
				inner join salas as s on s.id = c.sala 
				where s.establecimiento = ".$servicio->id." 
				and b.fecha <= '".$end."' 
				and 
				(b.fecha_habilitacion >= '".$start."'
				or				
				b.fecha_habilitacion is null
				)"));

				$total_bloqueada += $camas_bloqueadas[0]->cama_bloqueada;
				//fin de camas bloqueadas

				//inicio dias camas ocupadas

				$dia_transf = Carbon::parse($inicio_)->format("Y-m-d");
				$whereOcupadosBeneficiarios = "where prevision::varchar ilike '%FONASA%'";

				$sql_ocupados = "select count(*) as cant_camas_ocupadas from (
					select distinct caso, cama , prevision 
					from
					(select * from
							t_historial_ocupaciones_vista_aux h
							join casos c on c.id=h.caso
							where
							fecha_ingreso_real is not null 
					and '".$dia_transf."' <= fecha_liberacion::date and '".$dia_transf."' >= fecha_ingreso_real::date 
						and fecha_liberacion is not null
					and contador = 1
							and h.id_servicio=".$servicio->id."
							union
							select * from
							t_historial_ocupaciones_vista_aux h
							join casos c on c.id=h.caso
							where
							fecha_ingreso_real is not null 
					and '".$dia_transf."' <= fecha_liberacion::date and '".$dia_transf."' >= fecha::date 
						and fecha_liberacion is not null
					and contador > 1
							and h.id_servicio=".$servicio->id.") as dias_camas_ocupadas) as contador ";

				$dias_camas_ocupadas = DB::select(DB::Raw($sql_ocupados));
				//beneficiarios
				$dias_camas_ocupadas_beneficiarios = DB::select(DB::Raw($sql_ocupados." ".$whereOcupadosBeneficiarios));

					$contador_dias_camas_ocupadas += $dias_camas_ocupadas[0]->cant_camas_ocupadas;
					//inicio dias camas ocupadas
					$contador_dias_camas_ocupadas_beneficiarios += $dias_camas_ocupadas_beneficiarios[0]->cant_camas_ocupadas;
				
			}

			//CORRECCION CAMILO dias camas disponibles
			$disponible = (($dotacion_servicio* $dias_mes) - $total_bloqueada);
			$prom_disponible = $disponible/$dias_mes;
			
			$total_bloqueadas_servicios+=$total_bloqueada;
			$total_contador_dias_camas_ocupadas+=$contador_dias_camas_ocupadas;
			$total_contador_dias_camas_ocupadas_beneficiarios +=$contador_dias_camas_ocupadas_beneficiarios; 

			//inicio dias egresos (INDICE DE ROTACION)
			
			$total_egresos = DB::select(DB::Raw("select count(distinct h.caso) as total_egresos from
				t_historial_ocupaciones_vista_aux h
				join casos c on c.id=h.caso
				where
				fecha_liberacion is not null and
				fecha_liberacion >= '".$mes_inicio."' and
				fecha_liberacion <= '".$mes_final."'
				and h.id_servicio=".$servicio->id.";"));


			

			
			$total_egresos_suma += $total_egresos[0]->total_egresos;

			$indice_rotacion = ($prom_disponible == 0)?0:$total_egresos[0]->total_egresos/$prom_disponible;

			$intervalo_sustitucion =
			($total_egresos[0]->total_egresos == 0)?0:
				($disponible - $contador_dias_camas_ocupadas)/$total_egresos[0]->total_egresos;
			//fin dias egresos

			$porcentaje_ocupacional = 
			($disponible == 0)?0:
				($contador_dias_camas_ocupadas/$disponible)*100;
			//HASTA AQUI //DESCOMENTAR


			$info [] = [
				$servicio->alias,
				($existencia_mes_anterior[0]->count == NULL)?0:$existencia_mes_anterior[0]->count,
				//DESCOMENTAR
				$dotacion_servicio,
				round($prom_disponible,0),
				$disponible,
				$contador_dias_camas_ocupadas,
				$contador_dias_camas_ocupadas_beneficiarios,
				($dias_estada_total[0]->total == NULL)?0:$dias_estada_total[0]->total,
				($dias_beneficiarios_servicio[0]->total == NULL)?0:$dias_beneficiarios_servicio[0]->total,
				round($intervalo_sustitucion,1),
				round($porcentaje_ocupacional,1),
				($promedio_estada[0]->promedio == NULL)?0:round($promedio_estada[0]->promedio,1),
				round($indice_rotacion,1),
				$letalidad[0]->porcentaje_letalidad
				//HASTA AQUI //DESCOMENTAR
			];
			
		}
		$disponible_total = (($dotacion_total * $dias_mes) - $total_bloqueadas_servicios);
		$prom_disponible_total = $disponible_total/$dias_mes;

		$indice_rotacion_total = ($prom_disponible_total == 0)?0:$total_egresos_suma/$prom_disponible_total;

		$intervalo_sustitucion_total =
		($total_egresos_suma == 0)?0:
			($disponible_total - $total_contador_dias_camas_ocupadas)/$total_egresos_suma;

		$porcentaje_ocupacional_total = 
		($disponible_total == 0)?0:
			($total_contador_dias_camas_ocupadas/$disponible_total)*100;


		$sql_prom_total = "select avg(estada) as promedio
			from
			(select caso, sum(t_estada_null::numeric) as estada from
			(select
			h.id,
			h.cama,
			h.caso,
			h.fecha,
			h.fecha_ingreso_real,
			h.fecha_liberacion as fecha_liberacion_cama,
			c.fecha_termino as fecha_alta,
			CASE
				--caso cuando egresa el mismo mes que ingresa
				WHEN (h.contador=1 and h.fecha_liberacion is not null and (h.fecha_liberacion <= '".$mes_final."'::timestamp and h.fecha_liberacion >= '".$mes_inicio."'::timestamp)) 
					--estada = fecha de liberacion - fecha de hospitalizacion  
					THEN (EXTRACT(day FROM h.fecha_liberacion)-EXTRACT(day FROM h.fecha_ingreso_real))+1
				--caso cuando ingresa el mes actual pero aun no egresa
				WHEN (h.contador=1 and h.fecha_liberacion is null)  
					--estada = fin de mes - fecha de hospitalizacion  
					THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM h.fecha_ingreso_real))+1
				--caso cuando egresa el mismo mes que ingresa, pero ha sido cambiado de cama antes
				WHEN (h.contador>1 and h.fecha_liberacion is not null and (h.fecha_liberacion <= '".$mes_final."'::timestamp and h.fecha_liberacion >= '".$mes_inicio."'::timestamp))   
					THEN (EXTRACT(day FROM h.fecha_liberacion)-EXTRACT(day FROM h.fecha))+1
				--caso cuando ingresa el mes actual pero aun no egresa
				WHEN (h.contador>1 and h.fecha_liberacion is null)  
					THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM h.fecha))+1
			END as t_estada_null,
			h.contador
			from t_historial_ocupaciones_vista_aux h
			join casos c on c.id=h.caso
			where
			--ingresa dentro del mes
			(h.fecha_ingreso_real<= '".$mes_final."' and h.fecha_ingreso_real>= '".$mes_inicio."') and
			--solcita camas dentro del mes
			(h.fecha <= '".$mes_final."' and h.fecha >= '".$mes_inicio."') and
			--egresa antes de fin de mes o no egresa ese mes
			((h.fecha_liberacion <= '".$mes_final."' and h.fecha_liberacion >= '".$mes_inicio."') or h.fecha_liberacion is null) and
			h.id_establecimiento=".$establecimiento."
			union
			--caso cuando el paciente es hospitalizado en meses anteriores y egresa el mes de rem o se mantiene hospitalizado todo ese mes  
			select
			h.id,
			h.cama,
			h.caso,
			h.fecha,
			h.fecha_ingreso_real,
			h.fecha_liberacion as fecha_liberacion_cama,
			c.fecha_termino as fecha_alta,
			CASE
				WHEN (h.contador=1 and h.fecha_liberacion::timestamp is not null and (h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp))  THEN (EXTRACT(day FROM h.fecha_liberacion::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador=1 and h.fecha_liberacion::timestamp is null)  THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador>1 and h.fecha_liberacion::timestamp is not null and (h.fecha_liberacion::timestamp <= '".$mes_final."'::timestamp and h.fecha_liberacion::timestamp >= '".$mes_inicio."'::timestamp))  THEN (EXTRACT(day FROM h.fecha_liberacion::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
				WHEN (h.contador>1 and h.fecha_liberacion::timestamp is null) THEN (EXTRACT(day FROM '".$mes_final."'::timestamp)-EXTRACT(day FROM '".$mes_inicio."'::timestamp))+1
			END as t_estada_null,
			h.contador
			from t_historial_ocupaciones_vista_aux h
			join casos c on c.id=h.caso
			where
			--ingresa dentro de otro mes
			(h.fecha_ingreso_real < '".$mes_inicio."') and
			--egresa antes de fin de mes o no egresa ese mes
			((h.fecha_liberacion <= '".$mes_final."' and h.fecha_liberacion >= '".$mes_inicio."') or h.fecha_liberacion is null) and
			h.id_establecimiento=".$establecimiento."
			) tabla_suma
			group by caso
			) tabla_promedio";

			$prom_total_ejecutado = DB::select(DB::raw($sql_prom_total));

			$letalidad_total = DB::select(DB::raw("

				SELECT COALESCE( 
					( 
								( 
								SELECT COALESCE(Count(*)*100,0) AS total_fallecidos 
								FROM   t_historial_ocupaciones_vista_aux h 
								JOIN   casos c 
								ON     c.id=h.caso 
								WHERE  fecha_termino IS NOT NULL 
								AND    fecha_termino >= '".$mes_inicio."' 
								AND    fecha_termino <= '".$mes_final."' 
								AND    ( 
											motivo_termino='fallecimiento' 
									OR     detalle_termino='Fallecimiento') 
								AND    h.id IN 
									( 
												SELECT   Max(id) 
												FROM     t_historial_ocupaciones 
												GROUP BY caso) 
								AND    h.id_establecimiento=".$establecimiento." ) / 
							( 
									SELECT NULLIF( Count(DISTINCT h.caso) ,0) AS total_egresos 
									FROM   t_historial_ocupaciones_vista_aux h 
									JOIN   casos c 
									ON     c.id=h.caso 
									WHERE  fecha_liberacion IS NOT NULL 
									AND    fecha_liberacion >= '".$mes_inicio."' 
									AND    fecha_liberacion <= '".$mes_final."' 
									AND    h.id_establecimiento=".$establecimiento.") 
							) ,0
				) as porcentaje_letalidad"
			
			));
		
		$info [] = [
			"TOTALES HOSPITAL",
			$total_existencias,
			$dotacion_total,//listo
			round($prom_disponible_total,0),//listo
			$disponible_total,//listo
			$total_contador_dias_camas_ocupadas,//listo
			$total_contador_dias_camas_ocupadas_beneficiarios,
			$total_dias_estada_suma,//listo
			$total_dias_beneficiarios_servicio,
			round($intervalo_sustitucion_total,1),//listo
			round($porcentaje_ocupacional_total,1),//listo
			($prom_total_ejecutado[0]->promedio == NULL)?0:round($prom_total_ejecutado[0]->promedio,1),//listo
			round($indice_rotacion_total,1),//listo
			$letalidad_total[0]->porcentaje_letalidad
		];

		

		return response()->json(["aaData" => $info ]);
		
	}

	public function informacionREM($servicio, $informacion){

		
		$Urgencia = 0;
		$APS = 0;
		$CAE = 0;
		$otroH = 0;
		$otraP = 0;
		$existencias_v = 0;
		$sumaTraslados = 0;

		$Urgencia_VB = array(38,39,61);
		$APS_VB = array(41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,72,73,63);
		$CAE_VB = array(19,24,26);
		$otroH_VB = array(25,31,32,33,34,35,36,37,66,67,68,69,70,71);
		//Van Buren
		$mes_inicio = Carbon::createFromFormat('Y-m', $informacion['anno']."-".$informacion['mes'])
							->startOfMonth()->toDateTimeString();
		$mes_final = Carbon::createFromFormat('Y-m', $informacion['anno']."-".$informacion['mes'])
							->endOfMonth()->toDateTimeString();


		//return $mes_final;
		//Busqueda de existencias del mes anterior					
		$mes_anterior = $informacion['mes']-1;
		$exis_mes_inicio = Carbon::createFromFormat('Y-m', $informacion['anno']."-".$mes_anterior)
							->startOfMonth()->toDateTimeString();
		$exis_mes_final = Carbon::createFromFormat('Y-m', $informacion['anno']."-".$mes_anterior)
							->endOfMonth()->toDateTimeString();

		$existencias = DB::select(DB::raw("select min(t.id) as id_primer_historial, caso from
							( SELECT p.id,
								p.cama,
								p.caso,
								p.fecha_ingreso_real,
								p.fecha,
								p.created_at,
								p.updated_at,
								p.fecha_liberacion,
								p.motivo,
								pc.id AS paciente,
								p.id_establecimiento,
								p.nombre_establecimiento,
								p.id_servicio,
								p.nombre_servicio,
								row_number() OVER (PARTITION BY p.cama ORDER BY p.fecha DESC) AS rk,
								p.fecha_alta,
								pc.rut,
								(pc.nombre::text || ' '::text) || pc.apellido_paterno::text AS nombre_paciente,
								p.tipo
							FROM t_historial_ocupaciones_vista_aux p
								JOIN casos c ON p.caso = c.id
								JOIN pacientes pc ON c.paciente = pc.id) t
							WHERE fecha_ingreso_real >= '".$exis_mes_inicio."'
							AND fecha_ingreso_real <= '".$exis_mes_final."'
							AND id_servicio = ".$servicio->id."
							group by caso
							order by caso
								"));
		//fin busqueda existencias mes anterior

		//return $existencias;
		//BUSCAR servicio 
		$ocupaciones = DB::select(DB::raw("select min(t.id) as id_primer_historial, caso from
							( SELECT p.id,
								p.cama,
								p.caso,
								p.fecha_ingreso_real,
								p.fecha,
								p.created_at,
								p.updated_at,
								p.fecha_liberacion,
								p.motivo,
								pc.id AS paciente,
								p.id_establecimiento,
								p.nombre_establecimiento,
								p.id_servicio,
								p.nombre_servicio,
								row_number() OVER (PARTITION BY p.cama ORDER BY p.fecha DESC) AS rk,
								p.fecha_alta,
								pc.rut,
								(pc.nombre::text || ' '::text) || pc.apellido_paterno::text AS nombre_paciente,
								p.tipo
							FROM t_historial_ocupaciones_vista_aux p
								JOIN casos c ON p.caso = c.id
								JOIN pacientes pc ON c.paciente = pc.id) t
							WHERE fecha_ingreso_real >= '".$mes_inicio."'
							AND fecha_ingreso_real <= '".$mes_final."'
							AND id_servicio = ".$servicio->id."
							group by caso
							order by caso
								"));


		foreach ($ocupaciones as $key => $ocupacion) {


			$procedencia = Caso::select('procedencia')->where("id","=",$ocupacion->caso)->first()->procedencia;

			$traslados = THistorialOcupaciones::where("caso","=",$ocupacion->caso)->where("motivo","=","traslado interno")->count();

			$sumaTraslados += $traslados; 

				if (in_array($procedencia, $Urgencia_VB) ) {
					$Urgencia +=1;
				}elseif (in_array($procedencia, $APS_VB)) {
					$APS +=1;
				}elseif (in_array($procedencia, $CAE_VB)) {
					$CAE +=1;
				}elseif (in_array($procedencia, $otroH_VB)) {
					$otroH +=1;
				}else{
					$otraP +=1;
				}

				
						
		}

		$totalIngresos = $sumaTraslados + $Urgencia + $APS + $CAE + $otroH + $otraP;

		//existencias


		$camas = Cama::join("salas","salas.id","=","camas.sala")->get();






		return array(
			"Urgencia" => $Urgencia,
			"APS" => $APS,
			"CAE" => $CAE,
			"otroH" => $otroH,
			"otraP" => $otraP,
			"existencias" => $existencias_v,
			"traslados" => $sumaTraslados,
			"totalIngresos" => $totalIngresos,
			"nombreServicio" => $servicio->alias,
			"camas" => $camas
		);




		

	}

	public function PDFinformacionREM($anno,$mes){

		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


		$id_est = Auth::user()->establecimiento;
		$hoy = Carbon::now()->format('d/m/Y');
		$fecha = Carbon::createFromFormat('Y-m', $anno."-".$mes)
		->endOfMonth()->format('d/m/Y');

		
		$resultado = array();
		$resultado = $this->informacionREMCamas($anno, $mes);
		
		$datos = $resultado->original["aaData"];
		
		$establecimiento = DB::table('establecimientos')->where('id',$id_est )->first()->nombre;

				
		$html = PDF::loadView("NuevasEstadisticas/PdfRem20", [
			"informacion" => $datos,
			"establecimiento" => $establecimiento,
			"fecha" => $fecha,
			"hoy" => $hoy,
			"mes" => $meses[$mes-1],
			"anno" => $anno,
		]);
		
        
        try {

            return $html->setPaper('legal', 'landscape')->download('rem'.$meses[$mes-1].''.$anno.'.pdf');

        } catch (Exception $e) {
           	return response()->json($e->getMessage());
        }

	}

	public function descargarExcelRem($anno,$mes){

		setlocale(LC_ALL, 'es_ES');
		$nom_mes = Carbon::parse($anno."-".$mes);
		$nom_mes->format("F");
		$nom_mes = $nom_mes->formatLocalized('%B');// mes en idioma español


		$id_est = Auth::user()->establecimiento;
		$hoy = Carbon::now()->format('d/m/Y');
		$fecha = Carbon::createFromFormat('Y-m', $anno."-".$mes)
		->endOfMonth()->format('d/m/Y');
		
		
		$resultado = array();
		$resultado = $this->informacionREMCamas($anno, $mes);
		
		$datos = $resultado->original["aaData"];

		$establecimiento = DB::table('establecimientos')->where('id',$id_est )->first()->nombre;       
        
        try {

			$html = [
				"informacion" => $datos,
				"establecimiento" => $establecimiento,
				"fecha" => $fecha,
				"hoy" => $hoy,
				"mes" => $nom_mes,
				"anno" => $anno,
			];

			Excel::create('Rem20', function ($excel) use ($html){
				$excel->sheet('Rem20', function ($sheet) use ($html){
	
					$sheet->mergeCells('A1:N1');
					$sheet->setAutoSize(true);

					$sheet->setHeight(1, 50);
					$sheet->row(1, function ($row) {
	
						// call cell manipulation methods
						$row->setBackground('#1E9966');
						$row->setFontColor("#FFFFFF");
						$row->setAlignment("center");
	
					});
					#
	
					$sheet->loadView('NuevasEstadisticas.ExcelRem20', ["html" => $html]);
				});
			})->download('xls');

        } catch (Exception $e) {
           return response()->json($e->getMessage());
        }

	}

	public function reporteDotacionEnfermeria(){

		$id_establecimiento = Session::get("idEstablecimiento");


		$array[] = ["(191,192,193)","NEO"];//neo
		//$array[] = "(176,200,179,201,180,199,190,182,185,183,186,198,187,181,194,197,195,178,177)";//adulta
		$array[] = ["(176,200,180,179,199,201,184,187,190,181,183,182,186,185,198,194,197,195,177,178)","ADULTO"];//adulto
		$array[] = ["(188,189,196)","PED"];//pediatrica
		$tipo = ["BASICA","MEDIA", "CRITICA"]; 
		$valores ["ADULTO"] ["BASICA"] = [24,1,2,0];
		$valores ["ADULTO"] ["MEDIA"] = [24,2,3,0];
		$valores ["ADULTO"] ["CRITICA"] /* ["UPC"] */= [6,2,3,0];//con UPC
		//$valores ["ADULTO"] ["CRITICA "] ["NOUPC"]= [12,2,3,0];//sin UPC

		$valores ["PED"] ["BASICA"] = [24,2,3,0];
		$valores ["PED"] ["MEDIA"] = [24,2,3,0];
		$valores ["PED"] ["CRITICA"] /* ["UPC"] */ = [6,3,2,0];//con UPC
		//$valores ["PED"] ["CRITICA"] ["NOUPC"]= [12,2,3,0];//sin UPC

		$valores ["NEO"] ["BASICA"] = [24,0,2,3];
		$valores ["NEO"] ["MEDIA"] = [24,0,2,3];
		$valores ["NEO"] ["CRITICA"] /* ["UPC"] */ = [6,0,2,2];//con UPC
		//$valores ["NEO"] ["CRITICA"] ["NOUPC"]= [12,0,3,2];//sin UPC



		$total= [];
		$response_enfermeras = [];
		$response_tens = [];
		$response_enfermeras_cate = [];
		$response_tens_cate = [];

		$response_enfermeras2 ['NEO']['BASICA'] = 0;
		$response_enfermeras2 ['NEO']['MEDIA'] = 0;
		$response_enfermeras2 ['NEO']['CRITICA'] = 0;
		$response_enfermeras2 ['PED']['BASICA'] = 0;
		$response_enfermeras2 ['PED']['MEDIA'] = 0;
		$response_enfermeras2 ['PED']['CRITICA'] = 0;
		$response_enfermeras2 ['ADULTO']['BASICA'] = 0;
		$response_enfermeras2 ['ADULTO']['MEDIA'] = 0;
		$response_enfermeras2 ['ADULTO']['CRITICA'] = 0;
		$response_tens2 = [];
		foreach($array as $a){
			$total_pacientes [$a[1]] = DB::select("select caso, id_servicio, nombre_servicio
					FROM   historial_ocupaciones_vista as c				
					where
					id_establecimiento = ".$id_establecimiento."
					AND c.cama NOT IN (SELECT cama
					FROM   t_historial_bloqueo_camas
					WHERE  fecha_habilitacion IS NULL)
					AND c.fecha_liberacion is null
					AND c.rk = 1
					and c.id_servicio in ".$a[0]."
					");
			
			

			foreach($tipo as $t){
				$total [$a[1]] [$t] = DB::select("SELECT distinct /* id_servicio, alias,  */sum (count) as total_camas from(
					select t.id_servicio, t.nombre_servicio as alias, count(*) 
					from 
					  (select id_servicio, nombre_servicio
					  FROM   historial_ocupaciones_vista as c
               			INNER JOIN tipos_cama as t on t.id = c.tipo
					   where
					   id_establecimiento = :id_establecimiento
					   AND c.cama NOT IN (SELECT cama 
										 FROM   t_historial_bloqueo_camas 
										 WHERE  fecha_habilitacion IS NULL)
										 AND t.nombre = '$t'
										 AND c.fecha_liberacion is null 
										 AND c.rk = 1) t
						
					group by t.id_servicio, t.nombre_servicio
		
					union
					 select id, alias, 0 as count from unidades_en_establecimientos where establecimiento= :id_establecimiento
				  )tab
				  where id_servicio in ".$a[0]
				  /* group by id_servicio,alias */
				  , ['id_establecimiento'=>$id_establecimiento/* , 'id_servicio'=>$a */]);

			
				if($a[1] == "ADULTO" || $a[1] == "PED"){
					if($total [$a[1]] [$t][0]->total_camas == 0){
						$response_enfermeras [$a[1]] [$t] = 0;
						$response_tens [$a[1]] [$t] = 0;
					}else{
						$response_enfermeras [$a[1]] [$t] = ceil(( intval( $valores[$a[1]][$t][1]) * intval( $total [$a[1]] [$t][0]->total_camas)) 
					 / intval($valores[$a[1]][$t][0]) ) ;
						$response_tens [$a[1]] [$t] = ceil(( intval( $valores[$a[1]][$t][2]) * intval( $total [$a[1]] [$t][0]->total_camas)) 
						/ intval($valores[$a[1]][$t][0]) );
					}
					

					 
				}else{
					if($total [$a[1]] [$t][0]->total_camas == 0){
						$response_enfermeras [$a[1]] [$t] = 0;
						$response_tens [$a[1]] [$t] = 0;
					}else{
						$response_enfermeras [$a[1]] [$t] = ceil((intval($valores [$a[1]] [$t] [3]) * intval($total [$a[1]] [$t][0]->total_camas)) / intval($valores [$a[1]] [$t][0])) ;

						$response_tens [$a[1]] [$t] = ceil((intval($valores [$a[1]] [$t] [2]) * intval($total [$a[1]] [$t][0]->total_camas)) / intval($valores [$a[1]] [$t][0]));
					}
					
				}
				
				//$enf [$a[1]] [$t] =  
			}
			
		}

		foreach($total_pacientes as $key => $pacientes){
			foreach($pacientes as $pac){
				
				$cat = DB::select("select riesgo from ultimas_evoluciones_pacientes where caso = ".$pac->caso."");
				if($cat){
					if($cat[0]->riesgo == '' || $cat[0]->riesgo == 'D3' || $cat[0]->riesgo =='D2' || $cat[0]->riesgo =='D1' || $cat[0]->riesgo == 'C3' || $cat[0]->riesgo == null){
						
						$response_enfermeras2 [$key] ['BASICA'] +=1;
						
					}else if($cat[0]->riesgo == 'B3' || $cat[0]->riesgo == 'C1' || $cat[0]->riesgo == 'C2'){
						$response_enfermeras2 [$key] ['MEDIA']  += 1;
					}else{
						$response_enfermeras2 [$key] ['CRITICA'] += 1;
					}
				}else{
					$response_enfermeras2 [$key] ['BASICA'] +=1;
				}
				
			}
			if($key == "ADULTO" || $key == "PED"){

				if($response_enfermeras2 [$key] ['BASICA'] == 0){
					$response_enfermeras_cate [$key] ['BASICA'] = 0;
					$response_tens_cate [$key] ['BASICA'] = 0;
				}else{
					$response_enfermeras_cate [$key] ['BASICA'] = ceil(( intval( $valores[$key]['BASICA'][1]) * intval( $response_enfermeras2 [$key] ['BASICA'])) 
					 / intval($valores[$key]['BASICA'][0]) ) ;

					$response_tens_cate [$key] ['BASICA'] = ceil(( intval( $valores[$key]['BASICA'][2]) * intval( $response_enfermeras2 [$key] ['BASICA'])) 
					 / intval($valores[$key]['BASICA'][0]) ) ;
				}
				if($response_enfermeras2 [$key] ['MEDIA'] == 0){
					$response_enfermeras_cate [$key] ['MEDIA'] = 0;
					$response_tens_cate [$key] ['MEDIA'] = 0;
				}else{
					$response_enfermeras_cate [$key] ['MEDIA'] = ceil(( intval( $valores[$key] ['MEDIA'][1]) * intval( $response_enfermeras2 [$key] ['MEDIA'])) 
					/ intval($valores[$key] ['MEDIA'][0]) ) ;

					$response_tens_cate [$key] ['MEDIA'] = ceil(( intval( $valores[$key] ['MEDIA'][2]) * intval( $response_enfermeras2 [$key] ['MEDIA'])) 
				/ intval($valores[$key] ['MEDIA'][0]) ) ;
				}

				if($response_enfermeras2 [$key] ['CRITICA'] == 0){
					$response_enfermeras_cate [$key] ['CRITICA'] = 0;
					$response_tens_cate [$key] ['CRITICA'] = 0;
				}else{
					$response_enfermeras_cate [$key] ['CRITICA'] = ceil(( intval( $valores[$key] ['CRITICA'][1]) * intval( $response_enfermeras2 [$key] ['CRITICA'])) 
					/ intval($valores[$key] ['CRITICA'][0]) ) ;
					$response_tens_cate [$key] ['CRITICA'] = ceil(( intval( $valores[$key] ['CRITICA'][2]) * intval( $response_enfermeras2 [$key] ['CRITICA'] )) 
					/ intval($valores[$key] ['CRITICA'][0]) ) ;
				}				
			}else{
				//basica
				$response_enfermeras_cate [$key] ['BASICA'] = ceil((intval($valores [$key] ['BASICA'] [3]) * intval($response_enfermeras2 [$key] ['BASICA'] )) / intval($valores [$key] ['BASICA'][0])) ;

				//media
				$response_enfermeras_cate [$key] ['MEDIA'] = ceil((intval($valores [$key] ['MEDIA'] [3]) * intval($response_enfermeras2 [$key] ['MEDIA'] )) / intval($valores [$key] ['MEDIA'][0])) ;

				//critrixca
				$response_enfermeras_cate [$key] ['CRITICA'] = ceil((intval($valores [$key] ['CRITICA'] [3]) * intval($response_enfermeras2 [$key] ['CRITICA'] )) / intval($valores [$key] ['CRITICA'][0])) ;

				//basica
				$response_tens_cate [$key] ['BASICA'] = ceil((intval($valores [$key] ['BASICA'] [2]) * intval($response_enfermeras2 [$key] ['BASICA'])) / intval($valores [$key] ['BASICA'][0]));
				//media
				$response_tens_cate [$key] ['MEDIA'] = ceil((intval($valores [$key] ['MEDIA'] [2]) * intval($response_enfermeras2 [$key] ['MEDIA'])) / intval($valores [$key] ['MEDIA'][0]));
				//critica
				$response_tens_cate [$key] ['CRITICA'] = ceil((intval($valores [$key] ['CRITICA'] [2]) * intval($response_enfermeras2 [$key] ['CRITICA'])) / intval($valores [$key] ['CRITICA'][0]));
			}
						
		}

		
		return view('NuevasEstadisticas.ReporteDotacionEnfermeria', ["datos" => $total, "response_enfermeras" => $response_enfermeras, "response_tens" => $response_tens, "response_enfermeras2" => $response_enfermeras2, "response_tens_cate" => $response_tens_cate, "response_enfermeras_cate" => $response_enfermeras_cate]);
	}

}

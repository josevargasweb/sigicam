<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;
use Log;

class HistorialEspecialidades extends Model{

    protected $table = "t_evolucion_especialidades";
    protected $primaryKey = "id";
    public $timestamps = false;

    public static function consultaTotalEspecialidadesDia(){
		
		$dia = Carbon::now()->format("Y-m-d");
		//se considera la categorizaciones realizadas en el mes, a los cuales se les debe asociar las especialidades que tuvo ese dia
		return DB::select(DB::Raw("select c.id as idcaso, te.fecha as fecha_evolucion, t.fecha_ingreso_real, te.riesgo
			from casos as c
			inner join t_historial_ocupaciones as t on t.caso = c.id
			inner join t_evolucion_casos as te on te.caso = c.id
			where c.establecimiento = 8
			and c.fecha_termino is null
			and t.fecha_ingreso_real is not null
			and t.fecha_liberacion is null
			and te.riesgo is not null
			and (te.fecha <= '$dia 23:59:59' and te.fecha >= '$dia 00:00:00')"));

		
	}

	public static function consultaTotalEspecialidadesMes($fecha){

		$fechaInicio = $fecha;
		$finDeMes = $fechaInicio->copy()->endOfMonth()->toDateString();
		$inicioDeMes = $fechaInicio->copy()->startOfMonth()->toDateString();
	  
		//se considera la categorizaciones realizadas en el mes, a los cuales se les debe asociar las especialidades que tuvo ese dia
		return DB::select(DB::Raw("select c.id as idcaso, te.fecha as fecha_evolucion, t.fecha_ingreso_real, te.riesgo
			from casos as c
			inner join t_historial_ocupaciones as t on t.caso = c.id
			inner join t_evolucion_casos as te on te.caso = c.id
			where c.establecimiento = 8
			and c.fecha_termino is null
			and t.fecha_ingreso_real is not null
			and t.fecha_liberacion is null
			and te.riesgo is not null
			and (te.fecha <= '$finDeMes 23:59:59' and te.fecha >= '$inicioDeMes 00:00:00')"));
		
    }

    public static function consultaTotalEspecialidades($fecha, $tipo){

		//se considera la categorizaciones realizadas en el mes, a los cuales se les debe asociar las especialidades que tuvo ese dia
		if($tipo == "M"){
			$categorizaciones_mes = HistorialEspecialidades::consultaTotalEspecialidadesMes($fecha);
		}else{
			$categorizaciones_mes = HistorialEspecialidades::consultaTotalEspecialidadesDia();
		}		

		//conteo de especialidaddes por id
		$especialidades_conteo [1] = 0;//Medicina 
		$especialidades_conteo [2] = 0;//Cirugia 
		$especialidades_conteo [3] = 0;//Traumatologia 
		$especialidades_conteo [4] = 0;//Neurologia 
		$especialidades_conteo [5] = 0;//Urologia 
		$especialidades_conteo [6] = 0;//Neurocirugia 
		$especialidades_conteo [7] = 0;//otra 
		
		//conteo de dias por id especialidad
		$dias_conteo[1] = 0;//Medicina
		$dias_conteo[2] = 0;//Cirugia
		$dias_conteo[3] = 0;//Traumatologia
		$dias_conteo[4] = 0;//Neurologia
		$dias_conteo[5] = 0;//Urologia
		$dias_conteo[6] = 0;//Neurocirugia
		$dias_conteo[7] = 0;//otra

		//categoria de 1 a 3 hrs
		$info[1][1]= 0;//Medicina
		$info[1][2]= 0;//Cirugia
		$info[1][3]= 0;//Traumatologia
		$info[1][4]= 0;//Neurologia
		$info[1][5]= 0;//Urologia
		$info[1][6]= 0;//Neurocirugia
		$info[1][7]= 0;//otra
		
		//categoria de 4 a 12 hrs
		$info[2][1]= 0;//Medicina
		$info[2][2]= 0;//Cirugia
		$info[2][3]= 0;//Traumatologia
		$info[2][4]= 0;//Neurologia
		$info[2][5]= 0;//Urologia
		$info[2][6]= 0;//Neurocirugia
		$info[2][7]= 0;//otra

		//categoria mayor a 12
		$info[3][1]= 0;//Medicina
		$info[3][2]= 0;//Cirugia
		$info[3][3]= 0;//Traumatologia
		$info[3][4]= 0;//Neurologia
		$info[3][5]= 0;//Urologia
		$info[3][6]= 0;//Neurocirugia
		$info[3][7]= 0;//otra

		//Se buscara fecha por fecha la especialidad que se le hizo a ese caso
		foreach($categorizaciones_mes as $categorizacion){
			
			$fecha_especialidad = Carbon::parse($categorizacion->fecha_evolucion)->toDateString();
			$dias_hosp = Carbon::parse($categorizacion->fecha_ingreso_real)->diffInDays(Carbon::parse($categorizacion->fecha_evolucion));

			$especialidades = DB::select(DB::Raw("select e.nombre, e.id as id_especialidad 
				from t_evolucion_especialidades as te
				inner join especialidades as e on e.id = te.id_especialidad
				where te.id_caso = $categorizacion->idcaso
				and 
				( 
					(
						te.fecha <= '$fecha_especialidad 23:59:59' 
						and te.fecha_termino > '$fecha_especialidad 23:59:59' and te.comentario is null
					)
				or
					(te.fecha <= '$fecha_especialidad 23:59:59' and te.fecha_termino is null)
				)"));

			foreach($especialidades as $especialidad){
				$especialidades_conteo [$especialidad->id_especialidad] +=1;
				$dias_conteo [$especialidad->id_especialidad] += $dias_hosp;

				if($dias_hosp < 3){
					$info[1][$especialidad->id_especialidad] += 1;
				}else if($dias_hosp >= 4 && $dias_hosp <=12){
					$info[2][$especialidad->id_especialidad] += 1;
				}else{
					$info[3][$especialidad->id_especialidad] += 1;
				}
			}

		}
		ksort($especialidades_conteo);
		ksort($dias_conteo);
		ksort($info[1]);
		ksort($info[2]);
		ksort($info[3]);

		return [
			"total_dias" => $dias_conteo, //este indica el total de dias en el mes por especialidad
			"info_categorias" => $info,//esto va como info[1] Menor a 3 hrs, info[2] de 4 a 12 hrs y info[3] mayores a 12
			"total_especialidades" => $especialidades_conteo// total de especialidades contadas
		];

    }

    
}

?>
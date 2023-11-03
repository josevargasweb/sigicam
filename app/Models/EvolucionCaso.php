<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComplejidadAreaFuncional;
use App\Models\UnidadEnEstablecimiento;
use DB;
use Auth;
use Log;
use Form;
use Carbon\Carbon;
use App\Models\Especialidades;
use App\Models\EvolucionEspecialidad;
use App\Models\EvolucionAtencion;
use App\Models\EvolucionAcompanamiento;

class EvolucionCaso extends Model{

	protected $primaryKey = "id";
	protected $table = "t_evolucion_casos";
	protected $fillable = ["riesgo", "fecha", "riesgo_id"];

	public function riesgo() {
	  return $this->hasOne('App\Models\Riesgo');
	}

	public function complejidad_area_funcional (){
		return $this->belongsTo(ComplejidadAreaFuncional::class, 'id_complejidad_area_funcional');
	}

	public function unidad (){
		return $this->belongsTo(UnidadEnEstablecimiento::class, 'id_unidad');
	}
	
	public static function evoluciones(){
		return self::join("t_historial_ocupaciones AS h", "h.caso", "=", "t_evolucion_casos.caso")
			->join("camas AS cm", "cm.id", "=", "h.cama")
			->join("salas AS s", "s.id", "=", "cm.sala")
			->join("unidades_en_establecimientos AS ue", "ue.id", "=", "s.establecimiento")
			->select(
				"t_evolucion_casos.caso",
				"t_evolucion_casos.riesgo",
				DB::raw("t_evolucion_casos.fecha::date"),
				DB::raw("row_number() over (partition by t_evolucion_casos.caso, riesgo order by t_evolucion_casos.fecha desc) AS rk"),
				"ue.id as unidad_en_establecimiento",
				"s.id as sala",
				"cm.id as cama"
		);
	}

	public static function pacientesSinCategorizar($unidad){
		$resultado = [];
		$espe = [];
		$inicio_dia = Carbon::now()->startOfDay();		
		$lista_especialidades = Especialidades::pluck('nombre','id');
		$lista_atenciones = array('Estable' => 'Estable', 'Regular' => 'Regular', 'Grave' =>'Grave', 'Muy grave' =>'Muy grave');
		$lista_acompanamiento = array('Diurno' => 'Diurno', 'Nocturno' => 'Nocturno', 'Ambos' =>'Ambos');
		
		$cantidad_pediatricos = '';
		//cuando los pacientes son de hsopitalizacion domiciliaria
		if($unidad == "hospDom"){

			$casos = HospitalizacionDomiciliaria::whereNull("fecha_termino")->get();

			foreach($casos as $key => $caso){

				$paciente = Paciente::join("casos as c", "c.paciente", "pacientes.id")->where("c.id", $caso->caso)->first();

				$fecha_nacimiento = "";
				if ($paciente->fecha_nacimiento) {
					$fecha_nacimiento = date("d-m-Y", strtotime($paciente->fecha_nacimiento));
				}

				$ultima_categ = EvolucionCaso::select("categoria", "fecha")
						->join("riesgos", "riesgos.id", "=", "t_evolucion_casos.riesgo_id")
						->where("caso", "=", $caso->caso)
						->where(function($query) {
							$query->where("urgencia", "false")
							->orWhereNull("urgencia");
						})
						->whereNotNUll("riesgo_id")
						->orderBy("fecha", "desc")
						->first();
						

				//calcular dia de categorizacion
				$fecha_ult_cate ="";

				//Ultimo dia que se realizo categorizacion
				if ($ultima_categ) {
					//Si el paciente fue categorizado se toma esa fecha
					$fecha1 = new \DateTime($ultima_categ->fecha);
					$fecha_ult_cate = Carbon::parse("$ultima_categ->fecha");
					$ultima_categ = $ultima_categ->categoria;
				} else {
					//si el paciente nunca fue categorizado, se utiliza la fecha de hospitalizacion para mostrar esta
					$fecha1 = new \DateTime($caso->fecha);
					$ultima_categ = "";
				}

				if($fecha_ult_cate != "" && ($fecha_ult_cate >= $inicio_dia)){
					continue;
				}

				$fecha2 = new \DateTime();
				$diff = $fecha1->diff($fecha2);
				$hours = $diff->h;
				$hours = $hours + ($diff->days * 24);
				$diferencia = $hours;

				$especialidades = EvolucionEspecialidad::where(function($query) use ($caso) {
						$query->where('id_caso',$caso->caso)
						->whereNull('fecha_termino');
					})
					->orWhere(function($query) use ($caso, $inicio_dia) {
						$query->where('id_caso',$caso->caso)
						->where('fecha_termino','>=',$inicio_dia);
					})->get();
				
				$espe = [];
				$span = "";
				if(count($especialidades) >= 1){
					if($especialidades != "[]"){
						foreach ($especialidades as $key => $value) {
							$espe[] = $value->id_especialidad;
							if($value->id_especialidad != 7 && (Carbon::parse($value->fecha) >= $inicio_dia  || ( $value->fecha_termino != null && (Carbon::parse($value->fecha_termino) >= $inicio_dia))) ){
								//esto es en caso de encontrar que actualizaron la especialidad	
								$span = "<h4><span class='label label-success'>ACTUALIZADO</span></h4><input class='hide' name='especialidad[]' value='9' id= 'especialidad-$caso->caso'><input class='hide' espeid-".$caso->caso." name='especialidad_id[]' value='9'>";
								break;
							}
						}
					}
				}else{
					$espe[]= 7;
				}

				
				$select = '<div class="col-sm-10 form-group" style="margin-top: 4%;">';
				if($span == ""){
					$select .= Form::select('especialidad[]', $lista_especialidades, $espe, array('id' => 'especialidad-'.$caso->caso, 'class' => 'slctpikr form-control', 'multiple','data-max-options'=>'3','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;, &quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;]"))
				."<p  style='color: red;' id='message_".$caso->caso."' value=''></p>"
				."<input id='espeid-".$caso->caso."' class='form-control hidden' maxlength='2' name='especialidad_id[]' value='' type='text'>";
				}else{
					$select .= $span;
				}
				$select .= '</div>';
				
				$resultado[] = [
					"rut" => $paciente->rut,
					"dv" => $paciente->dv,
					"nombre" => $paciente->nombre . " " . $paciente->apellido_paterno,
					"fecha_nacimiento" => $fecha_nacimiento,
					"unidad_funcional" => "Hospitalización Domiciliaria",
					"area_funcional" => "Hospitalización Domiciliaria",
					"categorizacion_anterior" => $ultima_categ,
					"id_caso" => $caso->caso,
					"paciente_id" => $paciente->id,
					"id_unidad_en_establecimiento" => "--",
					"sala" => "--",
					"cama" => "--",
					"horas" => $diferencia." hrs",
					"sus_especialidades" => $espe,
					"especialidades" =>  $lista_especialidades,
					"select_especialidades" =>	$select,
					"key" => $key
				];
			}
			return response()->json($resultado);
		}

		$cantidad_pediatricos = 0;
		//cuando son pacientes del hospital
		$casos = DB::select(DB::raw("select t.id, t.caso
		from historial_ocupaciones_vista t
		inner join t_historial_ocupaciones ho on ho.id = t.id
		where t.id_servicio = ".$unidad."
		and ho.fecha_ingreso_real is not null
		and t.rk = 1
		and t.fecha_liberacion is null
		"));
		
		
		foreach ($casos as $key => $caso) {
			//Se modifico para que los casos que se fueron categorizados por usuario urgencia tambien aparezcan aqui debido a que estas categorizaciones del usuario urgencia 
			//no son contabilizadas como categorizaciones correctas
			$ultima_evolucion = DB::select("select * from ultimas_evoluciones_pacientes as u
			join t_evolucion_casos as t on t.id = u.id 
			where u.caso = ".$caso->caso." and (
				(u.riesgo is not null and t.urgencia = true) 
				or (u.riesgo is null)
			)
			order by t.id desc
			limit 1");

			if(!empty($ultima_evolucion)){
				

				$info = DB::select("select 
				u.alias, 
				af.nombre AS area_funcional, 
				ca.id_cama AS nombre_cama, 
				sa.nombre AS nombre_sala, 
				t.fecha_ingreso_real, 
				u.id AS id_unidad_en_establecimiento
				from t_historial_ocupaciones t
				inner join casos c on c.id = t.caso
				inner join camas ca on ca.id = t.cama
				inner join salas sa on sa.id = ca.sala
				inner join unidades_en_establecimientos as u on u.id = sa.establecimiento
				inner join area_funcional af on af.id_area_funcional = u.id_area_funcional
				where t.id = ".$caso->id);
	
				

				$restriccion_tiempo = Consultas::restriccionCategorizacionCama($caso->caso)->getData()->restriccion;

				if (!$restriccion_tiempo) {
					$ultima_categ = EvolucionCaso::select("categoria", "fecha")
						->join("riesgos", "riesgos.id", "=", "t_evolucion_casos.riesgo_id")
						->where("caso", "=", $caso->caso)
						->where(function($query) {
							$query->where("urgencia", "false")
							->orWhereNull("urgencia");
						})
						->whereNotNUll("riesgo_id")
						->orderBy("fecha", "desc")
						->first();
	
					if ($ultima_categ) {
						$fecha1 = new \DateTime($ultima_categ->fecha);
						$ultima_categ = $ultima_categ->categoria;
					} else {
						$fecha1 = new \DateTime($info[0]->fecha_ingreso_real);
						$ultima_categ = "";
					}
	
					$fecha2 = new \DateTime();
					$diff = $fecha1->diff($fecha2);
					$hours = $diff->h;
					$hours = $hours + ($diff->days * 24);
					$diferencia = $hours;
	
					$paciente = DB::table("casos as c")
								->join("pacientes as p", "p.id","c.paciente")
								->where("c.id",$caso->caso)
								->first();
								
					$fecha_nacimiento = "";
					if ($paciente->fecha_nacimiento) {
						$fecha_nacimiento = date("d-m-Y", strtotime($paciente->fecha_nacimiento));
					}

					$edad = '';
					if($paciente->fecha_nacimiento != '' || $paciente->fecha_nacimiento != null){
						$edad=Paciente::edad($paciente->fecha_nacimiento);
					}

					if($paciente->fecha_nacimiento == '' || $paciente->fecha_nacimiento == null || $edad <= 15 || $edad == ''){
						$cantidad_pediatricos ++;
					}

	
					$unidad = "";
					$unidad .= ($info[0]->alias)?"<b>Área:</b>".$info[0]->alias."":"";
					$unidad .= ($info[0]->area_funcional)?" <br><b>Servicio:</b> ".$info[0]->area_funcional."</b>":"";
					$unidad .= ($info[0]->nombre_sala)?"<br><b>Sala:</b> ".$info[0]->nombre_sala:"";
					$unidad .= ($info[0]->nombre_cama)?"<br><b>Cama:</b> ".$info[0]->nombre_cama:"";

					
					$especialidades = EvolucionEspecialidad::where(function($query) use ($caso) {
							$query->where('id_caso',$caso->caso)
							->whereNull('fecha_termino');
						})
						->orWhere(function($query) use ($caso, $inicio_dia) {
							$query->where('id_caso',$caso->caso)
							->where('fecha_termino','>=',$inicio_dia);
						})->get();
					$espe = [];
					$span = "";
					if(count($especialidades) >= 1){
						if($especialidades != "[]"){
							foreach ($especialidades as $key => $value) {
								$espe[] = $value->id_especialidad;
								
								if($value->id_especialidad != 7 && (Carbon::parse($value->fecha) >= $inicio_dia  || ( $value->fecha_termino != null && (Carbon::parse($value->fecha_termino) >= $inicio_dia))) ){
									//esto es en caso de encontrar que actualizaron la especialidad	
									$span = "<h4><span class='label label-success'>ACTUALIZADO</span></h4><input class='hide' name='especialidad[]' value='9' id= 'especialidad-$caso->caso'><input class='hide' espeid-".$caso->caso." name='especialidad_id[]' value='9'>";
									break;
								}
							}
						}
					}else{
						$espe[]= 7;
					}
					

					$select = '<div class="col-sm-10 form-group" style="margin-top: 4%;">';
					if($span == ""){
						$select .= Form::select('especialidad[]', $lista_especialidades, $espe, array('id' => 'especialidad-'.$caso->caso, 'class' => 'slctpikr form-control', 'multiple','data-max-options'=>'3','data-max-options-text' => "[&quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;, &quot;<label style='color:#000'>Máximo 3 especialidades permitidas</label>&quot;]"))
					."<p  style='color: red;' id='message_".$caso->caso."' value=''></p>"
					."<input id='espeid-".$caso->caso."' class='form-control hidden' maxlength='2' name='especialidad_id[]' value='' type='text'>";
					}else{
						$select .= $span;
					}
					$select .= '</div>';


					$atenciones = EvolucionAtencion::where(function($query) use ($caso) {
						$query->where('id_caso',$caso->caso)
						->whereNull('fecha_termino');
					})
					->orWhere(function($query) use ($caso, $inicio_dia) {
						$query->where('id_caso',$caso->caso)
						->where('fecha_termino','>=',$inicio_dia);
					})->get();
				$atencion = [];
				$span_atencion = "";

				if(count($atenciones) >= 1){
					if($atenciones != "[]"){
						foreach ($atenciones as $key => $value) {
							$atencion[] = $value->tipo_atencion;
							
							if($value->tipo_atencion != '' && (Carbon::parse($value->fecha) >= $inicio_dia  || ( $value->fecha_termino != null && (Carbon::parse($value->fecha_termino) >= $inicio_dia))) ){
								//esto es en caso de encontrar que actualizaron la atencion	
								$span_atencion = "<h4><span class='label label-success'>ACTUALIZADO</span></h4><input class='hide' name='categoria_atencion[]' id= 'atencion-$caso->caso'><input class='hide' atencionid-".$caso->caso." name='atencion_id'>";
								break;
							}
						}
					}
				}else{
					$atencion[]= '';
				}

				$select_atencion = '<div class="col-sm-12 form-group" style="margin-top: 4%;">';
				if($span_atencion == ""){
					$select_atencion .= Form::select('categoria_atencion[]', $lista_atenciones, $atencion, array('id' => 'categoria_atencion-'.$caso->caso, 'class' => 'form-control','placeholder' => 'seleccione'))
				."<p  style='color: red;' id='message_".$caso->caso."' value=''></p>"
				."<input id='categoria_atencionid-".$caso->caso."' class='form-control hidden' name='categoria_atencion_id' value='' type='text'>";
				}else{
					$select_atencion .= $span_atencion;
				}
				$select_atencion .= '</div>';
	

				$acompanamiento = [];
				$select_acompanamiento = '<div class="col-sm-12 form-group" style="margin-top: 4%;">';
				if($paciente->fecha_nacimiento == '' || $paciente->fecha_nacimiento == null || $edad <= 15 || $edad == ''){		
					$acomapamientos = EvolucionAcompanamiento::where(function($query) use ($caso) {
						$query->where('id_caso',$caso->caso)
						->whereNull('fecha_termino');
					})
					->orWhere(function($query) use ($caso, $inicio_dia) {
						$query->where('id_caso',$caso->caso)
						->where('fecha_termino','>=',$inicio_dia);
					})->get();
					$span_acompanamiento = "";
			
				if(count($acomapamientos) >= 1){
					if($acomapamientos != "[]"){
						foreach ($acomapamientos as $key => $value) {
							$acompanamiento[] = $value->tipo_acompanamiento;
							if($value->tipo_acompanamiento != '' && (Carbon::parse($value->fecha) >= $inicio_dia  || ( $value->fecha_termino != null && (Carbon::parse($value->fecha_termino) >= $inicio_dia))) ){
								//esto es en caso de encontrar que actualizaron la atencion	
								$span_acompanamiento = "<h4><span class='label label-success'>ACTUALIZADO</span></h4><input class='hide' name='categoria_acompanamiento[]' id= 'acompanamiento-$caso->caso'><input class='hide' acompanamientoid-".$caso->caso." name='acompanamiento_id'>";
							}
						}
					}
				}else{
					$acompanamiento[]= '';
				}

				
					if($span_acompanamiento == ""){
						$select_acompanamiento .= Form::select('categoria_acompanamiento[]', $lista_acompanamiento, $acompanamiento, array('id' => 'categoria_acompanamiento-'.$caso->caso, 'class' => 'form-control','placeholder' => 'seleccione'))
					."<p  style='color: red;' id='message_".$caso->caso."' value=''></p>"
					."<input id='categoria_acompanamientoid-".$caso->caso."' class='form-control hidden' name='categoria_acompanamiento_id' value='' type='text'>";
					}else{
						$select_acompanamiento .= $span_acompanamiento;
					}
				}
				$select_acompanamiento .= '</div>';
	

					$resultado['datos'][] = [
						"rut" => $paciente->rut,
						"dv" => $paciente->dv,
						"nombre" => $paciente->nombre . " " . $paciente->apellido_paterno,
						"fecha_nacimiento" => $fecha_nacimiento,
						"unidad_funcional" => $unidad,
						"area_funcional" => $unidad,
						"categorizacion_anterior" => $ultima_categ,
						"id_caso" => $caso->caso,
						"paciente_id" => $paciente->id,
						"id_unidad_en_establecimiento" => $info[0]->id_unidad_en_establecimiento,
						// "sala" => $info[0]->nombre_sala,
						// "cama" => $info[0]->nombre_cama,
						"horas" => $diferencia." hrs",
						"sus_especialidades" => $espe,
						"especialidades" =>  $lista_especialidades,
						"select_especialidades" =>	$select,
						"sus_atenciones" => $atencion,
						"atenciones" =>  $lista_atenciones,
						"select_atenciones" =>	$select_atencion,
						"sus_acomapamiento" => $acompanamiento,
						"acomapamiento" =>  $lista_acompanamiento,
						"select_acomapamientos" =>	$select_acompanamiento,
						"edad"=> $edad,
						"key" => $key
					];
				}
			}
			
			
		}
		$resultado['cantidad_pediatricos'] = $cantidad_pediatricos;
		return response()->json($resultado);
	}

	public static function consultaPacienteNoCategorizado ($rut){
		return  DB::select(DB::Raw("select max(cate.id) as id, cate.date_trunc
				from (
				select t.id, date_trunc('day', t.fecha), t.riesgo, t.motivo, t.comentario,t.riesgo_id
						from pacientes as p
						join casos as c on c.paciente = p.id
						join t_evolucion_casos as t on t.caso = c.id
						where rut = $rut and (t.riesgo is null and t.riesgo_id is null)
						group by t.id
						order by t.fecha desc
				) as cate
				
				
				where cate.date_trunc not in (
				
				select date_trunc('day', t.fecha)
						from pacientes as p
						join casos as c on c.paciente = p.id
						join t_evolucion_casos as t on t.caso = c.id
						where rut = $rut and (t.riesgo is not null or t.riesgo_id is not null)
						group by t.id
						order by t.fecha desc
				)		

				group by cate.date_trunc 
				order by cate.date_trunc desc
		"));
	}

}

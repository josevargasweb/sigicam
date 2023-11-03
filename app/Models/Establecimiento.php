<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Log;

class Establecimiento extends Model{
	protected $table = "establecimientos";
	public function unidades(){
		return $this->hasMany('App\Models\UnidadEnEstablecimiento', 'establecimiento', 'id');
	}

    public function unidadesVisibles(){
        return $this->unidades()->where("visible", true);
    }

	public function usuarios(){
		return $this->hasMany("App\Models\Usuario", "establecimiento", "id");
	}

	public function permisos(){
		return $this->has("App\Models\PermisosEstablecimiento", "establecimiento", "id");
	}

	public function salas(){
		return $this->hasManyThrough("App\Models\Sala", "App\Models\UnidadEnEstablecimiento", "establecimiento", "establecimiento");
	}

	public function contingencias(){
		return $this->hasManyThrough("App\Models\SolicitudContingencia", "App\Models\Usuario", "establecimiento", "usuario");
	}

	public static function getAll($id){
		$response=array();
		$establecimientos=self::where("id", "!=", $id)->get();
		foreach($establecimientos as $establecimiento){
			$response[$establecimiento->id]=$establecimiento->nombre;
		}
		return $response;
	}

	public static function getDiferentes($id, $idEstablecimiento){
		$response=array();
		$establecimientos=self::where("id", "!=", $id)->get();
		foreach($establecimientos as $establecimiento){
			if(!in_array($establecimiento->id, $idEstablecimiento)){
				$response[$establecimiento->id]=$establecimiento->nombre;
			} 
		}
		return $response;
	}

	public static function getNombre($id){
		$data=self::find($id);
		if($data != null){
			return $data->nombre;
		} 
		return "";
	}

	public static function getEstablecimientos($todos=false, $excepto = null){
		$response=($todos) ? array(0 => "Todos") : array();
		if (!$excepto){
			$establecimientos=self::orderBy("nombre")->get();
		}else{
			$establecimientos = self::whereNotIn('id', $excepto)->orderBy("nombre")->get();
		}
		foreach($establecimientos as $establecimiento){
			$response[$establecimiento->id]=$establecimiento->nombre;	
		} 
		return $response;
	}

	public static function getEstablecimientosSinTodos(){
		
		$region = Establecimiento::where("id",Auth::user()->establecimiento)->first();
		if($region){
			$establecimientos=self::where("id_region", $region->id_region)->orderBy("nombre", "asc")->get();
		}else{
			$establecimientos=self::all();
		}
		foreach($establecimientos as $establecimiento){
			$response[$establecimiento->id]=$establecimiento->nombre;
		} 
		return $response;
	}

	public static function getIDEstablecimiento(){
		$establecimientos=self::all();
		foreach($establecimientos as $establecimiento){
			$response[]=array("id" => $establecimiento->id, "nombre" => $establecimiento->nombre);
		} 
		return $response;
	}

	public static function getUnidadPorEstablecimiento($id){
		
		return DB::table("unidades_en_establecimientos as ue")
		->select("ue.id", "ue.alias","ue.tipo_unidad","ue.id_area_funcional")
		->where("ue.establecimiento", "=", $id)
		->where("ue.visible", "=", TRUE)
		->orderBy("ue.alias")
		->get();
	}

	public static function getUnidadUrlPorEstablecimiento($id){
		$response=array();
		$unidades=DB::table("unidades_en_establecimientos as ue")->select("ue.url")->where("ue.establecimiento", "=", $id)
		->orderBy("ue.alias")->get();
		foreach($unidades as $unidad) {
			$response[]=$unidad->url;
		}
		return $response;
	}

	public static function getCuposPorTipoDeCama($id_tipo){
		$q1 = DB::table("camas_habilitadas AS cm")
		->leftJoin("ultimas_ocupaciones as uo", "uo.cama", "=", "cm.id")
		->join("salas as s", "s.id", "=", "cm.sala")
		->join("unidades_en_establecimientos as ue", "ue.id", "=", "s.establecimiento")
		->join("historial_camas_en_unidades as hc", function($j){
			$j->on("hc.unidad", "=", "ue.id")->on("hc.cama", "=", "cm.id");
		})
		->join("tipos_cama AS tc", function($j){
			$j->on("tc.id", "=", "cm.tipo");
		})
		->join("establecimientos AS est", "est.id", "=", "ue.establecimiento");

		return $q1->select(
			"est.id",
			"est.nombre",
			"ue.alias",
			DB::raw("count(cm.id) as cantidad"),
			"tc.id as tipo_cama_id",
			"tc.nombre as tipo_cama"
		)
		->where("hc.rk", "=", 1)->where("tc.id", "=", $id_tipo)->where("uo.id", "=", null)
		->groupBy("est.id")->groupBy("est.nombre")->groupBy("ue.alias")->groupBy("tc.id")->groupBy("tc.nombre")
		->get();

	}

	public function getUnidades(){
		$unidades = UnidadEnEstablecimiento::where("establecimiento", $this->id)
		->get();
		$arr = array();
		$unidades->each(function($i) use (&$arr){
			$arr[$i->id] = $i->alias;
		});
		return $arr;
	}

	public static function _cuposParaExtrasistema(){
		return DB::table("derivaciones_extrasistema AS dx")
		->join("establecimientos_extrasistema as ex", "ex.id", "=", "dx.establecimiento_extrasistema")
		->leftJoin("servicios_recibidos AS sr", "sr.unidad", "=", "dx.servicio")
		->leftJoin("unidades as servicio", "servicio.id", "=", "sr.unidad")
		->leftJoin("unidades_en_establecimientos AS ue", "ue.id", "=", "sr.unidad_en_establecimiento")
		->join("usuarios as us", function($j){
			$j->on("us.id", "=", "dx.usuario");
		})
		->join("establecimientos as est", "est.id", "=", "us.establecimiento")
		->join("salas as s", "s.establecimiento", "=", "ue.id")
		->join("camas_habilitadas AS cm", "cm.sala", "=", "s.id")
		->leftJoin("historial_ocupaciones AS h", function($j){
			$j->on("h.cama", "=", "cm.id")
			->on("h.rk", "=", DB::raw(1));
		})
		->join("casos AS cs", "cs.id", "=", "dx.caso")
		->join("pacientes AS pc", "pc.id", "=", "cs.paciente")
		->where(function($q){
			$q->whereNotNull("h.fecha_liberacion")
			->orWhere("h.id", "=", null);
		})
		->where("dx.fecha_rescate", "=", null)
		;
	}

	public static function cuposTotalesParaExtrasistema(){
		return self::_cuposParaExtrasistema()
		->select(
			"dx.caso",
			"dx.fecha",
			"est.id AS id_est",
			"est.nombre AS nombre_est",
			"pc.nombre AS paciente",
			"pc.apellido_paterno",
			"pc.apellido_materno",
			"pc.rut",
			"pc.dv",
			"servicio.nombre AS nombre_unidad",
			DB::raw("count(cm.id) as cantidad"),
			"ex.id AS id_est_ex",
			"ex.nombre AS est_ex",
			"dx.id"
		)
		->groupBy("dx.caso")
		->groupBy("dx.fecha")
		->groupBy("dx.id")
		->groupBy("ex.id")
		->groupBy("ex.nombre")
		->groupBy("servicio.nombre")
		->groupBy("pc.nombre")
		->groupBy("pc.rut")
		->groupBy("pc.dv")
		->groupBy("pc.nombre")
		->groupBy("pc.apellido_materno")
		->groupBy("pc.apellido_paterno")
		->groupBy("est.id")
		->groupBy("est.nombre")
		->orderBy("est.nombre")
		->orderBy("dx.fecha");
	}

	public static function getCuposTotalesParaExtrasistema(){
		return self::cuposTotalesParaExtrasistema()->get();
	}

	public static function obtenerCuposExtraSistema($id){
		if($id == null){
			return self::cuposTotalesParaExtrasistema()->get();
		} 
		return self::cuposTotalesParaExtrasistema()->where("est.id", $id)->get();
	}

	public function cuposTotalesExtrasistema(){
		return self::cuposTotalesParaExtrasistema()
		->where("est.id", $this->id);
	}

	public function getCuposTotalesExtrasistema(){
		return $this->cuposTotalesExtrasistema()->get();
	}

	public static function cuposParaExtrasistema(){
		return self::_cuposParaExtrasistema()
		->select(
			"pc.rut",
			"pc.dv",
			"pc.nombre AS paciente",
			"pc.apellido_paterno",
			"pc.apellido_materno",
			"dx.caso",
			"dx.fecha",
			"est.id AS id_est",
			"est.nombre AS nombre_est",
			"ue.id as id_unidad",
			"ue.alias AS nombre_unidad",
			DB::raw("count(cm.id) as cantidad"),

			"dx.id",
			"ex.id AS id_est_ex",
			"ex.nombre AS est_ex"
		)
		->groupBy("dx.caso")
		->groupBy("dx.fecha")
		->groupBy("dx.id")
		->groupBy("ex.id")
		->groupBy("ex.nombre")
		->groupBy("est.id")
		->groupBy("est.nombre")
		->groupBy("ue.id")
		->groupBy("ue.alias")
		->groupBy("pc.rut")
		->groupBy("pc.dv")
		->groupBy("pc.nombre")
		->groupBy("pc.apellido_materno")
		->groupBy("pc.apellido_paterno")
		
		->orderBy("est.nombre")
		->orderBy("dx.fecha");
	}

	public static function getCuposParaExtrasistema(){
		return self::cuposParaExtrasistema()->get();
	}

	public function cuposExtrasistema(){
		return self::cuposParaExtrasistema()
		->where("est.id", $this->id);
	}
	public function getCuposExtrasistema(){
		return $this->cuposExtrasistema()->get();
	}

	public function cuposExtrasistemaCaso($idCaso){
		return $this->cuposExtrasistema()
		->where("dx.caso", "=", $idCaso);
	}

	public function getCuposExtrasistemaCaso($idCaso){
		return $this->cuposExtrasistemaCaso($idCaso)->get();
	}

	public function getPermisos()
	{
		return PermisosEstablecimiento::rightJoin("establecimientos AS e", "e.id", "=", "permisos_establecimientos.establecimiento")
		->where("e.id", $this->id)
		->first();

	}

	public static function similar($str){
		return self::whereRaw("similarity(nombre, ?) > 0.3")
			->orderByRaw("similarity(nombre, ?) desc")
			->addBinding($str)->addBinding($str)->firstOrFail();
	}

	public function unidadSimilar($str){
		return $this->unidades()
			->whereRaw("similarity(alias, ?) > 0.7")
			->orderByRaw("similarity(alias, ?) desc")
			->addBinding($str)->addBinding($str)->firstOrFail();
	}

	public function evolucionRiesgo($anno = null, $mes = null){
		$now = \Carbon\Carbon::now();
		if(!$anno){
			$anno = $now->year;
		}
		if(!$mes){
			$mes = $now->month;
		}
		$fecha_desde = \Carbon\Carbon::createFromDate($anno, $mes, 1)->firstOfMonth();
		$fecha_hasta = \Carbon\Carbon::createFromDate($anno, $mes, 1)->lastOfMonth();
		$asas=$this->id;
		return new \Illuminate\Database\Eloquent\Collection(

			DB::select("
				select
				count(riesgo) as cantidad,
				riesgo, fecha, unidad_en_establecimiento, nombre_servicio from (
					select f.id, f.caso, first_value(f.riesgo) over (partition by f.caso, f.key order by f.fecha) as riesgo, f.fecha
				, row_number() over (partition by f.caso, f.fecha order by h.fecha) as rk, s.establecimiento as unidad_en_establecimiento, ue.alias as nombre_servicio
				FROM (
					SELECT s.id as caso, riesgo, sum(case WHEN riesgo is not null THEN 1 else 0 END) over (order by s.id, s.fecha) as key, s.fecha::date, ev.id from t_evolucion_casos ev RIGHT JOIN (
						select fecha, id from (
							select generate_series((select min(fecha) from t_evolucion_casos)::date, '$fecha_hasta', '1 day'::interval)::date as fecha
						) as fecha
						inner join (
							select id,
							fecha_ingreso,
							CASE WHEN fecha_termino is null THEN (now()::date + '1 day'::interval) ELSE (fecha_termino::date + '1 day'::interval) END as fecha_termino
							from casos
						) as casos
						on casos.fecha_termino > fecha.fecha
						order by id, fecha
					) s ON s.id = ev.caso AND s.fecha::date = ev.fecha::date
					/*order by s.id, s.fecha*/
				) AS f
				left JOIN (select cama, caso, fecha, CASE WHEN fecha_liberacion is null THEN now()::date + '1 day'::interval ELSE fecha_liberacion::date + '1 day'::interval END as fecha_liberacion FROM t_historial_ocupaciones order by fecha desc) h ON h.caso = f.caso AND f.fecha::date + '1 day'::interval > h.fecha AND f.fecha < h.fecha_liberacion
				LEFT JOIN camas cm on cm.id = h.cama
				LEFT JOIN salas s on cm.sala = s.id
				LEFT JOIN unidades_en_establecimientos ue ON s.establecimiento = ue.id
				WHERE ue.establecimiento = $asas
				/*group by f.id, f.caso, f.riesgo, f.key, f.fecha, s.establecimiento*/
				/*order by caso, f.fecha*/
				) as ff WHERE rk = 1 AND fecha >= '$fecha_desde'
				group by fecha, riesgo, unidad_en_establecimiento, nombre_servicio order by nombre_servicio asc, fecha asc, riesgo asc
				")
		);
	}

	public static function obtenerTodos(){
		return self::orderBy("nombre", "asc")->get();
	}

	public static function obtenerHospitalesRegion($id_region){
		return self::where("id_region", $id_region)->orderBy("nombre", "asc")->get();
	}


	public function camas(){
		return $this->unidades()->camas();
	}

	public function casos(){
		return $this->hasMany("App\Models\Caso", "establecimiento", "id");
	}

}

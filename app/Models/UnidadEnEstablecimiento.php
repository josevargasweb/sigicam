<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Auth;
use Log;

class UnidadEnEstablecimiento extends Model{

	protected $table = "unidades_en_establecimientos";

	public $fecha = null;

	public function setFecha(\Carbon\Carbon $fecha = null){
		if(is_null($fecha)){
			$this->fecha = \Carbon\Carbon::now();
		}
		else{
			$this->fecha = $fecha;
		}
		return $this;
	}

	public function __construct(array $attr = [], \Carbon\Carbon $fecha = null){
		$this->fecha = null;
		$this->setFecha($fecha);
		parent::__construct($attr);
	}

	public function unidades(){
		return $this->belongsToMany('App\Models\Unidad', 'servicios_ofrecidos', 'unidad_en_establecimiento', 'unidad');
	}

	public function serviciosOfrecidos(){
		return $this->belongsToMany('App\Models\Unidad', 'servicios_ofrecidos', 'unidad_en_establecimiento', 'unidad');
	}

	public function serviciosRecibidos(){
		return $this->belongsToMany('App\Models\Unidad', 'servicios_recibidos', 'unidad_en_establecimiento', 'unidad');
	}

	public function establecimientos(){
		return $this->belongsTo('App\Models\Establecimiento', 'establecimiento', 'id');
	}

	public function area(){
    	return $this->belongsTo(AreaFuncional::class,'id_area_funcional');
    }

	public function unidadesAfines(){
		return DB::table("unidades_en_establecimientos as u")
		->join("servicios_ofrecidos as srv", "srv.unidad_en_establecimiento", "=", "u.id")
		->join(DB::raw("(select srv.* from servicios_ofrecidos srv inner join unidades u on u.id = srv.unidad) AS afin"), "srv.unidad", "=", "afin.unidad")
		->distinct()->select("u.*")
		->where("afin.unidad_en_establecimiento", $this->id);
	}

	public function unidadesDerivables(){
		return DB::table("servicios_ofrecidos AS ofr")
		->join("servicios_recibidos AS rec", "rec.unidad", "=", "ofr.unidad")
		->join("unidades_en_establecimientos AS ue", "ue.id", "=", "rec.unidad_en_establecimiento")
		->distinct()->select("ue.*")
		->where("ofr.unidad_en_establecimiento", $this->id)
		->where("rec.unidad_en_establecimiento", "<>", $this->id);
	}

	public static function getAliasUnidad($id, $unidad){
		return self::where("establecimiento", "=", $id)->where("id", "=", $unidad)->first()->alias;
	}

	public static function getServiciosEstablecimientos($id, $unidadEn){
		$response=array();
		$servicios=DB::table("unidades_en_establecimientos as u")
		->join("servicios_ofrecidos as s", "s.unidad_en_establecimiento", "=", "u.id")
		->join("unidades as un", "un.id", "=", "s.unidad")
		//->where("u.establecimiento", "=", $id)
		->where("s.unidad_en_establecimiento", "=", $unidadEn)
		->select("s.unidad", "un.nombre")
		->orderBy("nombre", "desc")
		->get();
		foreach ($servicios as $servicio) {
			$response[$servicio->unidad]=$servicio->nombre;
		}
		return $response;
	}

	public function scopeConCamas(){
		return self::whereHas("salas",  function($q){
			$q->whereHas("camas", function($qq){
				$qq->vigentes();
			});
		});
	}

	public function scopeConCamas2(){
		return self::whereHas("salas");
	}

	public static function getServiciosRecibidosEstablecimientos($id, $unidadEn){
		$response=array();
		$servicios=DB::table("unidades_en_establecimientos as u")
		->join("servicios_recibidos as s", "s.unidad_en_establecimiento", "=", "u.id")
		->join("unidades as un", "un.id", "=", "s.unidad")
		->where("u.establecimiento", "=", $id)
		->where("s.unidad_en_establecimiento", "=", $unidadEn)
		->select("s.unidad", "un.nombre")
		->orderBy("nombre", "desc")
		->get();
		foreach ($servicios as $servicio) {
			$response[$servicio->unidad]=$servicio->nombre;
		}
		return $response;
	}

	public static function getIDServiciosEstablecimientos($id){
		$response=array();
		$servicios=DB::table("servicios_ofrecidos as s")
		->join("unidades_en_establecimientos as un", "un.id", "=", "s.unidad_en_establecimiento")
		->select("s.unidad")->where("un.establecimiento", "=", $id)->get();
		foreach ($servicios as $servicio) {
			$response[]=$servicio->unidad;
		}
		return $response;
	}

	public static function getServiciosNoEstablecimientos($id){
		$response=array();
		$idServicios=self::getIDServiciosEstablecimientos($id);
		$servicios=DB::table("unidades as un")->select("un.id as unidad", "un.nombre")->distinct();
		if(!empty($idServicios)) $servicios=$servicios->whereNotIn("un.id", $idServicios);
		$servicios=$servicios->get();
		foreach ($servicios as $servicio) {
			$response[$servicio->unidad]=$servicio->nombre;
		}
		return $response;
	}

	public static function getIDServiciosRecibidosEstablecimientos($id){
		$response=array();
		$servicios=DB::table("servicios_recibidos as s")
		->join("unidades_en_establecimientos as un", "un.id", "=", "s.unidad_en_establecimiento")
		->select("s.unidad")->where("un.establecimiento", "=", $id)->get();
		foreach ($servicios as $servicio) {
			$response[]=$servicio->unidad;
		}
		return $response;
	}

	public static function getServiciosRecibidosNoEstablecimientos($id){
		$response=array();
		$idServicios=self::getIDServiciosRecibidosEstablecimientos($id);
		$servicios=DB::table("unidades as un")->select("un.id as unidad", "un.nombre")->distinct();
		if(!empty($idServicios)) $servicios=$servicios->whereNotIn("un.id", $idServicios);
		$servicios=$servicios->get();
		foreach ($servicios as $servicio) {
			$response[$servicio->unidad]=$servicio->nombre;
		}
		return $response;
	}

	public function salas(){
		return $this->hasMany("App\Models\Sala", "establecimiento", "id");
	}

	public function camas(){
		return $this->hasManyThrough("App\Models\Cama", "App\Models\Sala", "establecimiento", "sala");
	}
	public function historialCamas(){
		return $this->hasMany("App\Models\HistorialCamasUnidades", "unidad", "id");
	}

	public function camasEnFecha(\Carbon\Carbon $fecha = null){
		if (is_null($fecha)) $fecha = \Carbon\Carbon::now();
		DB::statement("DROP TABLE IF EXISTS temp_historial_camas_en_unidades");
		DB::statement("CREATE TEMP TABLE temp_historial_camas_en_unidades AS (SELECT *, row_number() over (partition by cama order by fecha ASC) as rko, case when fecha <= ? THEN sum(1) over (partition by cama order by fecha DESC) else 0 end as rk FROM t_historial_camas_en_unidades)", ["{$fecha->copy()->endOfDay()}"]);
		return $this->belongsToMany("App\Models\Cama", "temp_historial_camas_en_unidades", "unidad", "cama")->withPivot("cama", "unidad", "fecha", "rk", "rko")
			->where(function($q) use ($fecha) {
				$q->where("rk", 1)
				->orWhere(function($q) use ($fecha){
						$q->where("fecha", ">", "{$fecha->copy()->startOfDay()}")->where("rko", 1);
				});
			});
	}

	public function camasReconvertidas($fecha = null){
		if (is_null($fecha)) $fecha = \Carbon\Carbon::now();
		DB::statement("DROP TABLE IF EXISTS temp_reconvertidas");
		DB::statement("CREATE TEMP TABLE temp_reconvertidas AS (select distinct on (cama) *  from t_historial_camas_en_unidades where fecha <= ? order by cama, fecha desc)", ["{$fecha->copy()->endOfDay()}"]);
		return $this->camas()->join("temp_reconvertidas as tmp", "tmp.cama", "=", "camas.id")->join("salas as s", function($j){
				$j->on("s.id", "=", "camas.sala")
					->on("s.establecimiento", "<>", "tmp.unidad");
			});
	}

	public function camasReconvertidasActuales($fecha = null){
		return $this->camasEnFecha($fecha)
		->join("salas AS s", function($j){
			$j->on("s.id", "=", "camas.sala")
			->on("s.establecimiento", "<>", "temp_historial_camas_en_unidades.unidad");
		});
	}

	public function camasBloqueadas(){
		return $this->camasEnFecha($this->fecha)->camasBloqueadas($this->fecha);
	}

	public function camasReservadas(){
		return $this->camasEnFecha($this->fecha)->reservadas($this->fecha);
	}

	public function camasLibres(){
		return $this->camasEnFecha($this->fecha)->camasLibres($this->fecha);
	}

	public function camasOcupadas(){
		return $this->camasEnFecha($this->fecha)->ocupadas($this->fecha);
	}

	public function camasHabilitadas(){
		return $this->camasEnFecha($this->fecha)->habilitadas($this->fecha);
	}

	public function camasVigentes(){
		return $this->camas()->vigentes();
	}

	public function historialOcupaciones(){
		return HistorialOcupacion::whereHas('camas', function($q){
			$q->whereHas('sala', function($q){
				$q->where('establecimiento', "=", $this->id);
			});
		});
	}

	public function ultimasOcupaciones(){
		return $this->historialOcupaciones()
		->where("fecha_liberacion", "=", null)
		->where("rk", "=", 1);
	}

	public function promedioOcupacion(){
		return $this->historialOcupaciones()
		->select(DB::raw("AVG((CASE WHEN fecha_liberacion IS NULL THEN now() ELSE fecha_liberacion END ) - fecha) as promedio"))
		->get();
	}

	public function ocupacionesMayoresAlPromedio(){
		$q = $this->historialOcupaciones()
		->select(
			DB::raw("*, AVG((CASE WHEN fecha_liberacion IS NULL THEN now() ELSE fecha_liberacion END ) - fecha) OVER (partition by 1) as avg"),
			DB::raw("(CASE WHEN fecha_liberacion IS NULL THEN now() ELSE fecha_liberacion END ) - fecha AS estadia")
		);
		return DB::table(DB::raw("({$q->toSql()}) AS promedios"))
		->mergeBindings($q->getQuery())
		->whereRaw("promedios.avg >= promedios.estadia AND promedios.avg > '4 days'::interval ")
		->get();
	}

	public function porcentajeOcupacion(){
		$ocupadas = $this->ultimasOcupaciones()->count();
		$total = $this->camas()->count();
		return $total > 0 ? $ocupadas * 100.0 / $total : 0;
	}

	public static function getAliasPorUrl($url){
		$idEstablecimiento=Session::get("idEstablecimiento");
		return self::where("url", "=", $url)->where("establecimiento", "=", $idEstablecimiento)->first()->alias;
	}

	public static function getServiciosEstablecimientosDistintosA($unidad){
		$response=array();
		$servicios=Consultas::unidadesEnEstablecimiento()
			->where("est.id", "=", Session::get('idEstablecimiento'))->where("url", "!=", $unidad)->where("visible",true)->orderBy('alias', 'asc')->get();
		foreach ($servicios as $servicio) {
			$response[$servicio->id]=$servicio->nombre;
		}
		return $response;
	}

	public static function getIdEstablecimiento($idEstablecimiento, $unidad){
		return self::where("establecimiento", "=", $idEstablecimiento)->where("url", "=", $unidad)->first()->id;
	}

	public static function getNombre($id){
		if(is_null($id)) return "";
		return self::where("id", "=", $id)->first()->alias;
	}

	public function tiposDeCama(){
		$q1 = DB::table("camas AS cm")
		->join("salas as s", "s.id", "=", "cm.sala")
		->join("unidades_en_establecimientos as ue", "ue.id", "=", "s.establecimiento")
		->join("historial_camas_en_unidades as hc", function($j){
			$j->on("hc.unidad", "=", "ue.id")->on("hc.cama", "=", "cm.id");
		})
		->join("tipos_cama AS tc", function($j){
			$j->on("tc.id", "=", "s.tipo_cama")
			->orOn("tc.id", "=", "hc.tipo")
			->orOn("tc.id", "=", "cm.tipo");
		});

		$c1 = clone $q1;
		$c2 = clone $q1;
		$c3 = clone $q1;


		$q2 = $c1->select(DB::raw("distinct cm.tipo as id, tc.nombre"))
		->where("ue.id", "=", $this->id)
		->where("hc.rk", "=", 1)
		->whereNotNull("cm.tipo");

		$q3 = $c2->select(DB::raw("distinct hc.tipo as id, tc.nombre"))
		->where("ue.id", "=", $this->id)
		->where("hc.rk", "=", 1)
		->whereNotNull("hc.tipo");

		$q4 = $c3->select(DB::raw("distinct s.tipo_cama as id, tc.nombre"))
		->where("ue.id", "=", $this->id)
		->where("hc.rk", "=", 1)
		->whereNotNull("s.tipo_cama");


		return $q2->union($q3)->union($q4)->get();
	}

	public static function unidadesEnEstablecimiento($idEstablecimiento){
		return self::select('id', 'alias as nombre')
		->where('establecimiento', $idEstablecimiento)
		->pluck('alias as nombre','id');
	}

	public static function nombreUnidad($unidad){
		return DB::table('unidades_en_establecimientos as u')
                ->select('u.alias as nombre')
                ->where('u.url', $unidad)
                ->where('u.establecimiento', Auth::user()->establecimiento)
                ->first()->nombre;
	}


	public static function generarMapaServicios(){
		//Se busca todas los servicios visibles en el establecimiento
		$area_func = UnidadEnEstablecimiento::select("a.nombre","a.id_area_funcional","unidades_en_establecimientos.id","unidades_en_establecimientos.alias","t.descripcion")
			->join("area_funcional as a","a.id_area_funcional","unidades_en_establecimientos.id_area_funcional")
			->join("tipos_unidad as t","t.id","unidades_en_establecimientos.tipo_unidad")
			->where("unidades_en_establecimientos.visible",true)
			->where("unidades_en_establecimientos.establecimiento",Auth::user()->establecimiento)
			//->groupBy("a.nombre","a.id_area_funcional","unidades_en_establecimientos.id","unidades_en_establecimientos.alias","t.descripcion")
			->orderBy("a.nombre", "asc")
			->get();

		$listaDeseados = [202,189];//Copiapo hijo 202,189
		//Falta escribir el nombre del area, si es adulto, neo o ped
		$servicios_modificados = [];
        $cambios = [];
		foreach($area_func as $respo){
            if(!in_array($respo->id, $cambios)){
                foreach($area_func as $respo2){
                    if($respo->alias == $respo2->alias && !in_array($respo2->id, $cambios) && $respo->id != $respo2->id){    
                        $servicios_modificados[$respo2->id] = [
							$respo2->alias." ".$respo2->descripcion,//alias servicio
							$respo2->id,//id servicio
							$respo2->id_area_funcional,//id area funcional
							$respo2->nombre//nombre area funcional
						];
						$servicios_modificados[$respo->id] = [
							$respo->alias." ".$respo->descripcion,//alias servicio
							$respo->id,//id servicio
							$respo->id_area_funcional,//id area funcional
							$respo->nombre//nombre area funcional
						];
                        array_push($cambios,$respo2->id);
                        array_push($cambios,$respo->id);
                    }elseif(!in_array($respo2->id, $cambios)){
                        $servicios_modificados[$respo2->id] = (in_array($respo2->id, $listaDeseados))?[$respo2->alias." ".$respo2->descripcion,$respo2->id,$respo2->id_area_funcional,$respo2->nombre]:[$respo2->alias,$respo2->id,$respo2->id_area_funcional,$respo2->nombre];
                    }
				}
            }
		}
		
		return $servicios_modificados;
	}

	public static function selectGenerarMapaServicios(){
		//Se busca todas los servicios visibles en el establecimiento
		$area_func = UnidadEnEstablecimiento::select("a.nombre","a.id_area_funcional","unidades_en_establecimientos.id","unidades_en_establecimientos.alias","t.descripcion")
			->join("area_funcional as a","a.id_area_funcional","unidades_en_establecimientos.id_area_funcional")
			->join("tipos_unidad as t","t.id","unidades_en_establecimientos.tipo_unidad")
			->where("unidades_en_establecimientos.visible",true)
			->where("unidades_en_establecimientos.establecimiento",Auth::user()->establecimiento)
			//->groupBy("a.nombre","a.id_area_funcional","unidades_en_establecimientos.id","unidades_en_establecimientos.alias","t.descripcion")
			->orderBy("a.nombre", "asc")
			->get();

		$listaDeseados = [];//Copiapo hijo 202,189
		//Falta escribir el nombre del area, si es adulto, neo o ped
		$servicios_modificados = [];
        $cambios = [];
		foreach($area_func as $respo){
            if(!in_array($respo->id, $cambios)){
                foreach($area_func as $respo2){
                    if($respo->alias == $respo2->alias && !in_array($respo2->id, $cambios) && $respo->id != $respo2->id){    
                        $servicios_modificados[$respo2->id] = $respo2->alias." ".$respo2->descripcion;//alias servicio
						$servicios_modificados[$respo->id] = $respo->alias." ".$respo->descripcion;//alias servicio
                        array_push($cambios,$respo2->id);
                        array_push($cambios,$respo->id);
                    }elseif(!in_array($respo2->id, $cambios)){
                        $servicios_modificados[$respo2->id] = (in_array($respo2->id, $listaDeseados))?$respo2->alias." ".$respo2->descripcion:$respo2->alias;
                    }
				}
            }
		}
		
		return $servicios_modificados;
	}

	public static function traductorNombreUnidad($id){
		return UnidadEnEstablecimiento::select('alias')->find($id)->alias;
	}

}

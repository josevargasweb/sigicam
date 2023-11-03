<?php

namespace App\Models;
use Log;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Exception;
use Illuminate\Support\Str;
use App\Models\TipoUnidad;
use Carbon\Carbon;
use Auth;

class Cama extends Model{
	protected $table = "camas";

	public static function boot(){

		parent::boot();
	}

	public function historialUnidades(){
		return $this->hasMany("App\Models\HistorialCamasUnidades", "cama", "id");
	}

	public function reservas(){
		return $this->hasMany("App\Models\Reserva", "cama", "id");
	}

	public function ocupaciones(){
		return $this->belongsTo("App\Models\HistorialOcupacion", "id", "cama")->orderBy("fecha", "desc");
	}

	public function historialOcupaciones(){
		return $this->hasMany("App\Models\HistorialOcupacion", "cama", "id");
	}

	public function historialReservas(){
		return $this->hasMany("App\Models\Reserva", "cama", "id")->orderBy("fecha", "desc");
	}

	public function eliminacion(){
		return $this->hasOne("App\Models\HistorialEliminacion", "cama", "id");
	}

	public function bloqueos(){
		return $this->hasMany("App\Models\HistorialBloqueo", "cama", "id")->orderBy("fecha", "desc");
	}

	public function historialReconversiones(){
		return $this->hasMany("App\Models\HistorialCamasUnidades", "cama", "id");
	}

	public function sala(){
		return $this->belongsTo("App\Models\Sala", "sala", "id");
	}
    public function salaDeCama(){
        return $this->belongsTo("App\Models\Sala", "sala", "id");
    }
	public static function getIdCama($cama){

		return DB::table("camas as cm")->where("id_cama", "=", $cama)->select("cm.id")->first()->id;
	}

	public function casos(){
		return $this->belongsToMany('App\Models\Cama', 'App\Models\historial_ocupaciones', 'cama', 'caso');
	}

	public function tipoCama(){
		return $this->belongsTo("App\Models\TipoCama", "tipo", "id");
	}

	public function tipoUnidad(){
		return $this->belongsTo("App\Models\TipoUnidad", "tipo_unidad", "id");
	}

	public function bloquear($motivo){
		if ($this->enUso()){
			throw new Exception("La cama está en uso");
		}
		
		$bloqueo = new HistorialBloqueo();
		$bloqueo->fecha = DB::raw("date_trunc( 'second', now() )");
		$bloqueo->cama = $this->id;
		$bloqueo->motivo = $motivo;
		$bloqueo->save();
		return $bloqueo;
	}

	public function getid(){
		return $this->id;
	}

	public function bloqueoAutomatico(){
		$bloqueo = new HistorialBloqueo();
		$bloqueo->fecha = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
		$bloqueo->cama = $this->id;
		$bloqueo->motivo = "otros";
		$bloqueo->save();
	}

	public function enUso(){
		$r = $this->ocupaciones()->whereFecha_liberacion(null)->get() ;
		return !$r->isEmpty();
	}

	public function bloqueado(){
		$r = $this->bloqueos()->whereFecha_habilitacion(null)->first();
		/* Si retorna null, la cama está libre */
		return $r !== null;
	}

	public function desbloquear($motivo){
		$bloq = $this->bloqueos()->whereFecha_habilitacion(null)->orderBy("fecha", "desc")->first();
		if ( empty($bloq) ){
			throw new Exception("La cama no está bloqueada");
		}
		$bloq->fecha_habilitacion = DB::raw("date_trunc( 'second', now() )");
		$bloq->motivo_habilitacion = $motivo;
		$bloq->save();
		
	}

	public function eliminar($motivo = null){
	    if($this->enUso()){
			throw new Exception("La cama está en uso");
		}
		$eliminacion = new HistorialEliminacion();
   		$eliminacion->cama = $this->id;
		$eliminacion->fecha = DB::raw("date_trunc( 'second', now() )");
		$eliminacion->motivo = $motivo;
		$eliminacion->save();
		return $eliminacion;
	}
	public function reconvertir($id_unidad_destino){
	}
	public function reconvertirOriginal(\Carbon\Carbon $fecha = null){
		if ($fecha === null){
			$fecha = \Carbon\Carbon::now();
		}
		$ult = $this->historialReconversiones()->orderBy("fecha", "desc")->first();
		$sala = $this->sala()->first();
		if($sala->establecimiento != $ult->unidad) {
			$hist = new HistorialCamasUnidades();
			$hist->cama = $this->id;
			$hist->unidad = $sala->establecimiento;
			$hist->fecha = "{$fecha}";
			$hist->save();
			return $hist;
		}
		else{
			return $ult;
		}
	}
	public function mover($id_sala_destino){
	}

	public function scopeVigentes($query, $fecha = null){
		
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		/* CAMAS que NO ESTAN ELIMINADAS */
		/*return $query->whereRaw("camas.id NOT IN (SELECT cama FROM historial_eliminacion_camas ) ");*/
		/* @var $query Cama */
		return $query->whereDoesntHave("eliminacion", function ($q) use ($fecha) {
			$q->where("fecha", "<", "{$fecha}");
		});
	}

	public function scopeHabilitadas($query, $fecha = null){
		/* CAMAS QUE NO ESTAN NI ELIMINADAS NI BLOQUEADAS */
		/*return $query->whereRaw("camas.id IN (SELECT id FROM camas_habilitadas ) ");*/
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		$ret = $query->whereDoesntHave("bloqueos", function($q) use ($fecha){
			$q->where("fecha", "<", $fecha)
			->where(function($q) use ($fecha) {
				$q->whereNull("fecha_habilitacion")
					->orWhere("fecha_habilitacion", ">", $fecha);
			});
		});
		return $ret->vigentes($fecha);
	}

	public function scopeBloqueadas($query, $fecha = null){
		/* Estas son libres y bloqueadas*/
		return $query->join("ultimos_bloqueos_camas AS ub", "ub.cama", "=", "camas.id")
			->select("camas.*", "ub.fecha", "ub.motivo")
			->whereRaw("camas.id IN (SELECT cama FROM ultimos_bloqueos_camas)");
	}

	public function scopeCamasBloqueadas($query, $fecha = null){
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		return $query->whereHas("bloqueos", function($q) use ($fecha) {
			$q->where("fecha", "<", $fecha)
			->where(function($q) use ($fecha) {
				$q->whereNull("fecha_habilitacion")
					->orWhere("fecha_habilitacion", ">", $fecha);
			});
		})->with("bloqueos");
	}

	public function scopeEliminadas($query, $fecha = null){
		return $query->join("historial_eliminacion_camas AS h", "h.cama", "=", "camas.id")
			->select("camas.*", "h.fecha");
	}

	public function scopeCamasEliminadas($query, $fecha = null){
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		return $query->whereHas("eliminacion", function ($q) use ($fecha) {
			$q->where("fecha", "<", "{$fecha}");
		})->with("eliminacion");
	}

	public function scopeLibres($query, $fecha = null){
		return $query->habilitadas()
		->whereRaw("camas.id NOT IN (SELECT cama FROM historial_ocupaciones WHERE rk = 1 AND fecha_liberacion IS NULL)")
		->whereRaw("camas.id NOT IN (SELECT cama FROM reservas WHERE rk = 1 AND queda > '00:00:00')");
	}

	public function scopeCamasLibres($query, $fecha = null){
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		//return "nada";
		return $query;
		/*return $query->habilitadas($fecha)
			->whereDoesntHave("reservas", function($q) use ($fecha){
			$q->reservasVigentes($fecha);
		})->whereDoesntHave("historialOcupaciones", function($q) use ($fecha){
			$q->noLiberados($fecha);
		});*/
	}


	public function scopeOcupadas($query, $fecha = null){
		/*return $query->habilitadas()->whereRaw("camas.id IN (SELECT cama FROM historial_ocupaciones WHERE rk = 1 AND fecha_liberacion IS NULL)");*/

		return $query->whereHas("historialOcupaciones", function($q) use ($fecha) {
			$q->noLiberados($fecha);
		});
	}

	public function scopeReservadas($query, $fecha = null){
		return $query->whereRaw("camas.id IN (SELECT cama FROM ultimas_reservas)");
	}

	public function scopeCamasReservadas($query, $fecha = null){
		/* @var $query \Illuminate\Database\Eloquent\Builder */
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		return $query->whereHas("reservas", function($q) use ($fecha) {
			/* @var $q  \Illuminate\Database\Query\Builder */
			/*$q->where("fecha", "<", "{$fecha}")->whereRaw("(fecha + tiempo) <= ?", ["{$fecha}"])
			->selectRaw("distinct on (cama) *")->orderBy("fecha", "desc");*/
			$q->reservasVigentes($fecha);
		});
	}

	public function scopeReconvertidas($query, $fecha = null){
		/*return $query->join("salas AS s", "s.id", "=", "camas.sala")
			->join("ultimas_camas_unidades AS uc", "camas.id", "=", "uc.cama")
			->whereRaw("s.establecimiento <> uc.unidad");*/
		return $query->whereHas("historialReconversiones", function($q){

		});
	}

	public function scopeUnidad($query, $unidad){
		return $query->whereRaw("camas.sala IN (SELECT s.id FROM salas s
			INNER JOIN unidades_en_establecimientos ue ON s.establecimiento = ue.id
			WHERE ue.id = ?)")->addBinding($unidad);
	}

	public function scopeEstablecimiento($query, $establecimiento){
		return $query->whereRaw("camas.sala IN (SELECT s.id FROM salas s
			INNER JOIN unidades_en_establecimientos ue ON s.establecimiento = ue.id
			WHERE ue.establecimiento = ?")->addBinding($establecimiento);
	}

	public static function getNombreTipoCama($id){
		$cama=DB::table("tipos_cama as t")->where("id", "=", $id)->select("nombre")->first();
		if($cama == null) return "";
		return ucwords($cama->nombre);
	}
	

	public static function getCamaPorSalaEstab($sala, $establecimiento){
		$response=array();
		$camas=DB::table("camas as c")
		->join("salas as s", "s.id", "=", "c.sala")
		->join("unidades_en_establecimientos as u", "u.id", "=", "s.establecimiento")
		->select("c.id_cama", "c.id", "c.tipo", "c.diferenciacion")
		->where("s.id", "=", $sala)
		->where("s.establecimiento", "=", $establecimiento)->orderBy("c.id_cama")->get();
		foreach($camas as $cama){
			$response[]=[
			"idCama" => $cama->id_cama,
			"editar" => "<a class='cursor' onclick='editarCama(".$cama->id.", \"$cama->id_cama\", \"$cama->tipo\", \"$cama->diferenciacion\")'>Editar</a>",
			"tipo" => self::getNombreTipoCama($cama->tipo),
			"diferenciacion" => ucwords($cama->diferenciacion)
			];
		}
		return $response;
	}

	public static function getTotalDeCamas($id){
		return DB::table("unidades_en_establecimientos as u")
		->join("salas as s", "u.id", "=", "s.establecimiento")
		->join("camas as c", "s.id", "=", "c.sala")
		->where("u.id", "=", $id)->count();
	}

	public static function getTotalDeCamasPorSala($id, $idSala, $idEstab){
		return DB::table("unidades_en_establecimientos as u")
		->join("establecimientos as e", "e.id", "=", "u.establecimiento")
		->join("salas as s", "e.id", "=", "s.establecimiento")
		->join("camas as c", "s.id", "=", "c.sala")
		->where("u.id", "=", $id)->where("s.id", "=", $idSala)->where("u.establecimiento", "=", $idEstab)->count();
	}

	public static function getTotalDeCamasOcupadas($idEstablecimiento, $unidad){
		$total=0;
		$ocupaciones = Consultas::ultimoEstadoCamas()
		->where("est.id", "=", $idEstablecimiento)
		->where("ue.url", "=", $unidad)
		->get();
		foreach($ocupaciones as $ocupacion){
			if($ocupacion->ocupado !== null) $total++;
		}
		return $total;
	}

	public static function getPromedioEstadia($idEstablecimiento, $unidad){
		$promedio=0;
		$suma=0;
		$total=self::getTotalDeCamasOcupadas($idEstablecimiento, $unidad);
		$ocupaciones = Consultas::ultimoEstadoCamas()
		->where("est.id", "=", $idEstablecimiento)
		->where("ue.url", "=", $unidad)
		->get();
		foreach($ocupaciones as $ocupacion){
			if($ocupacion->ocupado !== null){
				$fecha=Consultas::tiemposOcupaciones()->where("rut", "=", $ocupacion->rut)->first()->fecha;
				$horas=($fecha == null) ? 0 : date("H", strtotime($fecha));
				$suma+=$horas;
			}
		}
		// $promedio=$suma/$total;
		return $promedio = $suma/$total;
	}

	public function disponible(\Carbon\Carbon $fecha = null){
		if(is_null($fecha)){
			$fecha = \Carbon\Carbon::now();
		}
		$historial_anterior = $this->historialOcupaciones()->where("fecha", "<=", "{$fecha}")->orderBy("fecha", "desc")->first();
		if(!is_null($historial_anterior)){
			if(is_null($historial_anterior->fecha_liberacion)){
				return false;
			}
			$fecha_liberacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $historial_anterior->fecha_liberacion);
			if($fecha_liberacion->gte($fecha)){
				return false;
			}
		}

		$historial_posterior = $this->historialOcupaciones()->where("fecha", ">", "{$fecha}")->orderBy("fecha", "asc")->first();

		return $historial_posterior === null;
	}

	public function insertarHistorialCama(HistorialOcupacion $h){
		return DB::transaction(function() use ($h) {

			$fecha = $h->fecha;
			
			//para comparar ambos commo tipo de dato fecha, se tiene que parsear la fecha
			$fecha = \Carbon\Carbon::parse($fecha);


			if ($fecha === null) {
				$fecha = \Carbon\Carbon::now();
			}
			/*
			 * esta función es copy-paste de Paciente::insertarCaso().
			 */
			$h_posteriores = $this->historialOcupaciones()->where("fecha", ">=", "{$fecha}")->orderBy("fecha", "asc")->first();
			
			if(!is_null($h_posteriores)){
				throw new \Illuminate\Database\Eloquent\ModelNotFoundException("La cama no puede ser ocupada en esta fecha.");
			}


			$h_anteriores = $this->historialOcupaciones()->where("fecha", "<", "{$fecha}")->orderBy("fecha", "desc")->first();
			
			
			if (!is_null($h_anteriores)) {

				if ($h_anteriores->fecha_liberacion === null) {
					$h_anteriores->fecha_liberacion = "{$fecha}";
				} else {
					$fecha_termino = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $h_anteriores->fecha_liberacion);
					if ($fecha_termino->gte($fecha)) {
						$h_anteriores->fecha_liberacion = "{$fecha->copy()->subMinute()}";
					}
				}
			}
			$h->cama = $this->id;
			$h->fecha = "{$fecha}";
			return $h;
		});
	}

	public function asignarCaso(Caso $caso){
		return DB::transaction(function() use ($caso) {
			//$fecha = \Carbon\Carbon::createFromFormat("Y-m-d", $caso->fecha_ingreso);
			$historial = new HistorialOcupacion();
			$historial->caso = $caso->id;
			$historial->cama = $this->id;
			$historial->fecha = $caso->fecha_ingreso;

			/*original*/
			$this->insertarHistorialCama($historial);
			
			$historial->save();
			return $historial;

			/*Prueba
			$var = $this->insertarHistorialCama($historial);
			return $var;*/

		});

	}

	public static function descripcionTipoUnidad($tipo_unidad){
		$data_unidad = TipoUnidad::find($tipo_unidad,['nombre','descripcion']);
		$nombre = $data_unidad["nombre"];
		$descripcion = $data_unidad["descripcion"];
		$contiene = (Str::contains($nombre, '_')) ? 'Si' : 'No';
		if($contiene == 'Si'){
			$partx = explode("_",$nombre);
			$detalle = end($partx);
			$tipo_unidad = Str::upper($descripcion) ." ($detalle)";
		}else{
			$tipo_unidad = Str::upper($descripcion);
		}
		return $tipo_unidad;
	}

	public static function dataCamasBloqueadas(){
		$camas = [];
		$hoy = Carbon::now();
		$msjNoDisponible = "No disponible";

		$camas_bloqueadas = DB::table("t_historial_bloqueo_camas as b")
								->select("c.id_cama as n_cama", "s.nombre as n_sala","u.alias as n_unidad","a.nombre as n_area", "b.fecha as fecha_bloqueo", "b.motivo", "b.fecha_habilitacion", "b.motivo_habilitacion")
								->leftjoin("camas as c", "c.id","=","b.cama")
								->leftjoin("salas as s", "s.id","=","c.sala")
								->leftjoin("unidades_en_establecimientos as u", "u.id", "=","s.establecimiento")
								->leftjoin("area_funcional as a", "a.id_area_funcional","=","u.id_area_funcional")
								->where([["u.visible", true], ["s.visible", true]])
								->orderBy("fecha_bloqueo","Desc")
								->get();

		foreach ($camas_bloqueadas as $key => $cama) {
			if ($cama->fecha_habilitacion == null) {
				$cama->fecha_habilitacion = $msjNoDisponible;
				$cama->motivo_habilitacion = $msjNoDisponible;
				$tiempo_bloqueada = $hoy->diffInDays($cama->fecha_bloqueo);
				$estado = "<h4><span class='label label-default'>Bloqueada</span></h4>";
			}else{
				$fecha_habilitacion = Carbon::parse($cama->fecha_habilitacion);
				$tiempo_bloqueada = $fecha_habilitacion->diffInDays($cama->fecha_bloqueo);
				$estado = "<h4><span class='label label-success'>Habilitada</span></h4>";
			}


			if ($tiempo_bloqueada >= 0) {
				$tiempo_bloqueada = " (".$tiempo_bloqueada." DÍAS)";
			}

			$fecha_bloqueo_ord=date("Y-m-d H:i", strtotime($cama->fecha_bloqueo));


			$camas [] = [
				$estado,
				$cama->n_cama,
				$cama->n_sala,
				$cama->n_unidad,
				$cama->n_area,
				// "<div hidden>".$fecha_bloqueo_ord."</div>".date("d-m-Y H:i", strtotime($cama->fecha_bloqueo)),
				date("d-m-Y H:i", strtotime($cama->fecha_bloqueo)),
				$tiempo_bloqueada,
				$cama->motivo,
				$cama->fecha_habilitacion,
				$cama->motivo_habilitacion
			];
		}
		return $camas;
	}

	public static function camasDisponiblesPorTipo($tipo, $fecha){
		// Log::info("tipo: {$tipo} - fecha: {$fecha}");
		$establecimiento = Auth::user()->establecimiento;
		$disponibles = DB::select("SELECT count(cv.tipo) as disponibles 
			from camas_vigentes_vista as cv
			join tipos_cama as tc on tc.id = cv.tipo
			where
			id_establecimiento = $establecimiento
			and cv.tipo = $tipo
			and created_at < '$fecha'
			and sala not in (select id from salas where visible is false)
			group by cv.tipo
		");
		return (isset($disponibles[0]->disponibles))?$disponibles[0]->disponibles:0;
	}
}

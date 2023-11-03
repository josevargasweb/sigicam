<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Log;
use DB;
use Auth;
use Session;
use App\Models\HistorialOcupacion;
use App\Models\THistorialOcupaciones;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class Caso extends Model implements Auditable{

	use \OwenIt\Auditing\Auditable;

	protected $table = "casos";
	protected $fillable = [
		'fecha_termino',
		'motivo_termino'
	];

	protected $auditInclude = [

    ];

    protected $auditTimestamps = true;
	public $timestamps = true;
    protected $auditThreshold = 10;

	public static function egresos(){
		$fecha = date("Y-m-d");

		$estab = Auth::user()->establecimiento;
		if(Session::get('usuario')->tipo != 'admin_ss'){
			//$estab = "establecimientos.id = ".Session::get("idEstablecimiento");
			$estab = "establecimientos.id = ".$estab;
		}else{
			$estab = "TRUE";
		}

		return DB::select(DB::raw("select *
		from casos
		inner join establecimientos on casos.establecimiento = establecimientos.id
		where ".$estab." and
		(fecha_termino::date) = '"."$fecha"."'"));

	}

	public static function estada(){

		$estab = Auth::user()->establecimiento;
		/* if(Session::get('usuario')->tipo != 'admin_ss'){
			$estab = "establecimientos.id = ".Session::get("idEstablecimiento");
		}else{
			$estab = "TRUE";
		} */
		$fecha = date("Y-m-d");
		/* $casos_pacientes = DB::select(DB::raw("select casos.id
		from casos
		inner join establecimientos on casos.establecimiento = establecimientos.id
		where establecimientos.id =  ".$estab." and
		(casos.fecha_termino is null or casos.fecha_termino::date = '"."$fecha"."'
		)")); */

		$casos_pacientes = DB::select(DB::raw("select h.caso
		from historial_ocupaciones as h
		join casos as c on c.id = h.caso
		left join establecimientos as e on e.id = c.establecimiento
		where fecha_ingreso_real is not null and fecha_liberacion is null and rk = 1"));

		$hoy = Carbon::now();

		/* $pacientes_en_transito = DB::table("lista_transito")
									->select("caso")
									->whereNull("fecha_termino")
									->get(); */

		//return $pacientes_en_transito;
		$suma_horas = 0;
		$total = 0;
		foreach($casos_pacientes as $key => $valor){

			$ocupacion = DB::table("t_historial_ocupaciones")
							->where("caso", $valor->caso)
							->whereNull("fecha_liberacion")
							->whereNotNull("fecha_ingreso_real")
							->first();

			if(!is_null($ocupacion)){
				$inicio_hospitalizacion = Carbon::parse($ocupacion->fecha_ingreso_real);
				$diferencia_horas = $hoy->diffInDays($inicio_hospitalizacion);
				$suma_horas += $diferencia_horas;
				$total++;
			}

		}

		if($total != 0){
			$prom = $suma_horas / $total;
		}else{
			$prom = 0;
		}


		return round($prom, 0)." días";



	}

	public static function boot(){
		parent::boot();

		self::saving(function($ev){
			/* @var $ev EvolucionCaso
			 * @var $reciente EvolucionCaso
			 */
			return $ev;
			if(is_null($ev->diagnostico)){
				$ev->diagnostico = '';
				Log::warning("Caso {$ev->id} ingresado sin diagnostico");
			}



		});

        self::created(function($ev){

            $d = new HistorialDiagnostico();
            $d->caso = $ev->id;
            $d->fecha = $ev->fecha_ingreso;
            $d->diagnostico = $ev->diagnostico;
            //$d->save();

        });


        self::updating(function($caso){
            /* @var $caso Caso */
            $original = $caso->getOriginal();
            foreach($original as $campo => $valor){
                if(trim($caso->$campo) === '' && !is_bool($caso->$campo) ) $caso->$campo = $valor;
            }
        });
	}

    public function diagnosticos(){
        return $this->hasMany("App\Models\HistorialDiagnostico", "caso", "id");
    }

	public function scopeEnFecha($query, \Carbon\Carbon $fecha){
		return $query->where("fecha_ingreso", "<=", "{$fecha}")->where(function($q) use ($fecha){
			$q->whereNull("fecha_termino")->orWhere("fecha_termino", "<=", "{$fecha}");
		});
	}

	public function historialEvolucion(){
		return $this->hasMany('App\Models\HistorialEvolucionCaso', 'caso', 'id');
	}

	public function evolucionActual(){
		return $this->historialEvolucion()->orderBy('fecha', 'desc')->first();
	}

	public function reservas(){
		return $this->hasMany("App\Models\Reserva", "caso", "id");
	}

	public function historialOcupacion(){

		return $this->hasMany('App\Models\HistorialOcupacion', 'caso', 'id')->orderBy('fecha', 'desc');
	}

	public function historialOcupacionNoLiberados(){
		return $this->historialOcupacion()->noLiberados();
	}

	public function camas(){
		return $this->belongsToMany('App\Models\Cama', 't_historial_ocupaciones', 'caso', 'cama')->withPivot(["cama", "caso", "fecha"]);
	}

	public function listaEspera(){
		return $this->hasMany('App\Models\ListaEspera', 'caso', 'id')->orderBy('fecha', 'desc');
	}

	public function listaEsperaActiva(){
		return $this->listaEspera()->whereNotNull("fecha_termino")->first();
	}

	public function derivaciones(){
		return $this->hasMany('App\Models\Derivacion', 'caso', 'id')->orderBy('fecha', 'desc');
	}

	public function derivacionesExtrasistema(){
		return $this->hasMany('App\Models\DerivacionesExtrasistema', 'caso', 'id')
			->orderBy('fecha', 'desc');
	}

	public function derivacionesExtrasistemaActivas(){
		return $this->derivacionesExtrasistema()
			->where('fecha_rescate', null);
	}

	public function derivacionesActivas(){
		return $this->derivaciones()
			//->where("fecha_cierre", null);
			->where(function($q){
			$q->whereNotNull("fecha_cierre")->where(function($q){
				$q->where("motivo_cierre", "aceptado")
					->orWhere("motivo_cierre", "aceptado, pendiente de cama");
			})
			->orWhere("fecha_cierre", null);
		});
	}

	public function liberar($motivo, $now = null){
		return DB::transaction(function() use ($motivo, $now){
			if (empty($now)){
				$now = \Carbon\Carbon::now();
			} 
			/* Aquí puede ocurrir que el caso ya haya estado cerrado cuando se añadió datos
			entonces hay que evitar cerrar de nuevo. */
			$h = HistorialOcupacion::enFecha($now)
				->where("caso", $this->id)
				->orderBy("fecha", "desc")->firstOrFail();
			if($h->fecha_liberacion !== null){
				if($this->fecha_termino === null){
					$this->fecha_termino = $now;
					$this->save();
					return $h;
				}
			}
			$h->fecha_liberacion = $now;
			$h->motivo = $motivo;
			if($motivo != 'traslado externo' && $motivo != 'traslado interno') {
				if($this->fecha_termino === null) {
					$this->fecha_termino = $now;
					$this->save();
				}
			}
			$h->save();
			return $h;
		});
	}

	public function cerrar($motivo, $now = null){
		return $this->liberar($motivo, $now);
	}

	public static function liberarCama($idCaso, $motivo, $now = null){
		if($now === null){
			$fecha = \Carbon\Carbon::now();
		}
		else{
			$fecha = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $now);
		}
		$caso = self::findOrFail($idCaso);
		$fecha_caso = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $caso->fecha_ingreso);

		if($fecha_caso->gt($fecha)){
			throw new MensajeException("La fecha de liberación es anterior a la de ingreso.");
		}

		$h=HistorialOcupacion::whereNull("fecha_liberacion")
			->where("rk", "=", "1")
			->where("caso", "=", $idCaso)
			->first();
		if($h != null) {
			$fecha_ocupacion = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $h->fecha);
			if($fecha_ocupacion->gt($fecha)){
				throw new MensajeException("La fecha de liberación es anterior a la de ocupación de la cama.");
			}
			$h->fecha_liberacion = $fecha->format("Y-m-d H:i:s");
			$h->motivo = $motivo;
			$h->save();
			if ($motivo != 'traslado externo' && $motivo != 'traslado interno'){
				$caso->fecha_termino = $fecha->format("Y-m-d H:i:s");
				$caso->save();
			}
		}
		return $caso;

	}

	public function asignarCama($idCama, $now = null){
		/* Now es la fecha de asignacion */
		if(empty($now)){
			$now = date("Y-m-d H:i:s");
		} 
		$h = HistorialOcupacion::where("fecha", "=", $now)->where("caso", $this->id)->where("cama", $idCama)->orderBy("fecha", "desc")->first();
		if($h){
			return $h;
		}

		$fecha_real_anterior  = HistorialOcupacion::where("caso", $this->id)->orderBy("fecha", "desc")->select('fecha_ingreso_real')->first();
		/*$h = new HistorialOcupacion();
		$h->fecha = $now;
		$h->caso = $this->id;
		$h->cama = $idCama;
		$h->fecha_ingreso_real = $fecha_real_anterior->fecha_ingreso_real;
		$h->save();
*/
		$h = new THistorialOcupaciones();
		$h->fecha = $now;
		$h->caso = $this->id;
		$h->cama = $idCama;
		$h->fecha_ingreso_real = $fecha_real_anterior->fecha_ingreso_real;
		$h->save();

		return $h;
	}

	public function reasignarCama($idCama, $cama_naranja, $now = null,$paciente = null){
		/* Aquí "now" será la fecha de cierre del antiguo historial y la fecha de apertura del nuevo.
		Se busca al historial directamente anterior a este para actualizarlo. */
		if (is_null($now)){
			$now = \Carbon\Carbon::now();
		}

		/* en caso de que también haya historial después de la fecha, hay que hacer unas piruetas
		para insertar el nuevo historial . */

		return DB::transaction(function() use($idCama, $now, $cama_naranja,$paciente){

			DB::beginTransaction();
			//Si viene la variable paciente es porque se está reingresando a un paciente egresado.
			if($paciente){
				//limpiando campos
				$datos_paciente = Paciente::where("id",$paciente)->firstorFail();
				
				$ultimocaso = $datos_paciente->casoActual()->firstorFail();
				if (isset($ultimocaso)) {
					
					if($ultimocaso->fecha_ingreso2 != null){
						$historialOcupacion = THistorialOcupaciones::where("caso", $ultimocaso->id)->orderBy("created_at", "desc")->first();
						if(isset($historialOcupacion) && $historialOcupacion->fecha != null){
							//paciente ocupando cama
							//Consultar si existe algun paciente ocupando el lugar que anteriormente era del paciente que se esta hospitalizando
							$confirmarCama = THistorialOcupaciones::where("cama",$historialOcupacion->cama)
							->whereNull("fecha_liberacion")
							->first();
							
							if(!isset($confirmarCama)){
								if($historialOcupacion->fecha_ingreso_real == null){
									//el paciente llego a lista de espera de hospitalizacion
									$pacienteTransito = ListaTransito::where("caso", $ultimocaso->id)->first();
									$pacienteTransito->fecha_termino = null;
									$pacienteTransito->save();
									
									$historialOcupacion->fecha_liberacion = null;
									$historialOcupacion->save();
								}
							}
						}else{
							//paciente en lista de espera
							ListaEspera::where("caso", $ultimocaso->id)->update(['fecha_termnino' => null]);
						}
					}
				}
				
				if(!isset($confirmarCama)){
					Caso::where("id", $ultimocaso->id)->update(['fecha_termino' => null, 'motivo_termino' => null]);
				}
				
				$hosp_dom = HospitalizacionDomiciliaria::where("caso", $ultimocaso->id)->whereNull('fecha_termino')->first();
				if($hosp_dom){
					$hosp_dom->fecha_termino = Carbon::now()->format('Y-m-d H:i:s');
					$hosp_dom->save();
				}
			}
			try{

				$h = HistorialOcupacion::where("fecha", "<", $now)->where("caso", $this->id)->orderBy("fecha", "desc")->firstOrFail();
				$tho = THistorialOcupaciones::where("fecha", "<", $now)->where("caso", $this->id)->orderBy("fecha", "desc")->firstOrFail();
				//revisar si la cama está ocupada o vacía para poder trasladar a un paciente a su misma cama (en caso de que esté egresado)
				$confirmarCama = THistorialOcupaciones::where("cama",$idCama)
				->whereNull("fecha_liberacion")
				->first();
				
				if ($h->cama == $idCama && $confirmarCama != null){
					return $h;
				}

				$h_posterior = HistorialOcupacion::where("fecha", ">=", $now)->where("caso", $this->id)->orderBy("fecha", "asc")->first();
				$tho_posterior = THistorialOcupaciones::where("fecha", ">=", $now)->where("caso", $this->id)->orderBy("fecha", "asc")->first();

				//verificar si el caso se encuentra abierto o cerrado
				$caso_p = Caso::where("id", $this->id)->first();

				if($caso_p->fecha_termino != null){
					//si esta abierto se tiene que abrir porque esta es una funcion que se usa en el buscador de pacientes
					//debido a que no me permite modificar caso_p, tuve que llamr de nuevo
					Caso::where("id", $this->id)->update(['fecha_termino' => null, 'motivo_termino' => null]);
				}

				if($cama_naranja != '[]'){
					$h->fecha_liberacion = $now;
					$h->motivo = "corrección cama";
					$tho->id_usuario_alta = Auth::user()->id;
				}else{
					$h->fecha_liberacion = $now;
					$h->motivo = "traslado interno";
					$tho->id_usuario_alta = Auth::user()->id;
				}
				$h->save();
				$tho->save();

				$h_nuevo = $this->asignarCama($idCama, $now);

				if($h_posterior !== null){
					if($cama_naranja != '[]'){
						$h_nuevo->fecha_liberacion = $h_posterior->fecha;
						$h_nuevo->motivo = "corrección cama";
						$tho_posterior->id_usuario_alta = Auth::user()->id;
					}else{
						$h_nuevo->fecha_liberacion = $h_posterior->fecha;
						$h_nuevo->motivo = "traslado interno";
						$tho_posterior->id_usuario_alta = Auth::user()->id;
					}
					$h_nuevo->save();
					$tho_posterior->save();
				}
				DB::commit();

				return $h_nuevo;

			}catch(Exception $ex){
				DB::rollback();
				return response()->json(["error"=>"Error al realizar traslado interno.", "ex"=>$ex->getMessage()]);
			}
		});
	}

	public function intercambiarCama(Caso $origen, \Carbon\Carbon $fecha = null){
		return DB::transaction(function() use ($origen, $fecha) {
			if (is_null($fecha)){
				$fecha = \Carbon\Carbon::now();
			}

			$cama_origen = $origen->liberar("traslado interno", $fecha)->camas()->first();
			$cama_destino = $this->liberar("traslado interno", $fecha)->camas()->first();

			$this->asignarCama($cama_origen->id, $fecha);
			$origen->asignarCama($cama_destino->id, $fecha);
			return $cama_destino;
		});

	}

	public function asignarPaciente($idPaciente){
		$this->paciente = $idPaciente;
		$this->save();
	}

	public function derivar($idEstablecimientoDestino, $comentario = null){
		$d = new Derivacion();
		$d->caso = $this->id;
		$d->usuario = Auth::user()->id;
		$d->destino = $idEstablecimientoDestino;
		$d->fecha = DB::raw( "date_trunc ( 'second', now() )" );
		$d->comentario = $comentario;
		$d->save();
		return $d;

	}

	public function paciente(){
		return $this->belongsTo('App\Models\Paciente', 'paciente', 'id');
	}

	public function pacienteCaso(){
		return $this->belongsTo('App\Models\Paciente', 'paciente', 'id');
	}

	public function establecimiento(\Carbon\Carbon $fecha = null){
		$h = $this->tieneCama($fecha);
		if($h){
			return Establecimiento::whereHas("unidades", function ($unidad) use ($h) {
				$unidad->whereHas("camas", function ($camas) use ($h) {
					$camas->where(
						"camas.id", $h->cama
					);
				});
			})->get();
		}
		$d = $this->tieneDerivacion($fecha);
		if($d){
			if($d->fecha_cierre) {
				return Establecimiento::whereHas("unidades", function ($unidad) use ($d) {
					$unidad->where("id", $d->destino);
				})->get();
			}
			else{
				return Establecimiento::whereHas("usuarios", function ($usuario) use ($d) {
					$usuario->where("id", $d->usuario);
				})->get();
			}
		}
		return new \Illuminate\Database\Eloquent\Collection([]);
	}

	public function tieneCama(\Carbon\Carbon $fecha = null){
		if (is_null($fecha)){
			$fecha = \Carbon\Carbon::now();
		}
		$hh = $this->historialOcupacion()->where("fecha", "<=", "{$fecha}")->orderBy("fecha", "desc")->get();
		if($hh->count() != 0) {
			return $hh->first();
		}
		return false;
	}

	public function tieneDerivacion(\Carbon\Carbon $fecha = null){
		if (is_null($fecha)){
			$fecha = \Carbon\Carbon::now();
		}
		$derivacion = $this->derivacionesActivas()->where("fecha", "<=", "{$fecha}")->orderBy("fecha", "desc")->get();
		if($derivacion->count() != 0){
			return $derivacion->first();
		}
		return false;
	}

	public function tieneDerivacionExtrasistema(){
		$derivacion = $this->derivacionesExtrasistemaActivas()->get();
		if($derivacion->count() != 0){
			return $derivacion->first();
		}
		return false;
	}

	public function asignarCasoCama(Cama $cama){
		return DB::transaction(function() use ($cama) {
			$historial = new HistorialOcupacion();
			$historial->cama = $cama->id;
			$historial->caso = $this->id;
			$historial->fecha = $this->fecha_ingreso;

			$cama->insertarHistorialCama($historial);

			$historial->save();
			return $historial;
		});
	}

	/*public function scopeEnEstablecimiento($query, $idEst){
		return $query->whereHas("historialOcupacion", function($q) use($idEst){
			$q->whereHas("camas", function($q) use($idEst){
				$q->whereHas("sala", function($q) use($idEst){
					$q->whereHas("unidadEnEstablecimiento", function($q) use($idEst){
						$q->where("establecimiento", $idEst);
					});
				});
			})->orWhereHas("derivaciones", function($q) use($idEst){
				$q->where("establecimiento", $idEst);
			})->orWhereHas("listaEspera", function($q) use($idEst){
				$q->where("")
			});
		});
	}*/

	public function scopeEnEstablecimiento($query, $idEst){
		return $query->where("establecimiento", $idEst);
	}

	public function establecimientoCaso(){
		return $this->belongsTo("App\Models\Establecimiento", "establecimiento", "id");
	}

	public static function ultimoCaso($paciente){

		$datos = DB::table("casos as c")
					->where('c.paciente', $paciente)
					->whereNull('c.fecha_termino')
					->orderBy('c.fecha_termino', 'desc')
					->first();

		return [
			"datos" => $datos
		];
	}

	public static function casoParaEditar($paciente){

		$datos = DB::table("casos as c")
					->where('c.paciente', $paciente)
					->orderBy('c.fecha_termino', 'desc')
					->first();

		return [
			"datos" => $datos
		];
	}

	public static function casoEgresado($idCaso){
		return Caso::whereNotNull('fecha_termino')->where('id',$idCaso)->first();
	}

}

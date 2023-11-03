<?php
use Carbon\Carbon;
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use SolicitudBinding;
use HTML;
use URL;
use Auth;
use App\util\TipoUsuario;
use OwenIt\Auditing\Contracts\Auditable;

class Derivacion extends Model implements Auditable{
	
	use \OwenIt\Auditing\Auditable;

	protected $table = "derivaciones";

	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
	 

	public function documentos(){
		return $this->hasMany('App\Models\Documento', 'derivacion', 'id');
	}

	public function casoOrigen(){
		return $this->belongsTo("App\Models\Caso", "caso", "id");
	}

	public function usuarioSolicitante(){

		return $this->belongsTo("App\Models\Usuario", "usuario", "id");
	}

	public function unidadDestino(){
		return $this->belongsTo("App\Models\UnidadEnEstablecimiento", "destino", "id");
	}

	public function mensajes(){
		return $this->hasMany("App\Models\MensajeDerivacion", "derivacion", "id");
	}

	public static function getDocumentos($id){
		return (new Derivacion())
		->find($id)
		->documentos()
		->get();
	}

	public static function tablaDerivacionesAbiertas(){
		return DB::table("ultimas_derivaciones as d");
	}

	public static function tablaDerivaciones(){
		return DB::table("derivaciones as d");
	}
	public static function tablaDerivacionesCerradas(){
		return DB::table("ultimas_derivaciones_cerradas as d");
	}

	public static function joinDerivaciones(){
		return self::tablaDerivaciones()
		->join('casos as c', 'c.id', '=', 'd.caso')
		->join("pacientes as p", "p.id", "=", "c.paciente")
		->join("ultimos_estados_pacientes as uep", "uep.caso", "=", "c.id")
		->join("unidades_en_establecimientos as ue", "ue.id", "=", "d.destino")
		->join("establecimientos as est_destino", "est_destino.id", "=", "ue.establecimiento")
		//->join("unidades as u", "u.id", "=", "ue.unidad")
		->join("usuarios as us", "us.id", "=", "d.usuario")
		->join("establecimientos as est_origen", "est_origen.id", "=", "us.establecimiento");
	}

	public static function derivaciones($motivo = null){
		$query =  self::with(['casoOrigen' => function($q){
			
		}]);

		if($motivo == "en curso") return $query->where(function($q) use ($motivo) {
			$q->where("motivo_cierre", "=", $motivo)->orWhereNull("motivo_cierre");
		});
		if($motivo != null) return $query->where("motivo_cierre", "=", $motivo);

	}

	public static function selectDerivaciones(){
		return self::joinDerivaciones()
		->select(
			"p.rut",
			"p.dv",

			"uep.riesgo",
			"p.fecha_nacimiento",
			DB::raw("to_char(case when d.fecha_cierre is null then now() - d.fecha ELSE d.fecha_cierre - d.fecha END , 'FMdd \"días\", FMHH24 \"horas\"') AS tiempo_espera"),
			DB::raw("case when d.fecha_cierre is null then 'en curso' else d.motivo_cierre END as estado"),
			"est_destino.nombre as nombre_establecimiento",
			"ue.alias as nombre_unidad",
			"d.id as id_derivacion",
			"d.fecha as fecha_derivacion",
			"us.rut as rut_solicitante",
			"us.dv as dv_solicitante",
			"d.fecha_cierre as fecha_cierre",
			"d.motivo_cierre as motivo_cierre",
			"d.destino as destino",
			"est_origen.nombre as nombre_establecimiento_origen",
			"est_origen.id as id_origen"
		);
	}

	public static function getDerivacionesEnviadas($idEstablecimiento, $motivo = null){
		$query=self::derivaciones($motivo);
		/*if($query === null){
			return new \Illuminate\Database\Eloquent\Collection();
		}*/
		$query->where("establecimiento", $idEstablecimiento);

		if($motivo == "en curso") return $query->where(function($q) use ($motivo) {
			$q->where("motivo_cierre", "=", $motivo)->orWhereNull("motivo_cierre");
		})->get();
		if($motivo != null) return $query->where("motivo_cierre", "=", $motivo)->get();
		return $query->get();
	}

	public static function getDerivacionesRecibidas($idEstablecimiento, $motivo = null){
		$query=self::derivaciones($motivo);

		//$paciente = Paciente::where("rut", "=", "7751")->first();

		//$caso = Caso::where("paciente", "=", $paciente->id)->whereNull("fecha_termino")->first();

		$query->whereHas("unidadDestino", function($q) use ($idEstablecimiento){
			if($idEstablecimiento != null) $q->where("establecimiento", $idEstablecimiento);
		});
		if($motivo == "en curso") return $query->where(function($q) use ($motivo) {
			$q->where("motivo_cierre", "=", $motivo)->orWhereNull("motivo_cierre");
		})->get();
		if($motivo != null) return $query->where("motivo_cierre", "=", $motivo)->get();
		return $query->get();
	}

	public static function tieneCasoPendiente($idEstablecimiento, $rut, $motivo = null){
		$derivaciones = self::getDerivacionesRecibidas($idEstablecimiento, $motivo);
		foreach($derivaciones as $derivacion){
			$paciente = $derivacion->casoOrigen->pacienteCaso;
			if($paciente->rut == $rut) return true;
		}
		return false;
	}

	public static function getTotalDerivacionesRecibidas($id){
		return DB::table("derivaciones as d")
		->join("unidades_en_establecimientos as ue", "d.destino", "=", "ue.id")
		->select(DB::raw("d.*"))
		->where("ue.establecimiento", "=", $id);
	}

	public static function tieneDerivaciones($idPaciente){
		$derivacion=DB::table("derivaciones as d")
		->join("casos as c", "d.caso", "=", "c.id")
		->where("c.paciente", "=", $idPaciente)
		->where("d.fecha_cierre", "=", null)->first();
		if(!is_null($derivacion)) return true;
		return false;
	}

	public static function tieneTrasladoExtraSistema($idPaciente){
		$derivacion=DB::table("derivaciones_extrasistema as d")
		->join("casos as c", "d.caso", "=", "c.id")
		->where("c.paciente", "=", $idPaciente)
			->where("d.fecha_rescate", null)->first();
		if(!is_null($derivacion)) return $derivacion;
		return false;
	}

	public static function getSolicitudId($id){
		$binding=null;
		/*$solicitud=DB::table("ultimas_derivaciones as d")
		->join("casos as c", "d.caso", "=", "c.id")
		->join("pacientes as p", "c.paciente", "=", "p.id")
		->join("evolucion_casos as e", "c.id", "=", "e.caso")
		->join("unidades_en_establecimientos as u", "u.id", "=", "d.destino")
		->join("establecimientos as estab", "estab.id", "=", "u.establecimiento")
		->join("mensajes_derivaciones as m", "m.derivacion", "=", "d.id")
		->select("p.rut", "p.dv", "p.fecha_nacimiento", "p.nombre as nombreP", "p.apellido_paterno", "p.sexo", "p.apellido_materno",
			"e.riesgo", "c.diagnostico", "c.id as id_caso","d.fecha", "estab.nombre as nombreEstab", "d.destino", "m.asunto")
		->where("d.id", "=", $id)->first();*/

		$solicitud = Derivacion::with(["casoOrigen" => function($q){
			$q->with("pacienteCaso")->with(["historialEvolucion" => function($q){
				$q->orderBy("fecha", "desc")->limit(1);
			}]);
		}])->with("unidadDestino.establecimientos")->with("establecimientoOrigen")
			->with(["mensajes" => function($q){
				$q->orderBy("fecha", "asc")->limit(1);
			}])->find($id);


		if(!is_null($solicitud)){
			$binding=new SolicitudBinding;

			$binding->id=$id;
			$binding->idEstablecimiento=$solicitud->unidadDestino->establecimientos->id;
			$binding->rut=$solicitud->casoOrigen->pacienteCaso->rut;
			$binding->dv=$solicitud->casoOrigen->pacienteCaso->dv;
			$binding->fechaNac=date("d-m-Y", strtotime($solicitud->casoOrigen->pacienteCaso->fecha_nacimiento));
			$binding->nombre=$solicitud->casoOrigen->pacienteCaso->nombre;//nombreP;
			$binding->sexo=$solicitud->casoOrigen->pacienteCaso->sexo;
			$binding->apellidoP=$solicitud->casoOrigen->pacienteCaso->apellido_paterno;
			$binding->apellidoM=$solicitud->casoOrigen->pacienteCaso->apellido_materno;
			$binding->diagnostico=$solicitud->casoOrigen->diagnostico;
			$binding->fechaSolicitud=date("d-m-Y", strtotime($solicitud->fecha));
			if(!$solicitud->casoOrigen->historialEvolucion->isEmpty())
				$binding->riesgo=$solicitud->casoOrigen->historialEvolucion[0]->riesgo;
			else
				$binding->riesgo = '';
			$binding->estabOrigen=$solicitud->establecimientoOrigen->nombre;//nombreEstab;
			if(!$solicitud->mensajes->isEmpty())
				$binding->asunto=$solicitud->mensajes[0]->asunto;
			else
				$binding->asunto = "";
			$binding->caso=$solicitud->casoOrigen->id;
		}
		return $binding;
	}

	public static function obtenerArchivosDerivaciones($id){
		$datos=DB::table("documentos_derivaciones as d")->where("derivacion", "=", $id)->get();
		$response=[];
		foreach($datos as $dato){
			$archivo=HTML::link("/trasladar/descargar/$dato->id", basename($dato->recurso));
			$url = URL::to('/');
			$archivo="<a href='$url/trasladar/descargar/$dato->id'>".basename($dato->recurso)."</a>";
			$response[]=[$archivo];
		}
		return $response;
	}

public function getMensajesDerivacion(){

	/*
		//arregla tabla mensaje derivaciones

		$EstablecimientoMensaje=DB::table( DB::raw(
             "(select m.id,e.nombre from mensajes_derivaciones as m,derivaciones as d,unidades_en_establecimientos as u, establecimientos as e where
				m.derivacion=d.id and u.id=d.destino and e.id=u.establecimiento) as re"
         ))->get();

		foreach ($EstablecimientoMensaje as $EstablecimientoD) {

			$mensaje2=MensajeDerivacion::find($EstablecimientoD->id);
			$mensaje2->destino=$EstablecimientoD->nombre;
			$mensaje2->save();

		}
		*/

		$response=array();
		$mensajesOrigen = $this->mensajes()->with("usuarioEmisor.establecimientoUsuario")->orderBy("fecha", "asc")->get();

		foreach($mensajesOrigen as $mensaje){
			$fecha=$mensaje->fecha;//date("d-m-Y H:i:s", strtotime($mensaje->fecha));

			if($mensaje->usuarioEmisor){


			if($mensaje->usuarioEmisor->establecimientoUsuario === null){
				$establecimiento = "{$mensaje->usuarioEmisor->nombres} {$mensaje->usuarioEmisor->apellido_paterno} {$mensaje->usuarioEmisor->apellido_materno} - ".TipoUsuario::getNombre($mensaje->usuarioEmisor->tipo);
			}
			else{
				$establecimiento = $mensaje->usuarioEmisor->establecimientoUsuario->nombre;
			}

			}

			$MiID=$mensaje->id;

			$EstableciMensaje=DB::table( DB::raw("(select m.destino from mensajes_derivaciones as m where m.id=$MiID) as ra"))->get();

        	 foreach ($EstableciMensaje as $Messages) {
        	 		$establecimi=$Messages->destino;


        	 	}

        	 	if($establecimi == "null"){
        	 			$establecimi = "";
        	 		}

			$response[]=array($fecha, $establecimiento,$mensaje->contenido,$establecimi,$mensaje->id);
		}
		return $response;
	}

	public static function ordernarMensajesPorFecha($mensajeA, $mensajeB){
		return $mensajeA[3] > $mensajeB[3];
	}

	public function enviarMensaje($contenido, $asunto){
	    $idEstabRemitente = Auth::user()->id;
	    $m = MensajeDerivacion::nuevoEnvio($this, $idEstabRemitente)->enviarMensaje($contenido, $asunto);
	    return $m;
	}

	public function cerrar($motivo, $comentario = null, \Carbon\Carbon $now = null){
		if($now === null){
			$now = \Carbon\Carbon::now();
		}
		$this->fecha_cierre = "{$now}";
		$this->motivo_cierre = $motivo;
		$this->comentario = $comentario;
		$this->save();
		return $this;
	}

	public static function getDerivacionMayorUnaHora(){
		$total=0;
		$idEstablecimiento=Session::get("idEstablecimiento");
		$derivaciones=Derivacion::selectDerivaciones()
		->where("ue.establecimiento", "=", $idEstablecimiento)
		->where("motivo_cierre", "!=", "rechazado")
		->where("motivo_cierre", "!=", "cancelado")
		->get();
		foreach($derivaciones as $derivacion){
			$hora=date("H", strtotime($derivacion->fecha_derivacion));
			if($hora >= 1) $total++;
		}
		return $total;
	}

	public function establecimiento(){
		/*if($this->fecha_cierre) {
			return $this->establecimientoDestino();
		}
		else{
			return $this->establecimientoOrigen();
		}*/

	}

	public function establecimientoDestino(){
		return Establecimiento::whereHas("unidades", function ($unidad){
			$unidad->where("id", $this->destino);
		})->get();
	}

	public function establecimientoOrigen(){
		/*return Establecimiento::find($this->establecimiento);*/
		return $this->belongsTo("App\Models\Establecimiento", "establecimiento", "id");
	}

	public static function getEstados($excepto = null){
		$response=array();
		$dietas=DB::table("pg_enum as e")
			->join("pg_type as t", "e.enumtypid", "=", "t.oid")
			->select("e.enumlabel")
			->where("t.typname", "=", "motivo_cierre_derivacion");
		if($excepto){
			$dietas->where("e.enumlabel", "<>", $excepto);
		}
		$dietas = $dietas->get();
		foreach ($dietas as $dieta) {
			$response[$dieta->enumlabel]=ucwords($dieta->enumlabel);
		}
		return $response;
	}

	public static function obtenerMotivosCierres(){
		$response=array();
		$motivos=DB::table("pg_enum as e")
			->join("pg_type as t", "e.enumtypid", "=", "t.oid")
			->select("e.enumlabel")
			->where("t.typname", "=", "motivo_cierre_derivacion")->get();
		$id=1;
		$motivos=["En Curso", "Aceptado, Pendiente De Cama", "Aceptado", "Rechazado", "Cancelado"];
		foreach ($motivos as $motivo) {
			$active=($id == 1) ? "active" : "";
			$response[]=["nombre" => ucwords($motivo), "id" => "id-motivo-$id", "active" => $active];
			$id++;
		}
		return $response;
	}


}

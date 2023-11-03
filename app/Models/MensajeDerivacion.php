<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class MensajeDerivacion extends Model{
	protected $table = "mensajes_derivaciones";

	public function usuarioEmisor(){
		return $this->belongsTo("App\Models\Usuario", "usuario", "id");
	}

	public static function joinMensajes(){
		return Derivacion::joinDerivaciones()
		->join("mensajes_derivaciones as md", "d.id", "=", "md.derivacion")
		->join("usuarios as us_remitente", "us_remitente.id", "=", "md.usuario")
		->join("establecimientos as est_remitente", "us_remitente.establecimiento", "=", "est_remitente.id");
	}

	public static function mensajesDerivaciones(){
		return self::joinMensajes()
		->select(
			"d.id as id_derivacion",
			"md.id as id_mensaje",
			"est_destino.nombre as estab_destino",
			"us.rut as rut_solicitante",
			"us.dv as dv_solicitante",
			"p.rut as rut_paciente",
			"p.dv as dv_paciente",
			"c.diagnostico",
			"uep.riesgo",
			"est_remitente.nombre as est_remitente",
			"us_remitente.rut as rut_remitente",
			"us_remitente.dv as dv_remitente",
			"md.fecha",
			"md.asunto"
		);
	}
	public static function detallesDerivaciones(){
		return self::mensajesDerivaciones()
		->addSelect("md.contenido");
	}
	public static function porCaso($idCaso){
		return self::detallesDerivaciones()
		->where("c.id", "=", $idCaso)
		->get();
	}
	public static function recibidosPorEstablecimiento($idEstab){
		return self::mensajesDerivaciones()
		->where("est_destino.id", "=", $idEstab)
		->get();
	}
	public static function porDerivacion($idDerivacion){
		return self::detallesDerivaciones()
		->where("d.id", "=", $idDerivacion)
		->get();
	}

	public function enviarMensaje($contenido, $asunto){
		if ( empty($this->usuario) ){
			throw new Exception('No se ha especificado el remitente');
		}
		$this->contenido = $contenido;
		$this->asunto = $asunto;
		$this->fecha = DB::raw("date_trunc( 'second', now() )");
		$this->save();
		return $this;		
	}

	public static function nuevoEnvio($derivacion, $idUsuario = null){
		$nuevo = new MensajeDerivacion;
		$nuevo->derivacion = $derivacion->id;
		if (is_null($idUsuario)) $idUsuario = $derivacion->usuario;
		$nuevo->usuario = $idUsuario;
		
		$DerivaDest=$derivacion->id;

		$EstableciMensaje=DB::table( DB::raw("(select e.nombre from derivaciones as d,unidades_en_establecimientos as u, establecimientos as e where d.id=$DerivaDest
		and u.establecimiento=e.id and u.id=d.destino) as ra"))->get();	

        	foreach ($EstableciMensaje as $Messages) 
        	{
        	 	$establecimi=$Messages->nombre;
        	}

        $nuevo->destino = $establecimi;
		return $nuevo;
	}
	

}


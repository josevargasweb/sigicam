<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HistorialEliminacion extends Model implements Auditable{
	use \OwenIt\Auditing\Auditable;
	
	protected $table = "historial_eliminacion_camas";
	protected $criterio = "cama";

	protected $auditInclude = [
    
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;


	public function camas(){
		return $this->belongsTo("App\Models\Cama", "cama", "id");
	}

	public static function boot(){
		parent::boot();

		self::saving(function($ev){
			/* @var $ev EvolucionCaso
			 * @var $reciente EvolucionCaso
			 */
			try {
				$cama = HistorialOcupacion::where("cama", $ev->cama)->orderBy("fecha", "desc")->firstOrFail();
				if($cama->fecha_liberacion === null){
					throw new MensajeException("La cama aún está ocupada.");
				}
			}catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
			}

			$cama = Reserva::reservasVigentes()->where("cama", $ev->cama)->get();
			if(!$cama->isEmpty()){
				throw new MensajeException("La cama está reservada.");
			}

			return true;


		});
	}

}

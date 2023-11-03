<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class DerivacionesExtrasistema extends Model{
	
	protected $table = "derivaciones_extrasistema";
    protected $fillable = ['establecimiento_extrasistema','fecha','fecha_rescate','servicio','usuario'];
    public $timestamps = false;
    protected $appends = ['opciones'];

	public function establecimiento(){
		return Establecimiento::whereHas("usuarios", function($q){
			$q->where("usuario", $this->usuario);
		})->get();
	}

	public function caso(){
		return $this->belongsTo('App\Models\Caso');

	}

	public function usuario(){
		return $this->belongsTo('App\Models\Usuario');

	}


	public function servicio(){
		return $this->belongsTo('App\Models\Unidad');

	}


	public function establecimiento_extrasistemas(){
		return $this->belongsTo('App\Models\EstablecimientosExtraSistema');

	}



















}


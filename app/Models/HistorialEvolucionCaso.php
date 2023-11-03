<?php
namespace App\Models;

class HistorialEvolucionCaso extends Historial{
	protected $table = "evolucion_casos";
	protected $criterio = "caso";

	public function casox(){
		return $this->belongsTo("App\Models\Caso", "caso", "id");
	}
}
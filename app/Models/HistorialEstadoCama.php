<?php

class HistorialEstadoCama extends Historial{
	protected $table = "historial_estados_camas";
	protected $criterio = "cama";

	public function camas(){
		return $this->hasMany("Cama", "cama", "id");
	}
}
<?php

class HistorialCamaSala extends Historial{
	
	protected $table = "historial_camas_en_salas";
	protected $criterio = "cama";

	public function camas(){
		return $this->hasMany("Cama", "id", "cama");
	}
	public function salas(){
		return $this->hasMany("Sala", "id", "sala");
	}
	
	
}
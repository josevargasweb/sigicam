<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
class Documento extends Model{
	protected $table = "documentos_derivaciones";
	
	public function derivacion(){
		return $this->belongsTo('App\Models\Derivacion', 'derivacion', 'id');
	}

	public static function getDocumentos($idDerivacion){
		return self::where("derivacion", "=", $idDerivacion)->get();
	}

	public static function getRuta($idDocumento){
		return self::find($idDocumento)->recurso;
	}
}


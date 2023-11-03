<?php


namespace App\models;
use Illuminate\Database\Eloquent\Model;
use Log;

class HistorialCamasUnidades extends Model{

    public static function boot(){
        parent::boot();

        self::creating(function($new){
            try{
                $reciente = self::where("cama", $new->cama)
                    ->where("fecha", "<", $new->fecha)
                    ->orderBy("fecha", "desc")->firstOrFail();
            }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                /* Si no existe, se guarda la categoría */
                return true;
            }
            if($new->unidad === $reciente->unidad){
                return false;
            }
            if($new->unidad === null){
                try {
                    $new->unidad = Cama::findOrFail($new->cama)->salaDeCama()->firstOrFail()->unidadEnEstablecimiento()->firstOrFail()->id;
                }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                    return false;
                }
            }
        });
    }

	protected $table = "t_historial_camas_en_unidades";

	public function camas(){
		return $this->belongsTo("App\Models\Cama", "cama", "id");
	}

	public function unidades() {
		return $this->belongsTo("App\Models\UnidadEnEstablecimiento", "unidad", "id");
	}

	public function scopesReconversiones($query, $fecha = null){
		if (is_null($fecha)) {
			$fecha = \Carbon\Carbon::now();
		}
		DB::statement("DROP TABLE IF EXISTS temp_historial_camas_en_unidades");
		DB::statement("CREATE TEMP TABLE temp_historial_camas_en_unidades AS (SELECT DISTINCT ON (cama) * FROM t_historial_camas_en_unidades WHERE fecha <= ? ORDER BY cama, fecha DESC)", ["{$fecha}"]);
		return $query->join("temp_historial_camas_en_unidades AS tmp", "tmp.id", "=", "t_historial_camas_en_unidades.id")
			->join("camas as cm", "cm.id", "=", "t_historial_camas_en_unidades.cama")
			->join("salas as s", function($j){
				$j->on("s.id", "=", "cm.sala")
				->on("s.establecimiento", "<>", "tmp.unidad");
			});
	}



}

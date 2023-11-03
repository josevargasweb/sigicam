<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Carbon\Carbon;
use Auth;


class IndicacionMedica extends Model{

    protected $table = "indicaciones_medicas";
	public $timestamps = false;
	protected $primaryKey = 'id';
	protected $guarded = [];

	//relacion uno a muchos.
	public function tipos_reposo(){
		return $this->hasMany('App\Models\TiposReposoIndicacionMedica','im_id');
	}

	public function farmacos(){
		return $this->hasMany('App\Models\FarmacosIndicacionMedica','im_id');
	}

	public function comentarios(){
		return $this->hasMany('App\Models\ComentariosIndicacionMedica','im_id');
	}

	public function usuario_ingreso(){
		return $this->belongsTo('App\Models\Usuario','usuario_ingresa','id');
	}

	public static function reposo($indicacion, $indice){
		switch ($indice) {
			case 1:
				$tipo_reposo = "Absoluto";
				break;
			case 2:
				$info_indicacion = self::findOrFail($indicacion);
				$grados = ($info_indicacion["grados_semisentado"]) ? $info_indicacion["grados_semisentado"] : 0;
				$tipo_reposo = "Semisentado - Grados semisentado: {$grados}";
				break;
			case 3:
				$tipo_reposo = "Relativo";
				break;
			case 4:
				$tipo_reposo = "Relativo asistido";
				break;
			case 5:
				$info_indicacion = self::findOrFail($indicacion);
				$comentario = ($info_indicacion["otro_reposo"]) ? $info_indicacion["otro_reposo"] : 'Sin información';
				$tipo_reposo = "Otro: {$comentario}";
				break;
			default:
				$tipo_reposo = "Sin información";
				break;
		}
		return $tipo_reposo;
	}

	public static function regimen($indicacion, $indice){
		//tipo via
		switch ($indice) {
			case 1:
				$tipo_reposo = "Oral";
				break;
			case 2:
				$tipo_reposo = "SNY";
				break;
			case 3:
				$tipo_reposo = "SNG";
				break;
			case 4:
				$tipo_reposo = "Parental";
				break;
			case 5:
				$info_indicacion = self::findOrFail($indicacion);
				$comentario = ($info_indicacion["detalle_via"]) ? $info_indicacion["detalle_via"] : 'Sin información';
				$tipo_reposo = "Otro: {$comentario}";
				break;
			default:
				$tipo_reposo = "Sin información";
				break;
		}
		return $tipo_reposo;
	}

	public static function falso($falsear,$fecha){
		$falsear->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Editado'
		]);
		return $falsear;
	}

	public static function nuevo($data_indicacion,$falsear,$fecha){
		$nueva = new IndicacionMedica;
		$nueva->caso = $data_indicacion["caso"];
		$nueva->usuario_ingresa = Auth::user()->id;
		$nueva->fecha_indicacion_medica = $fecha;
		$nueva->fecha_creacion = $falsear->fecha_creacion; //fecha real de la indicacion
		$nueva->tipo_reposo = $data_indicacion["tipo_reposo"];
		$nueva->grados_semisentado = ($data_indicacion["grados_semisentado"]) ? $data_indicacion["grados_semisentado"] : null;
		$nueva->otro_reposo = ($data_indicacion["otro_reposo"]) ? $data_indicacion["otro_reposo"] : null;
		$nueva->tipo_via = ($data_indicacion["tipo_via"]) ? $data_indicacion["tipo_via"] : null;
		$nueva->detalle_via = ($data_indicacion["detalle_via"]) ? $data_indicacion["detalle_via"] : null;
		$nueva->tipo_consistencia = ($data_indicacion["tipo_consistencia"]) ? $data_indicacion["tipo_consistencia"] : null;
		$nueva->detalle_consistencia = ($data_indicacion["detalle_consistencia"]) ? $data_indicacion["detalle_consistencia"] : null;
		$nueva->volumen = ($data_indicacion["volumen"]) ? $data_indicacion["volumen"] : null;
		$nueva->horas_signos_vitales = ($data_indicacion["horas_signos_vitales"]) ? $data_indicacion["horas_signos_vitales"] : null;
		$nueva->detalle_signos_vitales = ($data_indicacion["detalle_signos_vitales"]) ? $data_indicacion["detalle_signos_vitales"] : null;
		$nueva->horas_hemoglucotest = ($data_indicacion["horas_hemoglucotest"]) ? $data_indicacion["horas_hemoglucotest"] : null;
		$nueva->detalle_hemoglucotest = ($data_indicacion["detalle_hemoglucotest"]) ? $data_indicacion["detalle_hemoglucotest"] : null;
		$nueva->oxigeno = ($data_indicacion["oxigeno"]) ? $data_indicacion["oxigeno"] : null;
		$nueva->sueros = ($data_indicacion["sueros"] == "si") ? true : false;
		$nueva->suero = ($data_indicacion["suero"]) ? $data_indicacion["suero"] : null;
		$nueva->mililitro = ($data_indicacion["mililitro"]) ? $data_indicacion["mililitro"] : null;
		$nueva->atencion_terapeutica = $data_indicacion["atencion_terapeutica"];
		$nueva->visible = true;
		$nueva->fecha_emision = carbon::parse($data_indicacion["fecha_emision"])->format('Y-m-d H:i:s');
		$nueva->fecha_vigencia = carbon::parse($data_indicacion["fecha_vigencia"])->format('Y-m-d H:i:s');
		$nueva->padua = $data_indicacion["padua"];
		$nueva->caprini = $data_indicacion["caprini"];
		$nueva->save();
		return $nueva;
	}

	public static function eliminar($eliminar,$fecha){
		$eliminar->update([
			'usuario_modifica' => Auth::user()->id,
			'fecha_modificacion' => $fecha,
			'visible' => false,
			'estado' => 'Eliminado'
		]);
		return $eliminar;
	}
}

?>
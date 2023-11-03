<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use View;
use Auth;
use TipoUsuario;
use Log;
use HistorialOcupacion;
use OwenIt\Auditing\Contracts\Auditable;

class THistorialOcupaciones extends Model implements Auditable{
	use \OwenIt\Auditing\Auditable;
	
	protected $table = "t_historial_ocupaciones";
	protected $primaryKey = "id";

	protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;
 
	
	public static function casoHospitalizado($idCaso){
		return  DB::select(DB::Raw("select * from t_historial_ocupaciones 
		where caso = $idCaso
		and fecha_ingreso_real is not null
		and fecha_liberacion is null
		and (motivo is null 
		or motivo not in('corrección cama','traslado interno')) limit 1"));
	}


}
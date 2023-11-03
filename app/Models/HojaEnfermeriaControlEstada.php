<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaControlEstada extends Model
{
    public $table = "formulario_hoja_enfermeria_control_estada";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;

    public static function infoSelectTipoProcedecimiento($tipoP){
		$tipo_sng = ['8','10','12','14','16','18','20','22','24','26','28','30'];
		$tipo_cup = ['8','10','12','14','16','18','20'];
		$tipo_sny = ['6','8','10','12'];
		$tipo_vpp = ['14','16','18','20','22','24','26'];
		$data = [];
		switch ($tipoP) {
			case '1':
				$data = $tipo_sng; 
				break;
			case '2':
				$data = $tipo_cup;
				break;
			case '3':
				$data = $tipo_sny;
				break;
			case '4':
				$data = $tipo_vpp;
				break;
			default:
				$data;
				break;
		}
		return $data;
	}

    public static function tipoProcedimiento($tipoP){
        $data = "";
        switch ($tipoP) {
			case '1':
				$data = "SNG"; 
				break;
			case '2':
				$data = "CUP";
				break;
			case '3':
				$data = "SNY";
				break;
			case '4':
				$data = "VPP";
				break;
            case '5':
                $data = "Catéter Venoso Central";
                break;
            case '6':
                $data = "Catéter arterial";
                break;
			default:
				$data;
				break;
		}
		return $data;
    }

}

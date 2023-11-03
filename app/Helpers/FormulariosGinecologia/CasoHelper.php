<?php
namespace App\Helpers\FormulariosGinecologia;

use Exception;
use DB;
use Log;
use App\Helpers\Helper;
use App\Models\HistorialSubcategoriaUnidad;

class CasoHelper extends Helper { 

    function getCasoById($caso_id){
        try {

            $q = "select c.ficha_clinica as ficha_clinica, p.rut as run, p.dv as dv, c.id as caso_id, p.id as paciente_id, p.fecha_nacimiento as fecha_nacimiento, p.nombre as paciente_nombre, p.apellido_paterno as paciente_apellido_paterno, p.apellido_materno as paciente_apellido_materno, uee.alias as unidad, ca.id_cama as cama, s.nombre as sala
            from
            casos as c
             inner join pacientes as p on p.id = c.paciente
             inner join t_historial_ocupaciones as t on c.id=t.caso
             inner join camas as ca on ca.id = t.cama
             inner join salas as s on ca.sala = s.id
             inner join unidades_en_establecimientos AS uee on s.establecimiento = uee.id
             inner join area_funcional AS af on uee.id_area_funcional = af.id_area_funcional
            where
            c.id = ? and
            t.motivo is null
            limit 1";
            
            $caso = DB::select($q, [$caso_id]);
            
            if(empty($caso)){$caso = null;}
            return $caso;   
        }
        catch (Exception $e){
            Log::error($e);
            return null;
        }

    }
    
    public function validarUnidad($idCaso)
    {
    	$ubicacion = DB::table('t_historial_ocupaciones as t')
    	->join("camas as c", "c.id", "=", "t.cama")
    	->join("salas as s", "c.sala", "=", "s.id")
    	->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
    	->join("area_funcional AS af", "uee.id_area_funcional", "=", "af.id_area_funcional")
    	->where("caso", $idCaso)
    	->whereNull("t.motivo")
    	->select("uee.id")
    	->first();

        $subcategoria = HistorialSubcategoriaUnidad::select("id_subcategoria")->where('id_unidad',$ubicacion->id)->where('visible',true)->first();
    	
    	return ($subcategoria) ? $subcategoria->id_subcategoria : null;
    }

}
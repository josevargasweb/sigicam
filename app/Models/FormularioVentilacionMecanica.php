<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class FormularioVentilacionMecanica extends Model
{
    protected $primaryKey = 'id_formulario_ventilacion_mecanica';
    protected $table = "formulario_ventilacion_mecanica";
    public $timestamps = false;
	
	public function guardar($request){

		$this->caso = $request->caso_vm;
		$this->id_paciente = $request->id_paciente_vm;
		$this->fecha_toma = $this->fechas($request->fecha_vm)." ".$request->hora_vm;
		$this->intubacion = $request->intubacion_vm;
		$this->modalidad = $request->modalidad_vm;
		$this->fio2 = $request->fio2_vm;
		$this->f_programada = $request->f_programada_vm;
		$this->f_real = $request->f_real_vm;
		$this->v_c_programado = $request->vc_programado_vm;
		$this->v_c_real = $request->vc_real_vm;
		$this->v_minuto = $request->v_minuto_vm;
		$this->pr_via_aerea = $request->pr_via_aerea_vm;
		$this->peep = $request->peep_vm;
		$this->pr_soporte = $request->pr_soporte_vm;
		$this->sensibilidad = $request->sensibilidad_vm;
		$this->bipap = $request->bipap_vm;
		$this->ipap = $request->ipap_vm;
		$this->epap = $request->epap_vm;
		$this->flujo_o2 = $request->flujo_o2_vm;
		$this->ton_tet = $request->ton_tet_nro_vm;
		$this->canula_tqt = $request->canula_tqt_n_vm;
		$this->fijación_tubo = $request->fijacion_tubo_cm_vm;
		$this->dias_vm_vmni = $request->dias_vm_vmni_vm;
		$this->cambio_filtro = $this->fechas($request->cambio_filtro_vm);
		$this->cambio_set_vm = $this->fechas($request->cambio_set_vm);
		$this->usuario_responsable = Auth::user()->id;
		
		$this->save();
	}
	public function listar($request){
		
		return DB::select("SELECT 
			fvm.*,
			TO_CHAR(fvm.fecha_toma,'DD-MM-YYYY HH24:MI') AS fecha_toma,
			TO_CHAR(fvm.fecha_toma,'DD-MM-YYYY') AS cambio_filtro,
			TO_CHAR(fvm.fecha_toma,'DD-MM-YYYY') AS cambio_set_vm,
			u.nombres || ' ' || u.apellido_paterno || ' ' || u.apellido_materno AS nombre_usuario
			FROM formulario_ventilacion_mecanica fvm 
			INNER JOIN usuarios u ON u.id = fvm.usuario_responsable
			WHERE fvm.caso = ? AND fvm.fecha_toma::date >= ? AND fvm.fecha_toma::date <= ?
			AND fvm.visible IS TRUE",[
			$request->caso,
			$this->fechas($request->desde_vm),
			$this->fechas($request->hasta_vm)
		]);
	}
	private function fechas($f){
		if(strlen($f) != 10)
		{
			return null;
		}
		$ff = explode("-",$f);

		if(count($ff) != 3){
			return null;
		}
		return $ff[2]."-".$ff[1]."-".$ff[0];
	}
	public function eliminar($request){
		$fvm = FormularioVentilacionMecanica::find($request->id);

		$fvm->fecha_eliminacion = date("Y-m-d H:i:s");
		$fvm->attributes["visible"] = false;
		$fvm->save();
	}

}

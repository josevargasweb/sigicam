<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Illuminate\Support\Str;

class ListaPabellon extends Model{
    
    protected $table = "lista_pabellon";
    public $timestamps = false;
    protected $primaryKey = 'id_pabellon';

    public static function infoPacienteEnPabellon(){
        return DB::table("lista_pabellon as l")
            ->join("casos as c","c.id","=","l.id_caso")
            ->join("pacientes as p","p.id","=","c.paciente")
            ->join("usuarios as u","l.id_usuario_solicito_pabellon","=","u.id")
            ->where("c.establecimiento",Auth::user()->establecimiento)
            ->whereNull("l.fecha_salida")
            ->whereNull("l.id_usuario_saco_pabellon")
            ->select(
                "l.id_pabellon as idLista",
                "c.id as idCaso",
                "p.nombre as nombre",
                "p.apellido_paterno as apellidoP",
                "p.apellido_materno as apellidoM",
                "p.rut as rut",
                "p.dv as dv",
                "l.fecha_ingreso as fechaIngreso",
                "c.id_unidad",
                "l.comentario",
                "l.id_usuario_solicito_pabellon")
                ->orderBy("l.fecha_ingreso", "desc")
                ->get();
    }

    public static function generarDataExportar(){

        $fecha = date("d-m-Y");
		$idEstablecimiento = Auth::user()->establecimiento;
		$nombreEstablecimiento = Establecimiento::select('nombre')->where('id', $idEstablecimiento)->first();

		$unidades = DB::table('unidades_en_establecimientos as uee')
		->join("area_funcional as af", "uee.id_area_funcional", "=", "af.id_area_funcional")
        ->select("uee.alias","uee.url","uee.id","af.nombre")
        ->where("uee.visible",true)
		->where("uee.establecimiento",Auth::user()->establecimiento)->get();
		
		$pabellon = ListaPabellon::infoPacienteEnPabellon();

        foreach($pabellon as $d){
            $apellido=$d->apellidoP." ".$d->apellidoM;
            $nombre_completo = $d->nombre." ".$apellido;
            $dv=($d->dv == 10) ? 'K' : $d->dv;
            $rut = (empty($d->rut)) ? "-" : $d->rut."-".$dv;
            $fecha_ingreso = date("d-m-Y", strtotime($d->fechaIngreso));
            
            $id_medico = $d->id_usuario_solicito_pabellon;
            $medico = Usuario::find($id_medico);
            $nombres_medico = $medico->nombres. " ".$medico->apellido_paterno. " ".$medico->apellido_materno;


            $diag = HistorialDiagnostico::where("caso","=",$d->idCaso)->orderby("fecha","desc")->select("diagnostico", "comentario as co")->first();
            $d->diagnostico = $diag->diagnostico;
            $d->co = $diag->co;
            if($d->co == ""){
            $d->co = " Sin detalle";
            }

            $cama = DB::table('t_historial_ocupaciones as t')
                    ->join("camas as c", "c.id", "=", "t.cama")
                    ->join("salas as s", "c.sala", "=", "s.id")
                    ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
                    ->where("caso", "=", $d->idCaso)
                    ->orderBy("t.created_at", "desc")
                    ->select("uee.alias as nombre_unidad","uee.id as id_servicio", "uee.url")
                    ->take(1)->get();

                    if($cama == '[]'){
                        $cama = null;
                            
                    }else {
                        
                        foreach ($cama as $cam) {
                            $id_servicio = $cam->id_servicio;
                            $servicio = $cam->nombre_unidad;
                        }
                    }

            $response[] = [
                "rut" => $rut, 
                "nombre_completo" => Str::upper($nombre_completo),
                "fecha_ingreso" => $fecha_ingreso,
                "id_servicio" => $id_servicio,
                "unidad" =>$servicio,
                "comentario" => $d->comentario,
                "diagnostico" => $d->diagnostico.". Comentario: ".$d->co,
                "usuario_solicito" => $nombres_medico
            ];
        }

        $datos = [];
        foreach ($unidades as $u) {
			$pacientes=array();
            foreach ($response as $r) {
                if($u->id == $r["id_servicio"]){
					$pacientes[] = $r;
                }
            }
            
            $datos[] = [
                "area"=>$u->alias, 
                "nombre_unidad" => $u->nombre, 
                "pacientes"=>$pacientes, 
                "fecha"=>$fecha, 
                "establecimiento" => $nombreEstablecimiento->nombre
            ];
        }
        
        return $datos;

    }
}

?>
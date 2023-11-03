<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;
use Auth;
use DB;

class InformeProcesoDiagnostico extends Model
{
    protected $table = "informe_proceso_diagnostico_medico";
    public $timestamps = false;
	protected $primaryKey = "id";

    public function usuario_ingreso(){
		return $this->belongsTo("App\Models\Usuario", "usuario_ingresa");
	}

    public function usuario_modifico(){
		return $this->belongsTo("App\Models\Usuario", "usuario_modifica");
	}

    public static function dataHistorialInformeProcesoDiagnostico($caso){
        $response = [];
        $datos = self::where("caso", $caso)
        ->where("visible",true)
        ->get();

        foreach ($datos as $dato){
            // $pdf = "<button class='btn btn-danger' type='button' onclick='pdfInforme(".$dato->id.")'>PDF</button>";
            $editar = "<button class='btn btn-primary' type='button' onclick='editarInforme(".$dato->id.")'>Ver/Editar</button>";
            $eliminar = "<button class='btn btn-danger' type='button' onclick='eliminarInforme(".$dato->id.")'>Eliminar</button>";
        //     $opciones = '<div class="dropdown">
        //     <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        //       Opciones
        //       <span class="caret"></span>
        //     </button>
        //     <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        //       <li><a href="#" onclick="pdfInforme('.$dato->id.')">PDF</a></li>
        //       <li><a href="#" onclick="editarInforme('.$dato->id.')">Editar</a></li>
        //       <li><a href="#" onclick="eliminarInforme('.$dato->id.')">Eliminar</a></li>
        //     </ul>
        //   </div>';
            $opciones = "{$editar}<br>{$eliminar}";
            $fecha = ($dato->fecha_informe) ? Carbon::parse($dato->fecha_informe)->format('d-m-Y H:i:s') : "Sin información";
            $usuario_ingresa = "{$dato->usuario_ingreso->nombres} {$dato->usuario_ingreso->apellido_paterno} {$dato->usuario_ingreso->apellido_materno}";
            $response[] = [$opciones,$usuario_ingresa,$fecha];
        }
        return $response;
    }

    public static function dataInformeProcesoDiagnostico($caso){
        $id_usuario = Auth::user()->id;
        $usuario = Usuario::find($id_usuario);
        $establecimiento = $usuario->establecimientoUsuario->nombre;
        $servicio_salud = $usuario->establecimientoUsuario->snss;
            
        $paciente_caso = Caso::find($caso,'paciente');
        $paciente = Paciente::find($paciente_caso->paciente);
        $nombre_paciente = ($paciente->id) ? "{$paciente->nombre} {$paciente->apellido_paterno} {$paciente->apellido_materno}" : "Sin información.";
        $dv_paciente = ($paciente->dv == 10) ? "K" : $paciente->dv;
        $rut_paciente = ($paciente->id) ? "{$paciente->rut}-{$dv_paciente}" : "-";
        $sexo = $paciente->sexo;
        $fecha_nacimiento = "";
        $edad = "";
        if($paciente->fecha_nacimiento){
            $edad = Carbon::now()->diffInYears($paciente->fecha_nacimiento);
            $fecha_nacimiento = Carbon::parse($paciente->fecha_nacimento)->format('d-m-Y');
        }
        
        $nombre_usuario = ($usuario) ? "{$usuario->nombres} {$usuario->apellido_paterno} {$usuario->apellido_materno}" : "Sin información.";
        $dv_usuario = ($usuario->dv == 10) ? "K" : $usuario->dv;
        $rut_usuario = ($usuario) ? "{$usuario->rut}-{$dv_usuario}" : "-";

        $cama = DB::table('t_historial_ocupaciones as t')
            ->join("camas as c", "c.id", "=", "t.cama")
            ->join("salas as s", "c.sala", "=", "s.id")
            ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
            ->where("caso", "=", $caso)
            ->orderBy("t.created_at", "desc")
            ->select("cama", "c.id_cama as id", "s.nombre as sala_nombre", "uee.alias as nombre_unidad")
            ->first();
        
        if($cama){
            $servicio = $cama->nombre_unidad;				
            $detalleCama = $cama->sala_nombre." (".$cama->id.")";
        }

        $diagnosticos = HistorialDiagnostico::where('caso',$caso)
                        ->select("diagnostico")
                        ->get();

        $infoInforme = [
            "nombre_establecimiento" => $establecimiento,
            "servicio_salud" => $servicio_salud,
            "nombre_unidad" => isset($servicio) && isset($detalleCama) ? "{$servicio} - {$detalleCama}" : "",
            "nombre_paciente" => $nombre_paciente,
            "rut_paciente" => $rut_paciente,
            "sexo_paciente" => $sexo,
            "fecha_nacimiento" => $fecha_nacimiento,
            "edad" => $edad,
            "diagnosticos" => $diagnosticos,
            "nombre_usuario" => $nombre_usuario,
            "rut_usuario" => $rut_usuario
        ];

        return $infoInforme;
    }
}

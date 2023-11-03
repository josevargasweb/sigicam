<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;


class IESegmentado extends Model
{
    protected $table = 'formulario_ie_fisico_segmentado';
	public $timestamps = false;
	protected $primaryKey = 'id';

	public static function historicoSegmentario($caso){
		$response = [];
		$hSegmentario = HojaIngresoEnfermeria::select(
			'formulario_hoja_ingreso_enfermeria.fecha_creacion',
			'formulario_hoja_ingreso_enfermeria.fecha_modificacion',
			// 'formulario_hoja_ingreso_enfermeria.tipo_modificacion',
			'formulario_hoja_ingreso_enfermeria.cabeza',
			'formulario_hoja_ingreso_enfermeria.protesis_dental',
			'formulario_hoja_ingreso_enfermeria.detalle_protesis_dental',
			'formulario_hoja_ingreso_enfermeria.cuello',
			'formulario_hoja_ingreso_enfermeria.torax',
			'formulario_hoja_ingreso_enfermeria.abdomen',
			'formulario_hoja_ingreso_enfermeria.extremidades_superiores',
			'formulario_hoja_ingreso_enfermeria.extremidades_inferiores',
			'formulario_hoja_ingreso_enfermeria.columna_torso',
			'formulario_hoja_ingreso_enfermeria.genitales',
			'formulario_hoja_ingreso_enfermeria.altura_uterina',
			'formulario_hoja_ingreso_enfermeria.piel',
			'formulario_hoja_ingreso_enfermeria.altura_uterina',
			'formulario_hoja_ingreso_enfermeria.tacto_vaginal',
			'formulario_hoja_ingreso_enfermeria.membranas',
			'formulario_hoja_ingreso_enfermeria.liquido_anmiotico',
			'formulario_hoja_ingreso_enfermeria.amnioscopia',
			'formulario_hoja_ingreso_enfermeria.amnioscentesis',
			'formulario_hoja_ingreso_enfermeria.presentacion',
			'formulario_hoja_ingreso_enfermeria.contracciones',
			'formulario_hoja_ingreso_enfermeria.lfc',
			'formulario_hoja_ingreso_enfermeria.vagina',
			'formulario_hoja_ingreso_enfermeria.perine',
			'formulario_hoja_ingreso_enfermeria.tacto_vaginal_eg',
			'u.nombres', 'u.apellido_paterno', 'u.apellido_materno')
			->join("usuarios as u","u.id","formulario_hoja_ingreso_enfermeria.usuario_responsable")
			->where('formulario_hoja_ingreso_enfermeria.caso',$caso)
			->orderBy('formulario_hoja_ingreso_enfermeria.fecha_creacion', 'asc')
			->get();

			foreach ($hSegmentario as $key => $h) {
				$fecha_creacion = ($h->fecha_creacion) ? Carbon::parse($h->fecha_creacion)->format("d-m-Y H:i") : '--';
				$fecha_modificacion = ($h->fecha_modificacion) ? Carbon::parse($h->fecha_modificacion)->format("d-m-Y H:i:s") : '--';
				$cabeza = ($h->cabeza) ? ($h->cabeza) : '--';
				$p_dental = ($h->protesis_dental) ? 'Si' : 'No';
				$detalle_pd = ($h->detalle_protesis_dental) ? ($h->detalle_protesis_dental) : '--';
				$cuello = ($h->cuello) ? ($h->cuello) : '--';
				$torax = ($h->torax) ? ($h->torax) : '--';
				$abdomen = ($h->abdomen) ? ($h->abdomen) : '--';
				$e_superiores = ($h->extremidades_superiores) ? ($h->extremidades_superiores) : '--';
				$e_inferiores = ($h->extremidades_inferiores) ? ($h->extremidades_inferiores) : '--';
				$columna_torso = ($h->columna_torso) ? ($h->columna_torso) : '--';
				$genitales = ($h->genitales) ? ($h->genitales) : '--';
				$piel = ($h->piel) ? ($h->piel) : '--';
				$altura_uterina = ($h->altura_uterina) ? ($h->altura_uterina) : '--';
				$tacto_vaginal = ($h->tacto_vaginal) ? ($h->tacto_vaginal) : '--';
				$membranas = ($h->membranas) ? ($h->membranas) : '--';
				$liquido_anmiotico = ($h->liquido_anmiotico) ? ($h->liquido_anmiotico) : '--';
				$amnioscopia = ($h->amnioscopia) ? ($h->amnioscopia) : '--';
				$amnioscentesis = ($h->amnioscentesis) ? ($h->amnioscentesis) : '--';
				$presentacion = ($h->presentacion) ? ($h->presentacion) : '--';
				$contracciones = ($h->contracciones) ? ($h->contracciones) : '--';
				$lfc = ($h->lfc) ? ($h->lfc) : '--';
				$vagina = ($h->vagina) ? ($h->vagina) : '--';
				$perine = ($h->perine) ? ($h->perine) : '--';
				$tacto_vaginal_eg = ($h->tacto_vaginal_eg) ? ($h->tacto_vaginal_eg) : '--';
				$response [] = [
					$fecha_creacion, $fecha_modificacion,
					"Cabeza: ".$cabeza." <br>
					Protesis Dental: ".$p_dental." <br>
					Detalle Protesis: ".$detalle_pd."",
					"Cuello: ".$cuello." <br>
					Torax: ".$torax." <br>
					Abdomen: ".$abdomen."",
					"Superiores: ".$e_superiores." <br>
					Inferiores: ".$e_inferiores."",
					$columna_torso,
					"Genitales: ".$genitales." <br>
					Piel: ".$piel."",
					"Altura uterina: ".$altura_uterina." <br>
					Tacto vaginal: ".$tacto_vaginal." <br>
					Membranas: ".$membranas."",
					"Liquido amniotico: ".$liquido_anmiotico." <br>
					Amnioscopia: ".$amnioscopia." <br>
					Amniocentesis: ".$amnioscentesis."",
					"Presentación: ".$presentacion." <br>
					Contracciones: ".$contracciones." <br>
					Lfc (latidos cardio fetales): ".$lfc."",
					"Vagina: ".$vagina." <br>
					Periné: ".$perine." <br>
					Tacto vaginal: ".$tacto_vaginal_eg."",
					$h->nombres. " " .$h->apellido_paterno. " " .$h->apellido_materno
				];
			}
		return $response;
	}

    public static function crearNuevo($request, $modificar){

        $segmentado = new IESegmentado;
        $segmentado->caso = $request->idCaso;

        if($modificar != null || $modificar != ""){
			$segmentado->id_anterior = $modificar->id;
		}

        $segmentado->usuario_responsable = Auth::user()->id;
        $segmentado->visible = true;
        $segmentado->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
        $segmentado->cabeza = $request->cabeza;
        $dental = $request->dental;
        if($dental == "si"){
            $segmentado->protesis_dental = true;
			$segmentado->ubicacion_protesis_dental = $request->protesis_ubicacion;
        }else
        if($dental == 'no'){
            $segmentado->protesis_dental = false;
			
        }else{
            $segmentado->protesis_dental = false;
        }

        $segmentado->detalle_protesis_dental = $request->detalleDental;
        $segmentado->cuello = $request->cuello;
        $segmentado->torax = $request->torax;
        $segmentado->abdomen = $request->abdomen;
        $segmentado->extremidades_superiores = $request->superiores;
        $segmentado->extremidades_inferiores = $request->inferiores;
        $segmentado->columna_torso = $request->columnaDorso;
        $segmentado->genitales = $request->genitales;
        $segmentado->piel = $request->piel;

		//brazalete

		$brazalete = $request->brazalete;
        if($brazalete == "si"){
            $segmentado->brazalete = true;
        }else
        if($brazalete == 'no'){
            $segmentado->brazalete = false;
			
        }else{
            $segmentado->brazalete = false;
        }

		$segmentado->ubicacion_brazalete = $request->ubicacionbrazalete;

		//audifonos
		$auditiva = $request->dauditiva;
		$audifonos = $request->audifonos;
		if($auditiva == "si"){
            $segmentado->discapacidad_auditiva = true;
			
			if($audifonos == "si"){
				$segmentado->audifonos_discapacidad_auditiva = true;
				$segmentado->ubicacion_discapacidad_auditiva = $request->audi_ubicacion;
			}else
			if($audifonos == 'no'){
				$segmentado->audifonos_discapacidad_auditiva = false;
				
			}else{
				$segmentado->audifonos_discapacidad_auditiva = false;
			}
        }else{
			if($auditiva == 'no'){
				$segmentado->discapacidad_auditiva = false;
				
			}else{
				$segmentado->discapacidad_auditiva = false;
			}
		}
        
		$segmentado->detalle_discapacidad_auditiva = $request->detalleaudi;

		//visual
		$dis_visual = $request->dvisual;
		$lentes = $request->Lentes;
		if($dis_visual == "si"){
			$segmentado->discapacidad_visual = true;
			
			if($lentes == "si"){
				$segmentado->lentes_discapacidad_visual = true;
				$segmentado->ubicacion_discapacidad_visua = $request->l_ubi;
			}else
			if($lentes == 'no'){
				$segmentado->lentes_discapacidad_visual = false;
				
			}else{
				$segmentado->lentes_discapacidad_visual = false;
			}
		}else
		if($dis_visual == 'no'){
			$segmentado->discapacidad_visual = false;
			
		}else{
			$segmentado->discapacidad_visual = false;
		}

		$segmentado->detalle_discapacidad_visua = $request->detallelentes;


		//lesiones
		$presencia_lesiones = $request->plesiones;
		if($presencia_lesiones == "si"){
			$segmentado->presencia_lesiones = true;
			$tipo_lesiones = $request->les_tipo;
			if($tipo_lesiones == "Otras"){
				$segmentado->descripcion_presencia_lesiones = $request->les_desc;
			}
			$segmentado->tipo_presencia_lesiones = $request->les_tipo;
		}else
		if($presencia_lesiones == 'no'){
			$segmentado->presencia_lesiones = false;
			
		}else{
			$segmentado->presencia_lesiones = false;
		}

		$segmentado->ubicacion_presencia_lesiones = $request->les_ubi;


		if($request->v_ginecologica == 2){
			$segmentado->altura_uterina =  $request->altura_uterina;
			$segmentado->tacto_vaginal = $request->tacto_vaginal;
			$segmentado->membranas = $request->membranas;
			$segmentado->liquido_anmiotico = $request->liquido_anmiotico;
			$segmentado->amnioscopia = $request->amnioscopia;
			$segmentado->amnioscentesis = $request->amnioscentesis;
			$segmentado->presentacion = $request->presentacion;
			$segmentado->contracciones = $request->contracciones;
			$segmentado->lfc = $request->lfc;
			$segmentado->vagina = $request->vagina;
			$segmentado->perine = $request->perine;
			$segmentado->tacto_vaginal_eg = $request->tacto_vaginal_eg;
		}
        
        $segmentado->save();

        return $segmentado;
    }

}

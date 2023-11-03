<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use Log;
use View;
use App\Models\THistorialOcupaciones;
use Carbon\Carbon;
use Mail;
use Excel;

class CorreoSinCategorizarController extends Controller {
    /**
     * pacientes sin categorizar y pacientes con estadia de mas de 6 horas
     */
    public static function pacientesSinCategorizar() {

			$sinCategorizacion = DB::table('ultimas_evoluciones_pacientes as uep')
			->select("c.id as id_caso",
				"tho.id",
				"p.id as paciente_id",
				"p.rut",
                "p.dv", "p.nombre",
                "p.apellido_paterno",
                "p.fecha_nacimiento",
                "uee.alias",
                "af.nombre as area_funcional",
                "camas.id_cama as nombre_cama",
                "salas.nombre as nombre_sala",
                "tho.fecha_ingreso_real",
                "uee.id as id_unidad_en_establecimiento")
            ->join("t_historial_ocupaciones as tho", "uep.caso", "=", "tho.caso")
            ->join("camas", "camas.id", "=", "tho.cama")
            ->join("salas", "salas.id", "=", "camas.sala")
            ->join("casos as c", "c.id", "=", "tho.caso")
            ->join("pacientes as p", "p.id", "=", "c.paciente")
            ->join("unidades_en_establecimientos as uee", "uee.id", "=", "salas.establecimiento")
            ->join("area_funcional as af", "af.id_area_funcional", "=", "uee.id_area_funcional")
            ->whereNull("tho.fecha_liberacion")
            ->wherenull("uep.riesgo")
            ->whereNotNUll("tho.fecha_ingreso_real")
            ->get();

            $mas6Dias = DB::table('t_historial_ocupaciones as tho')
            ->select('c.id as id_caso',
            'tho.id',
            'p.id as paciente_id',
            'p.rut',
            'p.dv',
            'p.nombre',
            'p.apellido_paterno',
            'p.fecha_nacimiento',
            'uee.alias',
            'af.nombre as area_funcional',
            'camas.id as nombre_cama',
            'salas.nombre as nombre_sala',
            'tho.fecha_ingreso_real',
            'uee.id as id_unidad_en_establecimiento')
             ->join("camas", "camas.id", "=", "tho.cama")
            ->join("salas", "salas.id", "=", "camas.sala")
            ->join("casos as c", "c.id", "=", "tho.caso")
            ->join("pacientes as p", "p.id", "=", "c.paciente")
            ->join("unidades_en_establecimientos as uee", "uee.id", "=", "salas.establecimiento")
            ->join("area_funcional as af", "af.id_area_funcional", "=", "uee.id_area_funcional")
            ->whereNull("tho.fecha_liberacion")
            ->whereNotNUll("tho.fecha_ingreso_real")
            ->orderBy("tho.fecha_ingreso_real", "asc")
            ->get();


			$resultado = [];
			foreach ($sinCategorizacion as $sc) {
                $dv = ($sc->dv == 10) ? 'K' : $sc->dv;
                $resultado[] = [
                    'rut' => $sc->rut. "-" .$dv,
                    'nombre' => $sc->nombre . " " . $sc->apellido_paterno,
                    'fecha_nacimiento' => Carbon::parse($sc->fecha_nacimiento)->format("d-m-Y"),
                    'area_funcional' => $sc->area_funcional,
                    'unidad_funcional' => $sc->alias,
                    'sala' => $sc->nombre_sala,
                    'cama' => $sc->nombre_cama,
                ];
            }

          Excel::create('Lista_Pacientes_NoCategorizados', function ($excel) use ($resultado){
                $excel->sheet('Lista_Pacientes_NoCategorizados', function ($sheet) use ($resultado){

                    $sheet->mergeCells('A1:G1');
                    $sheet->setAutoSize(true);

                    $sheet->setHeight(1, 30);
                    $sheet->row(1, ['INFORME DIARIOS PACIENTES SIN CATEGORIZAR']);
                    $sheet->row(2, ['Fecha: '.Carbon::now()->format("d-m-Y")]);

                    $sheet->row(1, function ($row) {
                        $row->setBackground('#1E9966');
                        $row->setFontColor("#FFFFFF");
                        $row->setAlignment("center");
                        $row->setFontWeight('bold');

                    });

                    $sheet->fromArray($resultado, null, 'A4', true);
                });

            })->store('xls', storage_path('storage/test/'));

            $resultado2 = [];
            foreach ($mas6Dias as $md) {
                $idCaso = $md->id_caso;
                $ocupaciones = THistorialOcupaciones::where('caso',$idCaso)->whereNull('fecha_liberacion')->first()->fecha_ingreso_real;
                $fecha_ingreso_real = Carbon::parse($ocupaciones);
                $estadia = $fecha_ingreso_real->diffInDays(Carbon::now());
                if($estadia >= 6){
                    $dv = ($md->dv == 10) ? 'K' : $md->dv;
                    $resultado2[] = [
                        'rut' => $md->rut. "-" .$dv,
						'nombre' => $md->nombre . " " . $md->apellido_paterno,
						'fecha_nacimiento' => Carbon::parse($md->fecha_nacimiento)->format("d-m-Y"),
						'area_funcional' => $md->area_funcional,
						'unidad_funcional' => $md->alias,
						'sala' => $md->nombre_sala,
						'cama' => $md->nombre_cama,
						'estadia' => $estadia,
                    ];
                }
            }

            Excel::create('Lista_Pacientes_Mas6DiasEstadia', function ($excel) use ($resultado2){
                $excel->sheet('Lista_Pacientes_Mas6DiasEstadia', function ($sheet) use ($resultado2){

                    $sheet->mergeCells('A1:H1');
                    $sheet->setAutoSize(true);

                    $sheet->setHeight(1, 30);
                    $sheet->row(1, ['INFORME DIARIO PACIENTES DE LARGA ESTADIA (MÁS DE 6 DÍAS).']);
                    $sheet->row(2, ['Fecha: '.Carbon::now()->format("d-m-Y")]);

                    $sheet->row(1, function ($row) {
                        $row->setBackground('#1E9966');
                        $row->setFontColor("#FFFFFF");
                        $row->setAlignment("center");
                        $row->setFontWeight('bold');

                    });

                    $sheet->fromArray($resultado2, null, 'A4', true);
                
                });

            })->store('xls', storage_path('storage/test/'));

            //creando pdf
            $pdf = \Barryvdh\DomPDF\Facade::loadView('pdfNoCategorizados', ["resultado" => $resultado]);
            $pdf->setPaper('a3');

            $pdf2 = \Barryvdh\DomPDF\Facade::loadView('pdfMas6Dias', ["resultado2" => $resultado2]);
            $pdf2->setPaper('a3');

            $subject = "Lista de pacientes no categorizados y de pacientes con mas de 6 dias de estadia";
            $for = ["jessica.mella@redsalud.gov.cl", "gestiondecamashospitalcopiapo@gmail.com","jacqueline.escobar@redsalud.gov.cl","katherine.guerrerob@redsalud.gob.cl"]; 
            $cc = "camilo.guerrero@uv.cl"; 

            $excel['attach'] = base_path().'/storage/storage/test/Lista_Pacientes_NoCategorizados.xls';
            $excel['attach2'] = base_path().'/storage/storage/test/Lista_Pacientes_Mas6DiasEstadia.xls';

            Mail::send('correoSinCategorizar', ['resultado' => $resultado, 'resultado2' => $resultado2], function($msj) use($subject,$for,$pdf,$pdf2,$cc,$excel){
                $msj->from("eduardo.arellano@uv.cl", "SIGICAM");
                $msj->subject($subject);
                $msj->to($for)->cc($cc);
                $msj->attachData($pdf->output(), 'Lista_De_Pacientes_No_Categorizados.pdf', ['mime' => 'application/pdf']);
                $msj->attachData($pdf2->output(), 'Lista_De_Pacientes_Mas_6_Dias_Estadia.pdf', ['mime' => 'application/pdf']);
                $msj->attach($excel['attach']);
                $msj->attach($excel['attach2']);
            });


            return View::make("correoSinCategorizar", ["resultado" => $resultado, "resultado2" => $resultado2]);
    }
}

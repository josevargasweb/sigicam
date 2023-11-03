<?php

namespace App\Http\Controllers;

use App\Models\FormularioDerivacion;
use Illuminate\Http\Request;

class FormularioDerivacionController extends Controller
{

    public function formularioDerivacionStore(Request $request)
    {
        $this->validate($request, [
            'idCaso' => 'nullable|numeric',
            'idPaciente' => 'nullable|numeric',
            'fechaSolicitud' => 'nullable|date',
            'fechaDeri' => 'nullable|date',
        ]);
        $nuevo = new FormularioDerivacion();
        $nuevo->id_paciente = $request->idPaciente;
        $nuevo->id_caso = $request->idCaso;
        $nuevo->ges = $request->optGes;
        $nuevo->ugcc = $request->optUgcc;

        if ($nuevo->ugcc == false) {
            $nuevo->detalle_ugcc = null;
        } else {
            $nuevo->detalle_ugcc = $request->tipoUgcc;
        }
        $nuevo->fecha_creacion_solicitud = $request->fechaSolicitud;
        $nuevo->fecha_derivacion = $request->fechaDeri;
        $nuevo->save();

    }

}

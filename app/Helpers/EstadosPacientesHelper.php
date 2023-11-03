<?php

namespace App\Helpers;
use Carbon\Carbon;
use DateTime;
use Exception;
use Log;



class EstadosPacientesHelper extends Helper{

    public static function motivoPacienteHospitalizado(){
        return [
            'null'
        ];
    }

    public static function motivoPacienteEgresado(){
        return [
            'alta',
            'fallecimiento',
            'derivación',
            'otro',
            'traslado extra sistema',
            'hospitalización domiciliaria',
            'Liberación de responsabilidad',
            'derivacion otra institucion'
        ];
    }

    public static function motivoPacienteEgresadoYHospitalizado(){
        return [
            "'alta'",
            "'fallecimiento'",
            "'derivación'",
            "'traslado interno'",
            "'otro'",
            "'traslado extra sistema'",
            "'hospitalización domiciliaria'",
            "'alta sin liberar cama'",
            "'Liberación de responsabilidad'",
            "'derivacion otra institucion'"
        ];
    }
}
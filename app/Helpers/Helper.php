<?php

namespace App\Helpers;
use Carbon\Carbon;
use DateTime;
use Exception;
use Log;
use DB;


/**
 * Clase helper padre con herramientas utiles
 */

class Helper{

    function validateDate($date, $format = 'd-m-Y'){
        try{
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        } catch(Exception $e){
            Log::error($e);
            return false;
        }

    }


    function dateComparison($date_str_a, $date_str_b, $format, $opt){
        
        $flag = false;
        $date_str_a_is_valid = $this->validateDate($date_str_a, $format);
        $date_str_b_is_valid = $this->validateDate($date_str_b,$format);
        if($date_str_a_is_valid && $date_str_b_is_valid){

            $date_a =  Carbon::createFromFormat($format, $date_str_a);
            $date_b =  Carbon::createFromFormat($format, $date_str_b);
            
            if
            ($opt === 'a<b'){  $flag = $date_a->lt($date_b); }
            else if
            ($opt === 'a<=b'){ $flag = $date_a->lte($date_b);}
            else if
            ($opt === 'a>b'){  $flag = $date_a->gt($date_b); }
            else if
            ($opt === 'a>=b'){ $flag = $date_a->gte($date_b); }
            else if
            ($opt === 'a==b'){ $flag = $date_a->equalTo($date_b); }
            else if
            ($opt === 'a!=b'){ $flag = $date_a->ne($date_b); }

        }

        return $flag;

    }

    //Formato YYYY-mm-dd
    function getAge($birth_date){
        return Carbon::parse($birth_date)->diff(Carbon::now())->y;
    }

    /* 
        checker que evalua un conjunto de elementos
        devuelve true si todos o ninguno de sus elementos son nulls
        en caso contrario es false.
        usado en validaciones de formulario

        devuelve un valor boolean
    */
    function allOrNone($arrayOfElements){
        $null_elements = 0;
        foreach ($arrayOfElements as $key => $value) {
            if(!isset($value)){
                $null_elements++;
            }
        }

        return ($null_elements === count($arrayOfElements) || $null_elements === 0) ? true : false;

    }

    /* 
        obtiene un array unidimencional y: 
        1. transforma las cadenas de texto vacias en nulls 
        2. sanitiza sus valores
        usado en validaciones de formulario para inputs que se pueden
        agregar indefinidas veces

        devuelve un array
    */
    function uniArrayReplaceEmptyStringByNullAndSanitize($arrayOfElements){

        $array = [];

        if(isset($arrayOfElements) && is_array($arrayOfElements) ){
            foreach ($arrayOfElements as $key => $value) {

                $array[$key] = (isset($value) && trim($value) !== "") ?
                trim(strip_tags($value)) : null;
    
            }
        }

        return $array;

    }


    /** 
     * Check que todos los elementos de un arreglo son iguales
     * (para elementos enteros o strings)
    */

    function checkArrayElementsAreSame($arrayOfElements){


        if(isset($arrayOfElements) && is_array($arrayOfElements) ){

            return (!empty( $arrayOfElements )) ? count(array_unique($arrayOfElements)) === 1 : true;   

        } 
        else { throw new Exception('Ha ocurrido un error');}


    }


    /*
        Funcion que ordena inputs multiples y
        entrega un arreglo de objetos donde 
        cada uno corresponde a una fila de los inputs.

        Valida que lleguen inputs multiples de manera
        consistente.

    */

    function ordenamientoInputsMultiples($arrayOfInputsMultiples){

        $rows[] = array();
        $rowsO = array();


        if(isset($arrayOfInputsMultiples) && is_array($arrayOfInputsMultiples) ){

            $length_array = [];

            foreach ($arrayOfInputsMultiples as $key => $inputMultiple) {

                if(isset($inputMultiple) && is_array($inputMultiple) ){
                    $l = count($inputMultiple);
                    array_push($length_array, $l);

                    //check de consistencia
                    if (! $this->checkArrayElementsAreSame($length_array)) {
                        throw new Exception('Campo input_multiple no valido.');
                    }

                    for ($i = 0; $i < $l; $i++) {
                        $elem = [];
                        $elem = array($key => $inputMultiple[$i]);
                        if(!isset($rows[$i])){ $rows[$i] = array();}
                        $rows[$i] = $rows[$i]+$elem;
                    }


                } else {
                    throw new Exception('Campo input_multiple no valido.');
                }

            }

            if($length_array[0] === 0){ return $rowsO; }

            foreach ($rows as $key => $r) {
                array_push($rowsO, (object)$r);

            }

        }
        else {
            throw new Exception('Campo input_multiple no valido.');
        }

        return $rowsO;

    }

    /*
        Funcion que checkea si un array de enteros esta ordenado
        devuelve true o false.

    */

    function checkArrayIsSort($array){

        $sorted = array_values($array);
        sort($sorted);
        return $array === $sorted;
        
    }

    function numberOfDecimals($value)
    {
        return strlen(substr(strrchr((string)$value, "."), 1));
    }


    function isInteger($val){
        return ctype_digit((string)$val);
    }

    function isFloat($val){
        return preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $val) == 1;
    }

    function isNumeric($val){
        return $this->isFloat($val) || $this->isInteger($val);
    }


    //retorna la unidad del caso
    function getSubCategoria($caso_id){

        $subCategoria = null;

        try {
            $ubicacionQuery = DB::table('t_historial_ocupaciones as t')
            ->join("camas as c", "c.id", "=", "t.cama")
            ->join("salas as s", "c.sala", "=", "s.id")
            ->join("unidades_en_establecimientos AS uee", "s.establecimiento", "=", "uee.id")
            ->join("area_funcional AS af", "uee.id_area_funcional", "=", "af.id_area_funcional")
            ->where("t.caso", $caso_id)
            ->whereNull("t.motivo")
            ->select("uee.alias as nombre_unidad",  "af.nombre as nombre_area_funcional","uee.id")
            ->first();

    
            if($ubicacionQuery){
                $subCategoriaQuery = DB::table('historial_unidad_subcategoria as hus')
                ->select("hus.id_subcategoria as id_subcategoria")
                ->where("hus.id_unidad", $ubicacionQuery->id)
                ->where("hus.visible", true)
                ->first();

                $subCategoria = ($subCategoriaQuery) ? $subCategoriaQuery->id_subcategoria : null;
    
            }

        }catch(Exception $e){
            Log::error($e);
        }finally {
            return $subCategoria;
        }

    }
    

}
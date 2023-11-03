<?php namespace App\util{
use DateTime;
use Carbon\Carbon;
use Log;

class Funciones {
    //multiplica por un valor escalar los elementos entre dos arreglos
    public static function multiplicar_vector ($valor , $vector){
        $tamano = count($vector);
        $vector_multiplicado = array();
        for ($i = 0 ; $i < $tamano ; $i++){
            $vector_multiplicado[$i] = $valor * $vector[$i];
        }
        return $vector_multiplicado;
    } 

    //suma los elementos entre dos arreglos
    public static function sumar_vectores ($arreglo1 , $arreglo2){
        $tamano = count($arreglo1);
        $vectores_sumados = array();
        for ($i = 0 ; $i < $tamano ; $i++){
            $vectores_sumados[$i] = $arreglo1[$i] + $arreglo2[$i];
        }
        return $vectores_sumados;
    }

    //calcula la distancia euclidiana entre los valores de 2 arrays
    public static function distancia_euclidiana_kmens ($arreglo1 , $arreglo2){
        $tamano = ( count($arreglo1)<count($arreglo2) ) ? count($arreglo1) : count($arreglo2);//se considera tamaño del arreglo menor
        $distancia = 0; 
        for ($i = 0 ; $i < $tamano ; $i++){
            $diferencia=$arreglo1[$i] - $arreglo2[$i];
            $distancia = $distancia + pow($diferencia,2);
        }
        $distancia = sqrt($distancia) ;
        return $distancia ;
    }

    //compara 2 arreglos elemento por elemento
    public static function comparar ($arreglo1, $arreglo2){
        if (count($arreglo1) != count($arreglo2)){  
            return false;
        }
        for ($i = 0; $i < count($arreglo2); $i++){
            if ($arreglo1[$i]!=$arreglo2[$i]){
                return false;
            }
        }
        return true;
    }

    //obtiene k arreglos elegidos de manera aleatoria
    public static function obtener_arreglos_aleatorios($k, $vectores){               
        $n = count($vectores);
        if ($k > $n){
                return null;
        }
        $vectores_seleccionados = array();
        $indices_seleccionados = array();
        $indices_probados = array();
        $seleccionado = 0;
        $vector = array(); 
        while ($seleccionado < $k){
            $indice_aleatorio = floor((rand(0, 10)/10)*($n-1)) ;
            if (in_array($indice_aleatorio, $indices_probados)){
                continue;
            }
            $indices_probados[$indice_aleatorio] = 1;
            $vector = $vectores[$indice_aleatorio];
            $continuar = true;
            for ($i = 0 ; $i < $seleccionado ; $i++){
                if (self::comparar($vector,$vectores_seleccionados[$i])){
                        $continuar = false;
                        break;
                }
            }
            if ($continuar){
                $vectores_seleccionados[$seleccionado] = $vector;
                $indices_seleccionados[$seleccionado] = $indice_aleatorio; 
                $seleccionado++;
            }
        }
        $vectores_mas_indices=array();
        $vectores_mas_indices[0]=$vectores_seleccionados;
        $vectores_mas_indices[1]=$indices_seleccionados;
        return $vectores_mas_indices;
    }

    public static function kmeans ($k, $arreglos){

        $KMEANS_MAX_ITERATIONS = 10;
        $MAX_VALUE = 1.7976931348623157e+308;

        $n = count($arreglos);
        $asignaciones = array();
        $tamano_clusters = array();
        $repetir = true;
        $n_iteraciones = 0;
        $centroides = null;  
        $t = self::obtener_arreglos_aleatorios($k, $arreglos);
        if ($t == null)
            return null;
        else
            $centroides = $t[0];
        while ($repetir){
                for ($j = 0 ; $j < $k ; $j++)
                    $tamano_clusters[$j] = 0;
                
                for ($i = 0 ; $i < $n ; $i++){
                    $arreglo = $arreglos[$i];
                    $distancia_minima = $MAX_VALUE;
                    $mejor;
                    for ($j = 0 ; $j < $k ; $j++){
                        $dist = self::distancia_euclidiana_kmens ($centroides[$j], $arreglo);
                        if ($dist < $distancia_minima){
                            $distancia_minima = $dist;
                            $mejor = $j;
                        }
                    }
                    $tamano_clusters[$mejor]++;
                    $asignaciones[$i] = $mejor;
                }
                $nuevo_centroide = array();
                for ($j = 0 ; $j < $k ; $j++)
                    $nuevo_centroide[$j] = null;
    
                for ($i = 0 ; $i < $n ; $i++){
                    $cluster = $asignaciones[$i];
                    if ($nuevo_centroide[$cluster] == null)
                        $nuevo_centroide[$cluster] = $arreglos[$i];
                    else
                        $nuevo_centroide[$cluster] = self::sumar_vectores ($nuevo_centroide[$cluster] , $arreglos[$i]);       
                }
    
                for ($j = 0 ; $j < $k ; $j++) {
                    $nuevo_centroide[$j] = self::multiplicar_vector (1/$tamano_clusters[$j] , $nuevo_centroide[$j]);
                }        
                $repetir = false;
                for ($j = 0 ; $j < $k ; $j++){
                    if (!self::comparar($nuevo_centroide[$j], $centroides[$j])){
                        $repetir = true ; 
                        break; 
                    }
                }
                $centroides = $nuevo_centroide;
                $n_iteraciones++ ;
                if ($n_iteraciones > $KMEANS_MAX_ITERATIONS)
                    $repetir = false;           
        }
        $centroides_mas_asignaciones=array();
        $centroides_mas_asignaciones[0]=$centroides;
        $centroides_mas_asignaciones[1]=$asignaciones;
        return $centroides_mas_asignaciones;
    }


    public static function obtener_atributo_por_cluster2($dato,$clusters,$k, $a1, $a2) { //atributo v/s atributo para hacer scatterplot
        $arr=array();
        $n=0;
          if ($clusters) {
            //echo 'count($dato):'.count($dato).'<br>';
              for ($i = 0; $i < count($dato); $i++) {
                  if ($clusters[1][$i]==$k) {
                  //echo '$dato[$i][$a2]:'.$dato[$i][$a2].' i:'.$i.'<br>';
                  $arr[$n][0]=floatval($dato[$i][$a1]);
                  $arr[$n][1]=floatval($dato[$i][$a2]);
                  $n++;
                  } 
              } 
          }
      return $arr;
    }


    public static function numero_alertas(){ 
        global $enfermedad_comuna;
        global $enfermedad_comuna_numero;
        $numero_alertas=0;
        for ($i=0; $i <count($enfermedad_comuna) ; $i++) { 
          for ($j=0; $j <count($enfermedad_comuna[$i]) ; $j++) { 
               if ($j==0) {
                  for ($k=$i; $k <count($enfermedad_comuna)-1 ; $k++) { 
                       if ($enfermedad_comuna[$i][0]==$enfermedad_comuna[$k+1][0]) {
                           $enfermedad_comuna[$i][]=$enfermedad_comuna[$k+1][1];
                           unset($enfermedad_comuna[$k+1]);
                           $enfermedad_comuna = array_values($enfermedad_comuna);
                       }
                  }
                }else{
                        $numero_alertas++;
              }
          }$enfermedad_comuna_numero[]=$numero_alertas;
          $numero_alertas=0;
        }
    }

    public static function esperanza($arreglo){ 
        $esperanza=array();
        $suma=0;
        $divisor=count($arreglo);
        //echo 'divisor:'.$divisor.'<br>';
        for ($j=2; $j < count($arreglo[0]); $j++){
        $suma=0;
         for ($i=0; $i < count($arreglo); $i++){
           $suma=$suma+$arreglo[$i][$j];
           //echo 'suma:'.$suma.'<br>';
        }
        //$esperanza[]=round($suma/$divisor);
         $esperanza[]=$suma/$divisor;
        //echo '$esperanza[$j-2]:'.$esperanza[$j-2].'<br>';
       }
       return $esperanza;
      }

    public static function distribucionEspacial($arreglo,$enfermedades){

        $probabilidad_beta=0;
        global $enfermedad_comuna;
        $probabilidad=0;
        $Lam=0;
        $X=0;
        $alerta=array();
        $probabilidad_alerta=0.5;
        $esperanza_valor=array();
        $esperanza_valor = self::esperanza($arreglo);
        $comunas_con_alerta=array();
        $k=0;
        $contador_comuna=0;
      
        for ($i=2; $i < count($arreglo[0]); $i++) { 
         for ($j=0; $j < count($arreglo); $j++) {       
              
              $X=$arreglo[$j][$i]-1; //se resta 1 por que entrega P(X<=x)
              
          //echo '$esperanza_valor[$i-2]:'.$esperanza_valor[$i-2].'<br>';
              
              $Lam=$esperanza_valor[$i-2];
      
              //echo '$Lam:'.$Lam;
              $probabilidad_beta = self::poisson($X, $Lam);
              $probabilidad=(1-round($probabilidad_beta,4));
              if ($probabilidad<=$probabilidad_alerta) {
                //hay alerta
                $contador_comuna++;
                $alerta[$i][]=1;
      
                if (!in_array($arreglo[$j][1], $comunas_con_alerta)) {
                   $comunas_con_alerta[]=$arreglo[$j][1];
                }
                else{
                     //$contador_comuna++;
                }
                $enfermedad_comuna[$k][]=$arreglo[$j][0]; //inserta la comuna
                $enfermedad_comuna[$k][]=$enfermedades[$i-2]; //inserta la enfermedad
                $k++;
              }else{
                  //no hay alerta
                  $alerta[$i][]=0;
              }
                
          } $contador_comuna=0;
        }
        self::numero_alertas();
        return($comunas_con_alerta);
      }


    //---------------------------------------------------------------------------------------------------------------------------------------
    //FUNCIONES PARA CALCULAR LA DISTRIBUCION DE POISSON

    public static function LogGamma($Z){
        $S=1+76.18009173/$Z-86.50532033/($Z+1)+24.01409822/($Z+2)-1.231739516/($Z+3)+.00120858003/($Z+4)-.00000536382/($Z+5);
        $LG= ($Z-.5)*log($Z+4.5)-($Z+4.5)+log($S*2.50662827465);
        return $LG;
    }
    
    
    public static function Gcf($X,$A){
        $A0=0;
        $B0=1;
        $A1=1;
        $B1=$X;
        $AOLD=0;
        $N=0;
        while (abs(($A1-$AOLD)/$A1)>.00001) {
            $AOLD=$A1;
            $N=$N+1;
            $A0=$A1+($N-$A)*$A0;
            $B0=$B1+($N-$A)*$B0;
            $A1=$X*$A0+$N*$A1;
            $B1=$X*$B0+$N*$B1;
            $A0=$A0/$B1;
            $B0=$B0/$B1;
            $A1=$A1/$B1;
            $B1=1;
        }
        $Prob=exp($A*log($X)-$X-self::LogGamma($A))*$A1;
        return 1-$Prob;
    }
    
    public static function Gser($X,$A) {      
        $T9=1/$A;
        $G=$T9;
        $I=1;
        while ($T9>$G*0.00001) {
            $T9=$T9*$X/($A+$I);
            $G=$G+$T9;
            $I=$I+1;
        }
        $G=$G*exp($A*log($X)-$X-self::LogGamma($A));
        return $G;
    }
     
    public static function Gammacdf($x,$a) {
        $GI;
        if ($x<=0) {
            $GI=0;
        } else if ($x<$a+1) {
            $GI=self::Gser($x,$a);
        } else {
            $GI=self::Gcf($x,$a);
        }
        return $GI;
    }

    public static function poisson($Z, $Lam) {
        if ($Lam<=0) {
            $Poiscdf=0;
        } else if ($Z<0) {
            $Poiscdf=0;
        } else {
            $Z=floor($Z);
            $Poiscdf=1-self::Gammacdf($Lam,$Z+1);
        }
        $Poiscdf=round($Poiscdf*100000)/100000;
            return $Poiscdf;
    }

    //calcula la distancia euclideana entre un par de arreglos
    public static function distancia_euclidiana ($vec1 , $vec2){
        $N = count($vec1);
        $distancia = 0 ;
        for ($i = 0 ; $i < $N ; $i++){
            $diferencia=$vec1[$i] - $vec2[$i];
            $distancia = $distancia + pow($diferencia,2);
        }
        $distancia = sqrt($distancia) ;
        return $distancia ;
    }


    //calcula la distancia "GOOGLE" entre los pares (longitud, latitud)
    public static function distancia_haversine ($lat1, $long1, $lat2, $long2){
        $radio_tierra_kilometros=6371;
        $radio_tierra_millas=3959;
        $rlat1 = deg2rad($lat1);
        $rlat2 = deg2rad($lat2);
        $rlong1 = deg2rad($long1);
        $rlong2 = deg2rad($long2);
        //entrega la distancia haversine entre 2 puntos (en kilometros)
        $d=acos(sin($rlat1)*sin($rlat2)+cos($rlat1)*cos($rlat2)*cos($rlong2-$rlong1))*$radio_tierra_kilometros;
        $dm=$d*1000; //transformacion a metros
        $dm=round($dm, 4);
        return $dm;
    }

    //retorna la diferencia de tiempo entre 2 fechas - en número de dias
    public static function distancia_temporal ($fecha1, $fecha2){
        $uno = new DateTime($fecha1);
        $dos = new DateTime($fecha2);
        $interval = $uno->diff($dos);
        $distancia= $interval->format('%a');
        return $distancia;
    }

    public static function sanearString($string) {
        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            ".", " "), '', $string
        );


        return $string;
    }
            public static function reemplazarCharEs($text) {
        $search = [ "á","é","í","ó","ú","ñ" ];
        $replace = [ "a","e","i","o","u","n" ];

        return str_replace($search, $replace, $text);
    }

        public static function getFileName($fileRute) {
        return pathinfo( $fileRute )["filename"];
    }

        public static function getFileExtension($fileRute) {
        try { $ext = ".".pathinfo( $fileRute )["extension"]; }
        catch (Exception $e) { $ext = ""; }
        return $ext;
    }

        public static function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    public static function intervalo($fecha_mayor, $fecha_menor){
        $mayor = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_mayor);
        $menor = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_menor);
        if (!$mayor || !$menor) {
            throw new Exception("Error en el formato de la fecha");
        }
        $int = $mayor->diff($menor);
        $f = '';
        if ($int->d == 1){
            $f.= '%a día, ';
        }
        elseif ($int->d > 1){
            $f.= "%a días, ";
        }
        $f.= '%h horas, %i minutos.';

        return $int->format($f);
    }


    public static function calcularDv($r){
        $s=1;
        for($m=0;$r!=0;$r/=10)
            $s=($s+$r%10*(9-$m++%6))%11;
        return chr($s?$s+47:75);
   }


   public static function calcularEdad($fechaNac){
    //$cumpleanos = new DateTime("1982-06-03");
    //$desde = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $desde)
    //return $fechaNac;

    $fechaNac = explode("/",$fechaNac);
    $anio = $fechaNac[2];
    $mes =$fechaNac[1];
    $dia =$fechaNac[0];
    
    $edad  = Carbon::createFromDate($anio, $mes, $dia)->age;
    return $edad;
   }

   public static function calcularRangoEdad($edad2){
    $rango = null;
    if (($edad2 >= 0) && ($edad2 <= 9)) {
        $rango = "0-9";
    } elseif (($edad2 >= 10) && ($edad2 <= 19)) {
        $rango = "10-19";
    } elseif (($edad2 >= 20) && ($edad2 <= 29)) {
        $rango = "20-29";
    } elseif (($edad2 >= 30) && ($edad2 <= 39)) {
        $rango = "30-39";
    } elseif (($edad2 >= 40) && ($edad2 <= 49)) {
        $rango = "40-49";
    } elseif (($edad2 >= 50) && ($edad2 <= 59)) {
        $rango = "50-59";
    } elseif (($edad2 >= 60) && ($edad2 <= 69)) {
        $rango = "60-69";
    } elseif (($edad2 >= 70) && ($edad2 <= 79)) {
        $rango = "70-79";
    } elseif (($edad2 >= 80) && ($edad2 <= 89)) {
        $rango = "80-89";
    } elseif (($edad2 >= 90) && ($edad2 <= 99)) {
        $rango = "90-99";
    } elseif (($edad2 >= 100) && ($edad2 <= 109)) {
        $rango = "100-109";
    } elseif (($edad2 >= 110) && ($edad2 <= 119)) {
        $rango = "110-119";
    } elseif (($edad2 >= 120) && ($edad2 <= 129)) {
        $rango = "120-129";
    }
    return $rango;
   }

    public static $meses = [
        1 => "Enero",
        2 => "Febrero",
        3 => "Marzo",
        4 => "Abril",
        5 => "Mayo",
        6 => "Junio",
        7 => "Julio",
        8 => "Agosto",
        9 => "Septiembre",
        10 => "Octubre" ,
        11 => "Noviembre" ,
        12 => "Diciembre" ,
    ];

}

}
?>

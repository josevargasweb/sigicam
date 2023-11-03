<?php
namespace App\Http\Controllers;
class ArchivoController extends Controller {
    /**
     * Recibe un archivo y los guarda en public/files
     */
    public static function recibir() {
        $retorno = false;

        if( count($_FILES) > 0 ) {
            try {
                foreach( $_FILES as $file ){

                    $fileName = Funciones::reemplazarCharEs($file["name"]);
                    $fileName = (ArchivoFormulario::obtenerUltimoId()+1)."___".
                                Funciones::getFileName( $fileName )
                                .Funciones::getFileExtension( $fileName )
                                ;
                    $fileFolder = "/public/files/";
                    $filePath = $fileFolder.urlencode( $fileName );
                    $absoluteFilePath = str_replace( "\\", "/", base_path().$filePath );

                    rename( $file['tmp_name'], $absoluteFilePath );

                    // guardar en base de datos
                    self::registrar( $filePath );
                    $retorno = true;
                    return Response::json( $retorno );
                }
            } catch (Exception $e) { $retonro = utf8_decode( $e->__toString() ); }
        }
        return Response::json( $retorno );
    } 

    /**
     * Registra un archivo en la base de datos
     * @return Boolean|Exception True|False|Error como excecion
     */
    public static function registrar($ruta) {
        $retorno = false;
        try {
            $archivoFormulario = new ArchivoFormulario;
            if(ArchivoFormulario::obtenerUltimoId())
                $archivoFormulario->id_archivos = ArchivoFormulario::obtenerUltimoId()+1;
            else
                $archivoFormulario->id_archivos = 1;
            $archivoFormulario->ruta = $ruta;
            //$archivoFormulario->visibilidad = self::obtenerVisibilidad();
            //if( Input::has('seccionArchivo') ) $archivoFormulario->seccion_archivo = Input::get('seccionArchivo');
            $archivoFormulario->fecha_subida_archivo = date_format( date_create(), "Y-m-d h:m:s" );
            $archivoFormulario->save();
            $retorno = true;
        } catch (Exception $e) { utf8_decode( $retorno = $e->__toString() ); }
        return $retorno;
    }

    /**
     * Visibilidad viene de Paciente/JSCrearConsulta.blade.php (archivos adjuntos de ulceras)
     * visibilidadArchivos viene de Documentos/Documentos.blade.php@ver (cuando se construye la vista de los archivos)
     * Si no existe en ninguno de los 2 lados se obtiene false
     */
    private static function obtenerVisibilidad() {
        return Input::has('visibilidad') ? Input::get('visibilidad') : (
            Session::has('visibilidadArchivos') ? Session::get('visibilidadArchivos') : false
        ); 
    }

    public static function obtenerAsocCaso($idCaso) {
        $archivos = ArchivoFormulario::listarAssocCaso($idCaso, 'medico');
        return Response::json($archivos);
    }
}
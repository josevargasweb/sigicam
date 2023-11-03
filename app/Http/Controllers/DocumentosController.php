<?php
namespace App\Http\Controllers;
use App\Models\ArchivoFormulario;
use Session;
use DB;
use Funciones;
use Illuminate\Http\Request;
use View;

class DocumentosController extends Controller
{
    /**
     * Renderiza la vista documentos
     * @return View
     */
    public static function ver($visibilidad) {
        Session::put('visibilidadArchivos', $visibilidad);
        return View::make('Documentos/Documentos')
            ->with('visibilidad', $visibilidad);
    }

    /**
     * Elimina un archivo de public/files y su registro en la base de datos
     * @return boolean|(String)Exception
     */
    public static function eliminar(Request $request) {
        $eliminadoDB = $eliminadoHDD = false;

        if( $request->has('nroDocumento') ) {
            $ruta = base_path().ArchivoFormulario::find( $request->input('nroDocumento') )->ruta;
            $ruta = str_replace("", "/", $ruta);
            $eliminadoHDD = unlink($ruta);
            $eliminadoDB = ArchivoFormulario::eliminar( $request->input('nroDocumento') );
        }
        return json_encode( ($eliminadoDB && $eliminadoHDD) );
    }

    /**
     * Obtiene un listado de archivos en formato JSON.
     * @return JSON
     */
    public static function listar() {

        //return "archivos";
        $archivos = ArchivoFormulario::obtenerArchivos(Session::get('visibilidadArchivos'));
        $listadoArchivos = [];

        if( !$archivos ) return response()->json(false);
        
        //return $archivos;
        foreach ($archivos as $item) {
            $nombre = Funciones::getFileName( urldecode( $item->ruta_archivo ) );
            $listadoArchivos[] = [
                "id" => $item->id_archivo,
                "link" =>  "/camas".urldecode( $item->ruta_archivo ),
                "name" => substr_replace( $nombre, "", 0, strrpos($nombre, '___')+3 ),
                "extension" => Funciones::getFileExtension( urldecode( $item->ruta_archivo ) ),
                "size" => "1",
                //"size" => Funciones::FileSizeConvert( filesize( base_path().$item->ruta_archivo ) ),
                "uploadDate" => $item->fecha_subida_archivo
                ];
        }
        return response()->json($listadoArchivos);
    }

    /**
     * Permite descargar un archivo
     * Reemplaza atributo download de <a />, no lo soporta firefox.
     * @param  String $idFile id del archivo a descargar
     * @return [type]         [description]
     */
    public static function descargar( $idFile ) {
            //try {
                $ruta = ArchivoFormulario::find($idFile)->ruta;
                $path = base_path().$ruta;
                $nombre = urldecode( basename($ruta) );
                $nombre = substr_replace( $nombre, "", 0, strrpos($nombre, '___')+3 );
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeInfo = finfo_file($finfo, $path);
                header("Content-type: $mimeInfo");
                header("Content-Disposition: attachment; filename=$nombre");
                readfile($path);
            //} catch (Exception $e) { return "Error en la descarga"; }
    }

    /**
     * Ver contenido de cualquier archivo
     * Funcion original en la url de abajo.
     * url: http://laraveles.com/foro/viewtopic.php?id=125
     * @param  String $idFile Id del archivo a visualizar
     */
    public function verContenido( $idFile )
    {
        try {
            $file = base_path().ArchivoFormulario::find($idFile)->ruta; ;
            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            $mime = finfo_file($finfo, $file);
            $content = file_get_contents( $file ) ;
            return Response::make( $content , 200 , array( 'content-type' => $mime, 'X-Frame-Options:' => ' GOFORIT' ) ) ;
        } catch (Exception $e) { return "<pre>".$e->__toString()."</pre>"; }
    }

    /**
     * Obtiene el link y la extension de un archivoo.
     * @param  String|Int $idFile   Id del archivo
     * @return JSON                 Datos del archivo
     */
    public function obtenerLink( $idArchivo ) {
        try {
            $archivo = ArchivoFormulario::find($idArchivo);

            $nombre = Funciones::getFileName( urldecode( $archivo->ruta ) );
            $extension = Funciones::getFileExtension( urldecode( $archivo->ruta ) );
            $nombre = substr_replace( $nombre, "", 0, strrpos($nombre, '___')+3 );

            $inforArchivo = [
                "name" => urlencode( $nombre),
                "ext" => $extension,
                "link" => '/camas'. $archivo->ruta
            ];
            return response()->json( $inforArchivo );
            
        } catch (Exception $e) { return "<pre>".$e->__toString()."</pre>"; }
    }

    public function vistaDocumentos(){
        $archivos = ArchivoFormulario::obtenerArchivos(null);
        //return $archivos;
        return View::make("Gestion/Documentos");
    }

    public function listaDocumentos(){
        $archivos = ArchivoFormulario::obtenerArchivosCompleto();
        $response = array();
        foreach($archivos as $archivo){
            $response[] = array(
                $archivo->nombre,
                date("d-m-Y H:i", strtotime($archivo->fecha_subida_archivo)),
                "<a href='descargarDocumento/".$archivo->ruta."' class='btn btn-primary'>Descargar</a>"
            );
        }
        return array("aaData"=>$response);
    }
}
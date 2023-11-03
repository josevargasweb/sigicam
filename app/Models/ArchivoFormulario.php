<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;

class ArchivoFormulario extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = 'id_archivos';
    public $timestamps = false;
    //protected $fillable = array('id_formulario_telemedicina', 'ruta');

    /**
     * Obtiene el ultimo id del ultimo archivo agregado en la tabla.
     * @return Exception|Int Ultimo id o la exception lanzada
     */
    public static function obtenerUltimoId() {
        try {
            $retorno = ArchivoFormulario::select('id_archivos')
                        ->max('id_archivos');
        } catch (Exception $e) {
            $retorno = 0;
        }
        return $retorno;
    }

    /**
     * Obtiene una lista de archivos filtrados por visibilidad
     * @return Resulset|Exception
     */
    public static function obtenerArchivos($visibilidad) {
        try {
            $sql = "SELECT
                        id_archivos as id_archivo,
                        ruta AS ruta_archivo,
                        TO_CHAR( fecha_subida_archivo, 'YYYY-MM-DD') as fecha_subida_archivo
                    FROM archivos";
            $retorno = DB::select($sql);
        } catch (Exception $e) { $retorno = false; }
        return $retorno;
    }

    public static function obtenerArchivosCompleto() {
        try {
            $sql = "SELECT * FROM archivos";
            $retorno = DB::select($sql);
        } catch (Exception $e) { $retorno = false; }
        return $retorno;
    }

    /**
     * Elmina un archivo de la base de datos
     * @param  Integer $idArchivo
     * @return boolean
     */
    public static function eliminar($idArchivo) {
        try {
            ArchivoFormulario::destroy($idArchivo);
            $resultado = true;
        } catch (Exception $e) { $resultado = $e; }
        return $resultado;
    }

    public static function listarAssocCaso($idCaso, $visibilidad) {
        $archivos = self::where('id_formulario_telemedicina', '=', $idCaso)
                        ->where('visibilidad', '=', $visibilidad)
                        ->get();
        return $archivos;
    }

    /**
     * Cuenta los archivos adjuntos que tiene un caso.
     * @param  Int    $idCaso      Id del caso.
     * @param  String $visibilidad visibilidad de los archivos.
     * @return Int|Exception       Cantidad de archivos o la excepcion
     *                             de algun error en la ejecucion del metodo.
     */
    public static function contarArchivosCaso($idCaso, $visibilidad) {
        try {
            $retorno = self::where('id_formulario_telemedicina', '=', $idCaso)
                         ->where('visibilidad', '=', $visibilidad)
                         ->count('id_archivos_formulario');
        } catch (Exception $e) { $retorno = $e; }
        return $retorno;
        
    }

    /**
     * Obtiene la ruta de los archivos asociados a un caso en particular.
     * @param  Int    $idCaso      Id del caso.
     * @param  String $visibilidad visibilidad de los archivos.
     * @return Array|Exception     Array con las rutas de los archivos o la exepcion
     *                             producida durante la ejecucion.
     */
    public static function obtenerLinksArchivosCaso($idCaso, $visibilidad) {
        try {
            $retorno = self::where('id_formulario_telemedicina', '=', $idCaso)
                         ->where('visibilidad', '=', $visibilidad)
                         ->select(
                            'ruta'
                            )
                         ->get();
            $output = [];
            foreach ($retorno as $archivo) {
                $output[] = ['ruta' => base_path().$archivo->ruta];
            }
            $retorno = $output;
        } catch (Exception $e) { $retorno = $e; }
        return $retorno;
    }
}
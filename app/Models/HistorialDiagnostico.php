<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 5/15/15
 * Time: 12:57 PM
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HistorialDiagnostico extends Model implements Auditable{
    use \OwenIt\Auditing\Auditable;

    protected $table = "diagnosticos";
    protected $primaryKey = "id";
    
    protected $auditInclude = [
       
    ];

    protected $auditTimestamps = true;

    protected $auditThreshold = 10;


    public static function boot(){
        parent::boot();

        self::creating(function($diag){
            /* @var $diag HistorialDiagnostico */
            if(trim($diag->diagnostico) === ''){
                return false;
            }
        });

        /* self::updating(function($diagnostico){
            //@var $caso HistorialDiagnostico
            $original = $caso->getOriginal();
            foreach($original as $campo => $valor){
                if(trim($caso->$campo) === '') $caso->$campo = $valor;
            }
        }); */
    }

    public function casoDiagnostico(){
        return $this->belongsTo("App\Models\Caso", "caso", "id");
    }

    public static function ultimoDiagnostico($caso){
        return self::where("caso",$caso)->orderBy("fecha","desc")->first()->diagnostico;
    }

}
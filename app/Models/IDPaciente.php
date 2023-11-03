<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 4/10/15
 * Time: 11:47 AM
 */
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class IDPaciente extends Model {
    protected $table = 'ids_pacientes';
    protected $fillable = ["id_paciente"];

    public static function boot(){
        parent::boot();

        self::creating(function($ev){
            /* @var $ev IDPaciente
             * @var $reciente IDPaciente
             */
            if(is_null($ev->id_paciente) || $ev->id_paciente === '0' || $ev->id_paciente === 0 ){
                return false;
            }
            try{
                $reciente = IDPaciente::where("paciente", $ev->paciente)
                ->where("establecimiento", $ev->establecimiento)
                ->firstOrFail();
            }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){

                return true;
            }

            $reciente->update(["id_paciente" => $ev->id_paciente]);
            return false;
        });

        self::updating(function($ev){
            /* @var $ev IDPaciente
             * @var $reciente IDPaciente
             */
            if(is_null($ev->id_paciente) || $ev->id_paciente === '0' || $ev->id_paciente === 0 ){
                return false;
            }

        });

    }

}
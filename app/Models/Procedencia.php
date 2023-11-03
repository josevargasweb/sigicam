<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Procedencia extends Model {
    protected $table = 'procedencias';


    public static function procedencias(){
        $procedencias = [];
        foreach(Procedencia::all() as $proc){
            $procedencias[$proc->id] = $proc->nombre;
        }
        return $procedencias;
    }
}
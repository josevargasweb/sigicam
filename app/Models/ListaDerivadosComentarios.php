<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ListaDerivadosComentarios extends Model{
    
    protected $table = "lista_derivados_comentarios";
    //public $incrementing =  true;
    public $timestamps = false;
    //protected $primaryKey = 'caso'; // se cambio caso como primary key para poder hacer el find en quitarRecuperacion
    protected $primaryKey = 'id_lista_derivados_comentarios';

}

?>
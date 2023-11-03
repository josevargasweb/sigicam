<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaEnfermeriaControlSignoVital extends Model
{
    public $table = "formulario_hoja_enfermeria_signos_vitales";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;

    public static function graficarSignosVitales($caso){

        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();

        // $frecuencia_respiratoria = HojaEnfermeriaControlSignoVital::where('caso')
        // ->whereBetween('')
        // Ticket::whereBetween('created_at', array($from, $to))
		// 			 ->get();
        return 'oka';
    }
}

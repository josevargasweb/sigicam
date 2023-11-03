<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertenencias extends Model
{
    public $table = "pertenencias";
    protected $primaryKey = 'id';
    protected $fillable = [
        
    ];

    public $timestamps = false;
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *        'Funciones' => App\util\Funciones::class,
        'Debugbar' => Barryvdh\Debugbar\Facade::class,
        'UploadHandler' => App\util\UploadHandler::class,
     * @return void
     */
    public function boot()
    {
        

        if (true) {
            \DB::listen(function ($query) {
               
                //Guardar el log de los SQL en un archivo sql.log
                $log = ['orderId' => $query->sql,
                        'description' => $query->bindings,
                        "time" => $query->time];

                $orderLog = new Logger("order");
                $orderLog->pushHandler(new StreamHandler(storage_path('logs/sql.log')), Logger::INFO);
                $orderLog->info('OrderLog', $log);

                
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

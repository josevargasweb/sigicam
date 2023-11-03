<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
 */
use App\Http\Controllers\EstadisticasAltaController;
use App\Http\Controllers\EstadisticasCamasController;
use App\Http\Controllers\EstadisticasCasoSocialController;
use App\Http\Controllers\EstadisticasDerivacionesController;
use App\Http\Controllers\EstadisticasRiesgoController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\PreAltaController;
use App\Http\Controllers\UsoRestringidoController;
use App\Models\Caso;
use App\Models\Comuna;
use App\Models\Consultas;
use App\Models\DocumentoDerivacionCaso;
use App\Models\Establecimiento;
use App\Models\EvolucionCaso;
use App\Models\Examen;
use App\Models\HistorialDiagnostico;
use App\Models\Paciente;
use App\Models\Procedencia;
use App\Models\Unidad;
use App\Models\UnidadEnEstablecimiento;
use App\User;
use App\Models\AreaFuncional;
use App\Models\ListaDerivados;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Telefono;
use App\Models\DocumentosDiptico;

use Vsmoraes\Pdf\Pdf;

Route::get("testCambioHora", "horaController@testCambioHora");
Route::get('cronCategorizar', 'UrgenciaController@cronCategorizar');

Route::post('getSir', 'OptimizacionController@getSir');

Route::get('/laravel', function () {

    try
    {
        return User::create([
            'name' => "fabian",
            'email' => "fasme@asd.cl",
            'password' => bcrypt("123"),
        ]);
    } catch (Exception $e) {
        return $e;
    }

});

Route::bind('unidad', function ($value, $route) {
    try {
        if ($value == 'todos') {
            return "TODOS";
        }
        return UnidadEnEstablecimiento::where("visible", true)->where('establecimiento', Session::get('idEstablecimiento'))
            ->where('url', $value)
            ->firstOrFail()
            ->url;
    } catch (Exception $e) {
        App::abort(404);
    }
});

Route::bind('id_unidad', function ($value, $route) {
    if ($value == 0) {
        return $value;
    }

    try {
        return UnidadEnEstablecimiento::where("visible", true)->findOrFail($value)->id;
    } catch (Exception $e) {
        App::abort(404);
    }
});

Route::bind('est', function ($value, $route) {
    if ($value == 0) {
        return $value;
    }

    try {
        $found = Establecimiento::findOrFail($value);
        if (Auth::user()->tipo == TipoUsuario::ADMINSS || Auth::user()->tipo == TipoUsuario::MONITOREO_SSVQ || Session::get('idEstablecimiento') == $value) {
            return $value;
        } else {
            throw new Exception("Código inválido");
        }
    } catch (Exception $e) {
        App::abort(404);
    }
});

Route::bind('fecha', function ($value, $route) {
    $d = DateTime::createFromFormat('d-m-Y', $value);
    if ($d && $d->format('d-m-Y') == $value) {
        return $value;
    } else {
        echo App::abort(404);
    }
});

Route::get('/', function () {
    return View::make("Sesion/index");
});
Route::get('acerca', function () {
    return View::make("Sesion/acerca");
});
Route::get('equipo', function () {
    return View::make("Sesion/equipo");
});
Route::get('contacto', function () {
    return View::make("Sesion/contacto");
});

/* Route::get('phpInfo', function () {
    return View::make("Sesion/infoPhp");
}); */


Route::resource('/Pyxis/integracion', 'PyxisController');

/*Inicio Busqueda de camas optimizadas(OPTIMIZACION)*/



Route::post('unidades_funcionales', array('as' => 'unidades_funcionales', 'uses' => 'UnidadController@consulta'));



Route::post('buscarCama', 'OptimizacionController@buscarInformacion');

Route::post('ingresarPacienteOptimizacion', 'OptimizacionController@ingresarPacienteOptimizacion');

Route::post('registrarTrasladoOptimizacion', 'OptimizacionController@registrarTrasladoOptimizacion');

Route::post('notificaciones', 'GestionController@notificaciones');

/*Fin Busqueda de camas optimizadas(OPTIMIZACION)*/

Route::get('genpass', function () {
    $password = Hash::make(Input::get("key"));
    echo $password;
});

Route::get('login', 'SesionController@login');
Route::post('doLogin', 'SesionController@doLogin');
Route::post('enviarCorreoContacto', array('as' => 'enviarCorreoContacto', 'uses' => 'SesionController@enviarCorreoContacto'));
Route::get('cerrarSesion', ['uses' => 'SesionController@cerrarSesion', 'middleware' => 'auth']);


Route::group(array('prefix' => 'index', 'middleware' => 'auth'), function () {
    Route::get("/", ["as" => 'indexPrincipal', "uses" => function () {
        return view("Index/index");
    }]);
});

Route::group(array('prefix' => '/', 'middleware' => 'auth'), function () {

    //categorizacion pacientes con falta de categorizacion
    Route::get('indexNoCategorizados', array('as' => 'indexNoCategorizados', 'uses' => 'AdministracionController@indexNoCategorizados'));
    Route::get('infoPacienteNoCategorizado', array('as' => 'infoPacienteNoCategorizado', 'uses' => 'AdministracionController@infoPacienteNoCategorizado'));
    Route::get('categorizarNoCategorizados', array('as' => 'categorizarNoCategorizados', 'uses' => 'AdministracionController@categorizarNoCategorizados'));
    Route::get('reporteNoCategorizados/{rut}/{reporte}', array('as' => 'reporteNoCategorizados', 'uses' => 'AdministracionController@reporteNoCategorizados'));

    Route::get('menuOpciones', 'IndexController@menuOpciones');
    Route::post('quitarDerivado', "UrgenciaController@quitarDerivado");

    //productos
    Route::get('{query}/consulta_productos', 'BoletinPagoController@consulta_productos');
    Route::get('obtenerListaProductos', 'BoletinPagoController@obtenerListaProductos');
    Route::get('obtenerListaProductosModificados', 'BoletinPagoController@obtenerListaProductosModificados');

    Route::post('ingresarProducto', 'BoletinPagoController@ingresarProducto');
    Route::post('editarProducto', 'BoletinPagoController@editarProducto');
    Route::post('eliminarProducto', 'BoletinPagoController@eliminarProducto');
    Route::get('cargarProducto/{idProducto}', 'BoletinPagoController@cargarProducto');


    Route::get("exportarBoletinPDF/{idCaso}/{fecha}", "BoletinPagoController@exportarBoletinPDF");
    Route::get("exportarBoletinExcel/{idCaso}/{fecha}", "BoletinPagoController@exportarBoletinExcel");
    Route::get("exportarBoletinHistoricoPDF/Historico/{idCaso}", "BoletinPagoController@exportarBoletinHistoricoPDF");
    Route::get("exportarBoletinHistoricoExcel/Historico/{idCaso}", "BoletinPagoController@exportarBoletinHistoricoExcel");
    Route::get("validarFechaBoletin", "BoletinPagoController@validarFechaBoletin");


    /* examenes estudios y procedimientos */
    Route::get("obtenerEEP/{caso}", "GestionController@obtenerEEP");
    Route::post("ingresarExamen", "GestionController@ingresarExamen");
    Route::post("eliminarEEP", "GestionController@eliminarEEP");
    Route::post("modificarExamenImagen", "GestionController@modificarExamenImagen");

    Route::post("sacarListaEEP", "ExamenController@sacarListaEEP");

    /* Soliictar cama */
    Route::get('buscarCamaInteligente', 'OptimizacionController@indexOptimizacion');
    Route::get('IngresarDomiciliaria', 'OptimizacionController@ingresarHospDom');

    /* Asignar cama */
    Route::post('asignarCama', 'GestionController@asignarCama');

    /* comunas */
    Route::post('comunas', array('as' => 'comunas', 'uses' => 'OptimizacionController@comunas'));

    /* Estudio prevalencia: es un informe con los pacientes hospitalizados hasta una determinada hora. Se uso solo 1 vez a peticion de copiapo*/
    Route::get('estudioPrevalencia', 'EstadisticasCamasController@estudioPrevalencia');


    Route::get('graficoCat', 'EstadisticasCamasController@graficoCat');
    Route::get('graficoCategorizados', 'PacientesCategorizados@graficoCategorizados');


    //Formulario Escala Evaluación Riesgo de Ulceras por Presión
    Route::get('formulario/{idCaso}', 'GestionController@formularioRiesgoUlcera');
    Route::post('ingresoRiesgoUlcera', 'RiesgoUlceraController@ingresoRiesgoUlcera')->name('ingresoRiesgoUlcera');
    Route::get('formulario/{idCaso}/historialRiesgoUlcera', array('as' => 'historialRiesgoUlcera', 'uses' => 'RiesgoUlceraController@historialRiesgoUlcera'));
    Route::post('buscarHistorialriesgoUlceras', 'RiesgoUlceraController@buscarHistorialriesgoUlceras')->name('buscarHistorialRiesgoUlceras');
    Route::get('editarRiesgoUlceras/{idForm}', 'RiesgoUlceraController@edit')->name('editarRiesgoUlceras');
    Route::get('pdfHistorialriesgoUlcera/{caso}', array('as' => 'pdfHistorialriesgoUlcera', 'uses' => 'RiesgoUlceraController@pdfHistorialriesgoUlcera'));

    //rutas documentos derivacion
    Route::post("fileupload", "GestionController@fileupload");
    Route::post("ingresarDoducmentoDerivacion", "GestionController@ingresarDoducmentoDerivacion");
    Route::post("quitarDocumentoDerivacion/{id}", "GestionController@quitarDocumentoDerivacion");
    Route::get('descargarDocumento/{ruta}', function ($ruta) {
        $path = public_path() . "/archivos/" . $ruta;
        return response()->download($path);
    });    

    //Solicitudes de traslado interno
    Route::group(['prefix' => 'trasladoInterno'], function () {
        Route::get("recibidas", "TrasladoInternoController@recibidas");
        Route::get("enviadas", "TrasladoInternoController@enviadas");
        Route::get("getTableRecibidas/{motivo}", "TrasladoInternoController@getTableRecibidas");
        Route::post("asignarTraslado","TrasladoInternoController@asignarTraslado");
        Route::post("rechazarTraslado","TrasladoInternoController@rechazarTraslado");
        Route::get("getTableEnviadas/{motivo}", "TrasladoInternoController@getTableEnviadas");
        Route::post("confirmarTraslado","TrasladoInternoController@confirmarTraslado");
        Route::post("cancelarTraslado","TrasladoInternoController@cancelarTraslado");
    });

    Route::post('solicitarTrasladoInterno', 'TrasladoInternoController@solicitarTrasladoInterno');
    Route::post('confirmarTI', 'TrasladoInternoController@confirmarTI');

    /* Validacion indicaciones */
    Route::get('validarFechaIndicacion', 'GestionMedicaController@validarFechaIndicacion')->name('validarFechaIndicacion');
    Route::get('validarFechaIndicacionActualizar', 'GestionMedicaController@validarFechaIndicacionActualizar')->name('validarFechaIndicacionActualizar');

    /* Recien nacido */
    Route::get('obtenerEgresosRn', 'EgresosRecienNacidoGinecoController@obtenerEgresosRn');
    Route::post('egresarRecienNacido', 'EgresosRecienNacidoGinecoController@egresarRecienNacido');
    Route::put('eliminarRecienNacido', 'EgresosRecienNacidoGinecoController@eliminarRecienNacido');
    Route::put('actualizarRecienNacido', 'EgresosRecienNacidoGinecoController@actualizarRecienNacido');

    /* Validar Caso encriptado */
    Route::get('validarCaso', 'CasoController@validarCaso');
    
});

//Pre alta
Route::post("enviarPreAlta","PreAltaController@enviarPreAlta");


Route::get("vistaDocumentos", "DocumentosController@vistaDocumentos");
Route::get("listaDocumentos", "DocumentosController@listaDocumentos");


Route::get('index1', ['uses' => 'IndexController@index']);
Route::get("reporteUsoDeCamas", 'IndexController@reporteUsoDeCamas');
Route::get("resumenCamasIndex",'IndexController@resumenCamasIndex');
Route::get('graficoBloqueadas', 'EstadisticasCamasController@graficoBloqueadas');

//muestra alerta de los pacientes
Route::get('alertaPacienteEspera', 'IndexController@alertaPacienteEspera');

Route::get('rnInfo', array('as' => 'rnInfo', 'uses' => 'GestionController@rnInfo'));

Route::get('categorizacion', 'IndexController@categorizacion');

Route::group(["prefix" => "some"], function () {
    Route::post('actualizar_mapa', ["as" => 'actualizarMapa', "uses" => function () {
        return (new SOMEController)->actualizarMapa(Input::get("mapa"));
    }]);
});
Route::get('{query}/categorias_cie10', array('as' => 'categorias_cie10', 'uses' => 'CIE10Controller@consulta'));

Route::get('{query}/categorias_cie101', array('as' => 'categorias_cie101', 'uses' => 'CIE10Controller@consulta_categoria'));
Route::get('{query}/consulta_medicos', 'MedicoController@consulta_medicos');
Route::get('{query}/consulta_medicos_nombre', 'MedicoController@consulta_medicos_nombre');
Route::get('{query}/consulta_medicos_rut', 'MedicoController@consulta_medicos_rut');
Route::get('{query}/consulta_medicos_rut_completo', 'MedicoController@consulta_medicos_rut_completo');
Route::get('{query}/consulta_medicos_completo', 'MedicoController@consulta_medicos_completo');





Route::get('{query}/consulta_areasFuncionales', 'AreaFuncionalController@consulta_areasFuncionales');
Route::post('getAreaFuncionalPorServicio', 'AreaFuncionalController@getAreaFuncionalPorServicio')->name("getAreaFuncionalPorServicio");
Route::post('getComplejidadPorRiesgo', 'ComplejidadservicioController@getComplejidadPorRiesgo');

Route::group(array('prefix' => 'unidad', 'middleware' => 'auth'), function () {
    Route::get('{unidad}', array('as' => 'unidad', 'uses' => 'GestionController@camas'));
    Route::post('{unidad}/getCamas', 'GestionController@getCamas');
    Route::post('intercambiar', array('as' => 'intercambiar_unidad', 'uses' => 'GestionController@intercambiar'));
    //Route::post('{unidad}/getCamas', 'GestionController@getCamas');
    Route::post('{unidad}/obtenerCamasLista', 'GestionController@obtenerCamasLista');
    Route::post('{unidad}/getCamasDisponiblesVerdes', 'GestionController@getCamasDisponiblesVerdes');
    Route::post('bloquearCama', 'GestionController@bloquearCama');
    Route::post('desbloquearCama', 'GestionController@desbloquearCama');
    Route::post('obtenerMensajeBloqueo', 'GestionController@obtenerMensajeBloqueo');
    Route::post('getSalas', 'GestionController@getSalas');
    Route::post('reconvertir', 'GestionController@reconvertir');
    Route::post('reconvertirOriginal', 'GestionController@reconvertirOriginal');
    Route::get('{unidad}/exportar', 'GestionController@exportar');
    Route::get('{unidad}/exportarpacientes', 'GestionController@exportarpacientes');
    Route::get('{unidad}/exportarPdf', 'GestionController@exportarPdf');
    Route::get('{unidad}/exportarpacientesPdf', 'GestionController@exportarpacientesPdf');
    Route::get('{unidad}/exportarExcelListaEspera', 'GestionController@exportarExcelListaEspera');
    Route::get('{unidad}/exportarPdfListaEspera', 'GestionController@exportarPdfListaEspera');
    Route::post('selector_categorizacion', ["as" => 'selector_categorizacion', 'uses' => "GestionController@selectorCategorizacion"]);
    Route::post('ingresarEsperaAmbulancia', [
        'as' => 'ingresarEsperaAmbulancia',
        'uses' => 'InformacionController@ingresarEsperaAmbulancia',
    ]);
    Route::post('datosParaDerivacion', "UrgenciaController@datosParaDerivacion");
    Route::post('enviarDerivado', "UrgenciaController@enviarDerivado");
    Route::post('enviarPabellon', "UrgenciaController@enviarPabellon");
    Route::post('quitarPabellonCamas', "UrgenciaController@quitarPabellonCamas");

    Route::post('AltaSinLiberarCama', 'GestionController@AltaSinLiberarCama');

    Route::post('descripcionCamas', 'GestionController@descripcionCamas');
    Route::post('cambiarDescripcion', 'GestionController@cambiarDescripcion');


});


Route::group(array('prefix' => 'administracionUnidad', 'middleware' => 'auth'), function () {
    Route::get('unidad/{id}', array('as' => 'verunidad', 'uses' => 'AdministrarUnidadController@unidad'));
    Route::get('editarUnidad/{idEstab}/{idUnidad}', array('as' => 'editarUnidad', 'uses' => 'AdministrarUnidadController@editarUnidadView'));
    Route::get('obtenerUnidades/{id}', array('as' => 'obtenerUnidades', 'uses' => 'AdministrarUnidadController@obtenerUnidades'));
    Route::post('unidad/crearUnidad', array('as' => 'crearUnidad', 'uses' => 'AdministrarUnidadController@crearUnidad'));
    Route::post('updateUnidad', array('as' => 'updateUnidad', 'uses' => 'AdministrarUnidadController@updateUnidad'));
    Route::get('obtenerSalasCamas/{idEstab}/{idUnidad}', array('as' => 'obtenerSalasCamas', 'uses' => 'AdministrarUnidadController@obtenerSalasCamas'));
    Route::post('bloquearSala', array('as' => 'bloquearSala', 'uses' => 'AdministrarUnidadController@bloquearSala'));
    Route::post('desbloquearSala', array('as' => 'desbloquearSala', 'uses' => 'AdministrarUnidadController@desbloquearSala'));
    Route::post('crearSala', array('as' => 'crearSala', 'uses' => 'AdministrarUnidadController@crearSala'));
    Route::post('updateNombreSala', array('as' => 'updateNombreSala', 'uses' => 'AdministrarUnidadController@updateNombreSala'));
    Route::post('borrarSala', array('as' => 'borrarSala', 'uses' => 'AdministrarUnidadController@borrarSala'));
    Route::post('agregarCamas', array('as' => 'agregarCamas', 'uses' => 'AdministrarUnidadController@agregarCamas'));
    Route::get('editarSala/{idSala}/{idEstab}/{idUnidad}', array('as' => 'editarSala', 'uses' => 'AdministrarUnidadController@editarSalaView'));
    Route::get('obtenerCamasVigentes/{idSala}', array('as' => 'obtenerCamasVigentes', 'uses' => 'AdministrarUnidadController@obtenerCamasVigentes'));
    Route::post('cambiarNombreCama', array('as' => 'cambiarNombreCama', 'uses' => 'AdministrarUnidadController@cambiarNombreCama'));
    Route::post('bloquearCama', array('as' => 'bloquearCama', 'uses' => 'AdministrarUnidadController@bloquearCama'));
    Route::post('eliminarCama', array('as' => 'eliminarCama', 'uses' => 'AdministrarUnidadController@eliminarCama'));
    Route::post('desbloquearCama', array('as' => 'desbloquearCama', 'uses' => 'AdministrarUnidadController@desbloquearCama'));
    Route::post('cancelarCama', array('as' => 'cancelarCama', 'uses' => 'AdministrarUnidadController@cancelarCama'));
    Route::get('obtenerCamasEliminadas/{idSala}', array('as' => 'obtenerCamasEliminadas', 'uses' => 'AdministrarUnidadController@obtenerCamasEliminadas'));
    Route::get('obtenerCamasBloqueadas/{idSala}', array('as' => 'obtenerCamasBloqueadas', 'uses' => 'AdministrarUnidadController@obtenerCamasBloqueadas'));
    Route::get('obtenerServiciosOfrecidos/{idEstab}/{idUnidad}', array('as' => 'obtenerServiciosOfrecidos', 'uses' => 'AdministrarUnidadController@obtenerServiciosOfrecidos'));
    Route::get('obtenerServiciosRecibidos/{idEstab}/{idUnidad}', array('as' => 'obtenerServiciosRecibidos', 'uses' => 'AdministrarUnidadController@obtenerServiciosRecibidos'));
    Route::get('editarServicioView/{idEstab}/{idUnidad}/{servicio}', array('as' => 'editarServicioView', 'uses' => 'AdministrarUnidadController@editarServicioView'));
    Route::get("obtenerCama", ["as" => 'obtenerInfoCama', "uses" => "AdministrarUnidadController@obtenerCama"]);
    Route::post('unidad/crearArea', array('as' => 'crearArea', 'uses' => 'AdministrarUnidadController@crearAreaFuncional'));
    Route::get('obtenerTodasAreasFuncionales', array('as' => 'obtenerTodasAreasFuncionales', 'uses' => 'AdministrarUnidadController@obtenerTodasAreasFuncionales'));
    Route::post('todasAreasFuncionales', array('as' => 'todasAreasFuncionales', 'uses' => 'AdministrarUnidadController@todasAreasFuncionales'));
	Route::post('areasFuncionalesEstablecimientoOrdenadas', array('as' => 'areasFuncionalesEstablecimientoOrdenadas', 'uses' => 'AdministrarUnidadController@areasFuncionalesEstablecimientoOrdenadas'));
		Route::post('guardarOrdenAreasFuncionales', array('as' => 'guardarOrdenAreasFuncionales', 'uses' => 'AdministrarUnidadController@guardarOrdenAreasFuncionales'));
    Route::post('updateAreaFuncional', array('as' => 'updateAreaFuncional', 'uses' => 'AdministrarUnidadController@updateAreaFuncional'));
});

Route::group(array('prefix' => 'administracion', 'middleware' => 'auth'), function () {

    Route::get('establecimientos', array('before' => 'ADMINSS', 'as' => 'gestionEstablecimientos', 'uses' => 'AdministracionController@gestionEstablecimientosAdm'));
    Route::get('/', array('as' => 'buscador', 'uses' => 'AdministracionController@gestionBuscarAdm'));
    Route::post('editarEstablecimiento', array('before' => 'ADMINSS', 'as' => 'editarEstablecimiento', 'uses' => 'AdministracionController@editarEstablecimiento'));
    Route::get('obtenerEstablecimientos', array('as' => 'obtenerEstablecimientos', 'uses' => 'AdministracionController@obtenerEstablecimientos'));
    Route::get('camas', array('before' => 'ADMINSS', 'as' => 'gestionCamas', 'uses' => 'AdministracionController@gestionCamasAdm'));
    Route::get('salas', array('before' => 'ADMINSS', 'as' => 'gestionSalas', 'uses' => 'AdministracionController@gestionSalasAdm'));
    Route::get('servicios', array('before' => 'ADMINSS', 'as' => 'gestionServicios', 'uses' => 'AdministracionController@gestionServiciosAdm'));
    Route::get('unidades', array('before' => 'ADMINSS', 'as' => 'gestionUnidad', 'uses' => 'AdministrarUnidadController@viewUnidad'));
    Route::post('getUnidades', array('as' => 'getUnidades', 'uses' => 'AdministrarUnidadController@getUnidades'));
    Route::post('crearUnidad', array('before' => 'ADMINSS', 'as' => 'crearUnidad', 'uses' => 'AdministrarUnidadController@crearUnidad'));
    Route::get('editar', array('before' => 'ADMINSS', 'as' => 'editar', 'uses' => 'AdministrarUnidadController@editarView'));

    Route::get('buscador', array('before' => 'ADMINSS', 'as' => 'gestionBuscar', 'uses' => 'BuscadorController@gestionBuscarAdm'));


    Route::get('gestionIaas', array('as' => 'gestionIaas', 'uses' => 'GestionController@gestionIaas'));
    Route::get('agregarLocalizacion', array('as' => 'agregarLocalizacion', 'uses' => 'GestionController@agregarLocalizacion'));
    Route::get('agregarInvasivo', array('as' => 'agregarInvasivo', 'uses' => 'GestionController@agregarInvasivo'));
    Route::get('agregarEtiologia', array('as' => 'agregarEtiologia', 'uses' => 'GestionController@agregarEtiologia'));
    Route::get('agregarCaracteristicaAgente', array('as' => 'agregarCaracteristicaAgente', 'uses' => 'GestionController@agregarCaracteristicaAgente'));

    Route::post('editarNombre', array('before' => 'ADMINSS', 'as' => 'editarNombre', 'uses' => 'AdministrarUnidadController@updateNombre'));
    Route::post('updateServicios', array('before' => 'ADMINSS', 'as' => 'updateServicios', 'uses' => 'AdministrarUnidadController@updateServicios'));
    Route::post('updateServiciosRecibidos', array('before' => 'ADMINSS', 'as' => 'updateServiciosRecibidos', 'uses' => 'AdministrarUnidadController@updateServiciosRecibidos'));

    Route::post('getSalas', array('as' => 'getSalas', 'uses' => 'AdministrarUnidadController@getSalas'));

    Route::post('getCamas', array('as' => 'getCamas', 'uses' => 'AdministrarUnidadController@getCamas'));
    Route::post('idSalaUnico', array('before' => 'ADMINSS', 'as' => 'idSalaUnico', 'uses' => 'AdministrarUnidadController@idSalaUnico'));
    Route::post('editarSala', array('before' => 'ADMINSS', 'as' => 'editarSala', 'uses' => 'AdministrarUnidadController@editarSala'));
    Route::post('getSalasSelect', array('as' => 'getSalasSelect', 'uses' => 'AdministrarUnidadController@getSalasSelect'));
    Route::post('editarCama', array('before' => 'ADMINSS', 'as' => 'editarCama', 'uses' => 'AdministrarUnidadController@editarCama'));
    Route::post('crearCama', array('before' => 'ADMINSS', 'as' => 'crearCama', 'uses' => 'AdministrarUnidadController@crearCama'));

    //Boletin de pago
    //Administracion
    Route::get('gestionBoletinPago', array('as' => 'gestionBoletinPago', 'uses' => 'BoletinPagoController@gestionBoletinPago'));



    Route::get('gestionUsuario', array('as' => 'gestionUsuario', 'uses' => 'AdministrarUsuarioController@formUsuario'));
    Route::post('registrarUsuario', array('before' => 'ADMINSS', 'as' => 'registrarUsuario', 'uses' => 'AdministrarUsuarioController@registrarUsuario'));
    Route::post('desactivarUsuario', array('before' => 'ADMINSS', 'as' => 'desactivarUsuario', 'uses' => 'AdministrarUsuarioController@deshabilitarUsuario'));
    Route::post('activarUsuario', array('before' => 'ADMINSS', 'as' => 'activarUsuario', 'uses' => 'AdministrarUsuarioController@habilitarUsuario'));

    //medicos
    Route::get('gestionMedicos', array('as' => 'gestionMedicos', 'uses' => 'MedicoController@indexMedicos'));
    Route::post('deshabilitarMedico', array('before' => 'ADMINSS', 'as' => 'deshabilitarMedico', 'uses' => 'MedicoController@deshabilitarMedico'));
    Route::post('habilitarMedico', array('before' => 'ADMINSS', 'as' => 'habilitarMedico', 'uses' => 'MedicoController@habilitarMedico'));
    Route::get('editarMedico/{id}', array('as' => 'editarMedico', 'uses' => 'MedicoController@editarMedico'));
    Route::post('actualizarDatosMedico', array('as' => 'actualizarDatosMedico', 'uses' => 'MedicoController@actualizarDatosMedico'));
    Route::post('registrarMedico', array('before' => 'ADMINSS', 'as' => 'registrarMedico', 'uses' => 'MedicoController@registrarMedico'));

    //productos
    Route::get('gestionProductos', array('as' => 'gestionProductos', 'uses' => 'ProductoController@indexProductos'));
    Route::post('deshabilitarProducto', array('before' => 'ADMINSS', 'as' => 'deshabilitarProducto', 'uses' => 'ProductoController@deshabilitarProducto'));
    Route::post('habilitarProducto', array('before' => 'ADMINSS', 'as' => 'habilitarProducto', 'uses' => 'ProductoController@habilitarProducto'));
    Route::get('editarProducto/{id}', array('as' => 'editarProducto', 'uses' => 'ProductoController@editarProducto'));
    Route::get('actualizarProducto/{id}', array('as' => 'actualizarProducto', 'uses' => 'ProductoController@actualizarProducto'));
    Route::post('actualizarValorProducto', array('as' => 'actualizarValorProducto', 'uses' => 'ProductoController@actualizarValorProducto'));
    Route::post('actualizarDatosProducto', array('as' => 'actualizarDatosProducto', 'uses' => 'ProductoController@actualizarDatosProducto'));
    Route::post('registrarProducto', array('before' => 'ADMINSS', 'as' => 'registrarProducto', 'uses' => 'ProductoController@registrarProducto'));

    Route::get('cambiarContraseña', array('as' => 'cambiarContraseña', 'uses' => function () {
        return View::make("Administracion/CambiarPassword");
    }));
    Route::get('Alerta', array('as' => 'Alerta', 'uses' => 'IndexController@Alerta'));
    Route::post('mismaPassword', array('as' => 'mismaPassword', 'uses' => 'AdministrarUsuarioController@mismaPassword'));
    Route::post('cambiarPassword', array('as' => 'cambiarPassword', 'uses' => 'AdministrarUsuarioController@cambiarPassword'));
    //actualizar fecha clave usuario
    Route::get('actualizarFechaClave', array('as' => 'actualizarFechaClave', 'uses' => 'SesionController@actualizarFechaClave'));
    //

    Route::get('editarUsuario/{id}', array('as' => 'editarUsuario', 'uses' => 'AdministrarUsuarioController@editarUsuario'));
    Route::post('registrarCambioUsuario', array('as' => 'registrarCambioUsuario', 'uses' => 'AdministrarUsuarioController@registrarCambioUsuario'));
    Route::get('cargarRestricciones', array('as' => 'cargarRestricciones', 'uses' => 'AdministrarUsuarioController@cargarRestricciones'));
});

Route::group(['prefix' => 'derivaciones', 'before' => 'auth|NOT_DIRECTOR|NOT_MEDICO_JEFE_DE_SERVICIO|NOT_MONITOREO_SSVQ|NOT_GESTION_CLINICA|NOT_ENFERMERA_P|NOT_USUARIO'], function () {
    Route::get('{recibidas}/{registros?}', 'DerivacionController@tablaDerivaciones');
    Route::post('getCamas', 'DerivacionController@getCamasDisponibles');
    Route::post('reservarPendiente', 'DerivacionController@reservarPendiente');
    Route::post('cambiarDestino', 'DerivacionController@cambiarDestino');
});

Route::post('getInfectados', 'GestionController@getInfectados');

Route::get('obtenerFechaIAAS2/{IaasFecha}', array('uses' => function ($IaasFecha) {
    $datas = EstadisticaInfecciones::obtenerListaFechaIAAS($IaasFecha);
    Session::flash("datos_xls", $datas);
    return View::make("Estadisticas/ExportarIAASFecha", ["data" => $datas,
    ]);

}));

Route::get('enviarCorreoCenso', 'EstadisticasCamasController@enviarCorreoCenso');

Route::post('obtenerFechaIAAS2/obtenerFechaIAAS', array('as' => 'obtenerFechaIAAS3', 'uses' => 'EstadisticasIAASController@MostrarIAASFecha'));

Route::group(array('prefix' => 'estadisticas', 'middleware' => 'auth'), function () {

    Route::get('informacionREMCamas/{anno}/{mes}', array( 'uses' => 'EstadisticasController@informacionREMCamas'));
    Route::get('informacionREM', array('as' => 'informacionREM', 'uses' => 'EstadisticasController@informacionREM'));
    Route::get('descargarInformeRem/{anno}/{mes}',  'EstadisticasController@PDFinformacionREM');
    Route::get('descargarExcelRem/{anno}/{mes}',  'EstadisticasController@descargarExcelRem');

    Route::get('obtenerListaIAAS', array('as' => 'obtenerListaIAAS', 'uses' => 'EstadisticasIAASController@MostrarListaIAAS'));
    Route::post('obtenerFechaIAAS', array('as' => 'obtenerFechaIAAS', 'uses' => 'EstadisticasIAASController@MostrarIAASFecha'));
    Route::post('obtenerFechaIAAS2/obtenerFechaIAAS', array('as' => 'obtenerFechaIAAS3', 'uses' => 'EstadisticasIAASController@MostrarIAASFecha'));
    Route::post('generar', array('as' => 'generarExceliaas', 'uses' => 'EstadisticasIAASController@generar'));
    Route::get("getUnidades", "EstadisticasCamasController@getUnidades");

    Route::get('exportarpacientesUrgencias', 'GestionController@exportarpacientesUrgencias');
    Route::get('exportarpacientesUrgenciasPdf', 'GestionController@exportarpacientesUrgenciasPdf');
    //Nuevas Estadisticas

    //Fin nuevas Estadisicas

    Route::group(array('prefix' => 'camas'), function () {

        //nuevas Estadisticas

        Route::get('estDirector', array('as' => 'estDirector', 'uses' => 'EstadisticasCamasController@estDirector'));

        Route::get('estEstada', array('as' => 'estEstada', 'uses' => 'EstadisticasCamasController@estEstada'));
        Route::get('estCamaBloqueada', array('as' => 'estCamaBloqueada', 'uses' => 'EstadisticasCamasController@estCamaBloqueada'));

        Route::get('estknox', array('as' => 'estKnox', 'uses' => 'EstadisticasCamasController@knox'));
        Route::get('estDistEspacial', array('as' => 'estDistEspacial', 'uses' => 'EstadisticasCamasController@distribucionEspacial'));
        Route::get('estKmeans', array('as' => 'estKmeans', 'uses' => 'EstadisticasCamasController@kmeans'));
        Route::get('estSir', array('as' => 'estSir', 'uses' => 'EstadisticasCamasController@Sir'));
        Route::get('censoDiario', array('as' => 'censoDiario', 'uses' => 'EstadisticasCamasController@censoDiario'));

        Route::post('calcularKnox', array('as' => 'calcularKnox', 'uses' => 'EstadisticasCamasController@calcularKnox'));
        Route::post('calcularDistEsp', array('as' => 'calcularDistEsp', 'uses' => 'EstadisticasCamasController@calcularDistEsp'));
        Route::post('calcularKmeans', array('as' => 'calcularKmeans', 'uses' => 'EstadisticasCamasController@calcularKmeans'));
        Route::get('regresion', array('as' => 'regresion', 'uses' => 'EstadisticasCamasController@regresion'));
        Route::get('randomForest', array('as' => 'randomForest', 'uses' => 'EstadisticasCamasController@randomForest'));
        Route::get('aplicarRegresion', "EstadisticasCamasController@aplicarRegresion");
        Route::get('aplicarRandom', "EstadisticasCamasController@aplicarRandom");

        Route::get('estadisticasPacientesGeneral', array('as' => 'estadisticasPacientesGeneral', 'uses' => 'EstadisticasCamasController@estadisticasPacientesGeneral'));
        Route::get('estadisticaEstada', array('as' => 'estadisticaEstada', 'uses' => 'EstadisticasCamasController@estadisticaEstada'));
        Route::post('estadisticaEstadaTotal', array('as' => 'estadisticaEstadaTotal', 'uses' => 'EstadisticasCamasController@estadisticaEstadaTotal'));
        Route::get('estadisticaEstadaReporte/{dias}/{reporte}', "EstadisticasCamasController@estadisticaEstadaReporte");

        Route::get('estadisticaCambioTurno/{fecha}', array('as' => 'estadisticaCambioTurno', 'uses' => 'EstadisticasCamasController@estadisticaCambioTurno'));

        Route::get('estCamasBloqueadas', array('as' => 'estCamasBloqueadas', 'uses' => 'EstadisticasCamasController@estCamasBloqueadas'));
        Route::get('camasBloqueadasExcel', array('as' => 'camasBloqueadasExcel', 'uses' => 'EstadisticasCamasController@camasBloqueadasExcel'));
        Route::get('camasBloqueadasPdf', array('as' => 'camasBloqueadasPdf', 'uses' => 'EstadisticasCamasController@camasBloqueadasPdf'));

        //Fin nuevas Estadisticas

        Route::get('/', array('as' => 'estCamas', 'uses' => 'EstadisticasCamasController@pagina'));
        Route::get('datos/{fecha}', array('as' => 'totalCamas', 'uses' => function ($fecha, Vsmoraes\Pdf\Pdf $a) {
            if (Auth::user()->tipo != TipoUsuario::ADMINSS && Auth::user()->tipo != TipoUsuario::MONITOREO_SSVQ) {
                return Redirect::route("datosCamas", array($fecha, Session::get("idEstablecimiento")));
            } else {
                return (new EstadisticasCamasController($a, $a, $a, $a, $a, $a))
                    ->reporteTotal(\Carbon\Carbon::now(), \Carbon\Carbon::createFromFormat("d-m-Y", $fecha));
            }
        }));

        Route::get('datos/{fecha}/{est}', array('as' => 'datosCamas', 'uses' => function ($fecha, $est, Vsmoraes\Pdf\Pdf $a) {
            return (new EstadisticasCamasController($a, $a, $a, $a, $a, $a))->reporteEstablecimiento(\Carbon\Carbon::now(), \Carbon\Carbon::createFromFormat("d-m-Y", $fecha), $est);
        }));

        Route::get('datos/{fecha}/{est}/{id_unidad}', function ($fecha, $est, $id_unidad, Vsmoraes\Pdf\Pdf $a) {

            if ($id_unidad == 0 || $id_unidad == null) {

                return Redirect::route("datosCamas", array("fecha" => $fecha, "est" => $est));
            } else {
                return (new EstadisticasCamasController($a, $a, $a, $a, $a, $a))
                    ->reporteUnidad(\Carbon\Carbon::now(), \Carbon\Carbon::createFromFormat("d-m-Y", $fecha), $est, $id_unidad);
            }
        });

    });

    Route::group(array('prefix' => 'derivaciones', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estDerivaciones', 'uses' => 'EstadisticasDerivacionesController@pagina'));

        Route::get('datos', ["as" => "estDerivacionesDatos", "uses" => function (Request $request) {

            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha"))->endOfDay();
            $estab = $request->input("estab");
            $user = Auth::user();

            if ($estab === '' || $estab == 0) {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasDerivacionesController())->reporteTotal($fecha_inicio, $fecha);
                }
            }

            return (new EstadisticasDerivacionesController())->reporteEstablecimiento($fecha_inicio, $fecha, $estab);
        }]);
    });

    Route::group(array('prefix' => 'estEstadiaCamasPaciente', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estEstadiaCamasPaciente', 'uses' => 'EstadisticasCamasController@estadisticaEstadiaYCamas'));

        Route::post('datos', array('as' => 'datos', 'uses' => 'EstadisticasCamasController@datosEstadiaYCamas'));
    });

    Route::group(array('prefix' => 'estadiaDiagnostico', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estDiagnostico', 'uses' => 'EstadisticasCamasController@estadisticaDiagnostico'));

        Route::post('datos', array('as' => 'datos', 'uses' => 'EstadisticasCamasController@datosDiagnostico'));
    });

    Route::group(array('prefix' => 'casoSocial', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estCasoSocial', 'uses' => 'EstadisticasCasoSocialController@pagina'));
        Route::get('datos', ["as" => "estCasoSocialDatos", "uses" => function (Request $request) {

            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha"))->endOfDay();
            $estab = $request->input("estab");
            $user = Auth::user();

            if ($estab === '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasCasoSocialController())->reporte($fecha_inicio, $fecha);
                }
            }
            return (new EstadisticasCasoSocialController())->reporte($fecha_inicio, $fecha, $estab);
        }]);
    });

    Route::group(array('prefix' => 'riesgo', 'middleware' => 'auth'), function () {

        Route::get("/", array('as' => 'estRiesgo', 'uses' => 'EstadisticasRiesgoController@pagina'));
        Route::get("datos", ["as" => "estRiesgoDatos", "uses" => function (Request $request) {

            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha"))->endOfDay();
            $estab = $request->input("estab");
            $user = Auth::user();

            if ($estab == '0' || $estab == '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasRiesgoController())->reporte($fecha_inicio, $fecha, 0);
                }
            }

            return (new EstadisticasRiesgoController())->reporte($fecha_inicio, $fecha, $estab);
        }]);

    });

    Route::group(array('prefix' => 'listaEspera', 'middleware' => 'auth'), function () {
        Route::get("/", array('as' => 'reporteListaEspera', 'uses' => 'EstadisticasCamasController@reporteListaEspera'));
        Route::get("datos", array('as' => 'ListaEsperaDatos', 'uses' => 'EstadisticasCamasController@ListaEsperaDatos'));
    });

    Route::group(array('prefix' => 'listaTransito', 'middleware' => 'auth'), function () {
        Route::get("/", array('as' => 'reporteListaTransito', 'uses' => 'EstadisticasCamasController@reporteListaTransito'));
        Route::get("datos", array('as' => 'ListaTransitoDatos', 'uses' => 'EstadisticasCamasController@ListaTransitoDatos'));
    });

    Route::get("reporteDocumentoDerivacion", array("as" => "reporteDocumentoDerivacion", "uses" => "EstadisticasCamasController@reporteDocumentoDerivacion"));
    Route::get("informeDerivacion", array("as" => "informeDerivacion", "uses" => "EstadisticasCamasController@informeDerivacion"));
    Route::get("informeDerivacionDatos", "EstadisticasCamasController@informeDerivacionDatos");
    Route::get("informePromedioSolicitudAsignacion", array("as" => "informePromedioSolicitudAsignacion", "uses" => "EstadisticasCamasController@informePromedioSolicitudAsignacion"));
    Route::get("informePromedioSolicitudAsignacionDatos", array("as" => "informePromedioSolicitudAsignacionDatos", "uses" => "EstadisticasCamasController@informePromedioSolicitudAsignacionDatos"));

    Route::get("informeListaEspera", array("as" => "informeListaEspera", "uses" => "EstadisticasCamasController@informeListaEspera"));

    Route::get("informeDiagnosticos", array("as" => "informeDiagnosticos", "uses" => "EstadisticasCamasController@informeDiagnosticos"));

    Route::get("informeHospitalizacionDomiciliaria", array("as" => "informeHospitalizacionDomiciliaria", "uses" => "EstadisticasCamasController@informeHospitalizacionDomiciliaria"));

    Route::get("informeMensualCateg", array("as" => "informeMensualCateg", "uses" => "EstadisticasCamasController@informeMensualCateg"));
    Route::get("informeMensualCategDatos", array("as" => "informeMensualCategDatos", "uses" => "EstadisticasCamasController@informeMensualCategDatos"));
    Route::get("reporteUrgencias", array("as" => "reporteUrgencias", "uses" => "EstadisticasCamasController@reporteUrgencias"));

    //Reporte de urgencias
    Route::get("reporteDeUrgencias", array("as" => "reporteUrgencias2", "uses" => "EstadisticasCamasController@reporteUrgencias2"));
    Route::get("salidaUrgencias/{fecha}", "EstadisticasCamasController@salidaUrgencias");
    Route::get("estadiaUrgencias/{mes}/{anno}", "EstadisticasCamasController@estadiaUrgencias");


    Route::get("listaPacientesUrgencia", "UrgenciaController@obtenerListaPaciente");
    Route::post("reporteUrgenciasGeneral", array("as" => "reporteUrgenciasGeneral", "uses" => "EstadisticasCamasController@reporteUrgenciasGeneral"));


    Route::get("reporteMensualEstadistico", array("as" => "reporteMensualEstadistico", "uses" => "EstadisticasController@reporteMensualEstadistico"));
    Route::get("reporteDotacionEnfermeria", array("as" => "reporteDotacionEnfermeria", "uses" => "EstadisticasController@reporteDotacionEnfermeria"));

    Route::get("reportePacienteEspera", array("as" => "reportePacienteEspera", "uses" => "EstadisticasCamasController@reportePacienteEspera"));
    Route::get("reporteRiesgoCategorizacion", array("as" => "reporteRiesgoCategorizacion", "uses" => "EstadisticasCamasController@reporteRiesgoCategorizacion"));

    Route::get("pacientesD2D3Datos", "EstadisticasCamasController@pacientesD2D3Datos");

    Route::get("reporteOtrasRegiones", array("as" => "reporteOtrasRegiones", "uses" => "EstadisticasCamasController@reporteOtrasRegiones"));
    Route::get("otrasRegionesDatos", "EstadisticasCamasController@otrasRegionesDatos");
    Route::get("reporteEspecialidades", array("as" => "reporteEspecialidades", "uses" => "EstadisticasCamasController@reporteEspecialidades"));
    //Route::get('excelReporteEspecialidades', array('as' => 'excelReporteEspecialidades', 'uses' => 'EstadisticasCamasController@excelReporteEspecialidades'));
    Route::get('excelReporteEspecialidades/{anno}/{mes}/{tipo}',  'EstadisticasCamasController@excelReporteEspecialidades');
    Route::group(array('prefix' => 'IngresosYEgresos', 'middleware' => 'auth'), function () {

        Route::get("/", array('as' => 'estAlta', 'uses' => 'EstadisticasAltaController@pagina'));
        Route::get("datos", ["as" => "estAltaDatos", "uses" => function (Request $request) {
            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha"))->endOfDay();
            $estab = $request->input("estab");
            $user = Auth::user();

            if ($estab == '0' || $estab == '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasAltaController())->reporteAdminSS($fecha_inicio, $fecha);
                }
            }

            return (new EstadisticasAltaController())->reporte($fecha_inicio, $fecha, $estab);
        }]);

        Route::get("datosIngresos", ["as" => "estAltaDatos", "uses" => function (Request $request) {
            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", $request->input("fecha"))->endOfDay();
            $estab = $request->input("estab");
            $user = Auth::user();

            if ($estab == '0' || $estab == '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasAltaController())->reporteIngresosAdminSS($fecha_inicio, $fecha);
                }
            }

            return (new EstadisticasAltaController())->reporteIngresos($fecha_inicio, $fecha, $estab);
        }]);

        //Informes de ingreso
        Route::get("pdfInformeIngresos/{inicio}/{fin}/{estab}", "EstadisticasAltaController@pdfInformeIngresos");
        Route::get("excelInformeIngresos/{inicio}/{fin}/{estab}", "EstadisticasAltaController@excelInformeIngresos");
        //Informes de egresos
        Route::get("informeEgreso/{inicio}/{fin}/{reporte}/{estab}", "EstadisticasAltaController@informeEgreso");
        Route::get("pdfInformeFoliados/{inicio}/{fin}/{estab}/{folio}", "EstadisticasAltaController@pdfInformeFoliados");

    });



    Route::group(array('prefix' => 'contingencia', 'middleware' => 'auth'), function () {

        Route::get("/", array('as' => 'estContingencia', 'uses' => 'EstadisticasContingenciaController@pagina'));
        Route::get("datos", ["as" => "estContingenciaDatos", "uses" => function () {

            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha"))->endOfDay();
            $estab = Input::get("estab");
            $user = Auth::user();

            if ($estab == '0' || $estab == '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new EstadisticasContingenciaController())->reporteAdminSS($fecha_inicio, $fecha);
                }
            }

            return (new EstadisticasContingenciaController())->reporte($fecha_inicio, $fecha, $estab);
        }]);

    });

    Route::get('expIAAS', array('as' => 'expIAAS', 'uses' => 'EstadisticasIAASController@expIAAS'));
//iaas
    //contiene  cvc, infecciones
    Route::group(array('prefix' => 'cvc', 'middleware' => 'auth'), function () {

        Route::get('/', array('as' => 'estIAAS', 'uses' => 'EstadisticasIAASController@pagina'));
        Route::get('datos', ["as" => "estIAASDatos", "uses" => function () {

            $fecha_inicio = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha-inicio"))->startOfDay();
            $fecha = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha"))->endOfDay();
            $estab = Input::get("estab");
            $user = Auth::user();

            if ($estab === '') {
                if ($user->tipo != TipoUsuario::ADMINSS && $user->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    $estab = Session::get("idEstablecimiento");
                } else {
                    return (new App\Http\Controllers\EstadisticasIAASController())->reporte($fecha_inicio, $fecha);
                }
            }
            return (new App\Http\Controllers\EstadisticasIAASController())->reporte($fecha_inicio, $fecha, $estab);
        }]);
    });

    Route::group(array('prefix' => 'camas_habilitadas', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estHabilitadas', 'uses' => 'EstadisticasCamasHabilitadasController@pagina'));

        Route::get("datos", function () {
            $fecha_desde = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha_desde"))->startOfDay();
            $fecha_hasta = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha_hasta"))->endOfDay();
            $establecimiento = Input::get("establecimiento");
            if ($establecimiento == 0) {
                if (Auth::user()->tipo != TipoUsuario::ADMINSS && Auth::user()->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    return (new EstadisticasCamasHabilitadasController())
                        ->reporteEstablecimiento($fecha_desde, $fecha_hasta, Session::get("idEstablecimiento"));
                } else {
                    return (new EstadisticasCamasHabilitadasController)
                        ->reporteTotal($fecha_desde, $fecha_hasta);
                }
            } else {
                return (new EstadisticasCamasHabilitadasController())
                    ->reporteEstablecimiento($fecha_desde, $fecha_hasta, $establecimiento);
            }
        });

    });

    Route::group(array('prefix' => 'camas_deshabilitadas', 'middleware' => 'auth'), function () {
        Route::get('/', array('as' => 'estDeshabilitadas', 'uses' => 'EstadisticasCamasDeshabilitadasController@pagina'));
        Route::get("datos", function () {
            $fecha_desde = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha_desde"))->startOfDay();
            $fecha_hasta = \Carbon\Carbon::createFromFormat("d-m-Y", Input::get("fecha_hasta"))->endOfDay();
            $establecimiento = Input::get("establecimiento");
            if ($establecimiento == 0) {
                if (Auth::user()->tipo != TipoUsuario::ADMINSS && Auth::user()->tipo != TipoUsuario::MONITOREO_SSVQ) {
                    return (new EstadisticasCamasDeshabilitadasController())->reporteEstablecimiento($fecha_desde, $fecha_hasta, Session::get("idEstablecimiento"));
                } else {
                    return (new EstadisticasCamasDeshabilitadasController)->reporteTotal($fecha_desde, $fecha_hasta);
                }
            } else {
                return (new EstadisticasCamasDeshabilitadasController())->reporteEstablecimiento($fecha_desde, $fecha_hasta, $establecimiento);
            }
        });
    });

    Route::group(array('prefix' => 'indiceOcupacional', 'middleware' => 'auth'), function () {

        Route::get("/", array('as' => 'estOcupacional', 'uses' => 'EstadisticasCamasController@estOcupacional'));
        Route::get("graficoOcupacional", 'EstadisticasCamasController@graficoOcupacional');
    });

});

Route::group(array('prefix' => 'contingencia', 'middleware' => 'auth'), function () {
    Route::get('declararContingencia', array('as' => 'declararContingencia', 'uses' => 'ContingenciaController@viewContingencia'));
    Route::post('registrarContingencia', array('as' => 'registrarContingencia', 'uses' => 'ContingenciaController@registrarContingencia'));
    Route::get('contingencias', array('as' => 'contingencias', 'uses' => 'ContingenciaController@tablaContingencias'));
    Route::post('getEstablecimientos', array('as' => 'getEstablecimientos', 'uses' => 'ContingenciaController@getEstablecimientos'));
    Route::post('getContingencias', array('as' => 'getContingencias', 'uses' => 'ContingenciaController@getContingencias'));
    Route::get('verPDF/{id}', array('as' => 'verPDF', 'uses' => 'ContingenciaController@verPDF'));
    Route::get('descargarPDF/{id}', array('as' => 'descargarPDF', 'uses' => 'ContingenciaController@descargarPDF'));
    Route::get('verContingencia/{id}', array('as' => 'verContingencia', 'uses' => 'ContingenciaController@verContingencia'));
    Route::post('anularContingencia', array('as' => 'anularContingencia', 'uses' => 'ContingenciaController@anularContingencia'));
    Route::post('actualizarContingencia', array('as' => 'actualizarContingencia', 'uses' => 'ContingenciaController@actualizarContingencia'));
});

Route::group(array('prefix' => 'evolucion', 'middleware' => 'auth'), function () {
    Route::get('subir', array('as' => 'subir', 'uses' => 'EvolucionController@subirView'));
    Route::post('subirExcel', array('as' => 'subirExcel', 'uses' => 'EvolucionController@subirExcel'));
    Route::post('aceptar', array('as' => 'aceptarEvolucion', 'uses' => 'EvolucionController@aceptarDatos'));
    Route::get('exportar', array('as' => 'exportarEvolucion', 'uses' => 'EvolucionController@exportarView'));
    Route::post('generar', array('as' => 'generarExcel', 'uses' => 'EvolucionController@generar'));
    Route::post('cargar', array('as' => 'cargarDatos', 'uses' => 'EvolucionController@cargar'));
});

Route::group(array('prefix' => 'trasladar', 'middleware' => 'auth'), function () {
    Route::get('/', ["uses" => 'GestionController@trasladar', "before" => 'ADMIN']);
    Route::post('registrarExtraSistema', array('as' => 'registrarExtraSistema', 'uses' => 'GestionController@registrarExtraSistema', 'before' => 'ADMIN'));
    Route::post('{idCaso}/registrarExtraSistema', array('as' => 'registrarExtraSistema', 'uses' => 'GestionController@registrarExtraSistema', 'before' => 'ADMIN'));
    Route::get('trasladosExtraSistema', ["uses" => 'DerivacionController@viewTrasladoExtraSistema', "before" => 'NOT_DIRECTOR|NOT_MEDICO_JEFE_DE_SERVICIO|NOT_MONITOREO_SSVQ|NOT_GESTION_CLINICA|NOT_ENFERMERA_P|NOT_USUARIO']);
    Route::post('obtenerUnidades', 'DerivacionController@obtenerUnidades');
    Route::post('getCamasParaRescate', 'DerivacionController@getCamasParaRescate');
    Route::post('rescatar', 'DerivacionController@rescatar');
    Route::post('buscarPacientePorCaso', 'DerivacionController@buscarPacientePorCaso');
    Route::post("altaExtraSistema", "DerivacionController@altaExtraSistema");
    Route::get("descargar/{id}", "DerivacionController@descargar");

    Route::get("{unidad}", "GestionController@trasladar");
    Route::get("{unidad}/{idCaso}", "GestionController@trasladar");
});

Route::group(array('prefix' => 'historial', 'middleware' => 'auth'), function () {
    Route::get('/', ["uses" => 'GestionController@historial', "before" => 'ADMIN']);
    Route::get("{unidad}", "GestionController@historial");
    Route::get("{unidad}/{idCaso}", "GestionController@historial");
});

Route::get("infecciones2/{idCaso}/{idEstablecimiento}", "GestionController@infecciones2");
Route::get("verinfecciones2/{idCaso}/{idEstablecimiento}", "GestionController@verinfecciones2");
Route::get("historial2/{idCaso}", "GestionController@historial2");

Route::group(array('prefix' => 'infecciones', 'middleware' => 'auth'), function () {
    Route::get('/', ["uses" => 'GestionController@infecciones', "before" => 'ADMIN']);
    Route::get("{unidad}", "GestionController@infecciones");
    Route::get("{unidad}/{idCaso}", "GestionController@infecciones");
});

Route::group(array('prefix' => 'verinfecciones', 'middleware' => 'auth'), function () {
    Route::get('/', ["uses" => 'GestionController@verinfecciones', "before" => 'ADMIN']);
    Route::get("{unidad}", "GestionController@verinfecciones");
    Route::get("{unidad}/{idCaso}", "GestionController@verinfecciones");
});

Route::group(array('prefix' => 'busqueda', 'middleware' => 'auth'), function () {
    Route::get('buscarPacienteIAAS', array('as' => 'buscarPacienteIAAS', 'uses' => 'BusquedaPaciente@buscarPacienteIAAS'));

    Route::group(array('prefix' => 'paciente'), function () {
        Route::get('/', array('as' => 'buscarPaciente', 'uses' => 'BusquedaPaciente@buscarPaciente'));
        Route::get('info', array('as' => 'mostrarInfo'));
        Route::get('info/{id}', "BusquedaPaciente@porId");
        Route::get('rut', array('as' => 'busquedaRut', 'uses' => 'BusquedaPaciente@porRut'));
        Route::get('busquedaRutIAAS', array('as' => 'busquedaRutIAAS', 'uses' => 'BusquedaPaciente@porRutIaas'));
        Route::get('nombre', array('as' => 'busquedaNombre', 'uses' => 'BusquedaPaciente@porNombre'));
        Route::get('buscarServicio', array('as' => 'buscarServicio', 'uses' => 'BusquedaServicioController@viewBusqueda'));
        Route::post('getCuposEstablecimiento', array('as' => 'getCuposEstablecimiento', 'uses' => 'BusquedaServicioController@getCuposEstablecimiento'));
    });
});


Route::group(array('prefix' => 'busquedaIAAS', 'middleware' => 'auth'), function () {

    Route::get('buscarPacienteIAAS', array('as' => 'buscarPacienteIAAS', 'uses' => 'BusquedaPaciente@buscarPacienteIAAS'));

    Route::get("reingresar/{paciente}", "BusquedaPaciente@reingresar");

    Route::post('datosParaDerivacionCaso', "UrgenciaController@datosParaDerivacion");

    Route::post('enviarDerivadoCaso', "UrgenciaController@enviarDerivado");

    Route::group(array('prefix' => 'paciente'), function () {
        Route::get('/', array('as' => 'buscarPaciente', 'uses' => 'BusquedaPaciente@buscarPaciente'));
        Route::get('info', array('as' => 'mostrarInfo'));
        Route::get('info/{id}', "BusquedaPaciente@porId");
        Route::get('rut', array('as' => 'busquedaRut', 'uses' => 'BusquedaPaciente@porRut'));
        Route::get('busquedaRutIAAS', array('as' => 'busquedaRutIAAS', 'uses' => 'BusquedaPaciente@porRutIaas'));
        Route::get('nombre', array('as' => 'busquedaNombre', 'uses' => 'BusquedaPaciente@porNombre'));
        Route::get('ficha', array('as' => 'busquedaFicha', 'uses' => 'BusquedaPaciente@porFicha'));
        Route::get('buscarServicio', array('as' => 'buscarServicio', 'uses' => 'BusquedaServicioController@viewBusqueda'));
        Route::post('getCuposEstablecimiento', array('as' => 'getCuposEstablecimiento', 'uses' => 'BusquedaServicioController@getCuposEstablecimiento'));
        Route::get('busquedaGeneral', array('as' => 'busquedaGeneral', 'uses' => 'BusquedaPaciente@busquedaGeneral'));
        /* Modificacion de diagnostico */
        Route::post('modificarDiagnostico', array('as' => 'modificarDiagnostico', 'uses' => 'BusquedaPaciente@modificarDiagnostico'));
        /* indicaciones */
        Route::post('addIndicacion', array('as' => 'addIndicacion', 'uses' => 'BusquedaPaciente@addIndicacion'));
        Route::post('editIndicacion', array('as' => 'editIndicacion', 'uses' => 'BusquedaPaciente@editIndicacion'));
        Route::post('deleteIndicacion', array('as' => 'deleteIndicacion', 'uses' => 'BusquedaPaciente@deleteIndicacion'));
        /* fechas */
        Route::get('mostrarFechas/{caso}', "BusquedaPaciente@mostrarFechas");
        Route::post('addFechas', "BusquedaPaciente@addFechas");
        Route::get("validarAsignacion", "BusquedaPaciente@validarAsignacion");
        Route::get("validarModFechas", "BusquedaPaciente@validarModFechas");
        Route::get("validarFechaSolicitud", "BusquedaPaciente@validarFechaSolicitud");
        Route::get("validarFechaIndicacionMedica", "BusquedaPaciente@validarFechaIndicacionMedica");
        Route::get("validarFechaEgresoBPaciente", "BusquedaPaciente@validarFechaEgresoBPaciente");

    });
});

Route::group(array('prefix' => 'index', 'middleware' => 'auth'), function () {
    Route::get('camas/{id}', array('as' => 'camas', 'uses' => 'IndexController@camas'));
    Route::get('camas/{id}/exportar', array('as' => 'exportarAdmin', 'uses' => 'IndexController@exportar'));
    Route::post('camas/getCamas', array('as' => 'getCamasIndex', 'uses' => 'IndexController@getCamas'));
    Route::post('camas/getListacCamaUnidad', array('as' => 'getListacCamaUnidad', 'uses' => function (Request $request) {
        return (new GestionController())->obtenerCamasLista($request->input('unidad'), $request->input('id'));
    }));
    Route::post('camas/obtenerMensajeBloqueo', array('as' => 'obtenerMensajeBloqueo', 'uses' => 'IndexController@obtenerMensajeBloqueo'));
});

Route::group(array('prefix' => 'urgencia', 'middleware' => 'auth'), function () {
    Route::post('registrarRiesgos', "UrgenciaController@registrarRiesgos");

    Route::post('buscarPacientesSinCategorizar', array('as' => 'buscarPacientesSinCategorizar', 'uses' => 'EvolucionController@buscarPacientesSinCategorizar'));

    Route::post('RiesgoDependencia', array('as' => 'RiesgoDependencia', 'uses' => 'UrgenciaController@RiesgoDependencia'));

    Route::get('ingresarPaciente', array('as' => 'ingresarPaciente', 'uses' => function () {
        return View::make("Urgencia/IngresarPaciente",
            ["riesgo" => Consultas::getRiesgos(),
                "prevision" => Prevision::getPrevisiones(),
                'regiones' => Consultas::getRegion(),
                'comunas' => Comuna::where('id_comuna', '<', '2000')->where('id_comuna', '>', '1000')->pluck('nombre_comuna', 'id_comuna'),
                'unidades' => Unidad::unidades(),
                'procedencias' => Procedencia::procedencias(),

            ]);
    }));
    Route::get('listaEspera', array('as' => 'listaEspera', 'uses' => function () {
        $motivos = Consultas::getMotivosLiberacion2();
        $ubicaciones = Consultas::obtenerEnum("tipo_ubicacion");
        $lista_servicios = DB::table('servicios_vista')
            ->where('establecimiento', '=', Auth::user()->establecimiento)
            ->orderBy('alias')
            ->get();

        $servicios = [];
        $atributos = [];
        foreach ($lista_servicios as $key => $servicio) {
            $servicios[$servicio->id_unidad] = $servicio->alias;
            $atributos[$servicio->id_unidad] = ["data-toggle" => "tooltip", "title" => $servicio->tooltip];
        }

        $procedencias = Procedencia::procedencias();

        return View::make("Urgencia/ListaEspera", ["motivo" => $motivos, "servicios" => $servicios, "atributos" => $atributos, "ubicaciones" => $ubicaciones, "procedencias" => $procedencias]);
    }));

    Route::get('listaPreAlta', array('as' => 'listaPreAlta', 'uses' => function () {
        $tipos_transito = Consultas::obtenerEnum("tipo_transito");
        $motivos = Consultas::getMotivosLiberacion2();
        return View::make("Urgencia/ListaPreAlta", ["motivo" => $motivos, "tipos_transito" => $tipos_transito]);
    }));

    //lista derivados
    Route::get('listaDerivados', array('as' => 'listaDerivados', 'uses' => function () {

        $response = ListaDerivados::obtenerListaDerivados();
        return View::make("Urgencia/ListaDerivados", ["response" => $response]);
    }));

    Route::get('historialDerivados', array('as' => 'historialDerivados', 'uses' => function () {
        $response = ListaDerivados::historialDerivados();
        return View::make("Urgencia/historialDerivados", ["response" => $response]);
    }));

    Route::get('excelListaDerivados', array('as' => 'excelListaDerivados', 'uses' => 'UrgenciaController@excelListaDerivados'));

    //lista pabellon
    Route::get('listaPabellon', array('as' => 'listaPabellon', 'uses' => function () {

        return View::make("Urgencia/ListaPabellon", []);
    }));
    Route::get('pdfPacientesPabellonPorUnidad', array('as' => 'pdfPacientesPabellonPorUnidad', 'uses' => 'UrgenciaController@pdfPacientesPabellonPorUnidad'));
    Route::get('excelListaPabellonPorUnidad', array('as' => 'excelListaPabellonPorUnidad', 'uses' => 'UrgenciaController@excelListaPabellonPorUnidad'));
    Route::get('pdfPacientesPabellon', array('as' => 'pdfPacientesPabellon', 'uses' => 'UrgenciaController@pdfPacientesPabellon'));
    Route::get('excelListaPabellon', array('as' => 'excelListaPabellon', 'uses' => 'UrgenciaController@excelListaPabellon'));

    Route::get('listaTransito', array('as' => 'listaTransito', 'uses' => function () {
        $tipos_transito = Consultas::obtenerEnum("tipo_transito");
        $motivos = Consultas::getMotivosLiberacion2();
        return View::make("Urgencia/ListaTransito", ["motivo" => $motivos, "tipos_transito" => $tipos_transito, "procedencias" => $procedencias = Procedencia::procedencias()]);
    }));
    Route::get('listaEstudio', array('as' => 'listaEstudio', 'uses' => function () {
        return View::make("Urgencia/ListaEstudio", []);
    }));
    Route::get('obtenerListaEstudios', 'ExamenController@obtenerListaEstudios');
    Route::get('obtenerListaPabellon', 'UrgenciaController@obtenerListaPabellon');
    Route::post('obtenerComentariosListaDerivado', 'UrgenciaController@obtenerComentariosListaDerivado');
    Route::post('infoFormDerivado', "UrgenciaController@infoFormDerivado");
    Route::post('datosParaDerivacion2', "UrgenciaController@datosParaDerivacion");
    Route::post('infoFormularioDerivado', "UrgenciaController@infoFormularioDerivado");
    Route::post('solicitarinfoFormularioDerivado', array('as'=>'solicitarinfoFormularioDerivado', 'uses'=>"UrgenciaController@solicitarinfoFormularioDerivado"));
    Route::post('editarFormDerivado', array('as'=>'editarFormDerivado', 'uses'=>'UrgenciaController@editarFormDerivado'));
    Route::post('agregarComentarioListaDerivado', 'UrgenciaController@agregarComentarioListaDerivado');

    Route::post('registarRiesgo', "UrgenciaController@registarRiesgo");

    Route::get('formularioDerivacion/{idCaso}/{idLista}', "UrgenciaController@formularioDerivacion");

    Route::get('listaSalidaUrgencia', array("as" => "listaSalidaUrgencia", "uses" => function () {
        $tipos_transito = Consultas::obtenerEnum("tipo_transito");
        $motivos = Consultas::getMotivosLiberacion2();
        return View::make("Urgencia/listaSalidaUrgencia", ["motivo" => $motivos, "tipos_transito" => $tipos_transito]);

    }));

    Route::get('listaCategorizados', array('as' => 'listaCategorizados', 'uses' => function () {

        //aqui van los id de las unidades que si o si quieres añadir con su descripcion
        $listaDeseados = [202,189];

        $servicios = DB::table("unidades_en_establecimientos as u")
            ->leftjoin("tipos_unidad as t","t.id","u.tipo_unidad")
            ->select("u.alias", "u.id", "t.descripcion")
            ->where("u.establecimiento", Auth::user()->establecimiento)
            ->where("u.visible",true)
            ->orderBy("u.alias","asc")
            ->get();

        $servicios_modificados = [];
        $cambios = [];
        //Se busca si existen midmos nombres
        foreach($servicios as $key => $respo){
            if(!in_array($respo->id, $cambios)){
                foreach($servicios as $key2 => $respo2){
                    if($respo->alias == $respo2->alias && !in_array($respo2->id, $cambios) && $respo->id != $respo2->id){
                        $servicios_modificados[$respo2->id] = $respo2->alias." ".$respo2->descripcion;
                        $servicios_modificados[$respo->id] = $respo->alias." ".$respo->descripcion;
                        array_push($cambios,$respo2->id);
                        array_push($cambios,$respo->id);
                    }elseif(!in_array($respo2->id, $cambios)){
                        $servicios_modificados[$respo2->id] = (in_array($respo2->id, $listaDeseados))?$respo2->alias." ".$respo2->descripcion:$respo2->alias;
                    }
                }
            }
        }

        //comprobar que no esten prohibidos para el usuario
        foreach($servicios_modificados as $id_unidad => $ser){
            if(Consultas::restriccionPersonal($id_unidad) == true){
                unset($servicios_modificados[$id_unidad]);
            }
        }
        //se añade hospitalizacion domiciliaria
        $servicios_modificados["hospDom"] = "Hospitalización Domiciliaria";

        return View::make("Urgencia/listaCategorizados", [/* "casos" => $resultado, */ "servicios" => $servicios_modificados]);
    }));

    Route::post("cantidadExamenPendiente", 'ExamenController@cantidadExamenPendiente');

    Route::post('agregarListaEspera', array('as' => 'agregarListaEspera', 'uses' => 'UrgenciaController@agregarListaEspera'));
    Route::post('agregarHospDom', array('as' => 'agregarHospDom', 'uses' => 'UrgenciaController@agregarHospDom'));
    Route::get('pacienteEnListaEspera', array('as' => 'pacienteEnListaEspera', 'uses' => 'UrgenciaController@pacienteEnListaEspera'));
    Route::get('obtenerListaEspera', array('as' => 'obtenerListaEspera', 'uses' => 'UrgenciaController@obtenerListaEspera'));
    Route::get('excelListaEspera/{procedencia}', 'UrgenciaController@excelListaEspera');
    Route::get('pdfListaEspera/{procedencia}', 'UrgenciaController@pdfListaEspera');
    Route::get('obtenerListaTransito', array('as' => 'obtenerListaTransito', 'uses' => 'UrgenciaController@obtenerListaTransito'));
    Route::get('excelListaEsperaHosp/{procedencia}', 'UrgenciaController@excelListaEsperaHosp');
    Route::get('obtenerSalidaUrgencia', array('as' => 'obtenerSalidaUrgencia', 'uses' => 'UrgenciaController@obtenerSalidaUrgencia'));
    Route::get('obtenerListaPreAlta', array('as' => 'obtenerListaPreAlta', 'uses' => 'UrgenciaController@obtenerListaPreAlta'));

    Route::post('ingresarACama', array('as' => 'ingresarACama', 'uses' => 'UrgenciaController@ingresarACama'));
    Route::post('darAlta', array('as' => 'darAlta', 'uses' => 'UrgenciaController@darAlta'));
    Route::post('darAltaTransito', array('as' => 'darAltaTransito', 'uses' => 'UrgenciaController@darAltaTransito'));
    Route::post("darAltaPrealta", "PreAltaController@darAltaPrealta");
    Route::post('ingresarACamaReal', array("as" => "ingresarACamaReal", "uses" => "UrgenciaController@ingresarACamaReal"));
    Route::post('cambiarTipoTransito', "UrgenciaController@cambiarTipoTransito");
    Route::post('getTipoTransito', "UrgenciaController@getTipoTransito");
    Route::post('agregarComentario', "UrgenciaController@agregarComentario");
    Route::post('agregarUbicacion', "UrgenciaController@agregarUbicacion");
    Route::post('cambiarUnidad', 'GestionController@cambiarUnidad');
    Route::post('cambiarCama', 'GestionController@cambiarCama');
    Route::post('cambiarFechaTrasladoUnidadHosp', "UrgenciaController@cambiarFechaTrasladoUnidadHosp");
    Route::post('getFechaTrasladoUnidadHosp', "UrgenciaController@getFechaTrasladoUnidadHosp");

    Route::post('quitarDerivado', "UrgenciaController@quitarDerivado");
    Route::post('quitarPabellon', "UrgenciaController@quitarPabellon");
    Route::get('probando', 'UrgenciaController@probando');
    Route::post('{unidad}/getCamasDisponiblesVerdes2', 'GestionController@getCamasDisponiblesVerdes');
    Route::post('intercambiar2', array('as' => 'intercambiar_unidad', 'uses' => 'GestionController@intercambiar'));


});

Route::group(array('prefix' => 'hospitalizacion', 'middleware' => 'auth'), function () {
    Route::get('listaPacientes', array('as' => 'listaPacientes', 'uses' => function () {
        $procedencias = [];
		foreach(Procedencia::all() as $proc){
			if ($proc->nombre == "Otro") {
				$ultimo = [$proc->nombre , $proc->id];
			}else if($proc->nombre == "Pabellón"){}
			else{
				$procedencias[$proc->id] = $proc->nombre;
			}
		}
		$procedencias[$ultimo[1]] = $ultimo[0];
        return View::make("HospitalizacionDom/ListaEspera", ["motivo" => Consultas::obtenerEnum("motivo_salida_urgencia"), "procedencias" => $procedencias]);
    }));
    Route::post('agregarListaEspera', array('as' => 'agregarListaEspera', 'uses' => 'UrgenciaController@agregarListaEspera'));
    Route::get('pacienteEnListaEspera', array('as' => 'pacienteEnListaEspera', 'uses' => 'UrgenciaController@pacienteEnListaEspera'));
    Route::get('obtenerListaPacientes', array('as' => 'obtenerListaPacientes', 'uses' => 'UrgenciaController@obtenerListaPacientes'));
    Route::post('DomingresarACama', array('as' => 'DomingresarACama', 'uses' => 'UrgenciaController@DomingresarACama'));
    Route::post('darAltaDom', array('as' => 'darAltaDom', 'uses' => 'UrgenciaController@darAltaDom'));
    Route::post('comentarioHospDom', array('as' => 'comentarioHospDom', 'uses' => 'UrgenciaController@comentarioHospDom'));
    Route::post('obtenerComentariosHospDom', 'UrgenciaController@obtenerComentariosHospDom');
    Route::get('excelHospitalizacionDomiciliaria', array('as' => 'excelHospitalizacionDomiciliaria', 'uses' => 'UrgenciaController@excelHospitalizacionDomiciliaria'));
    Route::post('reingresarListaEspera', array('as' => 'reingresarListaEspera', 'uses' => 'UrgenciaController@reingresarListaEspera'));
    Route::get('resumenHospDom', 'UrgenciaController@resumenHospDom');
});

//ambulancias
//añdir resource
Route::resource('ambulancias', 'AmbulanciaController');

//eliminar ambulancias
Route::get('ambulancias/{id}/destroy', [
    'as' => 'ambulancias.destroy',
    'uses' => 'AmbulanciaController@destroy',
]);

Route::group(array('prefix' => 'ambulancia', 'before' => 'auth'), function () {
    //InformacionController
    Route::get('indexRutas', [
        'as' => 'indexRutas',
        'uses' => 'InformacionController@indexRutas',
    ]);

    Route::get('enviarAmbulancia', [
        'as' => 'enviarAmbulancia',
        'uses' => 'InformacionController@enviarAmbulancia',
    ]);

    Route::post('ingresarEnvioAmbulancia', [
        'as' => 'ingresarEnvioAmbulancia',
        'uses' => 'InformacionController@ingresarEnvioAmbulancia',
    ]);

    Route::get('generarhora', [
        'as' => 'generarhora',
        'uses' => 'InformacionController@generarhora',
    ]);

    Route::get('listarPacientes', [
        'as' => 'listarPacientes',
        'uses' => 'InformacionController@listarPacientes',
    ]);

    //RutasController

    Route::get('ingresarRutas/{ambulancia}', [
        'as' => 'ingresarRutas',
        'uses' => 'RutaController@ingresarRutas',
    ]);

    Route::get('generarHoraRuta', [
        'as' => 'generarHoraRuta',
        'uses' => 'InformacionController@generarHoraRuta',
    ]);

    Route::get('buscarPacientes', [
        'as' => 'buscarPacientes',
        'uses' => 'InformacionController@buscarPacientes',
    ]);

    Route::post('listaRutas', [
        'as' => 'listaRutas',
        'uses' => 'RutaController@listaRutas',
    ]);

    Route::get('editarRutas/{ambulancia}', [
        'as' => 'editarRutas',
        'uses' => 'RutaController@editarRutas',
    ]);

    Route::post('listaRutas_guardar', [
        'as' => 'listaRutas_guardar',
        'uses' => 'RutaController@listaRutas_guardar',
    ]);

    Route::get('verificando/{ambulancia}', [
        'as' => 'verificando',
        'uses' => 'RutaController@verificando',
    ]);

    Route::get('rutas/{ambulancia}', [
        'as' => 'rutas',
        'uses' => 'RutaController@historialRuta',
    ]);

});
Route::group(array('prefix' => 'paciente', 'middleware' => 'auth'), function () {
    Route::get('puedeHacer/{caso}/{ubicacion}', array('as' => 'puedeHacer', 'uses' => 'PacienteController@puedeEditar'));
    Route::get('editar/{id}', array('as' => 'editar', 'uses' => function ($id) {
        $paciente = Paciente::find($id);
        $paciente->extranjero = ($paciente->extranjero == null || !$paciente->extranjero)?"false":"true";
        $paciente->fecha_nacimiento = ($paciente->fecha_nacimiento) ? date("d-m-Y", strtotime($paciente->fecha_nacimiento)) : null;
        $paciente->dv = $paciente->dv == 10 ? 'K' : $paciente->dv;
        $paciente->dv_madre = $paciente->dv_madre == 10 ? 'K' : $paciente->dv_madre;
        if($paciente->telefono != "-" && $paciente->telefono != ''){
            $datosTelefonos = Telefono::where('id_paciente',$id)->get();
            $datosTelefonos = collect($datosTelefonos);
            $contiene = ($datosTelefonos->contains('telefono',$paciente->telefono)) ? 'si' : 'no';
            if($contiene == 'si'){
                $paciente->telefono = "-";
                $paciente->save();
            }else{
                $telefono = new Telefono();
                $telefono->id_paciente = $id;
                $telefono->tipo = 'Casa';
                $telefono->telefono = $paciente->telefono;
                $telefono->save();

                $paciente->telefono = "-";
                $paciente->save();
            }
        }
        $telefonos = Telefono::where("id_paciente", $id)->get();
        return View::make("Paciente/Editar", [
            "paciente" => $paciente,
            "comunas" => Comuna::getComunas(),
            'regiones' => Consultas::getRegion(),
            "region" => Consultas::getRegionPaciente($paciente->id_comuna),
            'caso' => Caso::casoParaEditar($id),
            "telefonos" => $telefonos
        ]);
    }));

    Route::get('pruebaficha', array('as' => 'pruebaficha', 'uses' => 'PacienteController@pruebaficha'));
    Route::get('edad', array('as' => 'edad', 'uses' => 'PacienteController@calcularEdad'));
    Route::get('egreso/{id}', array('as' => 'egreso', 'uses' => 'PacienteController@generarEgreso'));
    Route::post('editarPaciente', array('as' => 'editarPaciente', 'uses' => 'PacienteController@editarPaciente'));
    // Route::post('conservarTelefono', array('as' => 'conservarTelefono', 'uses' => 'PacienteController@conservarTelefono'));
    Route::post('fichaEgresoPaciente', array('as' => 'fichaEgresoPaciente', 'uses' => 'PacienteController@fichaEgresoPaciente'));
    Route::post('tieneCaso', array('as' => 'tieneCaso', 'uses' => 'PacienteController@tieneCaso'));
    Route::post('obtenerPaciente', array('as' => 'obtenerPaciente', 'uses' => 'PacienteController@obtenerPaciente'));

    Route::get('fichaEgresoPDF', array('as' => 'fichaEgresoPDF', 'uses' => 'PacienteController@fichaEgresoPDF'));

    Route::post('existePaciente', array('as' => 'existPaciente', 'uses' => 'PacienteController@existePaciente'));
    Route::post('crearPaciente', array('as' => 'crearPaciente', 'uses' => 'PacienteController@crearPaciente'));
});

Route::group(array('prefix' => 'documentos'), function () {
    Route::get('Documentos', array('as' => 'Documentos', 'uses' => function () {
        return View::make("Documentos/Documentos");
    }));
    Route::post('subir', array('as' => 'subir', 'uses' => 'ArchivoController@recibir'));
    Route::get('eliminar', array('as' => 'eliminar', 'uses' => 'DocumentosController@eliminar'));
    Route::get('listar', array('as' => 'listar', 'uses' => 'DocumentosController@listar'));
    Route::get('{idArchivo}/descargar', array('as' => 'descargar', 'uses' => 'DocumentosController@descargar'));
    Route::get('{idArchivo}/link', array('as' => 'verContenido', 'uses' => 'DocumentosController@obtenerLink'));
});

Route::get("testx", "GestionController@test");


Route::get('verHistorialVisitas/{idCaso}', 'GestionController@verHistorialVisitas');
Route::get('regresarEspera/{idCaso}/{ubicacion}', 'GestionController@regresarEspera');
Route::post('liberarCama', 'GestionController@liberarCama');
Route::get("validarFechaEgresoThistorial", "GestionController@validarFechaEgresoThistorial");
Route::get("validarFechaEgresoCaso", "GestionController@validarFechaEgresoCaso");
Route::get("validarFechaHospDomCasoEgresado", "GestionController@validarFechaHospDomCasoEgresado");
Route::get("validarFechaDerivacionRealizada", "GestionController@validarFechaDerivacionRealizada");
Route::post('acostarPaciente', 'GestionController@acostarPaciente');
Route::post('regresarTransito', 'GestionController@regresarTransito');
Route::post('darAlta', 'GestionController@darAlta');
Route::post('planTratamiento', 'GestionController@planTratamiento');
Route::post('liberarInfeccion', 'GestionController@liberarInfeccion');
Route::post('validarTraslado', 'GestionController@validarTraslado');
Route::post('validarTraslado2', 'GestionController@validarTraslado2');
Route::post('validarTraslado3', 'GestionController@validarTraslado3');
Route::post('validarTraslado4', 'GestionController@validarTraslado4');
Route::get('validarFechaIngreso', 'GestionController@validarFechaIngreso');
Route::post('liberarReserva', 'GestionController@liberarReserva');
Route::post('{unidad}/reasignarCama', 'GestionController@reasignarCama');
Route::post('{unidad}/buscarCama', 'GestionController@buscarCama');

Route::post('ingresarinfeccion', 'GestionController@ingresarinfeccion');
Route::post('Veringresarinfeccion', 'GestionController@Veringresarinfeccion');

Route::post('notificaInfeccion', 'GestionController@notificaInfeccion');
Route::post('getPaciente', 'GestionController@getPaciente');
Route::post('getPlanTratamiento', 'GestionController@getPlanTratamiento');
Route::post("registrarTraslado", "GestionController@registrarTraslado");
Route::post('getCamasDisponibles', 'GestionController@getCamasDisponibles');

Route::post('registrarPaciente', 'GestionController@registrarPaciente');
Route::post('movimientoSOME', 'SOMEController@movimientoSOME');
Route::post('derivarPaciente', 'GestionController@derivarPaciente');
Route::post("detallesCaso", "GestionController@detallesCaso");
Route::post("detallesCasoHospDom", "GestionController@detallesCasoHospDom");
Route::post("detallesCasoDieta", "GestionController@detallesCasoDieta");
Route::get("detallesCasodescargarExcel/{idCaso}/{variableunidad}", "GestionController@descargarExceldetallesCaso");
Route::post("quitarDiagnostico", ["as" => "quitarDiagnostico", "uses" => "GestionController@quitarDiagnostico"]);
Route::post("ingresarDiagnostico", ["as" => "ingresarDiagnostico", "uses" => "GestionController@ingresarDiagnostico"]);
Route::post("cambiarCasoSocial", ["as" => "cambiarCasoSocial", "uses" => "GestionController@cambiarCasoSocial"]);
Route::post("diagnosticosCaso", "GestionController@diagnosticosCaso");
Route::post("cambiarRiesgo", "GestionController@cambiarRiesgo");

Route::post("riesgoActual", "GestionController@riesgoActual");
Route::post("nuevoRiesgoActual", "GestionController@nuevoRiesgoActual");
Route::post("consultarHora", "EvolucionController@consultarHora");

Route::post("cambiarDieta", "GestionController@cambiarDieta");
Route::post("examenesCaso", "GestionController@examenesCaso");

Route::post("updatePendiente", "GestionController@updatePendiente");
Route::post("documentosDerivacion", "GestionController@documentosDerivacion");
Route::post("actualizarEstado", "GestionController@actualizarEstado");

Route::get("getEspecificarProcedencia", "GestionController@especificarProcedencia");
Route::get("getEspecificarAlta", "GestionController@especificarAlta");

Route::get("perejil", "GestionController@perejil");

Route::get('getDerivaciones', 'DerivacionController@getDerivaciones');
Route::post('getRiesgos', 'DerivacionController@getRiesgos');
Route::post('obtenerMotivos', 'DerivacionController@obtenerMotivos');

Route::post('renovar', 'GestionController@renovar');
Route::post('ingresar', 'GestionController@ingresar');
Route::post('getUnidades', 'GestionController@getUnidades');
Route::post('getUnidadesIndex', 'GestionController@getUnidadesIndex');
Route::get('getUnidadesSelect', 'GestionController@getUnidadesSelect');
Route::post('reasignar', 'GestionController@reasignar');

Route::post('getTablaCamas', 'AdministracionController@getTablaCamas');
Route::post('editarCama', 'AdministracionController@editarCama');
Route::post('cancelarTraslado', 'DerivacionController@cancelarTraslado');
Route::post('aceptarAdmin', 'DerivacionController@aceptarAdmin');
Route::post('rechazarAdmin', 'DerivacionController@rechazarAdmin');
Route::post('cancelarAceptacionTraslado', 'DerivacionController@cancelarAceptacionTraslado');
Route::post('resolicitarTraslado', 'DerivacionController@resolicitarTraslado');
Route::post("restablecerDerivacion", "DerivacionController@restablecerDerivacion");
Route::get('traslado/{tipo}/enviarMensaje/{id}/{motivo}', ['uses' => 'DerivacionController@enviarMensajeView', 'middleware' => 'auth']);
Route::post('traslado/{tipo}/enviarMensaje/getCamas', ['uses' => 'DerivacionController@getCamasDisponibles', 'middleware' => 'auth']);
Route::post('enviarMensaje', 'DerivacionController@enviarMensaje');
Route::post('getMensajeTraslado', 'DerivacionController@getMensajeTraslado');
Route::post('rechazarTraslado', 'DerivacionController@rechazarTraslado');
Route::post('aceptarSolicitud', 'DerivacionController@aceptarSolicitud');
Route::get('validarParaTraslado', 'GestionController@validarParaTraslado');
Route::get('validarParaIngreso', 'GestionController@validarParaIngreso');
Route::post('nuevoEstablecimientoExtrasistema', array("as" => 'nuevoEstablecimientoExtrasistema', "uses" => 'GestionController@nuevoEstablecimientoExtrasistema'));
Route::get('test', 'TestController@doTest');
Route::get('validarRutUsuario', 'AdministrarUsuarioController@validarRut');
Route::get('obtenerUsuario', 'AdministrarUsuarioController@obtenerDatosUsuario');
Route::post('testingQuillota', function () {
    $all = Input::all();
    $out = "";
    foreach ($all as $k => $f) {
        $out .= "{$k} => {$f}\n";
    }
    echo $out;

});

Route::get('validarDau', 'GestionController@validarDau');

// OPTIMIZACION
Route::post('/optimizacion', [
    'as' => 'optimizacion',
    'uses' => 'OptimizacionController@optimizacion',
]);

Route::get('pruebaSome', function () {
    return View::make("prueba/some");
});

Route::get('pedirHora', array('as' => 'pedirHora', 'uses' => 'HoraTraumatologiaController@pedirHora'));
Route::post('pedirHora', 'HoraTraumatologiaController@pedirHoraPost');
Route::get('revisarHora', array('as' => 'revisarHora', 'uses' => 'HoraTraumatologiaController@revisarHora'));

Route::get('medicos', array('as' => 'medicos', 'uses' => 'MedicoController@medico'));
Route::get('crearMedico', array('as' => 'crearMedico', 'uses' => 'MedicoController@crearMedico'));
Route::post('crearMedico', array('as' => 'crearMedico', 'uses' => 'MedicoController@crearMedicoPost'));

Route::get('actualizarMedico/{id}', array('as' => 'actualizarMedico', 'uses' => 'MedicoController@actualizarMedico'));

Route::post('actualizarMedico/{id}', array('as' => 'actualizarMedico', 'uses' => 'MedicoController@actualizarMedicoPost'));

Route::post('eliminarMedico', array('as' => 'eliminarMedico', 'uses' => 'MedicoController@eliminarMedico'));

Route::post('existeMedico', array("as" => "existeMedico", 'uses' => 'MedicoController@existeMedico'));

Route::post('responderHora', 'HoraTraumatologiaController@responderHora');
Route::post('cancelarHora', 'HoraTraumatologiaController@cancelarHora');
Route::post('getArchivosHora', 'HoraTraumatologiaController@getArchivosHora');

Route::get('descargarAdjuntoHora/{id}', 'HoraTraumatologiaController@descargarAdjuntoHora');

Route::get('pruebaReporteAutomatico', array('as' => 'pruebaReporteAutomatico', 'uses' => 'SesionController@enviarCorreoContacto'));

Route::get("pruebaAqq", function () {
    \Artisan::call('enviar:correo');
});

Route::get('descargarManual', function () {
    $path = storage_path() . "/data/manuales";
    $tipo = Session::get("usuario")->tipo;
    if ($tipo == "gestion_clinica" || $tipo == "enfermeraP") {
        return Response::download("{$path}/Manual-Enfermera.pdf", "Manual-Enfermera.pdf");
    } else if ($tipo == "director" || tipo == 'medico_jefe_servicio') {
        return Response::download("{$path}/Manual-Director.pdf", "Manual-Director.pdf");
    } else {
        return Response::download("{$path}/Manual-Gestor_Camas.pdf", "Manual.pdf");
    }


});

Route::get('descargarDocumentoDerivacion/{id}', function ($id) {
    $path = storage_path() . "/data/documentosDerivacionesCaso/";
    $tipo = Session::get("usuario")->tipo;
    $solicitud = DocumentoDerivacionCaso::where("id_documento_derivacion_caso", "=", $id)->first();
    return response()->download($path . $solicitud->recurso);

});

Route::post("ingresarCategorizacion", array("as" => "ingresarCategorizacion", "uses" => "GestionController@ingresarCategorizacion"));
Route::auth();
Auth::routes();

///////////// RUTAS FORMULARIO DERIVACIÓN ///Alexis///////////////////////////////////////////////////////

Route::post('/derivacionEnuevo', 'DerivacionExtraSistemaController@store')->name('DerivacionExtra.store');
Route::post('/derivacionNueva', 'DerivacionController@derivarPacienteStore')->name('DerivacionExtra.store');

Route::get('/data/establecimiento/lista', 'PacienteController@enviarDatosEstablecimiento');
Route::get('/data/medico/lista', 'PacienteController@enviarDatosMedico');
Route::get('data/thistoric/lista', 'PacienteController@enviaDatosTHOcupa');

Route::get('/derivacionPaciente/{id}', 'DerivacionController@formularioDerivacion')->name('Derivacion.DerivarPaciente');
Route::get('/data/uestablecimiento/lista/{id}', 'DerivacionController@unidadesEnEstablecimiento');
Route::get('correoSinCategorizar' , 'CorreoSinCategorizarController@pacientesSinCategorizar');


Route::group(['prefix' => 'access'], function(){
    Route::get("generarXls/{inicio}/{fin}/{estab}",array('as' => 'generarXls', 'uses' =>"AccessController@generarXls"));
    Route::get("xlsView",array('as' => 'xlsView', 'uses' =>"AccessController@xlsView"));
    Route::post("getDatos", "AccessController@getDatos");

});

Route::group(array('prefix' => 'gestionEnfermeria', 'middleware' => 'auth'), function () {
    //formularios
    Route::get('{idCaso}/formRiesgoCaida', array('as' => 'formRiesgoCaida', 'uses' => 'GestionController@formRiesgoCaida'));
    Route::get('{idCaso}/formGlasgow', array('as' => 'formGlasgow', 'uses' => 'GestionController@formGlasgow'));
    Route::get('{idCaso}/formNova', array('as' => 'formNova', 'uses' => 'GestionController@formNova'));
    Route::get('{idCaso}/formBarthel', array('as' => 'formBarthel', 'uses' => 'GestionController@formBarthel'));
    Route::get('{idCaso}/formPacientePostrado', array('as' => 'formPacientePostrado', 'uses' => 'GestionController@formPacientePostrado'));
    Route::get('{idCaso}/formMacdems', array('as' => 'formMacdems', 'uses' => 'GestionController@formMacdems'));
    Route::get('{idCaso}/formRiesgoUlcera', array('as' => 'formRiesgoUlcera', 'uses' => 'GestionController@formRiesgoUlcera'));
    Route::get('{idCaso}/formUsoRestringido', array('as' => 'formUsoRestringido', 'uses' => 'GestionController@formUsoRestringido'));
    //formularios


    //Ingreso de Enfermeria
    Route::post('ingresoHojaEnfermeria', 'HojaIngresoEnfermeriaController@ingresoHojaEnfermeria');
    Route::get('{idCaso}/historialIngresoEnfermeria', array('as' => 'historialIngresoEnfermeria', 'uses' => 'HojaIngresoEnfermeriaController@historialIngresoEnfermeria'));
    Route::post('buscarHistorialIngresoEnfermeria', 'HojaIngresoEnfermeriaController@buscarHistorialIngresoEnfermeria');
    Route::get('editarIngresoEnfermeria/{idForm}', 'HojaIngresoEnfermeriaController@edit');
    Route::post('obtenerIndicacionesMedicas', 'HojaIngresoEnfermeriaController@obtenerIndicacionesMedicas');
    Route::get('pdfResumenHojaIngresoEnfermeria/{caso}', array('as' => 'pdfResumenHojaIngresoEnfermeria', 'uses' => 'HojaIngresoEnfermeriaController@pdfResumenHojaIngresoEnfermeria'));

    Route::post('store', 'EnfermeriaNovaController@store');

    //Planificacion de cuidados
    Route::post('registrarPlanificiacionCuidados', 'EnfermeriaPlanificacionCuidadosController@registrarPlanificiacionCuidados');
    Route::get('{idCaso}', 'GestionController@gestionEnfermeria');
    Route::get('{idCaso}/histPlanificacionCuidados', array('as' => 'histPlanificacionCuidados', 'uses' => 'EnfermeriaPlanificacionCuidadosController@histPlanificacionCuidados'));
    Route::post('buscarHistorialPlanificacion', 'EnfermeriaPlanificacionCuidadosController@buscarHistorialPlanificacion');
    Route::post('datosPlanificacion', 'EnfermeriaPlanificacionCuidadosController@datosPlanificacion');
    Route::get('{query}/consulta_aetipo', 'EnfermeriaPlanificacionCuidadosController@consulta_aetipo');
	Route::get('{query}/consulta_aetipo_pediatria', 'EnfermeriaPlanificacionCuidadosController@consulta_aetipo_pediatria');

    //Hoja de enfermeria
    Route::get('indexHojaEnfermeria', 'EnfermeriaHojaEnfermeriaController@indexHojaEnfermeria');

    /* Volumenes de soluciones */
    Route::post('addVolumenSolucion', 'EnfermeriaHojaEnfermeriaController@addVolumenSolucion');
    Route::get('obtenerVolumenesSolucionesPendientes/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerVolumenesSolucionesPendientes');
    Route::post('modificarVolumenes', 'EnfermeriaHojaEnfermeriaController@modificarVolumenes');
    Route::post('eliminarVolumen', 'EnfermeriaHojaEnfermeriaController@eliminarVolumen');

    /* Control egresos */
    Route::post('addControlEgreso', 'EnfermeriaHojaEnfermeriaController@addControlEgreso');
    Route::get('obtenerControlesEgresos/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerControlesEgresos');
    Route::post('modificarControlEgreso', 'EnfermeriaHojaEnfermeriaController@modificarControlEgreso');
    Route::post('eliminarControlEgreso', 'EnfermeriaHojaEnfermeriaController@eliminarControlEgreso');

    /* Examen laboratorio */
    Route::post('addExamenLaboratorio', 'EnfermeriaHojaEnfermeriaController@addExamenLaboratorio');
    Route::get('obtenerExamenesLaboratorio/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerExamenesLaboratorio');
    Route::post('modificarLaboratorio', 'EnfermeriaHojaEnfermeriaController@modificarLaboratorio');
    Route::post('eliminarLaboratorio', 'EnfermeriaHojaEnfermeriaController@eliminarLaboratorio');

    /* Examen imagenes */
    Route::post('addExamenImagen', 'EnfermeriaHojaEnfermeriaController@addExamenImagen');
    Route::get('obtenerExamenesPendientes/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerExamenesPendientes');
    Route::post('modificarExamenImagen', 'EnfermeriaHojaEnfermeriaController@modificarExamenImagen');
    Route::post('eliminarExamenImagen', 'EnfermeriaHojaEnfermeriaController@eliminarExamenImagen');

    /* Valoración enfermería */
    Route::post('addValoracionEnfermeria', 'EnfermeriaHojaEnfermeriaController@addValoracionEnfermeria');
    Route::get('obtenerValoracionesEnfermeria/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerValoracionesEnfermeria');

    /*Interconsulta */
    //Route::post('agregarInterconsulta', 'EnfermeriaHojaEnfermeriaController@agregarInterconsulta');
    Route::post('obtenerInterconsultas/{id_caso}', 'EnfermeriaHojaEnfermeriaController@obtenerInterconsultas');
    Route::post('modificarInterconsulta', 'EnfermeriaHojaEnfermeriaController@modificarInterconsulta');
    Route::post('finalizarInterconsulta', 'EnfermeriaHojaEnfermeriaController@finalizarInterconsulta');
    Route::post('eliminarInterconsulta', 'EnfermeriaHojaEnfermeriaController@eliminarInterconsulta');
    Route::post('obtenerDiasEstada', 'EnfermeriaHojaEnfermeriaController@obtenerDiasEstada');

    /*Control Estada */
    Route::post('agregarAntibiotico', 'EnfermeriaHojaEnfermeriaController@agregarAntibiotico');
    Route::get('obtenerControlEstadaAntibioticos/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerControlEstadaAntibioticos');
    Route::get('obtenerControlEstadaOperaciones/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerControlEstadaOperaciones');
    Route::get('obtenerControlEstadaProcedimientos/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerControlEstadaProcedimientos');
    Route::get('obtenerControlEstadaOtros/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerControlEstadaOtros');
    Route::post('eliminarAntibiotico', 'EnfermeriaHojaEnfermeriaController@eliminarAntibiotico');
    Route::post('finalizarAntibiotico', 'EnfermeriaHojaEnfermeriaController@finalizarAntibiotico');
    Route::post('llenarSelectTipoProcedecimiento', 'EnfermeriaHojaEnfermeriaController@llenarSelectTipoProcedecimiento');

    /* cuidados de enfermeria */
    Route::get('obtenerCuidados/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerCuidados');
    Route::get('infocuidadoseindicaciones/{caso}','EnfermeriaHojaEnfermeriaController@infocuidadoseindicaciones');
    Route::post('addCuidadoEnfermeria', 'EnfermeriaHojaEnfermeriaController@addCuidadoEnfermeria');
    Route::get('obtenerCuidadosEnfermeria/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerCuidadosEnfermeria');
    Route::post('eliminarCuidadoEnfermeria', 'EnfermeriaHojaEnfermeriaController@eliminarCuidadoEnfermeria');
    Route::post('get-cuidados-signos-vitales-json', 'EnfermeriaHojaEnfermeriaController@getCuidadosSignosVitalesJson')->name('get-cuidados-signos-vitales-json');
    Route::post('check-planificacion-despues-signos', 'EnfermeriaHojaEnfermeriaController@checkSiPlanificacionExisteDespuesQueIngresoDeSignos')->name('check-planificacion-despues-signos');
    Route::post('anadir-check-para-signos-vitales', 'EnfermeriaHojaEnfermeriaController@anadirCheckParaSignosVitales')->name('anadir-check-para-signos-vitales');
    Route::get("pdfResumenCuidados/{caso}/{fecha}", "EnfermeriaHojaEnfermeriaController@pdfResumenCuidados");
    Route::get("pdfCuidadosRealizados/{caso}/{fecha}", "EnfermeriaHojaEnfermeriaController@pdfCuidadosRealizados");

  

    /*Signos vitales*/
    Route::post('agregarSignosVitales', 'EnfermeriaHojaEnfermeriaController@agregarSignosVitales');
    Route::post('obtener-signos-vitales', 'EnfermeriaHojaEnfermeriaController@obtenerSignosVitales')->name('obtener-signos-vitales');
    Route::post('validar-obtener-signos-vitales', 'EnfermeriaHojaEnfermeriaController@validarObtenerSignosVitales')->name('validar-obtener-signos-vitales');
    Route::post('modificarSignoVital', 'EnfermeriaHojaEnfermeriaController@modificarSignoVital');
    Route::post('eliminarSignoVital', 'EnfermeriaHojaEnfermeriaController@eliminarSignoVital');
    Route::post('graficar-signos-vitales/{caso}', 'EnfermeriaHojaEnfermeriaController@graficarSignosVitales');
    Route::post('agregarGlassgowx2', 'EnfermeriaHojaEnfermeriaController@agregarGlassgowx2');

    /*Riesgo caida*/
    Route::post('agregarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@agregarRiesgoCaida');
    Route::get('obtenerRiesgoCaidas/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerRiesgoCaidas');
    Route::post('modificarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@modificarRiesgoCaida');
    Route::post('eliminarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@eliminarRiesgoCaida');


    /* PLANIFICACION DE CUIDADOS */

    /* Atencion de enfermeria */
    Route::post('addAtencionEnfermeria', 'EnfermeriaPlanificacionCuidadosController@addAtencionEnfermeria');
    Route::get('obtenerAtencionEnfermeria/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerAtencionEnfermeria');
    Route::post('eliminarAtencionEnfermeria', 'EnfermeriaPlanificacionCuidadosController@eliminarAtencionEnfermeria');
    Route::post('alertaCuracionesSimples', 'EnfermeriaPlanificacionCuidadosController@alertaCuracionesSimples');//Alerta en planificacion de los cuidados
    Route::post('eliminarHora', 'EnfermeriaPlanificacionCuidadosController@eliminarHora');
    Route::post('validar_aetipo2', 'EnfermeriaPlanificacionCuidadosController@validar_aetipo2');
    Route::post('addaetipo', 'EnfermeriaPlanificacionCuidadosController@addaetipo');

    /* Curaciones */
    Route::post('addCuraciones', 'EnfermeriaPlanificacionCuidadosController@addCuraciones');
    Route::get('obtenerCuraciones/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerCuraciones');

    /* Protecciones */
    Route::post('addProtecciones', 'EnfermeriaPlanificacionCuidadosController@addProtecciones');
    Route::get('obtenerProtecciones/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerProtecciones');

    /* Novedades */
    Route::post('addNovedades', 'EnfermeriaPlanificacionCuidadosController@addNovedades');
    Route::get('obtenerNovedades/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerNovedades');
    Route::post('eliminarNovedad', 'EnfermeriaPlanificacionCuidadosController@eliminarNovedad');
    Route::post('modificarNovedad', 'EnfermeriaPlanificacionCuidadosController@modificarNovedad');

    /* Indicaciones medicas */

    Route::post('addIndicacionMedica', 'EnfermeriaPlanificacionCuidadosController@addIndicacionMedica');
    Route::get('obtenerPlanificacionIndicacionesMedicas/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerPlanificacionIndicacionesMedicas');
    Route::post('modificarPCIndicacion', 'EnfermeriaPlanificacionCuidadosController@modificarPCIndicacion');
    Route::post('eliminarTerminarPCIndicacion', 'EnfermeriaPlanificacionCuidadosController@eliminarTerminarPCIndicacion');
    Route::post('eliminarTerminarPCIndicacionHora', 'EnfermeriaPlanificacionCuidadosController@eliminarTerminarPCIndicacionHora');
    Route::get('obtenerIndicacion/{idIndicacion}/{tipo}', 'EnfermeriaPlanificacionCuidadosController@obtenerIndicacion');
    Route::post('obtenerIndicacionEliminarTerminar', 'EnfermeriaPlanificacionCuidadosController@obtenerIndicacionEliminarTerminar');
    Route::post('modificarAtencionHoras', 'EnfermeriaPlanificacionCuidadosController@modificarAtencionHoras');
    Route::post('modificacionHorasAtencion', 'EnfermeriaPlanificacionCuidadosController@modHorasAtencion');
    Route::post('terminarAtencionEnfermeria', 'EnfermeriaPlanificacionCuidadosController@terminarAtencionEnfermeria');
    Route::post('eliminarOTerminarAtencion', 'EnfermeriaPlanificacionCuidadosController@eliminarOTerminarAtencion');

    /*  Indicaciones medicas nuevo */
    Route::get('cargarDatosIndicacionMedica/{caso}', 'EnfermeriaPlanificacionCuidadosController@cargarDatosIndicacionMedica');
    Route::get('obtenerDatosIndicacionFarmacos/{caso}/{id_farmaco}/{id_indicacion_medica}', 'EnfermeriaPlanificacionCuidadosController@obtenerDatosIndicacionFarmacos');
    Route::post('addDatosIndicacionMedica', 'EnfermeriaPlanificacionCuidadosController@addDatosIndicacionMedica');
    Route::get('{idCaso}/histHojaEnfemeria', array('as' => 'histHojaEnfemeria', 'uses' => 'EnfermeriaHojaEnfermeriaController@histHojaEnfemeria'));
    Route::post('buscarHistorialHojaEnfermeria', 'EnfermeriaHojaEnfermeriaController@buscarHistorialHojaEnfermeria');
    Route::post('datosHoja', 'EnfermeriaHojaEnfermeriaController@datosHoja');
    Route::get('obtenerDatosPlanificacionIndicacionesMedicas/{caso}', 'EnfermeriaPlanificacionCuidadosController@obtenerDatosPlanificacionIndicacionesMedicas');
    Route::get('obtenerIndicacionMedica/{idIndicacion}/{tipo}', 'EnfermeriaPlanificacionCuidadosController@obtenerIndicacionMedica');
    Route::post('modificarDatosPCIndicacion', 'EnfermeriaPlanificacionCuidadosController@modificarDatosPCIndicacion');
    Route::post('obtenerIndicacionEliminarTerminarMedica', 'EnfermeriaPlanificacionCuidadosController@obtenerIndicacionEliminarTerminarMedica');
    Route::post('eliminarTerminarPCIndicacionMedica', 'EnfermeriaPlanificacionCuidadosController@eliminarTerminarPCIndicacionMedica');
    Route::post('eliminarTerminarPCIndicacionHoraMedica', 'EnfermeriaPlanificacionCuidadosController@eliminarTerminarPCIndicacionHoraMedica');

    /*Riesgo caida*/
    Route::post('agregarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@agregarRiesgoCaida');
    Route::get('obtenerRiesgoCaidas/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerRiesgoCaidas');
    Route::post('modificarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@modificarRiesgoCaida');
    Route::post('eliminarRiesgoCaida', 'EnfermeriaHojaEnfermeriaController@eliminarRiesgoCaida');

    /*Procedimientos invasivos */
    Route::post('agregarProcedimientoInvasivo', 'EnfermeriaHojaEnfermeriaController@agregarProcedimientoInvasivo');
    Route::get('obtenerProcedimientosInvasivos/{caso}', 'EnfermeriaHojaEnfermeriaController@obtenerProcedimientosInvasivos');
    Route::post('finalizarProcedimientoInvasivo', 'EnfermeriaHojaEnfermeriaController@finalizarProcedimientoInvasivo');
    Route::post('eliminarProcedimientoInvasivo', 'EnfermeriaHojaEnfermeriaController@eliminarProcedimientoInvasivo');

    /*Ingreso Hoja Enfermeria */
    Route::post('existenHojaIngresoEnfermeria', 'HojaIngresoEnfermeriaController@existenHojaIngresoEnfermeria');
    /*Anamnesis*/
    Route::post('agregarIEAnamnesis', 'HojaIngresoEnfermeriaController@agregarIEAnamnesis');
    Route::post('existenDatosAnamnesis', 'HojaIngresoEnfermeriaController@existenDatosAnamnesis');
    Route::get('obtenerHistoricoAnamnesis/{caso}', 'HojaIngresoEnfermeriaController@obtenerHistoricoAnamnesis');
    // Route::post('modificarIEAnamnesis', 'EnfermeriaHojaEnfermeriaController@modificarIEAnamnesis');
    // Route::post('eliminarIEAnamnesis', 'EnfermeriaHojaEnfermeriaController@eliminarIEAnamnesis');
    //Pueblo Originario
    Route::post('existenDatosPueblo', 'HojaIngresoEnfermeriaController@existenDatosPueblo');
    Route::post('existenDatosMedicamentos', 'HojaIngresoEnfermeriaController@existenDatosMedicamentos');
    Route::post('eliminarMedicamento', 'HojaIngresoEnfermeriaController@eliminarMedicamento');

    /*Examen Fisico General */
    Route::post('agregarIEGeneral', 'HojaIngresoEnfermeriaController@agregarIEGeneral');
    Route::post('existenDatosGeneral', 'HojaIngresoEnfermeriaController@existenDatosGeneral');
    Route::get('obtenerHistoricoGeneral/{caso}', 'HojaIngresoEnfermeriaController@obtenerHistoricoGeneral');

     /*Examen Fisico Segmentario */
     Route::post('agregarIESegmentario', 'HojaIngresoEnfermeriaController@agregarIESegmentario');
     Route::post('existenDatosSegmentario', 'HojaIngresoEnfermeriaController@existenDatosSegmentario');
     Route::get('obtenerHistoricoSegmentario/{caso}', 'HojaIngresoEnfermeriaController@obtenerHistoricoSegmentario');

    /*Otros */
    Route::post('agregarIEOtros', 'HojaIngresoEnfermeriaController@agregarIEOtros');
    Route::post('existenDatosOtros', 'HojaIngresoEnfermeriaController@existenDatosOtros');
    Route::post('existenDatosCateteres', 'HojaIngresoEnfermeriaController@existenDatosCateteres');

    //Hoja de curaciones
    Route::post('ingresoHojaCuracion', 'HojaCuracionController@ingresoHojaCuracion');
    Route::post('ingresoHojaCuracionSimple', 'HojaCuracionController@ingresoHojaCuracionSimple');
    Route::get('obtenerValoracionHeridas/{caso}', 'HojaCuracionController@obtenerValoracionHeridas');
    Route::get('obtenerCuracionSimple/{caso}', 'HojaCuracionController@obtenerCuracionSimple');
    Route::get('buscarValoracionHeridas/{caso}/{fechaBusqueda}', 'HojaCuracionController@buscarValoracionHeridas');
    Route::get('buscarCuracionesSimples/{caso}/{fechaBusqueda}', 'HojaCuracionController@buscarCuracionesSimples');
    Route::post('eliminarCuracion', 'HojaCuracionController@eliminarCuracion');
    Route::post('eliminarCuracionSimple', 'HojaCuracionController@eliminarCuracionSimple');
    Route::post('modificarCuracion', 'HojaCuracionController@modificarCuracion');
    Route::post('modificarCuracionSimple', 'HojaCuracionController@modificarCuracionSimple');

    // NOVA
    Route::post('store', 'EnfermeriaNovaController@store');
    Route::get('{idCaso}/historialNova', 'EnfermeriaNovaController@index');
    Route::get('{idCaso}/datosTablaNova', 'EnfermeriaNovaController@datosTablaNova');
    Route::get('editarNova/{idForm}', 'EnfermeriaNovaController@edit');

    // GLASGOW
    Route::get('{idCaso}/indexGlasgow', 'EnfermeriaGlasgowController@index');
    Route::get('{idCaso}/datosTabla', 'EnfermeriaGlasgowController@datosTabla');
    Route::post('guardarGlasgow', 'EnfermeriaGlasgowController@store');
    Route::get('editarGlasgow/{idForm}', 'EnfermeriaGlasgowController@edit');

     // BARTHEL
    Route::post('guardarBarthel', 'EnfermeriaBarthelController@store');
    Route::get('{idCaso}/historialBarthel', array('as' => 'historialBarthel', 'uses' => 'EnfermeriaBarthelController@historialBarthel'));
    Route::post('buscarHistorialBarthel' , 'EnfermeriaBarthelController@buscarHistorialBarthel');
    Route::get('editarBarthel/{idForm}', 'EnfermeriaBarthelController@edit');
    Route::get('pdfHistorialBarthel/{caso}', array('as' => 'pdfHistorialBarthel', 'uses' => 'EnfermeriaBarthelController@pdfHistorialBarthel'));

    //Paciente postrado
    Route::post('ingresoPacientePostrado', 'EnfermeriaPacientePostradoController@ingresoPacientePostrado');
    Route::get('{idCaso}/historialPacientePostrado', array('as' => 'historialPacientePostrado', 'uses' => 'EnfermeriaPacientePostradoController@historialPacientePostrado'));
    Route::post('buscarHistorialPacientePostrado', 'EnfermeriaPacientePostradoController@buscarHistorialPacientePostrado');
    Route::get('editarPacientePostrado/{idForm}', 'EnfermeriaPacientePostradoController@edit');

    //Riesgo Caídas
    Route::post('ingresoRiesgoCaida', 'EnfermeriaRiesgoCaidaController@ingresoRiesgoCaida');
    Route::get('{idCaso}/historialRiesgoCaida', array('as' => 'historialRiesgoCaida', 'uses' => 'EnfermeriaRiesgoCaidaController@historialRiesgoCaida'));
    Route::post('buscarHistorialRiesgoCaidas', 'EnfermeriaRiesgoCaidaController@buscarHistorialRiesgoCaidas');
    Route::get('editarRiesgoCaidas/{idForm}', 'EnfermeriaRiesgoCaidaController@edit');
    Route::get('pdfHistorialRiesgoCaida/{caso}', array('as' => 'pdfHistorialRiesgoCaida', 'uses' => 'EnfermeriaRiesgoCaidaController@pdfHistorialRiesgoCaida'));

    //formulario nuevo macdems
    Route::post('ingresoEscalaMacdems', 'EnfermeriaEscalaMacdensController@ingresoEscalaMacdems');
    Route::get('{idCaso}/historialEscalaMacdems', array('as' => 'historialEscalaMacdems', 'uses' => 'EnfermeriaEscalaMacdensController@historialEscalaMacdems'));
    Route::post('buscarHistorialEscalaMacdems', 'EnfermeriaEscalaMacdensController@buscarHistorialEscalaMacdems');
    Route::get('editarEscalaMacdems/{idForm}', 'EnfermeriaEscalaMacdensController@edit');
    Route::get('pdfHistorialMacdems/{caso}', array('as' => 'pdfHistorialMacdems', 'uses' => 'EnfermeriaEscalaMacdensController@pdfHistorialMacdems'));



    //epicrisis
    Route::post('epicrisis', 'EpicrisisController@epicrisis');
    Route::post('existenDatosEpicrisis', 'EpicrisisController@existenDatosEpicrisis');
    Route::get('pdfInformeEpicrisis/{caso}', array('as' => 'pdfInformeEpicrisis', 'uses' => 'EpicrisisController@pdfInformeEpicrisis'));
    Route::post('guardarEvolucionEnfermeria', 'EpicrisisController@guardarEvolucionEnfermeria');
    Route::post('existenDatosEvolucionEnfermeria', 'EpicrisisController@existenDatosEvolucionEnfermeria');
    Route::post('datosBarthelEvolucionEnfermeria', 'EpicrisisController@datosBarthelEvolucionEnfermeria');
    
    //pertenencias
    Route::post('agregarPertenencia', 'PertenenciasController@agregarPertenencia');
    Route::get('obtenerPertenencias/{caso}', 'PertenenciasController@obtenerPertenencias');
    Route::post("modificarPertenencia", "PertenenciasController@modificarPertenencia");
    Route::post('eliminarPertenencia', 'PertenenciasController@eliminarPertenencia');
});

Route::group(array('prefix' => 'gestionMedica', 'middleware' => 'auth'), function () {
    //Diagnosticos 
    Route::get('{caso}/cargarDiagnosticos','DiagnosticoController@cargarDiagnosticos'); 
    Route::post('editarDiagnostico', 'GestionMedicaController@editarDiagnostico');
    Route::post('ingresarDiagnostico', 'GestionMedicaController@ingresarDiagnostico');

    Route::get('{idCaso}', 'GestionController@gestionMedica');
    //Formulario GES
    Route::post('agregarGes', 'GesController@agregarGes');
    Route::get('mostrarDiagnosticosGes/{caso}', 'GesController@mostrarDiagnosticosGes');
    Route::get('mostrarDiagnosticos/{caso}', 'GesController@mostrarDiagnosticos');
    Route::get('modificar_notificacion/{idFormulario}', 'GesController@modificar_notificacion');
    Route::get('eliminar_notificacion/{idFormulario}', 'GesController@eliminar_notificacion');

    //Indicaciones medicas
    Route::post('agregarIndicacionMedica', 'GestionMedicaController@agregarIndicacionMedica');
    Route::get('{caso}/ultimaIndicacionMedica', 'GestionMedicaController@ultimaIndicacion');
    Route::get('{caso}/cargarIndicaciones', 'GestionMedicaController@cargarIndicaciones')->name('cargarIndicacionesMedicas');
    Route::get('{id}/cargarIndicacionMedica', 'GestionMedicaController@cargarIndicacionMedica');
    Route::post('editarIndicacionMedica', 'GestionMedicaController@editarIndicacionMedica');
    Route::get('{caso}/indicacionDiaActual', 'GestionMedicaController@indicacionDiaActual');
    Route::get('{id}/eliminarIndicacion','GestionMedicaController@eliminarIndicacion');
    Route::get('{caso}/pdfResumenIndicaciones/{fecha}', 'GestionMedicaController@pdfResumenIndicaciones');
    
    //consulta primera indicacion
    Route::get('{caso}/consultaPrimeraIndicacion', 'GestionMedicaController@consultaPrimeraIndicacion');

    //Comentarios indicacion
    Route::get('{id}/cargarComentariosIndicacion','ComentariosAgregadosIndicacionMedicaController@cargarComentariosIndicacion');
    Route::post('agregarComentarioIndicacionMedica', 'ComentariosAgregadosIndicacionMedicaController@agregarComentario');
    Route::get('{id}/eliminarComentarioAgregado','ComentariosAgregadosIndicacionMedicaController@eliminarComentarioAgregado');
    
    //formulario uso restringido
    Route::post('agregarUsoRestringido', 'UsoRestringidoController@agregarUsoRestringido');
    Route::get('{idCaso}/historialUsoRestringido', array('as' => 'historialUsoRestringido', 'uses' => 'UsoRestringidoController@historialUsoRestringido'));
    Route::get('buscarHistorialUsoRestringido/{caso}', 'UsoRestringidoController@buscarHistorialUsoRestringido');
    Route::get('editarFormulario/{idForm}', 'UsoRestringidoController@edit');
    Route::get('ultimoDiagnostico/{caso}', 'UsoRestringidoController@ultimoDiagnostico');
    Route::get('{query}/consulta_antimicrobiano','UsoRestringidoController@consulta_antimicrobiano');

    //Registro clinico 
    Route::post('agregarRegistroClinico', 'RegistroClinicoController@agregarRegistroClinico');
    Route::get('buscarHistorialRegistroClinico/{caso}', 'RegistroClinicoController@buscarHistorialRegistroClinico');
    Route::get('editarRegistroClinico/{idForm}', 'RegistroClinicoController@edit');
    Route::post('eliminarRegistroClinico', 'RegistroClinicoController@eliminarRegistroClinico');
    
    //Formulario de solicitud de examen imagenologia
    Route::get('{caso}/infoPacienteExamen', 'ExamenMedicoController@infoPacienteExamen');
    Route::post('agregarExamenMedico', 'ExamenMedicoController@agregarExamenMedico');
    Route::get('{caso}/listarExamenesMedicos', 'ExamenMedicoController@listarExamenesMedicos');
    Route::get('eliminarExamenImageneologia/{id}','ExamenMedicoController@eliminarExamenImageneologia');
    Route::get('editarExamenImagenologia/{id}','ExamenMedicoController@editarExamenImagenologia'); 

    //IPD
    Route::get('{caso}/infoPacienteInforme', 'InformeProcesoDiagnosticoController@infoPacienteInforme');
    Route::post('agregarInformeProcesoDiagnostico', 'InformeProcesoDiagnosticoController@agregarInformeProcesoDiagnostico');
    Route::get('listarInformesProcesoDiagnostico/{caso}', 'InformeProcesoDiagnosticoController@listarInformesProcesoDiagnostico');
    Route::get('editarInformeProcesoDiagnostico/{id}', 'InformeProcesoDiagnosticoController@editarInformeProcesoDiagnostico');
    Route::get('eliminarInformeProcesoDiagnostico/{id}', 'InformeProcesoDiagnosticoController@eliminarInformeProcesoDiagnostico');
    Route::get('pdfInformeProcesoDiagnostico/{id}', 'InformeProcesoDiagnosticoController@pdfInformeProcesoDiagnostico');
	
	//Formulario solicitud electro y neuro
	Route::get('{caso}/infoPacienteElectroNeuro', 'ElectroNeuroController@datosPaciente');
	Route::post('agregarElectroNeuro', 'ElectroNeuroController@agregar');
	Route::get('historialElectroNeuro/{caso}', 'ElectroNeuroController@historial');
	Route::post('eliminarElectroNeuro', 'ElectroNeuroController@eliminar');
	Route::post('cargarElectroNeuro', 'ElectroNeuroController@cargar');
	Route::post('pdfElectroNeuro', 'ElectroNeuroController@pdf');
	
	//Formulario solicitud electroencefalograma
	Route::get('{caso}/infoPacienteElectroencefalograma', 'ElectroencefalogramaController@datosPaciente');
	Route::post('agregarElectroencefalograma', 'ElectroencefalogramaController@agregar');
	Route::get('historialElectroencefalograma/{caso}', 'ElectroencefalogramaController@historial');
	Route::post('eliminarElectroencefalograma', 'ElectroencefalogramaController@eliminar');
	Route::post('cargarElectroencefalograma', 'ElectroencefalogramaController@cargar');
	Route::post('pdfElectroencefalograma', 'ElectroencefalogramaController@pdf');
	
    //Examenes de laboratorio
    Route::post('agregarExamenLaboratorio', 'ExamenLaboratorioController@agregarExamenLaboratorio');
    Route::get('listarExamenLaboratorio/{caso}', 'ExamenLaboratorioController@listarExamenLaboratorio');
    Route::get('editarExamenLaboratorio/{idForm}', 'ExamenLaboratorioController@edit');
    Route::get('eliminarExamenLaboratorio/{idForm}','ExamenLaboratorioController@eliminarExamenLaboratorio'); 
	
	//Formulario solicitud electro y neuro
	Route::get('{caso}/infoPacienteSolicitudExamen', 'SolicitudExamenController@datosPaciente');
	Route::post('agregarSolicitudExamen', 'SolicitudExamenController@agregar');
	Route::get('historialSolicitudExamen/{caso}', 'SolicitudExamenController@historial');
	Route::post('eliminarSolicitudExamen', 'SolicitudExamenController@eliminar');
	Route::post('cargarSolicitudExamen', 'SolicitudExamenController@cargar');
	Route::post('pdfSolicitudExamen', 'SolicitudExamenController@pdf');
    
    //Formulario Interconsulta medica
    Route::get('obtenerDiagnosticosDatosPaciente/{caso}', 'GestionInterconsultaController@obtenerDiagnosticosDatosPaciente');
    Route::get('obtenerDiagnosticosPorId/{caso}/{idDiagnostico}', 'GestionInterconsultaController@obtenerDiagnosticosPorId');
    Route::post('agregarinterconsulta', 'GestionInterconsultaController@agregarinterconsulta');
    Route::get('historialDiagnosticosInterconsulta/{caso}', 'GestionInterconsultaController@historialDiagnosticosInterconsulta');
    Route::get('eliminar_interconsulta_medica/{idFormulario}', 'GestionInterconsultaController@eliminar_interconsulta_medica');
    Route::get('modificar_interconsulta_medica/{idFormulario}', 'GestionInterconsultaController@modificar_interconsulta_medica');
    Route::get('pdfInterconsulta/{idFormulario}/{caso}', 'GestionInterconsultaController@pdfInterconsulta');


});

Route::get("getDatosDiagnoticosMedico", "GestionMedicaController@getDatosDiagnoticosMedico");

Route::get("validarTipoControlEstada", "EnfermeriaHojaEnfermeriaController@validarTipoControlEstada");
Route::get("validarSelectTipoProcedimiento", "EnfermeriaHojaEnfermeriaController@validarSelectTipoProcedimiento");
Route::get("validarSelectAntibioticos", "EnfermeriaHojaEnfermeriaController@validarSelectAntibioticos");
Route::get("validarSelectNumeroProcedimiento", "EnfermeriaHojaEnfermeriaController@validarSelectNumeroProcedimiento");

//remotes de validaciones
//atencionEnfermeria
Route::get('validar_horario2', 'EnfermeriaPlanificacionCuidadosController@validar_horario2');
Route::get('validar_resp_atencion', 'EnfermeriaPlanificacionCuidadosController@validar_resp_atencion');
Route::get('validar_aetipo', 'EnfermeriaPlanificacionCuidadosController@validar_aetipo');
Route::get('validar_metodo1', 'EnfermeriaPlanificacionCuidadosController@validar_metodo1');
Route::get('validar_fio1', 'EnfermeriaPlanificacionCuidadosController@validar_fio1');
Route::get('validar_tipo_curacion', 'HojaCuracionController@validar_tipo_curacion');

//crearUsuario
Route::get('validar_establecimiento', 'AdministrarUsuarioController@validar_establecimiento');
Route::get('validar_especialidades', 'AdministrarUsuarioController@validar_especialidades');

Route::get('cronPixys', 'PyxisController@transaction');
Route::get('getDataPixysRemoto','PyxisController@getDataPixysRemoto');

//establecimientos
Route::get('{query}/consulta_establecimientos', 'GestionController@consulta_establecimientos');
Route::get('{query}/consulta_establecimientos_privados', 'GestionController@consulta_establecimientos_privados');
Route::get('{query}/consulta_extrasistema', 'GestionController@consulta_extrasistema');
//establecimientos


//Formulario directo de gestion enfermeria
Route::get('formularios/{idCaso}', 'GestionController@formularios');
Route::get('formulariosPediatrico/{idCaso}', 'GestionController@formulariosP');

Route::get('gestionEgresos', array('as' => 'gestionEgresos', 'uses' => function () {
    // $response = ListaDerivados::obtenerListaDerivados();
    // return View::make("Urgencia/ListaDerivados", ["response" => $response]);
    // $datos = GestionEgresos::compararPacientesEgresados();
    // Log::info($datos);
    if(Auth::user()->tipo == TipoUsuario::GRD){
        return View::make("GestionEgresos/listaEgresos");
    }else{
        return redirect()->route('indexPrincipal');
    }
}));

Route::get('descargarExcelResumencamas', 'IndexController@descargarExcelResumencamas');
Route::get('{query}/consulta_examenes_imagenes','EnfermeriaHojaEnfermeriaController@consulta_examenes_imagenes');
//epicrisis cuidados
Route::get('{query}/{tipo_tabla}/consultar_cuidados_epicrisis','EpicrisisController@consultar_cuidados_epicrisis');
Route::post('validar_cuidados_epicrisis', 'EpicrisisController@validar_cuidados_epicrisis');
Route::post('addaepicrisistipo', 'EpicrisisController@addaepicrisistipo');
Route::post('addcuidadoAlta', 'EpicrisisController@addcuidadoAlta');
Route::get('obtenerCuidadosAlta/{caso}/{formulario}', 'EpicrisisController@obtenerCuidadosAlta');
Route::post('eliminarCuidado', 'EpicrisisController@eliminarCuidado');
Route::get('obtenerCuidadoAlta/{obtenerCuidado}/{formulario}', 'EpicrisisController@obtenerCuidadoAlta');
Route::get('obtenerTipoCuidado/{idTipoCuidado}', 'EpicrisisController@obtenerTipoCuidado');
Route::post('modificarPCCuidado', 'EpicrisisController@modificarPCCuidado');


Route::post("fileuploadDiptico", "DipticoController@fileuploadDiptico");
Route::post("ingresarDocumentoDiptico", "DipticoController@ingresarDocumentoDiptico");
Route::post("quitarDocumentoDiptico/{id}", "DipticoController@quitarDocumentoDiptico");
Route::post("documentosDiptico", "DipticoController@documentosDiptico");
Route::get('descargarDocumentoDiptico/{id}', function ($id) {
    $path = storage_path() . "/data/documentosDiptico/";
    $solicitud = DocumentosDiptico::where("id", "=", $id)->first();
    return response()->download($path . $solicitud->documento);
});

Route::post("guardarRegistroVisita","RegistroVisitasController@guardar");
Route::post("buscarCaso","RegistroVisitasController@buscarCaso");
Route::post("buscarCasoPorNombre","RegistroVisitasController@buscarCasoPorNombre");
Route::post("buscarVisita","RegistroVisitasController@buscarVisita");
Route::post("salidaVisita","RegistroVisitasController@salidaVisita");
Route::post("guardarConfiguracionVisitasHistorial","ConfiguracionVisitasController@guardar");
Route::get("registro-visitas","RegistroVisitasController@vista");
Route::get("validarRutAcompanante","RegistroVisitasController@validarRutAcompanante");
Route::get('pdfHistorialRegistroVisitas/{caso}', array('as' => 'pdfHistorialRegistroVisitas', 'uses' => 'GestionController@pdfHistorialRegistroVisitas'));

//formularios ginecologia
Route::group(array('prefix' => 'formularios-ginecologia', 'middleware' => 'auth'), function () {
    
    //entrega documentos alta
    Route::get('{idCaso}/entrega-documentos-alta', array('as' => 'entrega-documentos-alta', 'uses' => 'FormulariosGinecologia\EntregaDocumentosAltaController@view'));
    Route::post('entrega-documentos-alta/save', array('as' => 'entrega-documentos-alta-save', 'uses' => 'FormulariosGinecologia\EntregaDocumentosAltaController@store'));
    
    Route::get('pdf/{idCaso}', 'FormulariosGinecologia\EntregaDocumentosAltaController@pdf');

    //consentimiento informado interrupcion embarazo
    Route::get('{idCaso}/consentimiento-informado-interrupcion-embarazo', array('as' => 'consentimiento-informado-interrupcion-embarazo', 'uses' => 'FormulariosGinecologia\ConsentimientoInformadoInterrupcionEmbarazoController@view'));
    Route::post('consentimiento-informado-interrupcion-embarazo/save', array('as' => 'consentimiento-informado-interrupcion-embarazo-save', 'uses' => 'FormulariosGinecologia\ConsentimientoInformadoInterrupcionEmbarazoController@store'));

    Route::get('consentimiento-informado-interrupcion-embarazo/pdf/{idCaso}', 'FormulariosGinecologia\ConsentimientoInformadoInterrupcionEmbarazoController@pdf');

    //epicrisis interrupcion gestacion iii trimestre
    Route::get('{idCaso}/epicrisis-interrupcion-gestacion-iii-trimestre', array('as' => 'epicrisis-interrupcion-gestacion-iii-trimestre', 'uses' => 'FormulariosGinecologia\EpicrisisInterrupcionGestacionIIITrimestreController@view'));
    Route::post('epicrisis-interrupcion-gestacion-iii-trimestre/save', array('as' => 'epicrisis-interrupcion-gestacion-iii-trimestre-save', 'uses' => 'FormulariosGinecologia\EpicrisisInterrupcionGestacionIIITrimestreController@store'));
    
    Route::get('epicrisis-interrupcion-gestacion-iii-trimestre/pdf/{idCaso}', 'FormulariosGinecologia\EpicrisisInterrupcionGestacionIIITrimestreController@pdf');
    
    //partograma
    Route::get('{idCaso}/partograma', array('as' => 'partograma', 'uses' => 'FormulariosGinecologia\PartogramaController@view'));
    Route::post('partograma/save', array('as' => 'partograma-save', 'uses' => 'FormulariosGinecologia\PartogramaController@store'));
    
    Route::post('partograma/grafico', array('as' => 'partograma-grafico', 'uses' => 'FormulariosGinecologia\PartogramaController@guardarPartograma'));
    Route::post('partograma/cargar-partograma', array('as' => 'cargar-partograma', 'uses' => 'FormulariosGinecologia\PartogramaController@datosPartograma'));
    
    Route::post('partograma/pdf', 'FormulariosGinecologia\PartogramaController@pdf');

    //solicitud de transfusion de productos sanguineos
    Route::get('{idCaso}/solicitud-transfusion-productos-sanguineos', array('as' => 'solicitud-transfusion-productos-sanguineos', 'uses' => 'FormulariosGinecologia\SolicitudTransfusionProductosSanguineosController@view'));
    Route::post('solicitud-transfusion-productos-sanguineos/save', array('as' => 'solicitud-transfusion-productos-sanguineos-save', 'uses' => 'FormulariosGinecologia\SolicitudTransfusionProductosSanguineosController@store'));
    
    Route::get('solicitud-transfusion-productos-sanguineos/pdf/{idCaso}', 'FormulariosGinecologia\SolicitudTransfusionProductosSanguineosController@pdf');
	
	//protocolo de parto
    Route::get('{idCaso}/protocolo-de-parto', array('as' => 'protocolo-de-parto', 'uses' => 'FormulariosGinecologia\ProtocoloDePartoController@view'));
    Route::post('protocolo-de-parto/save', array('as' => 'protocolo-de-parto-save', 'uses' => 'FormulariosGinecologia\ProtocoloDePartoController@store'));
    
    Route::get('protocolo-de-parto/pdf/{idCaso}', 'FormulariosGinecologia\ProtocoloDePartoController@pdf');

    //partograma get alergias
    Route::post('partograma-alergia-data', array('as' => 'partograma-alergia-data', 'uses' => 'FormulariosGinecologia\PartogramaController@getPartogramaAlergias'));    

});

Route::get('homologar_unidad_ginecologica', 'FormulariosGinecologia\EntregaDocumentosAltaController@homologar_unidad_ginecologica');
Route::post("moverACamaTemporal","CamaTemporalController@moverACamaTemporal")->middleware("auth");
Route::post("traerCamasTemporales","CamaTemporalController@traerCamas")->middleware("auth");
Route::post("getPacienteCamaTemporal","CamaTemporalController@getPacienteCamaTemporal")->middleware("auth");

Route::post("guardar_ventilacion_mecanica","FormularioVentilacionMecanicaController@guardar")->middleware("auth");
Route::post("listar_ventilacion_mecanica","FormularioVentilacionMecanicaController@lista")->middleware("auth");
Route::post("eliminar_ventilacion_mecanica","FormularioVentilacionMecanicaController@eliminar")->middleware("auth");

Route::post("guardar_examen_ginecoobstetrico","FormularioExamenGinecoobstetricoController@guardar")->middleware("auth");
Route::post("cargar_examen_ginecoobstetrico","FormularioExamenGinecoobstetricoController@cargar")->middleware("auth");
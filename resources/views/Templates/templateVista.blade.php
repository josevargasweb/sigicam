<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{URL::asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
    {{ HTML::style('css/bootstrap.css') }}
    {{ HTML::style('css/estilos.css') }}
    {{ HTML::style('css/jquery.dataTables.css') }}
    {{ HTML::style('css/datepicker3.css') }}
    {{ HTML::style('css/bootstrap-datetimepicker.min.css') }}
    {{ HTML::style('css/bootstrapValidator.min.css') }}
    {{ HTML::style('css/fileinput.min.css') }}
    {{ HTML::style('css/notifyme.css') }}
    {{ HTML::style('css/bootstrap-duallistbox.min.css') }}
    {{ HTML::style('css/offcanvas.css') }}
    {{ HTML::style('css/breadcrumb.css') }}
    {{ HTML::style('css/bootstrap-select.css') }}

    {{ HTML::style('css/bootstrap-rating.css') }}

    {{ HTML::style('css/jquery.dataTables.min.css') }}
    {{ HTML::style('css/buttons.dataTables.min.css') }}

    {{ HTML::style('plugins/fileupload/css/jquery.fileupload.css')}}
    {{ HTML::script('js/jquery-1.9.1.min.js') }}
    {{ HTML::script('js/promise.min.js') }}
    {{ HTML::script('js/jquery.dataTables.js') }}
    {{ HTML::script('js/jquery-ui.js') }}
    {{ HTML::script('js/Highcharts.js') }}
    {{ HTML::script('js/atacama.js') }}

    {{ HTML::script('js/moment-with-locales.min.js') }}
    {{ HTML::script('js/bootstrap.js') }}
    {{ HTML::script('js/bootbox.min.js') }}
    
    {{ HTML::script('js/dataTables.bootstrap.js') }}
    {{ HTML::script('js/fnReloadAjax.js') }}
    {{ HTML::script('js/jquery.dataTables.columnFilter.js') }}
    {{ HTML::script('js/bootstrap-datepicker.js') }}
    {{ HTML::script('js/bootstrap-datetimepicker.min.js') }}
    {{ HTML::script('js/locales/bootstrap-datepicker.es.js') }}
    {{ HTML::script('js/bootstrapValidator.min.js') }}
    {{ HTML::script('js/language/es_ES.js') }}
    {{ HTML::script('js/funciones.js') }}
    {{ HTML::script('js/fileinput.js') }}
    {{ HTML::script('js/notifyme.js') }}
    {{ HTML::script('js/jquery.bootstrap-duallistbox.min.js') }}
    {{ HTML::script('js/jquery.bootstrap.wizard.js') }}
    {{ HTML::script('js/bootstrap-select.js') }}
    {{ HTML::script('js/typeahead.bundle.js') }}
    {{ HTML::script('js/bootstrap-rating.js') }}
    {{ HTML::script('js/bootstrap-rating.min.js') }}
    {{ HTML::script('js/jquery.dataTables.min.js') }}
    {{ HTML::script('js/dataTables.responsive.min.js') }}
    {{ HTML::script('js/vfs_fonts.js') }}
    {{ HTML::script('js/pdfmake.min.js') }}
    {{ HTML::script('js/jszip.min.js') }}
    {{ HTML::script('js/dataTables.buttons152.min.js') }}
    {{ HTML::script('js/buttons.html5.min.js') }}
    {{ HTML::script('js/buttons.print.min.js') }}
    {{ HTML::script('js/jquery.inputmask.min.js') }}

    {{ HTML::script('plugins/fileupload/js/vendor/jquery.ui.widget.js')}}
    {{ HTML::script('plugins/fileupload/js/jquery.iframe-transport.js')}}
    {{ HTML::script('plugins/fileupload/js/jquery.fileupload.js')}}

    {{ HTML::script('js/date-euro.js')}}

    <title>@yield("titulo")</title>

    @yield("css")

    {{-- <header class="contenedor-navegacion-principal col-sm-12">
        <div class="contenedor-menu-principal col-xs-12">
          <div class="col-sm-2 col-xs-3 cont-logo-principal">
            <a href="{{URL::to('index')}}"><img src="{{URL::to('/')}}/img/Sigicamcirculo.png" alt="Logo SIGICAM" title="Logo SIGICAM"> </a>
          </div>
          <div class="col-sm-10 col-xs-9 nombre-proyecto-principal">
            <b>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</b><br>@if(Session::has("nombreEstablecimiento")) {{ Session::get("nombreEstablecimiento") }} @endif
          </div>
        </div>
    
    </header> --}}
    <body style="background-color:#f5f5f5 !important">
        @yield("section")    
    </body>
    

    @yield("js")
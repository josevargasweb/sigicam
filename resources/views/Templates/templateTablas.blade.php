<!DOCTYPE html>
<!--<html lang="es ">-->
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{URL::asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
  
    {{ HTML::style('css/tablas/bootstrap.min.css') }}
    {{ HTML::style('css/tablas/dataTables.bootstrap.min.css') }}
    {{ HTML::style('css/tablas/responsive.bootstrap.min.css') }}
    
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


    <!-- <link media="all" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"> -->
    {{ HTML::style('css/jquery.dataTables.min.css') }}
    <!-- <link media="all" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css"> -->
    {{ HTML::style('css/buttons.dataTables.min.css') }}

    {{ HTML::style('plugins/fileupload/css/jquery.fileupload.css')}}




    {{ HTML::script('js/tablas/jquery-3.3.1.js') }}
    {{ HTML::script('js/promise.min.js') }}


    {{ HTML::script('js/jquery.dataTables.js') }}
    {{ HTML::script('js/jquery-ui.js') }}
    {{ HTML::script('js/Highcharts.js') }}
    {{-- <script src="http://code.highcharts.com/modules/heatmap.js"></script> --}}
    {{ HTML::script('js/highcharts-more.js') }}
    {{ HTML::script('js/map.js') }}
     {{-- HTML::script('js/highmaps.js')  --}}
    {{ HTML::script('js/exporting.js') }}
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
     {{-- HTML::script('js/highcharts.js')  --}}

    
    
    {{ HTML::script('js/fileinput.js') }}
    {{ HTML::script('js/notifyme.js') }}
    {{ HTML::script('js/jquery.bootstrap-duallistbox.min.js') }}
    {{ HTML::script('js/jquery.bootstrap.wizard.js') }}
    {{ HTML::script('js/bootstrap-select.js') }}
    {{ HTML::script('js/typeahead.bundle.js') }}

    

    
    
    
    
    

    {{ HTML::script('js/bootstrap-rating.js') }}
    {{ HTML::script('js/bootstrap-rating.min.js') }}


<!--<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->
    {{ HTML::script('js/tablas/jquery.dataTables.min.js') }}
    {{ HTML::script('js/tablas/dataTables.bootstrap.min.js') }}
    {{ HTML::script('js/tablas/dataTables.responsive.min.js') }}
    {{ HTML::script('js/tablas/responsive.bootstrap.min.js') }}

    {{-- <link rel="stylesheet" type="text/css" href="/vendor/formvalidation/css/formValidation.min.css"> --}}



{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script> --}}

{{-- <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> --}}

{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script> --}}

{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> --}}


{{ HTML::script('js/vfs_fonts.js') }}
{{ HTML::script('js/pdfmake.min.js') }}
{{ HTML::script('js/jszip.min.js') }}
{{--  HTML::script('js/buttons.flash.min.js')  --}}
{{ HTML::script('js/dataTables.buttons152.min.js') }}
{{ HTML::script('js/buttons.html5.min.js') }}
{{ HTML::script('js/buttons.print.min.js') }}
{{ HTML::script('js/jquery.inputmask.min.js') }}

{{ HTML::script('plugins/fileupload/js/vendor/jquery.ui.widget.js')}}
{{ HTML::script('plugins/fileupload/js/jquery.iframe-transport.js')}}
{{ HTML::script('plugins/fileupload/js/jquery.fileupload.js')}}

{{ HTML::script('js/date-euro.js')}}

    <title>@yield("titulo")</title>
    <script>

        $( document ).ready(function() {
            $("#simbolo").hide();
            $("#descripcionPaciente").hide();
            $('#notification_count').hide();
            //notificacion de transito
            $('#notification_transito').hide();
            //categorizados
            $('#notification_categorizado').hide();
            $('#notification_estudios').hide();
            $('#notification_general').hide();
            //traslado envidas y recibidas en curso
            $('#notification_enviadasencurso').hide();
            $('#notification_recibidasencurso').hide();
            //notificacion de recuperación
            $('#notification_recuperacion').hide();
            //resultado enviadas y recibidas
            $('#notification_resultado').hide();
        });    
        

        var resize = function () {
            var width = $("#main").width();
            $(".miga").width(width);
        }

        var resizeBreadCrumb = function () {
            var height = $("#navbar ").height();
            if (height > 50) $("#b2").css("float", "left");
            else $("#b2").css("float", "right");
        }

        var showLoad = function(){
            console.log("show. templa");
            $("#dvLoading").show();
            console.log("show template");
        };

        var hideLoad = function(){
            $("#dvLoading").hide();
        };

        $(function () {
            $("#cerrar-st1, #cerrar-st2").on("click", function(){
                sessionStorage.clear();
            });
            //
            resizeBreadCrumb();

            $("#bread").width($(".navbar-top").width());

            bootbox.setDefaults({locale: "es"});

            $('[data-toggle="offcanvas"]').click(function () {
                $('.row-offcanvas').toggleClass('active')
            });

            $(window).on('resize', function () {
                $("#bread").width($(".navbar-top").width());
                resizeMapaCamas();
                resizeBreadCrumb();
            });

            //waitForMsg();
            //waitForMsgTransito();
            //waitForMsgCategorizado();
            //waitForMsgEstudios();
            waitForMsgGeneral();
            waitForMsgSolicitudesTraslados();
        });

            function waitForMsgGeneral() {
                /* console.log("entro"); */
                
                

                var session = "{{ Session::get('idEstablecimiento') }}";
                
                if(session == ""){
                    console.log("VACIO CMS");
                    id_estab = 0;
                }
                else{
                    id_estab = session;
                }

                //notificacion de transito
                $.ajax({
                        type: "POST",
                        url: "{{URL::to('/')}}/contadorGeneralnotificaciones",
                        data: {establecimiento: id_estab },
                        dataType: "json",
                        success: function(data) {
                            console.log("general", data);

                            if(data.exito){

                                $('#notification_count').show();
                                $('#notification_count').text(data.cEspera);

                                $('#notification_estudios').show();
                                 $('#notification_estudios').text(data.cEstudios);

                                $('#notification_categorizado').show();
                                $('#notification_categorizado').text(data.cCategorizados);

                                $('#notification_transito').show();
                                 $('#notification_transito').text(data.cTransito);

                                $('#notification_recuperacion').show();
                                $('#notification_recuperacion').text(data.cRecuperacion);

                                $('#notification_general').show();
                                $('#notification_general').text(data.exito);
                                console.log('resultadooo!');
                                console.log(data.resultado);

                                console.log("data general")
                                console.log(data.exito);
                                setTimeout(
                                    waitForMsgGeneral,
                                    10000000
                                );
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {

                            console.log("holas error general");
                            console.log(errorThrown);
                        }
                });
            };

            function waitForMsgSolicitudesTraslados() {
                /* console.log("entro"); */
                
                

                var session = "{{ Session::get('idEstablecimiento') }}";
                
                if(session == ""){
                    console.log("VACIO CMS");
                    id_estab = 0;
                }
                else{
                    id_estab = session;
                }

                //notificacion de transito
                $.ajax({
                        type: "POST",
                        url: "{{URL::to('/')}}/contadorTraslados",
                        data: {establecimiento: id_estab },
                        dataType: "json",
                        success: function(data) {
                            console.log("general", data);

                            if(data.enviadasEnCurso){

                                $('#notification_enviadasencurso').show();
                                $('#notification_enviadasencurso').text(data.enviadasEnCurso);

                                $('#notification_recibidasencurso').show();
                                $('#notification_recibidasencurso').text(data.recibidasEnCurso);

                                $('#notification_resultado').show();
                                $('#notification_resultado').text(data.resultado);

                                console.log('enviadas!');
                                console.log(data.enviadasEnCurso);
                                console.log('recibidas');
                                console.log(data.recibidasEnCurso);

                                console.log("resultado");
                                console.log(data.resultado);

                                setTimeout(
                                    waitForMsgSolicitudesTraslados,
                                    10000000
                                );
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {

                            console.log("holas error enviadas :c");
                            console.log(errorThrown);
                        }
                });
            };
    </script>

    <style type="text/css" media="screen">

        #main_ {
                margin-left: 250px;
            } 

        #dvLoading{
            background-color:rgba(188,188,188,0.4);
            height: 150px;
            width: 300px;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            margin: -25px 0 0 -25px;
            text-align: center;
            margin-left:-150px;
        }

        .classLoadingText{
            position: relative;
            z-index: 1001;
            top: 25%;
            font-size: 14px;
        }

        #notification_count {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }

        #notification_transito {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }

        #notification_recuperacion {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }

        #notification_categorizado {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }

        #notification_estudios {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }
        /* suma de notificaciones de Estado pacientes */
        #notification_general {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }
        #notification_enviadasencurso {
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }

        #notification_recibidasencurso{
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }
        /* suma de notificaciones de Solicitudes traslado interno */
        #notification_resultado{
            padding: 3px 3px 3px 5px;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
            margin-left: 10px;
            border-radius: 9px;
            -moz-border-radius: 9px;
            -webkit-border-radius: 9px;
            
            margin-top: -1px;
            font-size: 10px;
        }


        .has-success .form-control {
            border-color: #ccc;
        }

        .has-success .help-block, .has-success .control-label, .has-success .radio, .has-success .checkbox, .has-success .radio-inline, .has-success .checkbox-inline{
            color:#333;
        }

        .has-success .input-group-addon{
            border-color: #ccc;
            background-color: #eee;
        }

        .letraNormal{
			font-size: 12px;
		}
    </style>

    
    @yield("css")
</head>
<body>
<div class="row" style="margin-top: 1px; display:none;">
    <div class="col-md-2 pad-der desaparecer">
    </div>
    <div class="col-md-9 pad-izq">
        <div class="col-md-12 text-der cabeceraTitulo">
            <div class="fondo" style="margin: 0;">
                <h1 style='font-family: "Antipasto","Open Sans",sans-serif'><b>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</b></h1>@if(Session::has("nombreEstablecimiento"))
                    <h4> {{ Session::get("nombreEstablecimiento") }} </h4>@endif</div>
        </div>

            <nav class="col-md-12 navbar navbar-top navbar-default marg-abajo display-flex menuMigas" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                            aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div id="navbar" class="collapse navbar-collapse" style="margin: 0; padding: 0;" aria-expanded="false">
                    <div class="col-md-12">
                        <ul class="nav navbar-nav">
                            <ol class="breadcrumb" style="background: none;">
                                <li><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
                                @yield("miga")
                            </ol>
                        </ul>
                        <ul id="b2" class="nav navbar-nav" style="float: right">
                            <li>
                                {{HTML::link("#",mb_strtoupper(Session::get("usuario")->nombres." ".Session::get("usuario")->apellido_paterno." ".Session::get("usuario")->apellido_materno))}}
                            </li>
                            <li>
                                {{ HTML::link('cerrarSesion','Cerrar Sesión', array("id"=>"cerrar-st2")) }}
                            </li>
                        </ul>
                    </div>


                </div>
            </nav>
        </div>
</div>


  <header class="contenedor-navegacion-principal col-sm-12">
    <div class="contenedor-menu-principal col-xs-12">
      <div class="col-sm-2 col-xs-3 cont-logo-principal">
        <a href="{{URL::to('index')}}"><img src="{{URL::to('/')}}/img/Sigicamcirculo.png" alt="Logo SIGICAM" title="Logo SIGICAM"> </a>
      </div>
      <div class="col-sm-10 col-xs-9 nombre-proyecto-principal">
        <b>Sistema inteligente para la gestión y análisis de la dotación de camas en la red asistencial del sector público</b><br>@if(Session::has("nombreEstablecimiento")) {{ Session::get("nombreEstablecimiento") }} @endif
      </div>
    </div>

    <nav class="col-xs-12 navbar navbar-top navbar-default menu-opciones-principal" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed navbar-toggle-index-principal" data-toggle="collapse" data-target="#navbar-principal" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div id="navbar-principal" class="collapse navbar-collapse navbar-ex2-collapse" style="margin: 0; padding: 0;" aria-expanded="false">
                    <div class="col-xs-12">
                        <ul class="nav navbar-nav">
                            <ol class="breadcrumb" style="background: none;">
                                <li><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
                                @yield("miga")
                            </ol>
                        </ul>
                        <ul id="b2" class="nav navbar-nav menu-index-principal" style="float: right">
                            <li>
                                {{HTML::link("#",mb_strtoupper(Session::get("usuario")->nombres." ".Session::get("usuario")->apellido_paterno." ".Session::get("usuario")->apellido_materno))}}
                            </li>
                            <li>
                                <a href="{{URL::to('vistaDocumentos/manual')}}"> Descargar manual de usuario</a>
                            </li>
                            <li>
                                {{ HTML::link('cerrarSesion','Cerrar Sesión', array("id"=>"cerrar-st1")) }}
                            </li>
                        </ul>
                    </div>


                </div>
    </nav>


  </header>
















<div class="container-fluid" style="margin-top: 10px;">

    <div class="row">

        @include("Templates/menu")
        <div class="col-md-8  main" id="main_>


            {{--<div class="col-xs-12 col-sm-9 col-md-12">
                <div style='background-color:red;color:white;height:30px;text-align:center;'>
                    <span style='font-weight:bold;font-size:20px;'>
                    EN MANTENCIÓN
                    </span>
                 </div>
                 </div>
                 --}}
                @yield("section")
        </div>
        <div class="col-md-2" id="simbolo">
            @include('Gestion/Simbologia')
        </div>
        
         <div class="descripcionPaciente" id="descripcionPaciente"  style=" width: 19%;"  >
            
        </div>
        <div id="dvLoading" style="display: none;">
            <span class="classLoadingText" id="texto-div-cargando">Cargando página, por favor espere </span><br>
            {{ HTML::image('images/ajax-loader.gif', '', array("style" => " margin-top: 50px;")) }}
        </div>
            
        </div>
    </div>
    
</div>

{{-- Se cambiio para ejecutar scripts, si alguna funcionalidad falla, revisar y ponerr antes del @yield del css --}}
@yield("script")

</body>
</html>

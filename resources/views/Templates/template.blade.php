<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{URL::asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
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
    {{ HTML::style('css/sweetalert2.min.css') }}
    {{ HTML::style('css/bootstrap-rating.css') }}


    <!-- <link media="all" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"> -->
    {{ HTML::style('css/jquery.dataTables.min.css') }}
    <!-- <link media="all" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css"> -->
    {{ HTML::style('css/buttons.dataTables.min.css') }}

    {{ HTML::style('plugins/fileupload/css/jquery.fileupload.css')}}
    @hasSection('estilo-tabla')
        @yield('estilo-tabla')
    @endif

    @hasSection('estilo-tablas-verdes')
        @yield('estilo-tablas-verdes')
    @endif


      <!-- boostrap new icons -->
  {{ HTML::style('css/smess/bootstrap-icons/font/bootstrap-icons.css') }}

  <!-- notificador de alertas styles -->
  {{ HTML::style('css/smess/smess-1.0.css') }}


    <!--  HTML::script('js/jquery-1.11.1.min.js')  -->
    <!--  HTML::script('js/jquery-1.12.4.js')  -->
    {{ HTML::script('js/jquery-1.9.1.min.js') }}
    {{ HTML::script('js/promise.min.js') }}
    {{ HTML::script('js/jquery.dataTables.js') }}
    {{ HTML::script('js/jquery-ui.js') }}
    {{ HTML::script('js/highcharts.js') }}

    {{--  HTML::script('js/highcharts-more.js')  --}}
    {{-- HTML::script('js/map.js') --}}
    {{-- <script src="https://code.highcharts.com/highcharts.js"></script> --}}

    {{-- <script src="https://code.highcharts.com/highcharts-more.js"></script> --}}
    {{-- <script src="https://code.highcharts.com/maps/modules/map.js"></script> --}}
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
    {{ HTML::script('js/sweetalert2.all.min.js') }}
    {{ HTML::script('js/funciones.js') }}



    {{ HTML::script('js/fileinput.js') }}
    {{ HTML::script('js/notifyme.js') }}
    {{ HTML::script('js/jquery.bootstrap-duallistbox.min.js') }}
    {{ HTML::script('js/jquery.bootstrap.wizard.js') }}
    {{ HTML::script('js/bootstrap-select.js') }}
    {{ HTML::script('js/typeahead.bundle.js') }}









    {{ HTML::script('js/bootstrap-rating.js') }}
    {{ HTML::script('js/bootstrap-rating.min.js') }}


    <!--<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->
    {{ HTML::script('js/jquery.dataTables.min.js') }}
    {{ HTML::script('js/dataTables.responsive.min.js') }}
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

  <!-- notificador de alertas library js-->
  {{ HTML::script('js/formularios_ginecologia/smess-1.0.js') }}

  <!-- Scripts -->

  <script>
    const alergiaDataURL = "{{URL::route('partograma-alergia-data')}}";
  </script>


  <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <title>@yield("titulo")</title>

    <script type="text/javascript">
        function copyToClipboard() {
            var aux = document.createElement("input");
            //aux.setAttribute("value", "Debido a las medidas de seguridad del sistema, no esta permitido realizar impresión de pantalla de esta página.");
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
            swalNormal.fire({
            title: 'Captura de pantalla desactivada',
            })
        }
    </script>

    <script type="text/javascript">
        @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN)
        //alert("admin");
        @else
        window.addEventListener("keyup", function (e) {
        if (e.keyCode == 44) {
                //console.log("captura de pantalla");
                copyToClipboard();
            }
        });
        @endif

        @if(Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER)
            /* console.log("tipos permitido"); */
        @else
            window.addEventListener("focus", function () {
            document.body.style.display = "block";
        });
        @endif

        @if(Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER)
            /* console.log("tipos permitido"); */
        @else
        window.addEventListener("blur", function () {
            document.body.style.display = "none";
        });
        @endif
    </script>

    <script>
        $( document ).ready(function() {
            $("#simbolo").hide();
            $("#descripcionPaciente").hide();
            $('#notification_count').hide();
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
            $("#dvLoading").show();
        };

        var hideLoad = function(){
            $("#dvLoading").hide();
        };

        $(function () {

            //Plugin para poder hacer busquedas en datatables con acentos y traer resultados igual 
            function removeAccents ( data ) {
                if ( data.normalize ) {
                    // Use I18n API if avaiable to split characters and accents, then remove
                    // the accents wholesale. Note that we use the original data as well as
                    // the new to allow for searching of either form.
                    return data +' '+ data
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '');
                }
            
                return data;
            }
            if(jQuery.fn.DataTable){
                var searchType = jQuery.fn.DataTable.ext.type.search;
            
                searchType.string = function ( data ) {
                    return ! data ?
                        '' :
                        typeof data === 'string' ?
                            removeAccents( data ) :
                            data;
                };
                
                searchType.html = function ( data ) {
                    return ! data ?
                        '' :
                        typeof data === 'string' ?
                            removeAccents( data.replace( /<.*?>/g, '' ) ) :
                            data;
                };
            }
       
            //Fin del plugin de traer resultados con acento

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

            waitForMsg();
        });

        function waitForMsg() {
            var session = "{{ Session::get('idEstablecimiento') }}";

            if(session == ""){
                id_estab = 0;
            }
            else{
                id_estab = session;
            }

            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/notificaciones",
                data: {establecimiento: id_estab },
                dataType: "json",
                success: function(data) {

                    if(data.exito){
                        $('#notification_count').show();
                        $('#notification_count').text(data.exito);
                        setTimeout(
                            waitForMsg,
                            10000000
                        );
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log("error: ",textStatus);
                }
            });
        };

        $(document).ready(function(){
            /* Ubicacion del menu y el cuerpo */

            $("#gestion").mouseover(function(){
                $(".menuIzquierdo").css('z-index',1);
            });

            $("#gestion").mouseleave(function(){
                $(".menuIzquierdo").css('z-index',0);
            });


        });

    </script>

    <style type="text/css" media="screen">

        #main_ {
            margin-left: 250px;
           /*  width: 85%; */
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
        /* tablet verticales  min 480*/
        @media only screen and (max-width: 767px) {
            .container-fluid {
                padding-top: 190px !important;
            }
        }
        /* tablet horizontales */
        @media only screen and (min-width: 768px) and (max-width: 1023px) {
            .container-fluid {
                padding-top: 160px !important;
            }
        }
        /* Notes */
        @media only screen and (min-width: 1024px) and (max-width: 1199px) {
            .container-fluid {
                padding-top: 160px !important;
            }
        }
        /* Escritorios */
        @media only screen and (min-width: 1200px) {
            .container-fluid {
                padding-top: 130px !important;
            }
        }

        /* Menu izquierdo espacio */
        .menuIzquierdo {
            overflow: auto !important;
            direction: rtl ;
            text-align: left;
            height: 75%;
            padding-left: 0;
        }
            /* diseño barra scroll */
            .menuIzquierdo::-webkit-scrollbar{
                width: 5px;
            }

            .menuIzquierdo::-webkit-scrollbar-thumb{
                background: #1E9966;
            }

        @media only screen and (max-width: 1023px) {
            #main_ {
                margin-left: 190px;
            }
            /* valor maximo que puede tomar el menu */
            .menuIzquierdo{
                width: 500px !important;
            }

                .menuIzquierdo #accordion{
                    padding-right: 300px !important;
                    margin-right: 10px;
                }
        }

        @media only screen and (min-width: 1024px) {
            #main_ {
                margin-left: 250px;
            }
            /* valor maximo que puede tomar el menu */
            .menuIzquierdo{
                width: 600px !important;
            }

                .menuIzquierdo #accordion{
                    padding-right: 340px !important;
                    margin-right: 25px;
                }
        }
    </style>

    @yield("css")
</head>

<body>
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
                <button style="margin-left: 190px;" type="button" id="menu_" class="hamburger is-open" data-toggle="offcanvas">
                    <span class="hamb-top"></span>
                            <span class="hamb-middle"></span>
                            <span class="hamb-bottom"></span>
                </button>

                <button type="button" class="navbar-toggle collapsed navbar-toggle-index-principal" data-toggle="collapse" data-target="#navbar-principal" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="navbar-principal" class="collapse navbar-collapse navbar-ex2-collapse" style="margin: 0; padding: 0;" aria-expanded="false">
                <div class="col-xs-12">
                    <ul class="nav navbar-nav migax" style="margin-left: 200px;">
                        <ol class="breadcrumb" style="background: none;">
                            <li style="margin-left: 30px;"><a href="{{URL::to('index')}}"><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
                            @yield("miga")
                        </ol>
                    </ul>
                    <ul id="b2" class="nav navbar-nav menu-index-principal" style="float: right">
                        <li>{{HTML::link("#",mb_strtoupper(Session::get("usuario")->nombres." ".Session::get("usuario")->apellido_paterno." ".Session::get("usuario")->apellido_materno))}}</li>
                        <li>{{ HTML::link('descargarManual', 'Descargar manual del usuario') }}</li>
                        <li>{{ HTML::link('cerrarSesion','Cerrar Sesión', array("id"=>"cerrar-st1")) }}</li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <div class="container-fluid"  id="cuerpo">

        <div class="row">
            @include("Templates/menu")
            <div class="col-md-7  main" id="main_">
                @yield("section")
            </div>

            <div class="col-md-2" id="simbolo">
				@if(Session::get("usuario")->tipo !== "oirs" && Session::get("usuario")->tipo !== "visualizador")
                @include('Gestion/Simbologia')
				@endif
            </div>

            <div class="descripcionPaciente" id="descripcionPaciente"  style=" width: 19%;"  ></div>

            <div id="dvLoading" style="display: none;">
                <span class="classLoadingText" id="texto-div-cargando">Cargando, por favor espere </span><br>
                {{ HTML::image('images/ajax-loader.gif', '', array("style" => " margin-top: 50px;")) }}
            </div>

        </div>
    </div>

    @yield("script")
</body>
</html>

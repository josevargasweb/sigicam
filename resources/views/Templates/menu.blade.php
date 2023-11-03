
<script>
	function cargarMenuOpciones(){
		$.ajax({
            url: '{{URL::to("menuOpciones")}}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            type: "get",
            success: function(data){
                var html = "<div class='ul'>";
                    $.each(data, function(indice, elemento) {
                        $.each(elemento, function(indice2, elemento2) {
                            //console.log( indice2 + ": " + elemento2);
                            if (indice2 == 0) {
                                html += "<li class='dropdown-submenu padding-submenu' id='"+indice+"'>"+elemento2[0]+"<ul class='dropdown-menu solo-menu'>";
                            }else{
                                html+="<li class='dropdown-submenu' style='position:relative;z-index: 500;'> <div class='margen'> <a href='{{ URL::to('/unidad') }}/"+elemento2[2]+"' style='color:#04B404 !important'>"+elemento2[0]+"</a> </div></li>";
                            }
                        });
                        html+="</ul></li>";
                    });

                html += "</div>";
                $("#gestion").html(html);

            },
            error: function(error){
                console.log(error);
            }
        });
	}
    $( document ).ready(function() {

        cargarMenuOpciones();
    });


</script>

<style>
    /* ajustes generales */
    /* color verde y texto blanco al pararse sobre opcion del menú */
    .heading {
        background-size: 200% 100%;
        /* background-image: linear-gradient(to right, white 50%, #1e9966 50%); */
        /* transition: background-position 0.7s; */
        border: 1px solid #1e9966 !important;
     }

     /* quitar el subrayado al pararse sobre los titulos de los menu */
     .panel-title a:hover {
         text-decoration: none;
     }

     /* efectos al pararse sobre un elemento del menu */
    .heading:hover {
        color: white !important;
        text-decoration: none;
        /* background-position: -100% 0; */
        background-color: #1e9966 !important;
        border: 1px solid white !important;
    }

    /* Elementos de lista sin . y con linea que divide */
    .dropdown-submenu {
        position: relative;
        list-style: none;
        border-bottom:1px #ccc solid;
    }

    /* Ultimo elemento de la lista sin linea */
    .dropdown-submenu:last-child{
    border-bottom:none;
    }

    /* ajustes generales */

    /* gestion de camas*/
    #gestion .ul li {
        padding: 8px;
    }
    #gestion li a {
        color: black !important;
        text-decoration: none;
    }
    #gestion .ul li:hover {
        background-color:#D1FEBC;
        color: #1e9966;
    }
    /* gestion de camas*/

    /* estado Pacientes */
    #urgenciaMenu .ul li {
        padding: 8px;
    }
    #urgenciaMenu li a {
        color: black;
        text-decoration: none;
    }
    #urgenciaMenu .ul li:hover {
        background-color:#D1FEBC;
    }
    /* estado Pacientes */

    /* HospitalizacionMenu */
    #HospitalizacionMenu .ul li {
        padding: 8px;
    }
    #HospitalizacionMenu li a {
        color: black;
        text-decoration: none;
    }
    #HospitalizacionMenu .ul li:hover {
        background-color:#D1FEBC;
    }
    /* HospitalizacionMenu */

    /* Derivación */
    #DerivacionMenu .ul li {
        padding: 8px;
    }
    #DerivacionMenu li a {
        color: black;
        text-decoration: none;
    }
    #DerivacionMenu .ul li:hover {
        background-color:#D1FEBC;
    }
    /* Derivación */


    /* Solicitud de traslado interno */
    #solicitudTrasladoInterno .ul li {
        padding: 8px;
    }
    #solicitudTrasladoInterno li a {
        color: black;
        text-decoration: none;
    }
    #solicitudTrasladoInterno .ul li:hover {
        background-color:#D1FEBC;
    }
    /* Solicitud de traslado interno */    

    /* Reportes */
    #estadistica .ul li{
        padding: 8px;
    }
    #estadistica li a {
        color: black;
        text-decoration: none;
    }
    #estadistica .ul li:hover {
        background-color:#D1FEBC;
    }
    /* Reportes */

    /* Buscador */
    #buscadorMenu .ul li {
        padding: 8px;
    }
    #buscadorMenu li a {
        color: black;
        text-decoration: none;
    }
    #buscadorMenu .ul li:hover {
        background-color:#D1FEBC;
    }
	/* Gestión de visitas*/
    #visitasMenu .ul li {
        padding: 8px;
    }
    #visitasMenu li a {
        color: black;
        text-decoration: none;
    }
    #visitasMenu .ul li:hover {
        background-color:#D1FEBC;
    }

    /* contingencia */
    #contingenciaMenu .ul li {
        padding: 8px;
    }
    #contingenciaMenu li a {
        color: black;
        text-decoration: none;
    }
    #contingenciaMenu .ul li:hover {
        background-color:#D1FEBC;
    }
    /* contingencia */

    /* Administracion */
    #administracionMenu .ul li {
        padding: 8px;
    }

    /* hover verde a los elementos de una lista */
    #administracionMenu .ul li:hover {
        background-color:#D1FEBC;
    }

    /*  texto de elementos de administracion con letra color negro */
    #administracionMenu li a {
        color: black;
        text-decoration: none;
    }
    /* Administracion */

    #ingresarPaciente .ul li {
        padding: 8px;
    }
    #ingresarPaciente li a {
        color: black;
        text-decoration: none;
    }
    #ingresarPaciente .ul li:hover {
        background-color:#D1FEBC;
    }

    #GestionEgresosMenu .ul li {
        padding: 8px;
    }

    #GestionEgresosMenu .ul li:hover {
        background-color: #D1FEBC;
    }

    #GestionEgresosMenu li a {
        color: black;
        text-decoration: none;
    }

    .dropdown-submenu>.dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -6px;
        margin-left: -1px;
        -webkit-border-radius: 0 6px 6px 6px;
        -moz-border-radius: 0 6px 6px;
        border-radius: 0 6px 6px 6px;
    }

    .dropdown-submenu:hover>.dropdown-menu {
        display: block;
    }

    .dropdown-menu{
        min-width: 240px !important;
    }

    .margen{
        margin-left: 13px;
        font-size:12px;
    }

    /*diseño y animacion hamburger*/
    .hamburger {
        position: absolute;
        top: 7px;
        z-index: 999;
        display: block;
        width: 28px;
        height: 28px;
        margin-left: 5px;
        background: transparent;
        border: none;
    }
    .hamburger:hover,
    .hamburger:focus,
    .hamburger:active {
        outline: none;
    }

    .hamburger.is-closed:before {
        content: '';
        display: block;
        width: 100px;
        font-size: 14px;
        color: #fff;
        line-height: 32px;
        text-align: center;
        opacity: 0;
        /* -webkit-transform: translate3d(0,0,0); */
        -webkit-transition: all .35s ease-in-out;
    }

    .hamburger.is-closed:hover:before {
        opacity: 1;
        display: block;
        /* -webkit-transform: translate3d(-100px,0,0); */
        -webkit-transition: all .35s ease-in-out;
    }

    .hamburger.is-closed .hamb-top,
    .hamburger.is-closed .hamb-middle,
    .hamburger.is-closed .hamb-bottom,
    .hamburger.is-open .hamb-top,
    .hamburger.is-open .hamb-middle,
    .hamburger.is-open .hamb-bottom {
    position: absolute;
    left: 0;
    height: 4px;
    width: 100%;
    }
    .hamburger.is-closed .hamb-top,
    .hamburger.is-closed .hamb-middle,
    .hamburger.is-closed .hamb-bottom {
    background-color: white;
    }
    .hamburger.is-closed .hamb-top {
    top: 5px;
    -webkit-transition: all .35s ease-in-out;
    }
    .hamburger.is-closed .hamb-middle {
    top: 50%;
    margin-top: -2px;
    }
    .hamburger.is-closed .hamb-bottom {
    bottom: 5px;
    -webkit-transition: all .35s ease-in-out;
    }

    .hamburger.is-closed:hover .hamb-top {
    top: 0;
    -webkit-transition: all .35s ease-in-out;
    }
    .hamburger.is-closed:hover .hamb-bottom {
    bottom: 0;
    -webkit-transition: all .35s ease-in-out;
    }
    .hamburger.is-open .hamb-top,
    .hamburger.is-open .hamb-middle,
    .hamburger.is-open .hamb-bottom {
    background-color: white;
    }
    .hamburger.is-open .hamb-top,
    .hamburger.is-open .hamb-bottom {
    top: 50%;
    margin-top: -2px;
    }
    .hamburger.is-open .hamb-top {
    -webkit-transform: rotate(45deg);
    -webkit-transition: -webkit-transform .2s cubic-bezier(.73,1,.28,.08);
    }
    .hamburger.is-open .hamb-middle { display: none; }
    .hamburger.is-open .hamb-bottom {
    -webkit-transform: rotate(-45deg);
    -webkit-transition: -webkit-transform .2s cubic-bezier(.73,1,.28,.08);
    }
    .hamburger.is-open:before {
    content: '';
    display: block;
    width: 100px;
    font-size: 14px;
    color: #fff;
    line-height: 32px;
    text-align: center;
    opacity: 0;
    -webkit-transform: translate3d(0,0,0);
    -webkit-transition: all .35s ease-in-out;
    }
    .hamburger.is-open:hover:before {
    opacity: 1;
    display: block;
    /* -webkit-transform: translate3d(-100px,0,0); */
    -webkit-transition: all .35s ease-in-out;
    }

    .overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(250,250,250,.8);
    z-index: 1;
    }
</style>


<div id="wrapper">
    {{-- <div id="page-content-wrapper">
        <button style="margin-left: 15px;" type="button" id="menu_" class="hamburger is-closed" data-toggle="offcanvas">
            <span class="hamb-top"></span>
                  <span class="hamb-middle"></span>
                    <span class="hamb-bottom"></span>
        </button>
    </div> --}}
    <br><br>

    <div class="overlay"></div>

    <div class="col-lg-2 col-md-3 col-xs-3 pad-der-10 menuDerecha sidenav menuIzquierdo" id="sideNavigation" >

        <div class="panel-group sidebar-offcanvas sidebar-offcanvas" id="accordion">
			@if(Session::get("usuario")->tipo != TipoUsuario::OIRS)
            <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-parent="#accordion" href="{{URL::to('index')}}">
                            Inicio
                        </a>
                    </h4>
                </div>
            </div>
			@endif

            @if(Session::get("usuario")->tipo == TipoUsuario::GRD)
            {{-- || Session::get("usuario")->tipo === TipoUsuario::MASTER) --}}
                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#GestionEgresosMenu">
                                Gestión de Egresos
                            </a>
                        </h4>
                    </div>
                    <div id="GestionEgresosMenu" class="panel-collapse collapse">
                        <div class="ul">
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('gestionEgresos'), 'Gestionar Egresos')}}
                            </li>
                        </div>
                    </div>
                </div>
            @endif
            @if (Session::get("usuario")->tipo !== TipoUsuario::ADMINSS )
                @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN
                ||Session::get("usuario")->tipo === TipoUsuario::IAAS
                ||Session::get("usuario")->tipo === TipoUsuario::DIRECTOR
                ||Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO
                ||Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA
                ||Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P
                ||Session::get("usuario")->tipo === TipoUsuario::ESTADISTICAS
                ||Session::get("usuario")->tipo === TipoUsuario::CENSO
                ||Session::get("usuario")->tipo === TipoUsuario::MASTER
                ||Session::get('usuario')->tipo == TipoUsuario::MASTERSS
                ||Session::get("usuario")->tipo === TipoUsuario::ADMINCOMERCIAL
                ||Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA
                ||Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO
                ||Session::get("usuario")->tipo === TipoUsuario::CDT
                ||Session::get("usuario")->tipo === TipoUsuario::MEDICO)

                    <div class="panel panel-default">
                        <div class="panel-heading heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#gestion">
                                    Gestión de Camas
                                </a>
                            </h4>
                        </div>
                        <div id="gestion" class="panel-collapse collapse">

                        </div>
                    </div>
                @endif
                @if (Session::get("usuario")->tipo == TipoUsuario::USUARIO 
                    || Session::get("usuario")->tipo == TipoUsuario::ADMIN  
                    || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA 
                    || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P 
                    || Session::get("usuario")->tipo === TipoUsuario::DIRECTOR 
                    || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO 
                    || Session::get("usuario")->tipo === TipoUsuario::MASTER
                    || Session::get("usuario")->tipo === TipoUsuario::MASTERSS
                    || Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA 
                    || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO 
					|| Session::get("usuario")->tipo === TipoUsuario::VISUALIZADOR
                    || Session::get("usuario")->tipo === TipoUsuario::CDT)
                    <div class="panel panel-default">
                        <div class="panel-heading heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#urgenciaMenu">
                                    Estado pacientes
                                </a>
                                <span id="notification_count"></span>
                            </h4>

                        </div>
                        <div id="urgenciaMenu" class="panel-collapse collapse">
                            <div class="ul">
                                @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN 
                                || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA 
                                || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P 
                                || Session::get("usuario")->tipo == TipoUsuario::USUARIO 
                                || Session::get("usuario")->tipo == TipoUsuario::DIRECTOR 
                                || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO 
                                || Session::get("usuario")->tipo === TipoUsuario::MASTER 
                                || Session::get("usuario")->tipo === TipoUsuario::MASTERSS
                                || Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA 
                                || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO 
                                || Session::get("usuario")->tipo === TipoUsuario::VISUALIZADOR
                                ||Session::get("usuario")->tipo === TipoUsuario::CDT)
                                    <li class="dropdown-submenu">
                                        {{ HTML::link(URL::route('listaEspera'), 'Espera de cama')}}
                                    </li>
                                    <li class="dropdown-submenu">
                                        {{ HTML::link(URL::route('listaTransito'), 'Espera de hospitalización')}}
                                    </li>
									@if(Session::get("usuario")->tipo !== TipoUsuario::VISUALIZADOR)
                                        @if(Session::get("usuario")->tipo != TipoUsuario::CDT)
                                            <li class="dropdown-submenu">
                                                {{ HTML::link(URL::route('listaSalidaUrgencia'), 'En tránsito a piso')}}
                                            </li>
                                        @endif
                                        <li class="dropdown-submenu">
                                            {{ HTML::link(URL::route('listaPabellon'), 'Lista de pabellón')}}
                                        </li>
                                        @if(Session::get("usuario")->tipo != TipoUsuario::CDT)
                                            <li class="dropdown-submenu">
                                                {{ HTML::link(URL::route('listaPreAlta'), 'Lista de Pre-alta')}}
                                            </li>
                                        @endif
                                        <li class="dropdown-submenu">
                                            {{ HTML::link(URL::route('listaEstudio'),'Exámenes / Estudios / Procedimientos')}}
                                        </li>
                                        @if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'enfermeraP'|| Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
                                            <li class="dropdown-submenu">
                                                {{ HTML::link(URL::route('listaCategorizados'), 'Espera de categorización')}}
                                            </li>
                                        @endif
									@endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS || Session::get("usuario")->tipo === TipoUsuario::ENCARGADO_HOSP_DOM || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::ENCARGADO_HOSP_DOM)
                    <div class="panel panel-default">
                        <div class="panel-heading heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#HospitalizacionMenu">
                                    Hospitalización Domiciliaria
                                </a>
                            </h4>
                        </div>
                        <div id="HospitalizacionMenu" class="panel-collapse collapse">
                            <div class="ul">
                                @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS  || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::ENCARGADO_HOSP_DOM)
                                <li class="dropdown-submenu">
                                    {{ HTML::link('IngresarDomiciliaria', 'Ingresar paciente')}}
                                </li>
                                    @if(Session::get("usuario")->tipo != TipoUsuario::GESTION_CLINICA && Session::get("usuario")->tipo != TipoUsuario::ENFERMERA_P)
                                        <li class="dropdown-submenu">
                                            {{ HTML::link('hospitalizacion/listaPacientes', 'Lista de pacientes')}}
                                            {{-- - {{ HTML::link('hospitalizacion/listaCategorizacion', 'Categorización de pacientes')}} <br> --}}
                                        </li>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(Session::get('usuario')->tipo == 'admin' || Session::get("usuario")->tipo === TipoUsuario::ESTADISTICAS || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
                    <div class="panel panel-default">
                        <div class="panel-heading heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#DerivacionMenu">
                                    Derivación
                                </a>
                            </h4>
                        </div>
                        <div id="DerivacionMenu" class="panel-collapse collapse">
                            <div class="ul">
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('listaDerivados'), 'Lista de derivados')}}
                                </li>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

            @if(Session::get("usuario")->tipo == TipoUsuario::USUARIO 
            || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA  
            || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P 
            || Session::get("usuario")->tipo === TipoUsuario::MASTER 
            || Session::get("usuario")->tipo === TipoUsuario::MASTERSS 
            || Session::get("usuario")->tipo === TipoUsuario::ADMIN 
            || Session::get("usuario")->tipo === TipoUsuario::MATRONA_NEONATOLOGIA 
            || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO
            || Session::get("usuario")->tipo === TipoUsuario::CDT)

                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            {{ HTML::link('buscarCamaInteligente', 'Solicitar Cama')}}
                        </h4>
                    </div>
                </div>

            @endif

            @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS
            // || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO
            )
                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#solicitudTrasladoInterno">
                                Solicitudes de Traslado Interno
                            </a>
                            <span id="notification_resultado" ></span>
                        </h4>
                    </div>
                    <div id="solicitudTrasladoInterno" class="panel-collapse collapse">
                        @if(Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::ADMIN
                        // || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO
                        )
                            <div class="ul">
                                <li class="dropdown-submenu">                                    
                                    {{HTML::link('trasladoInterno/enviadas', 'Enviadas')}} <span  id="notification_enviadasencurso"></span> <br>
                                </li>
                            </div>
                        @endif
                        @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER  || Session::get("usuario")->tipo === TipoUsuario::MASTERSS
                        // || Session::get("usuario")->tipo == TipoUsuario::SUPERVISORA_DE_SERVICIO
                        )
                            <div class="ul">
                                <li class="dropdown-submenu">
                                    {{HTML::link('trasladoInterno/recibidas', 'Recibidas')}} <span id="notification_recibidasencurso"></span>
                                </li>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO)

            {{-- <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#solicitudMenu">
                            Solicitudes de Traslado Externo
                        </a>
                    </h4>
                </div>
                <div id="solicitudMenu" class="panel-collapse collapse">
                    <div class="panel-body">

                        if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS)
                            @if(Session::get("usuario")->tipo != TipoUsuario::DIRECTOR && Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
                                -  HTML::link('trasladar', 'Realizar traslado externo') <br>
                            @endif
                            -  HTML::link('derivaciones/enviadas', 'Enviadas') <br>
                            -  HTML::link('derivaciones/recibidas', 'Recibidas')
                                if(Session::get('complejidad') != 'baja')
                                    <br>
                                    -  HTML::link('trasladar/trasladosExtraSistema', 'Traslados extra sistemas')
                                endif
                        else
                            -  HTML::link("derivaciones/enviadas", "Ver solicitudes")  <br>
                            -  HTML::link("trasladar/trasladosExtraSistema", "Ver traslados extra sistemas")
                        endif

                    </div>
                </div>
            </div> --}}

            @endif

            @if(Session::get("usuario")->tipo === TipoUsuario::MASTER)
                {{-- <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#gestionAmbulancia">
                                Gestion de Ambulancias
                            </a>
                        </h4>
                    </div>
                    <div id="gestionAmbulancia" class="panel-collapse collapse">
                        <div class="panel-body">
                            @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN )
                                - {{ HTML::link(URL::route('ambulancias.index'), 'Lista de ambulancias')}} <br>
                                - {{ HTML::link('ambulancia/indexRutas', 'Incorporar Rutas')}} <br>
                            @endif
                        </div>
                    </div>
                </div> --}}
            @endif

            @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN ||
                //Session::get("usuario")->tipo == TipoUsuario::ADMINSS ||
                Session::get("usuario")->tipo == TipoUsuario::DIRECTOR ||
                Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO||
                Session::get("usuario")->tipo === TipoUsuario::ESTADISTICAS ||
                Session::get("usuario")->tipo === TipoUsuario::CENSO ||
                Session::get("usuario")->tipo === TipoUsuario::MASTER ||
                Session::get("usuario")->tipo === TipoUsuario::MASTERSS ||
                Session::get("usuario")->tipo == TipoUsuario::USUARIO ||
                Session::get("usuario")->tipo == TipoUsuario::GRD|| 
                Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO ||
                Session::get("usuario")->tipo === TipoUsuario::CDT
            )
                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#estadistica">
                                Reportes
                            </a>
                        </h4>
                    </div>
                    <div id="estadistica" class="panel-collapse collapse">
                        <div class="ul">
                            @if(Session::get("usuario")->tipo != TipoUsuario::GRD)
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('reporteMensualEstadistico'), 'Reporte Estadístico Mensual')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('reportePacienteEspera'), 'Reporte pacientes en espera')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('reporteRiesgoCategorizacion'), 'Reporte riesgo y categorización')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('estDirector'), 'Reporte resumen')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('estEstada'), 'Reporte de estada')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{HTML::link(URL::route('estCamaBloqueada'), 'Reporte de camas bloqueadas')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('estCamas'), 'Reporte de camas')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('estCasoSocial'), 'Reporte caso social')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('estAlta'), 'Reporte de egresos')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('reporteOtrasRegiones'), 'Reporte pacientes otras regiones')}}
                                </li>
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('informeDerivacion'), 'Reporte de derivación')}}
                                </li>
                                @if(Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::MASTERSS || Session::get("usuario")->tipo == TipoUsuario::ADMIN)
                                <li class="dropdown-submenu">
                                    {{ HTML::link(URL::route('reporteUrgencias2'), 'Reporte de urgencias')}}
                                </li>
                                @endif
                            @endif
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('reporteEspecialidades'), 'Reporte de Especialidades')}}    
                            </li>                           
                        </div>
                    </div>
                </div>
            @endif

    {{--        @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN ||
                Session::get("usuario")->tipo == TipoUsuario::ADMINSS ||
                Session::get("usuario")->tipo == TipoUsuario::DIRECTOR ||
                Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO
            )
                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#tomadecisiones">
                                Apoyo toma de decisiones
                            </a>
                        </h4>
                    </div>
                    <div id="tomadecisiones" class="panel-collapse collapse">
                        <div class="panel-body">
                        @if(Session::get("usuario")->tipo != TipoUsuario::ADMINSS)
                        - {{ HTML::link(URL::route('censoDiario'), 'Censo diario')}}<br>
                        @endif

                            - {{ HTML::link(URL::route('estKnox'), 'Método Knox')}}<br>
                            - {{ HTML::link(URL::route('estDistEspacial'), 'Distribución Espacial')}}<br>
                            - {{ HTML::link(URL::route('estKmeans'), 'Kmeans')}}<br>
                            - {{ HTML::link(URL::route('estSir'), 'SIR')}}<br>

                            - {{ HTML::link(URL::route('regresion'), 'Regresión Lineal')}}<br>
                            - {{ HTML::link(URL::route('randomForest'), 'Random Forest')}}<br>
                        </div>
                    </div>
                </div>
            @endif

    --}}

        {{-- @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN ||
                Session::get("usuario")->tipo == TipoUsuario::ADMINSS ||
                Session::get("usuario")->tipo == TipoUsuario::DIRECTOR ||
                Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO ||
                Session::get("usuario")->tipo === TipoUsuario::ESTADISTICAS ||
                Session::get("usuario")->tipo === TipoUsuario::CENSO ||
                Session::get("usuario")->tipo === TipoUsuario::MASTER)
                <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#evolucionMenu">
                                Categorización
                            </a>
                        </h4>
                    </div>
                    <div id="evolucionMenu" class="panel-collapse collapse">
                        <div class="panel-body">
                            - {{ HTML::link(URL::route('exportarEvolucion'), 'Exportar categorización')}}<br>
                        </div>
                    </div>
                </div>
            @endif
            --}}
            @if(Session::get("usuario")->tipo != TipoUsuario::GRD)
            <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#buscadorMenu">
                            Buscar
                        </a>
                    </h4>
                </div>
                <div id="buscadorMenu" class="panel-collapse collapse">
                    <div class="ul">
                        <li class="dropdown-submenu padding-submenu">
                            {{ HTML::link(URL::route('buscarPaciente'), 'Paciente(s)')}}
                        </li>
                        @if (Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA
                        || Session::get("usuario")->tipo === TipoUsuario::MASTER
                        || Session::get("usuario")->tipo === TipoUsuario::MASTERSS
                        || Session::get("usuario")->tipo === TipoUsuario::ADMIN
                        || Session::get('usuario')->tipo == 'matrona_neonatologia'
                        || Session::get("usuario")->tipo === TipoUsuario::SUPERVISORA_DE_SERVICIO)
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('gestionBuscar'), 'Cama(s)')}} <br>
                            </li>
                        @endif
                    </div>
                </div>
            </div>
            @endif
			
			@if(Session::get("usuario")->tipo === TipoUsuario::MASTER
			|| Session::get("usuario")->tipo === TipoUsuario::MASTERSS
			|| Session::get("usuario")->tipo === TipoUsuario::VISUALIZADOR
			|| Session::get("usuario")->tipo === TipoUsuario::OIRS)
			<div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#visitasMenu">
                            Gestión visitas
                        </a>
                    </h4>
                </div>
                <div id="visitasMenu" class="panel-collapse collapse">
                    <div class="ul">
                        <li class="dropdown-submenu padding-submenu">
                            {{ HTML::link(URL::to('/registro-visitas'), 'Registro de visitas')}}
                        </li>
                    </div>
                </div>
            </div>
			@endif
            @if( Session::get("usuario")->establecimiento== 1 || Session::get("usuario")->establecimiento == 18 || Session::get("usuario")->establecimiento == 16 || Session::get("usuario")->establecimiento == 13 || Session::get("usuario")->establecimiento == 15 || Session::get("usuario")->establecimiento == 14 || Session::get("usuario")->establecimiento == 17 || Session::get("usuario")->tipo === TipoUsuario::MASTER)
            <!--<div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#traumatologiaHoras">
                            Horas traumatología
                        </a>
                    </h4>
                </div>
                <div id="traumatologiaHoras" class="panel-collapse collapse">
                    <div class="panel-body">
                        @if( Session::get("usuario")->establecimiento!= 1)
                        - {{ HTML::link(URL::route('pedirHora'), 'Pedir hora')}}<br>
                        @endif
                        - {{ HTML::link(URL::route('revisarHora'), 'Revisar horas')}}<br>
                        @if( Session::get("usuario")->establecimiento== 1)
                        - {{ HTML::link(URL::route('medicos'), 'Médicos')}}<br>
                        @endif
                    </div>
                </div>
            </div>-->
            @endif

            @if (Session::get("usuario")->tipo === TipoUsuario::MASTER)
                {{-- <div class="panel panel-default">
                    <div class="panel-heading heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#contingenciaMenu">
                                Contingencia
                            </a>
                        </h4>
                    </div>
                    <div id="contingenciaMenu" class="panel-collapse collapse">
                        <div class="ul">
                            @if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS && Session::get("usuario")->tipo != TipoUsuario::MONITOREO_SSVQ)
                            <li class="dropdown-submenu padding-submenu">
                                @if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
                                {{ HTML::link(URL::route('declararContingencia'), 'Declarar contingencia')}}
                                @endif
                            </li>
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('contingencias'), 'Ver creadas')}}
                            </li>
                            @else
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('contingencias'), 'Ver todas')}}
                            </li>
                            @endif
                        </div>
                    </div>
                </div> --}}
            @endif


            @if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS && Session::get("usuario")->tipo != TipoUsuario::MONITOREO_SSVQ || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    <!--<div class="panel panel-default">
                        <div class="panel-heading heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#CosaAzul">
                                    Alertas
                                </a>
                            </h4>
                        </div>
                        <div id="CosaAzul" class="panel-collapse collapse">
                            <div class="panel-body">
                            - {{ HTML::link(URL::route('Alerta'), 'Ver Alertas')}}
                        </div>
                    </div>
                </div>-->
                @endif

                @if(Session::get("usuario")->tipo == "xx")
            <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#documentoMenu">
                            Documentos
                        </a>
                    </h4>
                </div>
                <div id="documentoMenu" class="panel-collapse collapse">
                    <div class="panel-body">
                    - {{ HTML::link(URL::route('Documentos'), 'documentos')}}
                </div>
            </div>
        </div>
        @endif
			@if(Session::get("usuario")->tipo != TipoUsuario::OIRS)
            <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#administracionMenu">
                            Administración
                        </a>
                    </h4>
                </div>
                <div id="administracionMenu" class="panel-collapse collapse">
                    <div class="ul">
                        @if (Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS)
                            @if(Session::get("usuario")->tipo !== TipoUsuario::MASTERSS)
                                {{--  - {{ HTML::link(URL::route('gestionServicios'), 'Gestión Servicios')}} <br> --}}
                                <li class="dropdown-submenu padding-submenu">
                                    {{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión Establecimientos')}} <br>
                                </li>
                            @endif
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('gestionMedicos'), 'Gestión Médicos')}}<br>
                            </li>
                            @if(Session::get("usuario")->tipo !== TipoUsuario::MASTERSS)
                                {{--  - {{ HTML::link(URL::route('gestionUnidad'), 'Gestión Unidades')}} <br> --}}
                                @if (Session::get("usuario")->tipo === TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                                    <li class="dropdown-submenu">
                                        {{ HTML::link(URL::route('gestionUsuario'), 'Gestión Usuario')}}<br>
                                    </li>
                                @endif
                            @endif
                           

                        @endif
                        @if (Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER  || Session::get("usuario")->tipo === TipoUsuario::MASTERSS)
                            <li class="dropdown-submenu padding-submenu">
                                {{ HTML::link(URL::route('indexNoCategorizados'), 'No categorizados')}} <br>
                            </li>
                        @endif
                        @if (Session::get("usuario")->tipo === TipoUsuario::ADMINIAAS || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('gestionIaas'), 'Gestión IAAS')}} <br>
                            </li>
                        @endif
                        <li class="dropdown-submenu">
                            {{ HTML::link(URL::route('cambiarContraseña'), 'Cambiar contraseña')}} <br>
                        </li>
                        @if (Session::get("usuario")->tipo === TipoUsuario::ADMINCOMERCIAL || Session::get("usuario")->tipo === TipoUsuario::MASTER || Session::get("usuario")->tipo === TipoUsuario::MASTERSS)
                            <li class="dropdown-submenu">
                                {{ HTML::link(URL::route('gestionProductos'), 'Gestión Productos')}}<br>
                            </li>
                        @endif
                </div>
            </div>
        </div>
		@endif
        @if (Session::get("usuario")->tipo != TipoUsuario::MEDICO && Session::get("usuario")->tipo != TipoUsuario::OIRS)
            <div class="panel panel-default">
                <div class="panel-heading heading">
                    <h4 class="panel-title">
                        <a href="{{URL::to('vistaDocumentos')}}">
                            Documentos
                        </a>
                    </h4>
                </div>
            </div>
        @endif
        </div>

    </div>
</div>


<script>
    $(function(){

        var cerrado = false;

        $("#menu_").click(function(){
            if(cerrado == true){
                $("#menu_").removeClass("is-closed");
                $("#menu_").addClass("is-open");
                $("#main_").css('margin-left','240px');
                $(".migax").animate({'margin-left': '+=200px'},500);
                $("#menu_").animate({'margin-left': '+=190px'},500);
                $("#sideNavigation").show("slow");
                cerrado = false;
            }else{
                $("#menu_").removeClass("is-open");
                $("#menu_").addClass("is-closed");
                $("#main_").css('margin-left','100px');
                $(".migax").animate({'margin-left': '-=200px'},500);
                $("#menu_").animate({'margin-left': '-=190px'},500);
                $("#sideNavigation").hide("slow");
                cerrado = true;
            }
        });

        function marcar(seleccionado){
            if(seleccionado != null){
                $("#"+seleccionado).css("background-color", "#D1FEBC");
            }
        }

        var tipo = "{{Session::get('usuario')->tipo}}";
        var iaas = "{{Session::get('usuario')->iaas}}";
        if((tipo == "admin" && iaas == true) || tipo == "iaas" || tipo == "admin" || tipo == "director" || tipo == 'medico_jefe_servicio' || tipo == "gestion_clinica" || tipo == "enfermeraP" || tipo == "supervisora_de_servicio"){
            var estab = "{{Session::get('usuario')->establecimiento}}";
            //alert(estab);
            $.ajax({
                url: "{{ asset('getInfectados') }}",
                data: {estab: estab},
                type: "POST",
                dataType: "json",
                success: function(data){
                    $.each(data, function(index, value){
                        $("#menu"+value).show();
                    });
                },
                error: function(error){
                    console.log(error);

                }
            });
        }

        marcar(sessionStorage.getItem("seleccionado_menu"));

        $("div.margen > a").on("click", function(){
            var seleccionado = $(this).parent().parent().parent().parent();
            var id_seleccionado = seleccionado.attr("id");
            global = id_seleccionado;
            sessionStorage.setItem("seleccionado_menu", global);
        });

    });

</script>
{{-- @section("estilo-tablas-verdes")
{{ HTML::style('css/menunuevo.css') }}
@stop --}}

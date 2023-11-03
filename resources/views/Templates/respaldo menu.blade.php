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

    <div class="col-lg-2 col-md-3 col-xs-3 pad-der-10 menuDerecha sidenav" id="sideNavigation" style="display: none;">

        <div class="panel-group sidebar-offcanvas sidebar-offcanvas" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-parent="#accordion" href="{{URL::to('index')}}">
                            Inicio
                        </a>
                    </h4>
                </div>
            </div>

            
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
                ||Session::get("usuario")->tipo === TipoUsuario::ADMINCOMERCIAL

                )

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#gestion">
                                    Gestión de Camas
                                </a>
                            </h4>
                        </div>
                        <div id="gestion" class="panel-collapse collapse">
                            <div class="panel-body">
                                @foreach(Session::get('area') as $are)
                                    <li class="dropdown-submenu padding-submenu" id="{{$are->id_area_funcional}}">
                                        {{$are->nombre}}
                                        <ul class="dropdown-menu solo-menu">
                                                @foreach (Session::get('unidades') as $unidad)
                                                    @if($are->id_area_funcional == $unidad->id_area_funcional && Consultas::restriccionPersonal($unidad->id) != true)
                                                        @if(Session::get("some"))
                                                            @if($unidad->some === null)
                                                                <li><div class="margen">
                                                                    @if($are->nombre == "Neonatología" && $unidad->nombre == "Cuidados basicos")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Neonatología</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                </div></li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "Área Médico quirúrgico pediátrica" && $unidad->nombre == "Cuidados basicos")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Pediatría</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                </div></li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "Área Médico quirúrgico pediátrica" && $unidad->nombre == "Cuidados medios")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Pediatría</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                </div></li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "UPC Adultos" && $unidad->nombre == "UPC Intensivo")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Adultos</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                </div></li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "UPC Adultos")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Adultos</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                {{-- </div> --}}
                                                            </li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "UPC Neonatología" && $unidad->nombre == "UPC Intensivo")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Neonatología</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                {{-- </div> --}}
                                                            </li>
                                                                <li class="divider"></li>
                                                                @elseif($are->nombre == "UPC Neonatología" && $unidad->nombre == "UPC Intermedio")
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }} <b style="color:#04B404">Neonatología</b>
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                {{-- </div> --}}
                                                            </li>
                                                                <li class="divider"></li>
                                                                @else
                                                                {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias], ["style" => "color:#04B404"] ) }}
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                {{-- </div> --}}
                                                            </li>
                                                                <li class="divider"></li>
                                                                @endif
                                                            @else
                                                                <li><div class="margen">
                                                                    {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias] ) }}
                                                                    @if(Session::get('usuario')->iaas && Session::get('usuario')->tipo == "admin")
                                                                    <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                                    @endif
                                                                </div></li>
                                                                <li class="divider"></li>
                                                            @endif
                                                        @else
                                                            <li><div class="margen">
                                                                {{ HTML::linkRoute('unidad', $unidad->nombre, [$unidad->alias] ) }}
                                                                <img src="{{asset('img/iaas.png')}}" style="width:13px; display:none;" id="menu{{$unidad['id']}}">
                                                            </div></li>
                                                            <li class="divider"></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::ADMIN  || 
                    Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo === TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::MASTER
                )
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#urgenciaMenu">
                                    Estado pacientes
                                </a>
                                <span id="notification_count"></span>
                            </h4>

                        </div>
                        <div id="urgenciaMenu" class="panel-collapse collapse">
                            <div class="panel-body">
                                {{-- if (Session::get("usuario")->tipo == TipoUsuario::USUARIO)
                                    -  HTML::link(URL::route('ingresarPaciente'), 'Solicitar Cama ')<br>
                                endif --}}
                                @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                                    - {{ HTML::link(URL::route('listaEspera'), 'Espera de cama')}}<br>
                                    - {{ HTML::link(URL::route('listaTransito'), 'Espera de hospitalización')}}<br>
                                    - {{ HTML::link(URL::route('listaSalidaUrgencia'), 'En tránsito a piso')}}<br>
                                    - {{ HTML::link(URL::route('listaPabellon'), 'Lista de pabellón')}}{{--<span  id="notification_recuperacion"></span>--}}<br>
                                    - {{ HTML::link(URL::route('listaEstudio'),'Exámenes / Estudios / Procedimientos')}} <br>

                                    @if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'enfermeraP'|| Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
                                    - {{ HTML::link(URL::route('listaCategorizados'), 'Espera de categorización')}}<br>
                                    @endif

                                
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#HospitalizacionMenu">
                                    Hospitalización Domiciliaria
                                </a>
                            </h4>
                        </div>
                        <div id="HospitalizacionMenu" class="panel-collapse collapse">
                            <div class="panel-body">
                                @if (Session::get("usuario")->tipo == TipoUsuario::ADMIN || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                                    - {{ HTML::link('hospitalizacion/listaPacientes', 'Lista de pacientes')}} <br>
                                    {{-- - {{ HTML::link('hospitalizacion/listaCategorizacion', 'Categorización de pacientes')}} <br> --}}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#DerivacionMenu">
                                    Derivación
                                </a>
                            </h4>
                        </div>
                        <div id="DerivacionMenu" class="panel-collapse collapse">
                            <div class="panel-body">
                                - {{ HTML::link(URL::route('listaDerivados'), 'Lista de derivados')}}<br>
                            </div>
                        </div>
                    </div>
                @endif

            @endif
            @if(Session::get("usuario")->tipo == TipoUsuario::USUARIO || Session::get("usuario")->tipo == TipoUsuario::GESTION_CLINICA  || Session::get("usuario")->tipo == TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#ingresarPaciente">
                                    {{ HTML::link('buscarCamaInteligente', 'Solicitar Cama')}} <br>
                                </a>
                            </h4>
                        </div>
    <!--                    <div id="ingresarPaciente" class="panel-collapse collapse">
                            <div class="panel-body">


                            </div>
                        </div>-->
                    </div>

            @endif
            @if(Session::get("usuario")->tipo === TipoUsuario::ADMIN || Session::get("usuario")->tipo === TipoUsuario::GESTION_CLINICA || Session::get("usuario")->tipo === TipoUsuario::ENFERMERA_P || Session::get("usuario")->tipo === TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::DIRECTOR || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO)

            {{-- <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#solicitudMenu">
                            Solicitudes de Traslado Externo
                        </a>
                    </h4>
                </div>
                <div id="solicitudMenu" class="panel-collapse collapse">
                    <div class="panel-body">

                        if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS)
                            @if(Session::get("usuario")->tipo != TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
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

            @if(Session::get("usuario")->tipo === 4444 || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                <div class="panel panel-default">
                    <div class="panel-heading">
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
                </div>
            @endif

            @if(Session::get("usuario")->tipo == TipoUsuario::ADMIN ||
                //Session::get("usuario")->tipo == TipoUsuario::ADMINSS ||
                Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || 
                Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO ||
                Session::get("usuario")->tipo === TipoUsuario::ESTADISTICAS ||
                Session::get("usuario")->tipo === TipoUsuario::CENSO || 
                Session::get("usuario")->tipo === TipoUsuario::MASTER ||
                Session::get("usuario")->tipo == TipoUsuario::USUARIO
            )
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#estadistica">
                                Reportes
                            </a>
                        </h4>
                    </div>
                    <div id="estadistica" class="panel-collapse collapse">
                        <div class="panel-body">
                            -  {{HTML::link(URL::route('reporteMensualEstadistico'), 'Reporte Estadístico Mensual')}}<br> 
                            -  {{HTML::link(URL::route('reportePacienteEspera'), 'Reporte pacientes en espera')}}<br>
                            -  {{HTML::link(URL::route('reporteRiesgoCategorizacion'), 'Reporte riesgo y categorización')}}<br>
                            -  {{HTML::link(URL::route('estDirector'), 'Reporte resumen')}}<br>
                            -  {{HTML::link(URL::route('estEstada'), 'Reporte de estada')}}<br>
                            -  {{HTML::link(URL::route('estCamaBloqueada'), 'Reporte de camas bloqueadas')}}<br>


                            - {{ HTML::link(URL::route('estCamas'), 'Reporte de camas')}}<br>
                            {{-- - {{ HTML::link(URL::route('estDerivaciones'), 'Reporte de derivaciones')}}<br>  --}}
                        {{--   - {{ HTML::link(URL::route('estHabilitadas'), 'Camas habilitadas')}}<br>
                            - {{ HTML::link(URL::route('estDeshabilitadas'), 'Camas deshabilitadas')}}<br> --}}
                            
                            - {{ HTML::link(URL::route('estCasoSocial'), 'Reporte caso social')}}<br> 
                            
                            {{-- - {{ HTML::link(URL::route('estIAAS'), 'Reporte IAAS')}}<br> --}}
                            {{-- -  HTML::link(URL::route('expIAAS'), 'Exportar IAAS') --}}
                            

                            - {{ HTML::link(URL::route('estAlta'), 'Reporte de egresos')}}<br>
                            {{-- - {{ HTML::link(URL::route('estRiesgo'), 'Reporte de riesgo')}}<br>  --}}

                            {{-- - {{ HTML::link(URL::route('estDiagnostico'), 'Reporte de diagnósticos')}}<br> 
                            - {{ HTML::link(URL::route('estEstadiaCamasPaciente'), 'Promedio de camas disponibles y estada')}}<br> --}}
                            {{-- -  HTML::link(URL::route('estContingencia'), 'Reporte de contingencia')<br> --}}


                            {{-- - {{ HTML::link(URL::route('estOcupacional'), 'Índice ocupacional')}}<br> 
                            - {{ HTML::link(URL::route('estIntevalo'), 'Intervalo rotación y sustitución')}}<br> --}}

                        {{--  - {{ HTML::link(URL::route('reporteListaEspera'), 'Reporte lista espera de camas hospitalarias')}}<br> --}}
                            {{-- - {{ HTML::link(URL::route('reporteListaTransito'), 'Reporte lista tránsito')}}<br> --}}
                            - {{ HTML::link(URL::route('reporteOtrasRegiones'), 'Reporte pacientes otras regiones')}}<br>
                            
                            - {{ HTML::link(URL::route('informeDerivacion'), 'Informe de derivación')}}<br>

                            {{-- - {{ HTML::link(URL::route('informePromedioSolicitudAsignacion'), 'Informe tiempo promedio solicitud/asignación')}}<br> --}}

                            {{-- - {{ HTML::link(URL::route('informeMensualCateg'), 'Informe mensual categorización')}}<br> --}}

                            {{-- - {{ HTML::link(URL::route('reporteUrgencias'), 'Reporte de urgencias')}}<br> --}}

                            @if(Session::get("usuario")->tipo == TipoUsuario::DIRECTOR || Session::get("usuario")->tipo == TipoUsuario::MEDICO_JEFE_DE_SERVICIO || Session::get("usuario")->tipo == TipoUsuario::MASTER || Session::get("usuario")->tipo == TipoUsuario::ADMIN)
                            - {{ HTML::link(URL::route('reporteUrgencias2'), 'Reporte de urgencias')}}<br>
                        {{--  - {{ HTML::link(URL::route('reporteDotacionEnfermeria'), 'Reporte Dotación de Enfermeria')}}<br> --}}
                            @endif
                            

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
                    <div class="panel-heading">
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
                    <div class="panel-heading">
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
        
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a  href="{{URL::route('buscarPaciente')}}">
                            Buscar
                        </a>
                    </h4>
                </div>
                <div id="buscarMenu" class="panel-collapse collapse">
                    <div class="panel-body">
                        - {{ HTML::link(URL::route('buscarPaciente'), 'Pacientes')}}<br>
                        {{-- -  HTML::link(URL::route('buscarServicio'), 'Servicios')<br> --}}
                    {{-- - {{ HTML::link('busquedaIAAS/buscarPacienteIAAS', 'Pacientes IAAS')}} <br> --}}
                    </div>
                </div>
            </div>

            <!--
            Limache = 18
            Calera = 16
            Ligua = 13
            Petorca = 15
            Cabildo = 14
            -->

            @if( Session::get("usuario")->establecimiento== 1 || Session::get("usuario")->establecimiento == 18 || Session::get("usuario")->establecimiento == 16 || Session::get("usuario")->establecimiento == 13 || Session::get("usuario")->establecimiento == 15 || Session::get("usuario")->establecimiento == 14 || Session::get("usuario")->establecimiento == 17 || Session::get("usuario")->tipo === TipoUsuario::MASTER)
            <!--<div class="panel panel-default">
                <div class="panel-heading">
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

            @if ((1 == 2) || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#contingenciaMenu">
                                Contingencia
                            </a>
                        </h4>
                    </div>
                    <div id="contingenciaMenu" class="panel-collapse collapse">
                        <div class="panel-body">
                            @if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS && Session::get("usuario")->tipo != TipoUsuario::MONITOREO_SSVQ)
                                @if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
                                - {{ HTML::link(URL::route('declararContingencia'), 'Declarar contingencia')}}<br>
                                @endif
                                - {{ HTML::link(URL::route('contingencias'), 'Ver creadas')}}<br>
                            @else
                                - {{ HTML::link(URL::route('contingencias'), 'Ver todas')}}<br>
                            @endif
                        </div>
                    </div>
                </div>
            @endif


            @if (Session::get("usuario")->tipo != TipoUsuario::ADMINSS && Session::get("usuario")->tipo != TipoUsuario::MONITOREO_SSVQ || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    <!--<div class="panel panel-default">
                        <div class="panel-heading">
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
                <div class="panel-heading">
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

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#administracionMenu">
                            Administración
                        </a>
                    </h4>
                </div>
                <div id="administracionMenu" class="panel-collapse collapse">
                    <div class="panel-body">
                    @if (Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    {{--  - {{ HTML::link(URL::route('gestionServicios'), 'Gestión Servicios')}} <br> --}}
                    - {{ HTML::link(URL::route('gestionEstablecimientos'), 'Gestión Establecimientos')}} <br>
                    - {{ HTML::link(URL::route('gestionMedicos'), 'Gestión Médicos')}}<br>
                    {{--  - {{ HTML::link(URL::route('gestionUnidad'), 'Gestión Unidades')}} <br> --}}
                    @if (Session::get("usuario")->tipo === TipoUsuario::ADMINSS || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    - {{ HTML::link(URL::route('gestionUsuario'), 'Gestión Usuario')}}<br>
                    @endif

                    @endif
                    @if (Session::get("usuario")->tipo === TipoUsuario::ADMINIAAS || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                        - {{ HTML::link(URL::route('gestionIaas'), 'Gestión IAAS')}} <br>
                    @endif
                    - {{ HTML::link(URL::route('gestionBuscar'), 'Buscar Cama')}} <br>
                    - {{ HTML::link(URL::route('cambiarContraseña'), 'Cambiar contraseña')}} <br>
                    @if (Session::get("usuario")->tipo === TipoUsuario::ADMINCOMERCIAL || Session::get("usuario")->tipo === TipoUsuario::MASTER)
                    
                    - {{ HTML::link(URL::route('gestionProductos'), 'Gestión Medicamentos, Insumos y Sueros')}}<br>
                    @endif

                </div>
            </div>
        </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="{{URL::to('vistaDocumentos')}}">
                            Documentos
                        </a>
                    </h4>
                </div>
            </div>

        </div>

    </div>
</div>
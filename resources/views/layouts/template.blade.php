<!DOCTYPE html>
<html>
<!-- admin bsb Material Design -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Localhost</title>



        <script src="<?= asset('app/lib/angular/angular.min.js') ?>"></script>
        <script src="<?= asset('js/jquery.min.js') ?>"></script>
        <script src="<?= asset('js/bootstrap.min.js') ?>"></script>
        <script src="<?= asset('js/sweetalert.min.js') ?>"></script>
        <script src="<?= asset('js/SweetAlert.min.js') ?>"></script>
        <script src="<?= asset('js/angucomplete-alt.min.js') ?>"></script>
    <script src="<?= asset('js/tooltips-popovers.js') ?>"></script>
        <script src="<?= asset('js/nsPopover.js') ?>"></script>
        <!-- AngularJS Application Scripts -->
        <script src="<?= asset('app/app.js') ?>"></script>
        <script src="<?= asset('app/controllers/employees.js') ?>"></script>
        <script src="<?= asset('app/controllers/editarTragosController.js') ?>"></script>
        <script src="<?= asset('app/controllers/ingresarProductosController.js') ?>"></script>
        <script src="<?= asset('app/controllers/nuevoProductoController.js') ?>"></script>
        <script src="<?= asset('app/controllers/ventasPisoUnoController.js') ?>"></script>
        <script src="<?= asset('app/controllers/editarInventarioController.js') ?>"></script>
        <script src="<?= asset('app/controllers/editarMesasController.js') ?>"></script>
        <script src="<?= asset('app/controllers/verificarInventarioController.js') ?>"></script>
        <script src="<?= asset('app/controllers/ventasDiariasController.js') ?>"></script>
        <script src="<?= asset('app/controllers/iniciarCajaController.js') ?>"></script>
        <script src="<?= asset('app/controllers/cerrarCajaController.js') ?>"></script>
        <script src="<?= asset('app/controllers/historialCajaController.js') ?>"></script>
        
        
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    {{-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css"> --}}

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Preloader Css -->
    <link href="plugins/material-design-preloader/md-preloader.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />

    <link href="css/sweetalert.css" rel="stylesheet">
    <link href="css/nsPopover.css" rel="stylesheet">
</head>

<body class="theme-red" ng-app="bar">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="md-preloader pl-size-md">
                <svg viewbox="0 0 75 75">
                    <circle cx="37.5" cy="37.5" r="33.5" class="pl-red" stroke-width="4" />
                </svg>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->

    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="index.html">NOMBRE BAR</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                
                    <!-- Cerrar Sesión -->
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="material-icons">flag</i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">Cerrar Sesión</li>   
                        </ul>
                    </li>
                    <!-- #END# Tasks -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">more_vert</i></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info" style="background: url(<?= asset('img/user-img-background.jpg') ?>) no-repeat no-repeat">
                <div class="image">
                    <img src="<?= asset('img/user.png') ?>" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Nombre Bar</div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">Menú</li>
                    <li>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons col-brown">widgets</i>
                            <span>Ventas</span>
                        </a>
                        <ul class="ml-menu">
                            <li>
                                <a href="{{ URL::to('mesasPisoUno') }}">Mesas piso 1</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('mesasPisoDos') }}">Mesas piso 2</a>
                            </li>
                        </ul>
                    </li>

                    <li class="header">Tragos</li>
                    <li>
                        <a href="{{ URL::to('editarTragos') }}">
                            <i class="material-icons col-orange">mode_edit</i>
                            <span>Editar tragos</span>
                        </a>
                    </li>

                    <li class="header">Inventario</li>
                    <li>
                        <a href="{{ URL::to('ingresarProductos') }}">
                            <i class="material-icons col-light-blue">add</i>
                            <span>Stock</span>
                        </a>

                    </li>

                    <li class="header">Informe</li>
                    <li>
                        <a href="{{ URL::to('verificarInventario') }}">
                            <i class="material-icons col-amber">check</i>
                            <span>Verificar Inventario</span>
                        </a>
                        <a href="{{ URL::to('ventasDiarias') }}">
                            <i class="material-icons col-red">update</i>
                            <span>Ventas diarias</span>
                        </a>
                        <a href="{{ URL::to('historialCaja') }}">
                            <i class="material-icons col-red">update</i>
                            <span>Historial caja</span>
                        </a>
                    </li>

                    <li class="header">Caja</li>
                    <li>
                        <a href="{{ URL::to('iniciarCaja') }}">
                            <i class="material-icons col-amber">check</i>
                            <span>Iniciar caja</span>
                        </a>
                        <a href="{{ URL::to('cerrarCaja') }}">
                            <i class="material-icons col-red">update</i>
                            <span>cerrar caja</span>
                        </a>
                    </li>

                    <li class="header">configuración</li>
                    <li>
                        <a href="{{ URL::to('editarInventario') }}">
                            <i class="material-icons col-light-blue">add</i>
                            <span>Productos</span>
                        </a>
                        <a href="{{ URL::to('editarMesas') }}">
                            <i class="material-icons col-light-blue">add</i>
                            <span>Mesas</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2016 <a href="javascript:void(0);">Nombre Bar</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.3
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        <aside id="rightsidebar" class="right-sidebar">
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                <li role="presentation" class="active"><a href="#skins" data-toggle="tab">SKINS</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                    <ul class="demo-choose-skin">
                        <li data-theme="red" class="active">
                            <div class="red"></div>
                            <span>Red</span>
                        </li>
                        <li data-theme="pink">
                            <div class="pink"></div>
                            <span>Pink</span>
                        </li>
                        <li data-theme="purple">
                            <div class="purple"></div>
                            <span>Purple</span>
                        </li>
                        <li data-theme="deep-purple">
                            <div class="deep-purple"></div>
                            <span>Deep Purple</span>
                        </li>
                        <li data-theme="indigo">
                            <div class="indigo"></div>
                            <span>Indigo</span>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                            <span>Blue</span>
                        </li>
                        <li data-theme="light-blue">
                            <div class="light-blue"></div>
                            <span>Light Blue</span>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                            <span>Cyan</span>
                        </li>
                        <li data-theme="teal">
                            <div class="teal"></div>
                            <span>Teal</span>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                            <span>Green</span>
                        </li>
                        <li data-theme="light-green">
                            <div class="light-green"></div>
                            <span>Light Green</span>
                        </li>
                        <li data-theme="lime">
                            <div class="lime"></div>
                            <span>Lime</span>
                        </li>
                        <li data-theme="yellow">
                            <div class="yellow"></div>
                            <span>Yellow</span>
                        </li>
                        <li data-theme="amber">
                            <div class="amber"></div>
                            <span>Amber</span>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                            <span>Orange</span>
                        </li>
                        <li data-theme="deep-orange">
                            <div class="deep-orange"></div>
                            <span>Deep Orange</span>
                        </li>
                        <li data-theme="brown">
                            <div class="brown"></div>
                            <span>Brown</span>
                        </li>
                        <li data-theme="grey">
                            <div class="grey"></div>
                            <span>Grey</span>
                        </li>
                        <li data-theme="blue-grey">
                            <div class="blue-grey"></div>
                            <span>Blue Grey</span>
                        </li>
                        <li data-theme="black">
                            <div class="black"></div>
                            <span>Black</span>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        <!-- #END# Right Sidebar -->
    </section>



    <section class="content">
        @yield('content')   
    </section>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <!--<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>-->

    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="plugins/flot-charts/jquery.flot.js"></script>
    <script src="plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>

    <!-- PDF...-->
    <script src="js/pdfmake.min.js"></script>
    <script src="js/vfs_fonts.js"></script>  
    <script src="js/date-euro.js"></script>  


</body>

</html>
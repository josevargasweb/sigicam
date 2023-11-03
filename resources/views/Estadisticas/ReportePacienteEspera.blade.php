@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte pacientes en espera</a></li>
@stop

@section("script")

    <script>
        $(function() {

            $("#estadistica").collapse();

            $(".fecha-grafico").datepicker({
                startView: 'months',
                minViewMode: "months",
                autoclose: true,
                language: "es",
                format: "mm-yyyy",
                //todayHighlight: true,
                endDate: "+0d"
            });
            ///////////////////////////
            //INICIO REPORTE URGENCIA//
            ///////////////////////////
            $.ajax({
                url: "{{asset('estadisticas/reporteUrgenciasGeneral')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //data: $('#estDirector').serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    //numeros
                    $("#dato1").html("<b>"+data.dato1+"</b>");
                    $("#dato2").html("<b>"+data.dato2+"</b>");
                    $("#dato3").html("<b>"+data.dato3+"</b>");
                    $("#dato4").html("<b>"+data.dato4+"</b>");
                },
                error: function(error){
                    console.log("error: ", error);
                }
            });
            ////////////////////////
            //FIN REPORTE URGENCIA//
            ////////////////////////


            /////////////////////////////////
            //INICIO REPORTE LISTA TRANSITO//
            /////////////////////////////////



            var chartTransito = Highcharts.chart('containerTransito', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Cantidad máxima de pacientes en lista de tránsito por día'
                },
                subtitle: {
                    text: 'Fuente: SIGICAM'
                },
                xAxis: [{
                    categories: ['1', '2', '3', '4', '5', '6',
                        '7', '8', '9', '10', '11', '12', '13',
                        '14', '15', '16', '17', '18', '19', '20', '21', '22',
                        '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                    crosshair: true,
                    title:{
                        text: 'Día del mes'
                    }
                }],
                yAxis: [{
                    title: {
                        text: 'Pacientes',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }],
                tooltip: {
                    shared: true,
                    headerFormat: 'día {point.key} <br/>',
                },
                plotOptions: {
                            spline: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },
                legend: {
                    layout: 'vertical',
                    verticalAlign: 'bottom',
                    floating: false,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: ' N° Pacientes',
                    type: 'column',
                    yAxis: 0,
                    data: [1,5,9],
                    tooltip: {
                        valueSuffix: ''
                    },
                    dataLabels: {
                        enabled: true,
                        format: "{point.y}"
                    },
                    enableMouseTracking: true,

                }]
            });

            $.ajax({
                url: "{{asset('estadisticas/listaTransito/datos')}}",
                type: "get",
                dataType: "json",
                data: {'anno': 0, 'mes': 0},
                success: function(data){
                    chartTransito.update({
                        series: [{
                            data: data.resultados
                        }]
                    });
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });

            $("#btn-grafico-transito").on("click", function(){
                var valor = $("#fecha-grafico-transito").val();
                if(valor == ""){
                   swalWarning.fire({
                    title: 'Información',
                    text:"Debe seleccionar una fecha"
                    });
                }else{
                    var mes = $("#fecha-grafico-transito").datepicker('getDate').getMonth()+1;
                    var anno = $("#fecha-grafico-transito").datepicker('getDate').getFullYear();
                    $.ajax({
                        url: "{{asset('estadisticas/listaTransito/datos')}}",
                        type: "get",
                        dataType: "json",
                        data: {'anno': anno, 'mes': mes},
                        success: function(data){
                            chartTransito.update({
                                series: [{
                                    data: data.resultados
                                },
                                {
                                    data: data.limite
                                }]
                            });
                        },
                        error: function(error){
                            console.log("error: ", error)
                        }
                    });
                }
            });

            //////////////////////////////
            //FIN REPORTE LISTA TRANSITO//
            //////////////////////////////

            ///////////////////////////////
            //INICIO REPORTE LISTA ESPERA//
            ///////////////////////////////

            var chartEspera = Highcharts.chart('containerEspera', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Cantidad máxima de pacientes en lista de espera por día de camas hospitalarias'
                },
                subtitle: {
                    text: 'Fuente: SIGICAM'
                },
                xAxis: [{
                    categories: ['1', '2', '3', '4', '5', '6',
                        '7', '8', '9', '10', '11', '12', '13',
                        '14', '15', '16', '17', '18', '19', '20', '21', '22',
                        '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                    crosshair: true,
                    title:{
                        text: 'Día del mes'
                    }
                }],
                yAxis: [{
                    title: {
                        text: 'Pacientes',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }],
                tooltip: {
                    shared: true,
                    headerFormat: 'día {point.key} <br/>',
                },
                plotOptions: {
                            spline: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },
                legend: {
                    layout: 'vertical',
                    verticalAlign: 'bottom',
                    floating: false,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: ' N° Pacientes',
                    type: 'column',
                    yAxis: 0,
                    data: [1,5,9],
                    tooltip: {
                        valueSuffix: ''
                    },
                    dataLabels: {
                        enabled: true,
                        format: "{point.y}"
                    },
                    enableMouseTracking: true,

                }]
            });

            $.ajax({
                url: "{{asset('estadisticas/listaEspera/datos')}}",
                type: "get",
                dataType: "json",
                data: {'anno': 0, 'mes': 0},
                success: function(data){
                    chartEspera.update({
                        series: [{
                            data: data.resultados
                        }]
                    });
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });

            $("#btn-grafico-espera").on("click", function(){
                var valor = $("#fecha-grafico-espera").val();
                if(valor == ""){
                   swalWarning.fire({
                    title: 'Información',
                    text:"Debe seleccionar una fecha"
                    });

                }else{
                    var mes = $("#fecha-grafico-espera").datepicker('getDate').getMonth()+1;
                    var anno = $("#fecha-grafico-espera").datepicker('getDate').getFullYear();
                    $.ajax({
                        url: "{{asset('estadisticas/listaEspera/datos')}}",
                        type: "get",
                        dataType: "json",
                        data: {'anno': anno, 'mes': mes},
                        success: function(data){
                            chartEspera.update({
                                series: [{
                                    data: data.resultados
                                },
                                {
                                    data: data.limite
                                }]
                            });
                        },
                        error: function(error){
                            console.log("error: ", error)
                        }
                    });
                }
            });

            ////////////////////////////
            //FIN REPORTE LISTA ESPERA//
            ////////////////////////////


            //////////////////////////////////////////////////
            //INICIO REPORTE PROMEDIO ASIGNACION Y SOLICITUD//
            //////////////////////////////////////////////////

            var chartPromedio = Highcharts.chart('containerPromedio', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Promedio solicitud y asignación'
                },
                subtitle: {
                    text: 'Fuente: SIGICAM'
                },
                xAxis: [{
                    categories: ['1', '2', '3', '4', '5', '6',
                        '7', '8', '9', '10', '11', '12', '13',
                        '14', '15', '16', '17', '18', '19', '20', '21', '22',
                        '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                    crosshair: true,
                    title:{
                        text: 'Día del mes'
                    }
                }],
                yAxis: [{
                    title: {
                        text: 'Promedio',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }],
                tooltip: {
                    shared: true,
                    headerFormat: 'día {point.key} <br/>',
                },
                plotOptions: {
                            spline: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },
                legend: {
                    layout: 'vertical',
                    verticalAlign: 'bottom',
                    floating: false,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: 'Hrs promedio',
                    type: 'column',
                    yAxis: 0,
                    data: [],
                    tooltip: {
                        valueSuffix: ''
                    },
                    dataLabels: {
                        enabled: true,
                        format: "{point.y}"
                    },
                    enableMouseTracking: true,

                }]
            });

            $.ajax({
                url: "{{asset('estadisticas/informePromedioSolicitudAsignacionDatos')}}",
                type: "get",
                dataType: "json",
                data: {'anno': 0, 'mes': 0},
                success: function(data){
                    chartPromedio.update({
                        series: [{
                            data: data.resultados
                        }]
                    });
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });

            $(".fecha-grafico").datepicker({
                startView: 'months',
                minViewMode: "months",
                autoclose: true,
                language: "es",
                format: "mm-yyyy",
                //todayHighlight: true,
                endDate: "+0d"
            });

            $("#btn-grafico-promedio").on("click", function(){
                var valor = $("#fecha-grafico-promedio").val();
                if(valor == ""){
                   swalWarning.fire({
                    title: 'Información',
                    text:"Debe seleccionar una fecha"
                    });
                }else{
                    var mes = $("#fecha-grafico-promedio").datepicker('getDate').getMonth()+1;
                    var anno = $("#fecha-grafico-promedio").datepicker('getDate').getFullYear();
                    $.ajax({
                        url: "{{asset('estadisticas/informePromedioSolicitudAsignacionDatos')}}",
                        type: "get",
                        dataType: "json",
                        data: {'anno': anno, 'mes': mes},
                        success: function(data){
                            chartPromedio.update({
                                series: [{
                                    data: data.resultados
                                },
                                {
                                    data: data.limite
                                }]
                            });
                        },
                        error: function(error){
                            console.log("error: ", error)
                        }
                    });
                }
            });

            ///////////////////////////////////////////////
            //FIN REPORTE PROMEDIO ASIGNACION Y SOLICITUD//
            ///////////////////////////////////////////////




     });

    </script>


@stop

@section("section")
  {{ HTML::style('css/navegadortab.css') }}
    <style>
        .numeroActual {
            color: #6A7888;
            margin-top: 0px !important;
            margin-bottom: 0px !important;

        }

        .tituloReporte {
            color: #6A7888;

        }

        .estAnterior {
            color: #6A7888;
        }
        .tamano {
            font-size: 15px !important;
        }

         .main-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(225px, 1fr)); /* Where the magic happens */
            grid-auto-rows: 74px;
            grid-gap: 10px;
            margin: 20px;
        }

        .overviewcard {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: #cff0ce;
        }

        .padding_corregidgo{
            padding-left: 5px;
            padding-right: 5px;
            padding-top: 5px;
            padding-bottom: 5px;
        }

    </style>

    <fieldset>
        <legend>Reporte pacientes en espera</legend>
        <br><br>
        <div class="row" id="urgenciaActual">

             <div class="">
                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte">
                                <img src="{{ asset('img/icono_cama.png') }}" width="30" height="20c" alt=""> Tiempo promedio en espera (Hrs)
                            </label>
                        </div>
                        <div class="overviewcard__info" >
                            <label class="tituloReporte"><h1 class="numeroActual" id="dato1"></h1></label>
                            <p class="numeroActual"><label >Actual</label></p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte">
                                <img src="{{ asset('img/paciente_icono.png') }}" width="25" height="25" alt=""> Pacientes con t° espera > 12 horas
                            </label>
                        </div>
                        <div class="overviewcard__info">
                            <label class="tituloReporte"><h1 class="numeroActual" id="dato2"></h1></label>
                            <p class="numeroActual"><label >Actual</label></p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="25" height="25" alt=""> Casos ingresados hospitalizados</label>
                        </div>
                        <div class="overviewcard__info">
                            <label><h1 class="numeroActual" id="dato3"></h1></label>
                            <p class="numeroActual"><label >Actual</label> </p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="25" height="25" alt=""> Casos ingresados</label>
                        </div>
                        <div class="overviewcard__info">
                            <label><h1 class="numeroActual" id="dato4"></h1></label>
                            <p class="numeroActual"><label >Actual</label> </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <br>
        <div id="dia" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>

        <br>
        <div id="mes" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>


        <br>

        <div id="cie10" style="min-width: 310px; height: 600px; margin: 0 auto" hidden></div>

        <br>

        <div class="container" width='50%'>
            <ul class="nav nav-pills primerNav">
                <li class="nav"><a href="#EsperaCamas" data-toggle="tab">Espera de cama</a></li>
                <li class="nav active"><a href="#ReporteTransito" data-toggle="tab">Espera de hospitalización</a></li>
                <li class="nav"><a href="#PromedioSolicitudAsignacion" data-toggle="tab">Informe tiempo promedio solicitud/asignación</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" style="padding-top:10px;" id="ReporteTransito">
                    <fieldset>
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <input type="text" id="fecha-grafico-transito" class="form-control fecha-grafico"  value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btn-grafico-transito" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div id="containerTransito" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
                        </div>
                    </fieldset>
                </div>
                <div class="tab-pane fade" style="padding-top:10px;" id="EsperaCamas">
                    <fieldset>
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <input type="text" id="fecha-grafico-espera" class="form-control fecha-grafico"  value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btn-grafico-espera" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div id="containerEspera" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
                        </div>
                    </fieldset>
                </div>
                <div class="tab-pane fade" style="padding-top:10px;" id="PromedioSolicitudAsignacion">
                    <fieldset>
                        <div class="col-sm-11">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <input type="text" id="fecha-grafico-promedio" class="form-control fecha-grafico"  value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btn-grafico-promedio" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div id="containerPromedio" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
                        </div>
                    </fieldset>

                </div>
            </div>
        </div>





    </fieldset>

@stop

@extends("Templates/template")

@section("titulo")
    Estadísticas
@stop

@section("miga")
    <li><a href="#">Estadisticas</a></li>
    <li><a href="#" onclick='location.reload()'>Reporte Resumen</a></li>
@stop

@section("script")

    <script>

        $(function() {
            $("#estadistica").collapse();

            $(".fecha-sel").datepicker({
                startView: 'months',
                minViewMode: "months",
                autoclose: true,
                language: "es",
                format: "mm-yyyy",
                //todayHighlight: true,
                endDate: "-1m",
            });

            var diaListaEspera = Highcharts.chart('dia', {
                chart: {
                    type: 'areaspline'
                },
                title: {
                    text: 'Pacientes en lista de espera por día de semana'
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 150,
                    y: 100,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                },
                xAxis: {
                    categories: [
                    'Lunes',
                    'Martes',
                    'Miércoles',
                    'Jueves',
                    'Viernes',
                    'Sabado',
                    'Domingo'
                    ]
                },
                yAxis: {
                    title: {
                    text: 'Pacientes'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' pacientes'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    areaspline: {
                        fillOpacity: 0.5,
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                series: [{
                        name: 'Mínimo',
                    }, {
                        name: 'Promedio',
                    },{
                        name: 'Máximo',
                }]
            });

            var mesListaEspera = Highcharts.chart('mes', {
                chart: {
                    type: 'areaspline'
                },
                title: {
                    text: 'Pacientes por mes en lista de espera'
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 150,
                    y: 100,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                },
                xAxis: {
                    categories: [
                    'Enero',
                    'Febrero',
                    'Marzo',
                    'Abril',
                    'Mayo',
                    'Junio',
                    'Julio',
                    'Agosto',
                    'Septiembre',
                    'Octubre',
                    'Noviembre',
                    'Diciembre'
                    ]
                },
                yAxis: {
                    title: {
                    text: 'Pacientes'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' pacientes'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    areaspline: {
                        fillOpacity: 0.5,
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                series: [{
                        name: 'Mínimo',
                        //data: data.min_mes
                    }, {
                        name: 'Promedio',
                        //data: data.promedios_meses
                    },{
                        name: 'Máximo',
                        //data: data.max_mes
                }]
            });

            var pieCie10 = Highcharts.chart('cie10', {
                chart: {

                },
                title: {
                    text: ''
                },
                xAxis: {
                },
                yAxis: {
                    title: {
                        text: 'Pacientes con CIE 10'
                    }

                },
                labels: {
                    items: [{
                        html: 'Gráfico por porcentaje',
                        style: {
                            left: '200px',
                            top: '60px',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                        }
                    }]
                },
                series: [
                    {
                        type: 'pie',
                        name: '',
                        showInLegend: false,
                        dataLabels: {
                            enabled: true,
                            format: "<b>{point.cie10}</b>: {point.y} ({point.percentage:.1f} %)"
                        },
                        enableMouseTracking: true,
                        tooltip: {

                        },
                    }
                ]
            });

            //barras
            var barraCie10 = Highcharts.chart('cie10b', {
                chart : {
                    marginRight:20,
                        marginLeft:70,
                        marginTop:50,
                },
                title: {
                    text: ''
                },
                xAxis: {
                },
                yAxis: {
                    title: {
                        text: 'Pacientes con CIE 10'
                    }

                },
                series: [
                    {
                    type: 'column',
                    name: 'Pacientes',
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false,

                    },
                ]
            });

            var prom_domiciliaria = Highcharts.chart('prom_mes_domiciliaria', {
                chart: {
                    type: 'areaspline'
                },
                title: {
                    text: 'Días pacientes en Hospitalizacion domiciliaria'
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 150,
                    y: 100,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                },
                xAxis: {
                    categories: [
                    'Enero',
                    'Febrero',
                    'Marzo',
                    'Abril',
                    'Mayo',
                    'Junio',
                    'Julio',
                    'Agosto',
                    'Septiembre',
                    'Octubre',
                    'Noviembre',
                    'Diciembre'
                    ]
                },
                yAxis: {
                    title: {
                    text: 'Días'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' días'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    areaspline: {
                        fillOpacity: 0.5,
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                series: [{
                        name: 'Mínimo',
                    }, {
                        name: 'Promedio',
                    },{
                        name: 'Máximo',
                }]
            });

            var barraCie10HospDom = Highcharts.chart('cie10ComunesDomicilio', {
                chart : {
                    marginRight:20,
                        marginLeft:70,
                        marginTop:50,
                },
                title: {
                    text: 'Enfermedades más comunes en hospitalización domiciliaria'
                },
                xAxis: {
                },
                yAxis: {
                    title: {
                        text: 'Cantidad de pacientes'
                    }

                },
                series: [
                    {
                    type: 'column',
                    name: 'Pacientes',
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false,

                    },
                ]
            });

            $.ajax({
                url: "{{asset('estadisticas/camas/estadisticasPacientesGeneral')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //data: $('#estDirector').serialize(),
                dataType: "json",
                type: "get",
                success: function(data){
                    //numeros
                    $("#numeroActual").html("<b>"+data.urgencia+"</b>");
                    $("#numeroTransitoActual").html("<b>"+data.transito+"</b>");
                    $("#numeroTotalCamasOcupadasActual").html("<b>"+data.camas_ocupadas+"</b>");
                    $("#numeroTotalCamasEgresados").html("<b>"+data.egresados+"</b>");
                },
                error: function(error){
                    console.log("error: ", error);
                }
            });


            /* $("#estDirector").bootstrapValidator({
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                fields: {
                    "fecha-inicio": {
                        validators:{
                            notEmpty: {
                                message: 'El fecha de nacimiento es obligatoria'
                            },

                        },
                    },
                }
            }).on('status.field.bv', function(e, data) {
                data.bv.disableSubmitButtons(false);
            }).on('error.form.bv', function(e) {
                console.log(e);
            }).on("success.form.bv", function(evt){
                var $form = $(evt.target);
                fv = $form.data('bootstrapValidator');
                fv.disableSubmitButtons(false);
                evt.preventDefault();
            }); */

            $("#btnHospDomiciliaria").on("click", function(){
                var valorI = $("#fechahospDomInicio").val();
                var valorF = $("#fechahospDomFin").val();

                if(valorI == "" ){
                     swalWarning.fire({
                    title: 'Información',
                    text:"Debe seleccionar una fecha de inicio"
                    });

                }else if(valorF == ""){
                    swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha de fin"
					});
                }else{
                    var mesI = $("#fechahospDomInicio").datepicker('getDate').getMonth()+1;
                    var annoI = $("#fechahospDomInicio").datepicker('getDate').getFullYear();
                    var mesF = $("#fechahospDomFin").datepicker('getDate').getMonth()+1;
                    var annoF = $("#fechahospDomFin").datepicker('getDate').getFullYear();

                    var I = new Date(annoI, mesI);
                    var F = new Date(annoF, mesF);

                    if( I > F ){
                    swalWarning.fire({
					title: 'Información',
					text:"La fecha de inicio debe ser menor que la de fin"
					});
                    }else{
                        $.ajax({
                            url: "{{asset('estadisticas/informeHospitalizacionDomiciliaria')}}",
                            type: "get",
                            dataType: "json",
                            data: {'annoI': annoI, 'mesI': mesI,'annoF': annoF, 'mesF': mesF},
                            success: function(data){
                                

                                if (data.diagnostico0 != "") {
                                    console.log("entro");
                                    $('#prom_mes_domiciliaria').removeAttr('hidden');
                                    $('#cie10ComunesDomicilio').removeAttr('hidden');
                                    prom_domiciliaria.update({
                                        series: [{
                                            data: data.min_hosp_dom_mes
                                        },
                                        {
                                            data: data.prom_hosp_dom_mes
                                        },
                                        {
                                            data: data.max_hosp_dom_mes
                                        }]
                                    });
                                    $("#numeroPromedioDomicilio").html("<b>"+data.numeroPromedioDomicilio+"</b>");
                                    $("#numeroCIE10ActualHospDom").html("<b>"+data.cie10ComunHospDom+"</b>");


                                    barraCie10HospDom.update({
                                        xAxis: {
                                            categories: data.cie10ComunesHospDom

                                        },
                                        series: [{
                                            data: data.cie10ComunesHospDomCantidad,
                                        }]
                                    });
                                }
                                
                            },
                            error: function(error){
                                console.log("error: ", error)
                            }
                        });
                    }

                }
            });

            $("#btnCie10Comunes").on("click", function(){
                var valorI = $("#fechaGraficDiagnInicio").val();
                var valorF = $("#fechaGraficDiagnFin").val();
                if(valorI == "" ){
                    swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha de inicio"
					});
                }else if(valorF == ""){
                     swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha de fin"
					});
                }else{
                    var mesI = $("#fechaGraficDiagnInicio").datepicker('getDate').getMonth()+1;
                    var annoI = $("#fechaGraficDiagnInicio").datepicker('getDate').getFullYear();
                    var mesF = $("#fechaGraficDiagnFin").datepicker('getDate').getMonth()+1;
                    var annoF = $("#fechaGraficDiagnFin").datepicker('getDate').getFullYear();

                    var I = new Date(annoI, mesI);
                    var F = new Date(annoF, mesF);

                    if( I > F ){
                    swalWarning.fire({
					title: 'Información',
					text:"La fecha de inicio debe ser menor que la de fin"
					});
                    }else{
                        $.ajax({
                            url: "{{asset('estadisticas/informeDiagnosticos')}}",
                            type: "get",
                            dataType: "json",
                            data: {'annoI': annoI, 'mesI': mesI,'annoF': annoF, 'mesF': mesF},
                            success: function(data){
                                $('#cie10').removeAttr('hidden');
                                $('#cie10b').removeAttr('hidden');
                                pieCie10.update({
                                    xAxis: {
                                        categories: data.lista_cie10
                                    },
                                    series: [{
                                        data: data.lista_cie10_pie,
                                        tooltip: {
                                            pointFormat: '<span style="color:{series.color}">Pacientes</span>: <b>{point.y}</b> de '+data.total_cie10_pie+' ({point.percentage:.0f}%)<br/>',
                                            shared: true
                                        },
                                    }]
                                });

                                barraCie10.update({
                                    xAxis: {
                                        categories: data.lista_cie10

                                    },
                                    series: [{
                                        data: data.valores_cie10,
                                    }]
                                });
                                $("#numeroCIE10Actual").html("<b>"+data.lista_cie10[0]+"</b>");

                            },
                            error: function(error){
                                console.log("error: ", error)
                            }
                        });
                    }

                }
            });

            $("#btnListaEsperaDia").on("click", function(){
                var valorI = $("#fechaGraficListaEsperaInicio").val();
                var valorF = $("#fechaGraficListaEsperaFin").val();
                if(valorI == "" ){
                    swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha de inicio"
					});
                }else if(valorF == ""){
                    swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha de fin"
					});
                }else{
                    var mesI = $("#fechaGraficListaEsperaInicio").datepicker('getDate').getMonth()+1;
                    var annoI = $("#fechaGraficListaEsperaInicio").datepicker('getDate').getFullYear();
                    var mesF = $("#fechaGraficListaEsperaFin").datepicker('getDate').getMonth()+1;
                    var annoF = $("#fechaGraficListaEsperaFin").datepicker('getDate').getFullYear();

                    var I = new Date(annoI, mesI);
                    var F = new Date(annoF, mesF);

                    if( I > F ){
                    swalWarning.fire({
                    title: 'Información',
					text:"La fecha de inicio debe ser menor que la de fin"
					});
                    }else{
                        $.ajax({
                            url: "{{asset('estadisticas/informeListaEspera')}}",
                            type: "get",
                            dataType: "json",
                            data: {'annoI': annoI, 'mesI': mesI,'annoF': annoF, 'mesF': mesF},
                            success: function(data){
                                $('#dia').removeAttr('hidden');
                                $('#mes').removeAttr('hidden');
                                diaListaEspera.update({
                                    series: [{
                                        data: data.min_semana
                                    },
                                    {
                                        data: data.promedios_semana
                                    },
                                    {
                                        data: data.max_semana
                                    }]
                                });

                                mesListaEspera.update({
                                    series: [{
                                        data: data.min_mes
                                    },
                                    {
                                        data: data.promedios_meses
                                    },
                                    {
                                        data: data.max_mes
                                    }]
                                });
                            },
                            error: function(error){
                                console.log("error: ", error)
                            }
                        });
                    }

                }
            });


        });

    </script>

<script>

$(function() {

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
        <legend>Reporte Resumen</legend>
        <br>
        <div class="row" id="urgenciaActual">

            <div class="">
                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/icono_cama.png') }}" width="30" height="20c" alt=""> Número de camas ocupadas</label>
                        </div>
                        <div class="overviewcard__info" >
                            <label class="tituloReporte"><h1 class="numeroActual" id="numeroTotalCamasOcupadasActual"></h1></label>
                            <p class="numeroActual"><label >Actual</label></p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="25" height="25" alt=""> Pacientes en Lista de espera</label>
                        </div>
                        <div class="overviewcard__info">
                            <label class="tituloReporte"><h1 class="numeroActual" id="numeroActual"></h1></label>
                            <p class="numeroActual"><label >Actual</label></p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/paciente_icono.png') }}" width="25" height="25" alt=""> Pacientes en Lista de tránsito</label> 
                        </div>
                        <div class="overviewcard__info">
                            <label class="tituloReporte"><h1 class="numeroActual" id="numeroTransitoActual"></h1></label>
                            <p class="numeroActual"><label >Actual</label> </p>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                    <div class="overviewcard">
                        <div class="overviewcard__icon">
                            <label class="tituloReporte"><img src="{{ asset('img/dado_de_alta.png') }}" width="25" height="25" alt=""> Pacientes dados de alta</label>
                        </div>
                        <div class="overviewcard__info">
                            <label><h1 class="numeroActual" id="numeroTotalCamasEgresados"></h1></label>
                            <p class="numeroActual"><label >Actual</label> </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- <div class="row">
			{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'estDirector', 'style' => 'padding-left: 15px;')) }}
                <div class="form-group">
                    {{Form::text('fecha_inicio', \Carbon\Carbon::now()->startOfMonth()->format("m-Y"), array('id' => 'fecha-inicio', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
                </div>

                <div class="form-group">
                    {{Form::text('fecha', \Carbon\Carbon::now()->format("m-Y"), array('id' => 'fecha', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
                </div>

                <div class="form-group">
                    {{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary' )) }}
                </div>
            {{ Form::close() }}
        </div> --}}
        <br>



        <div class="container" width='50%'>
            <ul class="nav nav-pills primerNav">
                <li class="nav in active"><a href="#listaEsperaDiaSemana" data-toggle="tab">Lista de espera</a></li>
                <li class="nav"><a href="#diagnMasComunes" data-toggle="tab">Diagnósticos más comunes</a></li>
                @if(Auth::user()->tipo == TipoUsuario::MASTER)
                    <li class="nav"><a href="#hospDom" data-toggle="tab">Hospitalización domiciliaria</a></li>
                @endif
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" style="padding-top:10px;" id="listaEsperaDiaSemana">
                    <fieldset>
                        <div class="col-sm-11">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">
                                <input type="text" id="fechaGraficListaEsperaInicio" class="form-control fecha-sel">
                                <label for="">Fecha inicio</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">
                                <input type="text" id="fechaGraficListaEsperaFin" class="form-control fecha-sel">
                                <label for="">Fecha fin</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btnListaEsperaDia" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            {{-- <div id="containerTransito" style="min-width: 310px; height: 400px; margin: 40px auto"></div> --}}
                            <div id="dia" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>

                            <br>
                            <div id="mes" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>
                        </div>
                    </fieldset>
                </div>

                <div class="tab-pane fade" style="padding-top:10px;" id="diagnMasComunes">
                    <fieldset>
                        <div class="col-sm-11">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">
                                <input type="text" id="fechaGraficDiagnInicio" class="form-control fecha-sel">
                                <label for="">Fecha inicio</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">
                                <input type="text" id="fechaGraficDiagnFin" class="form-control fecha-sel">
                                <label for="">Fecha fin</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btnCie10Comunes" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="">
                                    <div class="col-xs-4 col-sm-4 col-md-4 padding_corregidgo">
                                        <div class="overviewcard">
                                            <div class="overviewcard__icon"> 
                                                <label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="35" height="35" alt=""> CIE 10 más común</label>
                                            </div>
                                            <div class="overviewcard__info">
                                                <label><h1 class="numeroActual tamano" id="numeroCIE10Actual"></h1></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header">
                                    <div class="escondido hidden"><center><h4>Diagnósticos más comunes</h4></center></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card card-chart">
                                                <div class="card-header">

                                                </div>
                                                <div class="card-content">
                                                    <div class="ct-chart">
                                                    <div id="cie10" style="min-width: 310px; height: 600px; margin: 0 auto" hidden></div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card card-chart">
                                                <div class="card-header">

                                                </div>
                                                <div class="card-content">
                                                    <div class="ct-chart">
                                                    <div id="cie10b" style="min-width: 310px; height: 600px; margin: 0 auto" hidden></div>
                                                    </div>
                                                </div>

                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                </div>
                <div class="tab-pane fade" style="padding-top:10px;" id="hospDom">
                    <fieldset>
                        <div class="col-sm-11">
                            <div class="col-sm-12">
                                <label>Seleccione fecha</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">

                                <input type="text" id="fechahospDomInicio" class="form-control fecha-sel">
                                <label for="">Fecha inicio</label>
                            </div>
                            <div class="col-sm-2 form-group" style="text-align:center;">
                                <input type="text" id="fechahospDomFin" class="form-control fecha-sel">
                                <label for="" style="">Fecha fin</label>
                            </div>
                            <div class="col-sm-2 form-group">
                                <button id="btnHospDomiciliaria" class="btn btn-primary">Generar gráfico</button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="">
                                    <div class="col-xs-6 col-sm-6 col-md-6 padding_corregidgo">
                                        <div class="overviewcard">
                                            <div class="overviewcard__icon">
                                                <label class="tituloReporte">
                                                    <img src="{{ asset('img/dado_de_alta.png') }}" width="25" height="25" alt=""> Promedio estadia domiciliaria
                                                </label>
                                            </div>
                                            <div class="overviewcard__info">
                                                <label><h1 class="numeroActual" id="numeroPromedioDomicilio"></h1></label>
                                                <p class="numeroActual"><label >Días</label> </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-6 col-sm-6 col-md-6 padding_corregidgo">
                                        <div class="overviewcard">
                                            <div class="overviewcard__icon"> 
                                                <label class="tituloReporte"><img src="{{ asset('img/cie_10_icono.png') }}" width="35" height="35" alt=""> CIE-10 más común</label>
                                            </div>
                                            <div class="overviewcard__info">
                                                <label><h1 class="numeroActual tamano" id="numeroCIE10ActualHospDom"></h1></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div id="prom_mes_domiciliaria" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>
                            <br>

                            <div id="cie10ComunesDomicilio" style="min-width: 310px; height: 400px; margin: 0 auto" hidden></div>
                        </div>
                    </fieldset>

                </div>

            </div>
        </div>

    </fieldset>

@stop

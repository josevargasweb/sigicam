@extends("Templates/template")
{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Kmeans</a></li>
@stop

@section("script")
<script>
    $("#estadistica").collapse();
    var hoy = new Date();
    hoy = hoy.getFullYear()+1;

    
    $(".fechakmeans").datetimepicker({
        locale: "es",
        format: "YYYY",
        maxDate: moment().years(hoy)
    });

    /* $(document).on("change", "text[name='año']", function(){
        $('#formCalcularKnox').bootstrapValidator('revalidateField', 'año');
    }); */


    $("#añoFin").on("dp.change", function(){
        $('#formCalcularKnox').bootstrapValidator('revalidateField', 'año');
    });

    $("#añoInicio").on("dp.change", function(){
        $('#formCalcularKnox').bootstrapValidator('revalidateField', 'añoInicio');
    });
    

    //ocultar tablas si no estan siendo usadas
    $("#scatterplot").hide();


    $(".remove").hide();
    $(".oculto").hide();

    $(document).on('click', '.add', function () {
        $(".add").hide();
        $(".remove").show();
        $(".oculto").show();
        $("#añoInicio").val(null);
    });

    $(document).on('click', '.remove', function () {
        $(".remove").hide();
        $(".add").show();
        $(".oculto").hide();
    });

    

    $("#formCalcularKnox").bootstrapValidator({
        excluded: [':disabled', ':hidden', ':not(:visible)'],
        fields: {
            
            año: {
                validators: {
                    notEmpty: {
                        message: 'Este campo es obligatorio'
                    },
                }
            },
            añoInicio: {
                validators: {
                    notEmpty: {
                        message: 'Este campo es obligatorio'
                    },
                }
            },
            grupo: {
                validators:{
                    notEmpty: {
                        message: 'Este campo es obligatorio'
                    },
                    integer: {
                        message: 'Solo ingresar números'
                    },
                    between: {
                        min: 1,
                        max: 9,
                        message: 'Tiene que estar entre el rango de 1 a 9'
                    }
                }
            },
            enfermedad: {
                validators:{
                    notEmpty: {
                        message: 'Este campo es obligatorio'
                    }
                }
            } 
        }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on('error.form.bv', function(e) {
        console.log(e);
    }).on("success.form.bv", function(evt){
        console.log("comprobado");

        evt.preventDefault(evt);
        $.ajax({
            url: "calcularKmeans",
            data: $( "form" ).serialize(),
            type: "post",
            dataType: "json",
            success: function (data) {  
                console.log("datos kmeans: ",data.length);
                
                datosKmeans = new Array();
                
                //console.log("datos Grafico: ", datosGrafico);
                for (let index = 0; index < data.length; index++) {

                    var color_random = 'rgba(' + (Math.floor(Math.random() * 256)) + ', ' + (Math.floor(Math.random() * 256)) + ', ' + (Math.floor(Math.random() * 256)) + ', .8)';

                    cadena = {};
                    cadena = {
                                name: 'Cluster '+(index+1),
                                color: color_random,
                                data: data[index]
                            };
                    console.log("cadena random: ",cadena);
                    datosKmeans.push(cadena);
                }
                
                $("#scatterplot").show();
                chart1 = new Highcharts.Chart({
                    chart: {
                        type: 'scatter',
                        renderTo: 'scatterplot',
                        zoomType: 'xy',
                        height:400
                    },
                    title: {
                        text: 'Edad v/s Tiempo de estada, separados por cluster'
                    },
                    subtitle: {
                        text: 'Fuente: SIGICAM'
                    },
                    xAxis: {
                        title: {
                            enabled: true,
                            text: 'Edad (años)'
                        },
                        startOnTick: true,
                        endOnTick: true,
                        showLastLabel: true
                    },
                    yAxis: {
                        title: {
                            text: 'T° de estada (días)'
                        },
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'left',
                        verticalAlign: 'top',
                        x: 100,
                        y: 60,
                        floating: true,
                        backgroundColor: '#FFFFFF',
                        borderWidth: 1
                    },
                    plotOptions: {
                        scatter: {
                            marker: {
                                radius: 5,
                                states: {
                                    hover: {
                                        enabled: true,
                                        lineColor: 'rgb(100,100,100)'
                                    }
                                }
                            },
                            states: {
                                hover: {
                                    marker: {
                                        enabled: false
                                    }
                                }
                            },
                            events: {
                                legendItemClick: function () {
                                    return false; 
                                }
                            },
                            tooltip: {
                                headerFormat: '<b>{series.name}</b><br>',
                                pointFormat: 'Edad: {point.x} años<br>T° de estada: {point.y} <días>'
                            }
                        },
                        allowPointSelect: false,
                    },
                    series: datosKmeans
                });

                
            },
            error: function (error) {
                console.log("erroriando; ",error);
            }
        });
    });

    


    var datos_cie10 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
            wildcard: '%QUERY',
            filter: function(response) {
                return response;
            }
        },
        limit: 10
    });

    datos_cie10.initialize();

    $('.diagnostico_cie101 .typeahead').typeahead(null, {
        name: 'best-pictures',
        display: 'nombre_cie10',
        source: datos_cie10.ttAdapter(),
        templates: {
            empty: [
                '<div class="empty-message">',
                    'No hay resultados',
                '</div>'
            ].join('\n'),
            suggestion: function(data){
                var nombres = data;
                return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
            },
            header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
        }
    }).on('typeahead:selected', function(event, selection){
            //$("#texto_cie10").val(selection.nombre_cie10);
        $("[name='hidden_enfermedad']").val(selection.id_cie10);
    });

    function invoice_no_setup_typeahead(self, self2) {
        var datos_cie10 = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
                wildcard: '%QUERY',
                filter: function(response) {
                    return response;
                }
            },
            limit: 10
        });
        datos_cie10.initialize();
        console.log('focus acheived');
        $(self).typeahead(null, {
            name: 'best-pictures',
            display: 'nombre_cie10',
            source: datos_cie10.ttAdapter(),
            templates: {
                empty: [
                '<div class="empty-message">',
                    'No hay resultados',
                '</div>'
                ].join('\n'),
                suggestion: function(data){
                    return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
                },
                header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
            }
        }).on('typeahead:selected', function(event, selection){
            //$("#texto_cie10").val(selection.nombre_cie10);
            $(self2).val(selection.id_cie10);

        });

    }

    
</script>


@stop



@section("section")

    <style type="text/css">

        .tt-input{
            width:100%;
        }
        .tt-query {
          -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
             -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }
        
        .tt-hint {
          color: #999
        }
        
        .tt-menu {    /* used to be tt-dropdown-menu in older versions */
          /*width: 430px;*/
          margin-top: 4px;
         /* padding: 4px 0;*/
          background-color: #fff;
          border: 1px solid #ccc;
          border: 1px solid rgba(0, 0, 0, 0.2);
          -webkit-border-radius: 4px;
             -moz-border-radius: 4px;
                  border-radius: 4px;
          -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
             -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
                  box-shadow: 0 5px 10px rgba(0,0,0,.2);
        }
        
        .tt-suggestion {
         /* padding: 3px 20px;*/
          line-height: 24px;
        }
        
        .tt-suggestion.tt-cursor,.tt-suggestion:hover {
          color: #fff;
          background-color: #1E9966;
        
        }
        
        .tt-suggestion p {
          margin: 0;
        }
        .twitter-typeahead{
            width:100%;
        }

    </style>

    <h3 class="text-center row">
        Kmeans
    </h3>

    <div class="panel panel-info row">
        <div class="panel-heading">Descripción</div>
        <div class="panel-body">
            El objetivo del algoritmo Kmeans es maximizar la similaridad de los elementos dentro de cada grupo al mismo tiempo que se maximiza la disimilaridad con los elementos en diferentes grupos.
        </div>
        
    </div>


    <div class="row">
        
        <div class="col-sm-4">
            {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'formCalcularKnox')) }}

            <br>
            <div class="row ">
                
                    <div class="col-sm-5 text-right">
                            {{ Form::label('grupo','Número de clusters (grupos):') }}
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-9">
                            {{ Form::text('grupo', null, array('id' => 'grupo', 'class' => 'form-control'))}}
                        </div>
                    </div>
            </div>

            {{-- <br>
            <div class="row ">
                
                    <div class="col-sm-5 text-right">
                            Form::label('comuna','Comuna :') 
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-9">
                            Form::select('comuna', $comunas, 0, array('id' => 'comuna', 'class' => 'col form-control'))
                        </div>
                        
                    </div>
            </div> --}}

            <br>
            <div class="row ">

                    <div class="col-sm-5 text-right">
                            {{ Form::label('enfermedad','Enfermedad :') }}
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-12 diagnostico_cie101">
                            <input type="text" name="enfermedad" class='form-control typeahead' />
                            <input type="hidden" name="hidden_enfermedad">
                        </div>
                        
                    </div>
            </div>

            <br>
            <div class="row oculto">
                    <div class="col-sm-5 text-right">
                            {{ Form::label('añoInicio','Año Inicio:') }}
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-8">
                            {{ Form::text('añoInicio', null , array('id' => 'añoInicio', 'class' => 'form-control fechakmeans'))}} 
                        </div>
                    </div>
            </div>

            <br>
            <div class="row ">
                
                    <div class="col-sm-5 text-right">
                            {{ Form::label('año','Año :') }}
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-8">
                            {{ Form::text('año', null, array('id' => 'añoFin', 'class' => 'form-control fechakmeans'))}}
                        </div>
                    </div>
            </div>

            <br>
            <div class="row">
                <div class="col text-center">
                    <a  class="btn add" type="button">Añadir rango de años</a>
                </div>
                <div class="col text-center">
                    <a  class="btn remove" type="button">Quitar rango de años</a>
                </div>    
            </div>
            

            <br>
            <br>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" id="actualizar">Actualizar</button>
            </div>

            {{ Form::close() }}
        </div>
        <div class="col-sm-8">
            <div id="scatterplot" style="width: 100%; height: 500px; margin-left:10px; float:left"></div> <br>
        </div>
    </div>

    
   

    

@stop

@extends("Templates/template")
{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Algoritmo de Distribución Espacial</a></li>
@stop

@section("script")
<script>
    $("#estadistica").collapse();
    var hoy = new Date();
    hoy = hoy.getFullYear()+1;
    console.log("hholaa ", hoy);

    $(".fechaDistEspacial").datetimepicker({
        locale: "es",
        format: "YYYY",
        maxDate: moment().years(hoy)
    });

    $("#añoFin").on("dp.change", function(){
        $('#formCalcularDistribucionEspacial').bootstrapValidator('revalidateField', 'año');
    });

    $("#añoInicio").on("dp.change", function(){
        $('#formCalcularDistribucionEspacial').bootstrapValidator('revalidateField', 'añoInicio');
    });

    $("#wrapper").hide();
    
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

    $("#formCalcularDistribucionEspacial").bootstrapValidator({
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
        evt.preventDefault(evt);
        $.ajax({
            url: "calcularDistEsp",
            data: $( "form" ).serialize(),
            type: "post",
            dataType: "json",
            success: function (data) {  
                //console.log("mapa atacama: ", mapa_atacama);
                var codigo;
                var valor;
                datosGrafico = new Array();
                
                //console.log("datos Grafico: ", datosGrafico);
                for (let index = 0; index < data.length; index++) {
                    cadena = {};
                    cadena = {code:data[index], value: index+1 };
                    datosGrafico.push(cadena);
                }
                /* console.log("datos: ", datosGrafico); */

                var series = [{
                    type: "map",
                    name:'Posible alerta',
                    enableMouseTracking: true,
                    color: "#ff0000",
                    allowPointSelect: false,
                    "joinBy": 'code',
                    mapData: mapa_atacama,
                }];

                //mostrar mapa
                $("#wrapper").show();
                
                $('#container').highcharts('Map', {
                    title : {
                        text : 'Casos Atacama'
                    },
                    chart : {
                            borderWidth : 1,
                            plotBorderColor: "#ff0000"
                        },
                    colorAxis: {
                            dataClasses: [
                            {
                            from: 1,
                            to: 2,
                            color: "#FF0000"
                        }]
                    },
                    subtitle : {
                        text : 'Casos Región de Atacama.<br/>' +
                            'Fuente: SIGICAM.'
                    },
                    plotOptions: {
                        series: {
                        name:'Posible alerta',
                            allowPointSelect: true,
                            data:datosGrafico
                        }       
                    },
                tooltip: { 
                        formatter: function() { 
                        /* console.log(this.series); */
                        valor="";
                        for(var i=0; i<this.series.data.length;i++){
                        if(this.point.value >=1){
                            valor="posible alerta";
                        }else{
                            valor="sin alerta";
                        }
                        return this.point.name+': '+valor+'<br>'; 
                        }
                    } 
                    },

                    legend: {
                    enabled: false
                    },
                    series : series
                });
                /* console.log("series: ", series); */

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

        /* #wrapper {
            height: 600px;
            width: 900px;
            margin: 0 auto;
            padding: 0;
        } */
        #container {
            /* float: left; */
            height: 500px; 
            width: 550px; 
            margin: 0;
        }
        #info {
           /*  float: left; */
            width: 300px;
            padding-left: 20px;
            margin: 100px 0 0 0;
            border-left: 1px solid silver;
        }
        #info h2 {
            display: inline;
        }
    </style>

    <h3 class="text-center row">
        Algoritmo de Distribución Espacial
    </h3>

    <div class="panel panel-info row">
        <div class="panel-heading">Descripción</div>
        <div class="panel-body">
                Método para el análisis de la distribución espacial de los datos, que permite identificar áreas geográficas, que presentan un número mayor de casos en cierta enfermedad de los que se esperan bajo condiciones 'normales'.
        </div>
        
    </div>

    <div class="row">
        <div class="col-sm-4">
            {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'formCalcularDistribucionEspacial')) }}

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
                        <div class="col-sm-9">
                            {{ Form::text('añoInicio', null , array('id' => 'añoInicio', 'class' => 'form-control fechaDistEspacial'))}} 
                        </div>
                    </div>
            </div>

            <br>
            <div class="row ">
                
                    <div class="col-sm-5 text-right">
                            {{ Form::label('año','Año :') }}
                    </div>
                    <div class="col-sm-7">
                        <div class="col-sm-9">
                            {{ Form::text('año', null, array('id' => 'añoFin', 'class' => 'form-control fechaDistEspacial'))}}
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
            <div id="wrapper" >
            <div id="container"></div>
            <div
                id="info">
                <h2></h2>
                <div id="atacama-chart"></div>
            </div>
        </div>
        </div>

    </div>

    

    

@stop

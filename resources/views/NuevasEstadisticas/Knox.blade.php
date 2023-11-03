@extends("Templates/template")
{{ HTML::script('js/ProgressBar.js') }}

{{ HTML::style('css/ProgressBar.css') }}

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Método Knox</a></li>
@stop

@section("script")
<script>
    $("#estadistica").collapse();
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


    var hoy = new Date();
    hoy = hoy.getFullYear()+1;
    console.log("hholaa ", hoy);

    $(".fechaKnox").datetimepicker({
        locale: "es",
        format: "YYYY",
        maxDate: moment().years(hoy)
    });

    /* var fecha = $("#añoInicio").data("DateTimePicker"); */

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

    $("#añoFin").on("dp.change", function(){
        $('#formCalcularKnox').bootstrapValidator('revalidateField', 'año');
    });

    $("#añoInicio").on("dp.change", function(){
        $('#formCalcularKnox').bootstrapValidator('revalidateField', 'añoInicio');
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
            espacio: {
                validators:{
                    notEmpty: {
                        message: 'El espacio es obligatorio'
                    },
                    integer: {
                        message: 'El valor debe ser un nómero entero'
                    }
                }
            },
            tiempo: {
                validators:{
                    notEmpty: {
                        message: 'El tiempo es obligatorio'
                    },
                    integer: {
                        message: 'El valor debe ser un nómero entero'
                    }
                }
            },
            enfermedad: {
                validators:{
                    notEmpty: {
                        message: 'La enfermedad es obligatoria'
                    }
                }
            }
        }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on('error.form.bv', function(e) {
        console.log(e);
    }).on("success.form.bv", function(evt){
        /* console.log("hola"); */
        evt.preventDefault(evt);
       /*  console.log($( "form" ).serialize()); */
        $.ajax({
            url: "calcularKnox",
            data: $( "form" ).serialize(),
            type: "post",
            dataType: "json",
            success: function (data) {
                $(".espacioTiempo").html(data.cercanos_en_tiempo_y_espacio);
                $(".soloTiempo").html(data.cercanos_en_tiempo);
                $(".soloEspacio").html(data.cercanos_en_espacio);
                $(".noCercanos").html(data.no_cercanos);
                $("#a").html(data.a);
                $("#probabilidad").html(data.probabilidad);
            },
            error: function (error) {
                console.log(error);
            }
        });
    });


    $("#selectFiltroPacientes").on("change", function () {
        obtenerPacientes($(this).val());
    });
</script>


@stop

@section("section")
<style>
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

<fieldset>
    <h3 class="text-center row">
        Método Knox
    </h3>
    <h4 class="text-center row">
        Agregación espacio-temporal
    </h4>
    <br>

    <!-- <span class="text-center panel panel-info">
        Descripción: Método que detecta agregaciones espacio-temporales, que ocurren cuando los casos observados de una una enfermedad en determinada región guardan cercania espacial y temporal.
    </span> -->

    <div class="panel panel-info row">
        <div class="panel-heading">Descripción</div>
        <div class="panel-body">
        Método que detecta agregaciones espacio-temporales, que ocurren cuando los casos observados de una una enfermedad en determinada región guardan cercania espacial y temporal.
        </div>
        
    </div>
  

    <br>
    <br>

    <div class="row">
        <div class="col-sm-6">
                {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'formCalcularKnox')) }}
                <div class="row ">
                    
                        <div class="col-sm-5 text-right">
                                {{ Form::label('espacio','Espacio máx. :') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                {{ Form::text('espacio', null, array('id' => 'espacio', 'class' => 'form-control'))}} 
                            </div>
                            <div class="col-sm-4">
                                <label> metros</label>
                            </div>
                        </div>
                </div>
                <br>
                <div class="row ">
                    
                        <div class="col-sm-5 text-right">
                                {{ Form::label('tiempo','Tiempo máx. :') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                {{ Form::text('tiempo', null, array('id' => 'tiempo', 'class' => 'form-control'))}} 
                            </div>
                            <div class="col-sm-4">
                                <label> días</label>
                            </div>
                        </div>
                </div>
            
                <br>
                <div class="row ">
                    
                        <div class="col-sm-5 text-right">
                                {{ Form::label('comuna','Comuna :') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-9">
                                {{ Form::select('comuna', $comunas, 0, array('id' => 'comuna', 'class' => 'col form-control'))}} 
                            </div>
                            
                        </div>
                </div>
            
                <br>
                <div class="row ">
            
                        <div class="col-sm-5 text-right">
                                {{ Form::label('enfermedad','Enfermedad :') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-9 diagnostico_cie101">
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
                        <div class="col-sm-4">
                            <div class="col-sm-9">
                                {{ Form::text('añoInicio', null , array('id' => 'añoInicio', 'class' => 'form-control fechaKnox'))}} 
                            </div>
                        </div>
                </div>
            
                <br>
                <div class="row ">
                    
                        <div class="col-sm-5 text-right">
                                {{ Form::label('año','Año :') }}
                        </div>
                        <div class="col-sm-4">
                            <div class="col-sm-9">
                                {{ Form::text('año', null, array('id' => 'añoFin', 'class' => 'form-control fechaKnox'))}}
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

        <div class="col-sm-6">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Cercanos en tiempo</th>
                        <th>NO Cercanos en tiempo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Cercanos en espacio</th>
                        <th><label class="espacioTiempo"></label></th> 
                        <th><label class="soloEspacio"></label></th>   
                    </tr>
                    <tr>
                        <th>NO cercanos en espacio</th>
                        <th><label class="soloTiempo"></label></th>
                        <th><label class="noCercanos"></label></th>
                    </tr>
                    
                </tbody>
            </table>

            <div class="row text-center">
                <p>Probabilidad de observar al menos: <label id="a"></label> pares ceranos en espacio y tiempo:  <label id="probabilidad"></label></p>
            </div>
        </div>

    </div>


    
	
</fieldset>
<br><br>








@stop

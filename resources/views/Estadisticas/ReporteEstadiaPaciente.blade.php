@extends("Templates/template")

@section("titulo")
Estadísticas por Diagnóstico
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte por diagnóstico</a></li>
@stop

@section("script")
<script>
    $("#estadistica").collapse();
    
    var compararFechas = function(){
		var inicio = $("#fechaInicio").val();
		var horaInicio = inicio.substring(0,2);
		var minutosInicio = inicio.substring(3,5);
		var anoInicio = inicio.substring(6,inicio.length);
		//var fechaInicio = horaInicio + "-" + minutosInicio + "-" + anoInicio;
		var fechaInicio = anoInicio + "-" + minutosInicio + "-" + horaInicio;

		var fin = $("#fechaFin").val();
		var horaFin = fin.substring(0,2);
		var minutosFin = fin.substring(3,5);
		var anoFin = fin.substring(6,fin.length);
		//var fechaFin = horaFin + "-" + minutosFin + "-" + anoFin;
		var fechaFin = anoFin + "-" + minutosFin + "-" + horaFin; 
		//alert(fechaInicio + "aaaa" + fechaFin);
		if(fechaInicio <= fechaFin){
			return true;
		}else{
			return false;
		}
	}


$(function() {
    $('#fechaInicio').datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        maxDate: moment().format("YYYY/MM/DD")
    });

    $('#fechaFin').datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        maxDate: moment().format("YYYY/MM/DD")
    });

    
    var myTable = $('#camasUsadas-table').DataTable();
	myTable.clear();

    var tabla2 = $('#camasPorcentaje-table').DataTable();
	tabla2.clear();

    

    $("#estEstadiaPaciente").bootstrapValidator({
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {
			
			inicio: {
				validators:{
					notEmpty: {
						message: 'La fecha de inicio es obligatoria'
					}
				}
			},
            fin: {
                validators:{
					notEmpty: {
						message: 'La fecha de fin es obligatoria'
					},
					callback: {
						callback: function(value, validator, $field){
							var comparar = compararFechas();
                            console.log("comparar", comparar);
							if(comparar){
								return true
							}else{
								return { valid: false, message: "La fecha no puede ser menor a la inicial" };
							}
                        }
					}
				}
            }
			
		}
	}).on('status.field.bv', function(e, data) {
        //$("#estEstadiaPaciente input[type='submit']").prop("disabled", false);
    }).on("success.form.bv", function(evt){
        //$("#estEstadiaPaciente input[type='submit']").prop("disabled", false);
        evt.preventDefault(evt);
        var $form = $(evt.target);
        console.log("hola");
        $.ajax({
            url: 'estEstadiaCamasPaciente/datos',
            headers: {        
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            dataType: "json",
            data: $form .serialize(),
            success: function(data){
                console.log("data ", data);
                myTable.clear();
                var arreglo = data[2];
                var arreglo2 = data[3];
                console.log("arreglo", arreglo);
                console.log("arreglo2", arreglo2);
                var tablaCamasUsadas = $("#camasUsadas-table").dataTable();
                var tablaPorcentaje  = $("#camasPorcentaje-table").dataTable();
               
                /* console.log("estadia ", data[0][0].estadia); */
                var numero = parseFloat(data[0][0].estadia);
                numero = numero.toFixed(2);
                console.log("estadia ", numero);

                if (numero != null) {
                    $("#estada").val(numero);
                }else{
                    console.log("nada");
                    $("#estada").val("No hay datos necesarios");
                }

                if (arreglo.length != 0) {
                    console.log("algo");
                    tablaCamasUsadas.fnAddData(arreglo);
                }
                if (arreglo2.length != 0) {
                    console.log("algo2");
                    tablaPorcentaje.fnAddData(arreglo2);
                }

            },
            error: function(error){
                console.log(error);
            }
        });
    });

    $('#fechaInicio').on('dp.change dp.show', function (e) {
        $('#estEstadiaPaciente').bootstrapValidator('revalidateField', 'inicio');
    });

    $('#fechaFin').on('dp.change dp.show', function (e) {
        $('#estEstadiaPaciente').bootstrapValidator('revalidateField', 'fin');
    });

    
    
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

        #consultar{
            margin-top: 20px;
        }
    </style>


    {{ Form::open(array('method' => 'post', 'class' => '', 'id' => 'estEstadiaPaciente')) }}
	<fieldset>
        <legend>Gráfico estadístico de estadía pacientes</legend>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Fecha inicio:</label>
                    <input type="text" class="form-control " id="fechaInicio" name="inicio"></input>
                </div>
            </div>
    
            <div class="col-md-2">
                <div class="form-group">
                    <label>Fecha fin:</label>
                    <input type="text" class="form-control " id="fechaFin" name="fin"></input>
                </div>            
            </div>
    
    
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary" id="consultar">Consultar</button>
            </div>
        </div>
        

        <div class="row">
            <div class="col-md-4 " id ="resultados">
                <label>Promedio días de estada:</label>
                <input type="text" class=" form-control" disabled id ="estada">
            
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4 " id ="resultados">
                <label>Promedio de camas disponibles:</label>
                <input type="text" class=" form-control" disabled id ="disponible">
            
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <label>Índice de rotación o giro de camas:</label>
                <br>
                <table id='camasUsadas-table' class='display responsive ' style="width:100%">
                    <thead>
                        <tr>
                            <th>Cama</th>
                            <th>Sala</th>
                            <th width="75">Veces usadas</th>
                        </tr>
    
                    </thead>
                    <tbody>
                        
    
                    </tbody>
                </table>
            </div>
            
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <label>Intervalo de sustitución:</label>
                <br>
                <table id='camasPorcentaje-table' class='display responsive ' style="width:100%">
                    <thead>
                        <tr>
                            <th>Cama</th>
                            <th>Sala</th>
                            <th>Unidad</th>
                            <th width="75">% tiempo sin usar</th>
                        </tr>
    
                    </thead>
                    <tbody>
                        
    
                    </tbody>
                </table>
            </div>
            
        </div>
        <br>
        
    </fieldset>

    {{ Form::close() }}

@stop

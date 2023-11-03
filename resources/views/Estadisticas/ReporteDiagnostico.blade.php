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

   


    //Mauricio
	$(document).on("input","input[name='diagnosticos']",function(){
		var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos']");
		
		if($cie10.val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		}
	});
	
	//Mauricio//
 	var datos_cie10 = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie101',
			wildcard: '%QUERY',
			filter: function(response) {
			    return response;
			}
		},
		limit: 6
	});

	datos_cie10.initialize();

	$('.diagnostico_cie101 .typeahead').typeahead(null, {
	  name: 'best-pictures',
	  display: 'nombre_categoria',
	  source: datos_cie10.ttAdapter(),
	  templates: {
		empty: [
		  '<div class="empty-message">',
			'No hay resultados',
		  '</div>'
		].join('\n'),
		suggestion: function(data){
			var nombres = data;
			//console.log(data);
			return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span></div>"
		},
		header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span></div><br>"
	  }
	}).on('typeahead:selected', function(event, selection){
			//$("#texto_cie10").val(selection.nombre_cie10);
		$("[name='hidden_diagnosticos']").val(selection.id_categoria);
	}).on('typeahead:close', function(ev, suggestion) {//Mauricio
        console.log('Close typeahead: ' + suggestion);
	  	var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos']");
		console.log("padre:",$(this).parents(".diagnostico_cie101"));
		console.log("cie10:",$cie10.val(),!$cie10.val());
		console.log("this:",$(this).val(),!!$(this).val());
		if(!$cie10.val()&&$(this).val())
		{
			$(this).val("");
			$cie10.val("");
			$(this).trigger('input');
		}
	});
    //Mauricio//

    $("#estDiagnostico").bootstrapValidator({
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
            },
            diagnosticos: {
                validators:{
					notEmpty: {
						message: 'El diagnóstico es necesario'
					},
					callback: {
						callback: function(value, validator, $field){
							return true;
                        }
					}
				}
            }
			
		}
	}).on('status.field.bv', function(e, data) {
        $("#estDiagnostico input[type='submit']").prop("disabled", false);
    }).on("success.form.bv", function(evt){
        $("#estDiagnostico input[type='submit']").prop("disabled", false);
        evt.preventDefault(evt);
        var $form = $(evt.target);
        $.ajax({
            url: 'estadiaDiagnostico/datos',
            headers: {        
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            dataType: "json",
            data: $form .serialize(),
            success: function(data){
                console.log("data ", data);


                Highcharts.chart('container', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Promedio de estadía por diagnóstico.'
                    },
                    subtitle: {
                        text: 'Fuente: SIGICAM'
                    },
                    xAxis: {
                        categories: data.cie_10
                    },
                    yAxis: [{
                        min: 0,
                        title: {
                            text: 'Días'
                        }
                    }],
                    legend: {
                        shadow: false
                    },
                    tooltip: {
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            grouping: false,
                            shadow: false,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Promedio general',
                        color: 'rgba(165,170,217,1)',
                        data: data.estadia_promedio,
                        pointPadding: 0.3,
                        pointPlacement: -0.2
                    }, {
                        name: 'Promedio específico',
                        color: 'rgba(126,86,134,.9)',
                        data: data.promedio,
                        pointPadding: 0.4,
                        pointPlacement: -0.2
                    }]
                });
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    $('#fechaInicio').on('dp.change dp.show', function (e) {
        $('#estDiagnostico').bootstrapValidator('revalidateField', 'inicio');
    });

    $('#fechaFin').on('dp.change dp.show', function (e) {
        $('#estDiagnostico').bootstrapValidator('revalidateField', 'fin');
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


    {{ Form::open(array('method' => 'post', 'class' => '', 'id' => 'estDiagnostico')) }}
	<fieldset>
        <legend>Gráfico estadísticas por diagnóstico</legend>
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

        <div class="col-md-6">
            <div class="col-sm-10 diagnostico_cie101 pr0">
                <div class="form-group">
                    <label>Diagnóstico CIE10:</label>
                    <input type="text" name="diagnosticos" class='form-control typeahead' />
                    <input type="hidden" name="hidden_diagnosticos">
                </div>                    
            </div>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary" id="consultar">Consultar</button>
        </div>

        <div class="col-md-12">
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>

    </fieldset>

    {{ Form::close() }}

@stop

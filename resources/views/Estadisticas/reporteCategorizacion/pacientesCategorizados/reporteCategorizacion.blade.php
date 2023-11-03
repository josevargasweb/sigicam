<script>
    
    $(function() {
		var vez2 = 1;// variable
        var chart2 = Highcharts.chart('graficoCategorizacion', {
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: 'Pacientes categorizados'
			},
			subtitle: {
				text: 'Fuente: SIGICAM, incluye todos los servicios.'
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
				/* layout: 'vertical',
				verticalAlign: 'bottom', */
				alignColumns: true,
				floating: false,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
			},
			series: [

			]
		});


		function mostrarCategorizados(){
            if (vez2 == 1) {
				chart2.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');
				vez2++;
				var categorizacionPC = $("#categorizacionPC").val();
                $.ajax({
					url: "{{asset('graficoCategorizados')}}",
					type: "get",
					dataType: "json",
					data: {'anno': 0, 'mes': 0, 'categorizacion': categorizacionPC},
					success: function(data){
						chart2.hideLoading();
						chart2.update({
							subtitle: {
								text: 'Fuente: SIGICAM en mes de '+data.fecha+', incluye todos los servicios.'
							}
						});
						$.each(data.cantidad, function( index, value ) {
							chart2.addSeries({
								data: value,
								name: index
							});
						});
						
					},
					error: function(error){
						console.log("error: ", error)
					}
				});
            }
            
		}
		
		$('#PC2').on('click', function (e) {
			mostrarCategorizados();
		});
		

        $('#fecha_graficoCat').on('change', function (e) {
			$('#pacienteCategorizacion').bootstrapValidator('revalidateField', 'fecha_graficoCat');
		});
        
        $("#pacienteCategorizacion").bootstrapValidator({
            fields:{
                fecha_graficoCat: {
                    validators:{
                        notEmpty: {
                            message: 'Debe especificar la fecha'
                        }
                    }
                },
                categorizacionUI: {
                    validators:{
                        notEmpty: {
                            message: 'Debe especificar la categorización'
                        },
                        callback: {
                            callback: function(value, validator, $field){
                                console.log(value);
                                console.log(validator);
                                console.log($field);
                                return { valid: false, message: "La fecha no puede ser menor a la inicial" };
                            }
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e){
			//url("")
			chart2.showLoading('<img src="{{URL::to('/')}}/images/default.gif">');

            e.preventDefault();
            var form = $(e.target);

            var mes = $("#fecha_graficoCat").datepicker('getDate').getMonth()+1;
            var anno = $("#fecha_graficoCat").datepicker('getDate').getFullYear();
			var categorizacionPC = $("#categorizacionPC").val();
			
            $.ajax({
                url: "{{asset('graficoCategorizados')}}",
                type: "get",
                dataType: "json",
                data: {'anno': anno, 'mes': mes, 'categorizacion': categorizacionPC},
                success: function(data){
					chart2.hideLoading();
					chart2.update({
						subtitle: {
							text: 'Fuente: SIGICAM en mes de '+data.fecha+', incluye todos los servicios.'
						}
					});
					//limpiar
					while(chart2.series.length>0){
						chart2.series[0].remove(false) //false = don't redraw
					}
					chart2.redraw()

					var arreglo = new Array();					
					$.each(data.cantidad, function( index, value ) {
						//console.log( index + ": " + value );
						/* var objeto =  new Object();
						objeto.data = value;
						objeto.name = index; */
						/* objeto.dataLabels: {
						enabled: true,
						format: "{point.y}"
					}, */
						/* console.log(objeto); */
						chart2.addSeries({
							data: value,
							name: index
						});
						/* arreglo.push(objeto); */
					});
					/* console.log(arreglo);
                    chart2.addSeries({
                        series: arreglo
					}); */
					
                },
                error: function(error){
                    console.log("error: ", error)
                }
            });
        });

    });
        
    
			
    
</script>


<fieldset>
    <legend id="pacientesD2yD34">Pacientes categorizados</legend>

    <fieldset>

		{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'pacienteCategorizacion')) }}
        <div class="col-md-12">
            <div class="col-md-3 form-group">
                <label>Seleccione fecha</label>
                <input name="fecha_graficoCat" type="text" id="fecha_graficoCat" class="form-control fecha-grafico" value = "{{\Carbon\Carbon::now()->format('m-Y') }}">
            </div>
            
			<div class="col-md-3 form-group">
				<label>Seleccione categorización</label> 
				{{ Form::select('categorizacionPC[]', ["A1" => "A1", "A2" => "A2","A3" => "A3","B1" => "B1", "B2" => "B2","B3" => "B3","C1" => "C1", "C2" => "C2","C3" => "C3","D1" => "D1", "D2" => "D2","D3" => "D3"], ["C1","C2","C3","D1"], array('id' => 'categorizacionPC', 'class' => 'selectpicker', 'multiple', 'title' => 'Ninguno','data-actions-box' => 'true')) }}               
            </div>

				
            <div class="col-md-3">
                <button id="btn-pac-categ" class="btn btn-primary" style="margin-top: 20px;">Generar gráfico</button>
            </div>
        </div>
        <div class="col-md-12">
            <div id="graficoCategorizacion" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>
        {{ Form::close() }}
        
    </fieldset>


</fieldset>
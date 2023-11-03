@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("script")

<script>
    $(function() {
    	$("#estadistica").collapse();
        
		var chart = Highcharts.chart('container', {
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: 'Índice ocupacional'
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
					text: '% camas',
					style: {
						color: Highcharts.getOptions().colors[1]
					}
				},
				labels: {
					format: '{value} %',
					style: {
						color: Highcharts.getOptions().colors[1]
					}
				}
			}],
			tooltip: {
				shared: true
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
				name: ' % Camas ocupadas',
				type: 'column',
				yAxis: 0,
				data: [0,0,0],
				tooltip: {
					valueSuffix: ' %'
				}

			}]
		});

		$.ajax({
			url: "{{asset('estadisticas/indiceOcupacional/graficoOcupacional')}}",
			type: "get",
			dataType: "json",
			data: {'anno': 0, 'mes': 0},
			success: function(data){
				console.log("data: ", data);
				chart.update({
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

		$(".fecha-grafico").datepicker({
			startView: 'months',
			minViewMode: "months",
    		autoclose: true,
    		language: "es",
    		format: "mm-yyyy",
    		//todayHighlight: true,
    		endDate: "+0d"
    	});

		$("#btn-grafico").on("click", function(){
			var valor = $("#fecha-grafico").val();
			if(valor == ""){
					swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha"
					});

			}else{
				var mes = $("#fecha-grafico").datepicker('getDate').getMonth()+1;
				var anno = $("#fecha-grafico").datepicker('getDate').getFullYear();
				$.ajax({
					url: "{{asset('estadisticas/indiceOcupacional/graficoOcupacional')}}",
					type: "get",
					dataType: "json",
					data: {'anno': anno, 'mes': mes},
					success: function(data){
						console.log("data: ", data);
						chart.update({
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
	
	});
</script>

@stop

@section("section")
	<fieldset>
		<div class="col-sm-12">
			<div class="col-sm-12">
				<label>Seleccione fecha</label>
			</div>
			<div class="col-sm-2 form-group">
				<input type="text" id="fecha-grafico" class="form-control fecha-grafico">
			</div>
			<div class="col-sm-2 form-group">
				<button id="btn-grafico" class="btn btn-primary">Generar gráfico</button>
			</div>
		</div>
		<div class="col-md-12">
			<label>Índice ocupacional o porcentaje de ocupación:</label>
			<div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>

		<div class="col-md-12">
			<label>Promedio de camas disponibles:</label>
		</div>
	</fieldset>
@stop

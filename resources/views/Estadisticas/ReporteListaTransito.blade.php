@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte lista tránsito</a></li>
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
				console.log("data: ", data);
				chart.update({
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
					url: "{{asset('estadisticas/listaTransito/datos')}}",
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
			<div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>
	</fieldset>
@stop

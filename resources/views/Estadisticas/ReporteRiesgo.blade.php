@extends("Templates/template")

@section("titulo")
Reporte de riesgos
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de riesgos</a></li>
@stop

@section("script")

<script>
	var getDatos = function(fecha_desde, fecha, estab){
		console.log("obteniendo datos de alta");
		estab = typeof estab !== 'undefined' ? estab : '';
        //if (estab != '') estab = "/" + estab;
        $.ajax({
            url: '{{URL::route("estRiesgo")}}/datos',
            data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
            dataType: "json",
            type: "get",
            success: function(response){

				var addData = [];

				for(i=0;i<response.especialidades.length;i++){
					//console.log(response.res[i]);
					addData.push([response.especialidades[i].alias, response.especialidades[i].count]);
				
				}

            	var tabla=$("#tablaAlta").dataTable();


                tabla.fnClearTable();
                if(addData.length > 0)
                tabla.fnAddData(addData);   

            },
            error: function(error){
            	console.log("error:"+JSON.stringify(error));
                console.log(error);
            }
        });
    }
    $(function() {
    	$("#estadistica").collapse();

    	$(".fecha-sel").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
    	});

        $('#tablaAlta').dataTable({ 
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 15,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

    	$("#establecimiento").on("change", function(){
    		var unidad=$(this).val();
    		if(unidad == 0){
    			$("#unidades").prop("disabled", true).hide();
    		}
    		else{
    			$("#unidades").prop("disabled", false).show();
    			$.ajax({
    				url: "getUnidades",
    				type: "get",
    				dataType: "json",
    				data: {unidad: unidad},
    				success: function(data){
    					$("#unidades").empty();
    					for(var i=0; i < data.length; i++){
    						var option="<option value='"+data[i].id+"'>"+data[i].alias+"</option>";
    						$("#unidades").append(option);
    					}
    					if(data.length == 0) $("#unidades").append("<option value='0'>Todos</option>");
    				},
    				error: function(error){
    					console.log(error)
    				}
    			});
    		}
    	});

		$("#updateEstadistica").submit(function(ev){
			ev.preventDefault();
			getDatos($("#fecha-inicio").val(), $("#fecha").val(), $("#establecimiento").val());
			return false;
		});

		$("#updatePacientesD2").submit(function(ev){
			ev.preventDefault();
			console.log("hola");
			var tabla=$("#tablaDocDer").dataTable();
            tabla.fnClearTable();
				fecha = $("#fechaD2D3").val();
				$.ajax({
				url: "{{asset('estadisticas/pacientesD2D3Datos')}}",
				data: {"fecha":fecha},
				dataType: "json",
				type: "get",
				success: function(response){

					var addData = [];

					/*for(i=0;i<response.especialidades.length;i++){
						//console.log(response.res[i]);
						addData.push([response.especialidades[i].alias, response.especialidades[i].count]);
					
					}*/


					tabla.fnClearTable();
					//console.log(response)
					if(response.aaData.length > 0)
					tabla.fnAddData(response.aaData);   

				},
				error: function(error){
					console.log("error:"+JSON.stringify(error));
					console.log(error);
				}
				});

		});
		getDatos( '{{  \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y") }}','{{  \Carbon\Carbon::now()->format("d-m-Y") }}' );
        
		var chart = Highcharts.chart('container', {
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: 'Pacientes categorizados D2 y D3'
			},
			subtitle: {
				text: 'Fuente: SIGICAM, no incluye los servicios de obtetricia, pensionado y salud mental'
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
					text: 'Pacientes D2 + D3',
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
			series: [
			{
				name: 'Número pacientes',
				type: 'spline',
				yAxis: 0,
				data: [0,0,0],
				tooltip: {
					valueSuffix: ''
				},
				dataLabels: {
					enabled: true,
					format: "{point.y}"
				},
				enableMouseTracking: true,

			},
			{
				name: ' % Pacientes',
				type: 'column',
				yAxis: 0,
				data: [1,5,9],
				tooltip: {
					valueSuffix: ' %'
				},
				color: '#1E9966',
				dataLabels: {
					enabled: true,
					format: "{point.y}%"
				},
				enableMouseTracking: true,


			},
			{
				name: 'Referencia',
				type: 'spline',
				yAxis: 0,
				data: [15,15,15],
				tooltip: {
					valueSuffix: ' %'
				}

			}]
		});

		$.ajax({
			url: "{{asset('graficoCat')}}",
			type: "get",
			dataType: "json",
			data: {'anno': 0, 'mes': 0, 'establecimiento': 8},
			success: function(data){
				console.log("dataa: ", data);
				chart.update({
					series: [
					{
						data: data.cantidad
					},
					{
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
				var establecimiento = $("#establecimiento").val();
				$.ajax({
					url: "{{asset('graficoCat')}}",
					type: "get",
					dataType: "json",
					data: {'anno': anno, 'mes': mes, 'establecimiento': establecimiento},
					success: function(data){
						console.log("data: ", data);
						chart.update({
							series: [
							{
								data: data.cantidad
							},
							{
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


		var fechaExport = new Date().toJSON().slice(0,10);
		table = $('#tablaDocDer').dataTable({ 
			dom: 'Bfrtip',
			buttons: [
        		{
					extend: 'excelHtml5',
					messageTop: 'Pacientes D2 y D3 ('+fechaExport+')',
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7,8,9,10,11,12]
					} ,
					text: 'Exportar',
					className: 'btn btn-default',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						var clRow = $('row', sheet);
						//$('row c', sheet).attr( 's', '25' );  //bordes
						$('row:first c', sheet).attr( 's', '67' ); //color verde, letra blanca, centrado
						$('row', sheet).attr('ht',15);
						$('row:first', sheet).attr( 'ht', 50 ); //ancho columna
						$('row:eq(1) c', sheet).attr('s','67'); //color verde, letra blanca, centrado
					}
				}
    		],
			"aaSorting": [[0, "asc"]],
			"iDisplayLength": 10,
			"bJQueryUI": true,
			"ajax": {
					"url": "{{asset('estadisticas/pacientesD2D3Datos')}}",
					type: "get",
					dataType: "json",
					data: {'establecimiento': 8},
				},
			"oLanguage": {
				"sUrl": "{{URL::to('/')}}/js/spanish.txt"
			}
		});
		
		$("#establecimiento").on('change', function(){
			$.ajax({
				url: "{{asset('estadisticas/pacientesD2D3Datos')}}",
				type: "get",
				dataType: "json",
				data: {'establecimiento': $(this).val()},
				success: function(data){
					console.log("data derivacion: ", data);
                    table.fnClearTable();
                    if(data.aaData.length > 0){
                        table.fnAddData(data.aaData);
                    }
				},
				error: function(error){
					console.log("error:"+JSON.stringify(error));
					console.log(error);
				}
			});
		});
	
	});
</script>

@stop

@section("section")
	<fieldset>
		<div class="col-sm-12">
			<div class="col-sm-2 form-group">
				<label>Seleccione fecha</label>
				<input type="text" id="fecha-grafico" class="form-control fecha-grafico">
			</div>
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS)
				<div class="col-sm-4 form-group">
					<label>Establecimiento</label>
					{{ Form::select('establecimiento', $establecimiento, 8, array('id' => 'establecimiento', 'class' => 'form-control')) }}
				</div>
			@endif
			<div class="col-sm-2 form-group">
				<label>&nbsp;&nbsp;</label>
				<button id="btn-grafico" class="btn btn-primary">Generar gráfico</button>
			</div>
		</div>
		<div class="col-md-12">
			<div id="container" style="min-width: 310px; height: 400px; margin: 40px auto"></div>
		</div>
	</fieldset>

	<br>
	<legend>Pacientes D2 y D3 actualmente</legend>

	<div class="row">
			{{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updatePacientesD2', 'style' => 'padding-left: 15px;')) }}
            <div class="form-group">
                {{Form::text('fechaD2D3', $fecha, array('id' => 'fechaD2D3', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
            </div>

			{{--
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
				<div class="form-group">
					{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
				</div>
			@endif
			--}}
						<!--<div class="form-group">
			{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
		</div>-->
				<div class="form-group">
					{{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }}
				</div>
				{{ Form::close() }}
		</div>



	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
			<table id="tablaDocDer" class="table table-striped table-bordered table-hover" style = "overflow-x: scroll;">
				<tfoot>
					<tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Rut</th>
						<th>Comuna</th>
						<th>Diagnóstico</th>
						<!-- <th>Exámenes pendientes</th> -->
						<th>Cama</th>
						<th>Sala</th>
						<th>Servicio</th>
						<th>Área funcional</th>
						<th>Comentario</th>
						<th>Fecha hospitalización</th>
						<th>Tiempo estada</th>
						<th>Categorización</th>
					</tr>
				</tfoot>
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Rut</th>
						<th>Comuna</th>
						<th>Diagnóstico</th>
						<!-- <th>Exámenes pendientes</th> -->
						<th>Cama</th>
						<th>Sala</th>
						<th>Servicio</th>
						<th>Área funcional</th>
						<th>Comentario</th>
						<th>Fecha hospitalización</th>
						<th>Tiempo estada</th>
						<th>Categorización</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
			</table>
			</div>
		</div>
	</div>

	<br><br><br><br>

	<fieldset>
		<legend>Riesgos</legend>

		<div class="row">
			{{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }}
            <div class="form-group">
                {{Form::text('fecha-inicio', \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y"), array('id' => 'fecha-inicio', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
            </div>
            <div class="form-group">
				{{Form::text('fecha', $fecha, array('id' => 'fecha', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha'))}}
			</div>
			@if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
				<div class="form-group">
					{{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
				</div>
			@endif
						<!--<div class="form-group">
			{{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
		</div>-->
				<div class="form-group">
					{{Form::submit('Actualizar', array('id' => 'btnUpdate', 'class' => 'btn btn-primary')) }}
				</div>
				{{ Form::close() }}
		</div>
		<br><br>
		<div id="contenido"></div>
		
	</fieldset>

    <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table id="tablaAlta" class="table table-striped table-bordered table-hover">
                        <tfoot>
                            <tr>
                               <th>Riesgo</th>
                                <th>Total pacientes</th>
                            </tr>
                        </tfoot>
                        <thead>
                            <tr>
                               
                              <th>Riesgo</th> 
                                <th>Total pacientes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>


<br><br>
@stop

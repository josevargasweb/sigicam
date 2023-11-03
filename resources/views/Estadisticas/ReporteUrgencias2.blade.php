@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de urgencias</a></li>
@stop

@section("script")

<script>

	function diagnostico(data, type, dataToSet) {
		return mayusculaPrimeraLetra(data.diagnostico);
	}

	function mayusculaPrimeraLetra(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	function nombrePacienteCompleto(data, type, dataToSet) {
		return (data.nombre + " " + data.apellido_paterno + " " + data.apellido_materno).toLowerCase() ;
	}

	$(function () {
		$('#tablaPacientesUrgencias').dataTable({
			"aaSorting": [4, "desc"],
			ajax:  '{{ URL::to('/estadisticas/listaPacientesUrgencia') }}',
			columnDefs: [
				{
					targets: ["nombre-completo"],
					className: 'dt-body-nombre-completo'
				}

			],
			"columns": [
				{ "data": "id_cama" },
				{ "data": "sala" },
				{ "data": nombrePacienteCompleto },
				{ "data": diagnostico },
				{ "data": "tiempo_espera" }
			],
			"iDisplayLength": 15,
			"bJQueryUI": true,
			"oLanguage": { "sUrl": "{{URL::to('/')}}/js/spanish.txt" },
		});
	});


    $(function() {
    	$("#estadistica").collapse();


        //////////////////
        //REPORTE RIESGO//
        //////////////////

        $(".fecha-sel").datepicker({
    		autoclose: true,
    		language: "es",
    		format: "dd-mm-yyyy",
    		todayHighlight: true,
    		endDate: "+0d"
    	});

		$("#btnSalidaUrgencia").click(function(){
			fechaSalidaurgencia = $("#fechaSalidaUrgencia").val();
			if(fechaSalidaurgencia == ""){
				swalWarning.fire({
				title: 'Información',
				text:"Debe seleccionar una fecha"
				});
			}else{
				window.location.href = "{{url('estadisticas/salidaUrgencias')}}"+"/"+fechaSalidaurgencia;
			}
		});

		$(".fecha-mes").datepicker({
    		startView: 'months',
			minViewMode: "months",
			autoclose: true,
			language: "es",
			format: "mm-yyyy",
			//todayHighlight: true,
			endDate: "+0d"
    	});

		$("#btnEstadiaUrgencia").on("click", function(){
                var valor = $("#fechaEstadiaUrgencia").val();
                if(valor == ""){
                   swalWarning.fire({
					title: 'Información',
					text:"Debe seleccionar una fecha"
					});
                }else{
					var mes = $("#fechaEstadiaUrgencia").datepicker('getDate').getMonth()+1;
                    var anno = $("#fechaEstadiaUrgencia").datepicker('getDate').getFullYear();

					window.location.href = "{{url('estadisticas/estadiaUrgencias')}}"+"/"+mes+"/"+anno;
                }
            });


	});
</script>

@stop

@section("section")
	{{ HTML::style('css/navegadortab.css') }}
<div class="container" style="width: 100%;">
	    <fieldset id="ocultar-cat">
			@if(Session::get('usuario')->tipo == 'admin' || Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP' || Session::get('usuario')->tipo == 'director' || Session::get('usuario')->tipo == 'medico_jefe_servicio' || Session::get('usuario')->tipo == 'master' || Session::get('usuario')->tipo == 'master_ss')
			<br>
			<legend>Reporte de urgencias</legend>

			<div class="container" style="width: 100%;">
				<ul class="nav nav-pills primerNav">
					<li class="nav active"><a href="#InformeDiario" data-toggle="tab">Informe Diario</a></li>
					<li class="nav"><a href="#SalidasUrgencia" data-toggle="tab">Salidas de Urgencia</a></li>
					<li class="nav"><a href="#InformeMensual" data-toggle="tab">Informe Mensual</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane fade in active" style="padding-top:10px;" id="InformeDiario">
						<fieldset>
							{{ HTML::link("estadisticas/exportarpacientesUrgencias", 'Pacientes Urgencia' , ['class' => 'btn btn-default']) }}
							{{ HTML::link("estadisticas/exportarpacientesUrgenciasPdf", 'Pdf Pacientes Urgencia', ['class' => 'btn btn-danger']) }}
							<div>
								<table class="table table-bordered categorizacion tabla-sigicam">
									<thead>
									<tr>
										<th>Sin categorización</th>
										{{-- <th>No habilitado para categorizar</th> --}}
										<th>A1</th>
										<th>A2</th>
										<th>A3</th>
										<th>B1</th>
										<th>B2</th>
										<th>B3</th>
										<th>C1</th>
										<th>C2</th>
										<th>C3</th>
										<th>D1</th>
										<th>D2</th>
										<th>D3</th>
										<th>TOTAL</th>

									</tr>
									</thead>
									<tbody>
									<tr>
										<th>{{$categorizacion[13]}}</th>
										{{-- <th>{{$categorizacion[14]}}</th> --}}
										<th>{{$categorizacion[0]}}</th>
										<th>{{$categorizacion[1]}}</th>
										<th>{{$categorizacion[2]}}</th>
										<th>{{$categorizacion[3]}}</th>
										<th>{{$categorizacion[4]}}</th>
										<th>{{$categorizacion[5]}}</th>
										<th>{{$categorizacion[6]}}</th>
										<th>{{$categorizacion[7]}}</th>
										<th>{{$categorizacion[8]}}</th>
										<th>{{$categorizacion[9]}}</th>
										<th>{{$categorizacion[10]}}</th>
										<th>{{$categorizacion[11]}}</th>
										<th>{{$categorizacion[12]}}</th>
									</tr>
									</tbody>
								</table>

							</div>

							<div class="table-responsive">
								<table id="tablaPacientesUrgencias" class="table table-hover tabla-sigicam">
									<thead>
									<tr>
										<th>Sala</th>
										<th class='rut'>Cama</th>
										<th class="nombre-completo">Nombre Completo</th>
										<th>Diagnóstico</th>
										<th>Tiempo Hospitalizacion (Hrs)</th>
									</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<br>
						</fieldset>
					</div>
					<div class="tab-pane fade" style="padding-top:10px;" id="SalidasUrgencia">
						<fieldset>
							<legend>Archivos</legend>
								<div class="col-md-12">

									<div class="col-md-3" style="text-align:center;">
										<label for="">Salidas de urgencia</label>
										<label for="">(Ingrese día)</label>
										{{Form::text('fechaSalidaUrgencia', null, array('id' => 'fechaSalidaUrgencia', 'class' => 'form-control fecha-sel'))}}
										<br>
										<button class="btn btn-danger" type="button" id="btnSalidaUrgencia">Generar Pdf</button>
									</div>

									<div class="col-md-3">

									</div>
									<div class="col-md-3">

									</div>
									<div class="col-md-3">

									</div>
								</div>
									<br><br><br><br>
						</fieldset>
					</div>
					<div class="tab-pane fade" style="padding-top:10px;" id="InformeMensual">
						<fieldset>

							<legend>Archivos</legend>
							<div class="col-md-3" style="text-align:center;">
								<label for="">Reporte mensual</label>
								<label for="">(Ingrese mes)</label>
								{{Form::text('fechaEstadiaUrgencia', null, array('id' => 'fechaEstadiaUrgencia', 'class' => 'form-control fecha-mes'))}}
								<br>
								<button class="btn btn-danger" type="button" id="btnEstadiaUrgencia">Generar Pdf</button>
							</div>
						</fieldset>

					</div>
				</div>
			</div>
			@endif
	</fieldset>
</div>
@stop


@section("estilo-tabla")
	{{ HTML::style('css/sigicam/tablas.css') }}
@stop

@extends("Templates/template")

@section("titulo")
Ver Contingecia
@stop


@section("miga")
<li><a href="#">Contingencia</a></li>
<li><a href="#" onclick='location.reload()'>Ver Contingecia</a></li>
@stop

@section("script")

<script>

	$(function(){


		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY H:m:s"
		});

		$("#contingenciaMenu").collapse();

		$("#form-contingencia-real").bootstrapValidator({
			fields: {
				@foreach($disponibilidad as $d)
				"real-{{$d->establecimiento}}": {
					validators: {
						greaterThan: {
							inclusive: true,
							value: 0,
							message: 'El valor debe ser cero o positivo'
						}
					}
				},
				@endforeach
                }
		}).on('status.field.bv', function(e, data) {
			data.bv.disableSubmitButtons(false);
		}).on("success.form.bv", function(evt){
			evt.preventDefault(evt);
			var $form = $(evt.target);
			$.ajax({
				url: "{{URL::to('/')}}/contingencia/actualizarContingencia",

				headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				type: "post",
				dataType: "json",
				data: $form .serialize(),
				success: function(data){
					$("#btn-actualizar").prop("disabled", false);
					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						location . reload();
							}, 2000)
						},
						});


					}
					if(data.error){
						swalError.fire({
						title: 'Error',
						text:data.error
						});
						console.log(data.error);
					}
				},
				error: function(error){
					console.log(error);
				}
			});
		});
	});
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("css")
<style type="text/css">
	td{
		text-align: center;
	}
	#hostConting td input{
		width: 100%;
	}
	#hostConting td select{
		width: 100%;
	}
	#hostConting td textarea{
		width: 100%;
	}
</style>
@stop

@section("section")
	<?php $actualizables = false; ?>
<fieldset>
	<legend>DECLARACIÓN DE CONTINGENCIA – HOSPITALES DE ALTA COMPLEJIDAD</legend>
	<div class="jumbotron">
		<p> HOSPITAL QUE LO SOLICITA: {{$establecimiento}}	  FECHA: {{ date("d/m/Y H:i:s", strtotime($solicitud->fecha)) }} </p>
		<p>  (Situación en que su hospital no cuenta con un número adecuado de camas para hospitalizar a la cantidad de pacientes en espera en Servicio de Urgencia que lo requieran).  </p>
		<p> <strong>IMPORTANTE:</strong> Antes de realizar el llamado a S.D.G.A. del Servicio de Salud para la activación de contingencia, recuerde que debe llamar a los hospitales de la red para determinar disponibilidad de cupos. </p>
		<br><br>
		<div class="form-horizontal" role="form">
		<form id="form-contingencia-real" method="post" action ="actualizarContingencia">
			
			<div class="table-responsive">
			<table id="hostConting" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>Hospital destino</th>
						<th>Nombre de la persona que entrega disponibilidad</th>
						<th>Cantidad de camas entregadas</th>
						<th>Cantidad real de camas entregadas</th>
						<th>Comentario</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($disponibilidad as $d)
					<tr>
						<td>
							{{App\Models\Establecimiento::getNombre($d->establecimiento)}}
						</td>
						<td>
							<input disabled type="text" class="form-control" value="{{$d->persona}}"/>
						</td>
						<td>
							<input disabled type="text" class="form-control" value="{{$d->n_camas}}"/>
						</td>
						<td class="form-group">
							@if(($d->n_camas_reales === null || trim($d->n_camas_reales) === '') && Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR
							&& Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO
							&& Session::get("usuario")->tipo !== TipoUsuario::MONITOREO_SSVQ
							&& Session::get("usuario")->tipo !== TipoUsuario::ADMINSS)
								<?php $actualizables = $actualizables || true;?>
								{{Form::number("real-{$d->establecimiento}", $d->n_camas_reales, array("id" => "real-{$d->establecimiento}", "class" => "form-control"))}}
							@else
								{{Form::number("real-{$d->establecimiento}", $d->n_camas_reales, array("id" => "real-{$d->establecimiento}", "class" => "form-control", "disabled" => "disabled"))}}
							@endif
						</td>
						<td>
							<textarea disabled class="form-control">{{$d->comentario}}</textarea>
						</td>
					</tr>
					@endforeach
			</tbody>
		</table>
		</div>
			
		<br><br>

		<input type="hidden" name="idSolicitud" value="{{$solicitud->id}}" />
		<div class="row form-group error">
			<label class="col-sm-4 control-label">ID UGCC</label>
			<div class="col-sm-8">
				<input disabled type="text" class="form-control" value = "{{trim($solicitud->idugcc,"{}")}}"/>
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-4 control-label">Nombre médico de turno que solicita activación: </label>
			<div class="col-sm-8">
				<input disabled type="text" class="form-control" value="{{$solicitud->solicitante}}" />
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-6 control-label">Cantidad de pacientes en espera en servicio de urgencia: </label>
			<div class="col-sm-2">
				<input disabled type="text" class="form-control" name="pacienteEspera" value="{{$solicitud->n_pacientes_espera}}"/>
			</div>
			<div class="col-sm-4">
				<textarea disabled name="comentarioEspera" class="form-control">{{$solicitud->comentario_espera}}</textarea>
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-6 control-label">Cantidad de pacientes con criterio de derivación a cama básica que requiere derivar a través de UGCC: </label>
			<div class="col-sm-2">
				<input disabled type="text" class="form-control" name="pacienteBasica" value="{{$solicitud->n_pacientes_basica}}"/>
			</div>
			<div class="col-sm-4">
				<textarea disabled name="comentarioBasica" class="form-control">{{$solicitud->comentario_basica}}</textarea>
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-6 control-label">Cantidad de pacientes con criterio de derivación a cama mayor complejidad a travéz de UGCC: </label>
			<div class="col-sm-2">
				<input disabled type="text" class="form-control" name="cantidadCompleja" value="{{$solicitud->n_pacientes_compleja}}"/>
			</div>
			<div class="col-sm-4">
				<textarea disabled name="comentarioCompleja" class="form-control">{{$solicitud->comentario_compleja}}</textarea>
			</div>
		</div>
		<div class="row form-group error">
			<label class="col-sm-4 control-label">Cupos otorgados UGCC: </label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="cupos" value="{{$solicitud->cupos}}"  />
			</div>
		</div>
		<div class="row form-group error">
			<label class="col-sm-4 control-label">Fecha solicitud: </label>
			<div class="col-sm-8">
			

			<!-- verifico si la fecha termina con 00:00 para saber si se edito o no -->
			<!--
				@if($solicitud->updated_at == $solicitud->created_at)
					<input type="text" class = 'form-control fecha-sel' name="fecha" value="{{Carbon\Carbon::parse($solicitud->fecha)->format('d/m/Y H:i:s')}}" id="fecha"  />
				@else
					<input type="text" class = 'form-control fecha-sel' name="fecha" value="{{Carbon\Carbon::parse($solicitud->fecha)->format('d/m/Y H:i:s')}}" id="fecha" disabled="" />
				@endif
			-->
				
			</div>
		</div>

		<div class="form-group">


			<div class="col-sm-10 col-xs-10">

			<!--
			@if($actualizables)
			{{Form::button('Actualizar', array("type" => "submit", "class" => "btn btn-primary", "id" => "btn-actualizar"))}}
			@endif
			-->

			{{Form::button('Actualizar', array("type" => "submit", "class" => "btn btn-primary", "id" => "btn-actualizar"))}}
		{{Form::close()}}


				{{ HTML::link(URL::route('contingencias'), 'Volver', ["class" => "btn btn-danger"])}}
				{{ HTML::link(URL::route("descargarPDF", $solicitud->id), 'Descargar informe', array('class' => 'btn btn-primary'))}} 
				{{ HTML::link(URL::route("verPDF", $solicitud->id), 'Ver informe', array('class' => 'btn btn-primary', 'target' => '_blank'))}}
			</div>
		</div>
	</div>
</div>



</fieldset>

@stop
@extends("Templates/template")

@section("titulo")
Generar Contingecia
@stop


@section("miga")
<li><a href="#">Contingencia</a></li>
<li><a href="#" onclick='location.reload()'>Declarar Contingencia</a></li>
@stop

@section("script")

<script>
	var addFila=function(){
		var idEstablecimiento=[];
		$("select[name='hospital[]']").each(function(){
			var value=$(this).val();
			if(value != null) idEstablecimiento.push($(this).val());
		});
		var data=getEstablecimiento(idEstablecimiento);
		completarSelectTemplate(data);
		var $template = $('#templateRow'),
		$clone    = $template
		.clone()
		.removeClass('hide')
		.removeAttr('id')
		.insertBefore($template),
		$option   = $clone.find('[name="hospital[]"]');
		$option.addClass("pick");
		$clone.find("select").prop("disabled", false);
		$clone.find("input").prop("disabled", false);
		$clone.find("textarea").prop("disabled", false);
		$('#formRegistrarContingencia').bootstrapValidator('addField', $clone.find('[name="hospital[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $clone.find('[name="nombre[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $clone.find('[name="cantidad[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $clone.find('[name="comentario[]"]'));
	}

	var addFila2=function(){
		var $template = $('#templateRow2'),
		$clone    = $template
		.clone()
		.removeClass('hide')
		.removeAttr('id')
		.insertBefore($template);
		$clone.find("input").prop("disabled", false);
		console.log($clone.find('[name="idugcc[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $clone.find('[name="idugcc[]"]'));

	}

	var deleteFila=function(button){
		var $row    = $(button).parent().parent();
		var hospital=$row.find('[name="hospital[]"] option:first');
		$('#formRegistrarContingencia').bootstrapValidator('addField', $row.find('[name="hospital[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $row.find('[name="nombre[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $row.find('[name="cantidad[]"]'));
		$('#formRegistrarContingencia').bootstrapValidator('addField', $row.find('[name="comentario[]"]'));
		$row.remove();
		$('select[name="hospital[]"]:not(:disabled)').each(function(){
			var option="<option value='"+hospital.val()+"'>"+hospital.text()+"</option>";
			var values = $.map($(this).find('option'), function(option) {
				return option.value;
			});

			if($.inArray(hospital.val(), values) == -1) $(this).append(option);
		});
	}

	var completarSelectTemplate=function(data){
		$("#templateRow select").empty();
		$.each(data, function(index, value){
			var option="<option value='"+index+"'>"+value+"</option>";
			$("#templateRow select").append(option);
		});
	}

	var getEstablecimiento=function(idEstablecimiento){
		var response=[];
		$.ajax({
			url: "getEstablecimientos",
			type: "post",
			data: {id: idEstablecimiento},
			dataType: "json",
			async: false,
			success: function(data){
				response=data;
			},
			error: function(error){
				console.log(error);
			}
		});
		return response;
	}

	var cambiarHospital=function(select){
		$(select).removeClass("pick").addClass("noPick");
		var idEstablecimiento=[];
		$(".pick").each(function(){
			var value=$(this).val();
			if(value != null) idEstablecimiento.push($(this).val());
		});
		var value=$(select).val();
		var data=getEstablecimiento([value]);
		$(".pick").each(function(){
			var select=$(this);
			valueSelect=select.val();
			select.empty();
			$.each(data, function(index, value){
				var option="<option value='"+index+"'>"+value+"</option>";
				select.append(option);
			});
			select.val(valueSelect);
		});
		$(select).removeClass("noPick").addClass("pick");
	}

	$(function(){
		$("#contingenciaMenu").collapse();

		$("#formRegistrarContingencia").bootstrapValidator({
			 excluded: ':disabled',
			 group: '.error',
 			 fields: {
 			 	"nombre[]": {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El nombre es obligatorio'
 			 			}
 			 		}
 			 	},
 			 	"cantidad[]": {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'La cantidad es obligatoria'
 			 			},
 			 			integer: {
 			 				message: "La cantidad debe ser un número"
 			 			},
 			 			greaterThan: {
 			 				inclusive: true,
 			 				value: 0,
 			 				message: 'La cantidad debe ser cero o mayor'
 			 			}
 			 		}
 			 	},
 			 	solicitante: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'El nombre del médico es obligatorio'
 			 			}
 			 		}
 			 	},
 			 	pacienteBasica: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'La cantidad es obligatoria'
 			 			},
 			 			integer: {
 			 				message: "La cantidad debe ser un número"
 			 			}
 			 		}
 			 	},
 			 	pacienteEspera: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'La cantidad es obligatoria'
 			 			},
 			 			integer: {
 			 				message: "La cantidad debe ser un número"
 			 			},
 			 			greaterThan: {
 			 				inclusive: true,
 			 				value: 0,
 			 				message: 'La cantidad debe ser cero o mayor'
 			 			}
 			 		}
 			 	},
				 pacienteOcupando: {
					 validators:{
						 notEmpty: {
							 message: 'La cantidad es obligatoria'
						 },
						 integer: {
							 message: "La cantidad debe ser un número"
						 },
						 greaterThan: {
							 inclusive: true,
							 value: 0,
							 message: 'La cantidad debe ser cero o mayor'
						 }
					 }
				 },
 			 	cantidadCompleja: {
 			 		validators:{
 			 			notEmpty: {
 			 				message: 'La cantidad es obligatoria'
 			 			},
 			 			integer: {
 			 				message: "La cantidad debe ser un número"
 			 			}
 			 		}
 			 	}
 			 }
		}).on('status.field.bv', function(e, data) {
 			data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
 			evt.preventDefault(evt);
 			var $form = $(evt.target);
 			$.ajax({
 				url: $form .prop("action"),
 				type: "post",
 				dataType: "json",
 				data: $form .serialize(),
 				success: function(data){
 					$("#btnContingencia").prop("disabled", false);
 					if(data.exito){
						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								location . assign("contingencias");
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


<fieldset>
	<legend>DECLARACIÓN DE CONTINGENCIA – HOSPITALES DE ALTA COMPLEJIDAD</legend>
	<div class="jumbotron">
		<p> HOSPITAL QUE LO SOLICITA: {{Session::get("nombreEstablecimiento")}}	  FECHA: {{date('d-m-Y H:i:s')}} </p>
		<p>  (Situación en que su hospital no cuenta con un número adecuado de camas para hospitalizar a la cantidad de pacientes en espera en Servicio de Urgencia que lo requieran).  </p>
		<p> <strong>IMPORTANTE:</strong> Antes de realizar el llamado a S.D.G.A. del Servicio de Salud para la activación de contingencia, recuerde que debe llamar a los hospitales de la red para determinar disponibilidad de cupos. </p>
		<br><br>
		{{ Form::open(array('url' => '/contingencia/registrarContingencia', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formRegistrarContingencia')) }}
		<div class="table-responsive">
		<table id="hostConting" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Hospital</th>
					<th>Nombre de la persona que entrega disponibilidad</th>
					<th>Cantidad de camas entregadas</th>
					<th>Comentario</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						{{ Form::select('hospital[]', $establecimientos, null, array('class' => "form-control pick", 'onchange' => 'cambiarHospital(this);')) }}
					</td>
					<td>
						<div class="error"><input type="text" name="nombre[]" class="form-control"/></div>
					</td>
					<td>
						<div class="error"><input type="text" name="cantidad[]" class="form-control" /></div>
					</td>
					<td>
						<div class="error"><textarea name="comentario[]" class="form-control"></textarea></div>
					</td>
					<td></td>
				</tr>
				<tr id="templateRow" class="hide">
					<td>
						<select name="hospital[]" class="form-control" disabled onchange="cambiarHospital(this);"></select>
					</td>
					<td>
						<div class="error"><input type="text" name="nombre[]" class="form-control" disabled /></div>
					</td>
					<td>
						<div class="error"><input type="text" name="cantidad[]" class="form-control" disabled/></div>
					</td>
					<td>
						<div class="error"><textarea name="comentario[]" class="form-control" disabled></textarea></div>
					</td>
					<td><button type="button" class="btn btn-default removeButton" onclick="deleteFila(this);"><span class="glyphicon glyphicon-minus"></span></button></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" class="text-left">
						<a class="btn btn-default" onclick="addFila();"><span class="glyphicon glyphicon-plus"></span> Añadir</a>
					</td>
				</tr>
			</tfoot>
		</table>
		</div>
		<br><br>
		<div class="row form-group error">
			<label class="col-sm-4 control-label">ID UGCC</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="idugcc[]" />
			</div>
		</div>
		
		<div id="templateRow2" class="row form-group error hide">
			<label class="col-sm-4 control-label"></label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="idugcc[]" disabled/>
			</div>
		</div>

		<tfoot>
				<tr>
					<td colspan="4" class="text-left">
						<a class="btn btn-default" onclick="addFila2();"><span class="glyphicon glyphicon-plus"></span> Agregar ID</a>
					</td>
				</tr>
			</tfoot>
		<div class="row form-group error">
			<label class="col-sm-4 control-label">Nombre médico de turno que solicita activación: </label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="solicitante" />
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-4 control-label">Cantidad de pacientes en espera en servicio de urgencia: </label>
			<div class="col-sm-2">
				<input type="number" class="form-control" name="pacienteEspera" />
			</div>
			<!--<label class="col-sm-2 control-label">Cantidad de pacientes ocupando servicio de urgencia: </label>
			<div class="col-sm-2">
				<input type="number" class="form-control" name="pacienteOcupando" />
			</div>-->
			<div class="col-sm-6">
				<textarea  name="comentarioEspera" placeholder="Comentario" class="form-control"></textarea>
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-4 control-label">Cantidad de pacientes con criterio de derivación a cama básica que requiere derivar a través de UGCC: </label>
			<div class="col-sm-2">
				<input type="number" class="form-control" name="pacienteBasica" />
			</div>
			<div class="col-sm-6">
				<textarea  name="comentarioBasica" placeholder="Comentario" class="form-control"></textarea>
			</div>
		</div>
		<div class="form-group error">
			<label class="col-sm-4 control-label">Cantidad de pacientes con criterio de derivación a cama mayor complejidad a travéz de UGCC: </label>
			<div class="col-sm-2">
				<input type="number" class="form-control" name="pacienteCompleja" />
			</div>
			<div class="col-sm-6">
				<textarea  name="comentarioCompleja" placeholder="Comentario" class="form-control"></textarea>
			</div>
		</div>
		<div class="row form-group error">
			<label class="col-sm-4 control-label">Cupos otorgados UGCC: </label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="cupos" />
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-10">
				<button id="btnContingencia" type="submit" class="btn btn-primary">Aceptar</button>
			</div>
		</div>
		{{ Form::close() }}
	</div>



</fieldset>

@stop
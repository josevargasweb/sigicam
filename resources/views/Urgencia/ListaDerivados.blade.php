@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")

<script>
	var table=null;
	var idCaso=null;
	var idLista=null;


	$( document ).ready(function() {

		$("#DerivacionMenu").collapse();

		$("#fechaDerivacion").on("dp.change", function(){
			$.ajax({
				url: "solicitarinfoFormularioDerivado",
				type: 'post',
				dataType: 'json',
				data: {
					'idCaso': $("#idCasoFormDerivacion").val(),
					'fecha': $("#fechaDerivacion").val()
				},
				success: function(data){
					$("#unidadFuncional").html(data["nombre"]);
					$("#idUnidadFuncional").val(data["id"]);

				},
				error: function(error){
					console.log(error);
				}
			});
			$("#formEditarDerivado").bootstrapValidator("revalidateField", "fechaDerivacion");
		});

		$("#fechaIda").on("dp.change", function(){
			$("#formEditarDerivado").bootstrapValidator("revalidateField", "fechaIda");
		});

		$("#fechaRescate").on("dp.change", function(){
			$("#formEditarDerivado").bootstrapValidator("revalidateField", "fechaRescate");
		});

		$("#formEditarDerivado").bootstrapValidator({
			excluded: [':disabled',':not(:visible)'],
			fields: {
				ugcc: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								if(value == "no"){
									$("#tipo_ugcc").addClass("hidden");
								}else{
									$("#tipo_ugcc").removeClass("hidden");
								}
								return true;
							}
						}
					}
				},
				tramo: {
					validators: {
						callback: {
							callback: function(value, validator, $field){
								if(value == 'pendiente'){
									$("#t_fecha_ida").addClass("hidden");
									$("#t_fecha_rescate").addClass("hidden");
								}
								else if(value == 'ida'){
									$("#t_fecha_ida").removeClass("hidden");
									$("#t_fecha_rescate").addClass("hidden");
								}
								else if(value == "ida-rescate"){
									$("#t_fecha_ida").removeClass("hidden");
									$("#t_fecha_rescate").removeClass("hidden");
								}
								return true;
							}
						}
					}
				},
				fechaDerivacion: {
					validators:{
						notEmpty: { 
						 	message: 'Debe ingresar la fecha de ida'
						}
					}
				},
				fechaIda: {
					validators:{
						notEmpty: { 
						 	message: 'Debe ingresar la fecha de ida'
						}
					}
				},
				fechaRescate: {
					validators:{
						notEmpty: { 
						 	message: 'Debe ingresar la fecha de rescate'
						}
					}
				},
				tipo_centro: {
					validators:{
						notEmpty: {
							message: 'Seleccione el tipo de centro'
						}
					}
				},
				otro_derivacion: {
					validators:{
						notEmpty: { 
						 	message: 'Debe especificar el Centro'
						}
					}
				},
				compra_servicio_otro: {
					validators:{
						notEmpty: { 
						 	message: 'Debe especificar otro movil'
						}
					}
				}
			}
			}).on('status.field.bv', function(e, data) {
				data.bv.disableSubmitButtons(false);
			}).on('error.form.bv', function(e) {
			}).on("success.form.bv", function(evt){
				var $form = $(evt.target);
				var $button = $form.data('bootstrapValidator').getSubmitButton();

				fv = $form.data('bootstrapValidator');
				evt.preventDefault();

				$("#btnFormularioDerivar").attr('disabled', 'disabled');
				$.ajax({
					url: "editarFormDerivado",
					type: 'post',
					dataType: 'json',
					data: $('#formEditarDerivado').serialize()
				})
				.done(function(data) {
					if(data.error) {
						swalError.fire({
							title: 'Error',
							text:data.error
							}).then(function(result) {
							if (result.isDenied) {
								  location . reload();
							}
							});
					}else{
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
				})				
			});

				//Mauricio//
			var datos_medicos = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicos'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				remote: {
					url: '{{URL::to('/')}}/'+'%QUERY/consulta_medicos',
					wildcard: '%QUERY',
					filter: function(response) {
						return response;
					}
				},
				limit: 50
			});

			datos_medicos.initialize();

			$('.medicos .typeahead').typeahead(null, {
			name: 'best-pictures',
			display: 'nombre_apellido',
			source: datos_medicos.ttAdapter(),
			limit: 50,
			templates: {
				empty: [
				'<div class="empty-message">',
					'No hay resultados',
				'</div>'
				].join('\n'),
				suggestion: function(data){
					var nombres = data;
					return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre_medico + " " + data.apellido_medico +"</b></span><span class='col-sm-4'><b>"+data.rut_medico+"-"+data.dv_medico+"</b></span></div>"
				},
				header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre médico</span><span class='col-sm-4' style='color:#1E9966;'>Rut médico</span></div><br>"
			}
			}).on('typeahead:selected', function(event, selection){
				console.log("selected");
				// $("#medico").val('asdas');
				$("[name='id_medico']").val(selection.id_medico);
			}).on('typeahead:close', function(ev, suggestion) {
				var med=$(this).parents(".medicos").find("input[name='id_medico']");
				console.log(med.val());
				if(!med.val()&&$(this).val()){
					$(this).val("");
					$med.val("");
					$(this).trigger('input');
				}
			});

			$("#medicoDerivador").on('change keyup',function(){
				var me = $(this);
				if(!me.val()){
					me.val('');
					$("[name='id_medico']").val('');
				}
			});
	});

	var quitarDerivado=function(idLista){
		$("#idListaQuitarDerivacion").val(idLista);
		$("#fechaTerminoDerivacion").val('').change();
		$("#modalquitarDerivados").modal("show");
	}

	$("#formquitarDerivados").bootstrapValidator({
		excluded: ':disabled',
		fields: {
			comentario: {
				validators:{
					notEmpty: {
					}
				}
			},
			fechaTerminoDerivacion: {
				validators:{
					notEmpty: {
						message: 'Debe ingresar una fecha'
					},
					remote: {
						data: function(validator){
							return {
								caso: validator.getFieldElements('idCaso').val(),
								lista: $("#idListaQuitarDerivacion").val(),
								fechaTermino: validator.getFieldElements('fechaTerminoDerivacion').val()
							};
						},
						url: "{{ URL::to("/validarFechaDerivacionRealizada") }}"
					}
				}
			}
		}
	}).on('status.field.bv', function(e, data) {
		$("#formquitarDerivados input[type='submit']").prop("disabled", false);
	}).on("success.form.bv", function(evt){
		$("#formquitarDerivados input[type='submit']").prop("disabled", false);
		evt.preventDefault(evt);
		var $form = $(evt.target);
		bootbox.confirm({
			message: "<h4>¿Está seguro de querer quitar al paciente de derivación?</h4>",
			buttons: {
				confirm: {
					label: 'Si',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) { 
				if(result){
					$.ajax({
						url: "quitarDerivado",
						headers: { 
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: "post",
						dataType: "json",
						data: $form .serialize(),
						success: function(data){
							$("#modalquitarDerivados").modal("hide");
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
							}).then(function(result) {
							if (result.isDenied) {
								  location . reload();
							}
							});
							} 
						},
						error: function(error){
							console.log(error);
						}
					});
				}
			}
		});
	});

	$("#fechaTerminoDerivacion").on("dp.change", function(){
		$("#formquitarDerivados").bootstrapValidator("revalidateField", "fechaTerminoDerivacion");
	});

	$("#modalquitarDerivados").on('hidden.bs.modal', function () {
		$("#fechaTerminoDerivacion").val('').change();
	});

	$(function(){ 
		$("#formAgregarComentarioDerivados").bootstrapValidator({
			excluded: ':disabled',
			fields: {
				comentario: {
					validators:{
						notEmpty: { 
						 	message: 'El comentario es obligatorio'
						}
					}
				}
			}
		}).on('status.field.bv', function(e, data) {
			$("#formAgregarComentarioDerivados input[type='submit']").prop("disabled", false);
		}).on("success.form.bv", function(evt){
			$("#formAgregarComentarioDerivados input[type='submit']").prop("disabled", false);
			evt.preventDefault(evt);
			var $form = $(evt.target);
			bootbox.confirm({
				message: "<h4>¿Está seguro de querer agregar el comentario?</h4>",
				buttons: {
					confirm: {
						label: 'Si',
						className: 'btn-success'
					},
					cancel: {
						label: 'No',
						className: 'btn-danger'
					}
				},
				callback: function (result) {
					if(result){
						form = $("#formAgregarComentarioDerivados");
						$.ajax({
							url: "agregarComentarioListaDerivado",
							headers: { 
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: "post",
							dataType: "json",
							data: $form .serialize(),
							success: function(data){
								$("#modalListaComentariosDerivado").modal("hide"); 
								if(data.exito) {
									swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
								form[0] . reset();

						location . reload();
							}, 2000)
						},
						});

								}
								if(data.error) swalError.fire({
												title: 'Error',
												text:data.error
												});
							},
							error: function(error){
								console.log(error);
							}
						});
					}
				}
			});
		});
	});

	table=$('#listaDerivados').dataTable({	
		"bJQueryUI": true,
		"iDisplayLength": 10,
		"order": [[ 6, "asc" ]],
		"columnDefs": [
			{ type: 'date-euro', targets: 6 }
		],
		"language": {
			"sUrl": "{{URL::to('/')}}/js/spanish.txt"
		},
		"sPaginationType": "full_numbers",
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if ( $('td', nRow).find('label')[0].outerText >= 12 ){
				$('td', nRow).css('color', '#d14d33');
				$('td', nRow).css('font-weight', 'bold');
			}else if ( $('td', nRow).find('label')[0].outerText >= 6 && $('td', nRow).find('label')[0].outerText < 12 ){
				$('td', nRow).css('color', 'rgb(186,186,57)');
				$('td', nRow).css('font-weight', 'bold');
			}else if($('td', nRow).find('label')[0].outerText >= 1 && $('td', nRow).find('label')[0].outerText < 6){
				$('td', nRow).css('color', '#41a643');
				$('td', nRow).css('font-weight', 'bold');
			}else{
				$('td', nRow).css('color', 'rgb(88,86,86)');
				$('td', nRow).css('font-weight', 'bold');
			}
			
		}
	});

	var documentosDerivacion = function (idCaso){
		$.ajax({
			url: "{{ URL::to('/documentosDerivacion') }}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {caso: idCaso},
			dataType: "json",
			type: "post",
			success: function(data){
				$("#modalDocumentosDerivacion .modal-body").html(data.contenido);
				$("#modalDocumentosDerivacion").modal();
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var obtenerListaComentariosDerivado=function(idCaso,idLista){
		$(".idCaso").val(idCaso);
		$("#modalListaComentariosDerivado").modal("show");
		$.ajax({
			url: "obtenerComentariosListaDerivado",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {idCaso:idCaso,idLista:idLista},
			dataType: "json",
			type: "post",
			success: function(data){
				tablaComentarios = $('#listaComentariosDerivado').dataTable( {
				"aaData": data,
				"bDestroy": true,
				"columnDefs": [
       			{ type: 'date-euro', targets: 0 }
     		],
				"columns": [					
					{ "data": "fecha" },
					{ "data": "comentario" }
				]
			})
			},
			error: function(error){
				console.log(error);
			}
		});
	}

	var formularioDerivacion = function(idCaso, idLista){
		$("#modalFormularioDerivado").modal("show");
		$('#formEditarDerivado').bootstrapValidator('revalidateField', 'tipo_centro');
		$("#idListaFormDerivacion").val(idLista);
		$(".idCaso").val(idCaso);
		$(".detalle_motivo_derivacion").attr("hidden",true);
		$("#tipo_ugcc").addClass("hidden");
		$(".select_red_privada").attr("hidden",true);
		$(".select_red_publica").attr("hidden",true);
		$(".otro_centro_derivacion").attr("hidden",true);
		$(".detalle_via_traslado").attr("hidden",true);
		$("#t_fecha_rescate").addClass("hidden");
		$(".select_compra").attr("hidden",true);
		$(".otro_compra_servicio").attr("hidden",true);
		//Informacion del caso en su mayoria
		$.ajax({
			url: "datosParaDerivacion2",
			data: {
				caso : idCaso
			},
			headers: {					 
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')				
			},
			type: "post",
			dataType: "json",
			success: function (data) {
				// console.log("info: ", data);
				$("#nombreCompletoPaciente").text(data.nombreCompleto);
				$("#rutDv").text(data.rutDv);
				$("#grupoEtareo").text(data.grupoEtareo);
				$("#edadPaciente").text(data.fechaNacimiento + " (" + data.edad + ")");
				$("#fechaHospitalizacion").text(data.fechaHospitalizacion);
				$("#idUnidadFuncional").val(data.idUnidad);
			},
			error: function (error) {
				console.log(error);
			}
		});

		//informacion del caso y del formulario
		$.ajax({
			url: "infoFormularioDerivado",
			data: {
				lista: idLista,
				caso : idCaso
			},
			headers: {					 
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')				
			},
			type: "post",
			dataType: "json",
			success: function (data) {
				$("#unidadFuncional").text(data.nombreUnidad);
				$("#tipo_traslado").val(data.tipo_traslado);
				$("#motivo_derivacion").val(data.motivo_derivacion);
				if(data.tipo_cama == 0 || data.tipo_cama == null){
					$("#tipo_cama").val(1);
				}else{
					$("#tipo_cama").val(data.tipo_cama);
				}

				if(data.id_medico && data.nombreMedico){
					$('#medicoDerivador').typeahead('val', data.nombreMedico);
					$("[name='id_medico']").val(data.id_medico);
				}

				if(data.ges == true){
					$("input[name=ges][value='si']").prop("checked",true);
				}else{
					$("input[name=ges][value='no']").prop("checked",true);
				}
				
				if(data.ugcc == true){
					$("input[name=ugcc][value='si']").prop("checked",true);
					$("#tipo_ugcc").removeClass("hidden");
					$("#t_ugcc").val(data.t_ugcc);
				}else{
					$("input[name=ugcc][value='no']").prop("checked",true);
				}

				$("#select-tipo-centro").val(data.tipo_centro);

				if(data.tipo_centro == 'derivacion'){
					$(".select_red_publica").removeAttr("hidden");
					$('select[name=red_publica]').val(data.red_publica);
					$('.selectpicker').selectpicker('refresh');
					$(".select_red_privada").attr("hidden",true);
				}else if(data.tipo_centro == 'traslado extra sistema'){
					$(".select_red_privada").removeAttr("hidden");
					$('select[name=red_privada]').val(data.red_privada);
					$('.selectpicker').selectpicker('refresh');
		  			$(".select_red_publica").attr("hidden",true);
				}else{
					$(".select_red_privada").attr("hidden",true);
					$(".select_red_publica").attr("hidden",true);
					$(".otro_centro_derivacion").attr("hidden",true);
				}

				$("#via_traslado").val(data.via_traslado);
				if(data.via_traslado == 2){
					$(".detalle_via_traslado").removeAttr("hidden");
					$("#detalle_via").val(data.detalle_via);
				}else{
					$(".detalle_via_traslado").attr("hidden",true);
				}

				fderivacion = moment(data.fecha_creacion).format('DD-MM-YYYY HH:mm');
				$("#fechaDerivacion").val(fderivacion);
				if(fderivacion){
					$("#fechaDerivacion").val(fderivacion);
				}
				if(data.fecha_egreso != null){
					$("#fechaDerivacion").val('');
					fegreso = moment(data.fecha_egreso).format('DD-MM-YYYY HH:mm');
					$("#fechaDerivacion").val(fegreso);
				}

				if(data.fecha_ida){
					fida = moment(data.fecha_ida).format('DD-MM-YYYY HH:mm');
				}

				if(data.fecha_rescate){
					frescate = moment(data.fecha_rescate).format('DD-MM-YYYY HH:mm');
				}

				if(data.tramo == 'ida'){
					$("input[name=tramo][value='ida']").prop('checked',true);
					$("#t_fecha_ida").removeClass("hidden");
					$("#fechaIda").val(fida);
				}else if(data.tramo == 'ida-rescate'){
					$("input[name=tramo][value='ida-rescate']").prop('checked',true);
					$("#t_fecha_ida").removeClass("hidden");
					$("#fechaIda").val(fida);
					$("#t_fecha_rescate").removeClass("hidden");
					$("#fechaRescate").val(frescate);
				}else{
					$("input[name=tramo][value='pendiente']").prop('checked',true);
				}

				$('select[name=comuna_origen]').val(data.comuna_origen);
				$('select[name=comuna_destino]').val(data.comuna_destino);
				$('.selectpicker').selectpicker('refresh');

				$("#estado_paciente").val(data.estado_paciente);

				$("#movil").val(data.movil);
				$("#compra_servicio").val(data.compra_servicio);
				$("#compra_servicio_otro").val(data.compra_servicio_otro);
				if(data.movil == 2){
					$(".select_compra").removeAttr("hidden");
					// $("#compra_servicio").val(data.compra_servicio);
					if(data.compra_servicio == 3){
						$(".otro_compra_servicio").removeAttr("hidden");
						// $("#compra_servicio_otro").val(data.compra_servicio_otro);
					}
				}

				if(data.compra_servicio == 3){
					$(".otro_compra_servicio").removeAttr("hidden");
					// $("#compra_servicio_otro").val(data.compra_servicio_otro);
				}

				$("#comentarios").val(data.comentarios);
			},
			error: function (error) {
				console.log(error);
			}
		});
	}

	$(window).load(function() {
    $(".loader").fadeOut("slow");
});

</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Derivados</a></li>
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

.tt-menu {
  margin-top: 4px;
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
	 -moz-border-radius: 4px;
		  border-radius: 4px;
  -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	 -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
		  box-shadow: 0 5px 10px rgba(0,0,0,.2);
	overflow-y: scroll; 
	max-height: 350px;
}

.tt-suggestion {
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





.table > thead:first-child > tr:first-child > th {
	color: cornsilk;
}


table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc {
	color: #032c11 !important;
}

table.dataTable thead .sorting, 
table.dataTable thead .sorting_asc, 
table.dataTable thead .sorting_desc {
    background : none;
}

table > thead:first-child > tr:first-child > th{
	vertical-align: middle;
}
.formulario > .panel-default > .panel-heading {
	background-color: #bce8f1 !important;
}
</style>
<fieldset>
	<legend>Derivados</legend>
	<div class="table-responsive">
		<div class="form-inline">
			{{ HTML::link(URL::route('excelListaDerivados'), 'Excel', ['class' => 'btn btn-success']) }}
			{{ HTML::link(URL::route('historialDerivados'),'Historial Derivados',['class' => ' btn btn-default', 'style' => 'color: #399865'])}}
		</div>		
		<br><br>
		<table id="listaDerivados" class="table  table-condensed table-hover">
		<thead>
			<tr style="background:#399865;">
			<th>Opciones</th>
				<th style="width:100px">Run</th>
				<th>Nombre completo</th>
				<th>Fecha nacimiento</th>
				<th>fecha de Ingreso</th>
				<th>Procedencia</th>
				<th>Diagnóstico</th>
				<th>Unidad</th>
				<th>Cama</th>
				<th>Ultimo comentario</th>
				<th>Volver unidad</th>
				
				</tr>
		</thead>
		<tbody>
			@foreach ($response as $resp)
			<tr>
				<td>{!!$resp["opciones"]!!}</td>
				<td>{{$resp["rut"]}}</td>
				<td>{{$resp["nombre_completo"]}}</td>
				<td>{!!$resp["fecha_nacimiento"]!!}</td>
				<td>{!!$resp["fecha_ingreso"]!!}</td>
				<td>{{$resp["procedencia"]}}</td>
				<td>{!!$resp["diagnostico"]!!}</td>
				<td>{{$resp["servicio"]}}</td>
				<td>{{$resp["cama"]}}</td>
				<td>{{$resp["ultimo_comentario"]}}</td>
				<td>{!!$resp["volver_unidad"]!!}</td>
			</tr>	
			@endforeach
			
		</tbody>
	</table>
	</div>
</fieldset>

<div id="modalquitarDerivados" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Quitar paciente de derivación</h4>
      </div>
	  {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formquitarDerivados')) }}
	  {{ Form::hidden('idLista', '', array('class' => 'idLista', 'id' => 'idListaQuitarDerivacion')) }}   
	  
		<div class="modal-body">
			<div class="row" style="margin: 0;">
				<div class="col-md-12 form-group">
					<div class="col-md-4">
						<label class="control-label">Fecha Termino Derivación: </label>
					</div>
					<div class="col-md-5">
						<div class="col-sm-12">
							{{Form::text('fechaTerminoDerivacion', null, array('id' => 'fechaTerminoDerivacion', 'class' => 'form-control fecha-sel'))}}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button id="quitarDerivadoSubmit" type="submit" class="btn btn-primary">Quitar paciente </button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
		</div>
      	{{ Form::close() }}
    </div>
  </div>
</div>


<div id="modalListaComentariosDerivado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        		<h4 class="modal-title">Historial De Comentarios</h4>
      		</div>
			
			<div class="modal-body">			
				<div class="row" style="margin: 0;">
					{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formAgregarComentarioDerivados')) }}
					{{ Form::hidden('idCaso', '', array('class' => 'idCaso')) }}
						<div class="form-group col-md-12">
							<h4>Comentario (máximo 100 caracteres)</h4>
						</div>
						<div class="form-group col-md-12">
							{{Form::textarea('comentario', null, array('id' => 'comentario', 'class' => 'form-control', 'rows'=>'5', 'maxlength'=>'100'))}}
						</div>
						<button id="agregarComentarioSubmit" type="submit" class="btn btn-primary">Agregar Comentario</button>
					{{ Form::close() }}
				</div>
	   			<fieldset>
					<legend>Derivados</legend>
					<div class="table-responsive">
					<table id="listaComentariosDerivado" class="table  table-condensed table-hover">
						<thead>
							<tr style="background:#399865;">
								<th style="width:100px">Fecha</th>
								<th>Comentario</th>								
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					</div>
				</fieldset>
      		</div> 
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
    	</div>
  	</div>
</div>

<div id="modalDocumentosDerivacion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Documentos de derivación</h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEditarDerivado', 'autocomplete' => 'off')) }}
	@include('Gestion/formularioDerivacion')
{{ Form::close() }}

@stop
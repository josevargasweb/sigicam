@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("miga")
<!-- <li><a href="#">Urgencia</a></li>
<li><a href="#">Documentos</a></li> -->
@stop

@section("script")

	<script>

		$( document ).ready(function() {
		
			$("#formGlasgow").bootstrapValidator({
				excluded: [':disabled', ':hidden', ':not(:visible)'],
				fields: { 
				 apertura_ocular:{
							validators:{
							notEmpty: {
								message: 'Campo obilgatorio'
							}
						}
					},
					respuesta_verbal:{
							validators:{
							notEmpty: {
								message: 'Campo obilgatorio'
							}
						}
					},
					respuesta_motora:{
							validators:{
							notEmpty: {
								message: 'Campo obilgatorio'
							}
						}
					}
				}
				  
			}).on('status.field.bv', function(e, data) {
				//$("#formBuscarNombres input[type='submit']").prop("disabled", false);  
			}).on("success.form.bv", function(evt){
				
				evt.preventDefault(evt); 
				datos = $("#formGlasgow").serialize();
		
				bootbox.confirm({				
					message: "<h4>¿Está seguro de ingresar la información?</h4>",				
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
						console.log('This was logged in the callback: ' + result);					
						if(result){					
							console.log("entra alajax?");					
							$.ajax({
								//url: "buscarNombres", 
								url: '{{URL::to("gestionEnfermeria/guardarGlasgow")}}',  
								headers: {  
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
								type: "post",
								dataType: "json",
								data: $("#formGlasgow").serialize(),
								success: function(data){ 
									//if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){ table.api().ajax.reload(); }); 
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
									
									if(data.error) {
										swalError.fire({
											title: 'Error',
											text:data.error
										});
									}

									if(data.info) {
										swalInfo2.fire({
											title: 'Información',
											text: data.info
										}).then(function(result) {
											location . reload();
										});
									}
																	  
								},
								error: function(error){ 
									//console.log(error);
								}
							});				
						}				
					}
		  		});
			});
		
			
			table=$('#tabla').dataTable({	
				//responsive: true,
				dom: 'Bfrtip',
				buttons: [
					{
						extend: 'excelHtml5',
						messageTop: 'Pacientes en espera',
						text: 'Exportar',
						exportOptions: {
							columns: [1,2,3,4,5]
						} ,
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
				"bJQueryUI": true,
				"iDisplayLength": 10,
				"order": [[ 1, "asc" ]],
				"columnDefs": [
					{ type: 'date-euro', targets: 1 }
				],
				"ajax": "datosTabla",
				"language": {
					"sUrl": "{{URL::to('/')}}/js/spanish.txt"
				},
				"sPaginationType": "full_numbers",
				
			});
		});

		$('#bannerformmodal').on('shown.bs.modal', function (e) {
			id = $(e.relatedTarget).data('id');
			$.ajax({
				//url: "buscarNombres", 
				url: "{{URL::to('gestionEnfermeria/editarGlasgow/')}}"+"/"+id,  
				headers: {  
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "get",
				dataType: "json",
				//data: {"id":id},
				success: function(data){ 
					$("#apertura_ocular").val(data.apertura_ocular).change();
					$("#respuesta_verbal").val(data.respuesta_verbal).change();
					$("#respuesta_motora").val(data.respuesta_motora).change(); 
					$("#total").val(data.total);
					$("#id_formulario_escala_glasgow").val(data.id_formulario_escala_glasgow);

					$("#btnhistorialglasgow").hide();
					$("#legendglasgow").hide();
					$("#guardarGlasgow").val("Editar Información");

					total = data.apertura_ocular + data.respuesta_motora + data.respuesta_verbal;
					$("#totalGlasgow").val(total);
					 
				},
				error: function(error){ 
					//console.log(error);
				}
			});  

		});
	</script>

@stop



@section("section")
<div class="container">
<fieldset>
	<a href="../../gestionEnfermeria/{{$caso}}" class="btn btn-primary">Volver</a>
	<br>
	<legend><center>Escala de Glasgow</center></legend>
	
	<div class="col-md-12">
		Nombre Paciente: {{$paciente}}
	</div>

<div class="container"></div>


<table id="tabla" class="table  table-condensed table-hover">
		<thead>
			<tr style="background:#399865;">
				<th>Opciones</th>

				<th>Fecha</th>
				<th>Apertura ocular</th>
        <th>Respuesta verbal</th>
        <th>Respuesta motora</th>
        <th>Total</th>

				
				
			</tr>
		</thead>
		<tbody></tbody>
	</table>

</fieldset>

<div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal">
<div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Escala de glasgow</h4>
                </div>
                <div class="modal-body">
					{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'formGlasgow', 'autocomplete' => 'off')) }}
					<input type="hidden" value="Editar" name="tipoFormGlasgow" id="tipoFormGlasgow">
					<input type="hidden" value="{{$caso}}" name="caso">
					<input type="hidden" value="" name="id_formulario_escala_glasgow" id="id_formulario_escala_glasgow">

					@include('Gestion.gestionEnfermeria.partials.Formglasgow')   
					{{ Form::close() }}
                </div>                     
        </div>
        </div>
      </div>
    </div>
</div>
@stop


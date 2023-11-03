
	<script type="text/javascript">
		$(function(){
			$("#formBarthel").bootstrapValidator({
				excluded: [':disabled', ':hidden', ':not(:visible)'],
				container: 'tooltip',
				fields: {                
							comida:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							lavado:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							vestido:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							arreglo:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							deposicion:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							miccion:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							retrete:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							transferencia:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							deambulacion:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							},
							escaleras:{
									validators:{
									notEmpty: {
										message: 'Campo obilgatorio'
									}
								}
							}
						} 

				}).on("success.form.bv", function(evt){

					evt.preventDefault(evt);
					var $form = $(evt.target);

					var form = $(this).serialize();
					console.log(form);

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
									url: '{{URL::to("gestionEnfermeria/guardarBarthel")}}', 
									data: form,
									type: "post",
									dataType: "json",
									success: function(data){
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
										if(data.error) swalError.fire({
														title: 'Error',
														text:data.error
														});
									},
									error: function(error){
										console.log(error);
									}
								});
							}
							}

							});


					return false;
				});

		});
	</script>
	<meta name="csrf-token" content="{{{ Session::token() }}}">

<style>
.table thead{
	background-color: #bce8f1;
}

.table > thead:first-child > tr:first-child > th {
    color: black;
}
</style>
<br>
<legend class="text-center" id="legendBarthel"><u>Índice de Barthel Inicial</u></legend>


{{ HTML::link("gestionEnfermeria/$caso/historialBarthel", ' Ver Historial', ['class' => 'btn btn-default' , "id" => "volver"]) }}
<br><br>

<div class="panel panel-default">
	<div class="panel-heading panel-info">
		<h4>Índice de Barthel Inicial</h4>
    </div>

	<div class="panel-body">
		<div style="text-align: left;">

			{{-- <div class="form"> --}}
			{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'formBarthel', 'autocomplete' => 'off')) }}

			<input type="hidden" value="{{$caso}}" name="caso">
			<input type="hidden" value="" name="id_formulario_barthel" id="id_formulario_barthel">
			<input type="hidden" value="En Curso" name="tipoFormBarthel" id="tipoFormBarthel">
			<div>
				<input name="inicio" value="true" hidden="">
				<input name="tipo-encuesta" value="indiceBarthel" hidden="">
			</div>

			@include('Gestion.gestionEnfermeria.partials.FormBarthel')

			{{Form::close()}}
		{{-- </form></div> --}}
		</div>
<!--</fieldset>-->
	</div>
</div>

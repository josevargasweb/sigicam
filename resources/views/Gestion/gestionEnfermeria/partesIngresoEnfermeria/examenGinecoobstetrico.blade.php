<style>
	#form_ginecoobstetrico .row{
		margin-bottom: 15px;
	}
</style>
<script>
$(function(){
	$("#form_ginecoobstetrico").bootstrapValidator().on('status.field.bv', function(e, data) {
		//data.bv.disableSubmitButtons(true);
	}).on("success.form.bv", function(evt, data){
		//$("#btGuardarSegmentario").prop("disabled", true);
		evt.preventDefault(evt);
		var $form = $(evt.target);
		
		$.ajax({
			url: "{{url('/guardar_examen_ginecoobstetrico')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: $form .serialize(),
			async: true,
			success: function(data){

				Swal.hideLoading();
				swalCargando.close();
				Swal.hideLoading();
				enviado=true;

				if(!data.error){
					swalExito.fire({
					title: 'Exito!',
					text: "Se ha guardado correctamente",
					didOpen: function() {
						setTimeout(function() {

						}, 2000)
					},
					});
				}
				if(data.error){
					swalError.fire({
					title: 'Error',
					text:data.msg
					}).then(function(result) {
					if (result.isDenied) {

					}
					});
					console.log(data.error);
				}
			},
			error: function(error){
				// swalCargando.close();
				Swal.hideLoading();
				swalCargando.close();
				Swal.hideLoading();
				console.log(error);
			},
			complete:function(){
				$form.find("[type=submit]").prop("disabled",false);
			}
		});
		
	});
	$("#hGO a").click(function() {
		cargarExamenGinecoobstetrico();
	});
	function cargarExamenGinecoobstetrico(){
		$.ajax({
			url: "{{url('/cargar_examen_ginecoobstetrico')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:{caso: {{$caso}}},
			type: "post",
			dataType: "json",
			async: true,
			success: function(data){

				$("#vulva_ego").val(data.vulva);
				$("#vagina_tacto_vaginal_ego").val(data.vagina_tacto_vaginal);
				$("#fondo_de_saco_tacto_vaginal_ego").val(data.fondo_de_saco_tacto_vaginal);
				$("#anexos_ego").val(data.anexos);
				$("#otros_tacto_vaginal_ego").val(data.otros_tacto_vaginal);
				$("#vagina_especuloscopia_ego").val(data.vagina_especuloscopia);
				$("#utero_ego").val(data.utero);
				$("#cervix_ego").val(data.cervix);
				$("#fondo_de_saco_especuloscopia_ego").val(data.fondo_de_saco_especuloscopia);
				$("#otros_especuloscopia_ego").val(data.otros_especuloscopia);
				$("#recto_ano_ego").val(data.recto_ano);
				$("#presentacion_ego").val(data.presentacion);
				$("#altura_uterina_ego").val(data.altura_uterina);
				$("#tono_ego").val(data.tono);
				$("#encajamiento_ego").val(data.encajamiento);
				$("#dorso_ego").val(data.dorso);
				$("#lcf_ego").val(data.lcf);
				$("#longitud_cuello_uterino_ego").val(data.longitud_cuello_uterino);
				$("#dilatacion_cuello_uterino_ego").val(data.dilatacion_cuello_uterino);
				$("#membranas_ego").val(data.membranas);
				$("#liquido_amniotico_ego").val(data.liquido_amniotico);
				$("#posicion_ego").val(data.posicion);
				$("#plano_ego").val(data.plano);
				$("#evaluacion_pelvis_ego").val(data.evaluacion_pelvis);
				$("#otros_examen_obstetrico_ego").val(data.otros_examen_obstetrico);
				
				if(data.contracciones === true){
					$("input[name=contracciones_ego][value=si]").prop("checked",true);
				}
				else if(data.contracciones === false){
					$("input[name=contracciones_ego][value=no]").prop("checked",true);
				}
				else{
					$("input[name=contracciones_ego]").prop("checked",false);
				}
				
				if(data.desaceleraciones === true){
					$("input[name=desaceleraciones_ego][value=si]").prop("checked",true);
				}
				else if(data.desaceleraciones === false){
					$("input[name=desaceleraciones_ego][value=no]").prop("checked",true);
				}
				else{
					$("input[name=desaceleraciones_ego]").prop("checked",false);
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}
});
</script>
<form id="form_ginecoobstetrico">
	<input type="hidden" name="caso_ego" value="{{$caso}}">
	<input type="hidden" name="id_paciente_ego" value="{{$infoPaciente->id}}">
	<div class="formulario">
		<div class="panel panel-default">
			<div class="panel-heading panel-info">
				<h4>IV. Examen Ginecoobstétrico</h4>
			</div>
			<div class="panel-body">
				<fieldset>
					<legend>Examen ginecológico</legend>
					<h4>Tacto vaginal</h4>
					<div class="row">
						<div class="col-md-4">
							<label>Vulva</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="vulva_ego" id="vulva_ego">
						</div>
						<div class="col-md-4">
							<label>Vagina</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="vagina_tacto_vaginal_ego" id="vagina_tacto_vaginal_ego">
						</div>
						<div class="col-md-4">
							<label>Fondo de saco</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="fondo_de_saco_tacto_vaginal_ego" id="fondo_de_saco_tacto_vaginal_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>Anexos</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="anexos_ego" id="anexos_ego">
						</div>
						<div class="col-md-8">
							<label>Otros</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="otros_tacto_vaginal_ego" id="otros_tacto_vaginal_ego">						
						</div>
					</div>
					<h4>Especuloscopía</h4>
					<div class="row">
						<div class="col-md-4">
							<label>Vagina</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="vagina_especuloscopia_ego" id="vagina_especuloscopia_ego">
						</div>
						<div class="col-md-4">
							<label>Útero</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="utero_ego" id="utero_ego">
						</div>
						<div class="col-md-4">
							<label>Cérvix</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="cervix_ego" id="cervix_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>Fondo de saco</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="fondo_de_saco_especuloscopia_ego" id="fondo_de_saco_especuloscopia_ego">
						</div>
						<div class="col-md-8">
							<label>Otros</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="otros_especuloscopia_ego" id="otros_especuloscopia_ego">						
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label>Recto/Ano</label>
							<input type="text" class="form-control" minlength="20" maxlength="200" name="recto_ano_ego" id="recto_ano_ego">						
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Examen obstétrico</legend>
					<div class="row">
						<div class="col-md-4">
							<label>Presentación</label>
							<select class="form-control" name="presentacion_ego" id="presentacion_ego">
								<option value="Cefálica">Cefálica</option>
								<option value="Podálica">Podálica</option>
								<option value="Transversa">Transversa</option>
							</select>
						</div>
						<div class="col-md-4">
							<label>Altura uterina</label>
							<input type="number" class="form-control" max="99" min="0" name="altura_uterina_ego" id="altura_uterina_ego">
						</div>
						<div class="col-md-4">
							<label>Tono</label>
							<input type="text" class="form-control" maxlength="15" name="tono_ego" id="tono_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>Encajamiento</label>
							<input type="text" class="form-control" maxlength="15" name="encajamiento_ego" id="encajamiento_ego">
						</div>
						<div class="col-md-4">
							<label>Dorso</label>
							<input type="text" class="form-control" maxlength="15" name="dorso_ego" id="dorso_ego">
						</div>
						<div class="col-md-4">
							<label>Contracciones</label><br>
							<label>
								<input type="radio" name="contracciones_ego" value="si">
								Sí
							</label>
							<label>
								<input type="radio" name="contracciones_ego" value="no">
								No
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>L.C.F.</label>
							<input type="number" class="form-control" max="999" min="0" name="lcf_ego" id="lcf_ego">
						</div>
						<div class="col-md-4">
							<label>Desaceleraciones</label><br>
							<label>
								<input type="radio" name="desaceleraciones_ego" value="si">
								Sí
							</label>
							<label>
								<input type="radio" name="desaceleraciones_ego" value="no">
								No
							</label>
						</div>
						<div class="col-md-4">
							<label>Longitud cuello uterino</label>
							<input type="number" class="form-control" max="99" min="0" name="longitud_cuello_uterino_ego" id="longitud_cuello_uterino_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>Dilatación cuello uterino</label>
							<input type="number" class="form-control" max="99" min="0" name="dilatacion_cuello_uterino_ego" id="dilatacion_cuello_uterino_ego">
						</div>
						<div class="col-md-4">
							<label>Membranas</label>
							<input type="text" class="form-control" maxlength="15" name="membranas_ego" id="membranas_ego">
						</div>
						<div class="col-md-4">
							<label>Líquido amniótico</label>
							<input type="text" class="form-control" maxlength="15" name="liquido_amniotico_ego" id="liquido_amniotico_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<label>Posición</label>
							<input type="text" class="form-control" maxlength="15" name="posicion_ego" id="posicion_ego">
						</div>
						<div class="col-md-4">
							<label>Plano</label>
							<input type="text" class="form-control" maxlength="15" name="plano_ego" id="plano_ego">
						</div>
						<div class="col-md-4">
							<label>Evaluación pelvis</label>
							<input type="text" class="form-control" maxlength="15" name="evaluacion_pelvis_ego" id="evaluacion_pelvis_ego">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label>Otros</label>
							<input type="text" class="form-control" maxlength="30" name="otros_examen_obstetrico_ego" id="otros_examen_obstetrico_ego">						
						</div>
					</div>
				</fieldset>
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-primary" type="submit">Guardar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
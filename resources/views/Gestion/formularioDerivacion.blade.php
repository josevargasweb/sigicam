<script>
	$(document).on('change', '#motivo_derivacion', function () {
      if ($("#motivo_derivacion").val() == 15) {
		$(".detalle_motivo_derivacion").removeAttr("hidden");
		$(".esp_tipo_cama").attr("hidden", true);
      }else{
		$(".detalle_motivo_derivacion").attr("hidden",true);
		$(".esp_tipo_cama").removeAttr("hidden");
      }
	});

	$(document).on('change', '#red_publica', function () {
      if ($("#red_publica").val() == 0) {
        $(".otro_centro_derivacion").removeAttr("hidden");
      }else{
        $(".otro_centro_derivacion").attr("hidden",true);
      }
	});

	$(document).on('change', '#via_traslado', function () {
      if ($("#via_traslado").val() == 2) {
        $(".detalle_via_traslado").removeAttr("hidden");
      }else{
        $(".detalle_via_traslado").attr("hidden",true);
      }
	});

	$(document).on('change', '#movil', function () {
      if ($("#movil").val() == 2) {
		$(".select_compra").removeAttr("hidden");
		if ($("#compra_servicio").val() == 3) {
        	$(".otro_compra_servicio").removeAttr("hidden");
      	}else{
        	$(".otro_compra_servicio").attr("hidden",true);
      	}
      }else{
		$(".select_compra").attr("hidden",true);
		$(".otro_compra_servicio").attr("hidden",true);
      }
	});

	$(document).on('change', '#compra_servicio', function () {
      if ($("#compra_servicio").val() == 3) {
        $(".otro_compra_servicio").removeAttr("hidden");
      }else{
        $(".otro_compra_servicio").attr("hidden",true);
      }
	});

	$(document).on('change', '#select-tipo-centro', function () {
      if ($("#select-tipo-centro").val() == "derivacion") {
		$(".select_red_publica").removeAttr("hidden");
		$(".select_red_privada").attr("hidden",true);
      }else if ($("#select-tipo-centro").val() == "traslado extra sistema"){
		  $(".select_red_privada").removeAttr("hidden");
		  $(".select_red_publica").attr("hidden",true);
		  $(".otro_centro_derivacion").attr("hidden",true);
      }else{
		$(".select_red_privada").attr("hidden",true);
		$(".select_red_publica").attr("hidden",true);
		$(".otro_centro_derivacion").attr("hidden",true);
	  }
    });

    $(function(){

		$(".fecha-sel").datetimepicker({
			locale: "es",
			format: "DD-MM-YYYY HH:mm"
		});

        $("#modalFormularioDerivado").on('hidden.bs.modal', function () {
			$("#tipo_traslado").val('').change;
			$("#motivo_derivacion").val('').change;
			$("#detalle_derivacion").val('').change;
			$(".detalle_motivo_derivacion").attr("hidden",true);
			$("input[name=ges][value='no']").prop("checked",true);
			$("input[name=ges][value='si']").prop("checked",false);
			$("input[name=ugcc][value='no']").prop("checked",true);
			$("input[name=ugcc][value='si']").prop("checked",false);
			$("#t_ugcc").val('').change;
			$("#tipo_ugcc").addClass("hidden");
			$("#medicoDerivador").val('');
			$("#id_medico").val('');
			$("#otro_derivacion").val('');
			$("#select-tipo-centro").val('').change;
			$("#red_publica").val('').change;
			$("#red_privada").val('').change;
			$(".select_red_privada").attr("hidden",true);
			$(".select_red_publica").attr("hidden",true);
			$(".otro_centro_derivacion").attr("hidden",true);
			$("#via_traslado").val('').change;
			$("#detalle_via").val('').change;
            $(".detalle_via_traslado").attr("hidden",true);
            $("input[name=tramo][value='pendiente']").prop("checked",true);
			$("input[name=tramo][value='ida']").prop("checked",false);
			$("input[name=tramo][value='ida-rescate']").prop("checked",false);
			$("#comuna_origen").val(3101);
			$("#comuna_destino").val('').change;
			$("#fechaDerivacion").val('');
			$("#fechaIda").val('');
			$("#t_fecha_ida").addClass("hidden");
			$("#fechaRescate").val('');
			$("#t_fecha_rescate").addClass("hidden");
			$("#estado_paciente").val('').change;
			$("#movil").val('').change;
			$("#compra_servicio").val('').change;
			$("#compra_servicio_otro").val('');
			$(".select_compra").attr("hidden",true);
			$(".otro_compra_servicio").attr("hidden",true);
			$("#estado_paciente").val('').change;
			$("#comentarios").val('');
			$(".esp_tipo_cama").attr("hidden", false);
			$("#tipo_cama").val('').change;
		});

		$("#pdfFormularioDerivacion").on("click", function(){
			var idCaso=$("#idCasoFormDerivacion").val();
			var idLista=$("#idListaFormDerivacion").val();

			window.location.href = "{{url('/')}}/urgencia/formularioDerivacion/"+idCaso+"/"+idLista;
		});
    });
</script>
<div id="modalFormularioDerivado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	{{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoFormDerivacion')) }}
	{{ Form::hidden('idLista', '', array('class' => 'idLista', 'id' => 'idListaFormDerivacion')) }}
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Formulario Derivación Paciente  <a id="pdfFormularioDerivacion" class="btn btn-danger">PDF</a></h4>
				<p>(*) : Campo obligatorio.</p>
			</div>
			<div class="modal-body">
				<div class="formulario" style="overflow-y: scroll;     height: 550px;">
					<div class="panel panel-default">
						<div class="panel-heading panel-info">
							<h4>Datos Generales:</h4>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										{{Form::label('FECHA ACTUAL')}}
										<p id="fecha_actual">{{\Carbon\Carbon::now()->format('d-m-Y H:m')}}</p>
									</div>
								</div>
								<div class="form-group col-md-4 col-md-offset-1">
									<div class="col-sm-12">
										{{Form::label('NOMBRE PACIENTE:')}}
										<p id="nombreCompletoPaciente"></p>
									</div>
								</div>
								<div class="form-group col-md-4 col-md-offset-1">
									<div class="col-sm-12">
										{{Form::label('RUT:')}}
										<p id="rutDv"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										{{Form::label('GRUPO ETARIO:')}}
										<p id="grupoEtareo"></p>
									</div>
								</div>
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										{{Form::label('EDAD:')}}
										<p id="edadPaciente"></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading"><h4>Datos de derivación:</h4></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<div class="col-sm-12">
										<label for="run" class=" control-label">Fecha de Hospitalizacion: </label>
										<p id="fechaHospitalizacion"></p>
									</div>
								</div>

								<div class="col-md-3">
									<div class="col-sm-12 form-group">
											<label class="control-label">Fecha Derivación: </label>
											{{Form::text('fechaDerivacion', null, array('id' => 'fechaDerivacion', 'class' => 'form-control fecha-sel'))}}
										</div>
									</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label class="control-label" title="Extranjero">Unidad Funcional: </label>
										<p id="unidadFuncional"></p>
										{{Form::hidden('idUnidadFuncional', null, array('id' => 'idUnidadFuncional'))}}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="rut" class="control-label">Tipo de Traslado:</label>
											{{ Form::select('tipo_traslado', array('critico' => 'Critico', 'especialidad' => 'Especialidad'), null, array('id' => 'tipo_traslado', 'class' => 'form-control')) }}
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="fechaNac" class="control-label" title="Fecha de nacimiento">Motivo Derivación: </label>
										{{Form::select('motivo_derivacion', array('1'=>'Cirugía Vascular','2'=>'Cirugía cardiaca','3'=>'Electrofisiología','4'=>'Hemodinamia','5'=>'Gran Quemado','6'=>'Hematología','7'=>'Oncología','8'=>'Hemato-oncología','9'=>'Caso social','10'=>'UCI Pediatrica','11'=>'UTI Pediatrica','12'=>'Neurocirugía','13'=>'Cardiocirugía infantil','14'=>'Rescate Hospital de origen','15'=>'Deficit de cama','16'=>'Imagenología compleja','17'=>'Trauma ocular grave','18'=>'Neonatología','19'=>'Ginecología-obstetricia','20'=>'Oncología ginecológica'), null, array('id' => 'motivo_derivacion', 'class' => 'form-control'))}}
									</div>
								</div>
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<div class="col esp_tipo_cama">
											<label for="">Tipo Cama:</label>
											{{Form::select('tipo_cama', array('1'=>'Básica','2'=>'Media','3'=>'Crítica','4'=>'Domicilio'), null, array('id' => 'tipo_cama', 'class' => 'form-control'))}}
										</div>
									</div>
								</div>
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<div class="col detalle_motivo_derivacion" hidden>
											<label for="rut" class="control-label">Especificar:</label>
											{{Form::select('detalle_derivacion', array('1'=>'Básica','2'=>'Media','3'=>'Crítica'), null, array('id' => 'detalle_derivacion', 'class' => 'form-control'))}}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<div class="col-sm-12 medicos">
										<label for="medico" class="control-label" title="Médico">Médico Derivador: </label>
										{{Form::text('medico', null, array('id' => 'medicoDerivador', 'class' => 'form-control typeahead'))}}
										{{Form::hidden('id_medico', null)}}
									</div>
								</div>
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<label for="caso_social" title="Caso social">Ges: </label>
										<div class="input-group">
											<label class="radio-inline">{{Form::radio('ges', "no", true, array('required' => true))}}No</label>
											<label class="radio-inline">{{Form::radio('ges', "si", false, array('required' => true))}}Sí</label>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										<label for="caso_social" title="Caso social">UGCC: </label>
										<div class="input-group">
											<label class="radio-inline">{{Form::radio('ugcc', "no", true, array('required' => true))}}No</label>
											<label class="radio-inline">{{Form::radio('ugcc', "si", false, array('required' => true))}}Sí</label>
										</div>
									</div>
								</div>	
								<div class="hidden" id="tipo_ugcc">
									<div class="col-md-3">
										<div class="form-group">
											<label for="t_caso_social" title="Tipo de caso social">Especificar: </label>
											<div class="input-group">
												{{ Form::select('t_ugcc', array('1' => 'Privado-GRD', '2' => 'GDR-Hospital','3'=>'Privado No ranqueado'), null, array('id' => 't_ugcc', 'class' => 'form-control')) }}
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-5">
									<div class="col-sm-12">
										<label for="calle" class="control-label" title="Calle">Tipo de Centro: </label>
										{{ Form::select('tipo_centro', array('derivacion'=>'1. Derivación a otro establecimiento de la red pública','traslado extra sistema'=>'2. Derivación a institución privada','pendiente'=>'3. Pendiente'), null, ['class' => 'form-control', "id" => "select-tipo-centro", "placeholder" => "seleccione"]) }}
									</div>
								</div>
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<div class="col select_red_publica" hidden>
											<label for="telefono" class="control-label" title="Nombre Social">Centro de Derivación: </label>
											{{ Form::select('red_publica', App\Models\Establecimiento::getEstablecimientos() + ['0' => 'otros'], null, array('class' => 'form-control selectpicker', "id" => "red_publica", 'data-live-search="true"')) }}
										</div>
									</div>
									<div class="col-sm-12">
										<div class="col select_red_privada" hidden>
											<label for="telefono" class="control-label" title="Nombre Social">Centro de Derivación: </label>
										{{ Form::select('red_privada', App\Models\EstablecimientosExtrasistema::getEstablecimiento(), null, array('class' => 'form-control selectpicker', "id" => "red_privada", 'data-live-search="true"')) }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-6">
									<div class="col-sm-12">
										<div class="col otro_centro_derivacion" hidden>
											<label for="rut" class="control-label">Especificar:</label>
											{{Form::text('otro_derivacion', null, array('id' => 'otro_derivacion', 'class' => 'form-control'))}}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="numeroCalle" class="control-label" title="Número">Vía de traslado: </label>
										{{Form::select('via_traslado', array('1'=>'Aéreo','2'=>'Terrestre'), null, array('id' => 'via_traslado', 'class' => 'form-control'))}}
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<div class="col detalle_via_traslado" hidden>
											<label for="rut" class="control-label">Especificar:</label>
											{{ Form::select('detalle_via', array('1' => 'M1 Básico', '2' => 'M2 Avanzado','3'=>'M3 medicalizado'), null, array('id' => 'detalle_via', 'class' => 'form-control')) }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-4">
									<div class="col-sm-12">
										<label for="caso_social" title="Caso social">Tramo: </label>
										<div class="input-group">
											<label class="radio-inline">{{Form::radio('tramo', "pendiente", true, array('required' => true))}}Pendiente</label>
											<label class="radio-inline">{{Form::radio('tramo', "ida", false, array('required' => true))}}Solo ida</label>
											<label class="radio-inline">{{Form::radio('tramo', "ida-rescate", false, array('required' => true))}}ida-rescate</label>
										</div>
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12" id="comunas">
										<label for="comuna" class="control-label" title="Comuna">Comuna origen: </label>
										{{Form::select('comuna_origen', App\Models\Comuna::getComunas(), 3101, array('id' => 'comuna_origen', 'class' => 'form-control selectpicker', 'data-live-search="true"'))}}
									</div>
								</div>

								<div class="form-group col-md-3">
									<div class="col-sm-12" id="comunas">
										<label for="comuna" class="control-label" title="Comuna">Comuna destino: </label>
										{{Form::select('comuna_destino', App\Models\Comuna::getComunas(), null, array('id' => 'comuna_destino', 'class' => 'form-control selectpicker', 'data-live-search="true"'))}}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="hidden" id="t_fecha_ida">
									<div class="col-md-3">
										<div class="form-group">
											<div class="col-sm-12">
												<label for="fechaIngreso" class="control-label" title="Fecha de ingreso">Fecha de ida: </label>
												{{Form::text('fechaIda', null, array('id' => 'fechaIda', 'class' => 'form-control fecha-sel'))}}
											</div>
										</div>
									</div>
								</div>
								<div class="hidden" id="t_fecha_rescate">
									<div class="col-md-3 col-md-offset-1">
										<div class="form-group">
											<label for="t_caso_social" title="Tipo de caso social">Fecha Rescate: </label>
											<div class="input-group">
												{{ Form::text('fechaRescate', null, array('id' => 'fechaRescate', 'class' => 'form-control fecha-sel')) }}
											</div>
										</div>
									</div>
								</div>

								<div class="form-group col-md-3 col-md">
									<div class="col-sm-12">
										<label for="numeroCalle" class="control-label" title="Número">Estado Paciente: </label>
										{{Form::select('estado_paciente', array('1'=>'Paciente en curso','2'=>'Paciente Derivado','3'=>'Paciente Nulo','4'=>'Cierre de caso'), null, array('id' => 'estado_paciente', 'class' => 'form-control'))}}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<label for="numeroCalle" class="control-label" title="Número">Movil: </label>
										{{Form::select('movil', array('1'=>'Samu','2'=>'Compra de servicios','3'=>'Pendiente','4'=>'Particular'), null, array('id' => 'movil', 'class' => 'form-control'))}}
									</div>
								</div>
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<div class="col select_compra" hidden>
											<label for="rut" class="control-label">Especificar:</label>
											{{Form::select('compra_servicio', array('1'=>'Atacama SH','2'=>'Altamira','3'=>'otros'), null, array('id' => 'compra_servicio', 'class' => 'form-control'))}}
										</div>
									</div>
								</div>
								<div class="form-group col-md-3">
									<div class="col-sm-12">
										<div class="col otro_compra_servicio" hidden>
											<label for="rut" class="control-label">Especificar:</label>
											{{Form::text('compra_servicio_otro', null, array('id' => 'compra_servicio_otro', 'class' => 'form-control'))}}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-12">
									<div class="col-sm-12">
										<label for="nombreSocial" class="control-label" title="Nombre Social">Comentarios: </label>
										{{Form::text('comentarios', null, array('id' => 'comentarios', 'class' => 'form-control'))}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="modal-footer">
					{{Form::submit('Aceptar', array('id' => 'btnFormularioDerivar', 'class' => 'btn btn-primary')) }}
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
				</div>
			</div>
		</div>
    </div>
   
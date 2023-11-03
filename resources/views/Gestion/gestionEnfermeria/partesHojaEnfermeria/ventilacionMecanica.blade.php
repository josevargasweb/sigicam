<script>
$(function(){
	$("#ventilacion_mecanica_form").bootstrapValidator({
		
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault();
		var $form = $("#ventilacion_mecanica_form");
		swalCargando.fire({});
		$.ajax({
			url: "{{url('/guardar_ventilacion_mecanica')}}",
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
							$form[0].reset();
							enviado = false;
						//	location.reload();
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
						enviado = false;
							$form[0].reset();
						//	location.reload();
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
	$("#fecha_vm").datetimepicker({
		format: 'DD-MM-YYYY'
	});
	$("#hora_vm").datetimepicker({
		format: 'HH:mm'
	});
	$("#cambio_filtro_vm").datetimepicker({
		format: 'DD-MM-YYYY'
	});
	$("#cambio_set_vm").datetimepicker({
		format: 'DD-MM-YYYY'
	});
	
	$("#desde_vm").datetimepicker({
		format: 'DD-MM-YYYY'
	});
	$("#hasta_vm").datetimepicker({
		format: 'DD-MM-YYYY'
	});
	
	$("#fecha_vm").data("DateTimePicker").date(new Date({{date('Y')}}, {{date('m') - 1}}, {{date('d')}}))
	
	$("#tabla_lista_ventilacion_mecanica").DataTable();
	
	$("#tabla_vm_form").bootstrapValidator({
		
	}).on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	}).on("success.form.bv", function(evt){
		evt.preventDefault();
		var $form = $("#tabla_vm_form");
		swalCargando.fire({});
		listar_ventilaciones($form);
	});
	function cargarTablaVentilacionMecanica(datos){
		var tabla = $("#tabla_lista_ventilacion_mecanica").DataTable();
		tabla.clear();
		
		for(var i = 0; i < datos.length; i++){
			tabla.row.add([datos[i].fecha_toma,crearDatos1(datos[i]),crearDatos2(datos[i]),datos[i].nombre_usuario,crearBotonEliminar(datos[i].id_formulario_ventilacion_mecanica)]);
		}
		tabla.draw();
	}
	
	function crearDatos1(fila){
		var dato = "";
		
		dato += crearDato("Intubación",fila.intubacion);
		dato += crearDato("Modalidad",fila.modalidad);
		dato += crearDato("FiO2",fila.fio2);
		dato += crearDato("F. programada",fila.f_programada);
		dato += crearDato("F. real",fila.f_real);
		dato += crearDato("V.C. programado",fila.v_c_programado);
		dato += crearDato("V.C. real",fila.v_c_real);
		dato += crearDato("V. minuto",fila.v_minuto);
		dato += crearDato("Pr. vía aérea",fila.pr_via_aerea);
		dato += crearDato("PEEP",fila.peep);
		dato += crearDato("Pr. soporte",fila.pr_soporte);
		
		return dato;
	}
	function crearDatos2(fila){
		var dato = "";
		
		dato += crearDato("Sensibilidad",fila.sensibilidad);
		dato += crearDato("BIPAP",fila.bipap);
		dato += crearDato("IPAP",fila.ipap);
		dato += crearDato("EPAP",fila.epap);
		dato += crearDato("Flujo O2",fila.flujo_o2);
		dato += crearDato("TON/TET nro.",fila.ton_tet);
		dato += crearDato("Cánula TQT nº",fila.canula_tqt);
		dato += crearDato("Fijación de tubo cm",fila.fijación_tubo);
		dato += crearDato("Días VM/VMNI",fila.dias_vm_vmni);
		dato += crearDato("Cambio de filtro",fila.cambio_filtro);
		dato += crearDato("Cambio set VM",fila.cambio_set_vm);
		
		return dato;
	}
	
	function crearDato(nombre,valor){
		return "<b>" + nombre + ":</b>&nbsp;<span>" + valor + "</span><br>";
	}
	function crearBotonEliminar(id){
		return "<button class='btn btn-danger eliminar_vm' type='button' data-id='" + id + "'>Eliminar</button>";
	}
	function listar_ventilaciones($form){
		$.ajax({
			url: "{{url('/listar_ventilacion_mecanica')}}",
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
				
				cargarTablaVentilacionMecanica(data);
				
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
	}
	function eliminar(id){
		$.ajax({
			url: "{{url('/eliminar_ventilacion_mecanica')}}",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {
				id: id
			},
			async: true,
			success: function(data){

				Swal.hideLoading();
				swalCargando.close();
				Swal.hideLoading();
				
				if(!data.error){
					swalExito.fire({
					title: 'Exito!',
					text:"Se ha eliminado correctamente",
						didOpen: function() {
						setTimeout(function() {
							
						}, 2000)
					},
					});
				}
				else{
					swalError.fire({
					title: 'Error',
					text:data.msg
					}).then(function(result) {
					
					});
				}
				
			},
			error: function(error){
				// swalCargando.close();
				Swal.hideLoading();
				swalCargando.close();
				Swal.hideLoading();
				console.log(error);
			}
		});
	}
	$(document).on("click",".eliminar_vm",function(){
		eliminar($(this).data("id"));
	});
});
</script>

<style>
	#ventilacion_mecanica_form .row{
		margin-bottom: 15px;
	}
	
	.formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

</style>

<div class="row">
	<div class="col-md-12 formulario">
		<div class="panel panel-default">
			<div class="panel-heading panel-info">
				<h4>VENTILACIÓN MECÁNICA</h4>
			</div>
			<div class="panel-body">
				<form id="ventilacion_mecanica_form">
					<input type="hidden" name="caso_vm" value="{{$caso}}">
					<input type="hidden" name="id_paciente_vm" value="{{$infoPaciente->id}}">
				<fieldset>
					<legend>Ingresar nueva ventilación mecánica</legend>
					<div class="row">
						<div class="col-md-3">
							<label>
								Fecha
								<input type="text" name="fecha_vm" id="fecha_vm" class="form-control" readonly>	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Hora
								<input type="text" name="hora_vm" id="hora_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								Intubación
								<input type="text" name="intubacion_vm" id="intubacion_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Modalidad
								<input type="text" name="modalidad_vm" id="modalidad_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								FiO2
								<input type="number" name="fio2_vm" id="fio2_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								F. programada
								<input type="text" name="f_programada_vm" id="f_programada_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								F. real
								<input type="text" name="f_real_vm" id="f_real_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								V.C. programado
								<input type="number" name="vc_programado_vm" id="vc_programado_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								V.C. real
								<input type="number" name="vc_real_vm" id="vc_real_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								V. minuto
								<input type="number" name="v_minuto_vm" id="v_minuto_vm" class="form-control" step="any">	
							</label>
						</div>

					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								Pr. vía aérea
								<input type="number" name="pr_via_aerea_vm" id="pr_via_aerea_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								PEEP
								<input type="number" name="peep_vm" id="peep_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Pr. soporte
								<input type="number" name="pr_soporte_vm" id="pr_soporte_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Sensibilidad
								<input type="number" name="sensibilidad_vm" id="sensibilidad_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								BIPAP
								<input type="number" name="bipap_vm" id="bipap_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								IPAP
								<input type="number" name="ipap_vm" id="ipap_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								EPAP
								<input type="number" name="epap_vm" id="epap_vm" class="form-control" step="any">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Flujo O2
								<input type="number" name="flujo_o2_vm" id="flujo_o2_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								TON/TET nro.
								<input type="text" name="ton_tet_nro_vm" id="ton_tet_nro_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Cánula TQT nº
								<input type="text" name="canula_tqt_n_vm" id="canula_tqt_n_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Fijación de tubo cm
								<input type="number" name="fijacion_tubo_cm_vm" id="fijacion_tubo_cm_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Días VM/VMNI
								<input type="number" name="dias_vm_vmni_vm" id="dias_vm_vmni_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								Cambio de filtro
								<input type="text" name="cambio_filtro_vm" id="cambio_filtro_vm" class="form-control">	
							</label>
						</div>
						<div class="col-md-3">
							<label>
								Cambio set VM
								<input type="text" name="cambio_set_vm" id="cambio_set_vm" class="form-control">	
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary">Guardar</button>
						</div>
					</div>
				</fieldset>
				</form>
				<fieldset>
					<legend>Listado de ventilaciones mecánicas</legend>
					<div class="row">
						<form id="tabla_vm_form">
							<input type="hidden" name="caso" id="caso_lista" value="{{$caso}}">
							<div class="col-md-1">
								Desde
							</div>
							<div class="col-md-3">
								<input type="text" name="desde_vm" class="form-control" id="desde_vm">
							</div>
							<div class="col-md-1">
								Hasta
							</div>
							<div class="col-md-3">
								<input type="text" name="hasta_vm" class="form-control" id="hasta_vm">
							</div>
							<div class="col-md-3">
								<button type="submit" class="btn btn-default">Buscar</button>
							</div>
						</form>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<table id="tabla_lista_ventilacion_mecanica" class="table table-striped table-bordered table-hover dataTable no-footer">
								<thead>
									<tr>
										<th>Fecha de ingreso</th>
										<th>Datos ventilación mecánica</th>
										<th>Datos ventilación mecánica</th>
										<th>Usuario</th>
										<th>Opciones</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>

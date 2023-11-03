<style>
    .agregarCirugiasPrevias{
        margin-top: 10%;
    }

    .formulario .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>

<br>
<div class="formulario panel panel-default">

    <div class="panel-body">
        <legend>Solicitud Electroencefalograma</legend>
        <button class="btn btn-primary" id="agregarElectroencefalograma">Generar Solicitud</button>
        <br><br>
        <legend>Listado de solicitudes de electroencefalogramas</legend>
        <table id="tableExamenElectroencefalograma" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 25%">Datos solicitud</th>
                    <th style="width: 25%">Datos paciente</th>
                    <th style="width: 25%">Exámenes solicitados</th>
					<th style="width: 25%">Opciones</th>
                </tr>
            </thead>
            <tbody>
    
            </tbody>
        </table>   
    </div>
</div>



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formElectroencefalograma')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoElectroencefalograma')) }}
	<input type="hidden" id="idElectroencefalograma">
    <div id="formularioAgregarElectroencefalograma" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"
    style="overflow-y:auto;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="tituloElectroencefalograma">Formulario Agregar Electroencefalograma <button class="btn btn-danger" id="btnPDFElectroencefalograma" type="button">PDF</button></h4>
                </div>
                <div class="modal-body">
                    
                    <div class="row" id="div_fecha" style="margin-left: auto">
                        <br>
                        <div class="col-md-1" style="pointer-events: none;">
                            <div class="form-group">
                                {{Form::label('FECHA:', null, ['class' => 'control-label'])}}
                                {{-- {{Form::text('fecha_actual', \Carbon\Carbon::now()->format('d-m-Y'), array('id' => 'fecha_actual', 'class' => 'form-control'))}} --}}
                                <p id="fecha_actual">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <legend>Datos Paciente</legend>
					<input type="hidden" id="id_diagnostico_paciente_electroencefalograma" name="id_diagnostico_paciente_electroencefalograma">
                    <div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Nombre</label>
								<input class="form-control" id="nombre_paciente_electroencefalograma" name="nombre_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">RUT</label>
								<input class="form-control" id="rut_paciente_electroencefalograma" name="rut_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Edad</label>
								<input class="form-control" id="edad_paciente_electroencefalograma" name="edad_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                    </div>
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Fecha de nacimiento</label>
								<input class="form-control" id="fecha_nacimiento_paciente_electroencefalograma" name="fecha_nacimiento_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Diagnóstico</label>
                                <input class="form-control" id="diagnostico_paciente_electroencefalograma" name="diagnostico_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
						<div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Comentario</label>
                                <input type="text" id="comentario_diagnostico_paciente_electroencefalograma" class="form-control" name="comentario_diagnostico_paciente_electroencefalograma" autocomplete="off"><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Procedencia</label>
                                <input type="text" id="procedencia_paciente_electroencefalograma" class="form-control" name="procedencia_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                    </div>
					
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Previsión</label>
								<input class="form-control" id="prevision_paciente_electroencefalograma" name="prevision_paciente_electroencefalograma" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" >
                            <div class="form-group">
                                <label class="control-label">Lesión en la neuroimagen: Localización</label>
                                <input class="form-control" id="lesion_localizacion_paciente_electroencefalograma" name="lesion_localizacion_paciente_electroencefalograma" autocomplete="off"><br>
                            </div>
                        </div>
						<div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Intervención quirúrgica: Área</label>
                                <input type="text" id="intervencion_area_paciente_electroencefalograma" class="form-control" name="intervencion_area_paciente_electroencefalograma" autocomplete="off"><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Medicamento</label>
                                <input type="text" id="medicamento_paciente_electroencefalograma" class="form-control" name="medicamento_paciente_electroencefalograma" autocomplete="off"><br>
                            </div>
                        </div>
                    </div>
					
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Fecha última crisis</label>
								<input class="form-control" id="fecha_ultima_crisis_paciente_electroencefalograma" name="fecha_ultima_crisis_paciente_electroencefalograma" autocomplete="off"><br>
                            </div>
                        </div>
                        <div class="form-group col-md-6 "  style="margin-left: 6.5%;">
							<label class="control-label">Lateralidad</label>
							<div class="input-group">
								<div class="radio-inline">
									<label class="control-label">
										<input type="radio" class="" id="diestro_electroencefalograma" name="categoria_lateralidad_electroencefalograma" value="diestro">
									Diestro</label>
								</div>
								<div class="radio-inline">
									<label class="control-label">
										<input type="radio" class="" id="zurdo_electroencefalograma" name="categoria_lateralidad_electroencefalograma" value="zurdo">
									Zurdo</label>
								</div>
								<div class="radio-inline">
									<label class="control-label">
										<input type="radio" class="" id="ninguno_electroencefalograma" name="categoria_lateralidad_electroencefalograma" value="ninguno">
									Ninguno</label>
								</div>
							</div>
                        </div>
                    </div>
					
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-3">
						    <div class="form-group">
								<label class="control-label">Examen solicitado</label>
								<div class="col-md-12">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="reposo" /> Reposo
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="hiperventilacion" /> Hiperventilación
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="fotoestimulacion" /> Fotoestimulación
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="privacion_parcial_sueno_nino" /> Pivación parcial de sueño: niño
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="privacion_total_sueno_adulto" /> Privación total de sueño: adulto
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroencefalograma[]" value="eeg_con_induccion_sueno" /> EEG con inducción sueño
										</label>
									</div>
								</div>
							</div>
                        </div>
                    </div>
					<div class="row" style="margin-left: auto;" id="div_eeg" hidden>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Medicamentos</label>
								<input class="form-control" id="medicamentos_paciente_electroencefalograma" name="medicamentos_paciente_electroencefalograma"><br>
							</div>
						</div>
						<div class="col-md-2 col-md-offset-1" >
							<div class="form-group">
								<label class="control-label">Dosis</label>
								<input class="form-control" id="dosis_paciente_electroencefalograma" name="dosis_paciente_electroencefalograma"><br>
							</div>
						</div>
						<div class="col-md-2 col-md-offset-1">
							<div class="form-group">
								<label class="control-label">Via de administración</label>
								<input type="text" id="via_administracion_paciente_electroencefalograma" class="form-control" name="via_administracion_paciente_electroencefalograma"><br>
							</div>
						</div>
						<div class="col-md-2 col-md-offset-1">
							<div class="form-group">
								<label class="control-label">Horario previo a examen</label>
								<input type="text" id="horario_previo_examen_paciente_electroencefalograma" class="form-control" name="horario_previo_examen_paciente_electroencefalograma"><br>
							</div>
						</div>
					</div>
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnElectroencefalograma', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>
    function validarFormularioSolicitudElectroencefalograma() {
        $("#formElectroencefalograma").bootstrapValidator("revalidateField", "examen_solicitado_electroencefalograma[]");
    }

   var tableElectroencefalograma = null;

    function generarTablaElectroencefalograma() {
       tableElectroencefalograma= $("#tableExamenElectroencefalograma").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/historialElectroencefalograma/{{$caso}}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
			"rowCallback": function(row, data, index){
				var b = $("<button>Ver</button>");
				b.addClass("btn");
				b.addClass("btn-primary");
				b.addClass("center-block");
				b.addClass("ver");
				b.data("id",data["id"]);
				
				var c = $("<button>Eliminar</button>");
				c.addClass("btn");
				c.addClass("btn-danger");
				c.addClass("center-block");
				c.addClass("eliminar");
				c.data("id",data["id"]);
				
				
				var concatenar = function(c,d){
					if(c){
						return ", " + d;
					}
					return d;
				};
				
				var columna2 = "Comentario diagnóstico: " + data["comentario_diagnostico"] + "\n<br>";
				
				columna2 += "Lesión en la neuroimagen: Localización: " + data["lesion_localizacion"] + "\n<br>";
				columna2 += "Intervención quirúrgica: Área: " + data["intervencion_area"] + "\n<br>";
				columna2 += "Medicamento: " + data["medicamento"] + "\n<br>";
				columna2 += "Fecha última crisis: " + data["fecha_ultima_crisis"] + "\n<br>";
				columna2 += "Lateralidad: " + data["lateralidad"] + "\n<br>";
				
				var examen = "";
				
				if(data["reposo"]){
					examen += concatenar(examen,"Reposo");
				}
				if(data["hiperventilacion"]){
					examen += concatenar(examen,"Hiperventilación");
				}
				if(data["fotoestimulacion"]){
					examen += concatenar(examen,"Fotoestimulación");
				}
				if(data["privacion_parcial_sueno_nino"]){
					examen += concatenar(examen,"Pivación parcial de sueño: niño");
				}
				if(data["privacion_total_sueno_adulto"]){
					examen += concatenar(examen,"Privación total de sueño: adulto");
				}
				if(data["eeg_con_induccion_sueno"]){
					examen += concatenar(examen,"EEG con inducción sueño");
				}
				
				$(row).find('td:eq(0)').html("Fecha de emisión: " + data["fecha"] + "\n<br>Usuario responsable: " + data["nombre_usuario"]);
				$(row).find('td:eq(1)').html(columna2);
				
				$(row).find('td:eq(2)').text(examen);
				
				$(row).find('td:eq(3)').empty();
				$(row).find('td:eq(3)').append(b);
				$(row).find('td:eq(3)').append("<br>");
				$(row).find('td:eq(3)').append(c);
            },
			"columns": [
				{"data": "nombre_usuario"},
				{"data": "nombre_usuario"},
                {"data": "nombre_usuario"},
				{"data": "id"}
            ]
			
        });
    }

	function cargarDatosAutomaticosEncefalograma(dato){
		$("#nombre_paciente_electroencefalograma").val(dato.nombre);
		$("#rut_paciente_electroencefalograma").val(dato.rut);
		$("#edad_paciente_electroencefalograma").val(dato.edad);
		//$("#fecha_nacimiento_paciente_electroneuro").val(dato.fecha_nacimiento);
		$("#diagnostico_paciente_electroencefalograma").val(dato.diagnostico);
		$("#id_diagnostico_paciente_electroencefalograma").val(dato.id_diagnostico);
		$("#procedencia_paciente_electroencefalograma").val("Hospitalizado");
		$("#prevision_paciente_electroencefalograma").val(dato.prevision);
		
		$("#fecha_nacimiento_paciente_electroencefalograma").datepicker("update",dato.fecha_nacimiento);
	}
    function datosPacienteElectroencefalograma(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/infoPacienteElectroencefalograma",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
				if(!data.error){
					cargarDatosAutomaticosEncefalograma(data.datos);
				}
            },
            error: function(error){
                console.log("error: ", error);
            }
        });    
    }
	function verElectroencefalograma(dato){
		soloLecturaElectroencefalograma(true);
		
		$("#idElectroencefalograma").val(dato.id);
		$("#nombre_paciente_electroencefalograma").val(dato.nombre);
		$("#rut_paciente_electroencefalograma").val(dato.rut);
		$("#edad_paciente_electroencefalograma").val(dato.edad);
		$("#diagnostico_paciente_electroencefalograma").val(dato.diagnostico);
		$("#id_diagnostico_paciente_electroencefalograma").val(dato.id_diagnostico);
		$("#fecha_nacimiento_paciente_electroencefalograma").datepicker("update",dato.fecha_nacimiento);
		
		$("#comentario_diagnostico_paciente_electroencefalograma").val(dato.comentario_diagnostico);
		$("#procedencia_paciente_electroencefalograma").val(dato.procedencia);
		$("#lesion_localizacion_paciente_electroencefalograma").val(dato.lesion_localizacion);
		$("#intervencion_area_paciente_electroencefalograma").val(dato.intervencion_area);
		$("#medicamento_paciente_electroencefalograma").val(dato.medicamento);
		$("#prevision_paciente_electroencefalograma").val(dato.prevision);
		$("#fecha_ultima_crisis_paciente_electroencefalograma").datepicker("update",dato.fecha_ultima_crisis);
		$("input[name=categoria_lateralidad_electroencefalograma][value=" + dato.lateralidad + "]").prop("checked",true);
		
		//------------------------
		$("input[name='examen_solicitado_electroencefalograma[]']").prop("checked",false);
		if(dato.reposo){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=reposo]").prop("checked",true);
		}
		if(dato.hiperventilacion){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=hiperventilacion]").prop("checked",true);
		}
		if(dato.fotoestimulacion){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=fotoestimulacion]").prop("checked",true);
		}
		if(dato.privacion_parcial_sueno_nino){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=privacion_parcial_sueno_nino]").prop("checked",true);
		}
		if(dato.privacion_total_sueno_adulto){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=privacion_total_sueno_adulto]").prop("checked",true);
		}
		if(dato.eeg_con_induccion_sueno){
			$("input[name='examen_solicitado_electroencefalograma[]'][value=eeg_con_induccion_sueno]").prop("checked",true);
			
		}
		$("input[name='examen_solicitado_electroencefalograma[]'][value=eeg_con_induccion_sueno]").trigger("change");
		
		$("#medicamentos_paciente_electroencefalograma").val(dato.medicamentos);
		$("#dosis_paciente_electroencefalograma").val(dato.dosis);
		$("#via_administracion_paciente_electroencefalograma").val(dato.via_administracion);

		$("#horario_previo_examen_paciente_electroencefalograma").val(dato.horario_previo_examen);
		
		$("#formularioAgregarElectroencefalograma #fecha_actual").text(dato.fecha);
		$("#formularioAgregarElectroencefalograma").modal("show");
	}
	function soloLecturaElectroencefalograma(soloLectura)
	{
		if(soloLectura){
			$("#formElectroencefalograma").find("[type=submit]").hide();
			$("#formElectroencefalograma").find("input").prop("disabled",true);
			$("#tituloElectroencefalograma").html("Formulario Electroencefalograma <button id='btnPDFElectroencefalograma' class='btn btn-danger pdf' type='button'>PDF</button>");
		}
		else{
			$("#formElectroencefalograma").find("[type=submit]").show();
			$("#formElectroencefalograma").find("input").prop("disabled",false);
			$("#tituloElectroencefalograma").html("Formulario Agregar Electroencefalograma");
		}
	}
	function cargarDatosElectroencefalograma(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/cargarElectroencefalograma",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {
				id: id
			},
			success: function(data){
				if(!data.error){
					verElectroencefalograma(data.datos);
				}
				else{
					swalError.fire({
						title: 'Error',
						text:data.msg
					}).then(function(result) {
						if (result.isDenied) {
						}
					});
				}
			},
			error: function(error){
				console.log(error);
			}
		});
	}
	function descargarPDFElectroencefalograma(id){
		var req = new XMLHttpRequest();
		req.open("POST", "{{URL::to('/gestionMedica')}}/pdfElectroencefalograma", true);
		req.responseType = "blob";
		req.setRequestHeader('X-CSRF-TOKEN',$('meta[name="csrf-token"]').attr('content'));
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		

		req.onload = function (event) {
		  var blob = req.response;
		  var link=document.createElement('a');
		  link.href=window.URL.createObjectURL(blob);
		  link.download = "examen_electroencefalograma_{{date('d-m-Y_H-i-s')}}.pdf";
		  link.click();
		};

		req.send("id=" + id);

	}
	function eliminarElectroencefalograma(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/eliminarElectroencefalograma",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "post",
			dataType: "json",
			data: {
				id: id
			},
			success: function(data){
				if(!data.error){
					swalExito.fire({
						title: 'Exito!',
						text: data.msg,
						didOpen: function() {
							setTimeout(function() {
								tableElectroencefalograma.api().ajax.reload();
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
				console.log(error);
			}
		});
	}

    $(function() {
		$(document).on("click","#tableExamenElectroencefalograma .eliminar",function(){
			var t = this;
			swalPregunta.fire({
                title: 'Está seguro que desea eliminar la solicitud?',
            }).then(function(result) {
				if (result.isConfirmed) {
					eliminarElectroencefalograma($(t).data("id"));
				}
			});
		});
		$(document).on("click","#tableExamenElectroencefalograma .ver",function(){
			cargarDatosElectroencefalograma($(this).data("id"));
		});
		$(document).on("click","#btnPDFElectroencefalograma",function(){
			descargarPDFElectroencefalograma($("#idElectroencefalograma").val());
		});
		$("input[name='examen_solicitado_electroencefalograma[]']").on("change",function(){
			if($(this).val() == "eeg_con_induccion_sueno" && $(this).is(":checked") && $("#edad_paciente_electroencefalograma").val() < 18){
				$("#div_eeg").prop("hidden",false);
			}
			else{
				$("#div_eeg").prop("hidden",true);
			}
		});
		$("#fecha_nacimiento_paciente_electroencefalograma,#fecha_ultima_crisis_paciente_electroencefalograma").datepicker({
			format: "dd-mm-yyyy",
			autoclose: true
		});
		$("#horario_previo_examen_paciente_electroencefalograma").datetimepicker({
			format: 'HH:mm'
		});
		
        generarTablaElectroencefalograma();

        $('#formularioAgregarElectroencefalograma').on('shown.bs.modal', function () {
            validarFormularioSolicitudElectroencefalograma();
        });

        $("#agregarElectroencefalograma").click(function() {
            var caso = "{{$caso}}";
			soloLecturaElectroencefalograma(false);
			$("#formularioAgregarElectroencefalograma #fecha_actual").text("{{date('d-m-Y')}}");
            datosPacienteElectroencefalograma();
            $("#idCasoElectroencefalograma").val(caso);
            // cargarUltimaIndicacion();
            $("#formularioAgregarElectroencefalograma").modal("show");
        });


        $("#formularioAgregarElectroencefalograma").on("hidden.bs.modal", function(){
            $('#formElectroencefalograma').trigger('reset');
			$('#formElectroencefalograma').find("[type=submit]").prop("disabled",false);
			$('#formElectroencefalograma').find("#examen_solicitado_electroencefalograma").datepicker("update","");
        });

        $("#btnElectroencefalograma").on("click", function() {
			
			
        });


        $("#formElectroencefalograma").bootstrapValidator({
            excluded: [ ':hidden', ':not(:visible)'],
            fields: {
                "examen_solicitado_electroencefalograma[]": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            evt.preventDefault(evt);
			
			swalPregunta.fire({
                title: '¿Está seguro de agregar el la solicitud?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $("#btnIndicaciones").attr('disabled', 'disabled');
                    var $form = $(evt.target);
                    // swalCargando.fire({});
                    $.ajax({
                        url: "{{URL::to('/gestionMedica')}}/agregarElectroencefalograma",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        async: false,
                        success: function(data){
                            if(!data.error){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.msg,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            $("#formularioAgregarElectroencefalograma").modal('hide');
											tableElectroencefalograma.api().ajax.reload();
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

                            if(data.errores){
                                let ul = '';
                                
                                ul = "<ul style='text-align:left'>";
                                $.each( data.errores, function( key, value ) {
                                    ul +="<li style='list-style:none'>"+value+"</li>";
                                });

                                ul += "</ul>";
                                swalError.fire({
                                    title: 'Error',
                                    html:ul
                                });
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }
            });
        });
    });


</script>


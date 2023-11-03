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
        <legend>Otros exámenes de imágenes</legend>
        <button class="btn btn-primary" id="agregarSolicitudExamen">Generar Solicitud</button>
        <br><br>
        <legend>Listado de otros exámenes de imágenes</legend>
        <table id="tableSolicitudExamen" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 25%">Datos solicitud</th>
                    <th style="width: 25%">Categoría</th>
                    <th style="width: 25%">Examen asociado</th>
					<th style="width: 25%">Opciones</th>
                </tr>
            </thead>
            <tbody>
    
            </tbody>
        </table>   
    </div>
</div>



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formSolicitudExamen')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoSolicitudExamen')) }}
    {{ Form::hidden('id_solicitud_examen', '', array('id' => 'id_solicitud_examen')) }}
    <div id="formularioAgregarSolicitudExamen" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"
    style="overflow-y:auto;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="tituloSolicitudExamen"></h4> 
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
					<input type="hidden" id="id_diagnostico_paciente_solicitud_examen" name="id_diagnostico_paciente_solicitud_examen">
                    <div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Nombre</label>
								<input class="form-control" id="nombre_paciente_solicitud_examen" name="nombre_paciente_solicitud_examen" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">RUT</label>
								<input class="form-control" id="rut_paciente_solicitud_examen" name="rut_paciente_solicitud_examen" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Edad</label>
								<input class="form-control" id="edad_paciente_solicitud_examen" name="edad_paciente_solicitud_examen" readonly><br>
                            </div>
                        </div>
                    </div>
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Fecha de nacimiento</label>
								<input class="form-control" id="fecha_nacimiento_paciente_solicitud_examen" name="fecha_nacimiento_paciente_solicitud_examen" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Diagnóstico</label>
                                <input class="form-control" id="diagnostico_paciente_solicitud_examen" name="diagnostico_paciente_solicitud_examen" readonly><br>
                            </div>
                        </div>
						<div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Peso</label>
                                <input type="number" id="peso_paciente_solicitud_examen" class="form-control" name="peso_paciente_solicitud_examen" step="0.1"><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Talla</label>
                                <input type="number" id="talla_paciente_solicitud_examen" class="form-control" name="talla_paciente_solicitud_examen" step="0.1"><br>
                            </div>
                        </div>
                    </div>
					
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Categoría de prioridad</label><br>
                                <label>
									<input type="radio" name="categoria_prioridad_solicitud_examen" value="urgente" class="control-form" required>
									Urgente
								</label>
								<label>
									<input type="radio" name="categoria_prioridad_solicitud_examen" value="medio_urgente" class="control-form">
									Medio urgente de 1 a 2 meses
								</label>
								<label>
									<input type="radio" name="categoria_prioridad_solicitud_examen" value="puede_esperar" class="control-form">
									Puede esperar más de 3 meses
								</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-md-offset-1">
						    <div class="form-group">
								<label class="control-label">Examen solicitado</label>
								<div class="col-md-12">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_solicitud_examen[]" value="ecocardiograma" /> Ecocardiograma 2 doppler color 2-D
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_solicitud_examen[]" value="test_esfuerzo" /> Test de esfuerzo
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_solicitud_examen[]" value="holter_presion" /> Holter de presión
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_solicitud_examen[]" value="holter_arritmia" /> Holter de arritmia
										</label>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnSolicitudExamen', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>
    function validarFormularioSolicitudExamen() {
        $("#formSolicitudExamen").bootstrapValidator("revalidateField", "examen_solicitado_solicitud_examen[]");
		$("#formSolicitudExamen").bootstrapValidator("revalidateField", "peso_paciente_solicitud_examen");
		$("#formSolicitudExamen").bootstrapValidator("revalidateField", "talla_paciente_solicitud_examen");
		$("#formSolicitudExamen").bootstrapValidator("revalidateField", "categoria_prioridad_solicitud_examen");
    }

   var tableSolicitudExamen = null;

    function generarTablaSolicitudExamen() {
       tableSolicitudExamen= $("#tableSolicitudExamen").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/historialSolicitudExamen/{{$caso}}" ,
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
				
				/* var d = $("<button>PDF</button>");
				d.addClass("btn");
				d.addClass("btn-secondary");
				d.addClass("center-block");
				d.addClass("pdf");
				d.data("id",data["id"]); */
				
				var concatenar = function(c,d){
					if(c){
						return ", " + d;
					}
					return d;
				};
				
				var categoria = "";
				
				if(data["urgente"]){
					categoria += concatenar(categoria,"Urgente");
				}
				if(data["medio_urgente"]){
					categoria += concatenar(categoria,"Medio urgente de 1 a 2 meses");
				}
				if(data["puede_esperar"]){
					categoria += concatenar(categoria,"Puede esperar más de 3 meses");
				}
				
				$(row).find('td:eq(1)').text(categoria);
				
				var examen = "";
				
				if(data["ecocardiograma"]){
					examen += concatenar(examen,"Ecocardiograma 2 doppler color 2-D");
				}
				if(data["test_esfuerzo"]){
					examen += concatenar(examen,"Test de esfuerzo");
				}
				if(data["holter_presion"]){
					examen += concatenar(examen,"Holter de presión");
				}
				if(data["holter_arritmia"]){
					examen += concatenar(examen,"Holter de arritmia");
				}
				
				$(row).find('td:eq(2)').text(examen);
				
				$(row).find('td:eq(0)').text("Fecha de emisión: " + data["fecha"] + "\n" + "Usuario responsable: " + data["nombre_usuario"]);
				
				$(row).find('td:eq(3)').empty();
				$(row).find('td:eq(3)').append(b);
				$(row).find('td:eq(3)').append("<br>");
				$(row).find('td:eq(3)').append(c);
				/* $(row).find('td:eq(3)').append("<br>");
				$(row).find('td:eq(3)').append(d); */
            },
			"columns": [
				{"data": "nombre_usuario"},
				{"data": "nombre_usuario"},
                {"data": "nombre_usuario"},
				{"data": "id"}
            ]
			
        });
    }

	function cargarDatosAutomaticosSolicitudExamen(dato){
		$("#nombre_paciente_solicitud_examen").val(dato.nombre);
		$("#rut_paciente_solicitud_examen").val(dato.rut);
		$("#edad_paciente_solicitud_examen").val(dato.edad);
		//$("#fecha_nacimiento_paciente_electroneuro").val(dato.fecha_nacimiento);
		$("#diagnostico_paciente_solicitud_examen").val(dato.diagnostico);
		$("#id_diagnostico_paciente_solicitud_examen").val(dato.id_diagnostico);
		if($("#peso_paciente_solicitud_examen").val() == ""){
			$("#peso_paciente_solicitud_examen").val(dato.peso);
		}
		if($("#talla_paciente_solicitud_examen").val() == ""){
			$("#talla_paciente_solicitud_examen").val(dato.talla);
		}
		
		$("#fecha_nacimiento_paciente_solicitud_examen").datepicker("update",dato.fecha_nacimiento);
	}
    function datosPacienteSolicitudExamen(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/infoPacienteSolicitudExamen",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
				if(!data.error){
					cargarDatosAutomaticosSolicitudExamen(data.datos);
				}
            },
            error: function(error){
                console.log("error: ", error);
            }
        });    
    }
	function verSolicitudExamen(dato){
		soloLecturaSolicitudExamen(true);
		
		$("#id_solicitud_examen").val(dato.id);
		$("#nombre_paciente_solicitud_examen").val(dato.nombre);
		$("#rut_paciente_solicitud_examen").val(dato.rut);
		$("#edad_paciente_solicitud_examen").val(dato.edad);
		$("#diagnostico_paciente_solicitud_examen").val(dato.diagnostico);
		$("#id_diagnostico_paciente_solicitud_examen").val(dato.id_diagnostico);
		$("#peso_paciente_solicitud_examen").val(dato.peso);
		$("#talla_paciente_solicitud_examen").val(dato.talla);
		$("#fecha_nacimiento_paciente_solicitud_examen").datepicker("update",dato.fecha_nacimiento);
		
		$("input[name='categoria_prioridad_solicitud_examen']").prop("checked",false);
		if(dato.urgente){
			$("input[name='categoria_prioridad_solicitud_examen'][value=urgente]").prop("checked",true);
		}
		if(dato.medio_urgente){
			$("input[name='categoria_prioridad_solicitud_examen'][value=medio_urgente]").prop("checked",true);
		}
		if(dato.puede_esperar){
			$("input[name='categoria_prioridad_solicitud_examen'][value=puede_esperar]").prop("checked",true);
		}

		//------------------------
		$("input[name='examen_solicitado_solicitud_examen[]']").prop("checked",false);
		if(dato.ecocardiograma){
			$("input[name='examen_solicitado_solicitud_examen[]'][value=ecocardiograma]").prop("checked",true);
		}
		if(dato.test_esfuerzo){
			$("input[name='examen_solicitado_solicitud_examen[]'][value=test_esfuerzo]").prop("checked",true);
		}
		if(dato.holter_presion){
			$("input[name='examen_solicitado_solicitud_examen[]'][value=holter_presion]").prop("checked",true);
		}
		if(dato.holter_arritmia){
			$("input[name='examen_solicitado_solicitud_examen[]'][value=holter_arritmia]").prop("checked",true);
		}

		$("#formularioAgregarSolicitudExamen #fecha_actual").text(dato.fecha);
		$("#formularioAgregarSolicitudExamen").modal("show");
	}
	function soloLecturaSolicitudExamen(soloLectura)
	{
		if(soloLectura){
			console.log("editar");
			$("#formSolicitudExamen").find("[type=submit]").hide();
			$("#formSolicitudExamen").find("input").prop("disabled",true);
			$("#tituloSolicitudExamen").html("Formulario Solicitud de Examen<button id='btnPDFSolicitudExamen' class='btn btn-danger pdf' type='button'>PDF</button>");
			//$("#btnPDF").show();
		}
		else{
			console.log("agregar");
			$("#formSolicitudExamen").find("[type=submit]").show();
			$("#formSolicitudExamen").find("input").prop("disabled",false);
			$("#tituloSolicitudExamen").html("Formulario Agregar Otros Exámenes De Imágenes");
			//$("#btnPDF").hide();
		}
	}
	function cargarDatosSolicitudExamen(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/cargarSolicitudExamen",
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
					verSolicitudExamen(data.datos);
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
	function descargarPDFSolicitudExamen(id){
		var req = new XMLHttpRequest();
		req.open("POST", "{{URL::to('/gestionMedica')}}/pdfSolicitudExamen", true);
		req.responseType = "blob";
		req.setRequestHeader('X-CSRF-TOKEN',$('meta[name="csrf-token"]').attr('content'));
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		

		req.onload = function (event) {
		  var blob = req.response;
		  var link=document.createElement('a');
		  link.href=window.URL.createObjectURL(blob);
		  link.download = "solicitud_examen_{{date('d-m-Y_H-i-s')}}.pdf";
		  link.click();
		};

		req.send("id=" + id);

	}
	function eliminarSolicitudExamen(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/eliminarSolicitudExamen",
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
								tableSolicitudExamen.api().ajax.reload();
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
		$(document).on("click","#tableSolicitudExamen .eliminar",function(){
			var t = this;
			swalPregunta.fire({
                title: 'Está seguro que desea eliminar la solicitud?',
            }).then(function(result) {
				if (result.isConfirmed) {
					eliminarSolicitudExamen($(t).data("id"));
				}
			});
		});
		$(document).on("click","#tableSolicitudExamen .ver",function(){
			cargarDatosSolicitudExamen($(this).data("id"));
		});
		$(document).on("click","#btnPDFSolicitudExamen",function(){
			descargarPDFSolicitudExamen($("#id_solicitud_examen").val());
			//descargarPDF($(this).data("id"));

		});
		$("#fecha_nacimiento_paciente_solicitud_examen").datepicker({
			format: "dd-mm-yyyy"
		});
		
        generarTablaSolicitudExamen();

        $('#formularioAgregarSolicitudExamen').on('shown.bs.modal', function () {
            validarFormularioSolicitudExamen();
        });

        $("#agregarSolicitudExamen").click(function() {
            var caso = "{{$caso}}";
			soloLecturaSolicitudExamen(false);
			$("#formularioAgregarSolicitudExamen #fecha_actual").text("{{date('d-m-Y')}}");
            datosPacienteSolicitudExamen();
            $("#idCasoSolicitudExamen").val(caso);
            // cargarUltimaIndicacion();
            $("#formularioAgregarSolicitudExamen").modal("show");
        });


        $("#formularioAgregarSolicitudExamen").on("hidden.bs.modal", function(){
            $('#formSolicitudExamen').trigger('reset');
			$('#formSolicitudExamen').find("[type=submit]").prop("disabled",false);
			$('#formSolicitudExamen').find("#examen_solicitado_solicitud_examen").datepicker("update","");
        });

        $("#btnSolicitudExamen").on("click", function() {
			
			
        });


        $("#formSolicitudExamen").bootstrapValidator({
            excluded: [ ':hidden', ':not(:visible)'],
            fields: {
                "examen_solicitado_solicitud_examen[]": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"peso_paciente_solicitud_examen": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"talla_paciente_solicitud_examen": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"categoria_prioridad_solicitud_examen": {
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
                        url: "{{URL::to('/gestionMedica')}}/agregarSolicitudExamen",
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
                                            $("#formularioAgregarSolicitudExamen").modal('hide');
											tableSolicitudExamen.api().ajax.reload();
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


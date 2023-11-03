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
        <legend>Solicitud Examen Electromiografía y Neuroconducción</legend>
        <button class="btn btn-primary" id="agregarElectroNeuro">Generar Solicitud</button>
        <br><br>
        <legend>Listado de solicitudes de examenes de electromiografía y neuroconducción</legend>
        <table id="tableExamenElectroNeuro" class="table table-striped table-bordered table-hover">
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



{{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formElectroNeuro')) }}
    {{ Form::hidden('idCaso', '', array('class' => 'idCaso', 'id' => 'idCasoElectroNeuro')) }}
    {{ Form::hidden('id_electroneuro', '', array('id' => 'id_electroneuro')) }}
    <div id="formularioAgregarElectroNeuro" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"
    style="overflow-y:auto;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="tituloElectro"></h4> 
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
					<input type="hidden" id="id_diagnostico_paciente_electroneuro" name="id_diagnostico_paciente_electroneuro">
                    <div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Nombre</label>
								<input class="form-control" id="nombre_paciente_electroneuro" name="nombre_paciente_electroneuro" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">RUT</label>
								<input class="form-control" id="rut_paciente_electroneuro" name="rut_paciente_electroneuro" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Edad</label>
								<input class="form-control" id="edad_paciente_electroneuro" name="edad_paciente_electroneuro" readonly><br>
                            </div>
                        </div>
                    </div>
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Fecha de nacimiento</label>
								<input class="form-control" id="fecha_nacimiento_paciente_electroneuro" name="fecha_nacimiento_paciente_electroneuro" readonly><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="pointer-events: none;">
                            <div class="form-group">
                                <label class="control-label">Diagnóstico</label>
                                <input class="form-control" id="diagnostico_paciente_electroneuro" name="diagnostico_paciente_electroneuro" readonly><br>
                            </div>
                        </div>
						<div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Peso</label>
                                <input type="number" id="peso_paciente_electroneuro" class="form-control" name="peso_paciente_electroneuro" step="0.1"><br>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1">
                            <div class="form-group">
                                <label class="control-label">Talla</label>
                                <input type="number" id="talla_paciente_electroneuro" class="form-control" name="talla_paciente_electroneuro" step="0.1"><br>
                            </div>
                        </div>
                    </div>
					
					<div class="row" style="margin-left: auto;">
                        <br>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Categoría de prioridad</label><br>
                                <label>
									<input type="radio" name="categoria_prioridad" value="urgente" class="control-form" required>
									Urgente
								</label>
								<label>
									<input type="radio" name="categoria_prioridad" value="medio_urgente" class="control-form">
									Medio urgente de 1 a 2 meses
								</label>
								<label>
									<input type="radio" name="categoria_prioridad" value="puede_esperar" class="control-form">
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
											<input type="checkbox" name="examen_solicitado_electroneuro[]" value="extremidades_superiores" /> Emg-ng extremidades superiores
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroneuro[]" value="extremidades_inferiores" /> Test extremidades inferiores
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroneuro[]" value="facial" /> Test facial
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroneuro[]" value="cuatro_extremidades" /> Test 4 extremidades
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="examen_solicitado_electroneuro[]" value="test_estimulacion" /> Test de estimulacion repetitiva
										</label>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnElectroNeuro', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

<script>
    function validarFormularioSolicitudElectroNeuro() {
        $("#formElectroNeuro").bootstrapValidator("revalidateField", "examen_solicitado_electroneuro[]");
		$("#formElectroNeuro").bootstrapValidator("revalidateField", "peso_paciente_electroneuro");
		$("#formElectroNeuro").bootstrapValidator("revalidateField", "talla_paciente_electroneuro");
		$("#formElectroNeuro").bootstrapValidator("revalidateField", "categoria_prioridad");
    }

   var tableElectroNeuro = null;

    function generarTablaElectroNeuro() {
       tableElectroNeuro= $("#tableExamenElectroNeuro").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/historialElectroNeuro/{{$caso}}" ,
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
				}
				
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
				
				if(data["extremidades_superiores"]){
					examen += concatenar(examen,"emg-ng extremidades superiores");
				}
				if(data["extremidades_inferiores"]){
					examen += concatenar(examen,"test extremidades inferiores");
				}
				if(data["facial"]){
					examen += concatenar(examen,"test Facial");
				}
				if(data["cuatro_extremidades"]){
					examen += concatenar(examen,"test 4 extremidades");
				}
				if(data["test_estimulacion"]){
					examen += concatenar(examen,"test de estimulacion repetitiva");
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

	function cargarDatosAutomaticos(dato){
		$("#nombre_paciente_electroneuro").val(dato.nombre);
		$("#rut_paciente_electroneuro").val(dato.rut);
		$("#edad_paciente_electroneuro").val(dato.edad);
		//$("#fecha_nacimiento_paciente_electroneuro").val(dato.fecha_nacimiento);
		$("#diagnostico_paciente_electroneuro").val(dato.diagnostico);
		$("#id_diagnostico_paciente_electroneuro").val(dato.id_diagnostico);
		if($("#peso_paciente_electroneuro").val() == ""){
			$("#peso_paciente_electroneuro").val(dato.peso);
		}
		if($("#talla_paciente_electroneuro").val() == ""){
			$("#talla_paciente_electroneuro").val(dato.talla);
		}
		
		$("#fecha_nacimiento_paciente_electroneuro").datepicker("update",dato.fecha_nacimiento);
	}
    function datosPacienteElectroNeuro(){
        var caso = "{{$caso}}";
        $.ajax({
            url: "{{URL::to('/gestionMedica')}}/"+caso+"/infoPacienteElectroNeuro",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
				if(!data.error){
					cargarDatosAutomaticos(data.datos);
				}
            },
            error: function(error){
                console.log("error: ", error);
            }
        });    
    }
	function ver(dato){
		soloLectura(true);
		
		$("#id_electroneuro").val(dato.id);
		$("#nombre_paciente_electroneuro").val(dato.nombre);
		$("#rut_paciente_electroneuro").val(dato.rut);
		$("#edad_paciente_electroneuro").val(dato.edad);
		$("#diagnostico_paciente_electroneuro").val(dato.diagnostico);
		$("#id_diagnostico_paciente_electroneuro").val(dato.id_diagnostico);
		$("#peso_paciente_electroneuro").val(dato.peso);
		$("#talla_paciente_electroneuro").val(dato.talla);
		$("#fecha_nacimiento_paciente_electroneuro").datepicker("update",dato.fecha_nacimiento);
		
		$("input[name='categoria_prioridad']").prop("checked",false);
		if(dato.urgente){
			$("input[name='categoria_prioridad'][value=urgente]").prop("checked",true);
		}
		if(dato.medio_urgente){
			$("input[name='categoria_prioridad'][value=medio_urgente]").prop("checked",true);
		}
		if(dato.puede_esperar){
			$("input[name='categoria_prioridad'][value=puede_esperar]").prop("checked",true);
		}

		//------------------------
		$("input[name='examen_solicitado_electroneuro[]']").prop("checked",false);
		if(dato.extremidades_superiores){
			$("input[name='examen_solicitado_electroneuro[]'][value=extremidades_superiores]").prop("checked",true);
		}
		if(dato.extremidades_inferiores){
			$("input[name='examen_solicitado_electroneuro[]'][value=extremidades_inferiores]").prop("checked",true);
		}
		if(dato.facial){
			$("input[name='examen_solicitado_electroneuro[]'][value=facial]").prop("checked",true);
		}
		if(dato.cuatro_extremidades){
			$("input[name='examen_solicitado_electroneuro[]'][value=cuatro_extremidades]").prop("checked",true);
		}
		if(dato.test_estimulacion){
			$("input[name='examen_solicitado_electroneuro[]'][value=test_estimulacion]").prop("checked",true);
		}
		$("#formularioAgregarElectroNeuro #fecha_actual").text(dato.fecha);
		$("#formularioAgregarElectroNeuro").modal("show");
		$("#formularioAgregarElectroNeuro").modal("show");
	}
	function soloLectura(soloLectura)
	{
		if(soloLectura){
			console.log("editar");
			$("#formElectroNeuro").find("[type=submit]").hide();
			$("#formElectroNeuro").find("input").prop("disabled",true);
			$("#tituloElectro").html("Formulario Examen Electromiografía y Neuroconducción <button id='btnPDF' class='btn btn-danger pdf' type='button'>PDF</button>");
			//$("#btnPDF").show();
		}
		else{
			console.log("agregar");
			$("#formElectroNeuro").find("[type=submit]").show();
			$("#formElectroNeuro").find("input").prop("disabled",false);
			$("#tituloElectro").html("Formulario Agregar Examen Electromiografía y Neuroconducción");
			//$("#btnPDF").hide();
		}
	}
	function cargarDatos(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/cargarElectroNeuro",
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
					ver(data.datos);
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
	function descargarPDF(id){
		var req = new XMLHttpRequest();
		req.open("POST", "{{URL::to('/gestionMedica')}}/pdfElectroNeuro", true);
		req.responseType = "blob";
		req.setRequestHeader('X-CSRF-TOKEN',$('meta[name="csrf-token"]').attr('content'));
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		

		req.onload = function (event) {
		  var blob = req.response;
		  var link=document.createElement('a');
		  link.href=window.URL.createObjectURL(blob);
		  link.download = "examen_electromiografia_neuroconduccion_{{date('d-m-Y_H-i-s')}}";
		  link.click();
		};

		req.send("id=" + id);

	}
	function eliminarElectroNeuro(id){
		$.ajax({
			url: "{{URL::to('/gestionMedica')}}/eliminarElectroNeuro",
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
								tableElectroNeuro.api().ajax.reload();
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
		$(document).on("click","#tableExamenElectroNeuro .eliminar",function(){
			var t = this;
			swalPregunta.fire({
                title: 'Está seguro que desea eliminar la solicitud?',
            }).then(function(result) {
				if (result.isConfirmed) {
					eliminarElectroNeuro($(t).data("id"));
				}
			});
		});
		$(document).on("click","#tableExamenElectroNeuro .ver",function(){
			cargarDatos($(this).data("id"));
		});
		$(document).on("click","#btnPDF",function(){
			descargarPDF($("#id_electroneuro").val());
			//descargarPDF($(this).data("id"));

		});
		$("#fecha_nacimiento_paciente_electroneuro").datepicker({
			format: "dd-mm-yyyy"
		});
		
        generarTablaElectroNeuro();

        $('#formularioAgregarElectroNeuro').on('shown.bs.modal', function () {
            validarFormularioSolicitudElectroNeuro();
        });

        $("#agregarElectroNeuro").click(function() {
            var caso = "{{$caso}}";
			soloLectura(false);
			$("#formularioAgregarElectroNeuro #fecha_actual").text("{{date('d-m-Y')}}");
            datosPacienteElectroNeuro();
            $("#idCasoElectroNeuro").val(caso);
            // cargarUltimaIndicacion();
            $("#formularioAgregarElectroNeuro").modal("show");
        });


        $("#formularioAgregarElectroNeuro").on("hidden.bs.modal", function(){
            $('#formElectroNeuro').trigger('reset');
			$('#formElectroNeuro').find("[type=submit]").prop("disabled",false);
			$('#formElectroNeuro').find("#examen_solicitado_electroneuro").datepicker("update","");
        });

        $("#btnElectroNeuro").on("click", function() {
			
			
        });


        $("#formElectroNeuro").bootstrapValidator({
            excluded: [ ':hidden', ':not(:visible)'],
            fields: {
                "examen_solicitado_electroneuro[]": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"peso_paciente_electroneuro": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"talla_paciente_electroneuro": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
				"categoria_prioridad": {
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
                        url: "{{URL::to('/gestionMedica')}}/agregarElectroNeuro",
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
                                            $("#formularioAgregarElectroNeuro").modal('hide');
											tableElectroNeuro.api().ajax.reload();
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


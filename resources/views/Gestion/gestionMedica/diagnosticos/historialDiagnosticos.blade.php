<script>


    function validarFormularioComentarioDiagnostico() {
        $("#formDiagnComentario").bootstrapValidator("revalidateField", "comDiagModal");
	}

    

    function modificarDiagnosticos(idDiagnostico) {
       
	}

    function modificarDiagnosticos(boton) {
        var idDiagnostico = boton.data("id");
        console.log(idDiagnostico);
        $("#modalEditarComentario").modal();
        //obtener el comentario de diagnostico
        var valor=$("#"+idDiagnostico).html();
        $("#comDiagModal").val(valor);
        $("#idDiagn").val(idDiagnostico);
	}

    function cargarDiagnosticoss(){
        //Cargar tabla de diagnosticos con datos
        var caso = "{{$caso}}";
        tabsDiagnosticossMedicos = $("#tabla-diagnostico-paciente").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "destroy":true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/"+caso+"/cargarDiagnosticos",
                type: 'GET'
            },
            "rowCallback": function(row, data, index){
                //dejo abierto por si se necesita
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            }
        });
    }

    $(function() {
        //Recargar la tabla cada vez que se presione sobre el tab de historial
        $("#hRx").click(function(){
            if(typeof tabsDiagnosticossMedicos !== 'undefined'){
                tabsDiagnosticossMedicos.api().ajax.reload();
                //dtpEditar,false
            }else{
                cargarDiagnosticoss();
            }
        });

        //Validar los campos siempre que se abra el modal
        $('#modalEditarComentario').on('shown.bs.modal', function () {
            validarFormularioComentarioDiagnostico();
        });

        //formulario con validacion para editar diagnostico
        $("#formDiagnComentario").bootstrapValidator({
            excluded: [ ':hidden', ':not(:visible)'],
            fields: {
                "comDiagModal": {
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
            console.log("que wa paso?");
            evt.preventDefault(evt);

			swalPregunta.fire({
                title: '¿Está seguro de cambiar el comentario del diagnostico?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $("#btnModificarDiagnostico").attr('disabled', 'disabled');
                    var $form = $(evt.target);
                    // swalCargando.fire({});
                    $.ajax({
                        url: "{{URL::to('/gestionMedica')}}/editarDiagnostico",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        async: false,
                        success: function(data){
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            $("#modalEditarComentario").modal('hide');
											tabsDiagnosticossMedicos.api().ajax.reload();
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

                            if(data.info){
                                swalInfo.fire({
                                    title: 'Información',
                                    text: data.info,
                                    allowOutsideClick: false
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

<style>
    {
       margin-left: -40px !important;
   }
</style>

<br>
<div class="panel panel-default">
   <div class="panel-heading">
       <h4>Historial Diagnósticos</h4>
   </div>
   <div class="panel-body">
        <legend>Diagnósticos</legend>
        <div class="form-group col-md-12">
            <div class="table-responsive">
                <table id="tabla-diagnostico-paciente" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th style="color: black;">Fecha</th>
                        <th style="color: black;">Diagnóstico</th>
                        <th style="color: black;">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
   </div>
</div>

<div id="modalEditarComentario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"> 
	<div class="modal-dialog" style="width: 60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificación del Comentario de Diagnostico</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array("class" => "submitDiagnModf", 'method' => 'post', 'role' => 'form', 'id' => 'formDiagnComentario')) }}
				<div class="row">
					<div class="form-group col-md-12">
                        {{ Form::hidden('idDiagn', '', array('id' => 'idDiagn')) }}
						{{ Form::textarea('comDiagModal', '', array("style" => "width:100%;", "class" => "form-control", 'id' => 'comDiagModal', 'rows' => '3')) }}
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-1">
						{{ Form::submit("Modificar", array("class" => "btn btn-success", 'id' => 'btnModificarDiagnostico')) }}
					</div>
				</div>
				{{ Form::close() }}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

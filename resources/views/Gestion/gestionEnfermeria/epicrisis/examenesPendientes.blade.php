<script>
  function generarTablaExamenesPendientes() {
        tablePendienteExamenesPendientes = $("#tablePCExamenesPendientes").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_examenes_pendientes" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarExamanesPendientes(idInterconsulta) {
        swalPregunta.fire({
        title: "¿Está seguro de eliminar este examen?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idInterconsulta,$('#tipo_examenes_pendientes_form').val(),tablePendienteExamenesPendientes);			
            }else if (result.isDenied) {
                tablePendienteExamenesPendientes.api().ajax.reload();
            }
        }); 

 
    }


    function obtenerExamenesPendientes(idInterconsulta) {
        mostrarModal(idInterconsulta,'tipo_examenes_pendientes','modalModificarExamenesPendientes');
    }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteExamenesPendientes == 'undefined') {
                generarTablaExamenesPendientes();
            }
        });


        $('#fecha_examenes_pendientes').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'fecha_examenes_pendientes');
        });

 
          //aqui modificar//
          var datos_examenes_pendientes = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('examenes_pendientes_group'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_examenes_pendientes/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_examenes_pendientes.initialize();


		$('.examenes_pendientes_group .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_examenes_pendientes.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#examenes_pendientes_item").val(-1);
                $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'examenes_pendientes_epi');
                $(".datos_examenes_pendientes").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#examenes_pendientes_item").val(selection.id);
        $('#examenesPendientesForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'examenes_pendientes_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#examenes_pendientes_epi").val() == '' &&  $("#examenes_pendientes_item").val() != ''){
                $("#examenes_pendientes_item").val('');
            }else{
            $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'examenes_pendientes_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_examenes_pendientes").find("#examenes_pendientes_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarExamenesPendientes(){
            $('#examenes_pendientes_item').val('');
            $('#examenes_pendientes_epi').val('');
            $('#fecha_examenes_pendientes').val('');
            $("#fecha_examenes_pendientes").data("DateTimePicker").date(null);
            $('#examenesPendientesForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.examenes_pendientes_group .typeahead').typeahead('val', '');
            $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'fecha_examenes_pendientes');
            $('#examenesPendientesForm').bootstrapValidator('revalidateField', 'examenes_pendientes_epi');
        }


        function guardarInterconsulta(evt){
            evt.preventDefault(evt);
            var $form = $(evt.target);
                          swalPregunta.fire({
			               title: "¿Está seguro de agregar este examen pendiente?"
		            }).then(function(result){
                        if (result.isConfirmed) {
	                       $.ajax({
                            url: "{{URL::to('/')}}/addcuidadoAlta",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                
                                $("#btnGuardarExamenesPentienes").prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    limpiarExamenesPendientes();
                                    
                                    tablePendienteExamenesPendientes.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tablePendienteExamenesPendientes.api().ajax.reload();
                                }
                            },
                            error: function(error){
                                $("#btnGuardarExamenesPentienes").prop("disabled", false);
                                console.log(error);
                                tablePendienteExamenesPendientes.api().ajax.reload();
                            }
                        });
	                 }else if(result.isDenied){
                                $("#btnGuardarExamenesPentienes").prop("disabled", false);
                     }

                         }); 
        }


        
        $("#examenesPendientesForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'examenes_pendientes_epi': {
                    selector: '#examenes_pendientes_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#examenes_pendientes_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una especialidad"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                }, 
                'fecha_examenes_pendientes':{
                    selector: '#fecha_examenes_pendientes',
                    validators:{
                        notEmpty: {   
                         message: 'Debe ingresar una fecha'
                        }
                    }
                }, 
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarExamenesPentienes").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#examenes_pendientes_epi').val($('#examenes_pendientes_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#examenes_pendientes_item');
                    item_nombre = $('#examenes_pendientes_epi');
                    tabla_actualizar = tablePendienteExamenesPendientes;
                    limpiarDatos = limpiarExamenesPendientes; 
                    botonFormulario = $("#btnGuardarExamenesPentienes");
                    texto = "examen pendiente";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                
                },
                error: function(error){
                    $("#btnGuardarExamenesPentienes").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
<br>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'examenesPendientesForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_examenes_pendientes', array('class' => '', 'id' => 'tipo_examenes_pendientes_form')) }}
    <div class="col-md-12" style="margin-top:20px;">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Examenes Pendientes</legend>
                </div>                
            </div>           

            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Especialidad", array( ))}}</div>
                        <div class="col-md-2 p-0 col-md-offset-1"> {{Form::label('', "Fecha", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group examenes_pendientes_group">
                            {{Form::text('cuidado_epi', null, array('id' => 'examenes_pendientes_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'examenes_pendientes_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1">
                        <div class="form-group">
                            {{Form::text('fecha_creacion', null, array('id' => 'fecha_examenes_pendientes', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarExamenesPentienes">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCExamenesPendientes" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>USUARIO</th>
                            <th>FECHA SOLICITADA</th>
                            <th>EXAMENES PENDIENTES</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}





            <div id="modalModificarExamenesPendientes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalInterconsulta" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos examen pendiente</h4>
				<div class="nombreInterconsulta"></div>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


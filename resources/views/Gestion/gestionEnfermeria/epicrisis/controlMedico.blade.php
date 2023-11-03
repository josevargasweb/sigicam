<script>
  function generarTablaControlMedico() {
        tablePendienteControlMedico = $("#tablePCControlMedico").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_controles_medicos" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarControlMedico(idControlMedico) {
        swalPregunta.fire({
            title: "¿Está seguro de eliminar este control medico?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idControlMedico,$('#tipo_control_medico_form').val(),tablePendienteControlMedico);			
            }else if(result.isDenied){
                tablePendienteControlMedico.api().ajax.reload();
            }
        });
 
    }


    function obtenerControlMedico(idControlMedico) {
        mostrarModal(idControlMedico,'tipo_controles_medicos','modalModificarControl');
      }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteControlMedico == 'undefined') {
                generarTablaControlMedico();
            }
        });


        $('#fecha_control_medico').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#controlMedicoForm').bootstrapValidator('revalidateField', 'fecha_control_medico');
        });

 
          //aqui modificar//
          var datos_control_medico = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('controles_epi'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_controles_medicos/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_control_medico.initialize();


		$('.controles_epi .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_control_medico.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#control_item").val(-1);
                $('#controlMedicoForm').bootstrapValidator('revalidateField', 'control_epi');
                $(".datos_control_medico").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#control_item").val(selection.id);
        $('#controlMedicoForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#controlMedicoForm').bootstrapValidator('revalidateField', 'control_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#control_epi").val() == '' &&  $("#control_item").val() != ''){
                $("#control_item").val('');
            }else{
            $('#controlMedicoForm').bootstrapValidator('revalidateField', 'control_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_control_medico").find("#control_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarControlesMedico(){
            $('#control_item').val('');
            $('#control_epi').val('');
            $('#fecha_control_medico').val('');
            $("#fecha_control_medico").data("DateTimePicker").date(null);
            $('#controlMedicoForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.controles_epi .typeahead').typeahead('val', '');
            $('#controlMedicoForm').bootstrapValidator('revalidateField', 'fecha_control_medico');
            $('#controlMedicoForm').bootstrapValidator('revalidateField', 'control_epi');
        }

        
        $("#controlMedicoForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'control_epi': {
                    selector: '#control_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#control_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una especialidad"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                }, 
                'fecha_control_medico':{
                    selector: '#fecha_control_medico',
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
            $("#btnGuardarControlMedico").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#control_epi').val($('#control_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#control_item');
                    item_nombre = $('#control_epi');
                    tabla_actualizar = tablePendienteControlMedico;
                    limpiarDatos = limpiarControlesMedico; 
                    botonFormulario = $("#btnGuardarControlMedico");
                    texto = "control medico";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                },
                error: function(error){
                    $("#btnGuardarControlMedico").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'controlMedicoForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_controles_medicos', array('class' => '', 'id' => 'tipo_control_medico_form')) }}
    <div class="col-md-12">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Control médico</legend>
                </div>                
            </div>           
    <input type="hidden" value="" name="id_formulario_controles_medicos" id="id_formulario_controles_medicos">

            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Especialidad", array( ))}}</div>
                        <div class="col-md-2 p-0 col-md-offset-1"> {{Form::label('', "Fecha", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group controles_epi">
                            {{Form::text('cuidado_epi', null, array('id' => 'control_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'control_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1">
                        <div class="form-group">
                            {{Form::text('fecha_creacion', null, array('id' => 'fecha_control_medico', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarControlMedico">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCControlMedico" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>USUARIO</th>
                            <th>FECHA SOLICITADA</th>
                            <th>CONTROL MÉDICO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}



<div id="modalModificarControl" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos control medico</h4>
				<div class="nombreIndicacion"></div>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

  
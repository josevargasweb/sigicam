<script>
  function generarTablaInterconsulta() {
        tablePendienteInterconsulta = $("#tablePCInterconsulta").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_interconsulta" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarInterconsulta(idInterconsulta) {
        swalPregunta.fire({
        title: "¿Está seguro de eliminar esta interconsulta?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idInterconsulta,$('#tipo_interconsulta_form').val(),tablePendienteInterconsulta);			
            }else if (result.isDenied) {
                tablePendienteInterconsulta.api().ajax.reload();
            }
        }); 

 
    }


    function obtenerInterconsulta(idInterconsulta) {
        mostrarModal(idInterconsulta,'tipo_interconsulta','modalModificarInterconsulta');
     }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteInterconsulta == 'undefined') {
                generarTablaInterconsulta();
            }
        });


        $('#fecha_interconsulta').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#interconsultaForm').bootstrapValidator('revalidateField', 'fecha_interconsulta');
        });

 
          //aqui modificar//
          var datos_interconsulta = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('interconsulta_group'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_interconsulta/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_interconsulta.initialize();


		$('.interconsulta_group .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_interconsulta.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#interconsulta_item").val(-1);
                $('#interconsultaForm').bootstrapValidator('revalidateField', 'interconsulta_epi');
                $(".datos_interconsulta").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#interconsulta_item").val(selection.id);
        $('#interconsultaForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#interconsultaForm').bootstrapValidator('revalidateField', 'interconsulta_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#interconsulta_epi").val() == '' &&  $("#interconsulta_item").val() != ''){
                $("#interconsulta_item").val('');
            }else{
            $('#interconsultaForm').bootstrapValidator('revalidateField', 'interconsulta_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_interconsulta").find("#interconsulta_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarInterconsulta(){
            $('#interconsulta_item').val('');
            $('#interconsulta_epi').val('');
            $('#fecha_interconsulta').val('');
            $("#fecha_interconsulta").data("DateTimePicker").date(null);
            $('#interconsultaForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.interconsulta_group .typeahead').typeahead('val', '');
            $('#interconsultaForm').bootstrapValidator('revalidateField', 'fecha_interconsulta');
            $('#interconsultaForm').bootstrapValidator('revalidateField', 'interconsulta_epi');
        }
        
        $("#interconsultaForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'interconsulta_epi': {
                    selector: '#interconsulta_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#interconsulta_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una especialidad"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                }, 
                'fecha_interconsulta':{
                    selector: '#fecha_interconsulta',
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
            $("#btnGuardarInterconsulta").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#interconsulta_epi').val($('#interconsulta_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#interconsulta_item');
                    item_nombre = $('#interconsulta_epi');
                    tabla_actualizar = tablePendienteInterconsulta;
                    limpiarDatos = limpiarInterconsulta; 
                    botonFormulario = $("#btnGuardarInterconsulta");
                    texto = "especialidad";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                       },
                error: function(error){
                    $("#btnGuardarInterconsulta").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
<br>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'interconsultaForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_interconsulta', array('class' => '', 'id' => 'tipo_interconsulta_form')) }}
    <div class="col-md-12" style="margin-top:20px;">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Interconsulta</legend>
                </div>                
            </div>           
            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Especialidad", array( ))}}</div>
                        <div class="col-md-2 p-0 col-md-offset-1"> {{Form::label('', "Fecha", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group interconsulta_group">
                            {{Form::text('cuidado_epi', null, array('id' => 'interconsulta_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'interconsulta_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1">
                        <div class="form-group">
                            {{Form::text('fecha_creacion', null, array('id' => 'fecha_interconsulta', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarInterconsulta">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCInterconsulta" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>USUARIO</th>
                            <th>FECHA SOLICITADA</th>
                            <th>INTERCONSULTA</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}





            <div id="modalModificarInterconsulta" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalInterconsulta" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos interconsulta</h4>
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


<script>
  function generarTablaEducacionesRealizadas() {
        tablePendienteEducacionesRealizadas = $("#tablePCEducacionesRealizadas").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_educaciones_realizadas" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarEducacionesRealizadas(idInterconsulta) {
        swalPregunta.fire({
        title: "¿Está seguro de eliminar esta educacion realizada?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idInterconsulta,$('#tipo_educacion_realizada_form').val(),tablePendienteEducacionesRealizadas);
            }else if (result.isDenied) {
                tablePendienteEducacionesRealizadas.api().ajax.reload();
            }
        }); 

 
    }


    function obtenerEducacionesRealizadas(idInterconsulta) {
        mostrarModal(idInterconsulta,'tipo_educaciones_realizadas','modalModificarEducacionesRealizadas');
    }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteEducacionesRealizadas == 'undefined') {
                generarTablaEducacionesRealizadas();
            }
        });



 
          //aqui modificar//
          var datos_educaciones_realizadas = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('educaciones_realizadas_group'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_educaciones_realizadas/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_educaciones_realizadas.initialize();


		$('.educaciones_realizadas_group .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_educaciones_realizadas.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#educaciones_realizadas_item").val(-1);
                $('#educacionesRealizadasForm').bootstrapValidator('revalidateField', 'educaciones_realizadas_epi');
                $(".datos_educaciones_realizadas").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Educación</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#educaciones_realizadas_item").val(selection.id);
        $('#educacionesRealizadasForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#educacionesRealizadasForm').bootstrapValidator('revalidateField', 'educaciones_realizadas_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#educaciones_realizadas_epi").val() == '' &&  $("#educaciones_realizadas_item").val() != ''){
                $("#educaciones_realizadas_item").val('');
            }else{
            $('#educacionesRealizadasForm').bootstrapValidator('revalidateField', 'educaciones_realizadas_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_educaciones_realizadas").find("#educaciones_realizadas_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarEducacionesRealizadas(){
            $('#educaciones_realizadas_item').val('');
            $('#educaciones_realizadas_epi').val('');
            $('#educacionesRealizadasForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.educaciones_realizadas_group .typeahead').typeahead('val', '');
            $('#educacionesRealizadasForm').bootstrapValidator('revalidateField', 'educaciones_realizadas_epi');
        }

        
        $("#educacionesRealizadasForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'educaciones_realizadas_epi': {
                    selector: '#educaciones_realizadas_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#educaciones_realizadas_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una educación"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                }, 
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarEducacionesRealizadas").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#educaciones_realizadas_epi').val($('#educaciones_realizadas_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#educaciones_realizadas_item');
                    item_nombre = $('#educaciones_realizadas_epi');
                    tabla_actualizar = tablePendienteEducacionesRealizadas;
                    limpiarDatos = limpiarEducacionesRealizadas; 
                    botonFormulario = $("#btnGuardarEducacionesRealizadas");
                    texto = "educación realizada";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                },
                error: function(error){
                    $("#btnGuardarEducacionesRealizadas").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
<br>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'educacionesRealizadasForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_educaciones_realizadas', array('class' => '', 'id' => 'tipo_educacion_realizada_form')) }}
    <div class="col-md-12" style="margin-top:20px;">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Educaciones realizadas</legend>
                </div>                
            </div>           

            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Educación", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group educaciones_realizadas_group">
                            {{Form::text('cuidado_epi', null, array('id' => 'educaciones_realizadas_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'educaciones_realizadas_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarEducacionesRealizadas">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCEducacionesRealizadas" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>FECHA Y USUARIO</th>
                            <th>EDUCACIONES REALIZADAS</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}





            <div id="modalModificarEducacionesRealizadas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalInterconsulta" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos educaciones realizadas</h4>
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


<script>
  function generarTablaMedicamentoAlAlta() {
        tablePendienteMedicamentoAlAlta = $("#tablePCMedicamentoAlAlta").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_medicamentos_alta" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarMedicamentoAlta(idInterconsulta) {
        swalPregunta.fire({
        title: "¿Está seguro de eliminar este medicamento?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idInterconsulta,$('#tipo_medicamento_alta_form').val(),tablePendienteMedicamentoAlAlta);			
            }else if (result.isDenied) {
                tablePendienteMedicamentoAlAlta.api().ajax.reload();
            }
        }); 

 
    }


    function obtenerMedicamentosAlta(idInterconsulta) {
        mostrarModal(idInterconsulta,'tipo_medicamentos_alta','modalModificarMedicamentosAlta');
    }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteMedicamentoAlAlta == 'undefined') {
                generarTablaMedicamentoAlAlta();
            }
        });



 
          //aqui modificar//
          var datos_medicamentos_alta = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('medicamentos_alta_group'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_medicamentos_alta/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_medicamentos_alta.initialize();


		$('.medicamentos_alta_group .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_medicamentos_alta.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#medicamento_alta_item").val(-1);
                $('#medicamentosAltaForm').bootstrapValidator('revalidateField', 'medicamento_alta_epi');
                $(".datos_medicamentos_alta").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Medicamento</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#medicamento_alta_item").val(selection.id);
        $('#medicamentosAltaForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#medicamentosAltaForm').bootstrapValidator('revalidateField', 'medicamento_alta_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#medicamento_alta_epi").val() == '' &&  $("#medicamento_alta_item").val() != ''){
                $("#medicamento_alta_item").val('');
            }else{
            $('#medicamentosAltaForm').bootstrapValidator('revalidateField', 'medicamento_alta_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_medicamentos_alta").find("#medicamento_alta_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarMedicamentosAlta(){
            $('#medicamento_alta_item').val('');
            $('#medicamento_alta_epi').val('');
            $('#medicamentosAltaForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.medicamentos_alta_group .typeahead').typeahead('val', '');
            $('#medicamentosAltaForm').bootstrapValidator('revalidateField', 'medicamento_alta_epi');
        }

        
        $("#medicamentosAltaForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'medicamento_alta_epi': {
                    selector: '#medicamento_alta_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#medicamento_alta_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar un medicamento"};

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
            $("#btnGuardarMedicamentosAlta").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#medicamento_alta_epi').val($('#medicamento_alta_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#medicamento_alta_item');
                    item_nombre = $('#medicamento_alta_epi');
                    tabla_actualizar = tablePendienteMedicamentoAlAlta;
                    limpiarDatos = limpiarMedicamentosAlta; 
                    botonFormulario = $("#btnGuardarMedicamentosAlta");
                    texto = "medicamento al alta";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                },
                error: function(error){
                    $("#btnGuardarMedicamentosAlta").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
<br>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'medicamentosAltaForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_medicamentos_alta', array('class' => '', 'id' => 'tipo_medicamento_alta_form')) }}
    <div class="col-md-12" style="margin-top:20px;">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Medicamento al alta</legend>
                </div>                
            </div>           

            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Medicamento", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group medicamentos_alta_group">
                            {{Form::text('cuidado_epi', null, array('id' => 'medicamento_alta_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'medicamento_alta_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarMedicamentosAlta">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCMedicamentoAlAlta" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>FECHA Y USUARIO</th>
                            <th>MEDICAMENTO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}





            <div id="modalModificarMedicamentosAlta" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalInterconsulta" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos medicamento al alta</h4>
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


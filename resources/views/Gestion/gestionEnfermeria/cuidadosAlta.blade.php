<script>
  function generarTablaCuidadosAlta() {
        tablePendienteActualCuidado = $("#tablePCCuidadosalAlta").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_cuidado_alta" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarCuidado(idcuidado) {
        swalPregunta.fire({
            title: "¿Está seguro de eliminar este cuidado al alta?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idcuidado,$('#tipo_cuidado_alta_form').val(),tablePendienteActualCuidado);			
            }else if(result.isDenied){
                tablePendienteActualCuidado.api().ajax.reload();
            }
        });
    }


    function obtenerCuidados(idCuidado) {
        mostrarModal(idCuidado,'tipo_cuidado_alta','modalModificarCuidados');
    }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteActualCuidado !== 'undefined') {
                tablePendienteActualCuidado.api().ajax.reload(false);
            }else{
                generarTablaCuidadosAlta();
            }
        });

          //aqui modificar//
          var datos_cuidado_alta = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_epi'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_cuidado_alta/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_cuidado_alta.initialize();


		$('.tipos_epi .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_cuidado_alta.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#cuidado_item").val(-1);
                if($("#cuidado_epi").val() == ''){
                    $('#cuidadosAltaForm').bootstrapValidator('revalidateField', 'cuidado_epi');
                }
                $(".tipos_epi").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Tipo Cuidado Alta</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#cuidado_item").val(selection.id);
        $('#cuidadosAltaForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#cuidadosAltaForm').bootstrapValidator('revalidateField', 'cuidado_epi');
		}).on('typeahead:change', function(event, selection){
            $('#cuidadosAltaForm').bootstrapValidator('revalidateField', 'cuidado_epi');
            if($("#cuidado_epi").val() == '' &&  $("#cuidado_item").val() != ''){
                $("#cuidado_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_cuidado_alta").find("#cuidado_item");
		if(!$med.val() && $(this).val()){
			// $(this).val("");
            $("#cuidado_item").val(-1);
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//

        function limpiarCuidadosAlta(){
            $('#cuidado_item').val('');
            $('#cuidado_epi').val('');
            $('#cuidadosAltaForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.tipos_epi .typeahead').typeahead('val', '');
            $('#cuidadosAltaForm').bootstrapValidator('revalidateField', 'cuidado_epi');
        }

        
        $("#cuidadosAltaForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_epi': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#cuidado_epi").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar un cuidado de alta"};

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
            $("#btnGuardarCuidadosAlta").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#cuidado_epi').val($('#cuidado_epi').val().trim());
            $.ajax({
                 url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#cuidado_item');
                    item_nombre = $('#cuidado_epi');
                    tabla_actualizar = tablePendienteActualCuidado;
                    limpiarDatos = limpiarCuidadosAlta; 
                    botonFormulario = $("#btnGuardarCuidadosAlta");
                    texto = "cuidado alta";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                },
                error: function(error){
                    $("#btnGuardarCuidadosAlta").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    p {
        font-size: 13px;
    }
    .p-0{
        padding:0;
    }
</style>

<div class="formulario">
    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'cuidadosAltaForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoCuidaosAlta')) }}
    {{ Form::hidden('nombreForm', 'tipo_cuidado_alta', array('class' => '', 'id' => 'tipo_cuidado_alta_form')) }}

    <input type="hidden" value="" name="id_formulario_cuidados_alta" id="id_formulario_cuidados_alta">

 
    <div class="panel panel-default">
        <div class="panel-heading panel-info" style="background-color: #bce8f1 !important;">
            <h4>Cuidados al alta</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-2 p-0">  {{Form::label('', "Nuevo Cuidado", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group tipos_epi">
                            {{Form::text('cuidado_epi', null, array('id' => 'cuidado_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'cuidado_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarCuidadosAlta">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
                <legend>Lista de cuidados</legend>
             
                <table id="tablePCCuidadosalAlta" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>FECHA Y USUARIO</th>
                            <th>CUIDADO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
 
    {{ Form::close() }}
</div>  

<div id="modalModificarCuidados" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos cuidados al alta</h4>
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

  
  
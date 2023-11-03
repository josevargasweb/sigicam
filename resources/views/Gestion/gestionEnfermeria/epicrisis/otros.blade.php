<script>
  function generarTablaOtros() {
        tablePendienteOtros = $("#tablePCOtros").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/')}}/obtenerCuidadosAlta/{{ $caso }}/tipo_otros" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

  

    function eliminarOtros(idInterconsulta) {
        swalPregunta.fire({
        title: "¿Está seguro de eliminar?"
        }).then(function(result){
            if (result.isConfirmed) {
                elimintarFormulario(idInterconsulta,$('#tipo_otro_form').val(),tablePendienteOtros);	
            }else if (result.isDenied) {
                tablePendienteOtros.api().ajax.reload();
            }
        }); 

 
    }


    function obtenerOtros(idInterconsulta) {
        mostrarModal(idInterconsulta,'tipo_otros','modalModificarOtros');
    }




$(function() {

    $("#epicrisis").click(function(){
            if (typeof tablePendienteOtros == 'undefined') {
                generarTablaOtros();
            }
        });



 
          //aqui modificar//
          var datos_otros = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('otro_group'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_otros/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_otros.initialize();


		$('.otro_group .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_otros.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#otro_item").val(-1);
                $('#otrosForm').bootstrapValidator('revalidateField', 'otro_epi');
                $(".datos_otros").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Otro</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#otro_item").val(selection.id);
        $('#otrosForm').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
        $('#otrosForm').bootstrapValidator('revalidateField', 'otro_epi');
		}).on('typeahead:change', function(event, selection){
            if($("#otro_epi").val() == '' &&  $("#otro_item").val() != ''){
                $("#otro_item").val('');
            }else{
            $('#otrosForm').bootstrapValidator('revalidateField', 'otro_epi');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".datos_otros").find("#otro_item");
		if(!$med.val()&&$(this).val()){
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}
		});//aqui modificar//

        function limpiarOtros(){
            $('#otro_item').val('');
            $('#otro_epi').val('');
            $('#otrosForm').find("input[name='seleccionado_cuidado_epi']").val('');
            $('.otro_group .typeahead').typeahead('val', '');
            $('#otrosForm').bootstrapValidator('revalidateField', 'otro_epi');
        }


        function guardarOtros(evt){
            evt.preventDefault(evt);
            var $form = $(evt.target);
                          swalPregunta.fire({
			               title: "¿Está seguro de agregar este elemento?"
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
                                
                                $("#btnGuardarOtros").prop("disabled", false);

                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    limpiarOtros();
                                    
                                    tablePendienteOtros.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    tablePendienteOtros.api().ajax.reload();
                                }
                            },
                            error: function(error){
                                $("#btnGuardarOtros").prop("disabled", false);
                                console.log(error);
                                tablePendienteOtros.api().ajax.reload();
                            }
                        });
	                 }else if(result.isDenied){
                                $("#btnGuardarOtros").prop("disabled", false);
                     }

                         }); 
        }


        
        $("#otrosForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'otro_epi': {
                    selector: '#otro_epi',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#otro_epi").val();
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
            $("#btnGuardarOtros").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#otro_epi').val($('#otro_epi').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    item_id = $('#otro_item');
                    item_nombre = $('#otro_epi');
                    tabla_actualizar = tablePendienteOtros;
                    limpiarDatos = limpiarOtros; 
                    botonFormulario = $("#btnGuardarOtros");
                    texto = "educación";
                    todasLasValidaciones(data,evt,item_id,item_nombre,tabla_actualizar,limpiarDatos,botonFormulario,texto);
                },
                error: function(error){
                    $("#btnGuardarOtros").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });
    });
</script>
<br>
        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'otrosForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoControlMedico')) }}
    {{ Form::hidden('nombreForm', 'tipo_otros', array('class' => '', 'id' => 'tipo_otro_form')) }}
    <div class="col-md-12" style="margin-top:20px;">
                <div class="col-md-12 form-group"  style="margin-bottom: 0px;">
                    <legend>Otros</legend>
                </div>                
            </div>           

            <div class="col-md-12">
            <div class="col-md-12">
                        <div class="col-md-4 p-0">  {{Form::label('', "Especificar", array( ))}}</div>
                    </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class=" form-group otro_group">
                            {{Form::text('cuidado_epi', null, array('id' => 'otro_epi', 'class' => 'form-control typeahead'))}}
                            {{Form::hidden('cuidado_item', null, array('id' => 'otro_item'))}}
                            {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                        </div>
                    </div>
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarOtros">Añadir</button>
                    </div>
                </div>
                    <!-- tabla de cuidados -->
            </div>

            <br><br>

            <div class="col-md-12">
             
                <table id="tablePCOtros" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>FECHA Y USUARIO</th>
                            <th>OTROS</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{ Form::close() }}





            <div id="modalModificarOtros" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalInterconsulta" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Modificar datos otros</h4>
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


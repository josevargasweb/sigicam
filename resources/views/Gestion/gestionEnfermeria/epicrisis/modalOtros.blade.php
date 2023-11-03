<script>
$(function() {

    function load_modificacion_otros(){
        var cuidadoOtros = "{{$cuidadoInfo->id_cuidado}}";
        var tipoCuidadoOtros = "{{$cuidadoInfo->tipo}}";
        if(cuidadoOtros > 0 && tipoCuidadoOtros != ''){
            $("#otros_modificacion_item").val(cuidadoOtros);
            $("#otros_modificacion_alta").val(tipoCuidadoOtros);
        }
    }

    load_modificacion_otros();



    
          //aqui modificar//
          var datos_otros_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_otros'),
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

		datos_otros_modificacion.initialize();


		$('.tipos_modificacion_otros .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_otros_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#otros_modificacion_item").val(-1);
                $(".tipos_modificacion_otros").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Educación</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#otros_modificacion_item").val(selection.id);
        $('#form_modificacion_otros').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#otros_modificacion_alta").val() == '' &&  $("#otros_modificacion_item").val() != ''){
                $("#otros_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_otros").find("#otros_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#otros_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//


        $("#form_modificacion_otros").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#otros_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#otros_modificacion_alta").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una descripción"};

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
            $("#btn_actualizar_modOtros").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#otros_modificacion_alta').val($('#otros_modificacion_alta').val().trim());
            $.ajax({
                url: "{{URL::to('/')}}/validar_cuidados_epicrisis",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    formulario = evt;
                    item_id = $('#otros_modificacion_item');
                    item_nombre = $('#otros_modificacion_alta');
                    tabla_actualizar = tablePendienteOtros;
                    boton_formulario =  $("#btn_actualizar_modOtros");
                    nombre_modal = $("#modalModificarOtros");
                    texto= "otros";

                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);
                },
                error: function(error){
                    $("#btn_actualizar_modOtros").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-otros-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_otros')) }}
        {{ Form::hidden('nombreForm', 'tipo_otros', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-otros-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_otros"> 
                    <label>ESPECIFICAR</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'otros_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'otros_modificacion_item'))}}
                    {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-otros-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modOtros" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


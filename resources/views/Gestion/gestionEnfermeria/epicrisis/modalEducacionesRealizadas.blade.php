<script>
$(function() {

    function load_modificacion_educacionesRealizadas(){
        var cuidadoEducacionRealizada = "{{$cuidadoInfo->id_cuidado}}";
        var tipoCuidadoEducacionRealizada = "{{$cuidadoInfo->tipo}}";
        if(cuidadoEducacionRealizada > 0 && tipoCuidadoEducacionRealizada != ''){
            $("#educacionRealizada_modificacion_item").val(cuidadoEducacionRealizada);
            $("#educacionRealizada_modificacion_alta").val(tipoCuidadoEducacionRealizada);
        }
    }

    load_modificacion_educacionesRealizadas();



    
          //aqui modificar//
          var datos_educacionRealizada_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_educacionRealizada'),
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

		datos_educacionRealizada_modificacion.initialize();


		$('.tipos_modificacion_educacionRealizada .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_educacionRealizada_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#educacionRealizada_modificacion_item").val(-1);
                $(".tipos_modificacion_educacionRealizada").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Educación</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#educacionRealizada_modificacion_item").val(selection.id);
        $('#form_modificacion_educacionRealizada').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#educacionRealizada_modificacion_alta").val() == '' &&  $("#educacionRealizada_modificacion_item").val() != ''){
                $("#educacionRealizada_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_educacionRealizada").find("#educacionRealizada_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#educacionRealizada_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//


        $("#form_modificacion_educacionRealizada").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#educacionRealizada_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#educacionRealizada_modificacion_alta").val();
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
            $("#btn_actualizar_modEducacionRealizada").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#educacionRealizada_modificacion_alta').val($('#educacionRealizada_modificacion_alta').val().trim());
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
                    item_id = $('#educacionRealizada_modificacion_item');
                    item_nombre = $('#educacionRealizada_modificacion_alta');
                    tabla_actualizar = tablePendienteEducacionesRealizadas;
                    boton_formulario =  $("#btn_actualizar_modEducacionRealizada");
                    nombre_modal = $("#modalModificarEducacionesRealizadas");
                    texto= "educaciones realizadas";
                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);
                },
                error: function(error){
                    $("#btn_actualizar_modEducacionRealizada").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-educacion-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_educacionRealizada')) }}
        {{ Form::hidden('nombreForm', 'tipo_educaciones_realizadas', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-educacion-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_educacionRealizada"> 
                    <label>MEDICAMENTO</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'educacionRealizada_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'educacionRealizada_modificacion_item'))}}
                    {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-educacion-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modEducacionRealizada" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


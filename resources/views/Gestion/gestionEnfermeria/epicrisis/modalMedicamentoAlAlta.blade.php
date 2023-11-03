<script>
$(function() {

    function load_modificacion_medicamentoAlta(){
        var cuidadoMedicamentoAlta = "{{$cuidadoInfo->id_cuidado}}";
        var tipoCuidadoMedicamentoAlta = "{{$cuidadoInfo->tipo}}";
        if(cuidadoMedicamentoAlta > 0 && tipoCuidadoMedicamentoAlta != ''){
            $("#medicamentoAlta_modificacion_item").val(cuidadoMedicamentoAlta);
            $("#medicamentoAlta_modificacion_alta").val(tipoCuidadoMedicamentoAlta);
        }
    }

    load_modificacion_medicamentoAlta();



    
          //aqui modificar//
          var datos_medicamentoAlta_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_medicamentoAlta'),
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

		datos_medicamentoAlta_modificacion.initialize();


		$('.tipos_modificacion_medicamentoAlta .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_medicamentoAlta_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#medicamentoAlta_modificacion_item").val(-1);
                $(".tipos_modificacion_medicamentoAlta").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#medicamentoAlta_modificacion_item").val(selection.id);
        $('#form_modificacion_medicamentoAlta').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#medicamentoAlta_modificacion_alta").val() == '' &&  $("#medicamentoAlta_modificacion_item").val() != ''){
                $("#medicamentoAlta_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_medicamentoAlta").find("#medicamentoAlta_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#medicamentoAlta_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//


        $("#form_modificacion_medicamentoAlta").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#medicamentoAlta_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#medicamentoAlta_modificacion_alta").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una especialidad"};

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
            $("#btn_actualizar_modMedicamentoAlta").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#medicamentoAlta_modificacion_alta').val($('#medicamentoAlta_modificacion_alta').val().trim());
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
                    item_id = $('#medicamentoAlta_modificacion_item');
                    item_nombre = $('#medicamentoAlta_modificacion_alta');
                    tabla_actualizar = tablePendienteMedicamentoAlAlta;
                    boton_formulario =  $("#btn_actualizar_modMedicamentoAlta");
                    nombre_modal = $("#modalModificarMedicamentosAlta");
                    texto= "medicamento al alta";

                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);
                },
                error: function(error){
                    $("#btn_actualizar_modMedicamentoAlta").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-interconsulta-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_medicamentoAlta')) }}
        {{ Form::hidden('nombreForm', 'tipo_medicamentos_alta', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-interconsulta-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_medicamentoAlta"> 
                    <label>MEDICAMENTO</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'medicamentoAlta_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'medicamentoAlta_modificacion_item'))}}
                    {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-interconsulta-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modMedicamentoAlta" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


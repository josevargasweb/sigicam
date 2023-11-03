<script>
$(function() {

    function load_modificacion_control(){
        var cuidado = "{{$cuidadoInfo->id_cuidado}}";
        var fecha_solicitada = "{{\Carbon\Carbon::parse($cuidadoInfo->fecha_solicitada)->format('d/m/Y H:i')}}";
        var tipoCuidado = "{{$cuidadoInfo->tipo}}";
        if(cuidado > 0 && tipoCuidado != '' && fecha_solicitada != '' ){
            $("#control_modificacion_item").val(cuidado);
            $("#control_modificacion_alta").val(tipoCuidado);
            $("#fecha_control_medico_modificacion").val(fecha_solicitada);
        }
    }

    load_modificacion_control();


    $('#fecha_control_medico_modificacion').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#form_modificacion_control').bootstrapValidator('revalidateField', 'fecha_modificacion');
        });

    
          //aqui modificar//
          var datos_control_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_control'),
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

		datos_control_modificacion.initialize();


		$('.tipos_modificacion_control .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_control_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#control_modificacion_item").val(-1);
                $(".tipos_modificacion_control").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#control_modificacion_item").val(selection.id);
        $('#form_modificacion_control').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#control_modificacion_alta").val() == '' &&  $("#control_modificacion_item").val() != ''){
                $("#control_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_control").find("#control_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#control_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//

        $("#form_modificacion_control").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#control_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#control_modificacion_alta").val();
                                if(cantidad == 0){
                                    return {valid: false, message: "Debe ingresar una especialidad"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },  
                'fecha_modificacion':{
                    selector: '#fecha_control_medico_modificacion',
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
            $("#btn_actualizar_modcontrol").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#control_modificacion_alta').val($('#control_modificacion_alta').val().trim());
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
                    item_id = $('#control_modificacion_item');
                    item_nombre = $('#control_modificacion_alta');
                    tabla_actualizar = tablePendienteControlMedico;
                    boton_formulario =  $("#btn_actualizar_modcontrol");
                    nombre_modal = $("#modalModificarControl");
                    texto= "control medico";

                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);
                },
                error: function(error){
                    $("#btn_actualizar_modcontrol").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-cuidado-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_control')) }}
        {{ Form::hidden('nombreForm', 'tipo_controles_medicos', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-cuidado-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_control"> 
                    <label>ESPECIALIDAD</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'control_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'control_modificacion_item'))}}
                    {{Form::hidden('seleccionado_cuidado_epi', null, array('id' => ''))}}
                </div>
                <div class="form-group"> 
                    <label>FECHA</label>
                    {{Form::text('fecha_modificacion', null, array('id' => 'fecha_control_medico_modificacion', 'class' => 'form-control'))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-cuidado-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modcontrol" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


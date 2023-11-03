<script>
$(function() {

    function load_modificacion_interconsulta(){
        var cuidadoInterconsulta = "{{$cuidadoInfo->id_cuidado}}";
        var fecha_creacionInterconsulta = "{{\Carbon\Carbon::parse($cuidadoInfo->fecha_solicitada)->format('d/m/Y H:i')}}";
        var tipoCuidadoInterconsulta = "{{$cuidadoInfo->tipo}}";
        if(cuidadoInterconsulta > 0 && tipoCuidadoInterconsulta != '' && fecha_creacionInterconsulta != '' ){
            $("#interconsulta_modificacion_item").val(cuidadoInterconsulta);
            $("#interconsulta_modificacion_alta").val(tipoCuidadoInterconsulta);
            $("#fecha_interconsulta_modificacion").val(fecha_creacionInterconsulta);
        }
    }

    load_modificacion_interconsulta();


    $('#fecha_interconsulta_modificacion').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#form_modificacion_interconsulta').bootstrapValidator('revalidateField', 'fecha_modificacion');
        });

    
          //aqui modificar//
          var datos_interconsulta_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_interconsulta'),
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

		datos_interconsulta_modificacion.initialize();


		$('.tipos_modificacion_interconsulta .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_interconsulta_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#interconsulta_modificacion_item").val(-1);
                $(".tipos_modificacion_interconsulta").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#interconsulta_modificacion_item").val(selection.id);
        $('#form_modificacion_interconsulta').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#interconsulta_modificacion_alta").val() == '' &&  $("#interconsulta_modificacion_item").val() != ''){
                $("#interconsulta_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_interconsulta").find("#interconsulta_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#interconsulta_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//


        $("#form_modificacion_interconsulta").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#interconsulta_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#interconsulta_modificacion_alta").val();
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
                    selector: '#fecha_interconsulta_modificacion',
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
            $("#btn_actualizar_modInterconsulta").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#interconsulta_modificacion_alta').val($('#interconsulta_modificacion_alta').val().trim());
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
                    item_id = $('#interconsulta_modificacion_item');
                    item_nombre = $('#interconsulta_modificacion_alta');
                    tabla_actualizar = tablePendienteInterconsulta;
                    boton_formulario =  $("#btn_actualizar_modInterconsulta");
                    nombre_modal = $("#modalModificarInterconsulta");
                    texto= "interconsulta";

                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);
                },
                error: function(error){
                    $("#btn_actualizar_modInterconsulta").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-interconsulta-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_interconsulta')) }}
        {{ Form::hidden('nombreForm', 'tipo_interconsulta', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-interconsulta-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_interconsulta"> 
                    <label>ESPECIALIDAD</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'interconsulta_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'interconsulta_modificacion_item'))}}
                </div>
                <div class="form-group"> 
                    <label>FECHA</label>
                    {{Form::text('fecha_modificacion', null, array('id' => 'fecha_interconsulta_modificacion', 'class' => 'form-control'))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-interconsulta-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modInterconsulta" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


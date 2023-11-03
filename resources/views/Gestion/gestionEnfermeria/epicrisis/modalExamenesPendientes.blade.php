<script>
$(function() {

    function load_modificacion_examenes(){
        var cuidadoexamenes = "{{$cuidadoInfo->id_cuidado}}";
        var fecha_solicitadaExamenes = "{{\Carbon\Carbon::parse($cuidadoInfo->fecha_solicitada)->format('d/m/Y H:i')}}";
        var tipoCuidadoExamenes = "{{$cuidadoInfo->tipo}}";
        if(cuidadoexamenes > 0 && tipoCuidadoExamenes != '' && fecha_solicitadaExamenes != '' ){
            $("#examenes_modificacion_item").val(cuidadoexamenes);
            $("#examenes_modificacion_alta").val(tipoCuidadoExamenes);
            $("#fecha_examenes_modificacion").val(fecha_solicitadaExamenes);
        }
    }

    load_modificacion_examenes();


    $('#fecha_examenes_modificacion').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on("dp.change", function(){ 
            $('#form_modificacion_examenes').bootstrapValidator('revalidateField', 'fecha_modificacion');
        });

    
          //aqui modificar//
          var datos_examenes_modificacion = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tipos_modificacion_examenes'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '{{URL::to("/")}}/'+'%QUERY/tipo_examenes_pendientes/consultar_cuidados_epicrisis',
				wildcard: '%QUERY',
				filter: function(response) {
					return response;
				}
			},
			limit: 1000
		});

		datos_examenes_modificacion.initialize();


		$('.tipos_modificacion_examenes .typeahead').typeahead(null, {
		name: 'best-pictures',
		display: 'tipo',
		source: datos_examenes_modificacion.ttAdapter(),
		limit: 1000,
		templates: {
            empty: function(context){
                $("#examenes_modificacion_item").val(-1);
                $(".tipos_modificacion_examenes").find(".tt-dataset").text('No hay resultados');
            },
			suggestion: function(data){
                return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.tipo +"</b></span></div>"
			},
			header: "<div class='col-sm-12'><span class='col-sm-12' style='color:#1E9966;'>Especialidad</span></div><br>"
		}
    }).on('typeahead:selected', function(event, selection){
        $("#examenes_modificacion_item").val(selection.id);
        $('#form_modificacion_examenes').find("input[name='seleccionado_cuidado_epi']").val(selection.id);
		}).on('typeahead:change', function(event, selection){
            if($("#examenes_modificacion_alta").val() == '' &&  $("#examenes_modificacion_item").val() != ''){
                $("#examenes_modificacion_item").val('');
            }
		}).on('typeahead:close', function(ev, suggestion) {
		var $med=$(this).parents(".tipos_modificacion_examenes").find("#examenes_modificacion_item");
		if(!$med.val()&&$(this).val()){
            $("#examenes_modificacion_item").val(-1);
			// $(this).val("");
			$med.val("");
			$(this).trigger('input');
		}else if(!$(this).val()){
            $(this).val("");
            $med.val("");
        }
		});//aqui modificar//

        $("#form_modificacion_examenes").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'cuidado_modificacion_alta': {
                    selector: '#examenes_modificacion_alta',
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#examenes_modificacion_alta").val();
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
                    selector: '#fecha_examenes_modificacion',
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
            $("#btn_actualizar_modexamenes").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $('#examenes_modificacion_alta').val($('#examenes_modificacion_alta').val().trim());
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
                    item_id = $('#examenes_modificacion_item');
                    item_nombre = $('#examenes_modificacion_alta');
                    tabla_actualizar = tablePendienteExamenesPendientes;
                    boton_formulario =  $("#btn_actualizar_modexamenes");
                    nombre_modal = $("#modalModificarExamenesPendientes");
                    texto= "examenes pendientes";

                    todasLasValidacionesModal(data,formulario,item_id,item_nombre,tabla_actualizar,boton_formulario,nombre_modal,texto);

                },
                error: function(error){
                    $("#btn_actualizar_modexamenes").prop("disabled", false);
                    console.log(error);
                }
            });

   
        });

      
});


</script>

<fieldset>
    <div class="modificacion-interconsulta-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_modificacion_examenes')) }}
        {{ Form::hidden('nombreForm', 'tipo_examenes_pendientes', array('class' => '')) }}
        {{ Form::hidden ('id_cuidado_actualizar', $idCuidado, array('class' => '') )}}

        <div class="tipo-modificacion-interconsulta-container">
            <div class="col-md-6">
                <div class="form-group tipos_modificacion_examenes"> 
                    <label>ESPECIALIDAD</label>
                    {{Form::text('cuidado_modificacion_alta', null, array('id' => 'examenes_modificacion_alta', 'class' => 'form-control typeahead'))}}
                    {{Form::hidden('cuidado_modificacion_item', null, array('id' => 'examenes_modificacion_item'))}}
                </div>
                <div class="form-group"> 
                    <label>FECHA</label>
                    {{Form::text('fecha_modificacion', null, array('id' => 'fecha_examenes_modificacion', 'class' => 'form-control'))}}
                </div>
            </div>
        </div> 

            
        <div class="btn-actualizar-modificacion-interconsulta-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-success" id="btn_actualizar_modexamenes" style="margin-top:15%;">Actualizar</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


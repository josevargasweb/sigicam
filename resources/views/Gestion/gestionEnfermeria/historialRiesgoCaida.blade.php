@extends("Templates/template")

@section("titulo")
Historial Paciente
@stop

@section("script")
<script>
    $(document).ready(function(){
        $("#riesgoCaidaform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
                caidas_previas: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                medicamentos: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'medicamentos[]': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                if($("#medicamentos").val() == '' || $("#medicamentos").val() == null){
                                    return {valid: false, message: "Campo obligatorio"};
                                }else{
                                    return true;
                                }
                            }
                        },
                    }
                },
                deficit: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                mental: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                deambulacion: {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data){
            $("#riesgoCaidaform input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#riesgoCaidaform input[type='submit']").prop("disabled", false); 
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                message: "<h4>¿Está seguro de ingresar la información?</h4>",
                buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result){
                if(result){
                    $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/ingresoRiesgoCaida",
                        headers: {					         
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                    },
                    type: "post",
                    dataType: "json",
                    data: $form .serialize(),
                    success: function(data){
                        if(data.exito) {
                            swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						    $form[0] . reset();
                                location . reload();

							}, 2000)
						},
						});
                           				        
                        }
                        if(data.error) {
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            });
                        }

                        if(data.info) {
                            swalInfo2.fire({
                                title: 'Información',
                                text: data.info
                            }).then(function(result) {
                                location . reload();
                            });
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                    });
                }
            }
        });
    });

    historial = $("#riesgoCaida").dataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json"
        },
	});

	$.ajax({
		url: "{{URL::to('gestionEnfermeria/buscarHistorialRiesgoCaidas')}}",
		headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {"idCaso": {{$caso}}},
        dataType: "json",
        type: "post",
        success: function(data){
            if(data.error){
                console.log("error: no se encuentran datos");
            }
            console.log(data);
            historial.fnClearTable();
            if(data.length !=0) historial.fnAddData(data);
        },
        error: function(error){
            console.log(error);
        }
	    });
    });


    function editar(id_formulario_riesgo_caida){
        console.log("boton presionado?");
        id = id_formulario_riesgo_caida;
        console.log(id);
        $.ajax({                            
            url: "{{URL::to('gestionEnfermeria/editarRiesgoCaidas/')}}"+"/"+id,                           
            headers: {                                 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')                            
            },                            
            type: "get",                            
            dataType: "json",                            
            //data: {"id":id},                            
            success: function(data){ 
                console.log(data);
                $("#caidas_previas").val(data.datos.caidas_previas).change();
                if (data.datos.medicamentos.indexOf(',') > -1) {
                    $("#medicamentos").val(data.datos.medicamentos.split(',')).change();
                     }else{
                        $("#medicamentos").val(data.datos.medicamentos).change();
                     }
                $("#deficit").val(data.datos.deficits_sensoriales).change(); 
                $("#mental").val(data.datos.estado_mental).change();
                $("#deambulacion").val(data.datos.deambulacion).change();
                $("#id_formulario_riesgo_caida").val(data.datos.id).change();
                $("#legendCaida").hide();
                $("#btnVolverCaida").hide();
                $("#btnriesgocaida").val("Editar Información");

                // var total = 0;
                // total += ($("#caidas_previas").val() === "0")?0:1;
                // total += ($("#deficit").val() === "0")?0:1;
                // total += ($("#medicamentos").val() === "0")?0:1;
                // total += ($("#mental").val() === "0")?0:1;
                // total += ($("#deambulacion").val() === "0")?0:1;

                caidas_previas = ($("#caidas_previas").val() === "0" || $("#caidas_previas").val() === "")?0:1;
                deficit = ($("#deficit").val() === "0" || $("#deficit").val() === "")?0:1;
                medicamentos_array = 0;
            if ($("#medicamentos").hasClass("selectpicker")) {
                var medicamentos_array = [];
                medicamentos_array = $("#medicamentos").val();
                    if(medicamentos_array == null){
                        medicamentos_array = '';
                    }         
                //en caso de seleccionar ninguno
                if(medicamentos_array.indexOf("0") != -1){
                    $('#medicamentos').val(0);
                    $('#medicamentos').selectpicker('refresh');
                    medicamentos = 0;
                //en caso de no seleccionar
                }else if(medicamentos_array.length == 0){
                    $('#medicamentos').val('');
                    $('#medicamentos').selectpicker('refresh');
                    medicamentos = '';
                }
                else{
                    //suma de valores
                  $("#medicamentos option[value='']").attr("selected", false);
                  $('#medicamentos').selectpicker('refresh');
                  medicamentos = $('#medicamentos option:selected').length;
                }


            }else{
                    medicamentos = ($("#medicamentos").val() === "0" || $("#medicamentos").val() === "")?0:1;
                }
                mental = ($("#mental").val() === "0" || $("#mental").val() === "")?0:1;
                deambulacion = ($("#deambulacion").val() === "0" || $("#deambulacion").val() === "")?0:1;

                total = Number(caidas_previas) + Number(deficit) + Number(medicamentos) + Number(mental) + Number(deambulacion);

                $("#puntosCaida").val(total);
                if(total <= 1){
                    $("#detalleCaida").val("Bajo Riesgo");
                }else{
                    $("#detalleCaida").val("Alto Riesgo");
                }         
            },                            
            error: function(error){                                
                console.log(error);                            
            }                        
            });  
            $('#modalFormCaidas').modal('show');
    }
</script>
@stop

@section('section')

    <div class="container">
        <fieldset>
            <a href="javascript:history.back()" class="btn btn-primary">Volver</a>

            <div class="row">
                <div class="col-md-12" style="text-align:center;"><h4>Historial Riesgo Caídas</h4></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        {{ HTML::link(URL::route('pdfHistorialRiesgoCaida', [$caso]), 'Historial PDF', ['class' => 'btn btn-danger']) }}
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    Nombre Paciente: {{$paciente}}
                </div>
            </div>
            <br>
            <table id="riesgoCaida" class="table  table-condensed table-hover">
                <thead>
                    <tr style="background:#399865;">
                        <th>Opciones</th>
                        <th>Usuario aplica</th>
                        <th>Fecha aplicación</th>
                        <th>Total</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </fieldset>
    </div>

    <div class="modal fade modalFormCaidas" tabindex="-1" role="dialog" aria-labelledby="modalFormCaidas" aria-hidden="true" id="modalFormCaidas">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 align="center" class="modal-title" id="myModalLabel">Riesgo Caídas</h4>
                    </div>
                    <div class="modal-body">
                        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'riesgoCaidaform')) }}
                        <input type="hidden" value="Editar" name="tipoFormRiesgoCaida" id="tipoFormRiesgoCaida">
                        {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoCaida')) }}
                        <input type="hidden" value="" name="id_formulario_riesgo_caida" id="id_formulario_riesgo_caida">
                        <br>
                        @include('Gestion.gestionEnfermeria.partials.FormRiesgoCaida')
                        {{ Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

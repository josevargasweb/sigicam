
<script>
    $(document).ready(function(){
        $("#riesgoCaidaform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
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
                        if(data.error) swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                        });
                    },
                    error: function(error){
                        console.log(error);
                    }
                    });
                }
            }
            });
        });

    });
</script>
<meta name="csrf-token" content="{{{ Session::token() }}}">

<br>
        <legend class="text-center" id="legendCaida">Escala Riesgo Caida</legend>
        {{ HTML::link("gestionEnfermeria/$caso/historialRiesgoCaida", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}

        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'riesgoCaidaform')) }}

        {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoCaida')) }}
        <input type="hidden" value="" name="id_formulario_riesgo_caida" id="id_formulario_riesgo_caida">
        <input type="hidden" value="En Curso" name="tipoFormRiesgoCaida" id="tipoFormRiesgoCaida">
        <br>

        @include('Gestion.gestionEnfermeria.partials.FormRiesgoCaida')
        {{-- <input id="btnriesgocaida" type="submit" name="" class="btn btn-primary" value="Ingresar Información">  --}}
        {{ Form::close()}}

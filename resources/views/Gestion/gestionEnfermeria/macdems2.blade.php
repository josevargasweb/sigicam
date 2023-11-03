

@section("titulo")
Formulario
@stop

<script>
    $(document).ready(function(){
        $("#escalaMacdemsform").bootstrapValidator({
            excluded: ':disabled', 
            fields: {
            }
            }).on('status.field.bv', function(e, data){
            $("#escalaMacdemsform input[type='submit']").prop("disabled", false);
            }).on("success.form.bv", function(evt){
            $("#escalaMacdemsform input[type='submit']").prop("disabled", false); 
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
                        url: "{{URL::to('/gestionEnfermeria')}}/ingresoEscalaMacdems",
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


@section("miga")
<li><a href="#">Formulario Escala Macdems</a></li>
@stop

        <legend class="text-center" id="legendMacdems">Escala Macdems</legend>
        {{ HTML::link("gestionEnfermeria/$caso/historialEscalaMacdems", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}

        {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaMacdemsform')) }}

        {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoMacdems')) }}
        <input type="hidden" value="" name="id_formulario_escala_macdems" id="id_formulario_escala_macdems">
        <br>
        <input type="hidden" value="En Curso" name="tipoFormMacdems" id="tipoFormMacdems">
        @include('Gestion.gestionEnfermeria.partials.FormMacdems')
        {{-- <input id="btnriesgocaida" type="submit" name="" class="btn btn-primary" value="Ingresar Información">  --}}
        {{ Form::close()}}

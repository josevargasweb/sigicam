<script>

    $( document ).ready(function() {
        console.log("realizando");

        console.log({{$caso}});
        caso = {{$caso}};
        $(".detalles-caso").val(caso);
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            }
        });


        var _token = $('input[name="_token"]').val();
    });

    $('#modalFormularioRiesgo').modal('hide');

    $(function(){
        $("#formIngresarDieta").submit(function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ URL::to('/cambiarDieta')}}",
                data: $(this).serialize(),
                type: "post",
                dataType: "json",
                success: function(data){
                    $("#modalVerDetallesDieta .modal-body").html(data.contenido);
                },
                error: function(error){
                }
            });
            //return false;
        });
    });
</script>

    <fieldset style="margin-top: 20px;">
        <legend>Dieta</legend>
        <div class="form-group col-md-12">
        	<div class="table-responsive">
            <table id="tabla-dietas-paciente" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Dieta</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($dietas as $dieta)
                    <tr>
                        <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dieta['fecha'])->format("d-m-Y H:i:s")}}</td>
                        <td>{{$dieta['dieta']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
        {{ Form::open( array('url' => 'cambiarDieta', 'method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formIngresarDieta') ) }}
        {{ Form::hidden('caso', '', array('class' => 'detalles-caso')) }}
        <div class="form-group col-md-12">
            @if(Session::get('usuario')->tipo == 'gestion_clinica' || Session::get('usuario')->tipo == 'enfermeraP')
            {{ Form::select('nueva-dieta', $opciones_dieta, "", array('id' => 'nueva-dieta', 'class' => 'form-control', 'style' => 'width: 70%;')) }}
            {{ Form::submit('Cambiar dieta', array('id' => 'btnCambiarDieta', 'class' => 'btn btn-primary')) }}
            {{-- else
             Form::select('nueva-dieta', $opciones_dieta, "", array('id' => 'nueva-dieta', 'class' => 'form-control', 'style' => 'width: 70%;', 'disabled'=>'true'))  --}}
            @endif
            
        </div>
        {{ Form::close() }}
    </fieldset>

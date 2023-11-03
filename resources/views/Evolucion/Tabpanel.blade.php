
@extends('Evolucion/Resultado')

<script>
    $(function(){
        $("#tab-resultados a").click(function(e){
            e.preventDefault();
            $(this).tab("show");
        });

        $("#tab-resultados a:first").tab("show");

       /*$("#form-datos-evolucion").on("submit", function(e){
           e.preventDefault();
           var form = $(this);
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: form.attr('method'),
                dataType: 'json',
                success: function(data){
                    if(data.exito) bootbox.alert("<h4>"+data.exito+"</h4>", function(){
                        location.reload();
                    });
                    if(data.error) {
                        swalError.fire({
						title: 'Error',
						text:data.error
						});
                    }
                },
                error: function(error){

                }
           });
           return false;
        });*/
    });

</script>



@foreach($datos as $info)
    @section('tabs')
        <li role="presentation"><a href="#tab-{{$info->nombreId()}}">{{$info->nombre}}</a></li>
    @parent
    @stop

    @section('contenido')
    <div role="tabpanel" class="tab-pane panel panel-default" id="tab-{{$info->nombreId()}}">
    {{-- @if($info->comprobar(Session::get("idEstablecimiento"))) --}}
    <div class="panel-heading">{{$info->getNombreHospital()}}
        <div class="row">
            <div class="col-md-3">Encargado:</div>
            <div class="col-md-4">{{$info->enfermera}}</div>
            <div class="col-md-5"></div>
        </div>
        <div class="row">
            <div class="col-md-3">Periodo:</div>
            <div class="col-md-4">{{$info->getMes()}} {{$info->anno}}</div>
            <div class="col-md-5"></div>
        </div>
    </div>
        {{-- <div class="panel-body">
             @if(!$info->servicio)
                 <div class="row">
                     <div class="col-md-12">
                         <div class="alert alert-danger">{{$info->error_unidad}}</div>
                     </div>
                 </div>
             @endif
    </div> --}}
    <div class="panel-body">
    	<div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr>
                <th>DIA</th>
                @foreach($info->categorias as $cat)
                    <th>{{$cat}}</th>
                @endforeach
            </tr></thead>
            <tbody>
                @foreach($info->rangoDias() as $dia)
                    <tr><td>{{$dia}}</td>
                        @foreach($info->categorias as $cat)
                            <td>{{  $info->{$cat}[$dia] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    {{--@else
        <div class="alert alert-danger">
            <p>{{$info->error}}</p>
        </div>
    @endif --}}
    </div>

    @parent
    @stop
@endforeach


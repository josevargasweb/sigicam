@extends("Templates/template")

@section("titulo")
Médicos
@stop

@section("miga")
<li><a href="#">Solicitudes</a></li>
<li><a href="#" onclick='location.reload()'>Solicitudes</a></li>
@stop

@section("script")

<script>
    $(function() {
    	$("#traumatologiaHoras").collapse();

        $('#tablaAlta').dataTable({ 
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 15,
            //"bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });
    });


    var eliminar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea eliminar el médico ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/eliminarMedico",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {idMedico: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                        location.href="{{URL::to('medicos')}}";
                                            }, 2000)
                                        },
                                        });
                                       

                                    } 
                                    if(data.error) swalError.fire({
                                                    title: 'Error',
                                                    text:data.error
                                                    });
                                },
                                error: function(error){
                                    console.log(error);
                                }
                            });
                        }
                    },
                    danger: {
                        label: "Cancelar",
                        className: "btn-danger",
                        callback: function() {
                        }
                    }
                }
            });
        }
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")
	<fieldset>
		<legend>Lista de médicos</legend>


		<br><br>
		<div id="contenido"></div>
		
	</fieldset>
    <div class="row">
        <div class="col-md-12">
        <a href="{{URL::to('/')}}/crearMedico" class="btn btn-primary" id="btnEnviar" type="button">
        Agregar médico
        </a>
        </div>
    </div>
    <br>
    <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table id="tablaAlta" class="table table-striped table-bordered table-hover">
                        <tfoot>
                            <tr>
                              
                                <th>Run</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th></th>
                                
                            </tr>
                        </tfoot>
                        <thead>
                            <tr>
                               
                                <th>Run</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Opciones</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicos as $medico)
                                <tr>
                                   
                                    <td>{{$medico->rut_medico}}-
                                    @if($medico->dv_medico != '10')
                                    {{$medico->dv_medico}}
                                    @else
                                    {{"K"}} 
                                    @endif
                                    </td>
                                    <td>{{$medico->nombre_medico}}</td>
                                    <td>{{$medico->apellido_medico}}</td>
                                    
                                    <td>
                                    <a href="{{URL::to('/')}}/actualizarMedico/{{$medico->id_medico}}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>

                                    <a onclick="eliminar({{$medico->id_medico}})"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="
    padding-left: 9px;"></span></a>
                                    </td>
                                </tr>
                            
                            
                            @endforeach

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>


<br><br>

@stop

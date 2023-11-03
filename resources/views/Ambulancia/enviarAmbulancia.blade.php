@extends("Templates/template")

@section("titulo")
Enviar Ambulancia
@stop

@section("script")
<script>

</script>
@stop

@section("miga")
@stop


@section("section")
<legend>Enviar ambulancia, patente: <b>{{$ambulancia->patente}}</b>, ubicación : <b>{{$ambulancia->ubicacion}}</b></legend>
<div class="container">
    <div class="row">
        <div class="col-md-11 "
                    @if(count($errors)>0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::open(['url' => 'ambulancia/ingresarRuta', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
                 
                    <fieldset>
                        <!-- Asignar destinos -->
                        <h4>Ingrese la ruta que desea para la ambulancia</h4>
                        <form method="post">
                            
                            <table class = "table bg-info" id = "tabla">
                                <thead>
                                    <tr>
                                        <th>Hora de salida solicitada:</th>
                                        <th>Hora app. de llegada:</th>
                                        <th>Hospital destino:</th>
                                        <th>¿Añadir Paciente?</th>
                                        <th>Paciente:</th>
                                    </tr>
                                    
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>
                                            <input required class = "cambio form-control" id="datetimepicker" type="text" name = "salida">
                                        </td>
                                        <td> 
                                            <input class = "form-control" id="mostrarHora" type="text" name = "salida" disabled value="">
                                        </td>    
                                            
                                            <input type="hidden" id="patenteInput" name="ambulancia" value="{{$ambulancia->patente}}">
                                        </td>

                                        <td>
                                            {!! Form::select('destino[]',$establecimientos,null,['class' => 'form-control', 'id' => 'destino']) !!}
                                        </td>
                                        
                                        <td>
                                            <div class = "col-md-6 col-md-offset-1 " >
                                               
                                                <button class= "btn btn-warning" type="button" id="listaPacientes" data-toggle="modal" data-target="#modalPacientes" >+</button>
                                                
                                            </div>

                                            <!-- Modal Marcar hora Salida-->
                                            <div id="modalPacientes" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Lista de Pacientes</h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <table id='gridPacientes' class='table table-striped table-bordered table-condensed no-footer '>
                                                                <thead>
                                                                    <th>Nombre</th>
                                                                    <th>Run</th>  
                                                                    <th>Motivo</th>
                                                                    <th width="50px">Acción</th>
                                                                </thead>
                                                    
                                                                <tbody>
                                                                    @foreach($pacientes as $paciente)
                                                                        <tr>
                                                                            <td>{{$paciente->nombre}}</td>
                                                                            <td>{{$paciente->rut}}-{{$paciente->dv}}</td>
                                                                            <td>{{$paciente->motivo}}</td>
                                                                            <td>
                                                                                <button class='btn btn-primary dropdown-toggle' type='button' id = "{{$paciente->rut}}-{{$paciente->dv}}"  onclick= "cargarPacientes({{$paciente->rut}}+'-'+{{$paciente->dv}})" >Ingresar</button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div> 
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- FIN MODAL -->

                                        </td>

                                        <td>
                                            <input class = "form-control" id="paciente" type="text" name = "paciente" disabled value="">
                                        </td>

                                    </tr> 
                                </tbody>
                                  

                            </table>

                            <div class = "btn-der">
                                <input type = "submit" name = "insertar" value = "Insertar Destino" class = "btn btn-info pull-right" />
                            </div>
                        </form>

                        <input type="hidden" name="patente" value="{{$ambulancia->patente}}">

                 
                    </fieldset>
                 
                    {!! Form::close()  !!}
                 
            
        </div>
    </div>
</div>

<script>
    


    function cargarPacientes(rut){
        $('#modalPacientes').modal('hide');
        $('#paciente').val(rut);
        console.log(rut);       
    }


    $('#datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });
    function generarHora(dia, mes, ano, hora, minuto, segundo, patente, destino){
        $.ajax({
            url: 'generarhora',
            headers: {        
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            data:{dia : dia, mes: mes, ano : ano, hora : hora, minuto : minuto, segundo: segundo, patente: patente, destino : destino},
            type: "get",
            dataType: "json",

            success: function(data){
                console.log(data.fecha);
                $('#mostrarHora').val(data.fecha);
            },
            error: function(error){
                console.log(error);
            }
        });
    }


    $('#datetimepicker').on('dp.change', function(e){ 
        tiempo_seleccionado = $('#datetimepicker').val();

        patente = $('#patenteInput').val();
        destino = $('#destino').val();

        hrllegada =  tiempo_seleccionado.split("-",3);
        hr2llegada =  hrllegada[2].split(" ",3);
        hr = hr2llegada[1].split(":",3);

        generarHora(hrllegada[0], hrllegada[1], hr2llegada[0],hr[0], hr[1], hr[2], patente, destino);
    });

    //en caso de que cambien el destino
    $("#destino").change(function(){
        var tiempo_seleccionado = $('#datetimepicker').val();

        if(tiempo_seleccionado != ''){
            patente = $('#patenteInput').val();
            destino = $('#destino').val();
            hrllegada =  tiempo_seleccionado.split("-",3);
            hr2llegada =  hrllegada[2].split(" ",3);
            hr = hr2llegada[1].split(":",3);

            generarHora(hrllegada[0], hrllegada[1], hr2llegada[0],hr[0], hr[1], hr[2], patente, destino);
        }
    });

    $("#gridPacientes").dataTable({
        "language": {
            "lengthMenu":     "Mostrar _MENU_ por página",
            "zeroRecords":    "No se ha encontrado registros",
            "info":           "Mostrando pagina _PAGE_ de _PAGES_",
            "infoEmpty":      "No se ha encontrado información",
            "infoFiltered":   "(filtered from _MAX_ total records)",
            "search":         "Buscar:",
            "paginate": {
                "first":      "Primero",
                "last":       "Ultimo",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
        }
    });

</script>
@stop
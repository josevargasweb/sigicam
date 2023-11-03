@extends("Templates/template")

@section("titulo")
Lista de Ambulancia
@stop

@section("script")

@stop

@section("miga")
@stop


@section("section")
{{ csrf_field() }}
<legend>Listado de Ambulancias</legend>
<div class="row" >
    <a class="btn btn-primary pull-right " style="margin-top: 25px; " data-toggle="modal" data-target="#modalAgregarAmbulancia" tabindex="-1">Agregar Ambulancia</a>

    <!-- Modal -->
    <div id="modalAgregarAmbulancia" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <legend>Formulario Agregar Ambulancia</legend>
                </div>
                <div class="modal-body">
                    <fieldset>
                    {!! Form::open(['route' => 'ambulancias.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
                    <!-- Salida -->
                <div class="" >
                    <div class="form-group">
                        {!! Form::label('patente', 'Patente:', ['class' => 'col-lg-3 control-label']) !!}
                        <div class="col-lg-8 col-md-offset-1">
                            {!! Form::text('patente',null,['class' => 'form-control', 'placeholder' => 'Patente de la maquina']) !!}
                        </div>
                    </div>


                    <!-- tipo -->
                    <div class="form-group">
                        {!! Form::label('tipo_id', 'Tipo:', ['class' => 'col-lg-3 control-label']) !!}
                        <div class="col-lg-8 col-md-offset-1">
                            {!! Form::select('tipo_id',$tipos ,null,['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <!-- establecimiento -->
                    <div class="form-group">
                        {!! Form::label('establecimiento_id', 'Establecimiento:', ['class' => 'col-lg-3 control-label'] )  !!}
                        <div class="col-lg-8 col-md-offset-1">
                            {!! Form::select('establecimiento_id', $establecimientos, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <!-- ubicacion -->
                    <div class="form-group">
                        {!! Form::label('ubicacion', 'Ubicación:', ['class' => 'col-lg-3 control-label'] )  !!}
                        <div class="col-lg-8 col-md-offset-1">
                            {!! Form::select('ubicacion', $establecimientos, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            {!! Form::submit('Registrar', ['class' => 'btn btn-lg btn-primary pull-right'] ) !!}
                        </div>
                    </div>
                    </fieldset>

                    {!! Form::close()  !!}
                </div> 
            </div>
        </div>
    </div>
</div>
<br>


<div class="row" >
    @if(empty($ambulancias))
        <div class="well text-center" >No se encontraron ambulancias.</div>
    @else
        <table id="gridGestionAmbulancias" class='display responsive ' style="width:100%"  >   
            <thead>
                <th>Patente</th>
                <th>Tipo</th>  
                <th>En Uso</th>
                <th>Capacidad</th>
                <th>Establecimiento origen</th>
                <th>Ubicación Actual Esimada</th>
                <th>Estado</th>
                <th width="50px">Acciones</th>
                </thead>
            <tbody >
                     
            <?php
            for($i=0; $i<count($ambulancias); $i++){
            ?>

                <tr>
                    <td>{!! $ambulancias[$i]->patente !!}</td>
                    <td>{!! $ambulancias[$i]->tipo !!}</td>
                    <td>{!! $ambulancias[$i]->enuso !!}</td>
                    <td>{!! $ambulancias[$i]->capacidad !!}</td>
                    <td>{!! $ambulancias[$i]->nestablecimiento!!}</td>
                    <td>{!! $ambulancias[$i]->ubicacion!!}</td>
                    <td>
                        @if($ambulancias[$i]->estadoa_id == 1)
                            <img class="ambulancia" src="img/ocupada.png">
                        @elseif($ambulancias[$i]->estadoa_id == 2)
                            <img class="ambulancia" src="img/disponible.png">
<!--                             <span class="label label-success">Disponible</span> -->
                        @elseif($ambulancias[$i]->estadoa_id == 4)
                            <img class="ambulancia" src="img/en_curso.png">
                        @else
                            <img class="ambulancia" src="img/bloqueado.png">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('ambulancias.edit',$ambulancias[$i]->id) }}"><i class="glyphicon glyphicon-edit"></i></a>


                        <a class="glyphicon glyphicon-remove" data-toggle="modal" data-target="#modalEliminar"></a>

                        <div id="modalEliminar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                         <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;  </button>
                                <h4 class="modal-title" id="myModalLabel">Eliminar ambulancia</h4>
                              </div>
                              <div class="modal-body">
                                ¿Seguro que deseas eliminar esta ambulancia?
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <a type="button" class="btn btn-danger" href="{{ route('ambulancias.destroy',$ambulancias[$i]->id) }}">Eliminar</a>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                       
                </tr>
                        
            <?php }?>
            </tbody>
        </table>
    @endIF
</div>




<script >

    $("#gridGestionAmbulancias").dataTable({
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
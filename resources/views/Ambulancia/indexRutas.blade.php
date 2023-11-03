@extends("Templates/template")

@section("titulo")
Generar Rutas
@stop

@section("script")

@stop

@section("miga")
@stop


@section("section")
<legend>Rutas de Ambulancias</legend>

<div class="row" >
    @if(empty($ambulancias))
        <div class="well text-center" >No se encontraron ambulancias.</div>
    @else
        <table id="gridAmbulanciasRutas" class="display responsive nowrap"  >   
            <thead>
                <th>Patente</th>
                <th>Tipo</th>  
                <th>En Uso</th>
                <th>Capacidad</th>
                <th>Ubicación</th>
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
                    <td>{!! $ambulancias[$i]->ubicacion!!}</td>
                    <td>
                        @if($ambulancias[$i]->estadoa_id == 1)
                            <img class="ambulancia" src="../img/ocupada.png">
                        @elseif($ambulancias[$i]->estadoa_id == 2)
                            <img class="ambulancia" src="../img/disponible.png">
<!--                             <span class="label label-success">Disponible</span> -->
                        @elseif($ambulancias[$i]->estadoa_id == 4)
                            <img class="ambulancia" src="../img/en_curso.png">
                        @else
                            <img class="ambulancia" src="../img/bloqueado.png">
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Opciones<span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                @if($ambulancias[$i]->estadoa_id == 1 || $ambulancias[$i]->estadoa_id == 2 || $ambulancias[$i]->estadoa_id == 4)
                                    <li><a href="{{ route('verificando', ['ambulancia' => $ambulancias[$i]->id]) }}">Ingresar Ruta(s)</a></li>
                                @endif
                                <li><a href="{{ route('rutas', ['ambulancia' => $ambulancias[$i]->id]) }}">Historial de Ruta</a></li>
                            </ul>
                        </div>
                    </td>
                       
                </tr>
                        
            <?php }?>
            </tbody>
        </table>
    @endIF
</div>

<script >

    $("#gridAmbulanciasRutas").dataTable({
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
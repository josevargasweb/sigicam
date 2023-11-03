@extends("Templates/template")

@section("titulo")
Enviar Ambulancia
@stop

@section("script")
@stop

@section("miga")
@stop


@section("section")
<legend>Enviar ambulancia, patente: <b>{{$ambulancia->patente}}</b>, ubicación : <b>{{$ambulancia->ubicacion}}</b></legend>
<form action='../listaRutas' method='POST' >
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

                 
                    <fieldset>
                        <!-- Asignar destinos -->
                        <h4>Ingrese la ruta que desea para la ambulancia</h4>
                        
                            
                            <table class = "table bg-info" id = "tabla" >
                                <thead>
                                    <tr>
                                        <th WIDTH="200">Hospital Origen</th>
                                        <th>Hora de salida solicitada:</th>
                                        <th>Hora app. de llegada:</th>
                                        <th WIDTH="200">Hospital destino:</th>
                                        <th WIDTH="50">¿Añadir Paciente?</th>
                                        <th WIDTH="125">Paciente:</th>
                                        <th>Quitar Ruta</th>
                                    </tr>
                                    
                                </thead>
                                
                                <tbody>
                                    
                                    
                                    <tr>
                                        <td>
                                            {!! Form::select('origen[]',$establecimientos,null,['class' => 'origen form-control', 'id' => 'origen']) !!}
                                        </td>
                                        <td>
                                            {{ csrf_field() }}
                                            <input required class = "cambio form-control" type="text" name = "salida[]" value= "">
                                        </td>
                                        <td> 
                                            <input readonly="readonly" class = "mostrarHora form-control"  type="text" name = "llegada[]" value="">
                                        </td>    
                                            
                                            <input type="hidden" id="patenteInput" name="ambulancia[]" value="{{$ambulancia->patente}}">
                                        </td>

                                        <td>
                                            {!! Form::select('destino[]',$establecimientos,null,['class' => 'form-control', 'id' => 'destino']) !!}
                                        </td>
                                        
                                        <td>
                                            <div class = "col-md-6 col-md-offset-1 " >
                                               
                                                <button class= "btn btn-warning" type="button" id="listaPacientes"  data-toggle="modal" data-target="#modalPacientes"  >+</button>
                                                
                                            </div>

                                        </td>

                                        <td>
                                            <input class="idPaciente" type="hidden" name="idPaciente[]" value="">
                                            <input readonly="readonly" name="paciente[]" class = "paciente form-control"  type="text" data-indice='0' value="">
                                            
                                        </td>

                                        <td class = "eliminar">
                                            <input class = "btn btn-danger" type = "button" value = "-"/>
                                        </td>
                                    </tr> 
                                </tbody>
                                  

                            </table>

                            <div class = "btn-der">
                                <button id = "adicional" name = "adicional" type = "button" class = "btn btn-warning" />+ Rutas</button>
                                

                                <input type = "submit" name = "insertar" value = "Insertar Destino" class = "btn btn-info pull-right" />
                                
                            </div>
                        
                        
                        <input type="hidden" name="id" value="{{$ambulancia->id}}">

                 
                    </fieldset>
                 
                    
                 
            
        </div>
    </div>
</div>

<!-- Modal -->
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
                        <th>Rut</th>  
                        <th>Motivo</th>
                        <th width="50px">Acción</th>
                    </thead>
                        
                    <tbody>
                            
                    </tbody>
                </table>
            </div> 
        </div>

    </div>
</div>
<!-- FIN MODAL -->
</form>
<script>




    $(document).on("click", ".botonql", function(){
            
            rut = $(this).data("rut");          
            nombre = $(this).data("nombre");  
            indiceOrigen = $(this).data("indiceorigen"); 
            idPaciente = $(this).data("idpaciente");

            $("#tabla tr").each(function (indice, elemento) {
                //console.log(indice);  
                //console.log($('.paciente').eq(indice).data('indice') );  
                if($('.paciente').eq(indice).data('indice') == indiceOrigen){
                    console.log("fila "+indiceOrigen);       
                               
                    $('.idPaciente').eq(indice).val(idPaciente);

                    if (rut == false || rut == '-') {
                        $('.paciente').eq(indice).val(nombre+'(Sin Rut)');
                    }else{
                        $('.paciente').eq(indice).val(nombre+'('+rut+')');
                    }
                    
                }
                    
            })

            $('#modalPacientes').modal('hide');
    });    
        


    

    $('body').on('click','#listaPacientes', function(e){  
        origen = $(this).parent().parent().parent().children().children('#origen').val();
        $('#origen_comparar').val(origen);
    });

    $('body').on('click','.cambio', function(e){    
        $(this).datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
    });
    $('body').on('blur','.cambio', function(e){ 
        tiempo_seleccionado = $(this).val();
        origen = $(this).parent().parent().children().children('#origen').val();
        destino = $(this).parent().parent().children().children('#destino').val();

        if(tiempo_seleccionado != '' ){
            hrllegada =  tiempo_seleccionado.split("-",3);
            hr2llegada =  hrllegada[2].split(" ",3);
            hr = hr2llegada[1].split(":",3);
            generarHoraRuta($(this), hrllegada[0], hrllegada[1], hr2llegada[0],hr[0], hr[1], hr[2], origen, destino);
        }
        
    });

    $('body').on('change','#origen', function(e){       
        tiempo_seleccionado = $(this).parent().parent().children().children('.cambio').val();

        origen = $(this).parent().parent().children().children('#origen').val();
        destino = $(this).parent().parent().children().children('#destino').val();

        if(tiempo_seleccionado != '' ){
            hrllegada =  tiempo_seleccionado.split("-",3);
            hr2llegada =  hrllegada[2].split(" ",3);
            hr = hr2llegada[1].split(":",3);
            generarHoraRuta($(this), hrllegada[0], hrllegada[1], hr2llegada[0],hr[0], hr[1], hr[2], origen, destino);
        }
    });

    $('body').on('change','#destino', function(e){       
        tiempo_seleccionado = $(this).parent().parent().children().children('.cambio').val();

        origen = $(this).parent().parent().children().children('#origen').val();
        destino = $(this).parent().parent().children().children('#destino').val();
        
        if(tiempo_seleccionado != '' ){
            hrllegada =  tiempo_seleccionado.split("-",3);
            hr2llegada =  hrllegada[2].split(" ",3);
            hr = hr2llegada[1].split(":",3);
            generarHoraRuta($(this), hrllegada[0], hrllegada[1], hr2llegada[0],hr[0], hr[1], hr[2], origen, destino);
        }
    });


    $('body').on('click','#listaPacientes', function(e){       

        origen = $(this).parent().parent().parent().children().children('#origen').val()
        indice = $(this).parent().parent().parent().children().children('.paciente').data('indice');
        console.log("indice: "+indice+ " origen: "+origen);
        //De esta manera utilizando eq seleccionamos la segunda fila, ya que la primera es 0


        $.ajax({
            url: '../buscarPacientes',
            headers: {        
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            data:{establecimiento_id: origen, indice:indice},
            type: "get",
            dataType: "json",

            success: function(data){
                console.log(data);
                var tabla=$("#gridPacientes").dataTable().columnFilter({
                                aoColumns: [
                                {type: "text"},
                                {type: "text"},
                                //{type: "select", values: getRiesgos()},
                                {type: "text"},
                                null
                                ]
                            });
                tabla.fnClearTable();
                if(data.length != 0) tabla.fnAddData(data);

                // $('#gridPacientes tbody').append(html);
            },
            error: function(error){
                console.log(error);
            }
        });

       
    });

    


    
    function generarHoraRuta(objeto,dia, mes, ano, hora, minuto, segundo, origen, destino){
        $.ajax({
            url: '../generarHoraRuta',
            headers: {        
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            data:{dia : dia, mes: mes, ano : ano, hora : hora, minuto : minuto, segundo: segundo, origen: origen, destino : destino},
            type: "get",
            dataType: "json",

            success: function(data){
                console.log(data);
                objeto.parent().parent().children().children('.mostrarHora').val(data.fecha);
            },
            error: function(error){
                console.log(error);
            }
        });



    }


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

    /*CREAR UN IDENTIFICADOR PARA EL INPUT Y ENVIARLO POR EL CARGAR PACIENTS*/

   


        var i =1;
        //clona la primera fila
        $("#adicional").on('click', function(){
            $("#tabla tbody tr:eq(0)").clone().find('input:text').val('').end().find('input:text').attr('data-indice', i).val('').end().appendTo("#tabla");
            i++;
        });

        //elimina filas, pero siempre queda una como minimo
        $(document).on("click", ".eliminar", function(){
            if(tabla.rows.length > 2){
                var parent = $(this).parents().get(0);
                $(parent).remove();
            }              
        });

</script>
@stop
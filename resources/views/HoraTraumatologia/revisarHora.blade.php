@extends("Templates/template")

@section("titulo")
Solicitudes
@stop

@section("miga")
<li><a href="#">Solicitudes</a></li>
<li><a href="#" onclick='location.reload()'>Solicitudes</a></li>
@stop

@section("script")

<script>

var descargar = function(id){

    $.ajax({
                                url: "{{URL::to('/')}}/descargarAdjuntoHora",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {idSolicitud: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                                  location.href="{{URL::to('revisarHora')}}";
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
var cancelar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea cancelar la solicitud ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/cancelarHora",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {idSolicitud: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                          location.href="{{URL::to('revisarHora')}}";
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


var getArchivos = function(id){

    alert(id);
}



    $(function() {


    	$("#traumatologiaHoras").collapse();

        $('#tabs a[href="#aceptada"]').trigger('click');
        $('#tabs a[href="#encurso"]').trigger('click');


        $('#tabla-encurso').dataTable({ 
            "aoColumns": [
            {"bVisible": false},
            {"iDataSort": 0},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": false}
            ],
            "aaSorting": [[0, "asc"]],
            "iDisplayLength": 15,
            //"bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

        $('#tabla-aceptada').dataTable({ 
            "aoColumns": [
            {"bVisible": false},
            {"iDataSort": 0},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": false}
            ],
            "aaSorting": [[0, "asc"]],
            "iDisplayLength": 15,
            //"bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

        $('#tabla-rechazada').dataTable({ 
            "aoColumns": [
            {"bVisible": false},
            {"iDataSort": 0},
            {"bSortable": true},
            {"bSortable": true},
           
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": true},
            {"bSortable": false}
            ],
            "aaSorting": [[0, "asc"]],
            "iDisplayLength": 15,
            //"bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

        $('#tableArchivos').dataTable({ 
            
            "aaSorting": [[0, "asc"]],
            "iDisplayLength": 15,
            //"bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

$('#modalOpciones').on('show.bs.modal', function(e) {

    //get data-id attribute of the clicked element
    var bookId = $(e.relatedTarget).data('id');
    //populate the textbox
    $("#idSolicitud").val(bookId);
});




enviado = false;
$("#asignarCamasForm").bootstrapValidator({
            excluded: ':disabled',
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            evt.preventDefault(evt);
            var $form = $(evt.target);
            var fv = $form.data('bootstrapValidator');
            if(!enviado){
                $.ajax({
                    url: $form .prop("action"),
                    headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                    type: "post",
                    dataType: "json",
                    data: $form .serialize(),
                    async: false,
                    success: function(data){
                        enviado=true;
                        if(data.exito){

						swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						location.href="{{URL::to('revisarHora')}}";
							}, 2000)
						},
						});

                        
                        }
                        if(data.error)
                            swalError.fire({
                            title: 'Error',
                            text:data.error
                            });
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            }
        });



$("#modalArchivos").on("show.bs.modal", function(e) {
    var bookId = $(e.relatedTarget).data('id');
    id = bookId;



    $.ajax({
        url: "{{URL::to('/')}}/getArchivosHora",
        headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        type: "post",
        dataType: "json",
        data:{idSolicitud: id} ,
        async: false,
        success: function(data){
            console.log(data);
            if(data.length){
                $("#tableArchivos").dataTable().fnClearTable();
                $('#tableArchivos').dataTable().fnAddData(data); 
            }

 
            //[[1,2],[2,3]]
            
          
            
        
        },
        error: function(error){
                        console.log(error);
        }
        });
    

});
        

        
    });
</script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")
	<fieldset>
		<legend>Lista de horas</legend>


		<br><br>
		<div id="contenido"></div>
		
	</fieldset>
    

    <div role="tabpanel">
        <div id="tabs">    
            <ul class="nav nav-tabs" role="tablist">
                <?php
                    $active = "active";
                ?>
                @foreach($motivos as $motivo)
                    <li role="presentation" class="{{$motivo}} {{$active}}"><a href="#{{$motivo}}" role="tab" data-toggle="tab">

                    @if($motivo == "encurso")
                                    En curso
                                    @else
                                    {{ucfirst($motivo)}}
                                    @endif

                    </a></li>
                    <?php
                        $active = "";
                    ?>
                @endforeach

               

            </ul>
        </div>
        <div class="tab-content">
            <?php
                    $active = "active";
                ?>
            @foreach($motivos as $motivo)
                
                
                <div role="tabpanel" class="tab-pane {{$active}}" id="{{$motivo}}">
                   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tabla-{{$motivo}}" class="table table-striped table-bordered table-hover">
                                <tfoot>
                                    <tr>
                                        <th></th>
                                       <th>Fecha solicitud</th>
                                        <th>Paciente</th>
                                        <th>Establecimiento</th>
                                        <th>Comentario de solicitud</th>
                                        <th>Respuesta</th>
                                        <th>Médico</th>
                                        <th>Opciones</th>
                                    </tr>
                                </tfoot>
                                <thead>
                                    <tr>
                                    <th></th>
                                      <th>Fecha solicitud</th>
                                      
                                        <th>Paciente</th>
                                        <th>Establecimiento</th>
                                        <th>Comentario de solicitud</th>
                                        <th>Respuesta</th>
                                        <th>Médico</th>
                                        <th>Opciones</th>


                                    </tr>
                                </thead>
                                <tbody>
                            @foreach($response["$motivo"][0] as $solicitud)

                                <tr>
                                     <td>{{Carbon\Carbon::parse($solicitud->fecha_solicitud)}}</td>
                                    <td>{{Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i:s')}}</td>
                                    <td>({{$solicitud->rut}}-
                                    @if($solicitud->dv != '10')
                                    {{$solicitud->dv}})
                                    @else
                                    {{"K"}})
                                    @endif
                                    
                                    {{$solicitud->paciente_nombre}}</td>
                                    <td>{{$solicitud->est_nombre}}</td>
                                    
                                    <td>{{{ $solicitud->texto_solicitud}}}</td>
                                    <td>{{$solicitud->texto_respuesta}}</td>
                                    <td>{{$solicitud->nombre_medico}} {{$solicitud->apellido_medico}}</td>
                                    <td>
                                    <!-- si es quillota se muestra opciones -->
                                    @if($establecimiento ==1)
                                        @if($solicitud->estado=="encurso")
                                    <a href="#modalOpciones" data-id="{{$solicitud->id_solicitud_hora_traumatologia}}" data-toggle="modal" data-dismiss="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                        @endif
                                    @else
                                        @if($solicitud->estado=="encurso")
                                    <a onclick="cancelar({{$solicitud->id_solicitud_hora_traumatologia}})"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                        @endif
                                    @endif

                                    <?php 
                                    //$archivo= basename($solicitud->archivo);
                                    ?>
                                        
                                    <a href="#modalArchivos" data-id="{{$solicitud->id_solicitud_hora_traumatologia}}" data-toggle="modal" data-dismiss="modal"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span></a>                        

                                    @if($solicitud->archivo)
                                    <a href="{{URL::to('/')}}/descargarAdjuntoHora/{{$solicitud->id_solicitud_hora_traumatologia}}"><span class="glyphicon glyphicon-paperclip" aria-hidden="true" style="padding-left: 9px;"></span></a>
                                    @endif

                                    </td>
                                </tr>
                            
                            <?php 
                                $active = "";
                            ?>

                            @endforeach

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            
        </div>
        
        @endforeach
    </div>
    


<br><br>




<div id="modalOpciones" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>

            

            <div class="modal-body">
                {{ Form::open(array('url' => 'responderHora', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'asignarCamasForm')) }}

                <input type="hidden" id="idSolicitud" name="idSolicitud" value="">


                <fieldset><legend>Respuesta</legend>
                    <div id="divLoadBuscarPaciente" class="row" style="display: none;">
                        <div class="form-group col-md-12">
                            <span class="col-sm-5 control-label">Buscando paciente </span>
                            {{ HTML::image('images/ajax-loader.gif', '') }}
                        </div>
                    </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="fechaNac" class="col-sm-2 control-label">Respuesta: </label>
                        <div class="col-sm-10">
                            <select name="estado_solicitud" class='form-control'>
                                <option value='aceptada'>Aceptar</option>
                                <option value='rechazada'>Rechazar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="fechaNac" class="col-sm-2 control-label">Comentario: </label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="comentario"></textarea>
                        </div>
                    </div>
                </div>
                
                </fieldset>
                
                
                <div class="modal-footer">
                    {{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
                {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


<div id="modalArchivos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>

            

            <div class="modal-body">
                

                <input type="hidden" id="idSolicitud" name="idSolicitud" value="">


                <fieldset>
                    <legend>Archivos</legend>
                    <table id="tableArchivos">
                        <thead>
                            <tr>
                                <th>Archivo</th>
                                
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td></td>
                                
                            </tr>
                        </tbody>
                        

                    </table>
                
                </fieldset>
                
                
                <div class="modal-footer">
                    
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

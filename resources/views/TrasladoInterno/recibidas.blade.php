@extends("Templates/template")

@section("titulo")
    SIGICAM
@stop

@section("script")

    <script>

        var asignar=function(caso, lista, historialOcupacion){
            idCaso=caso;
            idLista=lista;
            idHistorialOcupacion = historialOcupacion;
            getUnidades();
            $("#modalIngresar").modal("show");
            $("#mensajeError").hide();
            setTimeout(function(){
                $("#modalIngresar").modal();
            },2000)

        }

        var cancelar=function(caso, lista, historialOcupacion){
            idCaso=caso;
            idLista=lista;
            idHistorialOcupacion = historialOcupacion;

            var dialog = bootbox.dialog({
                    //title: 'Se ha realizado el traslado interno',
                message: "<h4>¿Está seguro de cancelar el traslado interno?</h4>",
                buttons: {
                    cancel: {
                        label: "No",
                        className: 'btn-danger',
                    },
                    ok: {
                        label: "Si",
                        className: 'btn-primary',
                        callback: function(){
                            $.ajax({
                                url: "{{url('trasladoInterno/rechazarTraslado')}}",
                                headers: {        
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    idCaso: idCaso, 
                                    idLista: idLista, 
                                    idHistorialOcupacion:idHistorialOcupacion
                                },
                                type: "post",
                                success: function(data){

                                    if(data.exito){
                                        swalExito.fire({
                                            title: 'Exito!',
                                            text: data.exito,
                                            didOpen: function() {
                                                setTimeout(function() {
                                                    tableEnCurso.api().ajax.reload();
                                                }, 2000)
                                            },
                                        });
                                    }

                                    if(data.info){
                                        swalWarning.fire({
                                            title: data.info,
                                            text: data.mensaje
                                        }).then(function(result) {
                                            tableEnCurso.api().ajax.reload();
                                        });
									}

                                    if(data.error){
                                        swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                        }).then(function(result) {
                                            location.reload();
                                        });
                                    }

                                },
                                error: function(error){
                                    console.log(error);
                                }
                            });
                        }
                    }
                }
            });

        }

        var getUnidades=function(){
            var unidades=[];
            swalCargando.fire({});
            $.ajax({
                url: "{{URL::to('/')}}/getUnidades",
                headers: {        
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                type: "post",
                dataType: "json",
                data: {traslado: idCaso},
                async: false,
                success: function(data){
                    unidades=data;
                    $("#tabUnidad").empty();
                    $("#contentUnidad").empty();
                    var activo = 0;
                    for(var i=0; i<data.length; i++){
                        var nombre=data[i].url;
                        var id="id-"+nombre;
                        var active = "";

                        if (data[i].seleccionado == "true") {
                            console.log("activo", data[i].seleccionado, i)
                            active = "active";
                            activo = i;
                        }
                        $("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
                        $("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'></div>");
                        generarMapaCamasDisponibles(id, data[i].url, true);
                    }
                    
                    if(data.length > 0) {
                        $("#id-"+data[activo].url).addClass("active");
                        $("#id-"+data[activo].url).tab("show");
                    }

                    /* setTimeout(function () { */
                        /* swalCargando.close(); */
                        /* swalCargando.hideLoading();
                        console.log("cerrar get unidad");
                    }, 2000); */
                },
                error: function(error){
                    console.log("error get unidad");
                    swalCargando.hideLoading();
                    console.log(error);
                }
            });
            swalCargando.close();
            return unidades;
        }

        var generarMapaCamasDisponibles=function(mapaDiv, unidad){
            $.ajax({
                url: "{{URL::to('/')}}/unidad/"+unidad+"/getCamasDisponiblesVerdes",
                headers: {        
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                data: {unidad: unidad},
                dataType: "json",
                type: "post",
                success: function(data){
                    crearMapaCamas(mapaDiv, data);
                },
                error: function(error){
                    console.log(error);
                }
            });
        }


        var marcarCamaDisponible=function(event, cama){
            event.preventDefault();
            
            var dialog = bootbox.dialog({
                //title: 'Se ha realizado el traslado interno',
                message: "<h4>¿Está seguro de asignar esta cama al paciente?</h4>",
                buttons: {
                    cancel: {
                        label: "No",
                        className: 'btn-danger',
                    },
                    ok: {
                        label: "Si",
                        className: 'btn-primary',
                        callback: function(){
                            swalCargando.fire({});
                            $.ajax({
                                url: "{{url('trasladoInterno/asignarTraslado')}}",
                                headers: {        
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                data: {cama: cama, idCaso: idCaso, idLista: idLista, idHistorialOcupacion:idHistorialOcupacion},
                                type: "post",
                                success: function(data){
                                    /* setTimeout(function() { */
										swalCargando.close();
										//swalCargando.hideLoading();
									/* },2000) */
                                    
                                    if(data.error){
										swalError.fire({
											title: 'Error',
											text: data.error
										}).then(function(result) {
                                            tableEnCurso.api().ajax.reload();
										    $("#modalIngresar").modal('toggle');
                                        });
                                        
									}
                                    
                                    if (data.exito) {
                                        swalExito.fire({
											title: 'Exito!',
											text: data.exito,
											didOpen: function() {
												setTimeout(function() {
                                                    location.reload();
													swalCargando.fire({});
												}, 2000)
											},
										});
                                    }

                                    if(data.info){
                                        swalWarning.fire({
                                            title: data.info,
                                            text: data.mensaje
                                        }).then(function(result) {
                                            tableEnCurso.api().ajax.reload();
                                            $("#modalIngresar").modal('toggle');
                                        });
									}

                                    if(data.warning){
                                        swalSolicitudTraslado.fire({
                                            title: data.warning,
                                            text: data.mensaje
                                        }).then(function(result) {
                                            if (result.isConfirmed) {
                                                //Mover al paciente
                                                $.ajax({
                                                    url: "{{URL::to('/trasladoInterno')}}/confirmarTraslado",
                                                    headers: {        
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        },
                                                    data: {
                                                        caso: data.caso,
                                                        cama: data.camaDestino,
                                                        idlista: data.idLista
                                                    },
                                                    dataType: "json",
                                                    type: "post",
                                                    success: function(data){

                                                        if (data.exito) {
                                                            swalExito.fire({
                                                                title: 'Exito!',
                                                                text: data.exito,
                                                                didOpen: function() {
                                                                    setTimeout(function() {
                                                                        location.reload();
                                                                        swalCargando.fire({});
                                                                    }, 2000)
                                                                },
                                                            });
                                                        }

                                                        if (data.error) {
                                                            swalError.fire({
                                                                title: 'Error',
                                                                text: data.error
                                                            }).then(function(result) {
                                                                tableEnCurso.api().ajax.reload();
                                                                $("#modalIngresar").modal('toggle');
                                                            });
                                                        }
                                                        
                                                    },
                                                    error: function(error){
                                                        console.log(error);
                                                    }
                                                });
                                            }else{
                                                //Esta parte solo cierra el modal
                                                $("#modalIngresar").modal('toggle');
                                                tableEnCurso.api().ajax.reload();
                                            }
                                        });
									}
                                    

                                },
                                error: function(error){
                                    swalCargando.close();
									swalCargando.hideLoading();
                                    console.log(error);
                                }
                            });
                        }
                    }
                }
            });

        }

        var nosepuede=function(){
            $("#mensajeError").show();
        }

        function generarTablaEnCurso() {
            tableEnCurso = $("#tablaEnCurso").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableRecibidas/encurso')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }

        function generarTablaAceptado() {
            tableAceptado = $("#tablaAceptado").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableRecibidas/aceptado')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }

        function generarTablaRechazado() {
            tableRechazado = $("#tablaRechazado").dataTable({
                "iDisplayLength": 10,
                "ordering": true,
                "searching": true,
                "ajax": {
                    url: "{{URL::to('trasladoInterno/getTableRecibidas/rechazado')}}" ,
                    type: 'GET'
                },
                "oLanguage": {
                    "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
                },
                /* "initComplete":  */
            });
        }
        
        
        $(function(){

        });
    </script>

    @include("TrasladoInterno.script")
    <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Espera de traslado</a></li>
@stop

@section("section")

    @include("TrasladoInterno.css")

<fieldset>
	<legend>Espera de traslado interno</legend>
	<div class="table-responsive">
		<div role="tabpanel">
        <div id="tabs">   
            <ul class="nav nav-tabs primerNav" role="tablist">
                <li role="presentation" class="active" id="enCurso"><a href="#id-motivo-1" role="tab" data-toggle="tab">En Curso</a></li>
                <li role="presentation" class="" id="aceptada"><a href="#id-motivo-2" role="tab" data-toggle="tab">Aceptado</a></li>
                <li role="presentation" class="" id="rechazado"><a href="#id-motivo-3" role="tab" data-toggle="tab">Rechazado</a></li>
            </ul>
        </div>
        <br>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="id-motivo-1">
                <table id="tablaEnCurso" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Fecha nacimiento</th>
                            <th>Diagnóstico</th>
                            {{-- <th>Comentario</th> --}}
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>
                            <!-- <th>Categorización</th> -->
                            <th>Opciones</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="id-motivo-2">
                <table id="tablaAceptado" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Fecha nacimiento</th>
                            <th>Diagnóstico</th>
                            {{-- <th>Comentario</th> --}}
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>
                            <!-- <th>Categorización</th> -->
                            {{-- <th>Opciones</th> --}}
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="id-motivo-3">
                <table id="tablaRechazado" class="table  table-condensed table-hover">
                    <thead>
                        <tr style="background:#399865;">
                            <th style="width:100px">Run</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Fecha nacimiento</th>
                            <th>Diagnóstico</th>
                            {{-- <th>Comentario</th> --}}
                            <th width="120">Fecha solicitud</th>
                            <th>Servicio Origen</th>
                            <th>Servicio Destino</th>
                            <th>Usuario que solicita</th>
                            <th>Riesgo - Dependencia</th>
                            <!-- <th>Categorización</th> -->
                            {{-- <th>Opciones</th> --}}
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
	    </div>
</div>
	</div>
</fieldset>



<div id="modalIngresar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Asignar cama</h4>
			</div>
			<div class="modal-body">
			<div id="mensajeError" class="alert alert-danger" role="alert" hidden>
				<h4>La cama no puede ser seleccionada</h4>
			</div>
				<div class="row">
					<ul id="tabUnidad" class="nav nav-tabs" role="tablist">
					</ul>
					<div id="contentUnidad" class="tab-content">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
@stop
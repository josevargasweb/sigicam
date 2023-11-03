@extends("Templates/template")

@section("titulo")
    {{$solicitud}}
@stop


@section("miga")
    <li><a href="#">Solicitudes de Traslado Externo</a></li>
    <li><a href="#" onclick='location.reload()'>{{$miga}}</a></li>
@stop

@section("script")
    <script>

    $(function(){
        var mensaje="{{ $mensajes }}";
        var tipo="{{ $tipo }}";
        if(mensaje != "" && tipo=='recibidas')
        {
            $("#Alerta").modal("show");
        }
            
    });

        var enCurso = function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea restablecer el caso?</h4><br>"+id,
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/restablecerDerivacion",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                                 location.href="/derivaciones/{{$tipo}}";
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
        
        var verMensajes=function(id){
            getMensajeTraslado(id);
            $("#modalMensajes").modal("show");
        }

        var cancelar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea cancelar el traslado externo ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/cancelarTraslado",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                            location . reload();
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
        /*
        var aceptar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea marcar este traslado como aceptado ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/aceptarAdmin",
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito) ("<h4>"+data.exito+"</h4>", function(){
                                        location.href="/derivaciones/{{$tipo}}";
                                    });
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
        */

        var aceptar = function(id){
            bootbox.prompt({
                title: "<h4>¿ Desea marcar este traslado como aceptado ?</h4>",
                inputType: 'date',
                callback: function (result) {
                    console.log(result);
                    if(result != null)
                    {
                        $.ajax({
                                url: "{{URL::to('/')}}/aceptarAdmin",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id, fecha_cierre:result},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    console.log(data);
                                    if(data.exito) swalExito.fire({
                                                    title: 'Exito!',
                                                    text: data.exito,
                                                    didOpen: function() {
                                                        setTimeout(function() {
                                                        location.href="{{URL::to('/')}}/derivaciones/{{$tipo}}";
                                                        }, 2000)
                                                    },
                                                    });
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
                }

        });

        }

        var rechazar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea marcar este traslado como rechazado ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/rechazarAdmin",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito) swalExito.fire({
                                                    title: 'Exito!',
                                                    text: data.exito,
                                                    didOpen: function() {
                                                        setTimeout(function() {
                                                    location.href="{{URL::to('/')}}/derivaciones/{{$tipo}}";
                                                        }, 2000)
                                                    },
                                                    });
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

        var cancelar_aceptacion=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea cancelar el estado de aceptación del traslado externo ?</h4><br>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/cancelarAceptacionTraslado",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito) swalExito.fire({
                                                    title: 'Exito!',
                                                    text: data.exito,
                                                    didOpen: function() {
                                                        setTimeout(function() {
                                                                location.href="{{URL::to('/')}}/derivaciones/{{$tipo}}";
                                                        }, 2000)
                                                    },
                                                    });
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

        var responder=function(id){

        }

        var resolicitar=function(id){
            bootbox.dialog({
                message: "<h4>¿ Desea resolicitar la solicitud ?</h4>",
                title: "Confirmación",
                buttons: {
                    success: {
                        label: "Aceptar",
                        className: "btn-primary",
                        callback: function() {
                            $.ajax({
                                url: "{{URL::to('/')}}/resolicitarTraslado",
                                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                                data: {id: id},
                                type: "post",
                                dataType: "json",
                                success: function(data){
                                    if(data.exito){
                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                              location . reload();
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

        var getRiesgos=function(){
            var riesgos=[];
            $.ajax({
                url: "{{URL::to('/')}}/getRiesgos",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "post",
                async: false,
                success: function(data){
                    riesgos=data;
                },
                error: function(error){
                    console.log(error);
                }
            });
            return riesgos;
        }
        var display_tiempo_espera = function(source, type, val){
            if (type === 'set'){
                return;
            }
            if (type === 'display'){
                return source.tiempo_espera_format;
            }
            if (type === 'filter'){
                return source.tiempo_espera_format;
            }
            return source.tiempo_espera;
        }

        var display_fecha_solicitud = function(source, type, val){
            if(type === 'set') return;
            if(type === 'display' || type === 'filter') return source.fecha_format;
            return source.fecha;
        }
        var display_rut_paciente = function(source, type, val){
            if(type === 'set') return;
            if(type === 'display' || type === 'filter') return source.rut_paciente + "-" + source.dv_paciente;
            return source.rut_paciente;
        }

        var display_rut_medico = function(source, type, val){
            if(type === 'set') return;
            if(type === 'display' || type === 'filter') return source.usuario_solicitante + "-" + source.dv_usuario;
            return source.usuario_solicitante;
        }
        var getDerivaciones=function(nombre, id){
            $.ajax({
                url: "{{URL::to('/')}}/getDerivaciones",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {param: "{{$tipo}}", motivo: nombre, registros: "{{$cantRegistros}}"},
                type: "get",
                dataType: "json",
                success: function(data){
                    console.log("MMM", data.data);
                    sessionStorage.setItem(nombre, JSON.stringify(data.ids));


                    var tabla=$("#tabla-"+id).dataTable().columnFilter({
                        aoColumns: [
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS)
                            {type: "text"},
                                @endif
                                {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            {type: "text"},
                            null,
                            null
                        ]
                    });
                    getRiesgos();
                    tabla.fnClearTable();
                    if(data.data){
                        console.log(data.data);
                        tabla.fnAddData(data.data);
                        tabla.fnSort([ [0, 'desc'] ]);
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }

        var getMensajeTraslado=function(id){
            $.ajax({
                url: "{{URL::to('/')}}/getMensajeTraslado",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {id: id},
                type: "post",
                dataType: "json",
                success: function(data){
                    console.log(data.derivaciones);
                    if(data.derivaciones.length){
                        $("#tableMensajes").dataTable().fnClearTable();
                        $('#tableMensajes').dataTable().fnAddData(data.derivaciones);
                    }

                    if(data.archivos.length){
                        $("#tableArchivos").dataTable().fnClearTable();
                        $('#tableArchivos').dataTable().fnAddData(data.archivos);
                    }

                },
                error: function(error){
                    console.log(error);
                }
            });
        }

        var getMotivos=function(){
            $.ajax({
                url: "{{URL::to('/')}}/obtenerMotivos",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                dataType: "json",
                success: function(data){
                    for(var i=0; i<data.length; i++){
                        getDerivaciones(data[i].nombre, data[i].id);
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }



        

        $(function(){


            var tabSeleccionado = sessionStorage.getItem("tabSeleccionado");
            console.log(tabSeleccionado);  
            if(tabSeleccionado != null)
            {
                index = tabSeleccionado.indexOf("#");

                texto = tabSeleccionado.substr(index);

                console.log(texto);
                $('a[href="'+texto+'"]').click(); 
            }
            
            //



            $("#solicitudMenu").collapse();
            $.fn.dataTableExt.oSort['estado-solicitud-desc'] = function(a,b){
                var prioridades = {'rechazado': 3, 'aceptado': 2, 'en curso': 1};
                var x = prioridades[a];
                var y = prioridades[b];
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            };
            $.fn.dataTableExt.oSort['estado-solicitud-asc'] = function(a,b){
                var prioridades = {'rechazado': 3, 'aceptado': 2, 'en curso': 1};
                var x = prioridades[a];
                var y = prioridades[b];
                return ((x < y) ? 1 : ((x > y) ? -1 : 0));
            };

            $('.derivaciones').dataTable({
                "aaSorting": [[9, "desc"], [0, "asc"]],
                dom: 'Bfrtip',
                //"scrollX": true,
                "scrollCollapse": true,
                "bAutoWidth": false,
                buttons: ['excel'],
                "iDisplayLength": 25,
                //"bJQueryUI": true,
                "oLanguage": {
                    "sUrl": "{{URL::to('/')}}/js/spanish.txt"
                },
                aoColumns: [
                    {"mData": function(source, type, val) {return display_fecha_solicitud(source, type, val);}},
                    {"mData": "numero_solicitud"},
                    {"mData": function(source, type, val) {return display_tiempo_espera(source, type, val);} },
                    @if(Session::get("usuario")->tipo === TipoUsuario::ADMINSS)
                    {"mData": "estab_origen"},
                        @endif
                        {"mData": "estab_destino"},
                    {"mData": "unidad_destino"},
                    {"mData": "usuario_solicitante" },
                    {"mData": "nombre_paciente"},
                    {"mData": function(source, type, val) {return display_rut_paciente(source, type, val);}},
                    {"mData": "edad_paciente"},
                    {"mData": "diagnostico"},
                    {"mData": "riesgo"},
                    {"mData": "estado"},
                    {"mData": "opciones"}
                ]
            });

            getMotivos();


            $('#tableMensajes').dataTable({
                "aaSorting": [[0, "desc"]],
                "bJQueryUI": true,
                "searching": false,
                "scrollCollapse": true,
                "paging": false,
                "lengthChange": false,
                "info": false,
                "iDisplayLength": 10,
                "oLanguage": {
                    "sUrl": "{{URL::to('/')}}/js/spanish.txt"
                }
            });

            $('#tableArchivos').dataTable({
                "aaSorting": [[0, "desc"]],
                "bJQueryUI": true,
                "searching": false,
                "scrollCollapse": true,
                "paging": false,
                "lengthChange": false,
                "info": false,
                "iDisplayLength": 10,
                "oLanguage": {
                    "sUrl": "{{URL::to('/')}}/js/spanish.txt"
                }
            });




            $("a[data-toggle='tab']").click(function(e){
            
            sessionStorage.setItem("tabSeleccionado", this.href);

            });


        });

    </script>
      <meta name="csrf-token" content="{{{ Session::token() }}}">

@stop

@section("section")

    
    @if($cantRegistros != 10000)
    <div class="alert alert-info">Mostrando los últimos {{$cantRegistros}} registros, si desea ver el historial completo <a href='{{URL::to("/derivaciones/$tipo/10000")}}'>click aquí</a> </div>
    
    @endif
    
    <div role="tabpanel">
        <div id="tabs">    
            <ul class="nav nav-tabs" role="tablist">
                @foreach($motivos as $motivo)
                    <li role="presentation" class="{{$motivo['active']}}"><a href="#{{$motivo['id']}}" role="tab" data-toggle="tab">{{$motivo["nombre"]}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="tab-content">
            @foreach($motivos as $motivo)
                

                <div role="tabpanel" class="tab-pane {{$motivo['active']}}" id="{{$motivo['id']}}">
                    <br>
                    <div class="table-responsive" style="overflow:auto;">
                    <!--class="derivaciones table table-striped table-condensed table-bordered" -->
                        <table id="tabla-{{$motivo['id']}}" class="derivaciones table table-striped table-condensed table-bordered "  >
                            <tfoot>
                            <tr>
                                <th>Fecha solicitud</th>
                                <th>Numero solicitud</th>
                                <th>Tiempo de espera</th>
                                @if ($tipo == "enviadas")
                                    @if(Auth::user()->tipo == TipoUsuario::ADMINSS)
                                        <th>Establecimiento origen</th>
                                    @endif
                                    <th>Establecimiento destino</th>
                                @else
                                    <th>Establecimiento origen</th>
                                    @if(Auth::user()->tipo == TipoUsuario::ADMINSS)
                                        <th>Establecimiento destino</th>
                                    @endif
                                @endif
                                <th>Servicio</th>
                                <th>Usuario</th>
                                <th>Paciente</th>
                                <th>Run</th>
                                <th>Edad</th>
                                <th>Diagnóstico</th>
                                <th>Riesgo</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <thead>
                            <tr>
                                <th>Fecha solicitud</th>
                                <th>Numero solicitud</th>
                                <th>Tiempo de espera</th>
                                @if ($tipo == "enviadas")
                                    @if(Auth::user()->tipo == TipoUsuario::ADMINSS)
                                        <th>Establecimiento origen</th>
                                    @endif
                                    <th>Establecimiento destino</th>
                                @else
                                    <th>Establecimiento origen</th>
                                    @if(Auth::user()->tipo == TipoUsuario::$ADMINSS)
                                        <th>Establecimiento destino</th>
                                    @endif
                                @endif
                                <th>Servicio</th>
                                <th>Usuario</th>
                                <th>Paciente</th>
                                <th>Run</th>
                                <th>Edad</th>
                                <th>Diagnóstico</th>
                                <th>Riesgo</th>
                                <th>Estado solicitud</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody ></tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="modalMensajes" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Mensajes</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tableMensajes" class="table table-striped table-condensed table-bordered" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Establecimiento</th>
                                <th>Mensaje</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                        <table id="tableArchivos" class="table table-striped table-condensed table-bordered" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Nombre archivo</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        var generarMapaCamasDisponibles=function(mapaDiv, unidad){
            $.ajax({
                url: "getCamas",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                type: "post",
                data: {unidad: unidad},
                success: function(data){
                    crearMapaCamas(mapaDiv, data);
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
        var getUnidades=function(){
            var unidades=[];
            $.ajax({
                url: "{{URL::to('/getUnidades')}}",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                dataType: "json",
                async: false,
                success: function(data){
                    unidades=data;
                    $("#tabUnidad").empty();
                    $("#contentUnidad").empty();
                    for(var i=0; i<data.length; i++){
                        var active = (i == 0) ? "active" : "";
                        var nombre=data[i].url;
                        var id="id-"+nombre;
                        $("#tabUnidad").append("<li class="+active+"><a href='#"+id+"' role='tab' data-toggle='tab'>"+data[i].alias+"</a></li>");
                        $("#contentUnidad").append("<div id='"+id+"' class='tab-pane' id='"+data[i].url+"' style='margin-top: 20px;'><h4>No hay camas disponibles</h4></div>");
                        generarMapaCamasDisponibles(id, data[i].url, true);
                    }
                    for(var i=0; i<data.length; i++){
                        $("#id-"+data[i].url).removeClass("active");
                    }
                    if(data.length > 0) {
                        $("#id-"+data[0].url).addClass("active");
                        $("#id-"+data[0].url).tab("show");
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
            return unidades;
        }
        var mostrarCamas=function(idCaso){
            $("#traslado").val(idCaso);
            getUnidades();
            $("#modalCamasDisponibles").modal("show");
        }

        var aceptarCama=function(cama){
            $("#cama").val(cama);
            $("#modalCama").modal("hide");
            $("#modalCamasDisponibles").modal("hide");
            $.ajax({
                type: "post",
                data: $("#form-reservar-pendiente").serialize(),
                url: "reservarPendiente",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data){
                    if(data.exito){
                        	swalExito.fire({
                            title: 'Exito!',
                            text: data.exito,
                            didOpen: function() {
                                setTimeout(function() {
                                    location.href="{{ URL::to("derivaciones/recibidas") }}";
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

                }
            });
        }

        var cambiarDestino = function(id_derivacion){
            $("#id-derivacion").val(id_derivacion);
            $("#lista-establecimientos").trigger("change");
            $("#modalCambiarEstablecimiento").modal("show");
        }

        var aceptarDestino = function(){
            $("#modalCambiarEstablecimiento").modal("hide");
            $.ajax({
                type: "post",
                headers: {        
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $("#form-cambiar-destino").serialize(),
                url: "cambiarDestino",
                dataType: "json",
                success: function(data){
                    	swalWarning.fire({
						title: 'Información',
						text:data.mensaje
						}).then(function(result) {
							  location.href="{{ URL::to("derivaciones/enviadas") }}";
						});
                   
                },
                error: function(error){}
            });
        }

        var marcarCamaDisponible=function(cama, sala, unidad){
            var msg="¿ Desea seleccionar la cama de la sala "+sala+" del servicio "+unidad+" ?";
            $("#msgCama").text(msg);
            var hora=$("#horas").val();
            var click="aceptarCama(\""+cama+"\")";
            $("#btnCama").attr("onclick", click);
            $("#modalCama").modal("show");
        }

    </script>
    <div id="modalCamasDisponibles" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Reserva de cama para traslado</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="salaReasignar"/>
                    <input type="hidden" id="camaReasignar"/>
                    <input type="hidden" id="casoReasignar"/>
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
    <div id="modalCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        {{ Form::open(array("id" =>	"form-reservar-pendiente") ) }}
        {{ Form::hidden('idCama', '', array('id' => "cama")) }}
        {{ Form::hidden('idTraslado', '', array('id' => "traslado")) }}
        {{ Form::hidden('horaHidden', '', array('id' => "horaHidden")) }}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Confirmación</h4>
                </div>
                <div class="modal-body">
                    <h4 id="msgCama"></h4><br>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="horas" class="col-sm-2 control-label">Horas de reserva: </label>
                            <div class="col-sm-10">
                                <select name="horas" id="horas" class="form-control" required=""><option value="6">6 horas</option><option value="5">5 horas</option><option value="4">4 horas</option><option value="3">3 horas</option><option value="2">2 horas</option><option value="1">1 hora</option></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="btnCama" href="#" class="btn btn-primary" onclick="">Aceptar</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
    <div id="modalCambiarEstablecimiento" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        {{ Form::open(array("id" =>	"form-cambiar-destino") ) }}
        {{ Form::hidden('id-derivacion', '', array('id' => "id-derivacion")) }}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Seleccionar nuevo establecimiento destino</h4>
                </div>
                <div class="modal-body">
                    <!--<h4 id="msgCama"></h4><br>-->
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="horas" class="col-sm-3 control-label">Establecimiento: </label>
                            <div class="col-sm-9">
                                {{ Form::select("establecimiento", App\Models\Establecimiento::getEstablecimientos(false, [Session::get("idEstablecimiento")]), null, ["id" => "lista-establecimientos"]) }}
                                {{ Form::select("servicio-destino", [], null, ["id" => "lista-servicios"]) }}
                                <script>
                                    $(function(){
                                        $("#lista-establecimientos").on("change", function(ev){
                                            var lista = $(this);
                                            $("#lista-servicios").html("<option>Cargando...</option>");
                                            $.ajax({
                                                type: "post",
                                                headers: {        
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                url: "{{URL::to("/getUnidades")}}",
                                                data: {id: lista.val()},
                                                dataType: "json",
                                                success : function(data){
                                                    var str = "";
                                                    for(var i = 0; i < data.length; i++){
                                                        var s = data[i].cupo > 1? 's':'';
                                                        str += '<option value="' + data[i].id + '">' + data[i].alias + ' ('+data[i].cupo+' cupo'+s+')</option>';
                                                    }
                                                    $("#lista-servicios").html(str);
                                                },
                                                error : function(error){

                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="btnCambiarDestino" href="#" class="btn btn-primary" onclick="aceptarDestino()">Aceptar</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>


<!-- Modal -->
<div id="Alerta" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tiene derivaciones pendientes para ser revisadas</h4>
      </div>
      <div class="modal-body">
    <p style="text-align: justify;"><b><?php echo $mensajes;?></b></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>    
@stop
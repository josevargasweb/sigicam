@extends("Templates/template")

@section("titulo")
    Traslado Extra Sistema
@stop


@section("miga")
<li><a href="#">Solicitudes de Traslado Externo</a></li>
<li><a href="#" onclick='location.reload()'>Traslado Extra Sistema</a></li>
@stop

@section("script")

    <script>

        var mostrarCamasExtraSistema = function (idCaso, idExtra) {
            $("#idCaso").val(idCaso);
            $("#idExtra").val(idExtra);
            getUnidades();
            $("#modalRescate").modal("show");
        }

        var terminarCaso = function (idCaso) {
            $("#casoExAlta").val(idCaso);
            $("#modalAlta").modal("show");
        }

        var buscarPaciente = function () {
            $.ajax({
                url: "buscarPacientePorCaso",
                data: {idCaso: $("#idCaso").val()},
                dataType: "json",
                type: "post",
                async: false,
                success: function (data) {
                    //alert();
                    $("#id").val(data.id);
                    $("#rut").val(data.rut);
                    $("#dv").val(data.dv);
                    $("#fechaNac").val(data.fecha);
                    $("#nombre").val(data.nombre);
                    $("#apellidoP").val(data.apellidoP);
                    $("#apellidoM").val(data.apellidoM);
                    $("#sexo").val(data.genero);

                    $("#riesgo").val(data.riesgo);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        var rescatar = function (idCama) {
            $("#camaReserva").val(idCama);
            $("#modalRescate").modal("hide");
            buscarPaciente();
            $("#modalAsignacionCama").modal("show");
        }

        var generarMapaCamasDisponibles = function (mapaDiv, unidad) {
            $.ajax({
                url: "getCamasParaRescate",
                data: {unidad: unidad},
                dataType: "json",
                type: "post",
                success: function (data) {
                    crearMapaCamas(mapaDiv, data);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        var getUnidades = function () {
            $.ajax({
                url: "obtenerUnidades",
                type: "post",
                dataType: "json",
                async: false,
                success: function (data) {
                    unidades = data;
                    $("#tabUnidad").empty();
                    $("#contentUnidad").empty();
                    for (var i = 0; i < data.length; i++) {
                        var nombre = data[i].alias;
                        var id = "id-" + nombre;
                        var active = (i == 0) ? "active" : "";
                        $("#tabUnidad").append("<li class=" + active + "><a href='#" + id + "' role='tab' data-toggle='tab'>" + data[i].nombre + "</a></li>");
                        $("#contentUnidad").append("<div id='" + id + "' class='tab-pane' id='" + data[i].alias + "' style='margin-top: 20px;'></div>");
                        generarMapaCamasDisponibles(id, data[i].alias);
                    }
                    if (data.length > 0) {
                        $("#id-" + data[0].alias).addClass("active");
                        $("#id-" + data[0].alias).tab("show");
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        $(function () {
            $("#solicitudMenu").collapse();

            $('#derivacionesExtra').dataTable({
                "iDisplayLength": 15,
                "bJQueryUI": true,
                "oLanguage": {
                    "sUrl": "{{URL::to('/')}}/js/spanish.txt"
                }
            }).columnFilter({
                aoColumns: [
                    {type: "text"},
                    {type: "text"},
                    {type: "text"},
                    {type: "text"},
                    null,
                    null
                    @if(Session::get("usuario")->tipo !== TipoUsuario::ADMINSS)
                    ,null
                    @endif
                ]
            });

            $("#fechaNac").datepicker({
                autoclose: true,
                language: "es",
                format: "dd-mm-yyyy",
                todayHighlight: true,
                endDate: "+0d"
            }).on("changeDate", function () {
                $('#asignarCamasForm').bootstrapValidator('revalidateField', 'fechaNac');
            });

            $("#asignarCamasForm").bootstrapValidator({
                excluded: ':disabled',
                fields: {
                    /*rut: {
                     validators:{
                     notEmpty: {
                     message: 'El rut es obligatorio'
                     }
                     }
                     },*/
                    nombre: {
                        validators: {
                            notEmpty: {
                                message: 'El nombre es obligatorio'
                            }
                        }
                    },
                    fechaNac: {
                        validators: {
                            notEmpty: {
                                message: 'El fecha de nacimiento es obligatoria'
                            },
                            callback: {
                                callback: function (value, validator, $field) {
                                    if (value === '') {
                                        return true;
                                    }
                                    var esMayor = esFechaMayor(value);
                                    if (esMayor) {
                                        return {
                                            valid: false,
                                            message: "La fecha de nacimiento no puede ser mayor a la fecha actual"
                                        };
                                    }
                                    var esValidao = validarFormatoFecha(value);
                                    if (!esValidao) return {valid: false, message: "Formato de fecha inválido"};
                                    return true;
                                }
                            }
                        }
                    },
                    diagnostico: {
                        validators: {
                            notEmpty: {
                                message: 'El diagnóstico es obligatorio'
                            }
                        }
                    },
                    motivo: {
                        validators: {
                            notEmpty: {
                                message: 'El motivo es obligatorio'
                            }
                        }
                    }
                }
            }).on('status.field.bv', function (e, data) {
                data.bv.disableSubmitButtons(false);
            }).on("success.form.bv", function (evt) {
                evt.preventDefault(evt);
                var $form = $(evt.target);
                $.ajax({
                    url: $form.prop("action"),
                    type: "post",
                    dataType: "json",
                    data: $form.serialize(),
                    success: function (data) {
                        if (data.exito) {
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
                        if (data.error) {
                            swalError.fire({
                            title: 'Error',
                            text:data.error
                            });
                            console.log(data.error);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
                $("#asignarCamasForm input[type='submit']").prop("disabled", false);
            });

            $("#formExAlta").on("click", function (e) {
                console.log("hola");
                $("#modalAlta").modal("hide");
            });


            $("#formExAlta").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).prop("action"),
                    type: "post",
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data.exito) {
                            swalExito.fire({
                            title: 'Exito!',
                            text: "Se ha dado el alta",
                            didOpen: function() {
                                setTimeout(function() {
                                     location . reload();
                                }, 2000)
                            },
                            });
                        }
                        if (data.error) {
                            swalError.fire({
                            title: 'Error',
                            text:"Error al dar el alta"
                            }).then(function(result) {
                            if (result.isDenied) {
                                location . reload();
                            }
                            });
                         

                            console.log(data.error);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
                return false;
            });
        })
    </script>

@stop

@section("section")
    <div class="table-responsive">
        <table id="derivacionesExtra" class="table table-striped table-condensed table-bordered">
            <tfoot>
            <tr>
                @if(Auth::user()->tipo == TipoUsuario::$ADMINSS) <th>Hospital origen</th> @endif
                <th>Clínica</th>
                <th>Servicio</th>
                <th>Paciente</th>
                <th>Diagnóstico</th>
                <th></th>
                <th></th>
                    @if(Session::get("usuario")->tipo !== TipoUsuario::ADMINSS)
                    <th></th>
                    @endif
            </tr>
            </tfoot>
            <thead>
            <tr>
                @if(Auth::user()->tipo == TipoUsuario::$ADMINSS) <th>Hospital origen</th> @endif
                <th>Clínica</th>
                <th>Servicio</th>
                <th>Paciente</th>
                <th>Diagnóstico</th>
                <th>Fecha de derivación</th>
                <th>Cupos</th>
                    @if(Session::get("usuario")->tipo !== TipoUsuario::ADMINSS)
                    <th>Opciones</th>
                        @endif
            </tr>
            </thead>
            <tbody>
            @foreach($cupos as $cupo)
                <tr>
                    @if(Auth::user()->tipo == TipoUsuario::$ADMINSS) <td>{{$cupo->nombre_est}}</td> @endif
                    <td>{{$cupo->est_ex}}</td>
                    <td>{{$cupo->nombre_unidad}}</td>
                    <td>
                        @if($cupo->rut)
                            {{App\Models\Paciente::formatearRut($cupo->rut,$cupo->dv)}}
                        @else
                            Run no disponible
                        @endif
                    </td>
                    <td>{{""}}</td>
                    <td>{{date("d-m-Y h:i:s", strtotime($cupo->fecha))}}</td>
                    <td>{{$cupo->cantidad}}</td>
                        @if(Session::get("usuario")->tipo !== TipoUsuario::ADMINSS)
                    <td>

                            @if($cupo->cantidad > 0)
                                <a onclick="{{ "mostrarCamasExtraSistema( {$cupo->caso}, {$cupo->id} )" }}" class="cursor">Rescatar</a>
                            @endif
                            <br>
                            <a onclick="{{ "terminarCaso( {$cupo->caso}, {$cupo->id})" }}" class="cursor">Dar alta</a>
                    </td>
                        @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div id="modalRescate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Seleccionar cama</h4>
                </div>
                <div class="modal-body">
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

    <div id="modalAlta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Confirmación</h4>
                </div>
                {{ Form::open(array('url' => "trasladar/altaExtraSistema", 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formExAlta')) }}
                {{ Form::hidden('caso', '', array('id' => 'casoExAlta')) }}
                <div class="modal-body">
                    <div class="row" style="margin: 0;">
                        <div class="form-group col-md-12">
                            <h4>¿ Desea dar de alta al paciente ?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="solicitar" type="submit" class="btn btn-primary">Dar alta</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div id="modalAsignacionCama" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Solicitar cama</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('url' => 'trasladar/rescatar', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'asignarCamasForm')) }}
                    {{ Form::hidden('cama', '', array('id' => 'camaReserva')) }}
                    {{ Form::hidden('idCaso', '', array('id' => 'idCaso')) }}
                    {{ Form::hidden('idExtra', '', array('id' => 'idExtra')) }}
                    {{ Form::hidden('id', '', array('id' => 'id') ) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="rut" class="col-sm-2 control-label">Run: </label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    {{Form::text('rut', null, array('id' => 'rut', 'class' => 'form-control', 'readonly'))}}
                                    <span class="input-group-addon"> - </span>
                                    {{Form::text('dv', null, array('id' => 'dv', 'class' => 'form-control', 'style' => 'width: 70px;', 'readonly'))}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="fechaNac" class="col-sm-2 control-label">Fecha de nacimiento: </label>

                            <div class="col-sm-10">
                                {{Form::text('fechaNac', null, array('id' => 'fechaNac', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="nombre" class="col-sm-2 control-label">Nombre: </label>

                            <div class="col-sm-10">
                                {{Form::text('nombre', null, array('id' => 'nombre', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="apellidopaterno" class="col-sm-2 control-label">Apellido Paterno: </label>

                            <div class="col-sm-10">
                                {{Form::text('apellidoP', null, array('id' => 'apellidoP', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="apellidomaterno" class="col-sm-2 control-label">Apellido Materno: </label>

                            <div class="col-sm-10">
                                {{Form::text('apellidoM', null, array('id' => 'apellidoM', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="fecha" class="col-sm-2 control-label">Género: </label>

                            <div class="col-sm-10">
                                {{ Form::select('sexo', array('masculino' => 'Masculino', 'femenino' => 'Femenino', 'indefinido' => 'Indefinido'), null, array('id' => 'sexo', 'class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="fecha" class="col-sm-2 control-label">Diagnóstico: </label>

                            <div class="col-sm-10">
                                {{Form::text('diagnostico', null, array('id' => 'diagnostico', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="riesgo" class="col-sm-2 control-label">Riesgo: </label>

                            <div class="col-sm-10">
                                {{ Form::select('riesgo', $riesgo, null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="riesgo" class="col-sm-2 control-label">Medico: </label>

                            <div class="col-sm-10">
                                {{Form::text('medico', null, array('id' => 'medico', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="horas" class="col-sm-2 control-label">Horas de reserva: </label>

                            <div class="col-sm-10">
                                {{ Form::select('horas', array('1' => '1 hora', '2' => '2 horas', '3' => '3 horas', '4' => '4 horas', '5' => '5 horas', '6' => '6 horas'), null, array('id' => 'horas', 'class' => 'form-control')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="motivo" class="col-sm-2 control-label">Motivo: </label>

                            <div class="col-sm-10">
                                {{Form::textarea('motivo', null, array('id' => 'motivoC', 'class' => 'form-control', 'rows' => '5'))}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{Form::submit('Aceptar', array('id' => 'btnAceptarAsingar', 'class' => 'btn btn-primary')) }}
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    </div>

@stop

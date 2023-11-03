@extends("Templates/template")

@section("titulo")
Estado Pacientes: Exámenes / Estudios / Procedimientos
@stop

@section("miga")
<li><a href="#" onclick='location.reload()'>Exámenes / Estudios / Procedimientos</a></li>
@stop

@section("script")
    <script>
        $("#urgenciaMenu").collapse();

        /* Abre el modal de examenes */
        function examenes(idCaso) {
            $("#detalle-diagnostico").val(idCaso);
            $.ajax({
                url: "{{ URL::to('/examenesCaso') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {caso: idCaso},
                dataType: "json",
                type: "post",
                success: function (data) {
                    $("#modalVerExamenes .modal-body").html(data.contenido);
                    $("#modalVerExamenes").modal();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        /* Sacar de lista estudios */
        function sacarListaEEP(idCaso) {
            bootbox.confirm({				
                message: "<h4>¿Está seguro de sacar de la lista de Exámenes / Estudios / Procedimientos?</h4>",				
                buttons: {					
                    confirm: {					
                        label: 'Si',					
                        className: 'btn-success'					
                    },					
                    cancel: {					
                        label: 'No',					
                        className: 'btn-danger'					
                    }				
                },				
                callback: function (result) {									
                    if(result){					
                        $.ajax({
                            url: "{{URL::to('/')}}/sacarListaEEP",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  {
                                id: idCaso
                            },
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                if (data.exito) {
                                  swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tablaListaEEP.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    }).then(function(result) {
                                    if (result.isDenied) {
                                        location . reload();
                                    }
                                    });
                                   
                                }
                            },
                            error: function(error){
                                console.log(error);
                            }
                        });				
                    }else{
                        tablaListaEEP.api().ajax.reload();
                    }				
                }
            }); 
        }
        

        function rutConDV(data, type, dataToSet) {
            var rut = (data.rut) ? parseInt(data.rut).toLocaleString("es-Es") + "-" + data.dv : "-";
            return rut;
        }

        function botonOpciones(data, type, dataToSet) {
            let htmlDivBotonOpciones = "";
            @if(Session::get("usuario")->tipo !== TipoUsuario::DIRECTOR && Session::get("usuario")->tipo !== TipoUsuario::MEDICO_JEFE_DE_SERVICIO)
                htmlDivBotonOpciones = '<div class="dropdown">';
                htmlDivBotonOpciones += '<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">';
                htmlDivBotonOpciones += 'Opciones<span class="caret"></span></button>';
                htmlDivBotonOpciones += '<ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu1">';
                /* Boton de para visuablizar examenes */
                htmlDivBotonOpciones +='<li role="presentation"><a class="cursor" onclick="examenes(' + data.id_caso + ')">Exámenes</a></li>';
                /* Boton para sacar de la lista de estudios */
                htmlDivBotonOpciones +='<li role="presentation"><a class="cursor" onclick="sacarListaEEP(' + data.id_caso + ')">Sacar de lista</a></li>';
                htmlDivBotonOpciones +='</ul></div>';
            @endif            
            return htmlDivBotonOpciones;
        }
        function diagnostico(data, type, dataToSet) {
            return mayusculaPrimeraLetra(data.diagnostico.diagnostico);
        }

        function mayusculaPrimeraLetra(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function estadoExamen(data, type, dataToSet) {
            return data.examen_pendiente ? "Pendiente: Si" : "Pendiente: No";
        }

        function nombrePacienteCompleto(data, type, dataToSet) {
            return data.nombreCompleto.toLowerCase();
        }

        $(function () {
            tablaListaEEP = $('#tablaDocDer').dataTable({
                "aaSorting": [[0, "desc"]],
                ajax:  '{{ URL::to('/urgencia/obtenerListaEstudios') }}',
                columnDefs: [
                    {
                        targets: ["rut"],
                        className: ['dt-body-right dt-body-rut']
                    },
                    /* {
                        targets: ["dias-espera"],
                        className: 'dt-body-center'
                    }, */
                    {
                        targets: ["nombre-completo"],
                        className: 'dt-body-nombre-completo'
                    }

                ],
                "columns": [
                    { "data": botonOpciones },
                    { "data": rutConDV },
                    { "data": nombrePacienteCompleto },
                    { "data": diagnostico },
                    { "data": "fecha_caso" },
                    /* { "data": "servicio" }, */
                    { "data": "areaYservicio" },
                    { "data": "mayor_tiempo" },
                    /* { "data": "dias_espera" }, */
                    { "data": "ultimo" },
                    { "data": "cant_pendiente" }

                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    text: 'Exportar',
                    className: 'btn btn-default',
                    customize: function (xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '67'); //color verde, letra blanca, centrado
                        $('row', sheet).attr('ht', 15);
                        $('row:first', sheet).attr('ht', 50); //ancho columna
                        $('row:eq(1) c', sheet).attr('s', '67'); //color verde, letra blanca, centrado
                    }
                }],

                "iDisplayLength": 15,
                "bJQueryUI": true,
                "oLanguage": { "sUrl": "{{URL::to('/')}}/js/spanish.txt" },
                "fnRowCallback": function (nRow) {
                    const texto = $('td', nRow)[7].outerText;
                    const categorizacion = $('td', nRow)[8].outerText;
                    if (texto >= 8 && categorizacion === "") {
                        $('td', nRow).css('color', '#d14d33');
                        $('td', nRow).css('font-weight', 'bold');
                    }
                },
                initComplete: function(settings){
					var api = new $.fn.dataTable.Api( settings );
					var usuario = "{{Auth::user()->tipo}}";
					if(usuario == "cdt"){
						api.columns(0).visible(false);
					}
				}
            });
        });
    </script>

@stop

@section("estilo-tabla")
{{ HTML::style('css/sigicam/tablas.css') }}
@stop


@section("section")
    <div class="row">
        <div class="col-md-12">
            <legend>Lista Exámenes / Estudios / Procedimientos pendientes</legend>
            <div class="table-responsive">
                <table id="tablaDocDer" class="table table-hover tabla-sigicam">
                    <thead>
                    <tr>
                        <th>Opciones</th>
                        <th class='rut'>Rut</th>
                        <th class="nombre-completo">Nombre Completo</th>
                        <th>Diagnóstico</th>
                        <th>Fecha Ingreso Paciente</th>
                        <th>Área Funcional y Servicio</th>
                        <th>Pendiente con mayor tiempo</th>
                        {{-- <th class='dias-espera'>Días de Espera</th> --}}
                        <th>Último tipo de exámen pendiente</th>
                        <th>Examenes pendientes</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <div id="modalVerExamenes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Historial de exámenes</h4>
                    <div class="nombreModal"></div>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



@stop

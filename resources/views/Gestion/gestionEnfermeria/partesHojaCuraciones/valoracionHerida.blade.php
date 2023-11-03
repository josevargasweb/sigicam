{{-- <legend class="text-center" id="legendCuracion"><u>Hoja de Curación</u></legend> --}}

{{-- {{ HTML::link("gestionEnfermeria/$caso/historialHojacuracion", ' Ver Historial', ['class' => 'btn btn-default' , "id" => "volver"]) }} --}}
{{-- <br> --}}
{{-- <!-- <a href="{{URL::to("gestionEnfermeria/get/indexHojaCuracion")}}" class="btn btn-default">Ver historial</a> --> --}}
<script>

    function funcionesTablaHeridas() {
        var ttotal = 0;
        var taspecto = 0;
        var tmayorextension = 0;
        var tprofundidad = 0;
        var tcalidad = 0;
        var tcantidad = 0;
        var tesfacelado = 0;
        var tgranulatorio = 0;
        var tedema = 0;
        var tdolor = 0;
        var tpiel = 0;

        $(".calculartValoracion").change(function(){
            var idFila=$(this).data("id");
            vaspecto = parseInt($("#taspecto-"+idFila).val());
            vmayorextension = parseInt($("#tmayorextension-"+idFila).val());
            vprofundidad = parseInt($("#tprofundidad-"+idFila).val());
            vcalidad = parseInt($("#tcalidad-"+idFila).val());
            vcantidad = parseInt($("#tcantidad-"+idFila).val());
            vesfacelado = parseInt($("#tesfacelado-"+idFila).val());
            vgranulatorio = parseInt($("#tgranulatorio-"+idFila).val());
            vedema = parseInt($("#tedema-"+idFila).val());
            vdolor = parseInt($("#tdolor-"+idFila).val());
            vpiel = parseInt($("#tpiel-"+idFila).val());
            vtotal = vaspecto+vmayorextension+vprofundidad+vcalidad+vcantidad+vesfacelado+vgranulatorio+vedema+vdolor+vpiel;
            if(vtotal <= 15 ){vtotal = vtotal+" (Grado 1)";};
            if(vtotal > 15 && vtotal <= 21 ){vtotal = vtotal+" (Grado 2)";};
            if(vtotal > 21 && vtotal <= 27 ){vtotal = vtotal+" (Grado 3)";};
            if(vtotal > 27 && vtotal <= 40 ){vtotal = vtotal+" (Grado 4)";};

            $("#totalValoracion"+idFila).val((vtotal));
        });

        $('.dPValoracion').datetimepicker({
            format: 'LT'
        });

        $('.dPtPcuracion').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    }

    function generarTablaValoracionHerida(){
        tablavaloracionherida = $("#tablavaloracionheridas").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerValoracionHeridas/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": funcionesTablaHeridas
        });
    }

    function modificarCuracion(idSolicitud,idFila) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de modificar la información?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarCuracion",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            thorariov: $("#thorariov-"+idFila).val(),
                            taspecto: $("#taspecto-"+idFila).val(),
                            tmayorextension: $("#tmayorextension-"+idFila).val(),
                            tprofundidad: $("#tprofundidad-"+idFila).val(),
                            tcalidad: $("#tcalidad-"+idFila).val(),
                            tcantidad: $("#tcantidad-"+idFila).val(),
                            tesfacelado: $("#tesfacelado-"+idFila).val(),
                            tgranulatorio: $("#tgranulatorio-"+idFila).val(),
                            tedema: $("#tedema-"+idFila).val(),
                            tdolor: $("#tdolor-"+idFila).val(),
                            tpiel: $("#tpiel-"+idFila).val(),
                            tobservaciones: $("#tobservaciones-"+idFila).val(),
                            tproximacuracion: $("#tproximacuracion-"+idFila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
                            }

                            if (data.error) {
                                swalError.fire({
                                title: 'Error',
                                text:data.error
                                });
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
                }
            }
        });
    }

    function eliminarCuracion(idSolicitud) {
        bootbox.confirm({
            message: "<h4>¿Está seguro de eliminar este registro?</h4>",
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarCuracion",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
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
                                tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
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
                    tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
                }
            }
        }); 
    }

    function cargarVistaValoracionHerida(){
        if(typeof tablavaloracionherida !== 'undefined') {
            tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
        }else{
            generarTablaValoracionHerida();
        }
    }

    $( document ).ready(function() {
        $("#hojaDeCuracion").click(function(){
            var tabsHojaCuraciones = $("#tabsHojaCuraciones").tabs().find(".active");
            tabHC = tabsHojaCuraciones[0].id;

            if(tabHC == "1ch"){
                console.log("tabHC valoracion herida: ", tabHC);
                cargarVistaValoracionHerida();
            }
            
        });

        $("#1hc").click(function(){
            cargarVistaValoracionHerida();
        });

        $("#ingresoCuracionform").bootstrapValidator({
            excluded: ':disabled',
            fields: {
            }
        }).on('status.field.bv', function(e, data) {
            // data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnGuardarHCuracion").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({
                    message: "<h4>¿Está seguro de ingresar la información?</h4>",
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
                            url: "{{URL::to('/gestionEnfermeria')}}/ingresoHojaCuracion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnGuardarHCuracion").prop("disabled", false);
                                $("#horariov").val("").trigger("change");
                                $("#totalCuracion").val("").trigger("change");
                                $('#ingresoCuracionform').bootstrapValidator('resetForm', true);

                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    });
                                    /* aactualizar tabla con pendientes */
                                    tablavaloracionherida.api().ajax.reload(funcionesTablaHeridas, false);
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
                                $("#btnGuardarHCuracion").prop("disabled", false);
                                console.log(error);
                            }
                        });
                    }
                }
            });  
            $("#btnGuardarHCuracion").prop("disabled", false);

        });

        $(".selectCuraciones").change( function(){
            aspecto = $("#aspecto").val();
            mayorExtension = $("#mayorExtension").val();
            profundidad = $("#profundidad").val();
            cantidad = $("#cantidad").val();
            calidad = $("#calidad").val();
            esfacelado = $("#esfacelado").val();
            granulatorio = $("#granulatorio").val();
            edema = $("#edema").val();
            dolor = $("#dolor").val();
            pielC = $("#pielC").val();

            sumaCuraciones = Number(aspecto) + Number(mayorExtension) + Number(profundidad) + Number(cantidad) + Number(calidad) + Number(esfacelado) + Number(granulatorio) + Number(edema) + Number(dolor) + Number(pielC);

            if(sumaCuraciones <= 15 ){sumaCuraciones = sumaCuraciones+" (Grado 1)";}else{sumaCuraciones = sumaCuraciones;};
            if(sumaCuraciones > 15 && sumaCuraciones <= 21 ){sumaCuraciones = sumaCuraciones+" (Grado 2)";};
            if(sumaCuraciones > 21 && sumaCuraciones <= 27 ){sumaCuraciones = sumaCuraciones+" (Grado 3)";};
            if(sumaCuraciones > 27 && sumaCuraciones <= 40 ){sumaCuraciones = sumaCuraciones+" (Grado 4)";};

            $("#totalCuracion").val(sumaCuraciones);
        });
        $('.dPVHerida').datetimepicker({
            format: 'LT'
        });



    });
</script>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'ingresoCuracionform')) }}

{{ Form::hidden('idCaso', $caso, array('class' => 'idCaso')) }}

<div class="formulario">
{{-- <input type="hidden" value="" name="id_formulario_hoja_curaciones" id="id_formulario_hoja_curaciones"> --}}
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>VALORACIÓN DE HERIDA</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <legend>Clasificación de Herida</legend>
                <div class="col-md-2">{{Form::label('','Grado 1 (10 a 15 puntos)')}}</div>
                <div class="col-md-2">{{Form::label('','Grado 2 (16 a 21 puntos)')}}</div>
                <div class="col-md-2">{{Form::label('','Grado 3 (22 a 27 puntos)')}}</div>
                <div class="col-md-2">{{Form::label('','Grado 4 (28 a 40 puntos)')}}</div>
            </div>
            <br><br>
            <div class="col-md-12">
                <br>
                <legend>Ingresar nueva valoración herida</legend>
                <div class="col-md-12">
                    <div class="col-md-2"> HORARIO </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('horariov', null, array( 'class' => 'dPVHerida form-control', 'id' => 'horariov', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-2">
                    {{Form::label('lbl_aspecto', "Aspecto", array( ))}}
                    <div class="form-group">
                        {{ Form::select('aspecto', array('1' => 'Eritematoso', '2' => 'Enrojecido','3' => 'Amarillo pálido','4' => 'Necrótico'), null, array('class' => 'form-control selectCuraciones', 'id' => 'aspecto', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_mayor_extension', "Mayor Extensión", array( ))}}
                    <div class="form-group">
                        {{ Form::select('mayorExtension', array('1' => '0 - 1 cm', '2' => '> 1 - 3 cm','3' => '> 3 - 6 cm','4' => '> 6 cm'), null, array('class' => 'form-control selectCuraciones', 'id' => 'mayorExtension', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_profundidad', "Profundidad", array( ))}}
                    <div class="form-group">
                        {{ Form::select('profundidad', array('1' => '0', '2' => '< 1 cm','3' => '1 - 3 cm','4' => '> 3 cm'), null, array('class' => 'form-control selectCuraciones', 'id' => 'profundidad', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_exudado_cantidad', "Exudado Cantidad", array( ))}}
                    <div class="form-group">
                        {{ Form::select('cantidad', array('1' => 'Ausente', '2' => 'Escaso','3' => 'Moderado','4' => 'Abundante'), null, array('class' => 'form-control selectCuraciones', 'id' => 'cantidad', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('lbl_exudado_calidad', "Exudado Calidad", array( ))}}
                    <div class="form-group">
                        {{ Form::select('calidad', array('1' => 'Sin exudado', '2' => 'Seroso','3' => 'Turbio','4' => 'Purulento'), null, array('class' => 'form-control selectCuraciones', 'id' => 'calidad', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_esfacelado_necrotico', "Tejido esfacelado o necrótico", array( ))}}
                    <div class="form-group">
                        {{ Form::select('esfacelado', array('1' => 'Ausente', '2' => '< 25%','3' => '25 - 50%','4' => '> 50%'), null, array('class' => 'form-control selectCuraciones', 'id' => 'esfacelado', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_granulatorio', "Tejido Granulatorio", array( ))}}
                    <div class="form-group">
                        {{ Form::select('granulatorio', array('1' => '100 - 75%', '2' => '< 75 - 50%','3' => '< 50 - 25%','4' => '< 25%'), null, array('class' => 'form-control selectCuraciones', 'id' => 'granulatorio', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_edema', "Edema", array( ))}}
                    <div class="form-group">
                        {{ Form::select('edema', array('1' => 'Ausente', '2' => '+','3' => '++','4' => '+++'), null, array('class' => 'form-control selectCuraciones', 'id' => 'edema', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    {{Form::label('lbl_dolor', "Dolor", array( ))}}
                    <div class="form-group">
                        {{ Form::select('dolor', array('1' => '0 - 1', '2' => '2 - 3','3' => '4 - 6','4' => '7 - 10'), null, array('class' => 'form-control selectCuraciones', 'id' => 'dolor', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_piel_circundante', "Piel Circundante", array( ))}}
                    <div class="form-group">
                        {{ Form::select('pielC', array('1' => 'Sana', '2' => 'Descamada','3' => 'Eritematosa','4' => 'Macerada'), null, array('class' => 'form-control selectCuraciones', 'id' => 'pielC', 'placeholder' => 'Seleccionar', 'required')) }}
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    {{Form::label('lbl_total', "Total", array( ))}}
                    <div class="form-group">
                        <input type="text" name="total" id="totalCuracion" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    <input  type="submit" name="" id="btnGuardarHCuracion" class="btn btn-primary" value="Guardar">
                    <br>
                </div>
            </div>
            {{ Form::close() }}
            <br><br>
            <div class="col-md-12">
                <br><br>
                <legend>Registros de valoración de herida</legend>
                <table id="tablavaloracionheridas" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 5%">HERIDAS VALORADAS</th>
                            <th style="width: 30%">ASPECTO / MAYOR EXTENSIÓN / PROFUNDIDAD / EXUDADO CANTIDAD / EXUDADO CALIDAD</th>
                            <th style="width: 30%">TEJIDO ESFACELADO O NECRÓTICO / TEJIDO GRANULATORIO / EDEMA / DOLOR /PIEL CIRCUNDANTE / TOTAL</th>
                            <th style="width: 5%">USUARIO</th>
                            <th style="width: 5%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

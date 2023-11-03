<script>
    /* eliminar la fila de datatable */
    function eliminarFila(idSolicitud) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar este examen?</h4>",				
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
                console.log('This was logged in the callback: ' + result);					
                if(result){					
                    $.ajax({
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarExamenImagen",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnSolicitarImagen").prop("disabled", false);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tablePendienteActual.api().ajax.reload(validarImagenes, false);
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
                            $("#btnSolicitarImagen").prop("disabled", false);
                            console.log(error);
                        }
                    });				
                }else{
                    tablePendienteActual.api().ajax.reload(validarImagenes, false);
                }				
            }
        }); 
    }

    /* Editar fila */
    function modificar(idSolicitud, idFila) {
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarExamenImagen",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idSolicitud,
                            estado: $("#estado"+idFila).val(),
                            tomados: $("#tomada"+idFila).val(),
                            solicitado: $("#solicitada"+idFila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            $("#btnSolicitarImagen").prop("disabled", false);
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                /* aactualizar tabla con pendientes */
                                tablePendienteActual.api().ajax.reload(validarImagenes, false);
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
                            $("#btnSolicitarImagen").prop("disabled", false);
                            console.log(error);
                        }
                    });				
                }else{
                    tablePendienteActual.api().ajax.reload(validarImagenes, false);
                }				
            }
        });  
        	
    }

    function validarImagenes() {
        /* añadir cualquier script que desees ejecutar dentro del datatable */
                
        $('.dPImagen').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        });

    }

    function generarTablaImagen() {
        tablePendienteActual = $("#pendientesSolcitados").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerExamenesPendientes/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": validarImagenes
        });
    }

    function cargarVistaExamenImagen(){
        if (typeof tablePendienteActual !== 'undefined') {
            tablePendienteActual.api().ajax.reload(validarImagenes, false);
        }else{
            generarTablaImagen();
        }
    }
    

    $(document).ready(function() {

        $("#hojaDeEnfermeria").click(function(){

            var tabsRegistroDiarioCuidados = $("#tabsRegistroDiarioCuidados").tabs().find(".active");
            tabRdc = tabsRegistroDiarioCuidados[0].id;

            if(tabRdc == "5b"){
                console.log("tabRdc examen imagen: ", tabRdc);
                cargarVistaExamenImagen();
            }

        });

        $( "#5ab" ).click(function() {
            cargarVistaExamenImagen();
        });

        $('.dPEImagen').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on('dp.change', function (e) { 
            $('#heexamenimagen').bootstrapValidator('revalidateField', $(this));
        });

        $(document).on("input","input[name='exam_img']", function(){
            var $exam = $(this).parents(".examenesImagenes").find("input[name='exam_item']");
            if($exam.val()){
                $(this).val("");
                $exam.val("");
                $(this).trigger('input');
                $('#heexamenimagen').bootstrapValidator('revalidateField', 'exam_img');
            }
        });

        var datos_examenes_imagenes = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('examenesImagenes'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '{{URL::to('/')}}/'+'%QUERY/consulta_examenes_imagenes',
                wildcard: '%QUERY',
                filter: function(response) {
                    return response;
                }
            },
            limit: 100
        });

        datos_examenes_imagenes.initialize();

        $('.examenesImagenes .typeahead').typeahead(null, {
            name: 'best-pictures',
            display: 'nombre',
            source: datos_examenes_imagenes.ttAdapter(),
            limit: 100,
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'No hay resultados',
                '</div>'
                ].join('\n'),
                suggestion: function(data){
                    // return  "<div class='col-sm-12'><span class='col-sm-12'><b>"+ data.nombre+"</b></span></div>"
                    return  "<div class='col-sm-12'><span class='col-sm-8 '><b>"+ data.nombre + "</b></span><span class='col-sm-4'><b>Código: "+data.codigo+"</b></span><span class='col-sm-8 '>Subunidad: "+ data.subunidad + "</span><span class='col-sm-4'>Modalidad: "+data.modalidad+"</span></div>"
                },
                header: "<div class='col-sm-12;'><span class='col-sm-12' style='color:#1E9966;'>Catalogo examen imagen</span></div><br>"
            }
        }).on('typeahead:selected', function(event, selection){
            $("[name='exam_item']").val(selection.id);
            $('#heexamenimagen').bootstrapValidator('revalidateField', 'exam_img');
        }).on('typeahead:close', function(ev, suggestion) {
            var $med=$(this).parents(".examenesImagenes").find("input[name='exam_item']");
            if(!$med.val()&&$(this).val()){
                $(this).val("");
                $med.val("");
                $(this).trigger('input');
            }
        });

        $("#btnSolicitarImagen").prop("disabled", true);

        $("#heexamenimagen").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                'exam_img': {
                    validators:{
                        callback: {
                            callback: function(value, validator, $field){
                                var cantidad = $("#exam_item").val();
                                if(cantidad <= 0){
                                    return {valid: false, message: "Debe ingresar un examen de imagen"};

                                }else{
                                    return true;
                                }
                            }
                        }
                    }
                },  
                exam_img4: {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una fecha de programada'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitarImagen").prop("disabled", true);
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
                            url: "{{URL::to('/gestionEnfermeria')}}/addExamenImagen",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btnSolicitarImagen").prop("disabled", false);
                                if (data.exito) {
                                    swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            $("#exam_img").val("");
                                            $("#exam_img3").val("");
                                            $("input[name='exam_img4']").val("").change();
                                        }, 2000)
                                    },
                                    });
                                 
                                    /* aactualizar tabla con pendientes */
                                    tablePendienteActual.api().ajax.reload(validarImagenes, false);
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
                                $("#btnSolicitarImagen").prop("disabled", false);
                                console.log(error);
                            }
                        });				
                    }				
                }
            });  
            $("#btnSolicitarImagen").prop("disabled", false);
        });

        /* $('.dpImagenTomados').datetimepicker({
            format: 'LT'
        }); */
    });
    

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    .tt-input{
	width:100%;
    }
    .tt-query {
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .tt-hint {
    color: #999
    }

    .tt-menu {    /* used to be tt-dropdown-menu in older versions */
    /*width: 430px;*/
    margin-top: 4px;
    /* padding: 4px 0;*/
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
            border-radius: 4px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
        overflow-y: scroll;
        max-height: 350px;
    }

    .tt-suggestion {
    /* padding: 3px 20px;*/
    line-height: 24px;
    }

    .tt-suggestion.tt-cursor,.tt-suggestion:hover {
    color: #fff;
    background-color: #1E9966;

    }

    .tt-suggestion p {
    margin: 0;
    }
    .twitter-typeahead{
        width:100%;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'heexamenimagen')) }}

{{ Form::hidden ('caso', $caso, array('class' => 'idCasoEnfermeria') )}}
{{-- {{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormImagen') )}} --}}
    
<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>EXAMENES IMAGENES</h4>
            </div>
    
            <div class="panel-body" id="examenesImg">

                <legend>Ingresar nuevos examenes</legend>
                <div class="col-md-12">
                    <div class="col-md-5"> SOLICITADOS</div>
                    <div class="col-md-2"> ESTADO</div>
                    <div class="col-md-2"> FECHA/HORA</div>
                </div>

                <br>
                    <div class="col-md-12">
                        <div class="form-group col-md-5"> 
                            <div class="examenesImagenes"> 
                                {{Form::text('exam_img', null, array('id' => 'exam_img', 'class' => 'form-control typeahead'))}}
                                {{Form::hidden('exam_item', null, array('id' => 'exam_item'))}}
                            </div> 
                        </div>
                        <div class="col-md-2"> <div class="form-group"> {{Form::select('exam_img3', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array('id' => 'exam_img3', 'class' => 'form-control')) }} </div> </div>
                        <div class="col-md-2"> <div class="form-group"> {{Form::text('exam_img4', null, array( 'class' => 'dPEImagen form-control'))}} </div> </div>   
                        <div class="col-md-1"> 
                            <button type="submit" class="btn btn-primary" id="btnSolicitarImagen">Guardar</button>
                        </div>   
                    </div>

                <br><br>

                <legend>Examenes pendientes y realizados hoy</legend>
                <table id="pendientesSolcitados" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SOLICITADOS</th>
                            <th>ESTADO</th>
                            <th>FECHA/HORA</th>
                            <th>USUARIO</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>
                
                <br><br>

                

                <div class="examenImagenExtra">

                </div>

                

                {{-- <div class="col">
                    <div class="col-md-6" align="left">
                        <button type="button" class="btn btn-success" id="btnAñadirExamImagen">+ Añadir</button>
                    </div>
                    <div class="col-md-6">
                        
                    </div>
                    
                </div>  --}}

            </div>
        </div>
    </div>
</div>

{{ Form::close() }} 

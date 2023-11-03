<script>

    function validarPertenencia(idfila){
        if($("#pertenencia"+idfila).val() == ""){
            $('#pertenencia'+idfila).focus($("#pertenencia"+idfila).css({
                'border': '1px solid #a94442'
            }));
            $("#errorPertenencia"+idfila).html('Debe ingresar la pertenencia');
            }else{
            $("#errorPertenencia"+idfila).html('');
            $('#pertenencia'+idfila).focus($("#pertenencia"+idfila).css({
                'border': '1px solid #ccc'
            }));
        }   
    }

    function validarFechaCreacion(idfila){
        $(".dPpertenenciaE").on("dp.change keyup", function(e) {
            if($('input[name="fecha_creacionE'+idfila+'"]').val() == ""){
                $('input[name="fecha_creacionE'+idfila+'"]' ).focus($('input[name="fecha_creacionE'+idfila+'"]').css({
                    'border': '1px solid #a94442'
                }));
                $("#errorFechaCreacion"+idfila).html('Debe ingresar una fecha');
            }else{
                $('input[name="fecha_creacionE'+idfila+'"]').focus($('input[name="fecha_creacionE'+idfila+'"]').css({
                    'border': '1px solid #ccc'
                }));
                $("#errorFechaCreacion"+idfila).html('');
            }
        });
    }
    function validarFechaRecepcion(idfila){
        $(".dPpertenenciaR").on("dp.change keyup", function(e) {
            if($('input[name="fecha_recepcionE'+idfila+'"]').val() == ""){
                $('input[name="fecha_recepcionE'+idfila+'"]' ).focus($('input[name="fecha_recepcionE'+idfila+'"]').css({
                    'border': '1px solid #a94442'
                }));
                $("#errorFechaRecepcion"+idfila).html('Debe ingresar una fecha');
            }else{
                $('input[name="fecha_recepcionE'+idfila+'"]').focus($('input[name="fecha_recepcionE'+idfila+'"]').css({
                    'border': '1px solid #ccc'
                }));
                $("#errorFechaRecepcion"+idfila).html('');
            }
        });
    }

    function validarResponsable(idfila){
        if($("#responsable"+idfila).val() == ""){
            $('#responsable'+idfila).focus($("#responsable"+idfila).css({
                'border': '1px solid #a94442'
            }));
            $("#errorResponsable"+idfila).html('Debe ingresar personal respondable');
        }else{
            $("#errorResponsable"+idfila).html('');
            $('#responsable'+idfila).focus($("#responsable"+idfila).css({
                'border': '1px solid #ccc'
            }));
        }   
    }

    function dtpEditar () {
        $('.dPpertenenciaE').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        });
        $('.dPpertenenciaR').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        });
    }
    function cargarPertenencias(){
        tablePertenencias = $("#tablaPertenencias").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerPertenencias/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
            "initComplete": dtpEditar
        });
    }

    var ocultarDetallePertencias=function(){
        var value=$("input[name='perte']:checked").val();
        if(value == "si"){
            $("#detallePertenencias").show("slow");
        }else{
            $("#detallePertenencias").hide("slow");
            $("#detallePertenencias").val('');
        } 
    }

    function modificarPertenencia(idpertenencia,idfila){
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
                        url: "{{URL::to('/gestionEnfermeria')}}/modificarPertenencia",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idpertenencia,
                            pertenencia: $("#pertenencia"+idfila).val(),
                            fecha_creacion: $("#fecha_creacion"+idfila).val(),
                            fecha_recepcion: $("#fecha_recepcion"+idfila).val(),
                            responsable: $("#responsable"+idfila).val(),
                            entrega: $("#entrega"+idfila).val(),
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tablePertenencias.api().ajax.reload(dtpEditar, false);
                            }

                            if (data.error) {
                                	swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    }).then(function(result) {
                                    if (result.isDenied) {
                                        tablePertenencias.api().ajax.reload(dtpEditar, false);
                                    }
                                    });
                               
                            }

                            //si faltan datos que rellenar
                            if(data.errores){
                                // console.log("errores");
                                // console.log(data.errores);
                                // var html = "";
                                // $.each(data.errores, function( index, value ) {
                                //     html += value+"."+"<br>";
                                // });

                                // bootbox.alert({
                                //     title: "Errores",
                                //     message:"<h4>"+html+"</h4>"});
                                //imprimira los errores
                                // imprimirErroresEditar(data.errores);
                                //abrira el modal donde se veran los errores
                                // $("#erroresModalPertenencias").modal("show");
                                let ul = '';
                                ul = "<ul style='text-align:left'>";
                                $.each( data.errores, function( key, value ) {
                                ul +="<li style='list-style:none'>"+value+"</li>";
                                });
                                ul += "</ul>";
                                swalError.fire({
                                title: 'Error',
                                html:ul
                                });
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });				
                }else{
                    tablePertenencias.api().ajax.reload(dtpEditar, false);
                }				
            }
        }); 
    }

    function eliminarPertenencia(idpertenencia) {
        bootbox.confirm({				
            message: "<h4>¿Está seguro de eliminar este procedimiento?</h4>",				
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
                        url: "{{URL::to('/gestionEnfermeria')}}/eliminarPertenencia",
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:  {
                            id: idpertenencia
                        },
                        dataType: "json",
                        type: "post",
                        success: function(data){
                            if (data.exito) {
                                swalExito.fire({
                                title: 'Exito!',
                                text: data.exito,
                                });
                                tablePertenencias.api().ajax.reload(dtpEditar, false);
                            }

                            if (data.error) {
                                	swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    }).then(function(result) {
                                    if (result.isDenied) {
                                        tablePertenencias.api().ajax.reload(dtpEditar, false);
                                    }
                                    });
                               
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });				
                }else{
                    tablePertenencias.api().ajax.reload(dtpEditar, false);
                }				
            }
        }); 
    }

    function imprimirErroresEditar (msg) {
            $(".imprimir-mensajes-pertenencias").find("ul").html('');
            $(".imprimir-mensajes-pertenencias").css('display','block');
            $.each( msg, function( key, value ) {
                $(".imprimir-mensajes-pertenencias").find("ul").append("<div style='display: flex'><i class='glyphicon glyphicon-remove' style='color: #a94442;'></i><div style='margin-left: 10px'><h4>"+value+"</h4></div></div>");
                // ('<label><i class="glyphicon glyphicon-remove"><h4>'+value+'</h4></i></label><br>');
           
            });
        }
    
    $(function (){

        $("#pertenencias").click(function(){
            if(typeof tablePertenencias !== 'undefined'){
                tablePertenencias.api().ajax.reload(dtpEditar,false);
            }else{
                cargarPertenencias();
            }
        });

        $("#EntregaPertenencia").bootstrapValidator({
            excluded: ':disabled',
                fields: {
                    pertenencia: {
                        validators: {
                            notEmpty: {
                                message: 'Debe ingresar la pertenencia'
                            }
                        }
                    },
                    'fecha_recepcion': {
                        validators: {
                            notEmpty: {
                                message: 'Debe ingresar una fecha'
                            }
                        }
                    },
                    'responsable': {
                        validators: {
                            notEmpty: {
                                message: 'Debe ingresar el nombre del responsable'
                            }
                        }
                    }
                }
            }).on('status.field.bv', function(e, data) {
            }).on("success.form.bv", function(evt, data){
                $("#btGuardarOtro").prop("disabled", true);
                evt.preventDefault(evt);
                var $form = $(evt.target);
                bootbox.confirm({				
                    message: "<h4>¿Está seguro de agregar esta información?</h4>",				
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
                                url: "{{URL::to('/gestionEnfermeria')}}/agregarPertenencia",
                                headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:  $form .serialize(),
                                dataType: "json",
                                type: "post",
                                success: function(data){
                                    $("#btnguardarPertenencia").prop("disabled", false);
                                
                                    if (data.exito) {

                                        swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        });
                                        // location.reload();
                                        tablePertenencias.api().ajax.reload(dtpEditar,false);
                                        $("#pertenencia").val('');
                                        $('#EntregaPertenencia').bootstrapValidator('revalidateField', 'pertenencia');
                                        $("input[name='fecha_recepcion']").val('');
                                        $('#EntregaPertenencia').bootstrapValidator('revalidateField', 'fecha_recepcion');
                                        $("#responsable").val('');
                                        $('#EntregaPertenencia').bootstrapValidator('revalidateField', 'responsable');
                                    }

                                    if (data.error) {
                                        swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                        });
                                       
                                        // location.reload();
                                        tablePertenencias.api().ajax.reload(dtpEditar,false);
                                    }
                                },
                                error: function(error){
                                    $("#btnguardarPertenencia").prop("disabled", false);
                                    console.log(error);
                                    // location.reload();
                                    tablePertenencias.api().ajax.reload(dtpEditar,false);
                                }
                            });				
                        }else{
                            $("#btnguardarPertenencia").prop("disabled", false);
                    }				
                }
            }); 
        });  

        $('.dPpertenencia').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#EntregaPertenencia').bootstrapValidator('revalidateField', $(this));
        });
        
    });
</script>
<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
</style>
<br>

<div class="formulario">
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Pertenencias</h4>
        </div>
        <div class="panel-body">
            {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'EntregaPertenencia')) }}

            {{ Form::hidden ('caso', $caso, array('class' => 'idCasoPertenencia') )}}
            <div class="col-md-12">
                <div class="col-md-4" id="detallePertenencias">
                    <div class="form-group"> 
                        {{Form::label('', "Detalle Pertenencias")}}
                        {{Form::text('pertenencia', null, array('id' => 'pertenencia', 'class' => 'form-control'))}} 
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('','Fecha Recepción')}}
                        {{Form::text('fecha_recepcion', null, array('id' => 'fecha_recepcion', 'class' => 'form-control dPpertenencia'))}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{Form::label('','Nombre del responsable')}}
                        {{Form::text('responsable', null, array('id' => 'responsable', 'class' => 'form-control'))}}
                    </div>
                </div>
                <div class="col-md-2">
                    {{Form::label('','&nbsp;')}} <br>
                    <button type="submit" class="btn btn-primary" id="btnguardarPertenencia">Guardar</button>
                </div>
                <br>
            </div>
            {{ Form::close() }}
            <br><br>
            <legend>Listado de pertenencias</legend>
            <table id="tablaPertenencias" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 18%;">Pertenencia</th>
                        <th style="width: 15%;">Fecha de recepción</th>
                        <th style="width: 18%;">Funcionario responsable</th>
                        <th style="width: 15%;">Fecha de entrega</th>
                        <th style="width: 20%;">Persona a quien entrega</th>
                        <th style="width: 20%;">Opciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="erroresModalPertenencias" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Falta Información</h4>
        </div>
        <div class="modal-body">
         <div class="alert alert-danger imprimir-mensajes-pertenencias" style="display:none">
            <ul></ul>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

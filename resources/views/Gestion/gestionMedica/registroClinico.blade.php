<script>
    function eliminarFilaRegistroClinico(position) {
        var myobj = document.getElementById("moduloRegistroClinico"+position);
        myobj.remove();               
    }

    var counterRegistroClinico  = 1;

    function agregarRegistroClinico(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloRegistroClinico");
        var cloneDiv = originalDiv.clone();    
        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloRegistroClinico'+counterRegistroClinico);

        cloneDiv.find('.btnAgregarRegistroClinico').remove();

        $("[name='registro_clinico[]']",cloneDiv).attr({'data-id':counterRegistroClinico,'id':'registro_clinico'+counterRegistroClinico});          
        $("[name='registro_clinico[]']",cloneDiv).val('');          

        $("[name='id_registro_clinico[]']",cloneDiv).attr({'data-id':counterRegistroClinico,'id':'id_registro_clinico'+counterRegistroClinico});    
        $("[name='id_registro_clinico[]']",cloneDiv).val(''); 
    
        html ='<div class="col-md-2 text-center"><button class="btn btn-danger" onclick="eliminarFilaRegistroClinico('+counterRegistroClinico+')">-</button></div>';       
        
        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloRegistroClinicocopia").append(cloneDiv);
        cloneDiv.append(html);
        
        $('#registroClinicoForm').bootstrapValidator('addField', cloneDiv.find("[name='registro_clinico[]']"));

        //incrementa el contador
        counterRegistroClinico++;      
    };

    function generarTablaRegistroClinico() {
        tabledRegistroClinico = $("#tableRegistroClinico").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionMedica')}}/buscarHistorialRegistroClinico/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    function editarRegistroClinico(idFormulario){
        $.ajax({
            url: "{{ URL::to('/gestionMedica')}}/editarRegistroClinico/"+idFormulario,
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.datos){
                    console.log(data);
                    $("#registroClinicomodal").modal();
                    $("#id_formulario_registro_clinico").val(data.datos.id);
                    $("#edit_registro_clinico").val(data.datos.registro);
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    }


    function eliminarRegistroClinico(idFormulario){
        swalPregunta.fire({
                title: '¿Esta seguro que desea eliminar este registro?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::to('/gestionMedica')}}/eliminarRegistroClinico",
                        headers:{
                            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: {"id_formulario":idFormulario},
                        success: function(data){
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                });
                            }
                            if(data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });	
                            }
                            tabledRegistroClinico.api().ajax.reload();
                            $("#moduloRegistroClinicocopia").empty();
                            counterRegistroClinico  = 1;
                            $("#registro_clinico0").val("");
                            $("#registroClinicoForm").bootstrapValidator("revalidateField", "registro_clinico[]");
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }
            });
    }
   
    $(document).ready( function() {

        $('#registroClinicomodal').on('hide.bs.modal', function () {
            $("#id_formulario_registro_clinico").val("");
            $("#edit_registro_clinico").val("");
        });
        
        $("#registroMedico").click(function(){
            if (typeof tabledRegistroClinico == 'undefined') {
                generarTablaRegistroClinico();
            }
        });


        $("#registroClinicoForm").bootstrapValidator({
            excluded:[':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'registro_clinico[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },

            }
        }).on('status.field.bv', function(e, data){
            $("#registroClinicoForm input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#registroClinicoForm input[type='submit']").prop("disabled", false);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            swalPregunta.fire({
                title: '¿Desea Guardar este formulario?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::to('/gestionMedica')}}/agregarRegistroClinico",
                        headers:{
                            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        success: function(data){
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                });
                            }
                            if(data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });	
                            }
                            tabledRegistroClinico.api().ajax.reload();
                            $("#moduloRegistroClinicocopia").empty();
                            counterRegistroClinico  = 1;
                            $("#registro_clinico0").val("");
                            $("#registroClinicoForm").bootstrapValidator("revalidateField", "registro_clinico[]");
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });

                }
            });
        });


        $("#editregistroClinicoForm").bootstrapValidator({
            excluded:[':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'registro_clinico[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },

            }
        }).on('status.field.bv', function(e, data){
            $("#editregistroClinicoForm input[type='submit']").prop("disabled", false);
        }).on("success.form.bv", function(evt){
            $("#editregistroClinicoForm input[type='submit']").prop("disabled", false);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            swalPregunta.fire({
                title: '¿Desea Guardar este formulario?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::to('/gestionMedica')}}/agregarRegistroClinico",
                        headers:{
                            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        success: function(data){
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                });
                            }
                            if(data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });	
                            }
                            tabledRegistroClinico.api().ajax.reload();
                            $('#registroClinicomodal').modal('hide');
                            $('#id_formulario_registro_clinico').val('');
                            $('#edit_registro_clinico').val('');
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });

                }
            });
       
        });

 

        


    });

  

</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    #rn_table tbody{
        counter-reset: Serial;           
    }

    table #rn_table{
        border-collapse: separate;
    }

    #rn_table tr td:first-child:before{
    counter-increment: Serial;      
    content: counter(Serial); 
    }
</style>

<br><br>
{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'registroClinicoForm')) }}
{{ Form::hidden('idCasoRegistroClinico', $caso, array('id' => 'idCasoRegistroClinico')) }}
    <div class="formulario">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <legend>Ingresar Evolución Medica</legend>
                        <div class="col-md-12">
                            <div class="col-md-5"> Evolución Medica</div>
                        </div>
                            <br>
                        <div class="col-md-12">
                            <div class="col-md-7 pl-0 pr-0" id="moduloRegistroClinico">
                                <div class="form-group col-md-9"> 
                                    <div class="examenesImagenes"> 
                                        {{Form::text('registro_clinico[]', null, array('id' => 'registro_clinico0', 'class' => 'form-control'))}}
                                    </div> 
                                </div> 
                                {{-- <div class="col-md-3 text-right btnAgregarRegistroClinico">
                                    <button type="button" class="btn btn-primary agregarRegistroClinico" onclick="agregarRegistroClinico()">+</button>
                                </div> --}}
                            </div>
                            <div class="col-md-1 pl-0"> 
                                <button type="submit" class="btn btn-primary" id="btnGuardarRegistroClinico">Guardar</button>
                            </div>
                            <div class="col-md-12 moduloRegistroClinicocopia pl-0 pr-0" id="moduloRegistroClinicocopia"></div>   
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <legend>Listado de evolución medica</legend>
                <table id="tableRegistroClinico" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 25%">USUARIO</th>
                            <th style="width: 50%">EVOLUCIÓN MEDICA</th>
                            <th style="width: 25%">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
            
                    </tbody>
                </table>   
            </div>
        </div>
    </div>
{{ Form::close() }}


{{ Form::open(array('method' => 'post','class' => 'form-horizontal',  'id' => 'editregistroClinicoForm', 'autocomplete' => 'off')) }}
{{ Form::hidden('idCasoRegistroClinico', $caso, array('id' => 'idCasoRegistroClinico')) }}
{{ Form::hidden('id_formulario_registro_clinico', '', array('id' => 'id_formulario_registro_clinico')) }}
<div class="modal fade" id="registroClinicomodal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-sm-10">
                            <label for="diagnosticor" class="control-label" title="diagnostico">Evolución Medica: </label>
                            <div class="col-md-12 form-group ">
                                {{Form::text('registro_clinico[]', null, array('id' => 'edit_registro_clinico', 'class' => 'form-control'))}}
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="btnGuardarRegistroClinico">Guardar</button>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</div>
{{Form::close()}}
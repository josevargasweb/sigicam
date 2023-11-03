<script>
    

    $(function () {
        $(document).on("input","input[name='diagnosticos[]']",function(){
            var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
            //a.prop("disabled", true);
            var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
            if($cie10.val()){
                $(this).val("");
                $cie10.val("");
                $(this).trigger('input');
            }
        });

        var datos_cie10 = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '{{URL::to('/')}}/'+'%QUERY/categorias_cie10',
                wildcard: '%QUERY',
                filter: function(response) {
                    return response;
                }
            },
            limit: 1000
        });
        datos_cie10.initialize();

        $('.diagnostico_cie101 .typeahead').typeahead(null, {
            name: 'best-pictures',
            display: 'nombre_cie10',
            source: datos_cie10.ttAdapter(),
            limit: 1000,
            templates: {
                empty: [
                '<div class="empty-message">',
                    'No hay resultados',
                '</div>'
                ].join('\n'),
                suggestion: function(data){
                    if(data.nombre_categoria == null){
                        return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>Sin categoría</b></span><span class='col-sm-4'><b>--</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
                    }else{
                        return  "<div class='col-sm-12' ><span class='col-sm-8 '><b>"+ data.nombre_categoria + "</b></span><span class='col-sm-4'><b>"+data.id_categoria+"</b></span><span class='col-sm-8 '>"+ data.nombre_cie10 + "</span><span class='col-sm-4'>"+data.id_cie10+"</span></div>"
                    }	
                },
                header: "<div class='col-sm-12'><span class='col-sm-8' style='color:#1E9966;'>Nombre categoría</span><span class='col-sm-4' style='color:#1E9966;'>id categoría</span><span class='col-sm-8' style='color:#1E9966;'>Nombre cie10</span><span class='col-sm-4' style='color:#1E9966;'>id cie10</span></div><br>"
            }
        }).on('typeahead:selected', function(event, selection){
                console.log("JJJJJJJJJJ");
                $("#texto_cie10").val(selection.nombre_cie10);
                $("[name='hidden_diagnosticos[]']").val(selection.id_cie10);
                $("#cie10-principal").prop("disabled", false);
            }).on('typeahead:close', function(ev, suggestion) {//Mauricio
        console.log('Close typeahead: ' + suggestion);
            var $cie10=$(this).parents(".diagnostico_cie101").find("input[name='hidden_diagnosticos[]']");
            console.log("padre:",$(this).parents(".diagnostico_cie101"));
            console.log("cie10:",$cie10.val(),!$cie10.val());
            console.log("this:",$(this).val(),!!$(this).val());
            if(!$cie10.val()&&$(this).val())
            {
                $(this).val("");
                $cie10.val("");
                $(this).trigger('input');
                console.log("RRRRRRRR");
                //$("#cie10-principal").prop("disabled", false);
            }
        });


        //formulario con validacion para agregar diagnostico
        $("#formIngresarDiagnostico").bootstrapValidator({
            excluded: [ ':hidden', ':not(:visible)'],
            fields: {
                "diagnosticos[]": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                "nuevo-diagnostico[]": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                "motivo": {
                    validators: {
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt){
            console.log("que wa paso?");
            evt.preventDefault(evt);

			swalPregunta.fire({
                title: '¿Está seguro de cambiar el comentario del diagnostico?',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $("#btnModificarDiagnostico").attr('disabled', 'disabled');
                    var $form = $(evt.target);
                    // swalCargando.fire({});
                    $.ajax({
                        url: "{{URL::to('/gestionMedica')}}/ingresarDiagnostico",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        dataType: "json",
                        data: $form .serialize(),
                        async: false,
                        success: function(data){
                            if(data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            $("#modalEditarComentario").modal('hide');
											tabsDiagnosticossMedicos.api().ajax.reload();
                                        }, 2000)
                                    },
                                });
                                $("#div-motivo").hide();
                            }
                            if(data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                });
                                console.log(data.error);
                            }

                            if(data.info){
                                swalInfo.fire({
                                    title: 'Información',
                                    text: data.info,
                                    allowOutsideClick: false
                                });
                            }

                            if(data.motivo){
                                swalInfo.fire({
                                    title: 'Información',
                                    text: data.motivo,
                                    allowOutsideClick: false
                                });
                                $("#div-motivo").show();
                            }

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
    {
       margin-left: -40px !important;
   }
</style>

<br>
<div class="panel panel-default">
   <div class="panel-heading">
       <h4>Diagnósticos</h4>
   </div>
   <div class="panel-body">
        <legend>Agregar nuevo</legend>
        {{ Form::open( array('method' => 'post', 'class' => 'form-inline', 'role' => 'form', 'id' => 'formIngresarDiagnostico') ) }}
            {{ Form::hidden('caso', "$caso", array('class' => 'detalle-diagnostico')) }}
            {{ Form::hidden('ubicacion', null, array('class' => 'ubicacion')) }}
            
            <div class="row" style="padding-bottom:25px">
                <div class="form-group col-md-12">
                    <div class="col-sm-2">
                        <label for="files[]">Diagnóstico CIE10</label>
                    </div>
                    <div class="col-sm-9 diagnostico_cie101">
                        <input type="text" name="diagnosticos[]" class='form-control typeahead' style='width:350px'/>
                        <input type="hidden" name="hidden_diagnosticos[]">
                    </div>
                    {{-- <div class="col-sm-1" style="right: 30px;">
                        <button disabled id="cie10-principal" class="btn btn-default" type="button" onclick="agregar(this);"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    </div> --}}
                </div>
            </div>
                    
            <div class="row">
                <div class="form-group col-md-12">
                    <div class="col-sm-2">
                        <label for="">Comentario diagnóstico</label>
                    </div>
                    <div class="col-sm-9 ">
                        <input type="text" name="nuevo-diagnostico[]" class='form-control' style='width:350px' />
                    </div>
                </div>
            </div>
            <br>
            <div class="row" id="div-motivo" hidden>
                <div class="form-group col-md-12">
                    <div class="col-sm-2">
                        <label for="">Motivo</label>
                    </div>
                    <div class="col-sm-9 ">
                        {{ Form::textarea('motivo', null, array('id' => 'motivo','class' => 'form-control', 'style'=>'height:100px;')) }}
                    </div>
                </div>
            </div>

            {{ Form::submit('Agregar diagnóstico', array('id' => 'agregarDiagnostico', 'class' => 'btn btn-primary', 'style'=>'margin-left: 12px;')) }}
        {{ Form::close() }}
       
   </div>
</div>

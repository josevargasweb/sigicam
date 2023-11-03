<script>

    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    function load_eliminar_indicacion(){

        var horarios = "{{$indciacionInfo[0]->horario}}";
        var arr_horarios = horarios.split(',');
        var color = '';
        if({{$indciacionInfo[0]->responsable}} == 1){
            color = "colorEnfermera";
        }else if({{$indciacionInfo[0]->responsable}} == 2){
            color= "colorTens";
        }
        var datoHora = '';
        arr_horarios.forEach(function(hora) {
            datoHora += '<div  class="'+color+'"><div class=""><button class="btn btn-danger botonCerrar" type="button"  onclick="eliminarHoraIndicacion('+hora+','+{{$indciacionInfo[0]->id}}+')">X</button><div class="valorInterno">'+hora+'</div></div></div>';
            });
            

        var tipo = "{{$indciacionInfo[0]->tipo}}";
        $("#tipo_eliminar").text(tipo+":");
     
        
            var eliminar_descripcion = '';
        if(tipo === "Medicamento"){
            eliminar_descripcion = "{{$indciacionInfo[0]->medicamento}}";
        }else if (tipo === "Indicación"){
            eliminar_descripcion = "{{$indciacionInfo[0]->indicacion}}";            
        }else if(tipo === "Interconsulta"){
            eliminar_descripcion = "{{$indciacionInfo[0]->tipo_interconsulta}}";
        }
        $("#eliminar_descripcion").text(eliminar_descripcion);
        $(".horas-eliminar-indicacion-container").html(datoHora);
    
    }


    $(document).ready(function() {

        $("#form_eliminar_indicacion").bootstrapValidator({
            excluded: [':disabled'],
            fields: {    
            }
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault();
            var $form = $(evt.target);
            bootbox.confirm({
                    message: "<h4>¿Está seguro de actualizar la información?</h4>",
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
                        $("#btn_actualizar_eliminar_indicacion").prop("disabled", true);
                        $.ajax({
                            url: "{{URL::to('/gestionEnfermeria')}}/modificarPCIndicacion",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(res){
                                $("#modalModificarIndicacion").modal('hide');

                                swalExito.fire({
                                title: 'Exito!',
                                text: res.exito,
                                });
                                    
                                //actualizar tabla
                                tablePCIndicaciones.api().ajax.reload();

                            },
                            error: function(xhr, status, error){
                                var error_json = JSON.parse(xhr.responseText);
                                swalError.fire({
                                title: 'Error',
                                text:error_json.error
                                });
                            },
                            complete: function (){
                                $("#btn_guardar_agregar_indicacion").prop("disabled", false);
                            }
                        });				
                    }				
                }
            });  
        });

        load_eliminar_indicacion();

    });
   
</script>

<fieldset>
    <div class="eliminar-indicacion-container">
        {{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'form_eliminar_indicacion')) }}

        {{ Form::hidden ('id_indicacion_actualizar', $indciacionInfo[0]->id, array('class' => '') )}}


        <div class="eliminar-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-2">
                    <div class="form-group">
                     <p id="tipo_eliminar"></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                     <p id="eliminar_descripcion"></p>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="horas-eliminar-indicacion-container">
          
        </div>
            
        <div class="btn-actualizar-eliminar-indicacion-container">
            <div class="col-md-12">
                <div class="col-md-offset-10">  
                    <button type="submit" class="btn btn-danger" id="btn_actualizar_eliminar_indicacion" style="margin-top:15%;">Eliminar Todo</button>
                </div>
            </div>
        </div>
        

        {{ Form::close() }} 

    </div>
</fieldset>


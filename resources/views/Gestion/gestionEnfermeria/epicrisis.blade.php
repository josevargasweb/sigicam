<script>


    function mostrarEpicrisis(){
        var caso = {{$caso}};
        $.ajax({ 
                url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosEpicrisis",
                data: { 
                    caso : caso
                }, 
                headers: {					         
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                },
                type: "post", 
                dataType: "json", 
                success: function (data) {
                    if(data){
                        var array = [];
                        $("#btnReportePDF").show();
                        if (data.epicrisis != null) {
                            var epicrisisid = (data.epicrisis.id) ? data.epicrisis.id : "";
                            $("#id_formulario_epicrisis").val(epicrisisid);
                            var epicrisisde = (data.epicrisis.destino_egreso) ? data.epicrisis.destino_egreso : "";
                            $("#destino_egreso").val(epicrisisde);
                            var epicrisisiq = (data.epicrisis.intervencion_quirurgica) ? data.epicrisis.intervencion_quirurgica : "";
                            $("#intervencion_quirurgica").val(epicrisisiq);
                            if(data.epicrisis.fecha_intervencion){
                                fecha_intervencion = moment(data.epicrisis.fecha_intervencion).format("DD-MM-YYYY HH:mm");
                                $("#fecha_intervencion").val(fecha_intervencion);
                            }

                            array = data.epicrisis.diagnosticos.split(',');
                        }
                        
                        if(data.infoResponsable != null){
                            var racompanante = (data.infoResponsable.acompanante) ? data.infoResponsable.acompanante : "--";
                            $("#tacompanante").text(racompanante);
                            var rvinculo = (data.infoResponsable.vinculo_acompanante) ? data.infoResponsable.vinculo_acompanante : "--";
                            $("#tvinculo_acompanante").text(rvinculo);
                            var rtelefono = (data.infoResponsable.telefono_acompanante) ? data.infoResponsable.telefono_acompanante : "--";
                            $("#ttelefono_acompanante").text(rtelefono);
                        }

                        var cantidad_diagnosticos = $("[name='diagnosticos[]']").length;
                        var valor;
                        var existe;
                        for (let index = 0; index < cantidad_diagnosticos; index++) {
                            valor = $("[name='diagnosticos[]']").eq(index).val();
                            existe = jQuery.inArray(valor,array);
                            if(existe != -1){
                                $("[name='diagnosticos[]']").eq(index).prop("checked", true);
                            }
                        }
                        
                    }else{
                        $("#btnReportePDF").hide();
                    }
                }, 
                error: function (error) {
                    console.log(error);
                    $("#btnReportePDF").hide();
                } 
            });
    }




    $(document).ready(function() {

        $("#epicrisis").click(function(){
            mostrarEpicrisis();
        });

        $('.fechaIntervencion').datetimepicker({
            format: "DD-MM-YYYY HH:mm",
            locale: 'es'
        }).on('dp.change', function (e) { 
            $('#epicrisisForm').bootstrapValidator('revalidateField', $(this));
        });
        
        $("#epicrisisForm").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                "diagnosticos[]": {
                    validators:{
                        notEmpty: {
                            message: 'Debe seleccionar al menos un diagnóstico'
                        }
                    }
                }
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(false);
        }).on("success.form.bv", function(evt, data){
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({				
                message: "<h4>¿Está seguro de generar el reporte de epicrisis?</h4>",				
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
                            url: "{{URL::to('/gestionEnfermeria')}}/epicrisis",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                if (data.exito) {
                                    swalExito.fire({
                                        title: 'Exito!',
                                        text: data.exito,
                                        didOpen: function() {
                                            setTimeout(function() {
                                                $("#btnGuardarEpicrisis").prop("disabled", false);
                                                mostrarEpicrisis();
                                            }, 2000)
                                        },
                                    });
                                  
                                    // tableProtecciones.api().ajax.reload();
                                }

                                if (data.error) {
                                    swalError.fire({
                                        title: 'Error',
                                        text:data.error
                                    }).then(function(result) {
                                        if (result.isDenied) {
                                            $("#btnGuardarEpicrisis").prop("disabled", false);
                                        }
                                    })
                                }
                            },
                            error: function(error){
                                console.log(error);
                                $("#btnGuardarEpicrisis").prop("disabled", false);
                            }
                        });
                    }			
                }
            });
        });
    });
</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    p {
        font-size: 13px;
    }
    .p-0{
        padding:0;
    }
</style>
<br>  
<div class="row">
    <div class="col-md-6">
        <b>AREA FUNCIONAL:</b>  {{$area}}
    </div>
    <div class="col-md-2">
        <b>SERVICIO:</b>  {{$unidad}}
    </div>
    <div class="col-md-4">
        <b>FECHA:</b>  {{ Carbon\Carbon::now() }}
    </div>
</div>

<br>

<div class="formulario">
    {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal', 'id' => 'epicrisisForm')) }}
    {{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfemeria')) }}

    <input type="hidden" value="" name="id_formulario_epicrisis" id="id_formulario_epicrisis">

    <div class="panel panel-default">
        <div class="panel-heading panel-info" style="background-color: #bce8f1 !important;">
            <h4>Identificación del paciente</h4>
        </div>
        <div class="panel-body">
            
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {{Form::label('', "Nombre", array( ))}}
                        {{-- {{Form::text('nombre_paciente', $infoPaciente->nombre ." ".$infoPaciente->apellido_paterno. " ".$infoPaciente->apellido_materno , array('id' => 'nombre_paciente', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$infoPaciente->nombre ." ".$infoPaciente->apellido_paterno. " ".$infoPaciente->apellido_materno}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "RUN", array( ))}}
                        {{-- {{Form::text('rut', $infoPaciente->rut."-".$infoPaciente->dv, array('id' => 'rut', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$infoPaciente->rut."-".$infoPaciente->dv}}</p>
                    </div> 
                </div>
                <div class="col-md-1 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Edad", array( ))}}
                        {{-- {{Form::text('edad', Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento), array('id' => 'edad', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento)}} años</p>
                    </div> 
                </div>
                <div class="col-md-1 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "N° de ficha", array( ))}}
                        {{-- {{Form::text('num_ficha', $dau->dau, array('id' => 'num_ficha', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$dau->dau}}</p>
                    </div> 
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('', "Nombre de cuidador o familiar", array()) }}
                        <p id="tacompanante"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{ Form::label('', "Vinculo familiar", array()) }}
                        <p id="tvinculo_acompanante"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{ Form::label('', "Número telefonico de cuidador o familiar", array()) }}
                        <p id="ttelefono_acompanante"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info" style="background-color: #bce8f1 !important;">
            <h4>Antecedentes de hospitalización</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group"> 
                        {{Form::label('', "Fecha Ingreso", array( ))}}
                        {{-- {{Form::text('fecha_ingreso', $fechaSolicitud, array('id' => 'fecha_ingreso', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$fechaSolicitud}}</p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Fecha Hospitalización", array( ))}}
                        {{-- {{Form::text('fecha_hospitalizacion', $fechaHospitalizacion, array('id' => 'fecha_hospitalizacion', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$fechaHospitalizacion}}</p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Fecha Egreso", array( ))}}
                        {{-- {{Form::text('fecha_egreso', $fechaEgreso, array('id' => 'fecha_egreso', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$fechaEgreso}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group"> 
                        {{Form::label('', "Estadia (Hospitalización y Egreso)", array( ))}}
                        {{-- {{Form::text('estadia2', $diffHospEgreso, array('id' => 'estadia2', 'class' => 'form-control', 'readonly'))}} --}}
                        <p>{{$diffHospEgreso}} días</p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "Destino", array( ))}}
                        {{ Form::select('destino_egreso', $destinos, null , ['class' => 'form-control', "id" => "destino_egreso"]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info" style="background-color: #bce8f1 !important;">
            <h4>Diagnóstico médico</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12" style="padding-bottom: 20px;">
                <div class="col-md-12 form-group">
                    <legend>Diagnósticos</legend>
                    @foreach($diagnosticos as $d)
                        {{-- <label for=""><input type="checkbox" name="diagnosticos[]" {{$d->id}}> {{$d->diagnostico}}</label> --}}
                        <label>
                            {{Form::checkbox('diagnosticos[]', $d->id)}} {{$d->diagnostico}}
                        </label><br>
                    @endforeach
                </div>                
            </div>
            <br><br>
            <div class="col-md-12">
                <div class="col-md-12">
                    <legend>Otros</legend>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('', "Intervencion Quirurgica", array( ))}}
                            {{Form::text('intervencion_quirurgica', null, array('id' => 'intervencion_quirurgica', 'class' => 'form-control'))}}
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "Fecha", array( ))}}
                            {{Form::text('fecha_intervencion', null, array('id' => 'fecha_intervencion', 'class' => 'fechaIntervencion form-control'))}}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-2"> 
                        <button type="submit" class="btn btn-primary" id="btnGuardarEpicrisis">Guardar</button>
                    </div>
                    <div class="col-md-2" id="btnReportePDF"> 
                        {{ HTML::link(URL::route('pdfInformeEpicrisis', [$caso]), 'Reporte Pdf', ['class' => 'btn btn-danger', 'target' => '_blank']) }}
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
    
  
    {{ Form::close() }}
</div>  
@include('Gestion.gestionEnfermeria.cuidadosAlta')
<br>
@include('Gestion.gestionEnfermeria.EvolucionEnfermeria.evolucionesEnfermeria')  
@include('Gestion.gestionEnfermeria.continuidadAtencion')
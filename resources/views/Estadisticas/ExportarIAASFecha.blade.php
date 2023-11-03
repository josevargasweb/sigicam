@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Exportar IAAS</a></li>
@stop

@section("script")
<script>
    $(function(){
               
$(document).ready(function() {
        $tabla=$('#tablaResultado2').DataTable({    
            "iDisplayLength": -1,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });
          <?php  foreach ($data as $datos){ ?>
           $tabla.row.add(["<?php echo $datos[0]; ?>","<?php echo $datos[1]; ?>","<?php echo $datos[2]; ?>","<?php echo $datos[3]; ?>",
            "<?php echo $datos[4]; ?>","<?php echo $datos[5]; ?>","<?php echo $datos[6]; ?>","<?php echo $datos[7]; ?>","<?php echo $datos[8]; ?>",
            "<?php echo $datos[9]; ?>","<?php echo $datos[10]; ?>","<?php echo $datos[11]; ?>","<?php echo $datos[12]; ?>","<?php echo $datos[13]; ?>",
            "<?php echo $datos[14]; ?>","<?php echo $datos[15]; ?>","<?php echo $datos[16]; ?>","<?php echo $datos[17]; ?>","<?php echo $datos[18]; ?>",
            "<?php echo $datos[19]; ?>","<?php echo $datos[20]; ?>","<?php echo $datos[21]; ?>","<?php echo $datos[22]; ?>","<?php echo $datos[23]; ?>",
            "<?php echo $datos[24]; ?>","<?php echo $datos[25]; ?>","<?php echo $datos[26]; ?>","<?php echo $datos[27]; ?>","<?php echo $datos[28]; ?>",
            "<?php echo $datos[29]; ?>","<?php echo $datos[30]; ?>","<?php echo $datos[31]; ?>","<?php echo $datos[32]; ?>","<?php echo $datos[33]; ?>",
            "<?php echo $datos[34]; ?>","<?php echo $datos[35]; ?>","<?php echo $datos[36]; ?>","<?php echo $datos[37]; ?>","<?php echo $datos[38]; ?>",
            "<?php echo $datos[39]; ?>","<?php echo $datos[40]; ?>","<?php echo $datos[41]; ?>","<?php echo $datos[42]; ?>","<?php echo $datos[43]; ?>",
            "<?php echo $datos[44]; ?>","<?php echo $datos[45]; ?>","<?php echo $datos[46]; ?>","<?php echo $datos[47]; ?>","<?php echo $datos[48]; ?>",
            "<?php echo $datos[49]; ?>","<?php echo $datos[50]; ?>","<?php echo $datos[51]; ?>"]);
           <?php } ?>
            
            $tabla.draw();
                                    
  });

        $(".fecha-mm-yyyy").datepicker({
                autoclose: true,
                language: "es",
                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months",
                todayHighlight: true,
                endDate: "+0d"
            }).on("changeDate", function(){
                $('#form-generar').bootstrapValidator('revalidateField', 'fecha');
            });

            $("#form-generar").bootstrapValidator({
                excluded: ':disabled',
                fields: {
                    fecha: {
                        validators:{
                            notEmpty: {
                                message: 'Debe especificar la fecha'
                            }
                        }
                    }
                }
            }).on('status.field.bv', function(e, data) {
                $("#form-generar input[type='submit']").prop("disabled", false);
            }).on("success.form.bv", function(evt){
                evt.preventDefault(evt);
                $("#resultado").html("");
                var $form = $(evt.target)[0];
                $("#loading").show();
                $.ajax({
                    url: "obtenerFechaIAAS",
                    type: "post",
                    dataType: "json",
                    data: new FormData($form),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        if(data.out) {
                            var IaasFecha=data.contenido;
                            location.href='{{URL::to("obtenerFechaIAAS2/'+IaasFecha+'")}}';
                        }
                        if(!data.out) {
                            	swaling.fire({
                                title: 'Información',
                                text:data.msg
						        });
                            console.log(data.msg);
                        }
                        $("#form-generar input[type='submit']").prop("disabled", false);
                        $("#loading").hide();
                    },
                    error: function(error){
                        $("#loading").hide();
                        console.log(error);
                    }
                });
                return false;
            });
        

      });
</script>
@stop

@section("section")
<fieldset>
    <legend>Seleccionar mes</legend>
        <div class="panel panel-default">
            <div class="panel-body">
                {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'form-generar', 'onsubmit' => 'return false')) }}
                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                    <label for="fecha" class="col-sm-1 control-label">Fecha: </label>
                    <div class="col-sm-4">
                        {{ Form::text("fecha", \Carbon\Carbon::now()->format("m-Y"), array("class" => "form-control fecha-mm-yyyy")) }}
                    </div>
                    <div class="col-md-2">
                        {{ Form::submit('Generar datos', array("class" => "btn btn-primary")) }}
                    </div>
                    <div class="col-md-7"></div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
</fieldset>
<fieldset>
<div class="col-md-2">
    {{ Form::open(['url' => URL::route("generarExceliaas"),"id" => "form-datos-iaas","method" => "POST"]) }}
    {{ Form::submit('Exportar a XLS', array("class" => "btn btn-primary","onclick"=>"location.reload()")) }}<br><br>
    {{ Form::close() }}
</div>
   <div class="table-responsive">
    <table id="tablaResultado2" class="table table-striped table-condensed table-bordered" style="display: block;overflow: scroll;overflow: auto;">
            <thead>
            <tr>
                <th>Establecimiento</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Run</th>
                <th>Aislamiento</th>
                <th>Fallecimiento</th>
                <th>Motivo Fallecimiento</th>
                <th>Fecha termino IAAS</th>
                <th>Motivo termino IAAS</th>
                <th>Servicio IAAS</th>
                <th>Fecha Notificación IAAS</th>
                <th>Fecha Inicio IAAS</th>
                <th>Localización</th>
                <th>Procedimiento_invasivo</th>
                <th>Agente Etiológico 1</th>
                <th>Sensibilidad 1</th>
                <th>Intermedia 1</th>
                <th>Resistencia 1</th>
                <th>Sensibilidad 2</th>
                <th>Intermedia 2</th>
                <th>Resistencia 2</th>
                <th>Sensibilidad 3</th>
                <th>Intermedia 3</th>
                <th>Resistencia 3</th>
                <th>Sensibilidad 4</th>
                <th>Intermedia 4</th>
                <th>Resistencia 4</th>
                <th>Sensibilidad 5</th>
                <th>Intermedia 5</th>
                <th>Resistencia 5</th>
                <th>Sensibilidad 6</th>
                <th>Intermedia 6</th>
                <th>Resistencia 6</th>
                <th>Agente Etiológico 2</th>
                <th>Sensibilidad 1</th>
                <th>Intermedia 1</th>
                <th>Resistencia 1</th>
                <th>Sensibilidad 2</th>
                <th>Intermedia 2</th>
                <th>Resistencia 2</th>
                <th>Sensibilidad 3</th>
                <th>Intermedia 3</th>
                <th>Resistencia 3</th>
                <th>Sensibilidad 4</th>
                <th>Intermedia 4</th>
                <th>Resistencia 4</th>
                <th>Sensibilidad 5</th>
                <th>Intermedia 5</th>
                <th>Resistencia 5</th>
                <th>Sensibilidad 6</th>
                <th>Intermedia 6</th>
                <th>Resistencia 6</th>
            </tr>
            </thead>
            <tbody></tbody>
    </table
    </div>
        <div id="resultado">
    </div>
</fieldset>
@stop

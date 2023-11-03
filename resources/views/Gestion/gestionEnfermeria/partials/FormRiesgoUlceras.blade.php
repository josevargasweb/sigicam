<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4>Escala Evaluación Riesgo de Lesiones por Presión</h4>
        
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="percepcion-sensorial" class="control-label" title="percepcion sensorial">Percepción Sensorial: </label>
                    {{Form::select('percepcion_sensorial', array(''=>'Seleccione', '1' => '(1 pts.) Completamente Limitada','2' => '(2 pts.) Muy Limitada','3' => '(3 pts.) Ligeramente Limitada','4' => '(4 pts.) Sin Limitaciones'), null,array('class' => 'form-control selectulceras', 'id'=>'percepcion_sensorial'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">   
                <div class="col-sm-10">
                    <label for="exposicion-humedad" class="control-label" title="Exposición a la Humedad">Exposición a la Humedad: </label>
                    {{Form::select('exposicion_humedad', array(''=>'Seleccione', '1' => '(1 pts.) Constantemente Húmeda','2' => '(2 pts.) A Menudo','3' => '(3 pts.) Ocasionalmente Húmeda','4' => '(4 pts.) Raramente Húmeda'), null,array('class' => 'form-control selectulceras', 'id'=>'exposicion_humedad'))}}
                </div>
            </div>    
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="actividad" class="control-label" title="Actividad">Actividad: </label>
                    {{Form::select('actividad', array(''=>'Seleccione', '1' => '(1 pts.) Encamado','2' => '(2 pts.) En silla','3' => '(3 pts.) Deambula Ocasionalmente','4' => '(4 pts.) Deambula Frecuentemente'), null,array('class' => 'form-control selectulceras', 'id'=>'actividad'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="movilidad" class="control-label" title="Movilidad">Movilidad: </label>
                    {{Form::select('movilidad', array(''=>'Seleccione', '1' => '(1 pts.) Completamente Inmóvil','2' => '(2 pts.) Muy Limitada','3' => '(3 pts.) Ligeramente Limitada','4' => '(4 pts.) Sin Limitaciones'), null,array('class' => 'form-control selectulceras', 'id'=>'movilidad'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="nutricion" class="control-label" title="Nutricion">Nutrición: </label>
                    {{Form::select('nutricion', array(''=>'Seleccione', '1' => '(1 pts.) Muy Pobre','2' => '(2 pts.) Probablemente Inadecuada','3' => '(3 pts.) Adecuada','4' => '(4 pts.) Excelente'), null,array('class' => 'form-control selectulceras', 'id'=>'nutricion'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="peligro-lesiones" class="control-label" title="peligro lesiones">Roce y Peligro de Lesiones: </label>
                    {{Form::select('peligro_lesiones', array(''=>'Seleccione', '1' => '(1 pts.) Problema','2' => '(2 pts.) Problema Potencial','3' => '(3 pts.) No Existe Problema'), null,array('class' => 'form-control selectulceras', 'id'=>'peligro_lesiones'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-5">
                    <label for="total-ulcera" class="control-label" title="Total Ulceras">Total:</label>
                    <input type="number" name="total" id="totalUlceras" class="form-control" readonly value="0">  
                </div>
                <div class="col-sm-5">
                    <label for="spanDetalleTotal" class="control-label" title="Total">&nbsp </label>
                    {{Form::text('detalleRiesgoUlcera', 'Alto', array('readonly','id' => 'detalleRiesgoUlcera', 'class' => 'form-control'))}}
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>


<input id="guardarUlceras" type="submit" name="" class="btn btn-primary" value="Guardar">
{{-- </div> --}}

<br><br>

    <table class="table table-bordered">
        <thead style="background:#399865; color: cornsilk;">
                <tr>
                    <th>Puntaje</th>
                    <th>Nivel</th>
                    {{-- <th>Código</th> --}}
                </tr>
        </thead>
        <tbody>
              <tr>
                  <td>≥ 16</td>
                  <td>Bajo</td>
                  {{-- <td style="background-color: #92d050">Verde</td> --}}
              </tr>

              <tr>
                  <td>13 - 15</td>
                  <td>Medio</td>
                  {{-- <td style="background-color: #ffc000">Amarillo</td> --}}
              </tr>

              <tr>
                  <td>≤ 12</td>
                  <td>Alto</td>
                  {{-- <td style="background-color: #ff0000">Rojo</td> --}}
              </tr>
        </tbody>
    </table>

<script>
    $(document).ready( function() {
        $(".selectulceras").change(function(){
            percepcion_sensorial  = $("#percepcion_sensorial").val();
            exposicion_humedad = $("#exposicion_humedad").val();
            actividad = $("#actividad").val();
            movilidad = $("#movilidad").val();
            nutricion = $("#nutricion").val();
            peligro_lesiones = $("#peligro_lesiones").val();

            suma = Number(percepcion_sensorial) + Number(exposicion_humedad) + Number(actividad) + Number(movilidad) + Number(nutricion) + Number(peligro_lesiones);

            $("#totalUlceras").val(suma);

            if(suma == 0){
                $("#detalleRiesgoUlcera").val("");
            }
            else if(suma <= 12){
                $("#detalleRiesgoUlcera").val("Alto");
            }else if(suma >= 13 && suma <= 15){
                $("#detalleRiesgoUlcera").val("Medio");
            }else if(suma >= 16){
                $("#detalleRiesgoUlcera").val("Bajo");
            }
        });
    });  
</script>
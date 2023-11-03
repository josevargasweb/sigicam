<div class="panel panel-default">
  <div class="panel-heading panel-info">
      <h4>Escala Nova:</h4>
  </div>


  <div class="panel-body">
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-10">
                  <label for="estado_mental" class="control-label" title="Estado mental">Estado mental</label>
                  {{Form::select('estado_mental', array(''=>'seleccione', '0'=>'(0 pts.) Alerta', '1'=>'(1 pts.) Desorientado', '2'=>'(2 pts.) Letárgico', '3'=>'(3 pts.) Coma'), null,array('class' => 'form-control selectNova', 'id'=>'estado_mental'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-10">
                  <label for="incontinencia" class="control-label" title="Incontinencia">Incontinencia</label>
                  {{Form::select('incontinencia', array(''=>'seleccione', '0'=>'(0 pts.) No', '1'=>'(1 pts.) Ocasionalmente limitada', '2'=>'(2 pts.) Urinario o Fecal importante', '3'=>'(3 pts.) Urinario y Fecal'), null,array('class' => 'form-control selectNova', 'id'=>'incontinencia'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-10">
                  <label for="movilidad" class="control-label" title="Movilidad">Movilidad</label>
                  {{Form::select('movilidad', array(''=>'seleccione', '0'=>'(0 pts.) Completa', '1'=>'(1 pts.) Ligeramente con ayuda limitada', '2'=>'(2 pts.) Limitación', '3'=>'(3 pts.) Inmovil'), null,array('class' => 'form-control selectNova', 'id'=>'movilidad'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-10">
                  <label for="nutricion_ingesta" class="control-label" title="Nutricion ingesta">Nutricion ingesta</label>
                  {{Form::select('nutricion_ingesta', array(''=>'seleccione', '0'=>'(0 pts.) Correcta', '1'=>'(1 pts.) Ocasionalmente con ayuda', '2'=>'(2 pts.) Incompleta siempre con ayuda', '3'=>'(3 pts.) No ingesta oral, ni enteral, ni parenteral superior a 72 hrs y/o desnutrición previa'), null,array('class' => 'form-control selectNova', 'id'=>'nutricion_ingesta'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-10">
                  <label for="actividad" class="control-label" title="Actividad">Actividad</label>
                  {{Form::select('actividad', array(''=>'seleccione', '0'=>'(0 pts.) Deambula', '1'=>'(1 pts.) Deambula con ayuda', '2'=>'(2 pts.) Deambula siempre precisa ayuda', '3'=>'(3 pts.) No deambula, encamado'), null,array('class' => 'form-control selectNova', 'id'=>'actividad'))}}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="form-group col-md-12">
              <div class="col-sm-5">
                  <label for="total" class="col-form-label">Puntos </label>
                  <input type="number" name="total" id="puntos" class="form-control" readonly>
              </div>
              <div class="col-sm-5">
                  <label for="spanDetalleTotal" class="control-label" title="Total">&nbsp; </label>
                  {{Form::text('detallex', null, array('readonly','id' => 'detallex', 'class' => 'form-control'))}}
                  
                  <br>
                  <b><span id="spanDetalleTotal"> </span></b>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="panel panel-default">
  
  <div class="panel-heading panel-info">
      <h4>Categorias de riesgo</h4>
  </div>
  
  <div class="panel-body">
      <div class="row">
          <div class="form-group col-md-3">
              <div class="col-sm-10">
                  <label for="sin_riesgo" class="col-form-label">0 puntos, Sin riesgo</label>
              </div>
          </div>
          <div class="form-group col-md-3">
              <div class="col-sm-10">
                  <label for="sin_riesgo" class="col-form-label">De 1 a 4 puntos, Riesgo bajo</label>
              </div>
          </div>
          <div class="form-group col-md-3">
              <div class="col-sm-10">
                  <label for="sin_riesgo" class="col-form-label">De 5 a 8 puntos, Riesgo medio</label>
              </div>
          </div>
          <div class="form-group col-md-3">
              <div class="col-sm-10">
                  <label for="sin_riesgo" class="col-form-label">De 9 a 15 puntos, Riesgo alto</label>
              </div>
          </div>
      </div>
  </div>
</div>

<input id="guardarNova" type="submit" name="" class="btn btn-primary" value="Ingresar Información">

<script>
    $(document).ready( function() {
        $(".selectNova").change(function(){
        // console.log("select change");
        estado_mental = $("#estado_mental").val();
        incontinencia = $("#incontinencia").val();
        movilidad = $("#movilidad").val();
        nutricion_ingesta = $("#nutricion_ingesta").val();
        actividad = $("#actividad").val();
        detallex;
        sinRiesgo = "Sin Riesgo";
        riesgoBajo = "Riesgo Bajo";
        riesgoMedio = "Riesgo Medio";
        riesgoAlto = "Riesgo Alto";

        total = Number(estado_mental) + Number(incontinencia) + Number(movilidad) + Number(nutricion_ingesta) + Number(actividad);
        if (total == 0) {
                //$("#spanDetalleTotal").text("Sin Riesgo");
                detallex = sinRiesgo;
                $("#detallex").val(detallex);
                //console.log($("#detallex").val(detallex));
                
            }
            if (total >= 1 && total <=4) {
                //$("#spanDetalleTotal").text("Riesgo Bajo");
                detallex = riesgoBajo;
                $("#detallex").val(detallex);
                //console.log($("#detallex").val(detallex));
                
            }
            if (total >= 5 && total <=8) {
                //$("#spanDetalleTotal").text("Riesgo Medio");
                detallex = riesgoMedio;
                $("#detallex").val(detallex);
                //console.log($("#detallex").val(detallex));
                
            }
            if (total >= 9 && total <=15) {
                //$("#spanDetalleTotal").text("Riesgo Alto");
                detallex = riesgoAlto;
                $("#detallex").val(detallex);
                //console.log($("#detallex").val(detallex));
                
            } 
            //$("#detallex").val(detallex);
        $("#puntos").val(total);

      });
    });
</script>
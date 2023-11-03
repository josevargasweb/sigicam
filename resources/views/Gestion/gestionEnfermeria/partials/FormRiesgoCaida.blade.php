

        
<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4>Escala Riesgo Caídas:</h4>
    </div>


    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="caidas_previas" class="control-label" title="Caídas Previas">Caídas Previas</label>
                    {{Form::select('caidas_previas', array(''=>'Seleccione', '0'=>'(0 pts.) No', '1'=>'(1 pts.) Si'), null,array('class' => 'form-control caidas_previas selectCaidas', 'id'=>'caidas_previas'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="medicamentos" class="control-label" title="Medicamentos">Medicamentos</label>
                    {{-- Form::select('medicamentos', array(''=>'Seleccione', '0'=>'(0 pts.) Ninguno', '1'=>'(1 pts.) Tranquilizantes sedantes', '2'=>'(1 pts.) Hipotensores (no diuréticos)', '3'=>'(1 pts.) Anti parkinsonianos', '4' => '(1 pts.) Antidepresivos', '5' => '(1 pts.) Anestesia'), null,array('class' => 'form-control selectCaidas', 'id'=>'medicamentos')) --}}
                    {{Form::select('medicamentos[]', array(''=>'Seleccione', '0'=>'(0 pts.) Ninguno', '1'=>'(1 pts.) Tranquilizantes sedantes', '2'=>'(1 pts.) Hipotensores (no diuréticos)', '3'=>'(1 pts.) Anti parkinsonianos', '4' => '(1 pts.) Antidepresivos', '5' => '(1 pts.) Anestesia', '6' => '(1 pts.) Diurético'), null,array('class' => 'form-control selectCaidas selectpicker', 'id'=>'medicamentos' , 'multiple'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="deficit" class="control-label" title="Déficits">Déficits sensoriales</label>
                    {{Form::select('deficit', array(''=>'Seleccione', '0'=>'(0 pts.) Ninguno', '1'=>'(1 pts.) Alteraciones visuales', '2'=>'(1 pts.) Alteraciones auditivas', '3'=>'(1 pts.) Extremidades (ictus)'), null,array('class' => 'form-control selectCaidas', 'id'=>'deficit'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="mental" class="control-label" title="Estado mental">Estado mental</label>
                    {{Form::select('mental', array(''=>'Seleccione', '0'=>'(0 pts.) Orientado', '1'=>'(1 pts.) Confuso'), null,array('class' => 'form-control selectCaidas', 'id'=>'mental'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="deambulacion" class="control-label" title="Deambulación">Deambulación</label>
                    {{Form::select('deambulacion', array(''=>'Seleccione', '0'=>'(0 pts.) Normal', '1'=>'(1 pts.) Segura con ayuda', '2'=>'(1 pts.) Insegura con ayuda/sin ayuda', '3'=>'(1 pts.) Imposible'), null,array('class' => 'form-control selectCaidas', 'id'=>'deambulacion'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-5">
                    <label for="total" class="col-form-label">Puntos </label>
                    <input type="number" name="total" id="puntosCaida" class="form-control" readonly value="0">
                </div>
                <div class="col-sm-5">
                    <label for="spanDetalleTotal" class="control-label" title="Total">&nbsp; </label>
                    {{Form::text('detalleCaida', "Bajo Riesgo", array('readonly','id' => 'detalleCaida', 'class' => 'form-control'))}}

                    <br>
                    {{-- <b><span id="spanDetalleTotal"> </span></b> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<input id="btnriesgocaida" type="submit" name="" class="btn btn-primary" value="Ingresar Información">
<br><br>

<table class="table table-bordered">
    <thead style="background:#399865; color: cornsilk;">
            <tr>
                <th>Puntaje</th>
                <th>Nivel</th>
            </tr>
    </thead>
    <tbody>
          <tr>
              <td>De 0 a 1 punto</td>
              <td>Bajo riesgo</td>
          </tr>

          <tr>
              <td>Mayor o igual a 2 puntos</td>
              <td>Alto riesgo</td>
          </tr>
    </tbody>
</table>




<script>
    $(document).ready( function() {
   
        $(".selectCaidas").change(function(){
            caidas_previas = ($("#caidas_previas").val() === "0" || $("#caidas_previas").val() === "")?0:1;
            deficit = ($("#deficit").val() === "0" || $("#deficit").val() === "")?0:1;
            if (!$("#medicamentos").hasClass("selectpicker")) {
                medicamentos = ($("#medicamentos").val() === "0" || $("#medicamentos").val() === "")?0:1;
            }
            mental = ($("#mental").val() === "0" || $("#mental").val() === "")?0:1;
            deambulacion = ($("#deambulacion").val() === "0" || $("#deambulacion").val() === "")?0:1;

            // total = Number(caidas_previas) + Number(deficit) + Number(medicamentos) + Number(mental) + Number(deambulacion);
            total = Number(caidas_previas) + Number(medicamentos) + Number(deficit) + Number(mental) + Number(deambulacion);
            
            $("#puntosCaida").val(total);
            if(total <= 1){
                $("#detalleCaida").val("Bajo Riesgo");
            }else{
                $("#detalleCaida").val("Alto Riesgo");
            }
        });


        if ($("#medicamentos").hasClass("selectpicker")) {
            $('#medicamentos').find('[value=""]').prop('disabled', true);
            $('#medicamentos').selectpicker('refresh');
            $("#medicamentos").on("changed.bs.select", function(e, clickedIndex, isSelected, oldValue) {
                //cuando marca seleccione
                if(clickedIndex == 0){
                    $('#medicamentos').selectpicker('deselectAll');
                    $('#medicamentos').selectpicker('val', '');
                    //cuando marca ninguno
                }else if(clickedIndex == 1){
                    $('#medicamentos').selectpicker('deselectAll');
                    if(oldValue == false){
                        $('#medicamentos').selectpicker('val','0');
                      
                    }else{
                        $('#medicamentos').selectpicker('val', ['']);
                    }
                    medicamentos = 0;
                    //cuando esta vacio 
                }else if( $("#medicamentos").val() == '' || $("#medicamentos").val() == null){
                    $('#medicamentos').selectpicker('deselectAll');
                    $('#medicamentos').selectpicker('val', '');
                    medicamentos = 0;
                }else{
                    arrayMedicamentos = $('#medicamentos').selectpicker('val');
                    vacio = arrayMedicamentos.indexOf('');
                    ninguno = arrayMedicamentos.indexOf('0');
                    //si viene vacio
                    if (vacio > -1) {
                        arrayMedicamentos.splice(vacio, 1);
                    }
                    if(ninguno > -1){
                        arrayMedicamentos.splice(ninguno, 1);
                    }
                    $('#medicamentos').selectpicker('val', arrayMedicamentos);
                    medicamentos = $('#medicamentos').selectpicker('val').length;
                }
                $('#medicamentos').selectpicker('refresh');


                total = Number(caidas_previas) + Number(medicamentos) + Number(deficit) + Number(mental) + Number(deambulacion);
            
            $("#puntosCaida").val(total);
            if(total <= 1){
                $("#detalleCaida").val("Bajo Riesgo");
            }else{
                $("#detalleCaida").val("Alto Riesgo");
            }
            });
        }
    });
</script>
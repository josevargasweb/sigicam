

<legend class="text-center" id="legendMacdems">Escala Macdems</legend>
        

        
        <br>

        {{-- arriba nuevo --}}

<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4>Escala Macdems:</h4>
    </div>


    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="edad" class="control-label" title="edad">Edad</label>
                    {{Form::select('edad', array(''=>'Seleccione', '0'=>'(1 pts.) Escolar', '1'=>'(3 pts.) Pre - escolar', '2'=>'(3 pts.) Lactante Mayor', '3'=>'(2 pts.) Lactante Menor', '4'=>'(2 pts.) Recién Nacido'), null,array('class' => 'form-control selectMacdems', 'id'=>'edad'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="caidas_previas" class="control-label" title="Caídas Previas">Antecedentes de Caídas Previas</label>
                    {{Form::select('caidas_previas', array(''=>'Seleccione', '0'=>'(0 pts.) No', '1'=>'(1 pts.) Si'), null,array('class' => 'form-control caidas_previas selectMacdems', 'id'=>'caidas_previas_macdems'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="antecedentes" class="control-label" title="Antecedentes">Antecedentes</label>
                    {{Form::select('antecedentes', array(''=>'Seleccione', '0'=>'(0 pts.) Sin antecedentes', '1'=>'(1 pts.) Otros', '2'=>'(1 pts.) Daño organico cerebral', '3'=>'(1 pts.) Sindrome convulsivo', '4' => '(1 pts.) Problemas Neuromusculares', '5' => '(1 pts.) Hiperactividad'), null,array('class' => 'form-control selectMacdems', 'id'=>'antecedentes'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="compromiso_conciencia" class="control-label" title="Compromiso conciencia">Compromiso conciencia</label>
                    {{Form::select('compr_conciencia', array(''=>'Seleccione', '0'=>'(0 pts.) No', '1'=>'(1 pts.) Si'), null,array('class' => 'form-control caidas_previas selectMacdems', 'id'=>'compr_conciencia'))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-5">
                    <label for="total" class="col-form-label">Puntos </label>
                    <input type="number" name="total" id="puntosMacdems" class="form-control" readonly value="0">
                </div>
                <div class="col-sm-5">
                    <label for="spanDetalleTotal" class="control-label" title="Total">&nbsp; </label>
                    {{Form::text('detalleMacdems', "Bajo Riesgo", array('readonly','id' => 'detalleMacdems', 'class' => 'form-control'))}}
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">

    <div class="panel-heading panel-info">
        <h4>Interpretación de puntaje</h4>
    </div>

    <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-3">
                    <div class="col-sm-10">
                        <label for="sin_riesgo" class="col-form-label">De 0 a 1 punto, bajo riesgo</label>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <div class="col-sm-10">
                        <label for="sin_riesgo" class="col-form-label">De 2 a 3 puntos, mediano Riesgo</label>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <div class="col-sm-10">
                        <label for="sin_riesgo" class="col-form-label">De 4 a 6 puntos, alto Riesgo</label>
                    </div>
                </div>
            </div>

    </div>
</div>
<input id="btnescalamacdems" type="submit" name="" class="btn btn-primary" value="Ingresar Información">

{{-- nuevo --}}


<script>
    $(document).ready( function() {
        
        $(".selectMacdems").change(function(){
                if($("#edad").val() === "0"){
                    edad = 1;
                }else if($("#edad").val() === "1" || $("#edad").val() === "2"){
                    edad = 3;
                }else if($("#edad").val() === "3" || $("#edad").val() === "4"){
                    edad = 2;
                }else{
                    edad = 0;
                }
                caidas_previas = ($("#caidas_previas_macdems").val() === "0" || $("#caidas_previas_macdems").val() === "")?0:1;
                antecedentes = ($("#antecedentes").val() === "0" || $("#antecedentes").val() === "")?0:1;
                compr_conciencia = ($("#compr_conciencia").val() === "0" || $("#compr_conciencia").val() === "")?0:1;
                
                total = Number(edad) + Number(caidas_previas) + Number(antecedentes) + Number(compr_conciencia);
                console.log(total);
                $("#puntosMacdems").val(total);
                if(total <= 1){
                    $("#detalleMacdems").val("Bajo Riesgo");
                }
                if(total >= 2 && total <= 3){
                    $("#detalleMacdems").val("Mediano Riesgo");
                }
                if(total >= 4){
                    $("#detalleMacdems").val("Alto Riesgo");
                }
            });
        });
</script>

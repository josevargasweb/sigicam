<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4>Escala de glasgow:</h4>
        
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="fecha-ingreso" class="control-label" title="Fecha de ingreso">Apertura ocular </label>
                    {{Form::select('apertura_ocular', array(''=>'Seleccione', '1' => '(1 pts.) No abre','2' => '(2 pts.) Al dolor','3' => '(3 pts.) A la voz','4' => '(4 pts.) Espontaneo'), null,array('class' => 'form-control selectglasgow', 'id'=>'apertura_ocular'))}}
            
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="form-group col-md-12">   
                <div class="col-sm-10">
                    <label for="via-ingreso" class="control-label" title="Via ingreso">Respuesta verbal: </label>
                    {{Form::select('respuesta_verbal', array(''=>'Seleccione', '1' => '(1 pts.) No hay','2' => '(2 pts.) Sonidos incomprensibles','3' => '(3 pts.) Palabras sueltas','4' => '(4 pts.) Desorientado', '5'=>'(5 pts.) Orientado'), null,array('class' => 'form-control selectglasgow', 'id'=>'respuesta_verbal'))}}

                </div>
            </div>    
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-sm-10">
                    <label for="rut" class="control-label" title="Rut">Respuesta motora: </label>
                    {{Form::select('respuesta_motora', array(''=>'Seleccione', '1' => '(1 pts.) No','2' => '(2 pts.) Descerebracioón','3' => '(3 pts.) Decorticación','4' => '(4 pts.) Movimientos sin proposito', '5'=>'(5 pts.) Localiza estimulo doloroso', '6'=>'(6 pts.) Obedece ordenes'), null,array('class' => 'form-control selectglasgow', 'id'=>'respuesta_motora'))}}

                </div>
            </div>
           
        </div>


        <div class="row">
            <div class="form-group col-md-12">
                <div class="col-md-12">
                    <label for="total" class="col-form-label" style="margin-bottom: 0px;">Total:</label>
                </div>
                <div class="col-sm-5">
                    <input type="number" name="total" id="totalGlasgow" class="form-control" readonly>
                </div>
                <div class="col-sm-5">
                    {{Form::text('detalleGlasgow', '', array('readonly','id' => 'detalleGlasgow', 'class' => 'form-control'))}}
                </div>
            </div>
        </div>

    </div>
</div>

<table class="table table-bordered">
        <thead style="background:#399865; color: cornsilk;">
            <tr>
                <th>Resultado</th>
                <th>Gravedad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>3-8</td>
                <td>Grave</td>
            </tr>

            <tr>
                <td>9-12</td>
                <td>Moderado</td>
            </tr>

            <tr>
                <td>13-15</td>
                <td>Leve</td>
            </tr>
        </tbody>
    </table>

<input id="guardarGlasgow" type="submit" name="" class="btn btn-primary" value="Guardar">
{{-- </div> --}}


<script>
    $(document).ready( function() {
        $(".selectglasgow").change(function(){
            // console.log("change");
            apertura_ocular  = $("#apertura_ocular").val();
            respuesta_verbal = $("#respuesta_verbal").val();
            respuesta_motora = $("#respuesta_motora").val();

            suma = Number(apertura_ocular) + Number(respuesta_verbal) + Number(respuesta_motora);

            $("#totalGlasgow").val(suma);

            if(suma >= 3 && suma <= 8){
                $("#detalleGlasgow").val("Grave");
            }else if(suma >= 9 && suma <= 12){
                $("#detalleGlasgow").val("Moderado");
            }else if(suma >= 13 && suma <= 15){
                $("#detalleGlasgow").val("Leve");
            }

        });
    });  
</script>
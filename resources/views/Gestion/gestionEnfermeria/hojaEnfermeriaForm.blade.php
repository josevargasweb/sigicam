<script>
    $(document).ready(function() {

        $(".horaHE").inputmask({ alias: "datetime", inputFormat:"HH:mm"});

        $( "#agregarControl" ).click(function() {

            var html = "<div class='row'>  <div class='col-md-1'> <div class='form-group'> <input type='text' name='valor1[]' class='form-control'> </div> </div> <div class='col-md-1'> <div class='form-group'> <input type='text' name='valor2[]' class='form-control'> </div> </div> <div class='col-md-1'> <div class='form-group'> <input type='text' name='valor3[]' class='form-control'> </div> </div> <div class='col-md-1'> <div class='form-group'> <input type='text' name='valor4[]' class='form-control'> </div> </div> <div class='col-md-1'> <div class='form-group'> <input type='text' name='valor5[]' class='form-control'> </div> </div> </div>";
            
            $("#signosVitales").append(html);
        });

        $( ".calcularRiesgoCaida" ).change(function() {
            var total = 0;
            
            total += ($("#criterioEdad").val() === "true")?1:0;
            total += ($("#criterioComprConciencia").val() === "true")?2:0;
            total += ($("#criterioAgiPsicomotora").val() === "true")?2:0;
            total += ($("#criterioLimSensorial").val() === "true")?1:0;
            total += ($("#criterioLimMotora").val() === "true")?1:0;
            $("#totalEnfermeria").val(total);
        });
        

        $("#hojaEnfermeriaform").bootstrapValidator({
            excluded: ':disabled',
            fields: {  
            }
        }).on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btnSolicitar").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            $.ajax({
                url: "{{URL::to('/gestionEnfermeria')}}/registrarHojaEnfermeria",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  $form .serialize(),
                dataType: "json",
                type: "post",
                success: function(data){
                    
                    console.log(data);
                    if (data.exito) {
                      swalExito.fire({
						title: 'Exito!',
						text: data.exito,
						didOpen: function() {
							setTimeout(function() {
						location . reload();
							}, 2000)
						},
						});
                    }

                    if (data.error) {
                        swalError.fire({
						title: 'Error',
						text:data.error
						}).then(function(result) {
						if (result.isDenied) {
							  location . reload();
						}
						})
						
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });

        });

       $("input").change(function(){
                    console.log("input change");
                    volumen1 = $("#volumen1").val();
                    volumen2 = $("#volumen2").val();
                    volumen3 = $("#volumen3").val();
                    volumen4 = $("#volumen4").val();
                    volumen5 = $("#volumen5").val();
                    volumen6 = $("#volumen6").val();

                    total = Number(volumen1) + Number(volumen2) + Number(volumen3) + Number(volumen4) + Number(volumen5) + Number(volumen6);
                       
                    $("#total_volumen").val(total);

            });

     
        $(function () {
                $('.datetimepicker3').datetimepicker({
                    format: 'LT'
                });
            });
        
        $(function () {
                $('.datetimepicker1').datetimepicker();
            });

        $("input").change(function(){
                    console.log("input change");
                    ml1 = $("#ml1").val();
                    ml2 = $("#ml2").val();
                    ml4 = $("#ml4").val();
                    ml5 = $("#ml5").val();
                    ml7 = $("#ml7").val();
                    ml8 = $("#ml8").val();
                    ml10 = $("#ml10").val();
                    ml11 = $("#ml11").val();
                    ml13 = $("#ml13").val();
                    ml14 = $("#ml14").val();
                    ml16 = $("#ml16").val();
                    ml17 = $("#ml17").val();
                    ml19 = $("#ml19").val();
                    ml20 = $("#ml20").val();
                    ml22 = $("#ml22").val();
                    ml23 = $("#ml23").val();

                    total1 = Number(ml1) + Number(ml2);
                    total2 = Number(ml4) + Number(ml5);
                    total3 = Number(ml7) + Number(ml8);
                    total4 = Number(ml10) + Number(ml11);
                    total5 = Number(ml13) + Number(ml14);
                    total6 = Number(ml16) + Number(ml17);
                    total7 = Number(ml19) + Number(ml20);
                    total8 = Number(ml22) + Number(ml23);

                       
                    $("#ml3").val(total1);
                    $("#ml6").val(total2);
                    $("#ml9").val(total3);
                    $("#ml12").val(total4);
                    $("#ml15").val(total5);
                    $("#ml18").val(total6);
                    $("#ml21").val(total7);
                    $("#ml24").val(total8);

            });


        
    });
    

</script>

<style>

    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }


</style>


{{ Form::open(array( 'method' => 'post', 'class' => 'form-horizontal',  'id' => 'hojaEnfermeriaform')) }}

{{ Form::hidden ('caso', $caso, array('id' => 'idCasoEnfermeria') )}}
{{ Form::hidden ('idForm', "INSERTAR", array('id' => 'idFormEnfermeria') )}}
    
<div class="row">
    <div class="col-md-6 formulario">
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4>CONTROL SIGNOS VITALES:</h4>
            </div>
            
            <div class="panel-body" id="signosVitales">
                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> HORARIO </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>

               <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Presión Arterial", array( 'class' => 'form-control'))}} </div> </div>   
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div> 
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>

                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Pulso", array( 'class' => 'form-control'))}} </div> </div>   
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>

                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Frec. Respiratoria", array( 'class' => 'form-control'))}} </div> </div>   
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>

                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Temp. Axílo/rectal", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>

                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Saturación", array( 'class' => 'form-control'))}} </div> </div>   
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
     
                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Hemoglucotest", array( 'class' => 'form-control'))}} </div> </div>   
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
    
                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Glasgow", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor2[]', array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor3[]', array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor4[]', array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor5[]', array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',), null, array( 'class' => 'form-control')) }} </div> </div>
                </div>
    
                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "FIO2", array( 'class' => 'form-control'))}} </div> </div> 
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control', 'placeholder' => 'Lts/min'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control', 'placeholder' => 'Lts/min'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control', 'placeholder' => 'Lts/min'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control', 'placeholder' => 'Lts/min'))}} </div> </div>
                </div>
    
                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Metodo O2", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor2[]', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor3[]', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor4[]', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::select('valor5[]', array('1' => 'Naricera', '2' => 'Mascarilla Simple', '3' => 'Mascarilla Venturi', '4' => 'Mascarilla con reservorio'), null, array( 'class' => 'form-control')) }} </div> </div>
                </div>

                <div class="col-md-12 signoVital">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('valor1[]', "Dolor (EVA)", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor2[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor4[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('valor5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                
                                          
            </div>
            {{-- <div class="panel-footer">
                <button id="agregarControl" type="button" class="btn btn-success">Agregar control</button>
            </div> --}}
            
        </div>
    </div>
    
    <div class="col-md-6 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>VOLUMENES DE SOLUCIONES</h4>
            </div>
    
            <div class="panel-body" id="columenesSoluciones">

                <div class="col-md-12" style="text-align:center;">
                    <div class="col-md-6"> Tipo solución</div>
                    <div class="col-md-2"> Volumen</div>
                    <div class="col-md-2"> Inicio</div>
                    <div class="col-md-2"> Término</div>
                </div>

                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen1'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen2'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen3'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen4'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen5'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('volumen1[]', array('1' => 'S. Fisiologico', '2' => 'S. Glucosalino', '3' => 'S. Glucosado', '4' => 'Ringer Lactato', '5' => ''), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::number('volumen2[]', null, array( 'class' => 'form-control', 'id' => 'volumen6'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen3[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('volumen4[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                </div>
                <div class="col-md-12 volumenesSoluciones">
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('totalVolumen', 'Total Volumen', array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('total_volumen', null, array( 'class' => 'form-control', 'id' => 'total_volumen', 'readonly'))}} </div> </div>
                </div>

            </div>
        </div>
    </div>
</div>
    
<div class="row">
    <div class="col-md-5 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CONTROL EGRESOS</h4>
            </div>
    
            <div class="panel-body" id="controlEgresos">

                <div class="col-md-14">
                    <div class="col-md-3"> </div>
                    <div class="col-md-2"> Día</div>
                    <div class="col-md-2"> Noche</div>
                    <div class="col-md-2"> Total</div>
                    <div class="col-md-2"> Observ.</div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "DIURESIS", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml1'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml2'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml3', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "DEPOSICIONES", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml4'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml5'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml6', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "VOMITOS", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml7'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml8'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml9', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "SNG", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml10'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml11'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml12', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "DRENAJE 1", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml13'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml14'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml15', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', "DRENAJE 2", array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml16'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml17'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml18', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', 'DRENAJE 3', array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml19'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml20'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml21', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', 'Perd. Insensibles', array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml22'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml23'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control', 'placeholder' => 'ml', 'id' => 'ml24', 'readonly'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>
                <div class="col-md-14 controlEgreso">
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('controlEgreso1[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso2[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso3[]', null, array( 'class' => 'form-control'))}} </div> </div>    
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso4[]', null, array( 'class' => 'form-control'))}} </div> </div>
                    <div class="col-md-2"> <div class="form-group"> {{Form::text('controlEgreso5[]', null, array( 'class' => 'form-control'))}} </div> </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-3 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>EXAMENES LABORATORIO</h4>
            </div>
    
            <div class="panel-body" id="examenesLab">

                <div class="col-md-12">
                    <div class="col-md-6"> SOLICITADOS</div>
                    <div class="col-md-6"> TOMADOS</div>
                </div>

                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>    
                </div>
                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>    
                </div>
                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>    
                </div>
                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>    
                </div>
                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>     
                </div>
                <div class="col-md-12 examenLaboratorio">
                    <div class="col-md-6"> <div class="form-group"> {{Form::select('examLab1[]', array('1' => 'Venosa', '2' => 'Arterial', '3' => 'Orina', '4' => 'Clasificacion Bioquimicos', '5' => 'Hematològicos'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-6"> <div class="form-group"> {{Form::text('examLab2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>     
                </div>
                

            </div>
        </div>
    </div>

    <div class="col-md-4 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>EXAMENES IMAGENES</h4>
            </div>
    
            <div class="panel-body" id="examenesImg">

                <div class="col-md-14">
                    <div class="col-md-3"> SOLICITADOS</div>
                    <div class="col-md-3"> TOMADOS</div>
                    <div class="col-md-3"> ESTADO</div>
                    <div class="col-md-3"> FECHA/HORA</div>
                </div>

                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam1')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam1'))}} </div> </div>  
                </div>
                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam2')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam2'))}} </div> </div>   
                </div>
                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam3')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam3'))}} </div> </div>   
                </div>
                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam4')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam4'))}} </div> </div>   
                </div>
                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam5')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam5'))}} </div> </div>  
                </div>
                <div class="col-md-14 examenImagen">
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg1[]', array('1' => 'EDA', '2' => 'Colonoscopia', '3' => 'EMN', '4' => 'TAC'), null, array( 'class' => 'form-control')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::select('examImg3[]', array('1' => 'Pendiente', '2' => 'Programado', '3' => 'Realizado'), null, array( 'class' => 'form-control', 'id'=>'estado_exam6')) }} </div> </div>
                    <div class="col-md-3"> <div class="form-group"> {{Form::text('examImg4[]', null, array( 'class' => 'datetimepicker1 form-control', 'placeholder' => 'HH:mm', 'id' => 'hora_exam6'))}} </div> </div>    
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CONTROL DÍAS ESTADA Y OTROS</h4>
            </div>

            <div class="panel-body" id="controlEgresos">
                <div class="col-md-2">
                    N° días Estada
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('estada', 0, array('id' => 'estada', 'class' => 'form-control','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días operado
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('operado', 0, array('id' => 'operado', 'class' => 'form-control','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array( 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array( 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array( 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array( 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array( 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
                <div class="col-md-2">
                    N° días ATB
                </div>
                <div class="col-md-2">
                    <div class="col-md-6"> <div class="form-group"> {{Form::number('atb[]', 0, array('min' => '0' , 'class' => 'form-control atb','required'))}} </div> </div>   
                </div>
            </div>

        </div>
    </div>
    
</div>

<div class="row">
    <div class="col-md-6" style="text-align:center;">
        RECETA MÉDICA
    </div>
    <div class="col-md-6">
        <div class="col-md-6">
            FOLIO
        </div>
        <div class="col-md-6">
            <div class="form-group"> {{Form::text('folio', null, array('id' => 'folio', 'class' => 'form-control'))}} </div> 
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>INDICACIONES MEDICAS</h4>
            </div>

            <div class="panel-body" id="">
                <div class="col-md-12"> <div class="form-group"> {{Form::textarea('indicacion', null, array('id' => 'indicacionesF', 'class' => 'form-control', 'rows' => '6','style' => 'resize:none'))}} </div> </div> 
            </div>
            <div class="panel-body" id="">
                <div class="col-md-3"> <div class="form-group"> {{Form::text('medicamento', 'Medicamento', array('class' => 'form-control'))}} </div> </div> 
                <div class="col-md-9"> <div class="form-group"> {{Form::text('ind_medicamento', null, array('class' => 'form-control'))}} </div> </div> 
            </div>
            <div class="panel-body" id="">
                <div class="col-md-3"> <div class="form-group"> {{Form::text('dosis', 'Dosis', array('class' => 'form-control'))}} </div> </div> 
                <div class="col-md-9"> <div class="form-group"> {{Form::text('ind_dosis', null, array('class' => 'form-control'))}} </div> </div> 
            </div>
            <div class="panel-body" id="">
                <div class="col-md-3"> <div class="form-group"> {{Form::text('via', 'Via', array('class' => 'form-control'))}} </div> </div> 
                <div class="col-md-9"> <div class="form-group"> {{Form::text('ind_via', null, array('class' => 'form-control'))}} </div> </div> 
            </div>
            <div class="panel-body" id="">
                <div class="col-md-3"> <div class="form-group"> {{Form::text('horario', 'Horario', array('class' => 'form-control'))}} </div> </div> 
                <div class="col-md-9"> <div class="form-group">{{ Form::select('ind_horario', array('1' => '4 hrs', '2' => '6 hrs', '3' => '8 hrs', '4' => '12 hrs', '5' => '24 hrs'), null, array( 'class' => 'form-control')) }} </div> </div>
            </div>

        </div>
    </div>

    <div class="col-md-6 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>HORARIO</h4>
            </div>

            <div class="panel-body" id="">
                <div class="col-md-12"> <div class="form-group"> {{Form::textarea('horario', null, array('id' => 'horarioF', 'class' => 'form-control', 'rows' => '6','style' => 'resize:none'))}} </div> </div> 
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CUIDADOS DE ENFERMERIA</h4>
            </div>

            <div class="panel-body" id="indicaciones">
                
                <div class="col-md-2">
                    <div class="col" style="margin-top: 16px;">
                        CUIDADOS
                    </div>
                    <div class="col descripcionCuidado"> <div class="form-group">{{Form::text('cuidado[]', 'Medición Diuresis', array( 'class' => 'form-control'))}}</div> </div>
                    <div class="col descripcionCuidado"> <div class="form-group">{{Form::text('cuidado[]', 'Aseo Ocular', array( 'class' => 'form-control'))}}</div> </div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Aseo Cavidades', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Aseo Genital', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Cambio Pañales', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Baño en Cama', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Aspirar SNG', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Aspirar Secreciones', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Curaciones', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Irrigación Foley', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Poner Vendas', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Retirar Vendas', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Levantar', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Lubricación Sitios Apoyo', array( 'class' => 'form-control'))}}</div></div>

                    <div class="col" style="margin-top: 20%;" ><div class="form-group">{{Form::label('','Cambio de Posición (agregar dirección)')}}</div></div>
                    
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Recorte Vello', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Enema', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Retiro Esmalte', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Retiro Joyas', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Retiro Protesis', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Colchon Antiescaras', array( 'class' => 'form-control'))}}</div></div>
                    <div class="col descripcionCuidado"><div class="form-group">{{Form::text('cuidado[]', 'Lubricación Sitios Apoyo', array( 'class' => 'form-control'))}}</div></div>
                </div>
                <div class="col-md-10">
                    <div class="col" style="text-align:center;">
                        HORARIO
                    </div>
                    <div class="col-md-6">
                        <div class="col" style="text-align:center;">
                            TURNO LARGO
                        </div>
                        <div class="col">
                            <div class="col-md-6 turno1">
                                <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div>
                            </div>

                            <div class="col-md-6 turno2">
                                <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div>
                            </div>
                        </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group"> {{Form::text('turnLargo1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno2"> <div class="form-group"> {{Form::text('turnLargo2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        
                        <div class="col">
                            <div class="col-md-4" style="text-align:center;">
                                10
                            </div>
                            <div class="col-md-8" style="text-align:center;">
                                <div class="col-md-6">
                                    14
                                </div>
                                <div class="col-md-6">
                                    18
                                </div>
                            </div>
                        </div>
                        <div class="col"> 

                            <div class="col-md-2"> 
                                <div class="form-group">{{ Form::select('turnoL1', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }} </div> 
                            </div>
                            <div class="col-md-2"> 
                                <div class="form-group">{{ Form::select('turnoL2', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }} </div>                                 
                            </div>
                            <div class="col-md-4"> 
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoL31', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoL32', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div>  
                                </div>
                            </div>  
                            <div class="col-md-4"> 
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoL41', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoL42', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div> 
                                </div>
                            </div> 
                        </div>

                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno1"> <div class="form-group">{{ Form::select('turnLargo1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno2"> <div class="form-group">{{ Form::select('turnLargo2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>

                        
                    </div>

                    <div class="col-md-6">
                        <div class="col"  style="text-align:center;">
                            TURNO NOCHE
                        </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group"> {{Form::text('turnNoche1[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div>  <div class="col-md-6 turno4"> <div class="form-group"> {{Form::text('turnNoche2[]', null, array( 'class' => 'datetimepicker3 form-control', 'placeholder' => 'HH:mm'))}} </div> </div> </div>

                        <div class="col">
                            <div class="col-md-4" style="text-align:center;">
                                22
                            </div>
                            <div class="col-md-8" style="text-align:center;">
                                <div class="col-md-6">
                                    02
                                </div>
                                <div class="col-md-6">
                                    05
                                </div>
                            </div>
                        </div>

                        <div class="col"> 
                            <div class="col-md-2"> 
                                <div class="form-group"> 
                                    {{ Form::select('turnoN1', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }} 
                                </div> 
                            </div>
                            <div class="col-md-2"> 
                                <div class="form-group"> {{ Form::select('turnoN2', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}  </div>                                 
                            </div>
                            <div class="col-md-4"> 
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoN31', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoN32', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div>  
                                </div>
                            </div>  
                            <div class="col-md-4"> 
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoN41', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }} </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">{{ Form::select('turnoN42', array('DLD' => 'DLD', 'DS' => 'DS', 'DLI' => 'DLI'), null, array( 'class' => 'form-control')) }}</div> 
                                </div>
                                
                            </div> 
                        </div>
                        

                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                        <div class="col"> <div class="col-md-6 turno3"> <div class="form-group">{{ Form::select('turnNoche1[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> <div class="col-md-6 turno4"> <div class="form-group">{{ Form::select('turnNoche2[]', array('si' => 'Si', 'no' => 'No'), 'no', array( 'class' => 'form-control')) }}</div> </div> </div>
                    </div>
                </div>
            </div>

        </div>


    </div>

</div>

<div class="row">
    <div class="col-md-12 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>VALORACIÓN EN ENFERMERÍA</h4>
            </div>

            <div class="panel-body" id="indicaciones">
                <div class="col-md-6">
                    <div class="col-md-12">
                        {{Form::label('valoracionTurnoLargo','TURNO LARGO')}}
                        <div class="col-md-12">
                            <div class="form-group"> {{Form::textarea('valoracionTurnoLargo', null, array('id' => 'valoracionTurnoLargo', 'class' => 'form-control', 'rows' => '6','style' => 'resize:none'))}} </div>
                        </div> 
                    </div>
                    
                    <div class="col-md-12">
                        {{Form::label('nombreEnfermeroTurnoLargo','Nombre Enfermera(o) Turno Largo')}}
                        <div class="col-md-12">
                            <div class="form-group"> {{Form::text('enfermeraTurnoLargo', null, array('id' => 'nombreEnfermeroTurnoLargo', 'class' => 'form-control'))}} </div>
                        </div>
                    </div>
                </div> 

                <div class="col-md-6">
                    <div class="col-md-12">
                        {{Form::label('valoracionTurnoNoche','TURNO NOCHE')}}
                        <div class="col-md-12">
                            <div class="form-group"> {{Form::textarea('valoracionTurnoNoche', null, array('id' => 'valoracionTurnoNoche', 'class' => 'form-control', 'rows' => '6','style' => 'resize:none'))}} </div>
                        </div>
                    </div>
                    

                    <div class="col-md-12">
                        {{Form::label('nombreEnfermeroTurnoNoche','Nombre Enfermera(o) Turno Noche')}}
                        <div class="col-md-12">
                            <div class="form-group"> {{Form::text('enfermeraTurnoNoche', null, array('id' => 'nombreEnfermeroTurnoNoche', 'class' => 'form-control'))}} </div>
                        </div>
                    </div>
                    
                        
                </div>
            </div>

        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>VALORACIÓN RIESGO DE CAIDAS EN PACIENTE HOSPITALIZADO</h4>
            </div>

            <div class="panel-body" id="indicaciones">

                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-top:3%;"> {{Form::label('', 'Edad >65 ó < 2 años(1)')}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::select('criterioEdad', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioEdad', 'class' => 'calcularRiesgoCaida form-control')) }} 
                        </div>                        
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-top:3%;"> {{Form::label('', 'Compromiso conciencia(2)')}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{ Form::select('criterioComprConciencia', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioComprConciencia', 'class' => 'calcularRiesgoCaida form-control')) }} </div>     
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-top:3%;"> {{Form::label('', 'Agitación psicomotora(2)')}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{ Form::select('criterioAgiPsicomotora', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioAgiPsicomotora', 'class' => 'calcularRiesgoCaida form-control')) }} </div>     
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-top:3%;"> {{Form::label('', 'Limitación sensorial(1)')}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{ Form::select('criterioLimSensorial', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioLimSensorial', 'class' => 'calcularRiesgoCaida form-control')) }} </div> 
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-top:3%;"> {{Form::label('', 'Limitación motora(1)')}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{ Form::select('criterioLimMotora', array('true' => 'Si', 'false' => 'No'), 'false', array('id' => 'criterioLimMotora', 'class' => 'calcularRiesgoCaida form-control')) }} </div> 
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-6" style="text-align:center;margin-top:3%;">
                        {{Form::label('totalEnf', 'Total:')}}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('total', 0, array('id' => 'totalEnfermeria', 'class' => 'form-control', 'disabled'))}} </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-12">
                        {{Form::label('actividades', 'Actividades según puntaje:')}}
                    </div>

                    <div class="col-md-12" >
                        <div class="col-md-6">{{Form::label('', 'Puntaje de 2 a 4')}} </div>
                        <div class="col-md-6">{{Form::label('', 'Cama con barandas')}} </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6">{{Form::label('', 'Puntaje de 5 a 7')}} </div>
                        <div class="col-md-6">{{Form::label('', 'Cama con barandas y contención')}} </div>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

    <div class="col-md-3 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>INTERCONSULTAS</h4>
            </div>

            <div class="panel-body" id="interconsultas">
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>  
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>
                <div class="col-md-12 interconsulta">
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta1[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> {{Form::text('interconsulta2[]', null, array( 'class' => 'form-control'))}} </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    
</div>


<div class="row">
    <div class="col-md-6 formulario">
        <div class="panel panel-default">

            <div class="panel-heading panel-info">
                <h4>CONTROL PROCEDIMIENTOS INVASIVOS</h4>
            </div>

            <div class="panel-body" id="procInvasivos">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N° días SNG')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('sng', 0, array('id' => 'sngEnfermeria', 'class' => 'form-control','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días CVC')}} </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días SNY')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('sny', 0, array('id' => 'sny', 'class' => 'form-control','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días CV Hd')}} </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días VVP')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('vpp[]', 0, array( 'class' => 'form-control vpp','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días')}} </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días VVP')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('vpp[]', 0, array( 'class' => 'form-control vpp','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días')}} </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días VVP')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('vpp[]', 0, array( 'class' => 'form-control vpp','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días')}} </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días CUP')}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> {{Form::number('cup', 0, array('id' => 'cup', 'class' => 'form-control','required'))}} </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">{{Form::label('', 'N°días')}} </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <input id="btnSolicitar" type="submit" name="" class="btn btn-primary" value="Ingresar Información">
    </div>    
</div>

{{ Form::close() }} 
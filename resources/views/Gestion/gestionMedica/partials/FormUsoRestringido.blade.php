<style>
    .espacio_cal{
        width: 12.333333%;
    }
    .espacio_cal2{
        width: 20.66666667%;
    }
    .espacio_cal3{
        width: 26%;
    }
    .espacio_div{
        margin-left: 15px;
    }
    .modal {
        overflow-y:auto;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading panel-info">
        <h4>Solicitud Tratamiento Antimicrobiano De Uso Restringido Con Autorización de PCIAAS</h4>
        
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-sm-10">
                    <label for="diagnosticor" class="control-label" title="diagnostico">Tipo de Tratamiento: </label>
                    <div class="col-md-12 form-group ">
                    {{Form::select('tipo_tratamiento', array('1'=>'Inicio tratamiento','2'=>'Continuación tratamiento','3'=>'Cambio tratamiento'), null, array('id' => 'tipo_tratamiento', 'class' => 'form-control'))}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-sm-10">
                    <label for="diagnosticor" class="control-label" title="diagnostico">Diagnóstico: </label>
                    <div class="col-md-12 form-group ">
                        {{ Form::hidden('id_diagnostico', '', array('id' => 'id_diagnostico')) }}
                        {{Form::textArea('diagnosticor', null, array('id' => 'diagnosticor', 'class' => 'form-control', 'rows' => 3,'disabled'))}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">  
                <div class="col-md-10">
                    <div class="col-md-12 form-group">
                        <div class="input-group">
                            <label class="radio-inline">{{Form::radio('terapia_empirica_especifica', "terapia_empirica", false, array('id'=>'terapia_empirica', 'required' => true))}}Terapia Empírica </label>
                            <label class="radio-inline">{{Form::radio('terapia_empirica_especifica', "terapia_especifica", false, array('id'=>'terapia_especifica', 'required' => true))}} Terapia Específica</label>
                        </div>
                    </div>
                <!-- <label> <input name="terapia_empirica" id="checkHabito" type="checkbox" value="empirica" /> Terapia Empírica (Por 48 horas, a la espera de resultado de cultivo)</label> -->
                </div> 
                <div class="col-sm-10 mostrarSitioInfeccion" hidden>
                    <label for="sitio_infeccion" class="control-label" title="sitio infeccion">Sitio Infección: </label>
                    <div class="col-md-12 form-group">
                        {{Form::textArea('sitio_infeccion', null, array('id' => 'sitio_infeccion', 'class' => 'form-control', 'rows' => 1))}}
                    </div>
                </div>
                <div class="col-sm-10 mostrarPatogeno" hidden>
                    <label for="patogeno" class="control-label" title="Patógeno">Patógeno: </label>
                    <div class="col-md-12 form-group">
                        {{Form::textArea('patogeno', null, array('id' => 'patogeno', 'class' => 'form-control', 'rows' => 1))}}
                    </div>
                </div>
            </div>    
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="col-sm-10">
                    <div class="col-md-3  pl-0 pr-0">
                        {{Form::label('', "Sospecha de IAAS:", array('class' => 'control-label'))}}
                    </div>
                    <div class="col-md-7 pl-0 pr-0 form-group">
                        <div class="input-group">
                            <label class="radio-inline">{{Form::radio('iaas', "no", false, array('id'=>'iaasF', 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('iaas', "si", false, array('id'=>'iaasT', 'required' => true))}}Sí</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mostrarCultivos" hidden>
            <div class="col-md-12">
                <div class="col-sm-12">
                    <div class="col-md-12 pl-0 pr-0">
                        <legend>Resultado de cultivos</legend>
                    </div>
                    <div class="col-md-10 pl-0">
                        <div class="col-md-3 espacio_cal3 pl-0">
                            {{Form::label('', "Fecha", array('class' => 'control-label' ))}}
                        </div>
                        <div class="col-md-4 pr-0">
                            {{Form::label('', "Agente identificado", array( 'class' => 'control-label'))}}
                        </div>
                        <div class="col-md-4 pl-0">
                            {{Form::label('', "Localización", array('class' => 'control-label' ))}}
                        </div>
                    </div>
                    <div class="cultivos">
                        <div class="col-md-10 pl-0 pr-0" id="moduloCultivos">
                        <input type="hidden" value="" name="id_cultivo[]" id="id_cultivo0">
                            <div class="col-md-3 pl-0 pr-0">
                                <div class="col-md-12 pr-0 form-group">
                                    {{Form::text('fechaCultivo[]', null, array( 'class' => 'form-control' /*, 'placeholder' => 'Seleccione fecha' */,'id'=>'fechaCultivo0', 'autocomplete' => 'off'))}}
                                </div>    
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-10 form-group">
                                    {{Form::select('antibioticoCultivo[]', App\Models\CaracteristicasAgente::pluck('nombre','id'), null, array( 'class' => 'form-control anti','id' => 'antibioticoCultivo0'/*, 'placeholder' => 'Seleccione'*/)) }}
                                </div>
                            </div>
                            <div class="col-md-4 form-group"> 
                              {{Form::text('locacionCultivo[]', null, array( 'class' => 'locacionCultivo form-control' /*, 'placeholder' => 'Ingrese localización' */,'id'=>'locacionCultivo0', 'maxlength' => 504))}}
                            </div>
                        </div>
                        <div class="col-md-2 pl-0 pr-0">
                            <button type="button" class="btn btn-primary agregarCultivos" onclick="agregarCultivos()" >+</button>
                        </div>
                        <div class="col-md-12 moduloCultivoscopia pl-0 pr-0" id="moduloCultivoscopia"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-12">
                <div class="col-sm-12">
                    <div class="col-md-12 pl-0 pr-0">
                        <legend id="titulo_tratamiento"></legend>
                    </div>
                    <div class="col-md-10 pl-0 pr-0">
                        <div class="col-md-4 pl-0 pr-0">
                            {{Form::label('', "Antimicrobiano", array( 'class' => 'control-label'))}}
                        </div>
                        <div class="col-md-1 espacio_cal pl-0 pr-0">
                            {{Form::label('', "Dosis", array('class' => 'control-label' ))}}
                        </div>
                        <div class="col-md-2 espacio_cal2 pl-0 pr-0">
                            {{Form::label('', "Posología", array('class' => 'control-label' ))}}
                        </div>
                        <div class="col-md-2 pl-0 pr-0">
                            {{Form::label('', "Duración", array( 'class' => 'control-label'))}}
                        </div>
                    </div>
                    <div class="antimicrobiano">
                        <div class="col-md-10 pl-0 pr-0" id="moduloAntimicrobiano">
                        <input type="hidden" value="" name="id_antimicrobiano[]" id="id_antimicrobiano0">
                            <div class="col-md-4 pl-0 pr-0">
                                <div class="col-md-12 form-group">
                                    {{Form::select('antimicrobiano[]', App\Models\Tratamiento_antimicrobiano::pluck('nombre','id'), null, array( 'class' => 'form-control','id' => 'antimicrobiano0'/*, 'placeholder' => 'Seleccione'*/)) }}
                                </div>    
                            </div>
                            <div class="col-md-2 pl-0 pr-0 form-group">
                                <div class="col-md-10">
                                {{Form::number('dosisAntimicrobiano[]', null, array( 'class' => 'dosisAntimicrobiano form-control' /*, 'placeholder' => 'Ingrese dosis' */,'id'=>'dosisAntimicrobiano0'))}}
                                </div>
                            </div>
                            <div class="col-md-3 pl-0 pr-0 form-group">
                                <div class="col-md-10">
                                {{Form::text('posologiantimicrobiano[]', null, array( 'class' => 'posologiantimicrobiano form-control' /*, 'placeholder' => 'Ingrese localización' */,'id'=>'posologiantimicrobiano0', 'maxlength' => 504))}}
                                </div>
                            </div>
                            <div class="col-md-2 pl-0 pr-0">
                                <div class="col-md-10 form-group">
                                {{Form::number('duracionAntimicrobiano[]', null, array( 'class' => 'duracionAntimicrobiano form-control'/*, 'placeholder' => 'Ingrese duración'*/,'id'=>'duracionAntimicrobiano0'))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 pl-0 pr-0">
                            <button type="button" class="btn btn-primary agregarAntimicrobiano" onclick="agregarAntimicrobiano()">+</button>
                        </div>
                        <div class="col-md-12 moduloAntimicrobianocopia pl-0 pr-0" id="moduloAntimicrobianocopia"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mostrarAntimicrobianoAnterior" hidden>
            <div class="col-md-12">
                <div class="col-sm-12">
                    <div class="col-md-12 pl-0 pr-0">
                        <legend>Tratamiento antimicrobiano actual</legend>
                        <!-- <label for="nutricion" class="control-label" title="Nutricion">Justificación de la solicitud</label> -->
                    </div>
                    <div class="col-md-10 pl-0 pr-0">
                        <div class="col-md-4 pl-0 pr-0">
                            {{Form::label('', "Antimicrobiano", array( 'class' => 'control-label'))}}
                        </div>
                        <div class="col-md-1 espacio_cal pl-0 pr-0">
                            {{Form::label('', "Dosis", array('class' => 'control-label' ))}}
                        </div>
                        <div class="col-md-2 espacio_cal2 pl-0 pr-0">
                            {{Form::label('', "Posología", array('class' => 'control-label' ))}}
                        </div>
                        <div class="col-md-2 pl-0 pr-0">
                            {{Form::label('', "Duración", array( 'class' => 'control-label'))}}
                        </div>
                    </div>
                    <div class="antimicrobianoActual">
                        <div class="col-md-10 pl-0 pr-0" id="moduloAntimicrobianoActual">
                        <input type="hidden" value="" name="id_antimicrobianoActual[]" id="id_antimicrobianoActual0">
                            <div class="col-md-4 pl-0 pr-0">
                                <div class="col-md-12 form-group">
                                 {{Form::select('antimicrobiano_actual[]', App\Models\Tratamiento_antimicrobiano::pluck('nombre','id'), null, array( 'class' => 'antimicrobiano_actual form-control','id' => 'antimicrobiano_actual0'/*, 'placeholder' => 'Seleccione'*/)) }}
                                </div>    
                            </div>
                            <div class="col-md-2 pl-0 pr-0 form-group">
                                <div class="col-md-10">
                                {{Form::number('dosisAntimicrobiano_actual[]', null, array( 'class' => 'dosisAntimicrobiano_actual form-control' /*, 'placeholder' => 'Ingrese dosis' */,'id'=>'dosisAntimicrobiano_actual0'))}}
                                </div>
                            </div>
                            <div class="col-md-3 pl-0 pr-0 form-group">
                                <div class="col-md-10">
                                {{Form::text('posologiantimicrobiano_actual[]', null, array( 'class' => 'posologiantimicrobiano_actual form-control' /*, 'placeholder' => 'Ingrese localización' */,'id'=>'posologiantimicrobiano_actual0', 'maxlength' => 504))}}
                                </div>
                            </div>
                            <div class="col-md-2 pl-0 pr-0">
                                <div class="col-md-10 form-group">
                                {{Form::number('duracionAntimicrobiano_actual[]', null, array( 'class' => 'duracionAntimicrobiano_actual form-control'/*, 'placeholder' => 'Ingrese duración'*/,'id'=>'duracionAntimicrobiano_actual0'))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 pl-0 pr-0">
                            <button type="button" class="btn btn-primary agregarAntimicrobiano_actual" onclick="agregarAntimicrobianoActual()">+</button>
                        </div>
                        <div class="col-md-12 moduloAntimicrobianocopia_actual pl-0 pr-0" id="moduloAntimicrobianocopia_actual"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="col-sm-12">
                    <div class="col-md-12 pl-0 pr-0">
                        <legend>Justificación de la solicitud</legend>
                        <!-- <label for="nutricion" class="control-label" title="Nutricion">Justificación de la solicitud</label> -->
                    </div>
                    <div class="col-md-12 pl-0 pr-0">
                        <div class="col-md-3 pl-0 pr-0">
                            <div class="col-md-9 pl-0">
                                <label for="diagnosticor" class="control-label" title="diagnostico">Temperatura: </label>    
                                <div class="col-md-12 form-group"> 
                                {{Form::number('justificacion_temperatura', null, array( 'class' => 'comentario form-control'/*, 'placeholder' => 'Ingrese temperadura'*/,'id'=>'justificacion_temperatura', 'step' => '0.1'))}} </div> 
                            </div>    
                        </div>
                        <div class="col-md-9  pl-0 pr-0">
                            <div class="col-md-9 pl-0 pr-0">
                                <label for="diagnosticor" class="control-label" title="diagnostico">Parametro infecciosos: </label>
                                <div class="col-md-12 form-group">   
                                    {{Form::textArea('justificacion_parametro', null, array('id' => 'justificacion_parametro', 'class' => 'form-control', 'rows' => 1))}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pl-0 pr-0">
                        <div class="col-md-10  pl-0 pr-0">
                            <label for="diagnosticor" class="control-label" title="diagnostico">Estado clinico: </label>
                            <div class="col-md-12 form-group">   
                                {{Form::textArea('justificacion_estado', null, array('id' => 'justificacion_estado', 'class' => 'form-control', 'rows' => 2))}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pl-0 pr-0">
                        <div class="col-md-10 pl-0 pr-0">
                            <label for="diagnosticor" class="control-label" title="diagnostico">Comentario: </label> 
                            <div class="col-md-12 form-group">  
                                {{Form::textArea('justificacion_comentario', null, array('id' => 'justificacion_comentario', 'class' => 'form-control', 'rows' => 2))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<input id="guardarUsoRestringido" type="submit" name="" class="btn btn-primary" value="Guardar">
<br><br>
<script>
      function eliminarFilaAntimicrobianocopia(position) {
            var myobj = document.getElementById("moduloAntimicrobiano"+position);
            if($("#id_antimicrobiano"+position).val() != '' && typeof $("#eliminados_antimicrobiano") !== "undefined"){
                $("#eliminados_antimicrobiano").val($("#eliminados_antimicrobiano").val()+','+$("#id_antimicrobiano"+position).val())
            }
            myobj.remove();
        }
     
        function eliminarFilaAntimicrobianocopiaActual(position) {
            var myobj = document.getElementById("moduloAntimicrobianoActual"+position);
            if($("#id_antimicrobianoActual"+position).val() != '' && typeof $("#eliminados_antimicrobiano_actual") !== "undefined"){
                $("#eliminados_antimicrobiano_actual").val($("#eliminados_antimicrobiano_actual").val()+','+$("#id_antimicrobianoActual"+position).val())
            }
            myobj.remove();
        }


      function eliminarFilaCultivos(position) {
            var myobj = document.getElementById("moduloCultivos"+position);
            if($("#id_cultivo"+position).val() != '' && typeof $("#eliminados_cultivos") !== "undefined"){
                $("#eliminados_cultivos").val($("#eliminados_cultivos").val()+','+$("#id_cultivo"+position).val())
            }

            myobj.remove();
        }

         
        var counterCultivos = 1;
    
    function agregarCultivos(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloCultivos");
        var cloneDiv = originalDiv.clone();    
        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloCultivos'+counterCultivos);
  
        $("[name='fechaCultivo[]']",cloneDiv).attr({'data-id':counterCultivos,'id':'fechaCultivo'+counterCultivos});          
        $("[name='fechaCultivo[]']",cloneDiv).val('');          

        $("[name='antibioticoCultivo[]']",cloneDiv).attr({'data-id':counterCultivos,'id':'antibioticoCultivo'+counterCultivos});    
        $("[name='antibioticoCultivo[]']",cloneDiv).val('2').change(); 
      
        $("[name='locacionCultivo[]']",cloneDiv).attr({'data-id':counterCultivos,'id':'locacionCultivo'+counterCultivos});    
        $("[name='locacionCultivo[]']",cloneDiv).val(''); 

        $("[name='id_cultivo[]']",cloneDiv).attr({'data-id':counterCultivos,'id':'id_cultivo'+counterCultivos});    
        $("[name='id_cultivo[]']",cloneDiv).val(''); 


        html ='<div class="col-md-1"><button class="btn btn-danger" onclick="eliminarFilaCultivos('+counterCultivos+')">-</button></div>';       

        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloCultivoscopia").append(cloneDiv);
        cloneDiv.append(html);

        $('#fechaCultivo'+counterCultivos).datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#usoRestringidoform').bootstrapValidator('revalidateField', $(this));
        });
        
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='fechaCultivo[]']"));
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='antibioticoCultivo[]']"));
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='locacionCultivo[]']"));

        //incrementa el contador
        counterCultivos++;      
	};  

    var counterAntimicrobiano  = 1;
    
    
    function agregarAntimicrobiano(){
     //toma el div original y lo clona
     var originalDiv = $("div#moduloAntimicrobiano");
     var cloneDiv = originalDiv.clone();    
     //cambiar datos de los divs clonados
     cloneDiv.attr('id','moduloAntimicrobiano'+counterAntimicrobiano);

     $("[name='antimicrobiano[]']",cloneDiv).attr({'data-id':counterAntimicrobiano,'id':'antimicrobiano'+counterAntimicrobiano});          
     $("[name='antimicrobiano[]']",cloneDiv).val('');          

     $("[name='dosisAntimicrobiano[]']",cloneDiv).attr({'data-id':counterAntimicrobiano,'id':'dosisAntimicrobiano'+counterAntimicrobiano});    
     $("[name='dosisAntimicrobiano[]']",cloneDiv).val(''); 
   
     $("[name='posologiantimicrobiano[]']",cloneDiv).attr({'data-id':counterAntimicrobiano,'id':'posologiantimicrobiano'+counterAntimicrobiano});    
     $("[name='posologiantimicrobiano[]']",cloneDiv).val(''); 

     $("[name='duracionAntimicrobiano[]']",cloneDiv).attr({'data-id':counterAntimicrobiano,'id':'duracionAntimicrobiano'+counterAntimicrobiano});    
     $("[name='duracionAntimicrobiano[]']",cloneDiv).val(''); 

     $("[name='id_antimicrobiano[]']",cloneDiv).attr({'data-id':counterAntimicrobiano,'id':'id_antimicrobiano'+counterAntimicrobiano});    
     $("[name='id_antimicrobiano[]']",cloneDiv).val(''); 

     html ='<div class="col-md-1"><button class="btn btn-danger" onclick="eliminarFilaAntimicrobianocopia('+counterAntimicrobiano+')">-</button></div>';       

     //agrega en el div los datos ya formatiados
     originalDiv.parent().find("#moduloAntimicrobianocopia").append(cloneDiv);
     cloneDiv.append(html);
     
     $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='antimicrobiano[]']"));
     $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='dosisAntimicrobiano[]']"));
     $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='posologiantimicrobiano[]']"));
     $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='duracionAntimicrobiano[]']"));

     

     //incrementa el contador
     counterAntimicrobiano++;      
 };

    var counterAntimicrobiano_actual = 1;
    
    function agregarAntimicrobianoActual(){
        //toma el div original y lo clona
        var originalDiv = $("div#moduloAntimicrobianoActual");
        var cloneDiv = originalDiv.clone();    
        //cambiar datos de los divs clonados
        cloneDiv.attr('id','moduloAntimicrobianoActual'+counterAntimicrobiano_actual);

        $("[name='antimicrobiano_actual[]']",cloneDiv).attr({'data-id':counterAntimicrobiano_actual,'id':'antimicrobiano_actual'+counterAntimicrobiano_actual});          
        $("[name='antimicrobiano_actual[]']",cloneDiv).val('');          

        $("[name='dosisAntimicrobiano_actual[]']",cloneDiv).attr({'data-id':counterAntimicrobiano_actual,'id':'dosisAntimicrobiano_actual'+counterAntimicrobiano_actual});    
        $("[name='dosisAntimicrobiano_actual[]']",cloneDiv).val(''); 
    
        $("[name='posologiantimicrobiano_actual[]']",cloneDiv).attr({'data-id':counterAntimicrobiano_actual,'id':'posologiantimicrobiano_actual'+counterAntimicrobiano_actual});    
        $("[name='posologiantimicrobiano_actual[]']",cloneDiv).val(''); 

        $("[name='duracionAntimicrobiano_actual[]']",cloneDiv).attr({'data-id':counterAntimicrobiano_actual,'id':'duracionAntimicrobiano_actual'+counterAntimicrobiano_actual});    
        $("[name='duracionAntimicrobiano_actual[]']",cloneDiv).val(''); 

        $("[name='id_antimicrobianoActual[]']",cloneDiv).attr({'data-id':counterAntimicrobiano_actual,'id':'id_antimicrobianoActual'+counterAntimicrobiano_actual});    
        $("[name='id_antimicrobianoActual[]']",cloneDiv).val(''); 

        html ='<div class="col-md-1"><button class="btn btn-danger" onclick="eliminarFilaAntimicrobianocopiaActual('+counterAntimicrobiano_actual+')">-</button></div>';       

        //agrega en el div los datos ya formatiados
        originalDiv.parent().find("#moduloAntimicrobianocopia_actual").append(cloneDiv);
        cloneDiv.append(html);
        
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='antimicrobiano_actual[]']"));
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='dosisAntimicrobiano_actual[]']"));
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='posologiantimicrobiano_actual[]']"));
        $('#usoRestringidoform').bootstrapValidator('addField', cloneDiv.find("[name='duracionAntimicrobiano_actual[]']"));

        //incrementa el contador
        counterAntimicrobiano_actual++;      
    };

    $(document).ready( function() {

      

        $('#tipo_tratamiento').change(function() {
            if( $(this).val() == 1){
                $('.mostrarAntimicrobianoAnterior').prop('hidden',true);
                $("#titulo_tratamiento").text("Tratamiento antimicrobiano actual");
            }else if( $(this).val() == 2 || $(this).val() == 3){
                $("#titulo_tratamiento").text("Tratamiento antimicrobiano anterior");
                $("#editarUsoRestringido").val("");
                $('.mostrarAntimicrobianoAnterior').removeAttr( "hidden" );
            }
        });
        $('input[name=terapia_empirica_especifica]').on('change', function() {
              if($(this).val() == "terapia_empirica"){
                $('.mostrarSitioInfeccion').removeAttr( "hidden" );
                $('.mostrarCultivos').prop('hidden',true);
                $('#patogeno').val('');
                $('.mostrarPatogeno').prop('hidden',true);
                counterCultivos = 1;
               
                $('#fechaCultivo0').val('');
                $('#locacionCultivo0').val('');
                $("#antibioticoCultivo0").val("2").change();
                
            }else if($(this).val() == "terapia_especifica"){
                $('.mostrarPatogeno').removeAttr( "hidden" );
                $('#sitio_infeccion').val('');
                $('.mostrarSitioInfeccion').prop('hidden',true);
                $('.mostrarCultivos').removeAttr( "hidden" );                
                $( "#moduloCultivoscopia" ).empty();
                counterCultivos = 1;

                $('#fechaCultivo0').val('');
                $('#locacionCultivo0').val('');
                $("#antibioticoCultivo0").val("2").change();
              }
        });


        $('#fechaCultivo0').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            locale: 'es'
        }).on('dp.change', function (e) {
            $('#usoRestringidoform').bootstrapValidator('revalidateField', $(this));
        });





  
    });  
</script>
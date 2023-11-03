<script>

//    function activarValidacionesProtesis(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'protesis_ubicacion');
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'detalleDental');
//     }

//    function activarValidacionesBrazalete(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'ubicacionbrazalete');
//     }

//    function activarValidacionesAuditiva(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'audifonos');
//     }
//    function activarValidacionesAudifonos(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'audi_ubicacion');
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'detalleaudi');
//     }

//    function activarValidacionesVisual(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'Lentes');
//     }
//    function activarValidacionesLentes(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'l_ubi');
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'detallelentes');
//     }

//    function activarValidacionesLesiones(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'les_tipo');
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'les_ubi');
//     }
//    function activarValidacionesOtras(){
//         $("#IESegmentario").bootstrapValidator('revalidateField', 'les_desc');
//     }

 

    $(document).ready(function() { 

        $( "#iEnfermeria" ).click(function() {
            tabIE = $("#tabsIngresoEnfermeria div.active").attr("id");

            if(tabIE == "3h"){
                IngresarMostrarSegmentario();
            }
            
        });

        $("#hS").click(function() {
            IngresarMostrarSegmentario();
        });

        $("#IESegmentario").bootstrapValidator({
           excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {  
                'dental': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'protesis_ubicacion': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'detalleDental': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar un detalle'
                        }
                    }
                },
                'brazalete': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'ubicacionbrazalete': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una ubicación'
                        }
                    }
                },
                'dauditiva': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'audifonos': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'audi_ubicacion': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una ubicación'
                        }
                    }
                },
                'detalleaudi': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el detalle'
                        }
                    }
                },
                'dvisual': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'Lentes': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'l_ubi': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una ubicación'
                        }
                    }
                },
                'detallelentes': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar el detalle'
                        }
                    }
                },
                'plesiones': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir una opción'
                        }
                    }
                },
                'les_tipo': {
                    validators:{
                        notEmpty: {
                            message: 'Debe elegir un tipo'
                        }
                    }
                },
                'les_desc': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una descripción'
                        }
                    }
                },
                'les_ubi': {
                    validators:{
                        notEmpty: {
                            message: 'Debe ingresar una ubicación'
                        }
                    }
                },
            }
        }).on('status.field.bv', function(e, data) {
            //data.bv.disableSubmitButtons(true);
        }).on("success.form.bv", function(evt, data){
            $("#btGuardarSegmentario").prop("disabled", true);
            evt.preventDefault(evt);
            var $form = $(evt.target);
            bootbox.confirm({				
                message: "<h4>¿Está seguro de agregar esta información?</h4>",				
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
                            url: "{{URL::to('/gestionEnfermeria')}}/agregarIESegmentario",
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:  $form .serialize(),
                            dataType: "json",
                            type: "post",
                            success: function(data){
                                $("#btGuardarSegmentario").prop("disabled", false);
                                
                                if (data.exito) {
                                   swalExito.fire({
                                    title: 'Exito!',
                                    text: data.exito,
                                    // didOpen: function() {
                                    //     setTimeout(function() {
                                    //         location . reload();
                                    //     }, 2000)
                                    // },
                                    });
                                    IngresarMostrarSegmentario();
                                }

                                if (data.error) {
                                    swalError.fire({
                                    title: 'Error',
                                    text:data.error
                                    });
                                    IngresarMostrarSegmentario();
                                }
                            },
                            error: function(error){
                                $("#btGuardarSegmentario").prop("disabled", false);
                                console.log(error);
                                IngresarMostrarSegmentario();
                            }
                        });				
                    }else{
                        $("#btGuardarSegmentario").prop("disabled", false);
                    }				
                }
            }); 
        });


        function activarValidacionesTipo1(activador){
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador, true);
            $("#IESegmentario").bootstrapValidator('revalidateField', activador);
        }

        function activarValidacionesTipo2(activador1,activador2){
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador1, true);
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador2, true);
            $("#IESegmentario").bootstrapValidator('revalidateField', activador1);
            $("#IESegmentario").bootstrapValidator('revalidateField', activador2);
        }
    
        function desactivarValidacionesTipo2(activador1,activador2){
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador1, false);
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador2, false);
        }

        function desactivarValidacionesTipo1(activador){
            $('#IESegmentario').bootstrapValidator('enableFieldValidators', activador, true);
        }

        var ocultarTodo=function(){
            var dental=$("input[name='dental']:checked").val();

            var brazalete =$("input[name='brazalete']:checked").val();

            var audicion =$("input[name='dauditiva']:checked").val();
            var audifonos =$("input[name='audifonos']:checked").val();

            var visual =$("input[name='dvisual']:checked").val();
            var lentes =$("input[name='lentes']:checked").val();
            
            
            var lesiones =$("input[name='plesiones']:checked").val();
            var tipo_lesiones =$( "select[name='les_tipo'] option:selected" ).val();


            if(dental == "si")$(".dental").show();
            else $(".dental").hide("");

            if(brazalete == "si")$(".brazalete").show();
            else $(".brazalete").hide("");

            if(audicion == "si")$(".audicion").show();
            else $(".audicion").hide("");
            if(audifonos == "si")$(".audifonos").show();
            else $(".audifonos").hide("");

            
            if(visual == "si")$(".visual").show();
            else $(".visual").hide("");
            if(visual == "si")$(".lentes").show();
            else $(".lentes").hide("");
        
            if(lesiones == "si"){
                $(".lesiones").show();
            }
            else{
                $(".lesiones").hide("");
                $(".les_desc").hide("");
            } 
            if(tipo_lesiones == "Otras")$(".les_desc").show();
            else $(".les_desc").hide("");
        }

        var limpiarTodo=function(){
            $(".dental").hide();
            $("#detalleDental").val('');
            $("input[name='dental']").prop( "checked", false );
    
            desactivarValidacionesTipo2('protesis_ubicacion','detalleDental');
            $("select[name='protesis_ubicacion']").val("").trigger("change");

            $("input[name='brazalete']").prop( "checked", false );
            $(".brazalete").hide();
            $("#ubicacionbrazalete").val('');
            desactivarValidacionesTipo1('ubicacionbrazalete');

            $(".audicion").hide();
            $(".audifonos").hide();
            $("input[name='dauditiva']").prop( "checked", false );
            $("input[name='audifonos']").prop( "checked", false );
            $("input[name='detalleaudi']").val('');
            desactivarValidacionesTipo1('audifonos');
            desactivarValidacionesTipo2('audi_ubicacion','detalleaudi');
            $("select[name='audi_ubicacion']").val("").trigger("change");


            $(".visual").hide();
            $("input[name='dvisual']").prop( "checked", false );
            $("input[name='Lentes']").prop( "checked", false );
            $(".lentes").hide();
            $("select[name='l_ubi']").val("").trigger("change");
            desactivarValidacionesTipo1('Lentes');
            desactivarValidacionesTipo2('l_ubi','detallelentes');
            
            $(".lesiones").hide();
            $(".les_desc").hide();
            $("input[name='plesiones']").prop( "checked", false );
            $("input[name='les_desc']").val('');
            $("input[name='les_ubi']").val('');
            $("select[name='les_tipo']").val("").trigger("change");
            desactivarValidacionesTipo2('les_tipo','les_ubi');
            desactivarValidacionesTipo1('les_desc');

        }

        function IngresarMostrarSegmentario(){
            mostrarOcultarExamenObstetrico();
            var caso = {{$caso}};
            $.ajax({ 
                url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosSegmentario",
                data: { 
                    caso : caso
                }, 
                headers: {					         
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                },
                type: "post", 
                dataType: "json", 
                success: function (data) { 
                    if(data.length > 0){
                        limpiarTodo();
                        $("#id_formulario_ingreso_enfermeria_segmentario").val(data[0].id);
                        //protesis
                        if(data[0].protesis_dental == true){
                            $("input[name='dental'][value='si']").prop('checked', true);
                            $(".dental").show();
                            $("select[name='protesis_ubicacion']").val(data[0].ubicacion_protesis_dental);
                            $("#detalledental").show();
                            $("#detalleDental").val(data[0].detalle_protesis_dental);
                        }else if(data[0].protesis_dental == false){
                            $("input[name='dental'][value='no']").prop('checked', true);
                        }
                        //brazalete
                        if(data[0].brazalete == true){
                            $("input[name='brazalete'][value='si']").prop('checked', true);
                            $(".brazalete").show();
                            $("#ubicacionbrazalete").val(data[0].ubicacion_brazalete);
                        }else if(data[0].brazalete == false){
                            $("input[name='brazalete'][value='no']").prop('checked', true);
                        }
                        //discapacidad auditiva
                        if(data[0].discapacidad_auditiva == true){
                            $("input[name='dauditiva'][value='si']").prop('checked', true);
                            $(".audicion").show();
                            
                            if(data[0].audifonos_discapacidad_auditiva == true){
                                $("input[name='audifonos'][value='si']").prop('checked', true);
                                $(".audifonos").show();
                                $("select[name='audi_ubicacion']").val(data[0].ubicacion_discapacidad_auditiva);
                                $("input[name='detalleaudi']").val(data[0].detalle_discapacidad_auditiva);
                            }else if(data[0].audifonos_discapacidad_auditiva == false ){
                                $("input[name='audifonos'][value='no']").prop('checked', true);
                            }

                        }else if(data[0].discapacidad_auditiva == false){
                            $("input[name='dauditiva'][value='no']").prop('checked', true);
                        }
                        //discapacidad visual
                        if(data[0].discapacidad_visual == true){
                            $("input[name='dvisual'][value='si']").prop('checked', true);
                            $(".visual").show();
                            
                            if(data[0].lentes_discapacidad_visual == true){
                                $("input[name='Lentes'][value='si']").prop('checked', true);
                                $(".lentes").show();
                                $("select[name='l_ubi']").val(data[0].ubicacion_discapacidad_visua);
                                $("input[name='detallelentes']").val(data[0].detalle_discapacidad_visua);
                            }else if(data[0].lentes_discapacidad_visual == false ){
                                $("input[name='Lentes'][value='no']").prop('checked', true);
                            }

                        }else if(data[0].discapacidad_visual == false){
                            $("input[name='dvisual'][value='no']").prop('checked', true);
                        }
                        //presencia lesiones
                        if(data[0].presencia_lesiones == true){
                            $("input[name='plesiones'][value='si']").prop('checked', true);
                            $(".lesiones").show();
                            $("select[name='les_tipo']").val(data[0].tipo_presencia_lesiones);
                                $("input[name='les_ubi']").val(data[0].ubicacion_presencia_lesiones);
                            
                            if(data[0].tipo_presencia_lesiones == "Otras"){
                                $(".les_desc").show();
                                $("input[name='les_desc']").val(data[0].descripcion_presencia_lesiones);
                                
                            }

                        }else if(data[0].presencia_lesiones == false){
                            $("input[name='plesiones'][value='no']").prop('checked', true);
                        }


                        $("#cabeza").val(data[0].cabeza);
                        $("#dental").val(data[0].protesis_dental);

                        $("#cuello").val(data[0].cuello);
                        $("#torax").val(data[0].torax);
                        $("#abdomen").val(data[0].abdomen);
                        $("#superiores").val(data[0].extremidades_superiores);
                        $("#inferiores").val(data[0].extremidades_inferiores);
                        $("#columnaDorso").val(data[0].columna_torso);
                        $("#genitales").val(data[0].genitales);
                        $("#piel").val(data[0].piel);

                        var ug = "{{$sub_categoria}}";
                        if(ug == 2){
                            $("#altura_uterina").val(data[0].altura_uterina);
                            $("#tacto_vaginal").val(data[0].tacto_vaginal);
                            $("#membranas").val(data[0].membranas);
                            $("#liquido_anmiotico").val(data[0].liquido_anmiotico);
                            $("#amnioscopia").val(data[0].amnioscopia);
                            $("#amnioscentesis").val(data[0].amnioscentesis);
                            $("#presentacion").val(data[0].presentacion);
                            $("#contracciones").val(data[0].contracciones);
                            $("#lfc").val(data[0].lfc);
                            $("#vagina").val(data[0].vagina);
                            $("#perine").val(data[0].perine);
                            $("#tacto_vaginal_eg").val(data[0].tacto_vaginal_eg);
                        }
                    }else{

                        limpiarTodo();
                    }
                }, 
                error: function (error) {
                    console.log(error);
                } 
            });
        }

        function mostrarOcultarExamenObstetrico(){
            var ug = "{{$sub_categoria}}";
            if(ug == 2){
                $("#ginecologica").show();
                $("#v_ginecologica").val(ug);
            }else{
                $("#ginecologica").hide();
                $("#v_ginecologica").val('');
            }
        }

        $("input[name='dental']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".dental").show();
                activarValidacionesTipo2('protesis_ubicacion','detalleDental');
            }else{ 
                $(".dental").hide("");
                $("#detalleDental").val('');
                desactivarValidacionesTipo2('protesis_ubicacion','detalleDental');
                $("select[name='protesis_ubicacion']").val("").trigger("change");
            }
           
        });

        $("input[name='brazalete']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".brazalete").show();
                activarValidacionesTipo1('ubicacionbrazalete');
            }else if(value == "no"){ 
                $(".brazalete").hide("");
                $("#ubicacionbrazalete").val('');
                desactivarValidacionesTipo1('ubicacionbrazalete');
            }
        });

        $("input[name='dauditiva']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".audicion").show();
                activarValidacionesTipo1('audifonos');
            }else{ 
                $(".audicion").hide("");
                $(".audifonos").hide("");
                $("input[name='audifonos']").prop( "checked", false );
                $("input[name='detalleaudi']").val('');
                desactivarValidacionesTipo1('audifonos');
                desactivarValidacionesTipo2('audi_ubicacion','detalleaudi');
                $("select[name='audi_ubicacion']").val("").trigger("change");
            }
        });
       
        $("input[name='audifonos']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".audifonos").show();
                activarValidacionesTipo2('audi_ubicacion','detalleaudi');
            }else{ 
                $(".audifonos").hide("");
                desactivarValidacionesTipo2('audi_ubicacion','detalleaudi');
                $("input[name='detalleaudi']").val('');
                $("select[name='audi_ubicacion']").val("").trigger("change");
                // $("#ubicacionbrazalete").val('');
            }
        });

        $("input[name='dvisual']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".visual").show();
                activarValidacionesTipo1('Lentes');
            }else{ 
                $(".visual").hide("");
                $("input[name='Lentes']").prop( "checked", false );
                $(".lentes").hide("");
                $("select[name='l_ubi']").val("").trigger("change");
                desactivarValidacionesTipo1('Lentes');
                desactivarValidacionesTipo2('l_ubi','detallelentes');
            }
        });

        $("input[name='Lentes']").on("change", function(){
            var value=$(this).val();
            if(value == "si"){
                $(".lentes").show();
                activarValidacionesTipo2('l_ubi','detallelentes');
            }else{ 
                $("select[name='l_ubi']").val("").trigger("change");
                $("input[name='detallelentes']").val('');
                $(".lentes").hide("");
                desactivarValidacionesTipo2('l_ubi','detallelentes');
            }
        });

        $("input[name='plesiones']").on("change", function(){
            var value=$(this).val();
        
            if(value == "si"){
                $(".lesiones").show();
                activarValidacionesTipo2('les_tipo','les_ubi');
            }else{ 
                $(".lesiones").hide("");
                $(".les_desc").hide("");
                $("input[name='les_desc']").val('');
                $("input[name='les_ubi']").val('');
                $("select[name='les_tipo']").val("").trigger("change");
                desactivarValidacionesTipo2('les_tipo','les_ubi');
                desactivarValidacionesTipo1('les_desc');
            }
        });

        $("select[name='les_tipo']").on("change", function(){
            var value=$(this).val();
            if(value == "Otras"){
                $(".les_desc").show();
                activarValidacionesTipo1('les_desc');
                
            }else{ 
                $(".les_desc").hide("");
                $("input[name='les_desc']").val('');
                desactivarValidacionesTipo1('les_desc');
            }
        });

        ocultarTodo();
    }); 
</script>

<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }
    @media (min-width: 992px){
    .ofset-1 {
        margin-left: 2.333333% !important;
    }
    }

</style>

{{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'IESegmentario')) }}
{{ Form::hidden('idCaso', $caso, array('class' => 'idCasoEnfermeria')) }}
 <div class="formulario">
    <input type="hidden" value="" name="id_formulario_ingreso_enfermeria" id="id_formulario_ingreso_enfermeria_segmentario">
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>III. Examen Físico Segmentario</h4>
        </div>
        <div class="panel-body"> 
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group"> 
                        {{Form::label('', "Cabeza", array( ))}}
                        {{Form::text('cabeza', null, array('id' => 'cabeza', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1"> 
                    <div class="form-group"> 
                        {{Form::label('', "Genitales", array( ))}} 
                        {{Form::text('genitales', null, array('id' => 'genitales', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Piel", array( ))}} 
                        {{Form::text('piel', null, array('id' => 'piel', 'class' => 'form-control'))}}   
                    </div> 
                </div>
               
            </div>
            <div class="col-md-12">
                <div class="col-md-3"> 
                    <div class="form-group"> 
                        {{Form::label('', "Cuello", array( ))}} 
                        {{Form::text('cuello', null, array('id' => 'cuello', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Torax", array( ))}} 
                        {{Form::text('torax', null, array('id' => 'torax', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Abdomen", array( ))}} 
                        {{Form::text('abdomen', null, array('id' => 'abdomen', 'class' => 'form-control'))}} 
                    </div> 
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3"> 
                    <div class="form-group"> 
                        {{Form::label('', "Extremidades superiores", array( ))}} 
                        {{Form::text('superiores', null, array('id' => 'superiores', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Extremidades inferiores", array( ))}} 
                        {{Form::text('inferiores', null, array('id' => 'inferiores', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Columna y Dorso", array( ))}} 
                        {{Form::text('columnaDorso', null, array('id' => 'columnaDorso', 'class' => 'form-control'))}} 
                    </div> 
                </div>
            </div>
            <br>
            <div class="col-md-12">
            <div class="col-md-2 calInput">
                    <div class="form-group"> 
                        {{Form::label('', "Protesis dental:", array( ))}} <br>
                        <label class="radio-inline">{{Form::radio('dental', "no", false, array( 'required' => true))}}No</label>
                        <label class="radio-inline">{{Form::radio('dental', "si", false, array('required' => true))}}Sí</label>
                    </div> 
                </div>
                <div class="col-md-2 dental"> 
                                <div class="form-group">
                                    {{Form::label('', "Ubicación", array( ))}} <br>
                                    {{Form::select('protesis_ubicacion', [ 'Superior'=> 'Superior', 'Inferior' => 'Inferior', 'Ambos' => 'Ambos'],null ,array('class' => 'form-control', 'placeholder' => 'Seleccione'))}}
                                </div>
                </div>
                <div class="col-md-3 ofset-1 dental">
                    <div class="form-group"> 
                        {{Form::label('', "Detalle protesis dental", array( ))}} 
                        {{Form::text('detalleDental', null, array('id' => 'detalleDental', 'class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-1 calInput ofset-1">
                    <div class="form-group"> 
                        {{Form::label('', "Brazalete:", array( ))}} <br>
                        <label class="radio-inline">{{Form::radio('brazalete', "no", false, array( 'required' => true))}}No</label>
                        <label class="radio-inline">{{Form::radio('brazalete', "si", false, array('required' => true))}}Sí</label>
                    </div> 
                </div>
                <div class="col-md-3 brazalete">
                    <div class="form-group"> 
                        {{Form::label('', "Ubicación", array( ))}} 
                        {{Form::text('ubicacionbrazalete', null, array('id' => 'ubicacionbrazalete', 'class' => 'form-control'))}}
                    </div> 
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                        <div class="form-group"> 
                            {{Form::label('', "Discapacidad auditiva:", array( ))}} <br>
                            <label class="radio-inline">{{Form::radio('dauditiva', "no", false, array( 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('dauditiva', "si", false, array('required' => true))}}Sí</label>
                        </div> 
                </div>
                <div class="col-md-1 calInput audicion">
                        <div class="form-group" id=""> 
                            {{Form::label('', "Audifonos:", array( ))}} <br>
                            <label class="radio-inline">{{Form::radio('audifonos', "no", false, array( 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('audifonos', "si", false, array('required' => true))}}Sí</label>
                        </div> 
                </div>
                <div class="col-md-2 audifonos"> 
                                <div class="form-group">
                                    {{Form::label('', "Ubicación", array( ))}} <br>
                                    {{Form::select('audi_ubicacion', [ 'Derecho'=> 'Derecho', 'Izquierdo' => 'Izquierdo', 'Ambos' => 'Ambos'],null ,array('class' => 'form-control', 'placeholder' => 'Seleccione'))}}
                                </div>
                </div>
                <div class="col-md-3 ofset-1 audifonos">
                    <div class="form-group"> 
                        {{Form::label('', "Detalle audífonos", array( ))}} 
                        {{Form::text('detalleaudi', null, array('class' => 'form-control'))}}
                    </div> 
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                        <div class="form-group"> 
                            {{Form::label('', "Discapacidad visual:", array( ))}} <br>
                            <label class="radio-inline">{{Form::radio('dvisual', "no", false, array( 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('dvisual', "si", false, array('required' => true))}}Sí</label>
                        </div> 
                </div>
                <div class="col-md-1 calInput visual">
                        <div class="form-group" id=""> 
                            {{Form::label('', "Lentes:", array( ))}} <br>
                            <label class="radio-inline">{{Form::radio('Lentes', "no", false, array( 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('Lentes', "si", false, array('required' => true))}}Sí</label>
                        </div> 
                </div>
                <div class="col-md-2 lentes"> 
                    <div class="form-group">
                        {{Form::label('', "Ubicación", array( ))}} <br>
                        {{Form::select('l_ubi', [ 'Derecho'=> 'Derecho', 'Izquierdo' => 'Izquierdo', 'Ambos' => 'Ambos'],null ,array('class' => 'form-control', 'placeholder' => 'Seleccione'))}}
                    </div>
                </div>
                <div class="col-md-3 ofset-1 lentes">
                    <div class="form-group"> 
                        {{Form::label('', "Detalle lentes", array( ))}} 
                        {{Form::text('detallelentes', null, array('class' => 'form-control'))}}
                    </div> 
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                        <div class="form-group"> 
                            {{Form::label('', "Presencia de lesiones:", array( ))}} <br>
                            <label class="radio-inline">{{Form::radio('plesiones', "no", false, array( 'required' => true))}}No</label>
                            <label class="radio-inline">{{Form::radio('plesiones', "si", false, array('required' => true))}}Sí</label>
                        </div> 
                </div>
                <div class="col-md-2 lesiones"> 
                    <div class="form-group">
                        {{Form::label('', "Tipo", array( ))}} <br>
                        {{Form::select('les_tipo', [ 'Quirúrgica'=> 'Quirúrgica', 'Traumática' => 'Traumática', 'UPP' => 'UPP', 'Flebitis' => 'Flebitis', 'Otras' => 'Otras'],null ,array('class' => 'form-control', 'placeholder' => 'Seleccione'))}}
                    </div>
                </div>
                <div class="col-md-3 ofset-1 les_desc" >
                    <div class="form-group"> 
                        {{Form::label('', "Descripción", array( ))}} 
                        {{Form::text('les_desc', null, array('class' => 'form-control'))}}
                    </div> 
                </div>
                <div class="col-md-3 ofset-1 lesiones">
                    <div class="form-group"> 
                        {{Form::label('', "Ubicación", array( ))}} 
                        {{Form::text('les_ubi', null, array('class' => 'form-control'))}}
                    </div> 
                </div>
            </div>
            <div id="ginecologica" hidden>
                <input type="hidden" value="" name="v_ginecologica" id="v_ginecologica">
                <div class="col-md-12">
                    <legend>Examen obstétrico</legend>
                    <div class="col-md-3">
                        <div class="form-group"> 
                            {{Form::label('', "Altura uterina", array( ))}}
                            {{Form::text('altura_uterina', null, array('id' => 'altura_uterina', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1"> 
                        <div class="form-group"> 
                            {{Form::label('', "Tacto vaginal", array( ))}} 
                            {{Form::text('tacto_vaginal', null, array('id' => 'tacto_vaginal', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Membranas", array( ))}} 
                            {{Form::text('membranas', null, array('id' => 'membranas', 'class' => 'form-control'))}}   
                        </div> 
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3"> 
                        <div class="form-group"> 
                            {{Form::label('', "Liquido amniótico", array( ))}} 
                            {{Form::text('liquido_anmiotico', null, array('id' => 'liquido_anmiotico', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Amnioscopia", array( ))}} 
                            {{Form::text('amnioscopia', null, array('id' => 'amnioscopia', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Amniocentesis", array( ))}} 
                            {{Form::text('amnioscentesis', null, array('id' => 'amnioscentesis', 'class' => 'form-control'))}} 
                        </div> 
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <div class="col-md-3"> 
                        <div class="form-group"> 
                            {{Form::label('', "Presentación", array( ))}} 
                            {{Form::text('presentacion', null, array('id' => 'presentacion', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Contracciones", array( ))}} 
                            {{Form::text('contracciones', null, array('id' => 'contracciones', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "LCF (Latidos cardio fetales)", array( ))}} 
                            {{Form::text('lfc', null, array('id' => 'lfc', 'class' => 'form-control'))}} 
                        </div> 
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <legend>Examen ginecológico</legend>
                    <div class="col-md-3"> 
                        <div class="form-group"> 
                            {{Form::label('', "Vagina", array( ))}} 
                            {{Form::text('vagina', null, array('id' => 'vagina', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Periné", array( ))}} 
                            {{Form::text('perine', null, array('id' => 'perine', 'class' => 'form-control'))}}
                        </div> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group"> 
                            {{Form::label('', "Tacto vaginal", array( ))}} 
                            {{Form::text('tacto_vaginal_eg', null, array('id' => 'tacto_vaginal_eg', 'class' => 'form-control'))}} 
                        </div> 
                    </div>
                </div>
            </div>
            <br>
            <br>
              <div class="col-md-2"> 
                <button type="submit" class="btn btn-primary" id="btGuardarSegmentario">Guardar</button>
            </div>
        </div>
    </div>
</div>   
{{ Form::close() }}     
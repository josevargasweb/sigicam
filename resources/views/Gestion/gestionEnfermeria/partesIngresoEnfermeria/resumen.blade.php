<script>
    // var caso = {{$caso}};
    function mostrarIngresoEnfermeria(){
        tmostrarOcultarExamenObstetrico();
        var caso = {{$caso}};
        $.ajax({
            url: "{{ URL::to('/gestionEnfermeria')}}/existenHojaIngresoEnfermeria",
            data: {
                caso : caso
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            dataType: "json",
            success: function (data) {
                console.log(data);
                $("#fecha_ingreso_enfermeria").text(data.fecha_ingreso_enfermeria);

                if(data.anamnesis){
                    console.log(data.anamnesis);
                        $('.tacom-sin-info').prop('hidden',true);
                    $('.tacom').css('display','inline-block');
                        $('.tdeis-sin-info').prop('hidden',true);
                    $('.tdeis').css('display','inline-block');
                    $("#id_formulario_ingreso_enfermeria_anamnesis").val(data.anamnesis.id);
                    tmordibos = (data.anamnesis.anamnesis_ant_morbidos) ? data.anamnesis.anamnesis_ant_morbidos : 'No Especificado';
                    $("#tmorbidos").html(tmordibos);

                    tacom = data.anamnesis.acom;
                    if(tacom == true){
                        $("input[name=tacom][value='si']").prop('checked', true);
                        $("#tAcompañamiento").show();
                        $("#tFamiliarAcompanante").text(data.anamnesis.acompanante);
                        $("#tVinculoAcompanante").text(data.anamnesis.vinculo_acompanante);
                        $("#tTelefonoAcompanante").text(data.anamnesis.telefono_acompanante);
                    }else{
                        $("input[name=tacom][value='no']").prop('checked', true);
                        $("#tAcompañamiento").hide();
                    }

                    tdeis = data.anamnesis.deis;

                    if(tdeis == true){
                        $("input[name=tdeis][value='si']").prop('checked', true);
                    }else{
                        $("input[name=tdeis][value='no']").prop('checked', true);
                    }

                    tantecedentesQ = (data.anamnesis.anamnesis_ant_quirurgicos) ? data.anamnesis.anamnesis_ant_quirurgicos : 'No Especificado';
                    $("#tantecedentesQ").text(tantecedentesQ);


                    var ant_alergicos = data.anamnesis.anamnesis_ant_alergicos;
                    if(ant_alergicos){
                        $("input[name=t_ant_alergico][value='si']").prop('checked', true);
                        $("#t_detalle_ant_alergico").removeAttr("hidden");
                    }else{
                        $("input[name=t_ant_alergico][value='no']").prop('checked', true);
                        $("#t_detalle_ant_alergico").attr("hidden",true);
                    }
                    tantecedentesA = (data.anamnesis.anamnesis_ant_alergicos) ? data.anamnesis.anamnesis_ant_alergicos : 'No Especificado';
                    $("#tantecedentesA").text(tantecedentesA);

                    tcheckHabito = data.anamnesis.habito_otros
                    $("#tcheckHabito").prop('checked', tcheckHabito);
                    detallex = document.getElementById("tdetalleHabito");
                    if(tcheckHabito == true){
                        detallex.style.display='block';
                    }else{
                        detallex.style.display='none';
                    }

                    tdetalleOtroHabito = (data.anamnesis.detalle_otro_habito) ? data.anamnesis.detalle_otro_habito : "";
                    $("#tdetalleOtroHabito").text(tdetalleOtroHabito);

                    $("#ttabaco").prop('checked', data.anamnesis.habito_tabaco);
                    $("#talcohol").prop('checked', data.anamnesis.habito_alcohol);
                    $("#tdrogas").prop('checked', data.anamnesis.habito_drogas);

                    tdiagnosticoMedico = (data.anamnesis.diagnosticos_medicos) ? data.anamnesis.diagnosticos_medicos : 'No Especificado';
                    $("#tdiagnosticoMedico").text(tdiagnosticoMedico);

                    tamnesisActual = (data.anamnesis.anamnesis_actual) ? data.anamnesis.anamnesis_actual : 'No Especificado';
                    $("#tamnesisActual").text(tamnesisActual);
					
					//precauciones
					$("#precaucion_estandar_si_resumen").prop("checked",false);
					$("#precaucion_estandar_no_resumen").prop("checked",false);
					var mostrar_precauciones_resumen = function(mostrar){
						$("#div_precauciones_resumen").prop("hidden",!mostrar);
					};
					
					if(data.anamnesis.precaucion_estandar === true){
						$("#precaucion_estandar_si_resumen").prop("checked",true);
						mostrar_precauciones_resumen(true);
					}
					else if(data.anamnesis.precaucion_estandar === false){
						$("#precaucion_estandar_no_resumen").prop("checked",true);
						mostrar_precauciones_resumen(false);
					}
					else{
						$("#precaucion_estandar_si_resumen").prop("checked",false);
						$("#precaucion_estandar_no_resumen").prop("checked",false);
						mostrar_precauciones_resumen(false);
					}
					
					$("#precaucion_respiratorio_resumen").prop("checked",data.anamnesis.precaucion_respiratorio);
					$("#precaucion_contacto_resumen").prop("checked",data.anamnesis.precaucion_contacto);
					$("#precaucion_gotitas_resumen").prop("checked",data.anamnesis.precaucion_gotitas);
					
					
					
                }else{
                    $('.tacom-sin-info').prop('hidden',false);
                    $('.tacom').css('display','none');
                    $('.tdeis-sin-info').prop('hidden',false);
                    $('.tdeis').css('display','none');
                }

                //ginecologico
                if(data.ginecologico != ''){
                    gesta = data.ginecologico.gesta;
                    if(gesta == true){
                        $('.tgesta').css('display','inline-block');
                        $("input[name=tgesta][value='si']").prop('checked', true);
                        $(".tgesta-observacion").text( data.ginecologico.gesta_observacion);
                        $(".tgesta-sin-info").hide();
                        $(".tgesta-mostrar").show();
                    }else if(gesta == false){
                        $('.tgesta').css('display','inline-block');
                        $("input[name=tgesta][value='no']").prop('checked', true);
                        $(".tgesta-observacion").hide();
                        $(".tgesta-sin-info").hide();
                        $(".tgesta-mostrar").hide();
                        $(".tgesta-observacion").text('');
                    }else{
                        $('.tgesta').css('display','none');
                        $(".tgesta-sin-info").show();
                        $(".tgesta-mostrar").hide();
                        $(".tgesta-observacion").text('');
                    }

                    parto = data.ginecologico.parto;
                    if(parto == true){
                        $('.tparto').css('display','inline-block');
                        $("input[name=tparto][value='si']").prop('checked', true);
                        $(".tparto-observacion").text( data.ginecologico.parto_observacion);
                        $(".tparto-sin-info").hide();
                        $(".tparto-mostrar").show();
                    }else if(parto == false){
                        $('.tparto').css('display','inline-block');
                        $("input[name=tparto][value='no']").prop('checked', true);
                        $(".tparto-observacion").hide();
                        $(".tparto-sin-info").hide();
                        $(".tparto-mostrar").hide();
                        $(".tparto-observacion").text('');
                    }else{
                        $('.tparto').css('display','none');
                        $(".tparto-sin-info").show();
                        $(".tparto-mostrar").hide();
                        $(".tparto-observacion").text('');
                    }

                    aborto = data.ginecologico.aborto;
                    if(aborto == true){
                        $('.taborto').css('display','inline-block');
                        $("input[name=taborto][value='si']").prop('checked', true);
                        $(".taborto-observacion").text( data.ginecologico.aborto_observacion);
                        $(".taborto-sin-info").hide();
                        $(".taborto-mostrar").show();
                    }else if(aborto == false){
                        $('.taborto').css('display','inline-block');
                        $("input[name=taborto][value='no']").prop('checked', true);
                        $(".taborto-observacion").hide();
                        $(".taborto-sin-info").hide();
                        $(".taborto-mostrar").hide();
                        $(".taborto-observacion").text('');
                    }else{
                        $('.taborto').css('display','none');
                        $(".taborto-sin-info").show();
                        $(".taborto-mostrar").hide();
                        $(".taborto-observacion").text('');
                    }

                    parto_vaginal = data.ginecologico.parto_vaginal;
                    if(parto_vaginal == true){
                        $('.tpartoVaginal').css('display','inline-block');
                        $("input[name=tpartoVaginal][value='si']").prop('checked', true);
                        $(".tpartoVaginal-observacion").text( data.ginecologico.parto_vaginal_observacion);
                        $(".tpartoVaginal-sin-info").hide();
                        $(".tpartoVaginal-mostrar").show();
                    }else if(parto_vaginal == false){
                        $('.tpartoVaginal').css('display','inline-block');
                        $("input[name=tpartoVaginal][value='no']").prop('checked', true);
                        $(".tpartoVaginal-observacion").hide();
                        $(".tpartoVaginal-sin-info").hide();
                        $(".tpartoVaginal-mostrar").hide();
                        $(".tpartoVaginal-observacion").text('');
                    }else{
                        $('.tpartoVaginal').css('display','none');
                        $(".tpartoVaginal-sin-info").show();
                        $(".tpartoVaginal-mostrar").hide();
                        $(".tpartoVaginal-observacion").text('');
                    }

                    forceps = data.ginecologico.forceps;
                    if(forceps == true){
                        $('.tforceps').css('display','inline-block');
                        $("input[name=tforceps][value='si']").prop('checked', true);
                        $(".tforceps-sin-info").hide();
                    }else if(forceps == false){
                        $('.tforceps').css('display','inline-block');
                        $("input[name=tforceps][value='no']").prop('checked', true);
                        $(".tforceps-sin-info").hide();
                    }else{
                        $('.tforceps').css('display','none');
                        $(".tforceps-sin-info").show();
                    }

                    cesarias = data.ginecologico.cesarias;
                    if(cesarias == true){
                        $('.tcesarias').css('display','inline-block');
                        $("input[name=tcesarias][value='si']").prop('checked', true);
                        $(".tcesarias-observacion").text( data.ginecologico.cesarias_observacion);
                        $(".tcesarias-sin-info").hide();
                        $(".tcesarias-mostrar").show();
                    }else if(cesarias == false){
                        $('.tcesarias').css('display','inline-block');
                        $("input[name=tcesarias][value='no']").prop('checked', true);
                        $(".tcesarias-observacion").hide();
                        $(".tcesarias-sin-info").hide();
                        $(".tcesarias-mostrar").hide();
                        $(".tcesarias-observacion").text('');
                    }else{
                        $('.tcesarias').css('display','none');
                        $(".tcesarias-sin-info").show();
                        $(".tcesarias-mostrar").hide();
                        $(".tcesarias-observacion").text('');
                    }

                    vivos_muertos = data.ginecologico.vivos_muertos;
                    if(vivos_muertos != null){
                        $('.tvivosMuertos').text(vivos_muertos);
                        
                    }else{
                        $('.tvivosMuertos').text("No Especificado");
                    }

                    fecha_ultimo_parto = data.ginecologico.fecha_ultimo_parto;
                    if(fecha_ultimo_parto != null){
                        $('.fechaUltimoParto').text(fecha_ultimo_parto);
                        
                    }else{
                        $('.fechaUltimoParto').text("No Especificado");
                    }

                    metodo_anticonceptivo = data.ginecologico.metodo_anticonceptivo;
                    if(metodo_anticonceptivo == true){
                        $('.tmedotoAnticonceptivo').css('display','inline-block');
                        $("input[name=tmedotoAnticonceptivo][value='si']").prop('checked', true);
                        $(".tmedotoAnticonceptivo-observacion").text( data.ginecologico.metodo_anticonceptivo_observacion);
                        $(".tmedotoAnticonceptivo-sin-info").hide();
                        $(".tmedotoAnticonceptivo-mostrar").show();
                    }else if(metodo_anticonceptivo == false){
                        $('.tmedotoAnticonceptivo').css('display','inline-block');
                        $("input[name=tmedotoAnticonceptivo][value='no']").prop('checked', true);
                        $(".tmedotoAnticonceptivo-observacion").hide();
                        $(".tmedotoAnticonceptivo-sin-info").hide();
                        $(".tmedotoAnticonceptivo-mostrar").hide();
                        $(".tmedotoAnticonceptivo-observacion").text('');
                    }else{
                        $('.tmedotoAnticonceptivo').css('display','none');
                        $(".tmedotoAnticonceptivo-sin-info").show();
                        $(".tmedotoAnticonceptivo-mostrar").hide();
                        $(".tmedotoAnticonceptivo-observacion").text('');
                    }

                    menarquia = data.ginecologico.menarquia;
                    if(menarquia == true){
                        $('.tmenarquia').css('display','inline-block');
                        $("input[name=tmenarquia][value='si']").prop('checked', true);
                        $(".tmenarquia-observacion").text( data.ginecologico.menarquia_observacion);
                        $(".tmenarquia-sin-info").hide();
                        $(".tmenarquia-mostrar").show();
                    }else if(menarquia == false){
                        $('.tmenarquia').css('display','inline-block');
                        $("input[name=tmenarquia][value='no']").prop('checked', true);
                        $(".tmenarquia-observacion").hide();
                        $(".tmenarquia-sin-info").hide();
                        $(".tmenarquia-mostrar").hide();
                        $(".tmenarquia-observacion").text('');
                    }else{
                        $('.tmenarquia').css('display','none');
                        $(".tmenarquia-sin-info").show();
                        $(".tmenarquia-mostrar").hide();
                        $(".tmenarquia-observacion").text('');
                    }

                    ciclo_menstrual = data.ginecologico.ciclo_menstrual;
                    if(ciclo_menstrual != null){
                        $('.tcicloMentrual').text(ciclo_menstrual);
                        
                    }else{
                        $('.tcicloMentrual').text("No Especificado");
                    }

                    menopausia = data.ginecologico.menopausia;
                    if(menopausia == true){
                        $('.tmenopausia').css('display','inline-block');
                        $("input[name=tmenopausia][value='si']").prop('checked', true);
                        $(".tmenopausia-sin-info").hide();
                    }else if(menopausia == false){
                        $('.tmenopausia').css('display','inline-block');
                        $("input[name=tmenopausia][value='no']").prop('checked', true);
                        $(".tmenopausia-sin-info").hide();
                    }else{
                        $('.tmenopausia').css('display','none');
                        $(".tmenopausia-sin-info").show();
                    }

                    pap = data.ginecologico.pap;
                    if(pap != null){
                        $('.tpap').text(pap);
                        
                    }else{
                        $('.tpap').text("No Especificado");
                    }
                 
                    fur = data.ginecologico.fur;
                    if(fur != null){
                        $('.tfur').text(fur);
                        
                    }else{
                        $('.tfur').text("No Especificado");
                    }
                }else{
                    $('.tgesta').css('display','none');
                    $(".tgesta-sin-info").show();
                    $(".tgesta-mostrar").hide();
                    $(".tgesta-observacion").text('');

                    $('.taborto').css('display','none');
                    $(".taborto-sin-info").show();
                    $(".taborto-mostrar").hide();
                    $(".taborto-observacion").text('');

                    $('.tparto').css('display','none');
                    $(".tparto-sin-info").show();
                    $(".tparto-mostrar").hide();
                    $(".tparto-observacion").text('');

                    $('.tpartoVaginal').css('display','none');
                    $(".tpartoVaginal-sin-info").show();
                    $(".tpartoVaginal-mostrar").hide();
                    $(".tpartoVaginal-observacion").text('');

                    $('.tforceps').css('display','none');
                    $(".tforceps-sin-info").show();

                    $('.tcesarias').css('display','none');
                    $(".tcesarias-sin-info").show();
                    $(".tcesarias-mostrar").hide();
                    $(".tcesarias-observacion").text('');

                    $('.tvivosMuertos').text("No Especificado");

                    $('.fechaUltimoParto').text("No Especificado");

                    $('.tmedotoAnticonceptivo').css('display','none');
                    $(".tmedotoAnticonceptivo-sin-info").show();
                    $(".tmedotoAnticonceptivo-mostrar").hide();
                    $(".tmedotoAnticonceptivo-observacion").text('');

                    $('.tmenarquia').css('display','none');
                    $(".tmenarquia-sin-info").show();
                    $(".tmenarquia-mostrar").hide();
                    $(".tmenarquia-observacion").text('');

                    $('.tcicloMentrual').text("No Especificado");

                    $('.tmenopausia').css('display','none');
                    $(".tmenopausia-sin-info").show();

                    $('.tpap').text("No Especificado");

                    $('.tfur').text("No Especificado");
                }

                if(data[1]){
                    data[1].forEach(function(element, indice) {
                        html = '<tr><td><p></p></td><td><p>'+element["nombre"]+'</p></td>';
                        $("#medicamentos_resumen").append(html);
                    });
                    // $.each( data[1], function(k, v) {
                    //     html = '<tr><td><p>'+ (k+1) +'</p></td><td><p>'+v["nombre"]+'</p></td>';
                    //     $("#medicamentos_resumen").append(html);
                    // });
                }

                $(".tpeso").text('No Especificado');
                $(".taltura").text('No Especificado');
                $("#timc").text('No Especificado');
                tresultado2 = 0;
                categoria = 'No Especificado';
                tpas = 'No Especificado';
                tpad = 'No Especificado';
                tpulso = 'No Especificado';
                tfcardiaca = 'No Especificado';
                ttemperatura = 'No Especificado';
                tsaturacion = 'No Especificado';
                tnutricional = 'No Especificado';
                tconciencia = 'No Especificado';
                trespiratoria = 'No Especificado';
                thigiene = 'No Especificado';
                tnova = 'No Especificado';
                tcaida = 'No Especificado';
                tglasgow = 'No Especificado';
                tbarthel = 'No Especificado';

                if(data.general){

                    tpeso = (data.general.peso) ? parseFloat(data.general.peso) : '--';
                    $(".tpeso").text(cambiarAComa(tpeso));
                    taltura = (data.general.altura) ? parseFloat(data.general.altura).toFixed(2) : '--';
                    $(".taltura").text(cambiarAComa(taltura))
                    
                    //calcular IMC
                    if(taltura != '--' && tpeso != '--'){
                        taltura2 = taltura * taltura;
                        tresultado = tpeso/taltura2;
                        tresultado2 = parseFloat(tresultado).toFixed(1);
                        $("#timc").text(cambiarAComa(tresultado2))
                    }

                    fecha_nacimiento = data.fecha_nacimiento;
                    edad_global = (fecha_nacimiento != null) ? moment().diff(fecha_nacimiento, 'years',false) : null;
                    // var edad = "{{ Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) }}";
                    edad = edad_global;

                    if(edad != null && edad < 60){
                      if (tresultado2 > 0 && tresultado2 < 18.5){
                        categoria = 'Insuficiencia ponderal';
                      }else if(tresultado2 >= 18.5 && tresultado2 <= 24.9){
                        categoria = 'Intervalo normal';
                      }else if(tresultado2 == 25.0){
                        categoria = 'Sobrepeso';
                      }else if(tresultado2 > 25.0 && tresultado2 <= 29.9){
                        categoria = 'Preobesidad';
                      }else if(tresultado2 == 30.0){
                        categoria = 'Obesidad';
                      }else if(tresultado2 > 30.0 && tresultado2 <= 34.9){
                        categoria = 'Obesidad de clase I';
                      }else if(tresultado2 >= 35.0 && tresultado2 <= 39.9){
                        categoria = 'Obesidad de clase II';
                      }else if(tresultado2 >= 40.0){
                        categoria = 'Obesidad de clase III';
                      }
                    }else if(edad != null && edad >= 60){
                      if (tresultado2 > 0 && tresultado2 <= 23){
                        categoria = 'Bajo peso';
                      }else if(tresultado2 > 23 && tresultado2 < 28){
                        categoria = 'Normal';
                      }else if(tresultado2 >= 28 && tresultado2 < 32){
                        categoria = 'Sobrepeso';
                      }else if(tresultado2 >= 32){
                        categoria = 'Obesidad';
                      }
                    }

                    tpas = (data.general.presion_arterial_sistolica) ? data.general.presion_arterial_sistolica : '--';
                    tpad = (data.general.presion_arterial_diastolica) ? data.general.presion_arterial_diastolica : '--';
                    tpulso = (data.general.pulso) ? data.general.pulso : '--';
                    tfcardiaca = (data.general.frecuencia_cardiaca) ? data.general.frecuencia_cardiaca : '--';
                    ttemperatura = (data.general.temperatura) ? data.general.temperatura : '--';
                    tsaturacion = (data.general.saturacion) ? data.general.saturacion : '--';
                    tnutricional = (data.general.patron_nutricional) ? data.general.patron_nutricional : 'No Especificado';
                    tconciencia = (data.general.estado_conciencia) ? data.general.estado_conciencia : 'No Especificado';
                    trespiratoria = (data.general.funcion_respiratoria) ? data.general.funcion_respiratoria : 'No Especificado';
                    thigiene = (data.general.higiene) ? data.general.higiene : 'No Especificado';
                    tnova = (data.general.nova) ? data.general.nova : '--';
                    tcaida = (data.general.riesgo_caida) ? data.general.riesgo_caida : '--';
                    tglasgow = (data.general.glasgow) ? data.general.glasgow : '--';                    
                    tbarthel = (data.general.barthel) ? data.general.barthel : '--';                    
                }

                $("#tcategoria").text(categoria);
                $("#tpas").text(tpas);
                $("#tpad").text(tpad);
                $("#tfrespiratoria").text(tpulso);
                $("#tfcardiaca").text(tfcardiaca);
                $("#ttemperatura").text(ttemperatura);
                $("#tsaturacion").text(tsaturacion);
                $("#tnutricional").text(tnutricional);
                $("#tconciencia").text(tconciencia);
                $("#trespiratoria").text(trespiratoria);
                $("#thigiene").text(thigiene);
                $("#tnova").text(tnova);
                $("#tcaida").text(tcaida);
                $("#tglasgow").text(tglasgow);
                $("#tbarthel").text(tbarthel);
                //datos segmentados
                if(data.segmentado){

                    tcabeza = (data.segmentado.cabeza) ? data.segmentado.cabeza : 'No Especificado';
                    $("#tcabeza").text(tcabeza);

                    //protesis dental
                    tprotesisdental = data.segmentado.protesis_dental;
                    tdetalleprotesisdental = (data.segmentado.detalle_protesis_dental) ? data.segmentado.detalle_protesis_dental : 'No Especificado';
                    ubicacionprotesisdental = (data.segmentado.ubicacion_protesis_dental) ? data.segmentado.ubicacion_protesis_dental : 'No Especificado';
                    $("#tdetalledental").text(tdetalleprotesisdental);
                    $("#ubicaciondental").text(ubicacionprotesisdental);
                    tdprotesisdental = $(".tdetalleprotesisdental");
                    if(tprotesisdental == true){
                        $("input[name=tdental][value='si']").prop('checked', true);
                        $('.tdental-hidden').show();
                        $('.tdental-sin-info').hide();
                    }else{
                        if (tprotesisdental == false) {
                            $("input[name=tdental][value='no']").prop('checked', true);
                            $('.tdental-hidden').show();
                            $('.tdental-sin-info').hide();
                        }else{
                            $('.tdental-hidden').hide();
                            $('.tdental-sin-info').show();
                            $('.tdental-sin-info').text('No Especificado');
                        }
                    }

                    //brazalete
                    tbrazalete = data.segmentado.brazalete;
                    tubicacionbrazalete = (data.segmentado.ubicacion_brazalete) ? data.segmentado.ubicacion_brazalete : 'No Especificado';
                    $("#tubicacionbrazalete").text(tubicacionbrazalete);
                    if(tbrazalete == true){
                        $("input[name=tbrazalete][value='si']").prop('checked', true);
                        $('.tbrazalete-hidden').show();
                        $('.tbrazalete-sin-info').hide();
                    }else{
                        if(tbrazalete == false){
                            $("input[name=tbrazalete][value='no']").prop('checked', true);
                            $('.tbrazalete-hidden').show();
                            $('.tbrazalete-sin-info').hide();
                        }else{
                            $('.tbrazalete-hidden').hide();
                            $('.tbrazalete-sin-info').show();
                            $('.tbrazalete-sin-info').text('No Especificado');
                        }
                    }

                      //discapacidad auditiva
                      tauditivo = data.segmentado.discapacidad_auditiva;
                      taudifonos = data.segmentado.audifonos_discapacidad_auditiva;
                      tubicacionaudio = (data.segmentado.ubicacion_discapacidad_auditiva) ? data.segmentado.ubicacion_discapacidad_auditiva : 'No Especificado';
                      tdetalleaudio = (data.segmentado.detalle_discapacidad_auditiva) ? data.segmentado.detalle_discapacidad_auditiva : 'No Especificado';
                    $("#tubicacionaudio").text(tubicacionaudio);
                    $("#tdetalleaudio").text(tdetalleaudio);
                    if(tauditivo == true){
                        $("input[name=tauditivo][value='si']").prop('checked', true);
                        $('.tauditivo-hidden').show();
                        $('.tauditivo-sin-info').hide();
                    }else{
                        if(tauditivo == false){
                            $("input[name=tauditivo][value='no']").prop('checked', true);
                            $('.tauditivo-hidden').show();
                            $('.tauditivo-sin-info').hide();
                        }else{
                            $('.tauditivo-hidden').hide();
                            $('.tauditivo-sin-info').show();
                            $('.tauditivo-sin-info').text('No Especificado');
                        }
                    }
                    if(taudifonos == true){
                        $("input[name=taudifonos][value='si']").prop('checked', true);
                        $(".taudifonos-hidden").show();
                        $('.taudifonos-sin-info').hide();
                    }else{
                        if(taudifonos == false){
                            $(".taudifonos-hidden").show();
                        $('.taudifonos-sin-info').hide();
                            $("input[name=taudifonos][value='no']").prop('checked', true);
                        }else{
                            $(".taudifonos-hidden").hide();
                            $('.taudifonos-sin-info').show();
                            $('.taudifonos-sin-info').text('No Especificado');
                        }
                    }
                      //discapacidad visual
                      tvisual = data.segmentado.discapacidad_visual;
                      tlentes = data.segmentado.lentes_discapacidad_visual;
                      tubicacionlentes = (data.segmentado.lentes_discapacidad_visual == true) ? data.segmentado.ubicacion_discapacidad_visua : 'No Especificado';
                      tdetallelentes = (data.segmentado.lentes_discapacidad_visual  == true) ? data.segmentado.detalle_discapacidad_visua : 'No Especificado';
                    $("#tubicacionlentes").text(tubicacionlentes);
                    $("#tdetallelentes").text(tdetallelentes);
                    if(tvisual == true){
                        $("input[name=tvisual][value='si']").prop('checked', true);
                        $('.tvisual-hidden').show();
                        $('.tvisual-sin-info').hide();
                    }else{
                        if(tvisual == false){
                            $("input[name=tvisual][value='no']").prop('checked', true);
                            $('.tvisual-hidden').show();
                            $('.tvisual-sin-info').hide();
                        }else{
                            $('.tvisual-hidden').hide();
                            $('.tvisual-sin-info').hide();
                            $('.tvisual-sin-info').show();
                            $('.tvisual-sin-info').text('No Especificado');
                        }
                    }
                    if(tlentes == true){
                        $("input[name=tlentes][value='si']").prop('checked', true);
                        $('.tlentes-hidden').show();
                            $('.tlentes-sin-info').hide();

                    }else{
                        if(tlentes == false){
                            $('.tlentes-hidden').show();
                            $('.tlentes-sin-info').hide();
                        $("input[name=tlentes][value='no']").prop('checked', true);
                        }else{
                            $('.tlentes-hidden').hide();
                            $('.tlentes-sin-info').show();
                            $('.tlentes-sin-info').text('No Especificado');
                        }
                    }
                      //presencia lesiones
                      tlesiones = data.segmentado.presencia_lesiones;
                      tlesionestipo = (data.segmentado.tipo_presencia_lesiones) ? data.segmentado.tipo_presencia_lesiones : 'No Especificado';
                      tdescripciontipo = (data.segmentado.descripcion_presencia_lesiones) ? data.segmentado.descripcion_presencia_lesiones : 'No Especificado';
                      tubicacionlesiones = (data.segmentado.ubicacion_presencia_lesiones) ? data.segmentado.ubicacion_presencia_lesiones : 'No Especificado';
                    $("#tlesionestipo").text(tlesionestipo);
                    $("#tdescripciontipo").text(tdescripciontipo);
                    $("#tubicacionlesiones").text(tubicacionlesiones);
                    if(tlesiones == true){
                        $("input[name=tlesiones][value='si']").prop('checked', true);
                        $('.tlesiones-hidden').show();
                        $('.tlesiones-sin-info').hide();
                    }else{
                        if(tlesiones == false){
                            $("input[name=tlesiones][value='no']").prop('checked', true);
                            $('.tlesiones-hidden').show();
                            $('.tlesiones-sin-info').hide();
                        }else{
                            $('.tlesiones-hidden').hide();
                            $('.tlesiones-sin-info').hide();
                            $('.tlesiones-sin-info').show();
                            $('.tlesiones-sin-info').text('No Especificado');
                        }

                    }
                    if(tlesionestipo == 'Otras'){
                        $('.tdescripcionlesiones').show();
                    }else{
                        $('.tdescripcionlesiones').hide();
                    }


                    tcuello = (data.segmentado.cuello) ? data.segmentado.cuello : 'No Especificado';
                    $("#tcuello").text(tcuello);

                    ttorax = (data.segmentado.torax) ? data.segmentado.torax : 'No Especificado';
                    $("#ttorax").text(ttorax);

                    tabdomen = (data.segmentado.abdomen) ? data.segmentado.abdomen : 'No Especificado';
                    $("#tabdomen").text(tabdomen);

                    tsuperiores = (data.segmentado.extremidades_superiores) ? data.segmentado.extremidades_superiores : 'No Especificado';
                    $("#tsuperiores").text(tsuperiores);

                    tinferiores = (data.segmentado.extremidades_inferiores) ? data.segmentado.extremidades_inferiores : 'No Especificado';
                    $("#tinferiores").text(tinferiores);

                    tcolumna = (data.segmentado.columna_torso) ? data.segmentado.columna_torso : 'No Especificado';
                    $("#tcolumna").text(tcolumna);

                    tgenitales = (data.segmentado.genitales) ? data.segmentado.genitales : 'No Especificado';
                    $("#tgenitales").text(tgenitales);

                    tpiel = (data.segmentado.piel) ? data.segmentado.piel : 'No Especificado';
                    $("#tpiel").text(tpiel);

                    tug = "{{$sub_categoria}}";
                    if(tug == 2){
                        taltura_uterina = (data.segmentado.altura_uterina) ? data.segmentado.altura_uterina : 'No Especificado';
                        $("#taltura_uterina").text(taltura_uterina);

                        ttacto_vaginal = (data.segmentado.tacto_vaginal) ? data.segmentado.tacto_vaginal : 'No Especificado';
                        $("#ttacto_vaginal").text(ttacto_vaginal);
                        
                        tmembranas = (data.segmentado.membranas) ? data.segmentado.membranas : 'No Especificado';
                        $("#tmembranas").text(tmembranas);
                        
                        tliquido_anmiotico = (data.segmentado.liquido_anmiotico) ? data.segmentado.liquido_anmiotico : 'No Especificado';
                        $("#tliquido_anmiotico").text(tliquido_anmiotico);
                        
                        tamnioscopia = (data.segmentado.amnioscopia) ? data.segmentado.amnioscopia : 'No Especificado';
                        $("#tamnioscopia").text(tamnioscopia);
                        
                        tamnioscentesis = (data.segmentado.amnioscentesis) ? data.segmentado.amnioscentesis : 'No Especificado';
                        $("#tamnioscentesis").text(tamnioscentesis);
                        
                        tpresentacion = (data.segmentado.presentacion) ? data.segmentado.presentacion : 'No Especificado';
                        $("#tpresentacion").text(tpresentacion);
                        
                        tcontracciones = (data.segmentado.contracciones) ? data.segmentado.contracciones : 'No Especificado';
                        $("#tcontracciones").text(tcontracciones);
                        
                        tlfc = (data.segmentado.lfc) ? data.segmentado.lfc : 'No Especificado';
                        $("#tlfc").text(tlfc);
                        
                        tvagina = (data.segmentado.vagina) ? data.segmentado.vagina : 'No Especificado';
                        $("#tvagina").text(tvagina);

                        tperine = (data.segmentado.perine) ? data.segmentado.perine : 'No Especificado';
                        $("#tperine").text(tperine);
                        
                        ttacto_vaginal_eg = (data.segmentado.tacto_vaginal_eg) ? data.segmentado.tacto_vaginal_eg : 'No Especificado';
                        $("#ttacto_vaginal_eg").text(ttacto_vaginal_eg);
                    }
                }else{
                    //dental
                    $('.tdental-hidden').hide();
                    $('.tdental-sin-info').show();
                    $('.tdental-sin-info').text('Sin información');
                    $("#tdetalledental").text('Sin información');
                    $("#ubicaciondental").text('Sin información');

                    //brazalete
                    $('.tbrazalete-hidden').hide();
                    $('.tbrazalete-sin-info').show();
                    $("#tubicacionbrazalete").text('Sin información');
                    $('.tbrazalete-sin-info').text('Sin información');

                    //auditiva
                    $("#tubicacionaudio").text('Sin información');
                    $("#tdetalleaudio").text('Sin información');
                    $('.tauditivo-hidden').hide();
                    $('.tauditivo-sin-info').show();
                    $('.tauditivo-sin-info').text('Sin información');
                    $(".taudifonos-hidden").hide();
                    $('.taudifonos-sin-info').show();
                    $('.taudifonos-sin-info').text('Sin información');

                    //visual
                    $("#tubicacionlentes").text('Sin información');
                    $("#tdetallelentes").text('Sin información');
                    $('.tvisual-hidden').hide();
                    $('.tvisual-sin-info').hide();
                    $('.tvisual-sin-info').show();
                    $('.tvisual-sin-info').text('Sin información');
                    $('.tlentes-hidden').hide();
                    $('.tlentes-sin-info').show();
                    $('.tlentes-sin-info').text('Sin información');

                    //lesiones
                    $("#tlesionestipo").text('Sin información');
                    $("#tdescripciontipo").text('Sin información');
                    $("#tubicacionlesiones").text('Sin información');
                    $('.tlesiones-hidden').hide();
                            $('.tlesiones-sin-info').hide();
                            $('.tlesiones-sin-info').show();
                            $('.tlesiones-sin-info').text('Sin información');
                }

                if(data.otros){
                  data.otros.forEach(function(datos) {
                      if(datos.tipo_cateter == 0){
                        $("#tnumeroB1").text(datos.numero != null ? datos.numero : 'Sin información');
                        $("#tlugarB1").text(datos.lugar_instalacion != null ? datos.lugar_instalacion : 'Sin información');
                        var tfechaB1 = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaB1").text(tfechaB1);
                        var tfechaB1sinFormato = datos.fecha_instalacion;
                        $("#tcalculoB1").text(calculoDias(tfechaB1sinFormato));
                        $("#tresponsableB1").text(datos.responsable_instalcion != null ? datos.responsable_instalcion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 1){
                        $('#tnumeroB2').text(datos.numero != null ? datos.numero : 'Sin información');
                        $("#tlugarB2").text(datos.lugar_instalacion != null ? datos.lugar_instalacion : 'Sin información');
                        var tfechaB2 = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaB2").text(tfechaB2);
                        var tfechaB2sinFormato = datos.fecha_instalacion;
                        $("#tcalculoB2").text(calculoDias(tfechaB2sinFormato));
                        $("#tresponsableB2").text(datos.responsable_instalcion != null ? datos.lugar_instalacion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 2){
                        $('#tnumeroF').text(datos.numero != null ? datos.numero : 'Sin información');
                        $("#tlugarF").text(datos.lugar_instalacion != null ? datos.lugar_instalacion : 'Sin información');
                        var tfechaF = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaF").text(tfechaF);
                        var tfechaFsinFormato = datos.fecha_instalacion;
                        $("#tcalculoF").text(calculoDias(tfechaFsinFormato));
                        $('#tmaterialF').text(datos.material_fabricacion != null ? datos.material_fabricacion : 'Sin información');
                        $('#tfechaCuracionF').text(moment(datos.fecha_curacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY'));
                        $('#tresponsableF').text(datos.responsable_curacioin != null ? datos.numero : 'Sin información');
                        $('#tobservacionF').text(datos.observacion != null ? datos.observacion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 3){
                        $('#tnumeroSng').text(datos.numero != null ? datos.numero : 'Sin información');
                        $("#tlugarSng").text(datos.lugar_instalacion != null ? datos.lugar_instalacion : 'Sin información');
                        var tfechaSng = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaSng").text(tfechaSng);
                        var tfechaSngsinFormato = datos.fecha_instalacion;
                        $("#tcalculoSng").text(calculoDias(tfechaSngsinFormato));
                        $('#tmaterialSng').text(datos.material_fabricacion != null ? datos.material_fabricacion : 'Sin información');
                        $('#tresponsableSng').text(datos.responsable_curacioin != null ? datos.responsable_curacioin : 'Sin información');
                      }

                      if(datos.tipo_cateter == 4){
                        $('#tnumeroCvc').text(datos.numero != null ? datos.numero : 'Sin información');
                        $('#ttipoCvc').text(datos.tipo  != null ? datos.tipo : 'Sin información');
                        $("#tlugarCvc").text(datos.lugar_instalacion  != null ? datos.lugar_instalacion : 'Sin información');
                        var tfechaCvc = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaCvc").text(tfechaCvc);
                        var tfechaCvcsinFormato = datos.fecha_instalacion;
                        $("#tcalculoCvc").text(calculoDias(tfechaCvcsinFormato));
                        $('#tmaterialCvc').text(datos.material_fabricacion  != null ? datos.material_fabricacion : 'Sin información');
                        $('#tresponsableCvc').text(datos.responsable_curacioin  != null ? datos.responsable_curacioin : 'Sin información');
                        $('#tviaCvc').text(datos.via_instalacion  != null ? datos.via_instalacion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 5){
                        $('#tnumeroNasoye').text(datos.numero != null ? datos.numero : 'Sin información');
                        var tfechaNasoye = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaNasoye").text(tfechaNasoye);
                        var tfechaNasoyesinFormato = datos.fecha_instalacion;
                        $("#tcalculoNasoye").text(calculoDias(tfechaB2sinFormato));
                        $("#tresponsableNasoye").text(datos.responsable_instalcion != null ? datos.responsable_instalcion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 6){
                        $('#tcuffTraqueo').text(datos.medicion_cuff != null ? datos.medicion_cuff : 'Sin información');
                        var tfechaTraqueo = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaTraqueo").text(tfechaTraqueo);
                        var tfechaTraqueosinFormato = datos.fecha_instalacion;
                        $("#tcalculoTraqueo").text(calculoDias(tfechaTraqueosinFormato));
                        $('#tresponsableTraqueo').text(datos.responsable_instalcion != null ? datos.responsable_instalcion : 'Sin información');
                        $('#tfechaCambioTraqueo').text(moment(datos.fecha_curacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY'));
                        $('#tveppTraqueo').text(datos.observacion != null ? datos.observacion : 'Sin información');
                      }

                      if(datos.tipo_cateter == 7){
                        $('#ttipoOsto').text(datos.tipo != null ? datos.tipo : 'Sin información');
                        var tfechaOsto = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaOsto").text(tfechaOsto);
                        $('#tcuidadoOsto').text(datos.cuidado_enfermeria) != null ? datos.cuidado_enfermeria : 'Sin información';
                        var tfechaOstosinFormato = datos.fecha_curacion;
                        $('#tresponsableOsto').text(datos.responsable_curacioin != null ? datos.responsable_curacioin : 'Sin información');
                        $("#tcalculoBolsaOsto").text(calculoDias(tfechaOstosinFormato));
                        $('#tvaloracionEstomaOsto').text(datos.valoracion_estomaypiel != null ? datos.valoracion_estomaypiel : 'Sin información');
                        $('#tcuidadoEstomaOsto').text(datos.responsable_curacion_ostomias != null ? datos.responsable_curacion_ostomias : 'Sin información');
                        $('#tmedicionEfluenteOsto').text(datos.medicion_efluente != null ? datos.medicion_efluente : 'Sin información');
                        $('#tfechaCambioOsto').text(moment(datos.fecha_curacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY'));
                        $('#tcaracteristicaOsto').text(datos.observacion != null ? datos.observacion : 'Sin información');
                        $('#tdetalleEducacionOsto').text(datos.detalle_educacion != null ? datos.detalle_educacion : 'Sin información');
                        if(datos.bagueta == false){
                          $('#tbaguetaOstoNo').prop("checked", true);
                        }else{
                          $('#tbaguetaOstoSi').prop("checked", true);
                        }
                      }

                      if(datos.tipo_cateter == 8){
                        $("#tdetalleOtro").text(datos.detalle != null ? datos.detalle : 'Sin información');
                        $("#tnumeroOtro").text(datos.numero != null ? datos.numero : 'Sin información');
                        $("#ttipoOtro").text(datos.tipo != null ? datos.tipo : 'Sin información');
                        var tfechaOtro = moment(datos.fecha_instalacion, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY HH:mm');
                        $("#tfechaOtro").text(tfechaOtro);
                        $("#tlugarOtro").text(datos.lugar_instalacion  != null ? datos.lugar_instalacion : 'Sin información');
                        $("#tmaterialOtro").text(datos.material_fabricacion  != null ? datos.material_fabricacion : 'Sin información');
                        $("#tviaOtro").text(datos.via_instalacion_otro  != null ? datos.via_instalacion_otro : 'Sin información');
                        $("#treponsablecuraOtro").text(datos.responsable_curacioin  != null ? datos.responsable_curacioin : 'Sin información');
                        var tfechaFsinFormato = datos.fecha_instalacion;
                        $("#tcalculoOtro").text(calculoDias(tfechaFsinFormato));
                      }
                  });

                }
				
				if(data.ginecoobstetrico){
					
					var procesarValor = function(val){
						if(val === null || val === ""){
							return "Sin información";
						}
						if(val === true){
							return "Sí";
						}
						if(val === false){
							return "No";
						}
						return val;
					};
					
					$("#vulva_ego_resumen").text(procesarValor(data.ginecoobstetrico.vulva));
					$("#vagina_tacto_vaginal_ego_resumen").text(procesarValor(data.ginecoobstetrico.vagina_tacto_vaginal));
					$("#fondo_de_saco_tacto_vaginal_ego_resumen").text(procesarValor(data.ginecoobstetrico.fondo_de_saco_tacto_vaginal));
					$("#anexos_ego_resumen").text(procesarValor(data.ginecoobstetrico.anexos));
					$("#otros_tacto_vaginal_ego_resumen").text(procesarValor(data.ginecoobstetrico.otros_tacto_vaginal));
					$("#vagina_especuloscopia_ego_resumen").text(procesarValor(data.ginecoobstetrico.vagina_especuloscopia));
					$("#utero_ego_resumen").text(procesarValor(data.ginecoobstetrico.utero));
					$("#cervix_ego_resumen").text(procesarValor(data.ginecoobstetrico.cervix));
					$("#fondo_de_saco_especuloscopia_ego_resumen").text(procesarValor(data.ginecoobstetrico.fondo_de_saco_especuloscopia));
					$("#otros_especuloscopia_ego_resumen").text(procesarValor(data.ginecoobstetrico.otros_especuloscopia));
					$("#recto_ano_ego_resumen").text(procesarValor(data.ginecoobstetrico.recto_ano));
					$("#presentacion_ego_resumen").text(procesarValor(data.ginecoobstetrico.presentacion));
					$("#altura_uterina_ego_resumen").text(procesarValor(data.ginecoobstetrico.altura_uterina));
					$("#tono_ego_resumen").text(procesarValor(data.ginecoobstetrico.tono));
					$("#encajamiento_ego_resumen").text(procesarValor(data.ginecoobstetrico.encajamiento));
					$("#dorso_ego_resumen").text(procesarValor(data.ginecoobstetrico.dorso));
					$("#contracciones_ego_resumen").text(procesarValor(data.ginecoobstetrico.contracciones));
					$("#lcf_ego_resumen").text(procesarValor(data.ginecoobstetrico.lcf));
					$("#desaceleraciones_ego_resumen").text(procesarValor(data.ginecoobstetrico.desaceleraciones));
					$("#longitud_cuello_uterino_ego_resumen").text(procesarValor(data.ginecoobstetrico.longitud_cuello_uterino));
					$("#dilatacion_cuello_uterino_ego_resumen").text(procesarValor(data.ginecoobstetrico.dilatacion_cuello_uterino));
					$("#membranas_ego_resumen").text(procesarValor(data.ginecoobstetrico.membranas));
					$("#liquido_amniotico_ego_resumen").text(procesarValor(data.ginecoobstetrico.liquido_amniotico));
					$("#posicion_ego_resumen").text(procesarValor(data.ginecoobstetrico.posicion));
					$("#plano_ego_resumen").text(procesarValor(data.ginecoobstetrico.plano));
					$("#evaluacion_pelvis_ego_resumen").text(procesarValor(data.ginecoobstetrico.evaluacion_pelvis));
					$("#otros_examen_obstetrico_ego_resumen").text(procesarValor(data.ginecoobstetrico.otros_examen_obstetrico));
				}

                botonPdf = document.getElementById("botonPdf");
                sinDatos = document.getElementById("sindatos");



            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    // function existePuebloPueblo(){
    //     var caso = {{$caso}};
    //     $.ajax({
    //         url: "{{ URL::to('/gestionEnfermeria')}}/existenDatosPueblo",
    //         data: {
    //             caso : caso
    //         },
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         type: "post",
    //         dataType: "json",
    //         success: function (data) {
    //             console.log("info: ", data);

    //             var pueblo = data['pueblo_indigena'];
    //             console.log(pueblo);
    //             var detalle_pueblo = data['detalle_pueblo_indigena'];
    //             if( pueblo === null || pueblo == 'Ninguno'){
    //                 $("input[name=tpuebloind][value='no']").prop('checked', true);
    //             }else{
    //                 $("input[name=tpuebloind][value='si']").prop('checked', true);
    //                 $("#tpueblo_ind").val(pueblo);
    //                 $("#tpueblo").css('display','block');
    //                 $("#tpueblo").show("slow");
    //             }

    //             if(pueblo == 'Otro'){
    //                 $(".tcla_ind").removeAttr("hidden");
    //                 $("#tesp_pueblo").val(detalle_pueblo);
    //             }else{
    //                 $(".tcla_ind").attr("hidden",true);
    //             }
    //         },
    //         error: function (error) {
    //             console.log(error);
    //         }
    //     });
    // }

    function tmostrarOcultarExamenObstetrico(){
            var tug = "{{$sub_categoria}}";
            if(tug == 2){
                $("#tginecologica").show();
            }else{
                $("#tginecologica").hide();
            }
        }

    $(document).ready(function(){

        $( "#iEnfermeria" ).click(function() {
            tabIE = $("#tabsIngresoEnfermeria div.active").attr("id");
            
            if(tabIE == "5h"){
                mostrarIngresoEnfermeria();
            }
            
        });

        $("#hR").click(function() {
            mostrarIngresoEnfermeria();
            $("#medicamentos_resumen").empty();
            // existePuebloPueblo();
        });
    });

</script>
<style>
    .formulario > .panel-default > .panel-heading {
        background-color: #bce8f1 !important;
    }

    #rs_table tbody{
        counter-reset: Serial;
    }

    table #rs_table{
        border-collapse: separate;
    }

    #rs_table tr td:first-child:before{
    counter-increment: Serial;
    content: counter(Serial);
    }

    .pb-1{
        padding-bottom: 10px;
    }
</style>

<fieldset>

<!-- boton pdf -->
<div class="formulario">
    <div class="row">
        <div class="col-md-3">
            <div class="form-inline" id="botonPdf">
                {{ HTML::link(URL::route('pdfResumenHojaIngresoEnfermeria', [$caso]), 'Reporte Pdf', ['class' => 'btn btn-danger', 'target' => '_blank']) }}
            </div>
            <div id="sindatos" style="display: none;">
                <strong>El Paciente no registra información para exportar</strong>
            </div>
        </div>
        <div class="col-md-5">
            <h4 style="text-align: center;">HOJA INGRESO ENFERMERIA</h4>
        </div>
        <div class="col-md-4">
            <div class="col-md-6" style="text-align: right;">
                <label class="subtitulos">FECHA</label>
            </div>
            <div class="col-md-6">
                <!-- {{ \Carbon\Carbon::now()->format('d-m-Y')}} -->
                <p id="fecha_ingreso_enfermeria"></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>I. ANAMNESIS</h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    {{Form::label('ANTECEDENTES MORBIDOS Y TRATAMIENTO:')}}
                    <p id="tmorbidos"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 pl-0 pr-0">
                    <div class="col-sm-12">
                        {{Form::label('Ofrecimiento de acompañamiento:')}} <br>
                        <label class="radio-inline tacom" style="display:none">{{Form::radio('tacom', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tacom" style="display:none">{{Form::radio('tacom', "si", false, array('disabled'))}}Sí</label>
                        <span class="tacom-sin-info" hidden>Sin información</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="col-sm-12">
                        {{Form::label('DEIS:')}} <br>
                        <label class="radio-inline tdeis" style="display:none">{{Form::radio('tdeis', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tdeis" style="display:none">{{Form::radio('tdeis', "si", false, array('disabled'))}}Sí</label>
                      <span class="tdeis-sin-info" hidden>Sin información</span>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <div class="col-sm-6">
                        {{Form::label('Pertenece a algún pueblo originario:')}}
                        <label class="radio-inline tpuebloind" style="display:none">{{Form::radio('tpuebloind', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tpuebloind" style="display:none">{{Form::radio('tpuebloind', "si", false, array('disabled'))}}Sí</label>
                      <span class="tpuebloind-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-12">
                    <div class="col-sm-6" id="tpueblo" style="display: none">
                        {{Form::select('tpueblo_ind',["Mapuche" =>"1. Mapuche", "Aymara"=> "2. Aymara", "Rapa nui" => "3. Rapa Nui (Pascuense)", "Lican Antai" => "4. Lican Antai (Atacameño)", "Quechua" => "5. Quechua", "Colla" => "6. Colla","Diaguita" => "7. Diaguita" , "Kawéscar" => "8. Kawésqar", "Yagán" => "9. Yagán (Yámana)", "Ninguno" => "96. Ninguno", "Otro" => "99. Otro"], null, array('id' => 'tpueblo_ind', 'class' => 'form-control', 'autofocus' => 'true', 'disabled'))}}
                    </div>
                    <div class="col-sm-6 tcla_ind" hidden>
                        {{Form::text('tesp_pueblo', null, array('id' => 'tesp_pueblo', 'class' => 'form-control', 'disabled'))}}
                    </div>
                </div> --}}
            </div>
            <br>
            <div class="row" id="tAcompañamiento" hidden>
                <!-- <div class="col-md-12"> -->
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('', "Nombre de cuidador o familiar", array()) }}
                            <p id="tFamiliarAcompanante"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{ Form::label('', "Vinculo familiar", array()) }}
                            <p id="tVinculoAcompanante"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{ Form::label('', "Número telefonico de cuidador o familiar", array()) }}
                            <p id="tTelefonoAcompanante"></p>
                        </div>
                    </div>
                <!-- </div> -->
            </div>
            <br>
            <legend></legend>

                <div class="row">
                    <div class="col-md-12  pl-0 pr-0">
                        <div class="col-md-12">
                            <p class="subtitulos" align="left" ><b>Lista Medicamentos</b></p>
                            <table id="rs_table" class="table table-striped table-bordered" style="width:100%;">
                                <thead style="background-color:#1E9966; color:#FFF">
                                    <tr>
                                        <th><p class="subtitulos" align="left">N°</p></th>
                                        <th><p class="subtitulos" align="left">Nombre</p></th>
                                    </tr>
                                </thead>
                                <tbody id="medicamentos_resumen">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="col-sm-12">
                    {{Form::label('ANTECEDENTES QUIRÚRGICO:')}}
                    <p id="tantecedentesQ"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    {{Form::label('ANTECEDENTES ALERGICOS:')}}
                    <label class="radio-inline">{{Form::radio('t_ant_alergico', "no", true, array('disabled'))}}No</label>
                    <label class="radio-inline">{{Form::radio('t_ant_alergico', "si", false, array('disabled'))}}Sí</label>
                </div>
                <div class="col-md-12" id="t_detalle_ant_alergico" hidden>
                    {{Form::label('DETALLE ANTECEDENTES ALERGICOS:')}}
                    <p id="tantecedentesA"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-bottom:10px;">
                    {{form::label('HABITOS: ')}}
                    <label> <input id="ttabaco" name="habitos[]" type="checkbox" value="tabaco" title="tabaco" disabled/> <span title="tabaco">Tabaco</span></label>
                    <label> <input id="talcohol" name="habitos[]" type="checkbox" value="alcohol" title="alcohol" disabled/> <span title="alcohol">Alcohol</span></label>
                    <label> <input id="tdrogas" name="habitos[]" type="checkbox" value="drogas" disabled/> Drogas</label>
                    <label> <input name="thabitos[]" id="tcheckHabito" type="checkbox" value="otras" disabled/> Otras</label>
                </div>

                <div id="tdetalleHabito"  class="col-sm-12" >
                    {{form::label('DETALLE OTRO HABITO: ')}}
                    <p id="tdetalleOtroHabito"></p>
                </div>
                <br>
            </div>
			@if($sub_categoria == 4)
			<div class="row">
				<div class="col-sm-12">
					{{form::label('PRECAUCIÓN ESTÁNDAR: ')}}
					<label>
						<input type="radio" id="precaucion_estandar_si_resumen" disabled>
						<span>Sí</span>
					</label>
					<label>
						<input type="radio" id="precaucion_estandar_no_resumen" disabled>
						<span>No</span>
					</label>
				</div>
				<div class="col-sm-12" id="div_precauciones_resumen" hidden>
					<label>
						<input type="checkbox" id="precaucion_respiratorio_resumen" disabled>
						<span>Respiratorio</span>
					</label>
					<label>
						<input type="checkbox" id="precaucion_contacto_resumen" disabled>
						<span>Contacto</span>
					</label>
					<label>
						<input type="checkbox" id="precaucion_gotitas_resumen" disabled>
						<span>Gotitas</span>
					</label>
				</div>
			</div>
			<br>
			@endif
            <div class="row">
                <div class="col-sm-12">
                        {{form::label('DIAGNOSTICOS MÉDICOS: ')}}
                        <p id="tdiagnosticoMedico"></p>
                </div>
                <div class="col-sm-12">
                    {{form::label('ANAMNESIS ACTUAL: ')}}
                    <p id="tamnesisActual"></p>
                </div>
            </div>
        </div>
        <!-- antecedentes abstetricos -->
        @if($sub_categoria == 2)
            <div class="panel-body">
                <legend>ANTECEDENTES OBSTÉTRICOS</legend>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('GESTA:')}} <br>
                        <label class="radio-inline tgesta" style="display:none">{{Form::radio('tgesta', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tgesta" style="display:none">{{Form::radio('tgesta', "si", false, array('disabled'))}}Sí</label>
                        <span class="tgesta-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tgesta-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tgesta-observacion"></span>
                    </div>
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('PARTO:')}} <br>
                        <label class="radio-inline tparto" style="display:none">{{Form::radio('tparto', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tparto" style="display:none">{{Form::radio('tparto', "si", false, array('disabled'))}}Sí</label>
                        <span class="tparto-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tparto-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tparto-observacion"></span>
                    </div>
                </div>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('ABORTO:')}} <br>
                        <label class="radio-inline taborto" style="display:none">{{Form::radio('taborto', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline taborto" style="display:none">{{Form::radio('taborto', "si", false, array('disabled'))}}Sí</label>
                        <span class="taborto-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 taborto-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="taborto-observacion"></span>
                    </div>
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('PARTO VAGINAL:')}} <br>
                        <label class="radio-inline tpartoVaginal" style="display:none">{{Form::radio('tpartoVaginal', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tpartoVaginal" style="display:none">{{Form::radio('tpartoVaginal', "si", false, array('disabled'))}}Sí</label>
                        <span class="tpartoVaginal-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tpartoVaginal-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tpartoVaginal-observacion"></span>
                    </div>
                </div>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-6 pl-0 pr-0">
                        {{Form::label('FORCEPS:')}} <br>
                        <label class="radio-inline tforceps" style="display:none">{{Form::radio('tforceps', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tforceps" style="display:none">{{Form::radio('tforceps', "si", false, array('disabled'))}}Sí</label>
                        <span class="tforceps-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('CESARIAS:')}} <br>
                        <label class="radio-inline tcesarias" style="display:none">{{Form::radio('tcesarias', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tcesarias" style="display:none">{{Form::radio('tcesarias', "si", false, array('disabled'))}}Sí</label>
                        <span class="tcesarias-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tcesarias-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tcesarias-observacion"></span>
                    </div>
                </div>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('VIVOS/MUERTOS:')}} <br>
                        <span class="tvivosMuertos"></span>
                    </div>
                    <div class="col-md-2">
                        {{Form::label('FECHA ULTIMO PARTO:')}} <br>
                        <span class="fechaUltimoParto"></span>
                    </div>
                </div>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('MÉTODO ANTICONCEPTIVO:')}} <br>
                        <label class="radio-inline tmedotoAnticonceptivo" style="display:none">{{Form::radio('tmedotoAnticonceptivo', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tmedotoAnticonceptivo" style="display:none">{{Form::radio('tmedotoAnticonceptivo', "si", false, array('disabled'))}}Sí</label>
                        <span class="tmedotoAnticonceptivo-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tmedotoAnticonceptivo-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tmedotoAnticonceptivo-observacion">Sin información</span>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <legend>ANTECEDENTES GINECOLÓGICOS</legend>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('MENARQUIA:')}} <br>
                        <label class="radio-inline tmenarquia" style="display:none">{{Form::radio('tmenarquia', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tmenarquia" style="display:none">{{Form::radio('tmenarquia', "si", false, array('disabled'))}}Sí</label>
                        <span class="tmenarquia-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4 tmenarquia-mostrar" hidden>
                        {{Form::label('OBSERVACIÓN:')}} <br>
                        <span class="tmenarquia-observacion">Sin información</span>
                    </div>
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('CICLO MENSTRUAL:')}} <br>
                        <span class="tcicloMentrual"></span>
                    </div>
                </div>
                <div class="col-md-12 pr-0 pl-0 pb-1">
                    <div class="col-md-2 pl-0 pr-0">
                        {{Form::label('MENOPAUSIA:')}} <br>
                        <label class="radio-inline tmenopausia" style="display:none">{{Form::radio('tmenopausia', "no", false, array('disabled'))}}No</label>
                        <label class="radio-inline tmenopausia" style="display:none">{{Form::radio('tmenopausia', "si", false, array('disabled'))}}Sí</label>
                        <span class="tmenopausia-sin-info" hidden>Sin información</span>
                    </div>
                    <div class="col-md-4">
                        {{Form::label('PAP:')}} <br>
                        <span class="tpap"></span>
                    </div>
                    <div class="col-md-4 pl-0 pr-0">
                        {{Form::label('FUR:')}} <br>
                        <span class="tfur"></span>
                    </div>
                </div>
            </div>
        @endif
         <!-- antecedentes abstetricos -->
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>II. EXAMEN FÍSICO GENERAL</h4>
        </div>
        <div class="panel-body">
            @if($sub_categoria == 3)
            <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "PESO (KG): ", array( ))}}
                            <p class="tpeso"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                            {{Form::label('', "TALLA (CM): ", array( ))}}
                            <p class="taltura"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                        {{Form::label('', "ESTADO NUTRICIONAL: ", array( ))}}
                            <p id="tnutricional"></p>
                        </div>
                    </div>
                </div>
            @else
                <legend>INFORMACIÓN IMC</legend>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "PESO (KG): ", array( ))}}
                            <p class="tpeso"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                            {{Form::label('', "ALTURA (MT): ", array( ))}}
                            <p class="taltura"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">

                        <div class="form-group">
                            {{Form::label('', "IMC: ", array( ))}}
                            <p id="timc"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "CATEGORIA: ", array( ))}}
                            <p id="tcategoria"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "ESTADO NUTRICIONAL: ", array( ))}}
                            <p id="tnutricional"></p>
                        </div>
                    </div>
                </div>
            @endif
            <br><br>
            <legend>SIGNOS VITALES</legend>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "PRESIÓN ARTERIAL SISTOLICA: ", array( ))}}
                        <p id="tpas"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "PRESIÓN ARTERIAL DIASTOLICA: ", array( ))}}
                        <p id="tpad"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "FRECUENCIA RESPIRATORIA: ", array( ))}}
                        <p id="tfrespiratoria"></p>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "FRECUENCIA CARDIACA: ", array( ))}}
                        <p id="tfcardiaca"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "TEMPERATURA: ", array( ))}}
                        <p id="ttemperatura"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "SATURACIÓN: ", array( ))}}
                        <p id="tsaturacion"></p>
                    </div>
                </div>
            </div>
            <br><br>
            <legend>VALORACIÓN DE NECESIDADES</legend>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "ESTADO CONCIENCIA: ", array( ))}}
                        <p id="tconciencia"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "FUNCIÓN RESPIRATORIA: ", array( ))}}
                        <p id="trespiratoria"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "HIGIENE (ASEO Y CONFORT): ", array( ))}}
                        <p id="thigiene"></p>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"NOVA: ")}}
                        <p id="tnova"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"RIESGO CAÍDA: ")}}
                        <p id="tcaida"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"GLASGOW: ")}}
                        <p id="tglasgow"></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('',"BARTHEL: ")}}
                        <p id="tbarthel"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>III. EXAMEN FÍSICO SEGMENTARIO</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "CABEZA", array( ))}}
                        <p id="tcabeza"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "GENITALES: ", array( ))}}
                        <p id="tgenitales"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "PIEL: ", array( ))}}
                        <p id="tpiel"></p>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "CUELLO: ", array( ))}}
                        <p id="tcuello"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "TORAX: ", array( ))}}
                        <p id="ttorax"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "ABDOMEN: ", array( ))}}
                        <p id="tabdomen"></p>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "EXTREMIDADES SUPERIORES: ", array( ))}}
                        <p id="tsuperiores"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "EXTREMIDADES INFERIORES: ", array( ))}}
                        <p id="tinferiores"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1">
                    <div class="form-group">
                        {{Form::label('', "COLUMNA Y DORSO: ", array( ))}}
                        <p id="tcolumna"></p>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', "PROTESIS DENTAL: ", array( ))}} <br>
                            <label class="radio-inline tdental-hidden">{{Form::radio('tdental', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tdental-hidden">{{Form::radio('tdental', "si", false, array('disabled'))}}Sí</label>
                            <span class="tdental-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "UBICACIÓN: ", array( ))}}
                        <p id="ubicaciondental"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "DETALLE PROTESIS DENTAL: ", array( ))}}
                        <p id="tdetalledental"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
            <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', "BRAZALETE: ", array( ))}} <br>
                            <label class="radio-inline tbrazalete-hidden">{{Form::radio('tbrazalete', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tbrazalete-hidden">{{Form::radio('tbrazalete', "si", false, array('disabled'))}}Sí</label>
                            <span class="tbrazalete-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-3 col-md-offset-1 tubicacionbrazalete" >
                    <div class="form-group">
                        {{Form::label('', "UBICACIÓN: ", array( ))}}
                        <p id="tubicacionbrazalete"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "DISCAPACIDAD AUDITIVA: ", array( ))}} <br>
                            <label class="radio-inline tauditivo-hidden">{{Form::radio('tauditivo', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tauditivo-hidden">{{Form::radio('tauditivo', "si", false, array('disabled'))}}Sí</label>
                            <span class="tauditivo-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', "AUDIFONOS: ", array( ))}} <br>
                            <label class="radio-inline taudifonos-hidden">{{Form::radio('taudifonos', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline taudifonos-hidden">{{Form::radio('taudifonos', "si", false, array('disabled'))}}Sí</label>
                            <span class="taudifonos-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "UBICACIÓN: ", array( ))}}
                        <p id="tubicacionaudio"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "DETALLE AUDIFONOS: ", array( ))}}
                        <p id="tdetalleaudio"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "DISCAPACIDAD VISUAL: ", array( ))}} <br>
                            <label class="radio-inline tvisual-hidden">{{Form::radio('tvisual', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tvisual-hidden">{{Form::radio('tvisual', "si", false, array('disabled'))}}Sí</label>
                            <span class="tvisual-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', "LENTES: ", array( ))}} <br>
                            <label class="radio-inline tlentes-hidden">{{Form::radio('tlentes', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tlentes-hidden">{{Form::radio('tlentes', "si", false, array('disabled'))}}Sí</label>
                            <span class="tlentes-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "UBICACIÓN: ", array( ))}}
                        <p id="tubicacionlentes"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "DETALLE LENTES: ", array( ))}}
                        <p id="tdetallelentes"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "PRESENCIA LESIONES: ", array( ))}} <br>
                            <label class="radio-inline tlesiones-hidden">{{Form::radio('tlesiones', "no", true, array('disabled'))}}No</label>
                            <label class="radio-inline tlesiones-hidden">{{Form::radio('tlesiones', "si", false, array('disabled'))}}Sí</label>
                            <span class="tlesiones-sin-info" hidden></span>
                        </div>
                </div>
                <div class="col-md-2">
                        <div class="form-group">
                            {{Form::label('', "TIPO: ", array( ))}} <br>
                            <p id="tlesionestipo"></p>
                        </div>
                </div>
                <div class="col-md-2 col-md-offset-1 tdescripcionlesiones" >
                    <div class="form-group">
                        {{Form::label('', "DESCRIPCIÓN: ", array( ))}}
                        <p id="tdescripciontipo"></p>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1 tdetalleprotesisdental" >
                    <div class="form-group">
                        {{Form::label('', "UBICACIÓN: ", array( ))}}
                        <p id="tubicacionlesiones"></p>
                    </div>
                </div>
            </div>
            <div id="tginecologica">
                <div class="col-md-12">
                    <legend>EXAMEN OBSTÉTRICO</legend>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "ALTURA UTERINA", array( ))}}
                            <p id="taltura_uterina"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "TACTO VAGINAL: ", array( ))}}
                            <p id="ttacto_vaginal"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "MEMBRANAS: ", array( ))}}
                            <p id="tmembranas"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "LIQUIDO AMNIÓTICO: ", array( ))}}
                            <p id="tliquido_anmiotico"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "AMNIOSCOPIA: ", array( ))}}
                            <p id="tamnioscopia"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "AMNIOCENTESIS: ", array( ))}}
                            <p id="tamnioscentesis"></p>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "PRESENTACIÓN: ", array( ))}}
                            <p id="tpresentacion"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "CONTRACCIONES: ", array( ))}}
                            <p id="tcontracciones"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "LCF (LATIDOS CARDIO FETALES): ", array( ))}}
                            <p id="tlfc"></p>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-12">
                    <legend>EXAMEN GINECOLÓGICO</legend>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('', "VAGINA: ", array( ))}}
                            <p id="tvagina"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "PERINÉ: ", array( ))}}
                            <p id="tperine"></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            {{Form::label('', "TACTO VAGINAL: ", array( ))}}
                            <p id="ttacto_vaginal_eg"></p>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
	@if($sub_categoria == 1)
	<div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>IV. EXAMEN GINECOOBSTÉTRICO</h4>
        </div>
        <div class="panel-body">
			<fieldset>
				<legend>Examen ginecológico</legend>
				<h4>Tacto vaginal</h4>
				<div class="row">
					<div class="col-md-4">
						<label>Vulva</label>
						<p id="vulva_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Vagina</label>
						<p id="vagina_tacto_vaginal_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Fondo de saco</label>
						<p id="fondo_de_saco_tacto_vaginal_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Anexos</label>
						<p id="anexos_ego_resumen"></p>
					</div>
					<div class="col-md-8">
						<label>Otros</label>
						<p id="otros_tacto_vaginal_ego_resumen"></p>
					</div>
				</div>
				<h4>Especuloscopía</h4>
				<div class="row">
					<div class="col-md-4">
						<label>Vagina</label>
						<p id="vagina_especuloscopia_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Útero</label>
						<p id="utero_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Cérvix</label>
						<p id="cervix_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Fondo de saco</label>
						<p id="fondo_de_saco_especuloscopia_ego_resumen"></p>
					</div>
					<div class="col-md-8">
						<label>Otros</label>
						<p id="otros_especuloscopia_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Recto/Ano</label>
						<p id="recto_ano_ego_resumen"></p>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Examen obstétrico</legend>
				<div class="row">
					<div class="col-md-4">
						<label>Presentación</label>
						<p id="presentacion_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Altura uterina</label>
						<p id="altura_uterina_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Tono</label>
						<p id="tono_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Encajamiento</label>
						<p id="encajamiento_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Dorso</label>
						<p id="dorso_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Contracciones</label><br>
						<p id="contracciones_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>L.C.F.</label>
						<p id="lcf_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Desaceleraciones</label><br>
						<p id="desaceleraciones_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Longitud cuello uterino</label>
						<p id="longitud_cuello_uterino_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Dilatación cuello uterino</label>
						<p id="dilatacion_cuello_uterino_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Membranas</label>
						<p id="membranas_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Líquido amniótico</label>
						<p id="liquido_amniotico_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label>Posición</label>
						<p id="posicion_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Plano</label>
						<p id="plano_ego_resumen"></p>
					</div>
					<div class="col-md-4">
						<label>Evaluación pelvis</label>
						<p id="evaluacion_pelvis_ego_resumen"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Otros</label>
						<p id="otros_examen_obstetrico_ego_resumen"></p>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	@endif
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
			@if($sub_categoria == 1)
			<h4>V. OTROS</h4>
			@else
            <h4>IV. OTROS</h4>
			@endif
        </div>
        <div class="panel-body">
            <div class="col-md-12" id="tdetalleBranula1">
              <legend>Branula 1</legend>
              <div class="col-md-12">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array( ))}}
                        <p id="tnumeroB1" style="text-align: center;"></p>
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array( ))}}
                        <p id="tfechaB1"></p>
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array( ))}}
                        <p id='tlugarB1'></p>
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array( ))}}
                        <p id='tresponsableB1'></p>
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array( ))}}
                        <p id='tcalculoB1' style="text-align: center;"></p>
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12">
              <legend>Branula 2</legend>
              <p id="nohayB2"></p>
              <div class="col-md-12" id="tdetalleBranula2">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array( ))}}
                        <p id='tnumeroB2' style='text-align:center;'></p>
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array( ))}}
                        <p id='tfechaB2'></p>
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array( ))}}
                        <p id='tlugarB2'></p>
                    </div>
                </div>
                <div class="col-cateter-1 col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array( ))}}
                        <p id='tresponsableB2'></p>
                    </div>
                </div>
                <div class="col-cateter-1 col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array( ))}}
                        <p id='tcalculoB2'></p>
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" id="tdetalleFoley">
              <legend>S. Foley</legend>
              <div class="col-md-12">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array())}}
                        <p id='tnumeroF' style='text-align:center'></p>
                    </div>
                </div>
                <div class="col-md-2 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array( ))}}
                        <p id='tfechaF'></p>
                    </div>
                </div>
                <div class="col-md-2 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Lugar de Instalación", array( ))}}
                        <p id='tlugarF'></p>
                    </div>
                </div>
                <div class="col-md-3 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Material de fabricación", array( ))}}
                        <p id='tmaterialF'></p>
                    </div>
                </div>
                <div class="col-md-3 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Mantención", array( ))}}
                        <p id='tfechaCuracionF'></p>
                    </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Curación", array( ))}}
                        <p id='tresponsableF'></p>
                    </div>
                </div>
                <div class="col-md-5 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Observación", array( ))}}
                        <p id='tobservacionF'></p>
                    </div>
                </div>
                <div class="col-md-3 col-cateter-1">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array( ))}}
                        <p id='tcalculoF'></p>
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" id="tdetalleSNG">
                <legend>SNG</legend>
                <div class="col-md-12">
                  <div class="col-md-1">
                      <div class="form-group">
                          {{Form::label('', "Número", array( ))}}
                          <p id='tnumeroSng' style="text-align:center;"></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array( ))}}
                          <p id='tfechaSng'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Lugar de Instalación", array( ))}}
                          <p id='templugarSng'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array( ))}}
                          <p id='tmaterialSng'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array( ))}}
                          <p id='tresponsableSng'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1" style="margin-left: 820px; margin-top: -36px;">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array( ))}}
                          <p id='tcalculoSng'></p>
                      </div>
                  </div>
                </div>
            </div>
            <br>
            <div class="col-md-12" id="tdetalleCVC">
                <legend>CVC</legend>
                <div class="col-md-12">
                  <div class="col-md-1">
                      <div class="form-group">
                          {{Form::label('', "Número", array( ))}}
                          <p id='tnumeroCvc' style="text-align:center;"></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Tipo", array( ))}}
                          <p id='ttipoCvc'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array( ))}}
                          <p id='tfechaCvc'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Lugar de Instalación", array( ))}}
                          <p id='tlugarCvc'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array( ))}}
                          <p id='tmaterialCvc'></p>
                      </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Vía de Instalación", array( ))}}
                          <p id='tviaCvc'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array( ))}}
                          <p id='tresponsableCvc'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array( ))}}
                          <p id='tcalculoCvc'></p>
                      </div>
                  </div>
                </div>
            </div>
            <br>
            <div class="col-md-12" id="tnasoyeyunales">
              <legend>Sondas Nasoyeyunales</legend>
              <div class="col-md-12">
                <div class="col-md-1">
                    <div class="form-group">
                        {{Form::label('', "Número", array( ))}}
                        <p id='tnumeroNasoye' style="text-align:center;"></p>
                    </div>
                </div>
                <div class="col-cateter col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array( ))}}
                        <p id='tfechaNasoye'></p>
                    </div>
                </div>
                <div class="col-md-3 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Responsable de Instalación", array( ))}}
                        <p id='tresponsableNasoye'></p>
                    </div>
                </div>
                <div class="col-md-3 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Calculo de Días Instalada", array( ))}}
                        <p id='tcalculoNasoye'></p>
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" id="ttraqueotomía">
              <legend>Traqueotomía</legend>
              <div class="col-md-12">
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('', "Fecha de Instalación", array( ))}}
                        <p id='tfechaTraqueo'></p>
                    </div>
                </div>
                <div class="col-cateter col-md-3">
                  <div class="form-group">
                      {{Form::label('', "Responsable de Instalación", array( ))}}
                      <p id='tresponsableTraqueo'></p>
                  </div>
                </div>
                <div class="col-md-3 col-cateter">
                  <div class="form-group">
                      {{Form::label('', "Calculo de Días Instalada", array( ))}}
                      <p id='tcalculoTraqueo'></p>
                  </div>
                </div>
                <div class="col-md-3 col-cateter">
                  <div class="form-group">
                      {{Form::label('', "Fecha de cambio de filtro", array( ))}}
                      <p id='tfechaCambioTraqueo'></p>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('', "Medición CUFF", array( ))}}
                        <p id='tcuffTraqueo'></p>
                    </div>
                </div>
                <div class="col-md-8 col-cateter">
                    <div class="form-group">
                        {{Form::label('', "Valoración de estoma y piel de periostomal ", array( ))}}
                        <p id='tveppTraqueo'></p>
                    </div>
                </div>
              </div>
            </div>
            <br>
            <div class="col-md-12" id="tdetalleOstomia">
                <legend>Ostomías</legend>
                <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Tipo", array( ))}}
                          <p id='ttipoOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array( ))}}
                          <p id='tfechaOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Cuidados de Enfermería", array( ))}}
                          <p id='tcuidadoOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Fecha cambio de bolsa", array( ))}}
                          <p id='tfechaCambioOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable (cuidados Enfermeria y cambio bolsa)", array( ))}}
                          <p id='tresponsableOsto'></p>
                      </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Cálculo días cambio de bolsa", array( ))}}
                          <p id='tcalculoBolsaOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Valoración de estoma y piel periostomal", array( ))}}
                          <p id='tvaloracionEstomaOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-4 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Responsable (Valoración de estoma y piel periostomal)", array( ))}}
                          <p id='tcuidadoEstomaOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Medición efluente en cc", array( ))}}
                          <p id='tmedicionEfluenteOsto'></p>
                      </div>
                  </div>
                </div>
                <div class="col-md-13">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Características", array( ))}}
                          <p id='tcaracteristicaOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Detalle de la educación al paciente", array( ))}}
                          <p id='tdetalleEducacionOsto'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter-1">
                    {{Form::label('', "Bagueta", array('class' => 'letraInvasivos' ))}}
                      <div class="input-group">
                        <label class="radio-inline">{{Form::radio('baguetaOsto', "no", false, array('id'=>'tbaguetaOstoNo', 'disabled'))}}No</label>
                        <label class="radio-inline">{{Form::radio('baguetaOsto', "si", false, array('id'=>'tbaguetaOstoSi', 'disabled'))}}Sí</label>
                      </div>
                  </div>
                </div>
            </div>
            <br>
            <div class="col-md-12" id="totro">
                <legend>Otro</legend>
                <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Detalle", array( ))}}
                          <p id='tdetalleOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-1 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Numero", array( ))}}
                          <p id='tnumeroOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Tipo", array( ))}}
                          <p id='ttipoOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Fecha de Instalación", array( ))}}
                          <p id='tfechaOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Lugar de Instalación", array( ))}}
                          <p id='tlugarOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-2 col-cateter-1">
                      <div class="form-group">
                          {{Form::label('', "Material de fabricación", array())}}
                          <p id="tmaterialOtro"></p>
                      </div>
                   </div>
                </div>
                <div class="col-md-12">
                  <div class="col-md-2">
                      <div class="form-group">
                          {{Form::label('', "Vía de Instalación", array( ))}}
                          <p id='tviaOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Responsable de Curación", array( ))}}
                          <p id='treponsablecuraOtro'></p>
                      </div>
                  </div>
                  <div class="col-md-3 col-cateter">
                      <div class="form-group">
                          {{Form::label('', "Calculo de Días Instalada", array( ))}}
                          <p id='tcalculoOtro'></p>
                      </div>
                  </div>
                </div>
            </div>
            {{--}}
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        {{form::label('INDICACIONES: ')}}
                        <p id="informacionEnfermera"></p>
                    </div>
                    <div class="form-group">
                        {{form::label('EXAMENES: ')}}
                        <p id="texamenes"></p>
                    </div>
                </div>
            </div>
            --}}
        </div>
    </div>
    </div>
</fieldset>

{{-- <div class="container" style="padding-left: 0px;">
    <ul  class="nav nav-pills  tercerNav">
        <li id="IeHa" class="active" >
            <a  href="#Ha1" data-toggle="tab">Histórico Anamnesis</a>
        </li>
        <li id="IeHg">
            <a href="#Hs1" data-toggle="tab">Histórico Examen Físico General</a>
        </li>
        <li id="IeHs">
            <a href="#Hg1" data-toggle="tab">Histórico Examen Físico Segmentario</a>
        </li>
        <li id="IeHo">
            <a href="#Ho1" data-toggle="tab">Histórico Otros</a>
        </li>
    </ul>

    <div class="tab-content clearfix">
        <br>
        {{ HTML::link("gestionEnfermeria/$caso/obtenerHistorialIngresoEnfermeria", 'Historico', ['class' => 'btn btn-danger']) }}
        {{ HTML::link(URL::route('excelListaDerivados'), 'Historico', ['class' => 'btn btn-danger']) }}
        <div class="tab-pane active" id="Ha1">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.resumen.historicoAnamnesis')
        </div>
        <div class="tab-pane" id="Hs1">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.resumen.historicoExamenFisicoGeneral')
        </div>
        <div class="tab-pane" id="Hg1">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.resumen.historicoExamenFisicoSegmentario')
        </div>
        <div class="tab-pane" id="Ho1">
            @include('Gestion.gestionEnfermeria.partesIngresoEnfermeria.resumen.historicoOtros')
        </div>
    </div>

</div> --}}

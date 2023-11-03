@extends("Templates/template")

@section("titulo")
Formulario
@stop

@section("section")

    <script>
        var caso_id = '{{$formulario_data->caso_id}}';
    </script>
    <!-- alertas de alergias logica-->
    {{ HTML::script('js/formularios_ginecologia/partograma-alergias-notificator.js') }}

    <style>
        .help-block {
            color: #a94442;
        }
    </style>

    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
    <br><br>
    {{ 
        Form::open(
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'solicitud_transfusion_productos_sanguineos_form')
        ) 
    }}

    <br>

    <!-- -->

    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Solicitud de transfusi&oacute;n de productos sangu&iacute;neos</h4>
        </div>

        <div class="panel-body">

            <div class="row">

                <div class="col-md-12">
                    <h5><strong>Identificaci&oacute;n del paciente</strong></h5>
                </div>
            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_nombre">Nombre</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_nombre" disabled>                                          
                    </div>                
                
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_apellido">Apellido</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_apellido" disabled>                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_rut">Rut</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_rut" disabled>                                          
                    </div>                
                
                </div>

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ficha">Ficha</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ficha" disabled>                                          
                    </div>                
                
                </div>

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_edad">Edad</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_edad" disabled>                                          
                    </div>                
                
                </div>

                <div class="col-md-3">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_servicio">Servicio</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_servicio" disabled>                                          
                    </div>                
                
                </div>
                
            </div>


            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_sala">Sala</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_sala" disabled>                                          
                    </div>                
                
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_cama">Cama</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_cama" disabled>                                          
                    </div>                
                
                </div>
                
            </div>


            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_diagnostico">Diagn&oacute;stico</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_diagnostico" name="solicitud_transfusion_productos_sanguineos_diagnostico">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_trans_previas">Trans. previas</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_trans_previas" name="solicitud_transfusion_productos_sanguineos_trans_previas">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_reacciones_transfusiones">Reacciones transfusiones</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_reacciones_transfusiones" name="solicitud_transfusion_productos_sanguineos_reacciones_transfusiones">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_numero_embarazos">N° Embarazos</label>
                        <input type="number" class="form-control" id="solicitud_transfusion_productos_sanguineos_numero_embarazos" name="solicitud_transfusion_productos_sanguineos_numero_embarazos" step="1" min="0">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ttpa">TTPA</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ttpa" name="solicitud_transfusion_productos_sanguineos_ttpa">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_tp">TP</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_tp" name="solicitud_transfusion_productos_sanguineos_tp">                                          
                    </div>                
                
                </div>


            </div>


            <div class="row">

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_plaq">Plaq</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_plaq" name="solicitud_transfusion_productos_sanguineos_plaq">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_hb">Hb</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_hb" name="solicitud_transfusion_productos_sanguineos_hb">                                          
                    </div>                
                
                </div>

                <div class="col-md-4">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_hto">Hto</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_hto" name="solicitud_transfusion_productos_sanguineos_hto">                                          
                    </div>                
                
                </div>


            </div>

            <hr>

            <div class="row">

                <div class="col-md-12">
                    <p><strong>G. rojos</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_g_rojos_cantidad">Cantidad</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_g_rojos_cantidad" name="solicitud_transfusion_productos_sanguineos_g_rojos_cantidad">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_g_rojos_horario">Horario</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_g_rojos_horario" name="solicitud_transfusion_productos_sanguineos_g_rojos_horario" 
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_g_rojos_observaciones">Observaciones</label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_g_rojos_observaciones" id="solicitud_transfusion_productos_sanguineos_g_rojos_observaciones"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>


            </div>  

            <div class="row">

                <div class="col-md-12">
                    <p><strong>P. Fresco</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_p_fresco_cantidad">Cantidad</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_p_fresco_cantidad" name="solicitud_transfusion_productos_sanguineos_p_fresco_cantidad">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_p_fresco_horario">Horario</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_p_fresco_horario" name="solicitud_transfusion_productos_sanguineos_p_fresco_horario"
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_p_fresco_observaciones">Observaciones</label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_p_fresco_observaciones" id="solicitud_transfusion_productos_sanguineos_p_fresco_observaciones"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>


            </div>  

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Plaquetas</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_plaquetas_cantidad">Cantidad</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_plaquetas_cantidad" name="solicitud_transfusion_productos_sanguineos_plaquetas_cantidad">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_plaquetas_horario">Horario</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_plaquetas_horario" name="solicitud_transfusion_productos_sanguineos_plaquetas_horario"
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_plaquetas_observaciones">Observaciones</label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_plaquetas_observaciones" id="solicitud_transfusion_productos_sanguineos_plaquetas_observaciones"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>


            </div>  

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Crioprec.</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_crioprec_cantidad">Cantidad</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_crioprec_cantidad" name="solicitud_transfusion_productos_sanguineos_crioprec_cantidad">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_crioprec_horario">Horario</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_crioprec_horario" name="solicitud_transfusion_productos_sanguineos_crioprec_horario"
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_crioprec_observaciones">Observaciones</label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_crioprec_observaciones" id="solicitud_transfusion_productos_sanguineos_crioprec_observaciones"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>


            </div>            

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Exsanguineot.</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad">Cantidad</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad" name="solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_exsanguineot_horario">Horario</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_exsanguineot_horario" name="solicitud_transfusion_productos_sanguineos_exsanguineot_horario"
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones">Observaciones</label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones" id="solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>


            </div>  


            <div class="row">
            

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_nombre">Leucorreducidos</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_leucorreducidos" name="solicitud_transfusion_productos_sanguineos_leucorreducidos">                                          
                    </div>                
                
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_nombre">Irradiado</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_irradiado" name="solicitud_transfusion_productos_sanguineos_irradiado">                                          
                    </div>                
                
                </div>
                
            </div> 


            <div class="row">

                <div class="col-md-12">
                    <h5><p><strong>Recepci&oacute;n</strong></p>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_recepcion_responsable">Responsable</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_recepcion_responsable" name="solicitud_transfusion_productos_sanguineos_recepcion_responsable">                                          
                    </div>                
                
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora">Fecha y hora</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora" name="solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora"
                        onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                    </div>                
                
                </div>
                
            </div>  

            <div class="row">

                <div class="col-md-12">

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="solicitud_transfusion_productos_sanguineos_nivel_urgencia" id="solicitud_transfusion_productos_sanguineos_inmediata" value="inmediata" >
                        <label  class="form-check-label" for="solicitud_transfusion_productos_sanguineos_inmediata" style="font-weight: 100;">
                            Inmediata
                            (Sin pruebas cruzadas, sin clasif, ABO Rh(D) Sin Ac. Irregulares)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="solicitud_transfusion_productos_sanguineos_nivel_urgencia" id="solicitud_transfusion_productos_sanguineos_urgente" value="urgente" >
                        <label class="form-check-label" for="solicitud_transfusion_productos_sanguineos_urgente" style="font-weight: 100;">
                            Urgente (Con pruebas cruzadas en 90 minutos)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="solicitud_transfusion_productos_sanguineos_nivel_urgencia" id="solicitud_transfusion_productos_sanguineos_no_urgente" value="no_urgente" >
                        <label class="form-check-label" for="solicitud_transfusion_productos_sanguineos_no_urgente" style="font-weight: 100;">
                            No urgente (Con pruebas cruzadas en 24 horas)
                        </label>
                    </div>

                </div>
            
            </div>  

            <div class="row">

                <div class="col-md-6">
                    <br>
                    <div class="col-md-12 form-group">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="reserva_pabellon" id="solicitud_transfusion_productos_sanguineos_reserva_pabellon" name="solicitud_transfusion_productos_sanguineos_reserva_pabellon">
                            <label class="form-check-label" for="solicitud_transfusion_productos_sanguineos_reserva_pabellon" style="font-weight: 100;">
                                Reserva de pabell&oacute;n
                            </label>
                        </div>

                    </div>                
                
                </div>

                <div class="col-md-6 solicitud_transfusion_productos_sanguineos_reserva_pabellon_group">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora">Fecha y hora</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora" name="solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora"
                        onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                    </div>                
                
                </div>

            </div>


            <div class="row">

                <div class="col-md-6 solicitud_transfusion_productos_sanguineos_reserva_pabellon_group">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_medico_responsable">M&eacute;dico responsable</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_medico_responsable" name="solicitud_transfusion_productos_sanguineos_medico_responsable">                                          
                    </div>                
                
                </div>

                <div class="col-md-6 solicitud_transfusion_productos_sanguineos_reserva_pabellon_group">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora">Fecha y hora solicitud</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora" name="solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora"
                        onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_observaciones">Observaciones</label>
                        <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_observaciones" id="solicitud_transfusion_productos_sanguineos_observaciones"></textarea>
                    </div>                
                </div>

            </div> 

            <div class="row">

                <div class="col-md-12">
                    <h5><p><strong>Estudios inmunohematol&oacute;gicos</strong></p>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Clasif. ABO Rh (D)</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha">Fecha</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha" name="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha"
                        onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                    </div>                
                
                </div>


                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp">Resp</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp" name="solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Reclasif. ABO Rh (D)</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha">Fecha</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha" name="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha"
                        onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                    </div>                
                
                </div>


                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp">Resp</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp" name="solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <p><strong>Ac. Irregulares.</strong></p>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd">TCD</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd" name="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd">                                          
                    </div>                
                
                </div>


                <div class="col-md-6">
                    <div class="col-md-12 form-group">
                        <label for="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca">CA</label>
                        <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca" name="solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca">                                          
                    </div>                
                
                </div>
                
            </div>

            <div class="row">

                <div class="col-md-12">
                    <h5><p><strong>Unidades compatibles</strong></p>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles"></label>
                                <textarea class="form-control" rows="5" name="solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles" id="solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles"></textarea>
                            </div>                
                        </div>

                    </div>              
                
                </div>

                <div class="col-md-6">


                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_uc_hora">Hora</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_uc_hora" name="solicitud_transfusion_productos_sanguineos_uc_hora" placeholder="HH:mm:ss"
                                onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                            </div>                

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="solicitud_transfusion_productos_sanguineos_uc_resp">Resp</label>
                                <input type="text" class="form-control" id="solicitud_transfusion_productos_sanguineos_uc_resp" name="solicitud_transfusion_productos_sanguineos_uc_resp">                                          
                            </div>                

                        </div>

                    </div>

                
                </div>


            </div>


            <div class="row">

                <div class="col-md-12">
                    <h5><p><strong>Instalaci&oacute;n</strong></p>
                </div>

                <div class="col-md-12" id="container_instalacion">
                
                
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" >
                <input id="solicitud_transfusion_productos_sanguineos_agregar_instalacion" type="button" name="" class="btn btn-primary" value="Agregar instalación">
                </div>
            </div>

        </div>
    </div>




    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="solicitud_transfusion_productos_sanguineos_save" type="button" name="" class="btn btn-primary" value="Guardar">
    <input id="solicitud_transfusion_productos_sanguineos_save_pdf" type="button" class="btn btn-primary" value="Guardar e imprimir">
    @endif

    @if (isset($formulario_data->form_id))
    <input id="solicitud_transfusion_productos_sanguineos_save" type="button" name="" class="btn btn-primary" value="Modificar">
    <input id="solicitud_transfusion_productos_sanguineos_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">
    <a id="solicitud_transfusion_productos_sanguineos_pdf" href="{{url('formularios-ginecologia/solicitud-transfusion-productos-sanguineos/pdf/'.$formulario_data->caso_id)}}" target="_blank" class="btn btn-primary">Imprimir</a>
    @endif


    <!-- instalacion clone template -->
    <div hidden>
        <div class="row instalacion_row" >
            <div class="hidden_inputs" hidden>
                <input name="solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion[]" type="hidden" >
            </div>
            <div class="col-md-12">
                <div class="row">

                    <div class="col-md-4">
                        <div class="col-md-12 form-group">
                            <label for="">Fecha y hora</label>
                            <input type="text" class="form-control solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora" name="solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora[]"
                            onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                        </div>                

                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 form-group">
                            <label for="">N° Matraz</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_n_matraz[]">                                          
                        </div>                         
                    
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 form-group">
                            <label for="">Grupo ABO</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo[]">                                          
                        </div>                         
                    
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">PSL</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_psl[]">                                          
                        </div>                      
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Cantidad</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_cantidad[]">                                          
                        </div>                     
                    </div>

                </div>

                <div class="row">
                
                    <div class="col-md-2" style="padding-right: 0px;">
                        <div class="col-md-12 form-group">
                            <label for="">T°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_t[]" min="34" step="0.1">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">Pulso</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pulso[]" step="1" min="10" max="300">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">P.arterial</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_p_arterial[]">                                          
                        </div>                    
                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Responsable</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_responsable[]">                                          
                        </div>                    
                    </div>
                
                </div>

                <div class="row">
                
                    <div class="col-md-2" style="padding-right: 0px;">
                        <div class="col-md-12 form-group">
                            <label for="">T° 10°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_t_10[]" min="34" step="0.1">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">Pulso 10°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pulso_10[]" step="1" min="10" max="300">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">PA 10°</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pa_10[]">                                          
                        </div>                    
                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Responsable</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_responsable_10[]">                                          
                        </div>                    
                    </div>
                
                </div>

                <div class="row">
                
                    <div class="col-md-2" style="padding-right: 0px;">
                        <div class="col-md-12 form-group">
                            <label for="">T° 30°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_t_30[]" min="34" step="0.1">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">Pulso 30°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pulso_30[]" min="10" step="1" max="300">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">PA 30°</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pa_30[]">                                          
                        </div>                    
                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Hora</label>
                            <input type="text" class="form-control solicitud_transfusion_productos_sanguineos_instalacion_hora" name="solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora[]" placeholder="HH:mm:ss"
                            onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                        </div>                

                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Responsable</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_responsable_30[]">                                          
                        </div>                    
                    </div>
                
                </div>

                <div class="row">
                
                    <div class="col-md-2" style="padding-right: 0px;">
                        <div class="col-md-12 form-group">
                            <label for="">T° 60°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_t_60[]" min="34" step="0.1">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">Pulso 60°</label>
                            <input type="number" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pulso_60[]" step="1" min="10" max="300">                                          
                        </div>                     
                    </div>

                    <div class="col-md-2">
                        <div class="col-md-12 form-group">
                            <label for="">PA 60°</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_pa_60[]">                                          
                        </div>                    
                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Hora</label>
                            <input type="text" class="form-control solicitud_transfusion_productos_sanguineos_instalacion_hora" name="solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora[]" placeholder="HH:mm:ss"
                            onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                        </div>                

                    </div>

                    <div class="col-md-3">
                        <div class="col-md-12 form-group">
                            <label for="">Responsable</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_responsable_60[]">                                          
                        </div>                    
                    </div>
                
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Reacci&oacute;n adversa transfusional</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional[]">                                          
                        </div>                      
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Folio ficha R.A.T</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat[]">                                          
                        </div>                     
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Tratamiento</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_tratamiento[]">                                          
                        </div>                      
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">M&eacute;dico responsable</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable[]">                                          
                        </div>                     
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Responsable toma de muestra</label>
                            <input type="text" class="form-control" name="solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra[]">                                          
                        </div>                      
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label for="">Hora</label>
                            <input type="text" class="form-control solicitud_transfusion_productos_sanguineos_instalacion_hora" name="solicitud_transfusion_productos_sanguineos_instalacion_hora[]" placeholder="HH:mm:ss"
                            onkeydown="notInputExceptBackSpace(event);" onPaste="return false">                                          
                        </div>                                          
                    </div>

                </div>


                <hr>
            </div>
        </div>
    </div>


    {!! HTML::script('js/formularios_ginecologia/helper.js') !!}
    <script>


        $.fn.bootstrapValidator.validators._g_rojos_cantidad_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_g_rojos_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_g_rojos_horario").val().length;
                if(cantL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        };


        $.fn.bootstrapValidator.validators._g_rojos_horario_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_g_rojos_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_g_rojos_horario").val().length;
                if(cantL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;

            }
        };

        $.fn.bootstrapValidator.validators._p_fresco_cantidad_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_p_fresco_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_p_fresco_horario").val().length;
                if(cantL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        };

        $.fn.bootstrapValidator.validators._p_fresco_horario_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_p_fresco_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_p_fresco_horario").val().length;
                if(cantL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._plaquetas_cantidad_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_plaquetas_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_plaquetas_horario").val().length;
                if(cantL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        };

        $.fn.bootstrapValidator.validators._plaquetas_horario_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_plaquetas_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_plaquetas_horario").val().length;
                if(cantL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };


        $.fn.bootstrapValidator.validators._crioprec_cantidad_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_crioprec_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_crioprec_horario").val().length;
                if(cantL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        };

        $.fn.bootstrapValidator.validators._crioprec_horario_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_crioprec_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_crioprec_horario").val().length;
                if(cantL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };


        $.fn.bootstrapValidator.validators._exsanguineot_cantidad_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_exsanguineot_horario").val().length;
                if(cantL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        };

        $.fn.bootstrapValidator.validators._exsanguineot_horario_not_null = {
            validate: function(validator, $field, options) {
                var cantL = $("#solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_exsanguineot_horario").val().length;
                if(cantL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._reserva_pabellon_not_null = {
            validate: function(validator, $field, options) {
                var is_check = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon").is(":checked");
                var horario_reserva_L = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora").val().length;
                var med_responsable_L = $("#solicitud_transfusion_productos_sanguineos_medico_responsable").val().length;
                var horario_solicitud_L = $("#solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora").val().length;
                if(!is_check && (horario_reserva_L > 0 || med_responsable_L > 0 || horario_solicitud_L > 0)  ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._reserva_pabellon_horario_not_null = {
            validate: function(validator, $field, options) {
                var is_check = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon").is(":checked");
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora").val().length;
                if(is_check && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._medico_responsable_not_null = {
            validate: function(validator, $field, options) {
                var is_check = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon").is(":checked");
                var medico_resposable_L = $("#solicitud_transfusion_productos_sanguineos_medico_responsable").val().length;
                if(is_check && medico_resposable_L === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._solicitud_fecha_hora_not_null = {
            validate: function(validator, $field, options) {
                var is_check = $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon").is(":checked");
                var solicitud_fecha_hora_L = $("#solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora").val().length;
                if(is_check && solicitud_fecha_hora_L === 0 ){
                    return false;
                }

                return true;
                
            }
        };


        $.fn.bootstrapValidator.validators._ei_clasif_abo_rh_d_fecha_not_null = {
            validate: function(validator, $field, options) {
                var fecL = $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha").val().length;
                var respL = $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp").val().length;
                if(fecL === 0 && respL > 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._ei_reclasif_abo_rh_d_fecha_not_null = {
            validate: function(validator, $field, options) {
                var fecL = $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha").val().length;
                var respL = $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp").val().length;
                if(fecL === 0 && respL > 0 ){
                    return false;
                }

                return true;
                
            }
        };


        $.fn.bootstrapValidator.validators._solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp_not_null = {
            validate: function(validator, $field, options) {
                var fecL = $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha").val().length;
                var respL = $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp").val().length;
                if(fecL > 0 && respL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha_not_null = {
            validate: function(validator, $field, options) {
                var fecL = $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha").val().length;
                var respL = $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp").val().length;
                if(fecL > 0 && respL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._recepcion_fecha_hora_not_null = {
            validate: function(validator, $field, options) {
                var resL = $("#solicitud_transfusion_productos_sanguineos_recepcion_responsable").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora").val().length;
                if(resL > 0 && horarioL === 0 ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._recepcion_responsable_not_null = {
            validate: function(validator, $field, options) {
                var resL = $("#solicitud_transfusion_productos_sanguineos_recepcion_responsable").val().length;
                var horarioL = $("#solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora").val().length;
                if(resL === 0 && horarioL > 0 ){
                    return false;
                }

                return true;

            }
        }; 

        $.fn.bootstrapValidator.validators._uc_unidades_compatibles_not_null = {
            validate: function(validator, $field, options) {
                var uc_unidades_compatibles_L = $("#solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles").val().length;
                var uc_hora_L = $("#solicitud_transfusion_productos_sanguineos_uc_hora").val().length;
                var uc_resp_L = $("#solicitud_transfusion_productos_sanguineos_uc_resp").val().length;
                if(uc_unidades_compatibles_L === 0 && (uc_hora_L > 0 || uc_resp_L > 0)  ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._uc_hora_not_null = {
            validate: function(validator, $field, options) {
                var uc_unidades_compatibles_L = $("#solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles").val().length;
                var uc_hora_L = $("#solicitud_transfusion_productos_sanguineos_uc_hora").val().length;
                var uc_resp_L = $("#solicitud_transfusion_productos_sanguineos_uc_resp").val().length;
                if(uc_hora_L === 0 && (uc_unidades_compatibles_L > 0 || uc_resp_L > 0)  ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._uc_resp_not_null = {
            validate: function(validator, $field, options) {
                var uc_unidades_compatibles_L = $("#solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles").val().length;
                var uc_hora_L = $("#solicitud_transfusion_productos_sanguineos_uc_hora").val().length;
                var uc_resp_L = $("#solicitud_transfusion_productos_sanguineos_uc_resp").val().length;
                if(uc_resp_L === 0 && (uc_unidades_compatibles_L > 0 || uc_hora_L > 0)  ){
                    return false;
                }

                return true;
                
            }
        };

        $.fn.bootstrapValidator.validators._only_1_decimal = {
            validate: function(validator, $field, options) {
                var inputL = $($field).val().length;
                if(inputL === 0){ return true;}
                else {
                    try {
                        var number = parseFloat($($field).val());
                        var decimals = number.countDecimals();
                        return decimals <=1;

                    } catch (error) {
                        return false;
                    }
                }
            }
        };

        const bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)'],
            fields: {
                'solicitud_transfusion_productos_sanguineos_diagnostico': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_trans_previas': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_reacciones_transfusiones': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_numero_embarazos': {
                    validators:{
                        between: {
                            min: 0,
                            max: 20,
                            message: 'El rango permitido es entre 0 y 20'
                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ttpa': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_tp': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_plaq': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_hb': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_hto': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_g_rojos_cantidad': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _g_rojos_cantidad_not_null: {
                            message: 'Al existir horario, cantidad no puede ser vacío.'
                        },
                    }
                },

                'solicitud_transfusion_productos_sanguineos_g_rojos_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_p_fresco_cantidad': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _p_fresco_cantidad_not_null: {
                            message: 'Al existir horario, cantidad no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_p_fresco_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_plaquetas_cantidad': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _plaquetas_cantidad_not_null: {
                            message: 'Al existir horario, cantidad no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_plaquetas_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_crioprec_cantidad': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _crioprec_cantidad_not_null: {
                            message: 'Al existir horario, cantidad no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_crioprec_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _exsanguineot_cantidad_not_null: {
                            message: 'Al existir horario, cantidad no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_g_rojos_horario' : {
                    validators:{
                        _g_rojos_horario_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },

                    }
                },
                'solicitud_transfusion_productos_sanguineos_p_fresco_horario' : {
                    validators:{
                        _p_fresco_horario_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_plaquetas_horario' : {
                    validators:{
                        _plaquetas_horario_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_crioprec_horario' : {
                    validators:{
                        _crioprec_horario_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_exsanguineot_horario' : {
                    validators:{
                        _exsanguineot_horario_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora' : {
                    validators:{

                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },

                        _recepcion_fecha_hora_not_null: {
                            message: 'Debe seleccionar un horario.'

                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_nivel_urgencia' : {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_reserva_pabellon' : {
                    validators:{
                        _reserva_pabellon_not_null: {
                            message: 'Debe seleccionar reserva de pabellón.'

                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora' : {
                    validators:{
                        _reserva_pabellon_horario_not_null: {
                            message: 'Debe seleccionar un horario.'

                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora' : {
                    validators:{
                        _not_future_datetime: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                        _solicitud_fecha_hora_not_null: {
                            message: 'Al existir reserva de pabellon, solicitud fecha y hora no puede ser vacío.'
                        },
                    }
                },

                'solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha' : {
                    validators:{

                        _not_future_date: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                        _ei_clasif_abo_rh_d_fecha_not_null: {
                            message: 'Al existir Resp, solicitud fecha no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha' : {
                    validators:{

                        _not_future_date: {
                            message: 'No se permiten fechas futuras o el formato es incorrecto.'
                        },
                        _ei_reclasif_abo_rh_d_fecha_not_null: {
                            message: 'Al existir Resp, solicitud fecha no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_leucorreducidos': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_irradiado': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_recepcion_responsable': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _recepcion_responsable_not_null: {
                            message: 'Al existir horario, responsable no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_medico_responsable': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _medico_responsable_not_null: {
                            message: 'Al existir reserva de pabellon, responsable no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_observaciones': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp_not_null: {
                            message: 'Al existir horario, resp no puede ser vacío.'
                        },                        
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha_not_null: {
                            message: 'Al existir cantidad, horario no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles': {
                    validators:{
                        stringLength: {
                            max: 300,
                            message: 'Máximo 300 caracteres.'
                        },
                        _uc_unidades_compatibles_not_null: {
                            message: 'Unidades compatibles no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_uc_hora': {
                    validators:{
                        _uc_hora_not_null: {
                            message: 'Uc hora no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_uc_resp': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                        _uc_resp_not_null: {
                            message: 'Resp no puede ser vacío.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_n_matraz[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo[]': {
                    validators:{
                        notEmpty: {
                            message: 'Campo obligatorio'
                        }
                    }
                },
                //
                'solicitud_transfusion_productos_sanguineos_instalacion_psl[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_cantidad[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_t[]': {
                    validators:{
                        _only_1_decimal: {
                            message: 'Solo se permite a lo mas un decimal.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso[]': {
                    validators:{

                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_p_arterial[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_t_10[]': {
                    validators:{
                        _only_1_decimal: {
                            message: 'Solo se permite a lo mas un decimal.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_10[]': {
                    validators:{

                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_10[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_10[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_t_30[]': {
                    validators:{
                        _only_1_decimal: {
                            message: 'Solo se permite a lo mas un decimal.'
                        },
                    }
                },   
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_30[]': {
                    validators:{

                    }
                },  
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_30[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },   
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_30[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },  
                'solicitud_transfusion_productos_sanguineos_instalacion_t_60[]': {
                    validators:{
                        _only_1_decimal: {
                            message: 'Solo se permite a lo mas un decimal.'
                        },
                    }
                },    
                'solicitud_transfusion_productos_sanguineos_instalacion_pulso_60[]': {
                    validators:{

                    }
                }, 
                'solicitud_transfusion_productos_sanguineos_instalacion_pa_60[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_60[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },  
                'solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },     
                'solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                }, 
                'solicitud_transfusion_productos_sanguineos_instalacion_tratamiento[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                }, 
                'solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
                'solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra[]': {
                    validators:{
                        stringLength: {
                            max: 60,
                            message: 'Máximo 60 caracteres.'
                        },
                    }
                },
            }
        };
        
        
        function load() {
            $("#solicitud_transfusion_productos_sanguineos_rut").val({!! json_encode($formulario_data->run_dv) !!});
            $("#solicitud_transfusion_productos_sanguineos_ficha").val({!! json_encode($formulario_data->ficha_clinica) !!});
            $("#solicitud_transfusion_productos_sanguineos_nombre").val({!! json_encode($formulario_data->paciente_nombre) !!});
            $("#solicitud_transfusion_productos_sanguineos_apellido").val({!! json_encode($formulario_data->paciente_apellido_paterno) !!});
            $("#solicitud_transfusion_productos_sanguineos_edad").val({!! json_encode($formulario_data->paciente_edad) !!});
            $("#solicitud_transfusion_productos_sanguineos_servicio").val({!! json_encode($formulario_data->unidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_sala").val({!! json_encode($formulario_data->sala) !!});
            $("#solicitud_transfusion_productos_sanguineos_cama").val({!! json_encode($formulario_data->cama) !!});

            @if (isset($formulario_data->form_id))

            $("#solicitud_transfusion_productos_sanguineos_diagnostico").val({!! json_encode($formulario_data->diagnostico) !!});
            $("#solicitud_transfusion_productos_sanguineos_trans_previas").val({!! json_encode($formulario_data->transf_previas) !!});
            $("#solicitud_transfusion_productos_sanguineos_reacciones_transfusiones").val({!! json_encode($formulario_data->reacciones_transfusiones) !!});
            $("#solicitud_transfusion_productos_sanguineos_numero_embarazos").val({!! json_encode($formulario_data->n_embarazos) !!});
            $("#solicitud_transfusion_productos_sanguineos_ttpa").val({!! json_encode($formulario_data->ttpa) !!});
            $("#solicitud_transfusion_productos_sanguineos_tp").val({!! json_encode($formulario_data->tp) !!});
            $("#solicitud_transfusion_productos_sanguineos_plaq").val({!! json_encode($formulario_data->plaq) !!});
            $("#solicitud_transfusion_productos_sanguineos_hb").val({!! json_encode($formulario_data->hb) !!});
            $("#solicitud_transfusion_productos_sanguineos_hto").val({!! json_encode($formulario_data->hto) !!});
            $("#solicitud_transfusion_productos_sanguineos_g_rojos_cantidad").val({!! json_encode($formulario_data->g_rojos_cantidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_g_rojos_horario").data("DateTimePicker").date("{{$formulario_data->g_rojos_horario}}");
            $("#solicitud_transfusion_productos_sanguineos_g_rojos_observaciones").val({!! json_encode($formulario_data->g_rojos_observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_p_fresco_cantidad").val({!! json_encode($formulario_data->p_fresco_cantidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_p_fresco_horario").data("DateTimePicker").date("{{$formulario_data->p_fresco_horario}}");
            $("#solicitud_transfusion_productos_sanguineos_p_fresco_observaciones").val({!! json_encode($formulario_data->p_fresco_observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_plaquetas_cantidad").val({!! json_encode($formulario_data->plaquetas_cantidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_plaquetas_horario").data("DateTimePicker").date("{{$formulario_data->plaquetas_horario}}");
            $("#solicitud_transfusion_productos_sanguineos_plaquetas_observaciones").val({!! json_encode($formulario_data->plaquetas_observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_crioprec_cantidad").val({!! json_encode($formulario_data->crioprec_cantidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_crioprec_horario").data("DateTimePicker").date("{{$formulario_data->crioprec_horario}}");
            $("#solicitud_transfusion_productos_sanguineos_crioprec_observaciones").val({!! json_encode($formulario_data->crioprec_observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_exsanguineot_cantidad").val({!! json_encode($formulario_data->exsanguineot_cantidad) !!});
            $("#solicitud_transfusion_productos_sanguineos_exsanguineot_horario").data("DateTimePicker").date("{{$formulario_data->exsanguineot_horario}}");
            $("#solicitud_transfusion_productos_sanguineos_exsanguineot_observaciones").val({!! json_encode($formulario_data->exsanguineot_observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_leucorreducidos").val({!! json_encode($formulario_data->leucorreducidos) !!});
            $("#solicitud_transfusion_productos_sanguineos_irradiado").val({!! json_encode($formulario_data->irradiado) !!});
            $("#solicitud_transfusion_productos_sanguineos_recepcion_responsable").val({!! json_encode($formulario_data->responsable_recepcion) !!});
            $("#solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora").data("DateTimePicker").date("{{$formulario_data->fecha_recepcion}}");
            jQuery('input:radio[name="solicitud_transfusion_productos_sanguineos_nivel_urgencia"]').filter('[value="{{$formulario_data->gravedad}}"]').prop('checked', true).trigger("change");  
            @if ($formulario_data->reserva_pabellon === "si")
            $('#solicitud_transfusion_productos_sanguineos_reserva_pabellon').prop('checked', true);
            $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora").data("DateTimePicker").date("{{$formulario_data->fecha_reserva_pabellon}}");
            $("#solicitud_transfusion_productos_sanguineos_medico_responsable").val({!! json_encode($formulario_data->medico_responsable) !!});
            $("#solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora").data("DateTimePicker").date("{{$formulario_data->fecha_solicitud}}");
            @endif

            $("#solicitud_transfusion_productos_sanguineos_observaciones").val({!! json_encode($formulario_data->observaciones) !!});
            $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha").data("DateTimePicker").date("{{$formulario_data->clasific_abo_fecha}}");
            $("#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_resp").val({!! json_encode($formulario_data->clasific_abo_resp) !!});
            $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha").data("DateTimePicker").date("{{$formulario_data->reclasific_abo_fecha}}");
            $("#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_resp").val({!! json_encode($formulario_data->reclasific_abo_resp) !!});
            $("#solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_tcd").val({!! json_encode($formulario_data->ac_irregulares_tcd) !!});
            $("#solicitud_transfusion_productos_sanguineos_ei_ac_irregulares_ca").val({!! json_encode($formulario_data->ac_irregulares_ca) !!});
            $("#solicitud_transfusion_productos_sanguineos_uc_unidades_compatibles").val({!! json_encode($formulario_data->unidades_compatibles) !!});
            $("#solicitud_transfusion_productos_sanguineos_uc_hora").data("DateTimePicker").date("{{$formulario_data->unidades_compatibles_hora}}");
            $("#solicitud_transfusion_productos_sanguineos_uc_resp").val({!! json_encode($formulario_data->unidades_compatibles_resp) !!});

            //instalaciones
            @foreach ($formulario_data->instalaciones as $instalacion)
            
            addInstalacion();
            
            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$instalacion->fecha_instalacion}}");

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_n_matraz[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->n_maltraz) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->grupo_abo) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_psl[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->psl) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_cantidad[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->cantidad) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_t[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->temp) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pulso[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->pulso) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_p_arterial[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->p_arterial) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_responsable[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->responsable) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_t_10[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->temp_10) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pulso_10[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->pulso_10) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pa_10[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->p_arterial_10) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_responsable_10[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->responsable_10) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_t_30[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->temp_30) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pulso_30[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->pulso_30) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pa_30[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->p_arterial_30) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pa_30_hora[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$instalacion->hora_30}}");

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_responsable_30[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->responsable_30) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_t_60[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->temp_60) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pulso_60[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->pulso_60) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pa_60[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->p_arterial_60) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_pa_60_hora[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$instalacion->hora_60}}");

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_responsable_60[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->responsable_60) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->reaccion_adversa_transfusional) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->folio_ficha_rat) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_tratamiento[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->tratamiento) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->medico_responsable) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->responsable_toma_muestra) !!});

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_hora[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$instalacion->hora}}");

            $("[name='solicitud_transfusion_productos_sanguineos_instalacion_id_instalacion[]']").eq({{$loop->index}}).val({!! json_encode($instalacion->id_formulario_solicitud_transfusion_instalacion) !!});


            @endforeach

            initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            @endif

        }

        function addInstalacion(){

            var $instalacion_row = $('.instalacion_row').clone();
            $('#container_instalacion').html($instalacion_row);

            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_n_matraz[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_grupo_abo[]');
            
            //
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_psl[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_cantidad[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_t[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pulso[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_p_arterial[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_responsable[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_t_10[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pulso_10[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pa_10[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_responsable_10[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_t_30[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pulso_30[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pa_30[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_responsable_30[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_t_60[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pulso_60[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_pa_60[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_responsable_60[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_reaccion_adversa_transfusional[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_folio_ficha_rat[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_tratamiento[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_medico_responsable[]');
            $('#solicitud_transfusion_productos_sanguineos_form').bootstrapValidator('addField', 'solicitud_transfusion_productos_sanguineos_instalacion_responsable_toma_de_muestra[]');


            initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));

            $('.solicitud_transfusion_productos_sanguineos_instalacion_fecha_hora').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('.solicitud_transfusion_productos_sanguineos_instalacion_hora').datetimepicker({
                format: 'HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

        }

        $(document).ready(function() { 


            $('#solicitud_transfusion_productos_sanguineos_g_rojos_horario').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_p_fresco_horario').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });
        
            $('#solicitud_transfusion_productos_sanguineos_plaquetas_horario').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_crioprec_horario').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_exsanguineot_horario').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_recepcion_fecha_hora').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_ei_clasif_abo_rh_d_fecha').datetimepicker({
                format: 'DD/MM/YYYY',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_ei_reclasif_abo_rh_d_fecha').datetimepicker({
                format: 'DD/MM/YYYY',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            $('#solicitud_transfusion_productos_sanguineos_uc_hora').datetimepicker({
                format: 'HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));
            });

            //

            $(document).on('change', '#solicitud_transfusion_productos_sanguineos_reserva_pabellon', function() {
                var is_check = this.checked;
                if(is_check){
                    $(".solicitud_transfusion_productos_sanguineos_reserva_pabellon_group").show();
                }
                else {
                    $(".solicitud_transfusion_productos_sanguineos_reserva_pabellon_group").hide();
                    $("#solicitud_transfusion_productos_sanguineos_reserva_pabellon_fecha_hora").val("");
                    $("#solicitud_transfusion_productos_sanguineos_medico_responsable").val("");
                    $("#solicitud_transfusion_productos_sanguineos_solicitud_fecha_hora").val("");
                }

                initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));

            });
            

            $(document).on('click', '#solicitud_transfusion_productos_sanguineos_save,#solicitud_transfusion_productos_sanguineos_save_pdf', function() {

                var con_pdf = $(this).attr("id") == "solicitud_transfusion_productos_sanguineos_save_pdf";
                
                var bv = initFormValidation ($("#solicitud_transfusion_productos_sanguineos_form"));

                if(true){
                //if(bv.isValid()){
                    bootbox.confirm({
                        message: "<h4>¿Está seguro de ingresar la información?</h4>",
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
                                $("#solicitud_transfusion_productos_sanguineos_save,#solicitud_transfusion_productos_sanguineos_save_pdf").attr("disabled", true);

                                var form_data = $("#solicitud_transfusion_productos_sanguineos_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                $.ajax({
                                    url: "{{URL::route('solicitud-transfusion-productos-sanguineos-save')}}",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:  form_data,
                                    dataType: "json",
                                    type: "post",
                                    success: function(data){
										swalExito.fire({
                							title: 'Exito!',
                							text: data.status,
                							didOpen: function() {
                								setTimeout(function() {
                									if(con_pdf)
                                                    {                                               
                                                    	window.open("{{url('formularios-ginecologia/solicitud-transfusion-productos-sanguineos/pdf/'.$formulario_data->caso_id)}}","imprimir","resizable,scrollbars,status");   
                                                    }
                                                    @if (!isset($formulario_data->form_id))

                                                    @else
                                                    $("#solicitud_transfusion_productos_sanguineos_save,#solicitud_transfusion_productos_sanguineos_save_pdf").attr("disabled", false);
                                                    @endif

                                                    location.reload();
                								}, 2000);
                							},
										});
                                    },
                                    error: function(request, status, error){
                                        

                                        try {
                                            var json_res = JSON.parse(request.responseText);
                                            swalError.fire({
                        						title: 'Error',
                        						text:json_res.status
                        					});
                                        } 
                                        
                                        catch (error) {
                                        	swalError.fire({
                        						title: 'Error',
                        						text:"Ha ocurrido un error"
                        					});
                                        }
                                        $("#solicitud_transfusion_productos_sanguineos_save,#solicitud_transfusion_productos_sanguineos_save_pdf").attr("disabled", false);

                                    }
                                });
                            }
                        }            
                    });               

                }                

            });

            $(document).on('click', '#solicitud_transfusion_productos_sanguineos_agregar_instalacion', function() {
                
                bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar una instalación?, no se podrá eliminar.</h4>",
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
                            addInstalacion();
    
                        }
                    }            
                });


            });

            //al final siempre
            load();
        });

    </script>

@stop
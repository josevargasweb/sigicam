{{-- $tab se usa para saber si el partograma se usa en una pestaña ($tab = true) o es una vista propia ($tab != true) --}}
    <script>
        var caso_id = '{{$formulario_data->caso_id}}';
    </script>
    <!-- alertas de alergias logica-->
    {{ HTML::script('js/formularios_ginecologia/partograma-alergias-notificator.js') }}


    <style>
        .help-block {
            color: #a94442;
        }
        
        /* tabla */
        
        .table_data {
            display: block;
            overflow-x: auto;
        }

        .table_data td {
            padding-bottom: 10px;
            padding-right: 10px;
        }

        .table_data td:first-child {
            position: sticky;
            left: 0;
            background-color: #fff;
        }



    </style>
	@if(!$tab)
    <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
	@endif
    <br><br>

    {{ 
        Form::open(
            array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'partograma_form')
        ) 
    }}

    <br>

    <!-- -->

    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Partograma</h4>
        </div>

        <div class="panel-body">


            <div class="row">
				<canvas id="partograma">
					Su navegador no soporta la característica «canvas».
				</canvas>
            </div>


            <div class="row">
            <br>

                <div class="col-md-12">
                    <h5><p><strong>Seguimiento</strong></p></h5>
                </div>

                <div class="col-md-12">
                    <button id="add_table" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> </button>                
                </div>

                <div class="contenedor_tabla col-md-12" style="padding-top:15px;" hidden>
                    <table class="table_data">

                        <tr class="contenedor_id_tabla" hidden>
                            <td style="width:100px;">ID</td>
                        </tr>
                        <tr class="partograma_fila_hora ">
                            <td style="width:100px;">HORA</td>
                        </tr>
                        <tr class="partograma_fila_lcf">
                            <td style="width:100px;">LCF</td>
                        </tr>
                        <tr class="partograma_fila_pa">
                            <td style="width:100px;">P.A.</td>

                        </tr>
                        <tr class="partograma_fila_pulso">
                            <td style="width:100px;">PULSO</td>

                        </tr>
                        <tr class="partograma_fila_du_frec">
                            <td style="width:100px;">D.U/FREC.</td>

                        </tr>
                        <tr class="partograma_fila_duracion">
                            <td style="width:100px;">DURACI&Oacute;N</td>

                        </tr>
                        <tr class="partograma_fila_intensidad">
                            <td style="width:100px;">INTENSIDAD</td>

                        </tr>
                        <tr class="partograma_fila_cuello">
                            <td style="width:100px;">CUELLO</td>

                        </tr>
                        <tr class="partograma_fila_membranas">
                            <td style="width:100px;">MEMBRANAS</td>

                        </tr>
                        <tr class="partograma_fila_la">
                            <td style="width:100px;">L.A.</td>

                        </tr>
                        <tr class="partograma_fila_uso_balon">
                            <td style="width:100px;">USO BAL&Oacute;N.</td>

                        </tr>
                        <tr class="partograma_fila_posicion_materna">
                            <td style="width:100px;">POSICI&Oacute;N <br> (MATERNA)</td>

                        </tr>
                        <tr class="partograma_fila_monitoreo">
                            <td style="width:100px;">MONITOREO</td>

                        <tr class="partograma_fila_analgesia_peridural">
                            <td style="width:100px;">ANALGESIA PERIDURAL</td>

                        </tr>
                        <tr class="partograma_fila_analgesia_peridural_observaciones">
                            <td style="width:100px;">ANALGESIA PERIDURAL OBSERVACIONES</td>

                        </tr>

                        </tr>
                        <tr class="partograma_fila_examinador">
                            <td style="width:100px;">EXAMINADOR</td>

                        </tr>
                        <tr class="partograma_fila_instalacion_de_via">
                            <td style="width:100px;">INSTALACI&Oacute;N DE VÍA</td>
                        </tr>
                        <tr class="partograma_fila_instalacion_de_via_numero">
                            <td style="width:100px;">INSTALACI&Oacute;N DE VÍA NUMERO</td>
                        </tr>
                        <tr class="partograma_fila_instalacion_de_via_observaciones">
                            <td style="width:100px;">INSTALACI&Oacute;N DE VÍA OBSERVACIONES</td>
                        </tr>
                        <tr class="partograma_fila_instalacion_de_sonda_vesical">
                            <td style="width:100px;">INSTALACI&Oacute;N DE SONDA VESICAL</td>
                        </tr>
                        <tr class="partograma_fila_instalacion_de_sonda_vesical_numero">
                            <td style="width:100px;">INSTALACI&Oacute;N DE SONDA VESICAL NUMERO</td>
                        </tr>
                        <tr class="partograma_fila_instalacion_de_sonda_vesical_observaciones">
                            <td style="width:100px;">INSTALACI&Oacute;N DE SONDA VESICAL OBSERVACIONES</td>
                        </tr>
                        <tr class="partograma_fila_cateterismo_vesical">
                            <td style="width:100px;">CATETERISMO VESICAL</td>
                        </tr>
                        <tr class="partograma_fila_cateterismo_vesical_numero">
                            <td style="width:100px;">CATETERISMO VESICAL NUMERO</td>
                        </tr>
                        <tr class="partograma_fila_cateterismo_vesical_observaciones">
                            <td style="width:100px;">CATETERISMO VESICAL OBSERVACIONES</td>
                        </tr>
                        <tr class="partograma_fila_alergias">
                            <td style="width:100px;">ALERGIAS</td>
                        </tr>
                        <tr class="partograma_fila_alergias_observaciones">
                            <td style="width:100px;">ALERGIAS OBSERVACIONES</td>
                        </tr>
                        <tr class="partograma_fila_medias_ate">
                            <td style="width:100px;">MEDIAS ATE</td>
                        </tr>

                    </table>
                
                    <br>
                    <hr>
                </div>

                
            </div>  

            <div class="row">

                <div class="col-md-12">
                    <h5><p><strong>Evoluci&oacute;n</strong></p></h5>
                </div>

                <div class="col-md-12 container_evolucion">
                
                
                </div>

                <div class="col-md-12">
                    <button id="add_evolucion" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> </button>                
                </div>

            </div>          



        </div>
    </div>


    {{ Form::close()}}   
    @if (!isset($formulario_data->form_id))
    <input id="partograma_save" type="button" name="" class="btn btn-primary" value="Guardar">
    <input id="partograma_save_pdf" type="button" class="btn btn-primary" value="Guardar e imprimir">
    @endif

    @if (isset($formulario_data->form_id))
    <input id="partograma_save" type="button" name="" class="btn btn-primary" value="Modificar">
    {{--<input id="partograma_save_pdf" type="button" name="" class="btn btn-primary" value="Modificar e imprimir">--}}
    <input id="partograma_pdf" type="button" class="btn btn-primary" value="Imprimir">
    @endif  


    <!-- evolucion clone template -->
    <div hidden>
        <div class="row evolucion_row" >
            <div class="hidden_inputs" hidden>
                <input name="partograma_id_evolucion[]" type="hidden" >
            </div>
            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 form-group">
                            <label >Fecha</label>
                            <input name="partograma_evolucion_fecha[]" type="text" class="form-control partograma_evolucion_fecha" onkeydown="notInputExceptBackSpace(event);" onPaste="return false" >                        
                        </div>                      
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 form-group">

                            <label >Responsable</label>
                            <input name="partograma_evolucion_responsable[]" type="text" class="form-control"  readonly="readonly" value="{{ Auth::user()->nombres.' '.Auth::user()->apellido_paterno.' '.Auth::user()->apellido_materno }}">    


                        </div>                     
                    </div>

                </div>



                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 form-group">
                            <label >Observaci&oacute;n</label>
                            <textarea class="form-control" rows="5" name="evolucion_observacion[]"></textarea>
                        </div>                      
                    </div>

                </div>

                <hr>
            </div>
        </div>
    </div> 

    {!! HTML::script('js/formularios_ginecologia/helper.js') !!}
    {{ HTML::script('js/formularios_ginecologia/partograma.js') }}
    <script>
    	var imprimir_partograma = {
    		valor: false,
    		cambioValor: function() {
    			if(this.valor === true)
    			{
        			$("#partograma_pdf").prop("disabled",false);
    			}
    			else if(this.valor === false)
    			{
    				$("#partograma_pdf").prop("disabled",true);
    			}
    		},
    		get imprimir() {
    			return this.valor;
    		},
    		set imprimir(v) {
    			this.valor = v;
    			this.cambioValor();
    		}
    	};

    	@if(isset($formulario_data->form_id))
        	imprimir_partograma.imprimir = true;
        @endif
		
        $.fn.bootstrapValidator.validators._partograma_tabla_fecha_hora_ordenada = {
            validate: function(validator, $field, options) {

                try {

                    var group_date = $("input[name='partograma_hora[]']").map(function(){
                        var val = $(this).val();

                        if(typeof val !== 'undefined' && val !== null && val.trim() !==""){
                            var d = val.split(" ")[0];
                            var t = val.split(" ")[1];

                            var day = parseInt(d.split("/")[0]);
                            var month = parseInt(d.split("/")[1]);
                            var year = parseInt(d.split("/")[2]);
                            var hour = parseInt(t.split(":")[0]);
                            var min = parseInt(t.split(":")[1]);
                            var sec = parseInt(t.split(":")[2]);

                            return new Date(year, month-1, day, hour, min, sec).getTime();

                        }      
                                                     
                    }).get();

                    return sortedIntArr(group_date);
                } 
                catch (error) {
                    return false;
                }

            }
        };

        $.fn.bootstrapValidator.validators._partograma_evolucion_fecha_hora_ordenada = {
            validate: function(validator, $field, options) {

                try {

                    var group_date = $("input[name='partograma_evolucion_fecha[]']").map(function(){
                        var val = $(this).val();

                        if(typeof val !== 'undefined' && val !== null && val.trim() !==""){
                            var d = val.split(" ")[0];
                            var t = val.split(" ")[1];

                            var day = parseInt(d.split("/")[0]);
                            var month = parseInt(d.split("/")[1]);
                            var year = parseInt(d.split("/")[2]);
                            var hour = parseInt(t.split(":")[0]);
                            var min = parseInt(t.split(":")[1]);
                            var sec = parseInt(t.split(":")[2]);

                            return new Date(year, month-1, day, hour, min, sec).getTime();

                        }      
                                                     
                    }).get();

                    return sortedIntArr(group_date);
                } 
                catch (error) {
                    return false;
                }

            }
        };


        var bv_options = {
            excluded: [':disabled', ':hidden', ':not(:visible)', '[readonly=readonly]'],
            fields: {

            }
        };


        //init
        var table_index = 0;

        function notEditTable(){
            $(".contenedor_tabla input").attr("readonly",true);
            $('.contenedor_tabla :radio:not(:checked)').attr('disabled', true);
            //$('.contenedor_tabla option:not(:selected)').attr('disabled', true);
            $('.contenedor_tabla select').attr('readonly', 'readonly');
            $('.contenedor_tabla textarea').attr('readonly', 'readonly');
        }

        function notEditEvolucion(){
            $(".container_evolucion input").attr("readonly",true);
            $(".container_evolucion textarea").attr("readonly",true);
        }


        function addTable(){

            $(".contenedor_tabla").show();
            var html_partograma_tabla_fila_id = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_tabla_fila_id[]" type="hidden" class="form-control" style="width:200px;"></div></td>';
            var html_partograma_hora = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_hora[]" type="text" class="form-control partograma_hora" onkeydown="notInputExceptBackSpace(event);" onPaste="return false" style="width:200px;"></div></td>';
            var html_partograma_lcf = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_lcf[]" type="number" step="1" min="80" max="180" class="form-control" style="width:200px;"></div></td>';
            var html_partograma_pa = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><div class="row"><div class="col-md-5" style="padding-right: 0px;width: 42.666667%; position: inherit;"><input name="partograma_pa_s[]" type="number" class="form-control" min="40" max="250" step="1"></div><div class="col-md-1" style="font-size:24px; padding-right: 0px; position: inherit;">/</div><div class="col-md-5" style="padding-right: 0px; width: 42.666667%; position: inherit;"><input name="partograma_pa_d[]" type="number" class="form-control" min="20" max="150" step="1"></div></div></div></td>';
            var html_partograma_pulso = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_pulso[]" type="number" step="1" class="form-control" style="width:200px;" min="10" max="300"></div></td>';
            var html_partograma_du_frec = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><div class="row"><div class="col-md-5" style="padding-right: 0px;width: 42.666667%; position: inherit;"><input name="partograma_du[]" type="number" class="form-control" min="0" max="10" step="1"></div><div class="col-md-1" style="font-size:24px; padding-right: 0px; position: inherit;">/</div><div class="col-md-5" style="padding-right: 0px; width: 42.666667%; position: inherit;"><input name="partograma_frec[]" type="number" class="form-control" min="20" max="240" step="1"></div></div></div></td>';
            var html_partograma_duracion = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_duracion[]" type="number" class="form-control" style="width:200px;" min="0" max="300" step="1"></div></td>';
            var html_partograma_intensidad = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><select class="form-control" name="partograma_intensidad[]" style="width:200px;"><option value="" selected>Seleccione</option><option value="+">+</option><option value="++">++</option><option value="+++">+++</option></select></div></td>';
            var html_partograma_cuello = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_cuello[]" type="text" class="form-control" style="width:200px;"></div></td>';
            var html_partograma_membranas = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><select class="form-control" name="partograma_membranas[]" style="width:200px;"><option value="" selected>Seleccione</option><option value="Integras">Integras</option><option value="Rotas">Rotas</option></select></div></td>';
            var html_partograma_la = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_la[]" type="text" class="form-control"></div></td>';
            var html_partograma_uso_balon = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_uso_balon['+table_index+']" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_uso_balon['+table_index+']" value="no" style="position: inherit;">No</label></div></td>';
            var html_partograma_posicion_materna = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><select class="form-control" name="partograma_posicion_materna[]" style="width:200px;"><option value="" selected>Seleccione</option><option value="Decúbito lateral">Decúbito lateral</option><option value="Semi-sentada">Semi-sentada</option><option value="Sentada">Sentada</option><option value="De pie">De pie</option><option value="SIMS">SIMS</option><option value="Genupectoral">Genupectoral</option></select></div></td>';
            var html_partograma_monitoreo = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_monitoreo['+table_index+']" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_monitoreo['+table_index+']" value="no" style="position: inherit;">No</label></div></td>';

            var html_partograma_analgesia_peridural = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_analgesia_peridural['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_analgesia_peridural['+table_index+']" data-index="'+table_index+'" value="no" style="position: inherit;">No</label></div></td>';

            var html_partograma_analgesia_peridural_observaciones = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><textarea name="partograma_analgesia_peridural_observaciones[]" class="form-control" style="width:200px;" rows="4" data-index="'+table_index+'"></textarea></div></td>';


            var html_partograma_examinador = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><input name="partograma_examinador[]" type="text" class="form-control" style="width:200px;" value="{{ Auth::user()->nombres.' '.Auth::user()->apellido_paterno.' '.Auth::user()->apellido_materno }}" disabled></div></td>';
            
            /*--*/

            var html_partograma_instalacion_de_via = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_instalacion_de_via['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_instalacion_de_via['+table_index+']" value="no" data-index="'+table_index+'" style="position: inherit;" >No</label></div></td>';

            var html_partograma_instalacion_de_via_numero = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><select class="form-control" name="partograma_instalacion_de_via_numero[]" style="width:200px;" data-index="'+table_index+'"><option value="" selected>Seleccione</option><option value="14">14</option><option value="16">16</option><option value="18">18</option><option value="20">20</option><option value="22">22</option><option value="24">24</option></select></div></td>';

            var html_partograma_instalacion_de_via_observaciones = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><textarea name="partograma_instalacion_de_via_observaciones[]" class="form-control" style="width:200px;" rows="4" data-index="'+table_index+'"></textarea></div></td>';
            
            /*--*/

            var html_partograma_instalacion_de_sonda_vesical = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_instalacion_de_sonda_vesical['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_instalacion_de_sonda_vesical['+table_index+']" value="no" data-index="'+table_index+'" style="position: inherit;" >No</label></div></td>';

            var html_partograma_instalacion_de_sonda_vesical_numero = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><input name="partograma_instalacion_de_sonda_vesical_numero[]" type="number" min="8" max="20" class="form-control" data-index="'+table_index+'"></div></td>';

            var html_partograma_instalacion_de_sonda_vesical_observaciones = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><textarea name="partograma_instalacion_de_sonda_vesical_observaciones[]" class="form-control" style="width:200px;" rows="4" data-index="'+table_index+'"></textarea></div></td>';
            
            /*--*/

            var html_partograma_cateterismo_vesical = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_cateterismo_vesical['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_cateterismo_vesical['+table_index+']" value="no" data-index="'+table_index+'" style="position: inherit;" >No</label></div></td>';

            var html_partograma_cateterismo_vesical_numero = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; pointer-events:none;" ><input name="partograma_cateterismo_vesical_numero[]" type="number" min="8" max="20" class="form-control" data-index="'+table_index+'" ></div></td>';

            var html_partograma_cateterismo_vesical_observaciones = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px; " ><textarea name="partograma_cateterismo_vesical_observaciones[]" class="form-control" style="width:200px;" rows="4" data-index="'+table_index+'"></textarea></div></td>';

            /*--*/

            var html_partograma_alergias = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_alergias['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_alergias['+table_index+']" value="no" data-index="'+table_index+'" style="position: inherit;" >No</label></div></td>';

            var html_partograma_alergias_observaciones = '<td style="width:200px;" style="display:none;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;" ><textarea name="partograma_alergias_observaciones[]" class="form-control" style="width:200px;" rows="4" data-index="'+table_index+'"></textarea></div></td>';

            /*--*/

            var html_partograma_medias_ate = '<td style="width:200px;"><div class="form-group" style="margin-left: 0px; margin-right: 0px; margin-bottom: 0px;"><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_medias_ate['+table_index+']" data-index="'+table_index+'" value="si" style="position: inherit;">S&iacute;</label><label class="radio-inline" style="padding-bottom: 8px;"><input type="radio" name="partograma_medias_ate['+table_index+']" value="no" data-index="'+table_index+'" style="position: inherit;" >No</label></div></td>';


            
            $( ".contenedor_id_tabla").append(html_partograma_tabla_fila_id);
            $( ".partograma_fila_hora" ).append(html_partograma_hora);
            $( ".partograma_fila_lcf" ).append(html_partograma_lcf);
            $( ".partograma_fila_pa" ).append(html_partograma_pa);
            $( ".partograma_fila_pulso" ).append(html_partograma_pulso);
            $( ".partograma_fila_du_frec" ).append(html_partograma_du_frec);
            $( ".partograma_fila_duracion" ).append(html_partograma_duracion);
            $( ".partograma_fila_intensidad" ).append(html_partograma_intensidad);
            $( ".partograma_fila_cuello" ).append(html_partograma_cuello);
            $( ".partograma_fila_membranas" ).append(html_partograma_membranas);
            $( ".partograma_fila_la" ).append(html_partograma_la);
            $( ".partograma_fila_uso_balon" ).append(html_partograma_uso_balon);
            $( ".partograma_fila_posicion_materna" ).append(html_partograma_posicion_materna);
            $( ".partograma_fila_monitoreo" ).append(html_partograma_monitoreo);
            $( ".partograma_fila_analgesia_peridural" ).append(html_partograma_analgesia_peridural);
            $( ".partograma_fila_analgesia_peridural_observaciones" ).append(html_partograma_analgesia_peridural_observaciones);
            $( ".partograma_fila_examinador" ).append(html_partograma_examinador);

            /*--*/

            $( ".partograma_fila_instalacion_de_via" ).append(html_partograma_instalacion_de_via);
            $( ".partograma_fila_instalacion_de_via_numero" ).append(html_partograma_instalacion_de_via_numero);
            $( ".partograma_fila_instalacion_de_via_observaciones" ).append(html_partograma_instalacion_de_via_observaciones);

            /*--*/

            $( ".partograma_fila_instalacion_de_sonda_vesical" ).append(html_partograma_instalacion_de_sonda_vesical);
            $( ".partograma_fila_instalacion_de_sonda_vesical_numero" ).append(html_partograma_instalacion_de_sonda_vesical_numero);
            $( ".partograma_fila_instalacion_de_sonda_vesical_observaciones" ).append(html_partograma_instalacion_de_sonda_vesical_observaciones);

            /*--*/

            $( ".partograma_fila_cateterismo_vesical" ).append(html_partograma_cateterismo_vesical);
            $( ".partograma_fila_cateterismo_vesical_numero" ).append(html_partograma_cateterismo_vesical_numero);
            $( ".partograma_fila_cateterismo_vesical_observaciones" ).append(html_partograma_cateterismo_vesical_observaciones);

            /*--*/

            $( ".partograma_fila_alergias" ).append(html_partograma_alergias);
            $( ".partograma_fila_alergias_observaciones" ).append(html_partograma_alergias_observaciones);

            /*--*/

            $( ".partograma_fila_medias_ate").append(html_partograma_medias_ate);

            //listeners

            $('.partograma_hora').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#partograma_form"));
            });


            $(document).on('change', '[name="partograma_analgesia_peridural['+table_index+']"]', function() {
                let value = $(this).val();
                let index = $(this).attr("data-index");
                if(value != null && value != undefined && value.trim() != "" && index != null && index != undefined && index.trim() != ""){


                    let partograma_analgesia_peridural_observaciones_elem = $('[name="partograma_analgesia_peridural_observaciones[]"][data-index='+index+']');


                    if (value == "si"){


                        //containers
                        partograma_analgesia_peridural_observaciones_elem.parent().css("pointer-events","");

                        partograma_analgesia_peridural_observaciones_elem.removeAttr("readonly");

                    }
                    else {

                        //containers
                        partograma_analgesia_peridural_observaciones_elem.parent().css("pointer-events","none");

                        partograma_analgesia_peridural_observaciones_elem.attr("readonly","readonly");

                        //defauls
                        partograma_analgesia_peridural_observaciones_elem.val("");
                    }

                    //re-evaluation
                    initFormValidation ($("#partograma_form"));

                }

            });








            $(document).on('change', '[name="partograma_instalacion_de_via['+table_index+']"]', function() {
                let value = $(this).val();
                let index = $(this).attr("data-index");
                if(value != null && value != undefined && value.trim() != "" && index != null && index != undefined && index.trim() != ""){


                    let partograma_partograma_instalacion_de_via_numero_elem = $('[name="partograma_instalacion_de_via_numero[]"][data-index='+index+']');

                    let partograma_instalacion_de_via_observaciones_elem = $('[name="partograma_instalacion_de_via_observaciones[]"][data-index='+index+']');


                    if (value == "si"){

                        //containers
                        partograma_partograma_instalacion_de_via_numero_elem.parent().css("pointer-events","");
                        partograma_instalacion_de_via_observaciones_elem.parent().css("pointer-events","");

                        partograma_partograma_instalacion_de_via_numero_elem.removeAttr("readonly");
                        partograma_instalacion_de_via_observaciones_elem.removeAttr("readonly");

                    }
                    else {


                        //containers
                        partograma_partograma_instalacion_de_via_numero_elem.parent().css("pointer-events","none");
                        partograma_instalacion_de_via_observaciones_elem.parent().css("pointer-events","none");


                        partograma_partograma_instalacion_de_via_numero_elem.attr("readonly","readonly");
                        partograma_instalacion_de_via_observaciones_elem.attr("readonly","readonly");

                        //defauls
                        partograma_partograma_instalacion_de_via_numero_elem.val("");
                        partograma_instalacion_de_via_observaciones_elem.val("");


                    }

                    //re-evaluation
                    initFormValidation ($("#partograma_form"));


                }

            });


            $(document).on('change', '[name="partograma_instalacion_de_sonda_vesical['+table_index+']"]', function() {
                let value = $(this).val();
                let index = $(this).attr("data-index");
                if(value != null && value != undefined && value.trim() != "" && index != null && index != undefined && index.trim() != ""){


                    let partograma_instalacion_de_sonda_vesical_numero_elem = $('[name="partograma_instalacion_de_sonda_vesical_numero[]"][data-index='+index+']');

                    let partograma_instalacion_de_sonda_vesical_observaciones_elem = $('[name="partograma_instalacion_de_sonda_vesical_observaciones[]"][data-index='+index+']');


                    if (value == "si"){

                        //containers
                        partograma_instalacion_de_sonda_vesical_numero_elem.parent().css("pointer-events","");
                        partograma_instalacion_de_sonda_vesical_observaciones_elem.parent().css("pointer-events","");

                        partograma_instalacion_de_sonda_vesical_numero_elem.removeAttr("readonly");
                        partograma_instalacion_de_sonda_vesical_observaciones_elem.removeAttr("readonly");


                    }
                    else {


                        //containers
                        partograma_instalacion_de_sonda_vesical_numero_elem.parent().css("pointer-events","none");
                        partograma_instalacion_de_sonda_vesical_observaciones_elem.parent().css("pointer-events","none");

                        partograma_instalacion_de_sonda_vesical_numero_elem.attr("readonly","readonly");
                        partograma_instalacion_de_sonda_vesical_observaciones_elem.attr("readonly","readonly");

                        //defauls
                        partograma_instalacion_de_sonda_vesical_numero_elem.val("");
                        partograma_instalacion_de_sonda_vesical_observaciones_elem.val("");


                    }

                    //re-evaluation
                    initFormValidation ($("#partograma_form"));


                }

            });


            $(document).on('change', '[name="partograma_cateterismo_vesical['+table_index+']"]', function() {
                let value = $(this).val();
                let index = $(this).attr("data-index");
                if(value != null && value != undefined && value.trim() != "" && index != null && index != undefined && index.trim() != ""){


                    let partograma_cateterismo_vesical_numero_elem = 
                    $('[name="partograma_cateterismo_vesical_numero[]"][data-index='+index+']');

                    let partograma_cateterismo_vesical_observaciones_elem = $('[name="partograma_cateterismo_vesical_observaciones[]"][data-index='+index+']');


                    if (value == "si"){


                        //containers
                        partograma_cateterismo_vesical_numero_elem.parent().css("pointer-events","");
                        partograma_cateterismo_vesical_observaciones_elem.parent().css("pointer-events","");

                        partograma_cateterismo_vesical_numero_elem.removeAttr("readonly");
                        partograma_cateterismo_vesical_observaciones_elem.removeAttr("readonly");

                    }
                    else {

                        //containers
                        partograma_cateterismo_vesical_numero_elem.parent().css("pointer-events","none");
                        partograma_cateterismo_vesical_observaciones_elem.parent().css("pointer-events","none");

                        partograma_cateterismo_vesical_numero_elem.attr("readonly","readonly");
                        partograma_cateterismo_vesical_observaciones_elem.attr("readonly","readonly");


                        //defauls
                        partograma_cateterismo_vesical_numero_elem.val("");
                        partograma_cateterismo_vesical_observaciones_elem.val("");
                    }

                    //re-evaluation
                    initFormValidation ($("#partograma_form"));

                }

            });

            $(document).on('change', '[name="partograma_alergias['+table_index+']"]', function() {
                let value = $(this).val();
                let index = $(this).attr("data-index");
                if(value != null && value != undefined && value.trim() != "" && index != null && index != undefined && index.trim() != ""){


                    let partograma_alergias_observaciones_elem = 
                    $('[name="partograma_alergias_observaciones[]"][data-index='+index+']');


                    if (value == "si"){

                        //containers
                        partograma_alergias_observaciones_elem.parent().css("pointer-events","");
                        partograma_alergias_observaciones_elem.removeAttr("readonly");

                                                
                    }
                    else {

                        partograma_alergias_observaciones_elem.parent().css("pointer-events","none");
                        partograma_alergias_observaciones_elem.attr("readonly","readonly");

                        //defauls
                        partograma_alergias_observaciones_elem.val("");
                    }

                    //re-evaluation
                    initFormValidation ($("#partograma_form"));

                }

            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_hora[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                    _not_future_datetime: {
                        message: 'No se permiten fechas futuras o el formato es incorrecto.'
                    },
                    _partograma_tabla_fecha_hora_ordenada: {
                        message: 'Las fechas deben estar ordenadas de menor a mayor.'
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_cuello[]', {
                validators: {
                    stringLength: {
                        max: 60,
                        message: 'Máximo 60 caracteres.'
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_la[]', {
                validators: {
                    stringLength: {
                        max: 60,
                        message: 'Máximo 60 caracteres.'
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_uso_balon['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_analgesia_peridural['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_analgesia_peridural_observaciones[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_monitoreo['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });


            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_hora[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                    _not_future_datetime: {
                        message: 'No se permiten fechas futuras o el formato es incorrecto.'
                    },
                    _partograma_tabla_fecha_hora_ordenada: {
                        message: 'Las fechas deben estar ordenadas de menor a mayor.'
                    },
                },
            });


            $('#partograma_form').bootstrapValidator('addField', 'partograma_instalacion_de_via['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });


            $('#partograma_form').bootstrapValidator('addField', 'partograma_instalacion_de_via_numero[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });


            $('#partograma_form').bootstrapValidator('addField', 'partograma_instalacion_de_sonda_vesical['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_instalacion_de_sonda_vesical_numero[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_cateterismo_vesical['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_cateterismo_vesical_numero[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_alergias['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_alergias_observaciones[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });

            $('#partograma_form').bootstrapValidator('addField', 'partograma_medias_ate['+table_index+']', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                },
            });


            //actualiza las reglas de los inputs appendeados
            bv_options.fields = $('#partograma_form').data('bootstrapValidator').getOptions().fields;

            //scroll
            var leftPos = $('.table_data').scrollLeft();
            var divwidth = $('.table_data')[0].scrollWidth;
            $(".table_data").animate({ scrollLeft: leftPos + divwidth }, 800);

            table_index++;

        }

        function addEvolucion(){
            var $evolucion_row = $('.evolucion_row').clone();

            $('.container_evolucion').html($evolucion_row);

            $('.partograma_evolucion_fecha').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:ss',

            }).on("dp.change", function () {
                initFormValidation ($("#partograma_form"));
            });

            //append regla
            $('#partograma_form').bootstrapValidator('addField', 'partograma_evolucion_fecha[]', {
                validators: {
                    notEmpty: {
                        enabled: true
                    },
                    _not_future_datetime: {
                        message: 'No se permiten fechas futuras o el formato es incorrecto.'
                    },
                    _partograma_evolucion_fecha_hora_ordenada: {
                        message: 'Las fechas deben estar ordenadas de menor a mayor.'
                    },
                },
            });


            //actualiza las reglas de los inputs appendeados
            bv_options.fields = $('#partograma_form').data('bootstrapValidator').getOptions().fields;


        }


        function load() {
        	cargarGraficoPartograma();

            @if (isset($formulario_data->form_id))

                //tabla
                @foreach ($formulario_data->tabla as $data)
            
                    addTable();

                    $("[name='partograma_tabla_fila_id[]']").eq({{$loop->index}}).val({!! json_encode($data->id_formulario_partograma_tabla	) !!});
                    $("[name='partograma_hora[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$data->hora}}");
                    $("[name='partograma_lcf[]']").eq({{$loop->index}}).val({!! json_encode($data->lcf) !!});
                    $("[name='partograma_pa_s[]']").eq({{$loop->index}}).val({!! json_encode($data->pa_s) !!});
                    $("[name='partograma_pa_d[]']").eq({{$loop->index}}).val({!! json_encode($data->pa_d) !!});
                    $("[name='partograma_pulso[]']").eq({{$loop->index}}).val({!! json_encode($data->pulso) !!});
                    $("[name='partograma_du[]']").eq({{$loop->index}}).val({!! json_encode($data->du) !!});
                    $("[name='partograma_frec[]']").eq({{$loop->index}}).val({!! json_encode($data->frecuencia_cardiaca) !!});
                    $("[name='partograma_duracion[]']").eq({{$loop->index}}).val({!! json_encode($data->duracion) !!});
                    $("[name='partograma_intensidad[]']").eq({{$loop->index}}).val({!! json_encode($data->intensidad) !!});
                    $("[name='partograma_cuello[]']").eq({{$loop->index}}).val({!! json_encode($data->cuello) !!});
                    $("[name='partograma_membranas[]']").eq({{$loop->index}}).val({!! json_encode($data->membrana) !!});
                    $("[name='partograma_la[]']").eq({{$loop->index}}).val({!! json_encode($data->la) !!});
                    jQuery('input:radio[name="partograma_uso_balon['+{{$loop->index}}+']"]').filter('[value="{{$data->uso_balon}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_posicion_materna[]']").eq({{$loop->index}}).val({!! json_encode($data->posicion_materna) !!});
                    jQuery('input:radio[name="partograma_monitoreo['+{{$loop->index}}+']"]').filter('[value="{{$data->monitoreo}}"]').prop('checked', true).trigger("change");

                    jQuery('input:radio[name="partograma_analgesia_peridural['+{{$loop->index}}+']"]').filter('[value="{{$data->analgesia_peridural}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_analgesia_peridural_observaciones[]']").eq({{$loop->index}}).val({!! json_encode($data->observaciones_analgesia_peridural) !!});

                    $("[name='partograma_examinador[]']").eq({{$loop->index}}).val({!! json_encode($data->examinador()->nombres.' '.$data->examinador()->apellido_paterno.' '.$data->examinador()->apellido_materno) !!});

                    //-- nuevos -- //
                    jQuery('input:radio[name="partograma_instalacion_de_via['+{{$loop->index}}+']"]').filter('[value="{{$data->instalacion_via}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_instalacion_de_via_numero[]']").eq({{$loop->index}}).val({!! json_encode($data->numero_instalacion_via) !!});
                    $("[name='partograma_instalacion_de_via_observaciones[]']").eq({{$loop->index}}).val({!! json_encode($data->observacion_instalacion_via) !!});


                    jQuery('input:radio[name="partograma_instalacion_de_sonda_vesical['+{{$loop->index}}+']"]').filter('[value="{{$data->sonda_vesical}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_instalacion_de_sonda_vesical_numero[]']").eq({{$loop->index}}).val({!! json_encode($data->numero_sonda_vesical) !!});
                    $("[name='partograma_instalacion_de_sonda_vesical_observaciones[]']").eq({{$loop->index}}).val({!! json_encode($data->observacion_sonda_vesical) !!});



                    jQuery('input:radio[name="partograma_cateterismo_vesical['+{{$loop->index}}+']"]').filter('[value="{{$data->cateterismo_vesical}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_cateterismo_vesical_numero[]']").eq({{$loop->index}}).val({!! json_encode($data->numero_cateterismo_vesical) !!});
                    $("[name='partograma_cateterismo_vesical_observaciones[]']").eq({{$loop->index}}).val({!! json_encode($data->observacion_cateterismo_vesical) !!});




                    jQuery('input:radio[name="partograma_alergias['+{{$loop->index}}+']"]').filter('[value="{{$data->alergia}}"]').prop('checked', true).trigger("change");
                    $("[name='partograma_alergias_observaciones[]']").eq({{$loop->index}}).val({!! json_encode($data->detalle_alergia) !!});



                    jQuery('input:radio[name="partograma_medias_ate['+{{$loop->index}}+']"]').filter('[value="{{$data->medias_ate}}"]').prop('checked', true).trigger("change");

                    //previene edicion de tablas traidas desde la bd
                    notEditTable();

                @endforeach

                //tabla
                @foreach ($formulario_data->evoluciones as $data)
                    
                    addEvolucion();

                    $("[name='partograma_id_evolucion[]']").eq({{$loop->index}}).val({!! json_encode($data->id_formulario_partograma_evolucion) !!});
                    $("[name='partograma_evolucion_fecha[]']").eq({{$loop->index}}).data("DateTimePicker").date("{{$data->fecha_evolucion}}");
                    $("[name='evolucion_observacion[]']").eq({{$loop->index}}).val({!! json_encode($data->observacion_evolucion) !!});
                    $("[name='partograma_evolucion_responsable[]']").eq({{$loop->index}}).val({!! json_encode($data->usuarioResponsable()->nombres.' '.$data->usuarioResponsable()->apellido_paterno.' '.$data->usuarioResponsable()->apellido_materno) !!});


                    //previene edicion de evolucines traidas desde la bd
                    notEditEvolucion();
                @endforeach

                initFormValidation ($("#partograma_form"));

            @endif
            
        }
        function cargarGraficoPartograma(){
        	$.ajax({
                url: "{{URL::route('cargar-partograma')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:  {
                    caso: {{$formulario_data->caso_id}}
                },
                dataType: "json",
                type: "post",
                success: function(data){
                	var p = Partograma.instancia();
                	p.ponerSecciones(data.bloques);
                	p.ponerPuntos(data.datos_bloques);
                	p.redibujar();
                },
                error: function(request, status, error){
                    

                    try {
                        var json_res = JSON.parse(request.responseText);
                        bootbox.alert(json_res.status, function(){ });
                    } 
                    
                    catch (error) {
                        bootbox.alert("Ha ocurrido un error");
                    }
                    $("#partograma_save,#partograma_save_pdf").attr("disabled", false);
                    def.reject();

                }
            });
        }
        $(document).ready(function() { 

            //iniciar partograma js
            var p = Partograma.instancia();
            p.ponerID("partograma");
            p.eventoCambio(function(resultado){
                imprimir_partograma.imprimir = !resultado;
            });

            $("#partograma_pdf").on("click",function(){
                generar_pdf();
            });

            function guardar_formulario(form_data){
                var def = $.Deferred();
            	$.ajax({
                    url: "{{URL::route('partograma-save')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:  form_data,
                    dataType: "json",
                    type: "post",
                    success: function(data){
                        def.resolve(data.id);
                    },
                    error: function(request, status, error){
                        

                        try {
                            var json_res = JSON.parse(request.responseText);
                            bootbox.alert(json_res.status, function(){ });
                        } 
                        
                        catch (error) {
                            bootbox.alert("Ha ocurrido un error");
                        }
                        $("#partograma_save,#partograma_save_pdf").attr("disabled", false);
                        def.reject();

                    }
                });
                return def.promise();
            }
            function guardar_partograma(id_formulario,con_pdf){
            	$.ajax({
                    url: "{{URL::route('partograma-grafico')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:  {
                        id_formulario: id_formulario,
                        datos: p.datos()
                    },
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
		                        		var promesa = generar_pdf();

		                        		promesa.done(function(){
		                        			location.reload();
		                        			@if (!isset($formulario_data->form_id))
		                                    //location.reload();
		                                    @else
		                                    $("#partograma_save,#partograma_save_pdf").attr("disabled", false);
		                                    cargarGraficoPartograma();
		                                    @endif
		                        		}).fail(function(){
		                        			
		                            		bootbox.alert("Se ha guardado correctamente pero no se ha podido generar el documento PDF",function(){
		                            			location.reload();
		                            		});
		                            		
		                            		@if (!isset($formulario_data->form_id))
		                                    //location.reload();
		                                    @else
		                                    $("#partograma_save,#partograma_save_pdf").attr("disabled", false);
		                                    cargarGraficoPartograma();
		                                    @endif
		                        		});
		                            }
		                        	else{
                                        location.reload();
		                        		@if (!isset($formulario_data->form_id))
		                                //location.reload();
		                                @else
		                                $("#partograma_save,#partograma_save_pdf").attr("disabled", false);
		                            	cargarGraficoPartograma();
		                                @endif
		                        	}
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
                        $("#partograma_save,#partograma_save_pdf").attr("disabled", false);

                    }
                });
            }
            function generar_pdf(){
                var d = $.Deferred();
                
				var imagenes = p.obtenerImagenes();
				
				var req = new XMLHttpRequest();
				req.responseType = "blob";
				req.open("POST", "{{url('formularios-ginecologia/partograma/pdf')}}", true);
				req.setRequestHeader('Content-Type', 'application/json');
				req.send(JSON.stringify({
                    imagenes: imagenes,
                    caso_id:'{{$formulario_data->caso_id}}'
                }));
				req.onreadystatechange = function () {
				    if (req.readyState === 4 && req.status === 200) {
				    	
				    	var blob=new Blob([req.response],{type:'application/pdf'});

                        window.open(window.URL.createObjectURL(blob));

                        d.resolve();
                        
				    }
				    else if(req.readyState === 4){
					    d.reject();
				    }
				};
				return d.promise();
        	}

            $(document).on('click', '#partograma_save,#partograma_save_pdf', function() {

                var con_pdf = $(this).attr("id") == "partograma_save_pdf";
                var bv = initFormValidation ($("#partograma_form"));
                if(bv.isValid()){
                    bootbox.confirm({
                        message: "<h4>¿Está seguro de ingresar la información?</h4>",
                        buttons: {
                            confirm: {
                                label: 'Sí',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'No',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if(result){
                                $("#partograma_save,#partograma_save_pdf").attr("disabled", true);

                                var form_data = $("#partograma_form").serialize()+"&caso_id={{$formulario_data->caso_id}}&form_id={{$formulario_data->form_id}}";

                                var promesa = guardar_formulario(form_data);
                                promesa.done(function(id_formulario){
                                    guardar_partograma(id_formulario,con_pdf);
                                });

                                
                            }
                        }            
                    });               

                }                

            });

            $(document).on('click', '#add_table', function() { 


                bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar un registro?, no se podrá eliminar.</h4>",
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
                            addTable();
                            imprimir_partograma.imprimir = false;
                        }
                    }            
                });


                
            });

            $(document).on('click', '#add_evolucion', function() { 

                bootbox.confirm({
                    message: "<h4>¿Está seguro de agregar una evolución?, no se podrá eliminar.</h4>",
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
                            addEvolucion();
                            imprimir_partograma.imprimir = false;
                        }
                    }            
                });

            });

            //al final siempre
            load();
        });

    </script>
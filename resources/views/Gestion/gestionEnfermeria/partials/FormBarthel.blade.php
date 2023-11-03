



<br><br>

<div class="panel panel-default">
	<div class="panel-heading panel-info">
		<h4>Índice de Barthel Inicial</h4>
    </div>

	<div class="panel-body">
		<div style="text-align: left;">

			{{-- <div class="form"> --}}
			

			<input type="hidden" value="{{$caso}}" name="caso">
			

			<div>
				<input name="inicio" value="true" hidden="">
				<input name="tipo-encuesta" value="indiceBarthel" hidden="">
			</div>

      




    <table class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
        <thead>
            <tr style="background:#399865; color: cornsilk;">
                <th>Parámetro</th>
                <th>Situación del paciente</th>
            </tr>
        </thead>


        <tbody class="agrupar-trs">

            <tr>
                <td >
                  <label for="" class="control-label" title="Comer">Comer</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('comida', array(''=>'Seleccione', '10'=>'(10 pts.) Totalmente independiente', '5'=>'(5 pts.) Necesita ayuda para cortar carne, el pan, etc.', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Comer'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Lavarse">Lavarse</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('lavado', array(''=>'Seleccione', '5'=>'(5 pts.) Independiente: entra y sale solo del baño', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Lavarse'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Comer">Vestirse</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('vestido', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente: capaz de ponerse y de quitarse la ropa, abotonarse, atarse los zapatos', '5'=>'(5 pts.) Necesita ayuda', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Vestirse'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Arreglarse">Arreglarse</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('arreglo', array(''=>'Seleccione', '5'=>'(5 pts.) Independiente para lavarse la cara, las manos, peinarse, afeitarse, maquillarse, etc.', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Arreglarse'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Deposicion">Deposiciones (valórese la semana previa)</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('deposicion', array(''=>'Seleccione', '10'=>'(10 pts.) Continencia normal', '5'=>'(5 pts.) Ocasionalmente algún episodio de incontinencia, o necesita ayuda para administrarse supositorios o lavativas', '0' =>'(0 pts.) Incontinencia'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Deposicion'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Miccion">Micción (valórese la semana previa)</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('miccion', array(''=>'Seleccione', '10'=>'(10 pts.) Continencia normal, o es capaz de cuidarse de la sonda si tiene una puesta', '5'=>'(5 pts.) Un episodio diario como máximo de incontinencia, o necesita ayuda para cuidar de la sonda', '0' =>'(0 pts.) Incontinencia'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Miccion'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Retrete">Usar el retrete</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('retrete', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente para ir al cuarto de aseo, quitarse y ponerse la ropa', '5'=>'(5 pts.) Necesita ayuda para ir al retrete, pero se limpia solo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Retrete'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Trasferencia">Trasladarse</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('trasferencia', array(''=>'Seleccione', '15'=>'(15 pts.) Independiente para ir del sillón a la cama','10'=>'(10 pts.) Mínima ayuda física o supervisión para hacerlo', '5'=>'(5 pts.) Necesita gran ayuda, pero es capaz de mantenerse sentado solo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Trasferencia'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Deambulacion">Deambular</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('deambulacion', array(''=>'Seleccione', '15'=>'(15 pts.) Independiente, camina solo 50 metros','10'=>'(10 pts.) Necesita ayuda física o supervisión para caminar 50 metros', '5'=>'(5 pts.) Independiente en silla de ruedas sin ayuda', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Deambulacion'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>
            <tr>
                <td >
                  <label for="" class="control-label" title="Escaleras">Escalones</label>
                </td>
                <td>
                  <div class="row">
                      <div class="form-group col-md-12">
                          <div class="col-sm-10">
                              
                              {{Form::select('escaleras', array(''=>'Seleccione', '10'=>'(10 pts.) Independiente para bajar y subir escaleras', '5'=>'(5 pts.) Necesita ayuda física o supervisión para hacerlo', '0' =>'(0 pts.) Dependiente'), null,array('class' => 'form-control  selectBarthel', 'id'=>'Escaleras'))}}
                          </div>
                      </div>
                  </div>
                </td>
            </tr>

            <tr>
                <td >
                    Total:
                </td>
                <td>
                    <div class="col-md-6">
                        <input type="number" min="0" id="totalBarthel" name="indiceBarthel-total" class="form-control indiceBarthelInicial-total" readonly=""  data-fv-field="indiceBarthel-total" value="0">
                    </div>
                    <div class="col-md-6">
                        {{Form::text('detalleBarthel', "Independiente", array('readonly','id' => 'detalleBarthel', 'class' => 'form-control'))}}
                    </div>
                    
                </td>
            </tr>
            <tr>
                <td colspan="2">

                    <input id="guardarBarthel" type="submit" name="" class="btn btn-primary" value="Guardar">
                </td>
            </tr>
        </tbody>

    </table>
    
		{{-- </form></div> --}}
		</div>
<!--</fieldset>-->
	</div>
</div>

    <p>Máxima puntuación: 100 puntos (90 si va en silla de ruedas)</p>

    <table class="table table-bordered">
        <thead style="background:#399865; color: cornsilk;">
                <tr>
                <th>Resultado</th>
                    <th>Grado de dependencia</th>
                </tr>
        </thead>
        <tbody>
              <tr>
                  <td>&lt;20</td>
                  <td>Dependencia Total</td>
              </tr>

              <tr>
                  <td>20-39</td>
                  <td>Grave</td>
              </tr>

              <tr>
                  <td>40-59</td>
                  <td>Moderado</td>
              </tr>

              <tr>
                  <td>60-99</td>
                  <td>Leve</td>
              </tr>

              <tr>
                  <td>100</td>
                  <td>Independiente</td>
              </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function(){
          

            $(".selectBarthel").change(function(){

                comida  = $("#Comer").val();
                lavado  = $("#Lavarse").val();
                vestido  = $("#Vestirse").val();
                arreglo  = $("#Arreglarse").val();
                deposicion  = $("#Deposicion").val();
                miccion  = $("#Miccion").val();
                retrete  = $("#Retrete").val();
                trasferencia  = $("#Trasferencia").val();
                deambulacion  = $("#Deambulacion").val();
                escaleras  = $("#Escaleras").val();

                suma = Number(comida) + Number(lavado) + Number(vestido) + Number(arreglo) + Number(deposicion)+ Number(miccion) + Number(retrete) + Number(trasferencia) + Number(deambulacion) + Number(escaleras);

                if(suma < 20){
                    $("#detalleBarthel").val("Dependencia total")
                }else if(suma>=20 && suma < 40){
                    $("#detalleBarthel").val("Grave")
                }else if(suma>=40 && suma < 60){
                    $("#detalleBarthel").val("Moderado")
                }else if(suma>=60 && suma < 100){
                    $("#detalleBarthel").val("Leve")
                }else{
                    $("#detalleBarthel").val("Independiente")
                }

                $("#totalBarthel").val(suma);
            });
        });
    </script>

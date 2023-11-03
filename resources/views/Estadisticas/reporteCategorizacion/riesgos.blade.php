<script>
function isValidDate(d) {
    return d instanceof Date && !isNaN(d);
}

function activarValidacionFechaInicio(){
    $("#updateEstadistica").bootstrapValidator('revalidateField', 'fecha-inicio');
  }
function activarValidacionFecha(){
    $("#updateEstadistica").bootstrapValidator('revalidateField', 'fecha');
}
function activarValidaciones(){
  $("#updateEstadistica").bootstrapValidator('revalidateField', 'fecha-inicio');
  $("#updateEstadistica").bootstrapValidator('revalidateField', 'fecha');
}

    var getDatos = function(fecha_desde, fecha, estab){

        estab = typeof estab !== 'undefined' ? estab : '';
        //if (estab != '') estab = "/" + estab;
        $.ajax({
            url: '{{URL::route("estRiesgo")}}/datos',
            data: {"fecha-inicio":fecha_desde, "fecha":fecha, "estab":estab},
            dataType: "json",
            type: "get",
            success: function(response){

                var addData = [];

                for(i=0;i<response.especialidades.length;i++){
                    //console.log(response.res[i]);
                    addData.push([response.especialidades[i].alias, response.especialidades[i].count]);
                
                }

                var tabla=$("#tablaAlta").dataTable();


                tabla.fnClearTable();
                if(addData.length > 0)
                    tabla.fnAddData(addData);
                activarValidaciones();
            },
            error: function(error){
                console.log("error:"+JSON.stringify(error));
                console.log(error);
                activarValidaciones();
            }
        });
    }

    $(function() {
        var vezR = 1;

        $('#tablaAlta').dataTable({ 
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 15,
            "bJQueryUI": true,
            "oLanguage": {
                "sUrl": "{{URL::to('/')}}/js/spanish.txt"
            }
        });

        $("#updateEstadistica").bootstrapValidator({
            excluded: ':disabled',
            fields: {
              'fecha-inicio':{
                  validators: {
                    notEmpty: {
                      message: 'Debe especificar la fecha'
                    },
                      callback: {
                          message: 'La fecha de inicio debe ser menor a la final',
                          callback: function(value, validator) {

                              if($('#fecha-inicio').val() == ''){
                                is_valid = true;
                              }else{

                              var is_valid = false;
                              var fecha_inicio = $("#fecha-inicio").val();
                              var fecha_fin = $("#fecha").val();
                              try {

                                  var fecha_inicio_arr = fecha_inicio.split(" ")[0].split("-");
                                  var fecha_inicio_str = fecha_inicio_arr[1]+"-"+fecha_inicio_arr[0]+"-"+fecha_inicio_arr[2];
                                  var fecha_inicio_complete = fecha_inicio_str;

                                  var fecha_fin_arr = fecha_fin.split(" ")[0].split("-");
                                  var fecha_fin_str = fecha_fin_arr[1]+"-"+fecha_fin_arr[0]+"-"+fecha_fin_arr[2];
                                  var fecha_fin_complete = fecha_fin_str;


                                  var fecha_inicio_comp = new Date(fecha_inicio_complete);
                                  var fecha_fin_comp = new Date(fecha_fin_complete);

                                  var is_valid_emision_date = isValidDate(fecha_fin_comp);

                                  is_valid = (is_valid_emision_date && fecha_inicio_comp <= fecha_fin_comp) ? true : false;

console.log(is_valid);
                              } catch (error) {
                                  console.log(error);
                                  is_valid = false;
                              }
                            }
                              return is_valid;
                          }
                      }

                  }

              },
              'fecha':{
                  validators: {
                      notEmpty: {
                        message: 'Debe especificar la fecha'
                      },
                      callback: {
                          message: 'La fecha final debe ser mayor a la de inicio',
                          callback: function(value, validator) {

                            if($('#fecha').val() == ''){
                              is_valid = true;
                            }else{

                              var is_valid = false;
                              var fecha_inicio = $("#fecha-inicio").val();
                              var fecha_fin = $("#fecha").val();

                              try {

                                  var fecha_inicio_arr = fecha_inicio.split(" ")[0].split("-");
                                  var fecha_inicio_str = fecha_inicio_arr[1]+"-"+fecha_inicio_arr[0]+"-"+fecha_inicio_arr[2];
                                  var fecha_inicio_complete = fecha_inicio_str;

                                  var fecha_fin_arr = fecha_fin.split(" ")[0].split("-");
                                  var fecha_fin_str = fecha_fin_arr[1]+"-"+fecha_fin_arr[0]+"-"+fecha_fin_arr[2];
                                  var fecha_fin_complete = fecha_fin_str;


                                  var fecha_inicio_comp = new Date(fecha_inicio_complete);
                                  var fecha_fin_comp = new Date(fecha_fin_complete);

                                  var is_valid_vigencia_date = isValidDate(fecha_fin_comp);

                                  is_valid = (is_valid_vigencia_date && fecha_inicio_comp <= fecha_fin_comp) ? true : false;

                              } catch (error) {
                                  is_valid = false;
                              }
                            }
                              return is_valid;
                          }
                      }
                  }
              }
            }
        });

$("#fecha").on('change', function(){
  if($('#fecha').val() == ''){
    activarValidacionFecha();
  }else{
    activarValidaciones();
  }
});
$("#fecha-inicio").on('change', function(){
  if($('#fecha-inicio').val() == ''){
    activarValidacionFechaInicio();
  }else{
    activarValidaciones();
  }
});
activarValidaciones();

        $("#updateEstadistica").submit(function(ev){
			ev.preventDefault();
      activarValidaciones();
			getDatos($("#fecha-inicio").val(), $("#fecha").val(), $("#establecimiento").val());
			//return false;
        });

        //carga 1 vez la tabla de pacientes categorizados en d2 y d3 cuando aprieta en la primera barra
        $('#RRC4').on('click', function (e) {
            if (vezR == 1) {
                vezR++;
                getDatos( '{{  \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y") }}','{{  \Carbon\Carbon::now()->format("d-m-Y") }}' );
            }
		});


    });


</script>

<fieldset>
    <legend>Riesgos</legend>
    <div class="row">
        {{ Form::open(array('url' => 'update', 'method' => 'get', 'class' => 'form-inline', 'role' => 'form', 'id' => 'updateEstadistica', 'style' => 'padding-left: 15px;')) }}
        <div class="form-group col-md-3">
          <div class="col-sm-12">
          {{Form::label('', "Fecha inicial:", array( ))}}
            <div class="input-group">
              {{Form::text('fecha-inicio', \Carbon\Carbon::now()->startOfMonth()->format("d-m-Y"), array('id' => 'fecha-inicio', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha', 'autocomplete'=>'off'))}}
            </div>
          </div>
        </div>
        <div class="form-group col-md-3">
          <div class="col-sm-12">
          {{Form::label('', "Fecha final:", array( ))}}
            <div class="input-group">
              {{Form::text('fecha', $fecha, array('id' => 'fecha', 'class' => 'form-control fecha-sel', 'placeholder' => 'Fecha', 'autocomplete'=>'off'))}}
            </div>
          </div>
        </div>
        @if(Session::get("usuario")->tipo == TipoUsuario::ADMINSS || Session::get("usuario")->tipo == TipoUsuario::MONITOREO_SSVQ)
            <div class="form-group">
                {{ Form::select('establecimiento', $establecimiento, null, array('class' => 'form-control', 'class' => 'establecimiento')) }}
            </div>
        @endif
                    <!--<div class="form-group">
        {{ Form::select('unidades', [], null, array('class' => 'form-control', 'id' => 'unidades', 'disabled', 'style' => 'display: none;')) }}
    </div>-->
            <div class="form-group">
                {{Form::submit('Actualizar', array('class' => 'btnUpdateRiesgo', 'class' => 'btn btn-primary', 'style'=>'margin-top: 22px;')) }}
            </div>
            {{ Form::close() }}
    </div>
    <br><br>
    <div id="contenido"></div>

    <div class="row">
        <div class="col-md-12"> 
            <div class="table-responsive">
            <table id="tablaAlta" class="table table-condensed table-hover">
                <thead>
                    <tr>                        
                        <th>Riesgo</th> 
                        <th>Total pacientes</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
        </div>
    </div>
</fieldset>
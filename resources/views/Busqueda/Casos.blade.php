<script>
    var enviarDerivado = function(idCaso){
      $(".idCaso").val(idCaso);
      var fechaderivacion = $("#fechaDerivacion").data("DateTimePicker");
      var fechaida = $("#fechaIda").data("DateTimePicker");
      window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
      fechaderivacion.date(window._gc_now);
      fechaderivacion.maxDate(moment(window._gc_now))
      fechaida.date(window._gc_now);
      $("#fecha_actual").text($("#fechaIda").val());

      var fecharescate = $("#fechaRescate").data("DateTimePicker");
      window._gc_now = moment("{{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}", 'DD/MM/YYYY HH:mm');
      fechaida.date(window._gc_now);
      fecharescate.minDate(moment(window._gc_now));

      $.ajax({
        url: "datosParaDerivacionCaso",
        data: {
          caso : idCaso
        },
        headers: {					         
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
        },
        type: "post",
        dataType: "json",
        success: function (data) {
          $("#nombreCompletoPaciente").text(data.nombreCompleto);
          $("#rutDv").text(data.rutDv);
          $("#grupoEtareo").text(data.grupoEtareo);
          $("#edadPaciente").text(data.fechaNacimiento + " (" + data.edad + ")");
          $("#fechaHospitalizacion").text(data.fechaHospitalizacion);
          $("#unidadFuncional").text(data.nombreUnidad);
          $("#idUnidadFuncional").val(data.idUnidad);

          fderivacion = moment(data.fecha_egreso_derivacion).format('DD-MM-YYYY HH:mm');
          $("#fechaDerivacion").val(fderivacion);
        },
        error: function (error) {
          console.log(error);
        }
      });
      $("#modalFormularioDerivado").modal();
    }

    $(function (){
      derivar = 0;
      $(".selectpicker").selectpicker('refresh');

      $("#fechaDerivacion").on("dp.change", function(){
        $.ajax({
          url: "../urgencia/solicitarinfoFormularioDerivado",
          type: 'post',
          dataType: 'json',
          data: {
            'idCaso': $("#idCasoFormDerivacion").val(),
            'fecha': $("#fechaDerivacion").val()
          },
          success: function(data){
            $("#unidadFuncional").html(data["nombre"]);
            $("#idUnidadFuncional").val(data["id"]);
          },
          error: function(error){
            console.log(error);
          }
        });
        $("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaDerivacion");
      });

      $("#fechaIda").on("dp.change", function(){
        $("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaIda");
      });

      $("#fechaRescate").on("dp.change", function(){
        $("#formEnviarDerivado").bootstrapValidator("revalidateField", "fechaRescate");
      });

      $("#formEnviarDerivado").bootstrapValidator({
        excluded: [':disabled',':not(:visible)'],
        fields: {
          ugcc: {
            validators: {
              callback: {
                callback: function(value, validator, $field){
                  if(value == "no"){
                    $("#tipo_ugcc").addClass("hidden");
                  }else{
                    $("#tipo_ugcc").removeClass("hidden");
                  }
                  return true;
                }
              }
            }
          },
          tramo: {
            validators: {
              callback: {
                callback: function(value, validator, $field){
                  if(value == 'pendiente'){
                    $("#t_fecha_ida").addClass("hidden");
                    $("#t_fecha_rescate").addClass("hidden");
                  }
                  else if(value == 'ida'){
                    $("#t_fecha_ida").removeClass("hidden");
                    $("#t_fecha_rescate").addClass("hidden");
                  }
                  else if(value == "ida-rescate"){
                    $("#t_fecha_ida").removeClass("hidden");
                    $("#t_fecha_rescate").removeClass("hidden");
                  }
                  return true;
                }
              }
            }
          },
          fechaDerivacion: {
            validators:{
              notEmpty: {   
               	message: 'Debe ingresar la fecha de ida'
              }
            }
          },
          fechaIda: {
          validators:{
            notEmpty: {   
               message: 'Debe ingresar la fecha de ida'
              }
            }
          },
          fechaRescate: {
          validators:{
            notEmpty: {   
               message: 'Debe ingresar la fecha de rescate'
              }
            }
          },
          tipo_centro: {
            validators:{
              notEmpty: {
                message: 'Seleccione el tipo de centro'
              }
            }
          },
          otro_derivacion: {
          validators:{
            notEmpty: {   
               message: 'Debe especificar el Centro'
              }
            }
          },
          compra_servicio_otro: {
          validators:{
            notEmpty: {   
               message: 'Debe especificar otro movil'
              }
            }
          }
        }
      }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
      }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
		  	var $form = $(evt.target);
        $("#btnFormularioDerivar").attr('disabled', 'disabled');
        if(derivar < 1){
          derivar++;
          bootbox.confirm({
            message: "<h4>¿Está seguro de querer derivar al paciente?</h4>",
            buttons: {
              confirm: {
                label: 'Si',
                className: 'btn-success'
              },
              cancel: {
                label: 'No',
                className: 'btn-danger'
              }
            },
            callback: function (result) {
              derivar--;
              if(result){
                $.ajax({
                  url: "enviarDerivadoCaso",
                  type: 'post',
                  headers: {					         
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')					    
                  },
                  dataType: 'json',
                  data: $form .serialize(),
                  success: function(data) {
                    if(data.error) {
                      swalError.fire({
                      title: 'Error',
                      text:data.error
                      }).then(function(result) {
                      if (result.isDenied) {
                        location . reload();
                      }
                      });
                    }else{
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
                  },
                  error: function(error){
                    console.log(error);
                  }
                });
              }
            }
          });
        }
        evt.preventDefault();
        return false;
      });
    });

    var editarFecha = function(idCaso){
    console.log(idCaso);
    evt.preventDefault();
  };
</script>
<style>
  .formulario > .panel-default > .panel-heading {
	  background-color: #bce8f1 !important;
  }
  .tt-input{
	  width:100%;
  }
  .tt-query {
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  }

  .tt-hint {
    color: #999
  }

  .tt-menu {
    margin-top: 4px;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
        border-radius: 4px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
    -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        box-shadow: 0 5px 10px rgba(0,0,0,.2);
    overflow-y: scroll; 
    max-height: 350px;
  }

  .tt-suggestion {
    line-height: 24px;
  }

  .tt-suggestion.tt-cursor,.tt-suggestion:hover {
    color: #fff;
    background-color: #1E9966;

  }

  .tt-suggestion p {
    margin: 0;
  }
  .twitter-typeahead{
    width:100%;
  }
</style>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#acc_caso{{{ $caso['id_caso'] }}}">
          <div class="row">
              <div class="col-md-2">{{$caso['fecha_termino']}}</div>
              <div class="col-md-2">Fecha de ingreso:</div>
              <div class="col-md-2">{{ \Carbon\Carbon::parse($caso['fecha_ingreso'])->format('d/m/Y H:i') }}</div>
              <div class="col-md-2">Fecha de hospitalización:</div>
              @if($caso['fecha_hosp'] == '-')
                <div class="col-md-2">-</div>
              @else
                <div class="col-md-2">{{ \Carbon\Carbon::parse($caso['fecha_hosp'])->format('d/m/Y H:i') }}</div>
              @endif
          </div>
          <br>
          <div class="row">
          <div class="col-md-2">Establecimiento:</div>
            <div class="col-md-2">{{{ $caso['estab'] }}}</div>
            <div class="col-md-2">Diagnóstico:</div>
            <div class="col-md-2">{{{ ucwords($caso['diagnostico']) }}}</div>
          </div>
        </a>
      </h4>
      @if(Auth::user()->tipo == TipoUsuario::ADMIN || Auth::user()->tipo == TipoUsuario::MASTER  || Auth::user()->tipo == TipoUsuario::MASTERSS)
      <a class="btn btn-xs btn-success" onclick="modalModificarFechas({{$caso['id_caso'] }})">Modificar fechas</a>   
      @endif
    </div>
    <div id="acc_caso{{{ $caso['id_caso'] }}}" class="panel-collapse collapse">
      <div class="panel-body">
          {{-- Boletin de egreso --}}
          @if($caso['fecha_termino'] != "Caso abierto" && (Auth::user()->tipo == TipoUsuario::ESTADISTICAS || Auth::user()->tipo == TipoUsuario::MASTER  || Auth::user()->tipo == TipoUsuario::MASTERSS) )
            <div class="row"> 
              <div class="col-md-12">
                <div class="col-md-3">
                  <a class="btn btn-primary" href="../paciente/egreso/{{$caso['id_caso'] }}">Ver boletín de egreso</a>  
                </div>
                <div class="col-md-2">
                  <a class="btn btn-primary" onclick="enviarDerivado({{ $caso['id_caso'] }})">Derivación</a>  
                </div>  
              </div>
            </div>
            <br>
            <br>
          @elseif(Auth::user()->tipo == TipoUsuario::MEDICO || Auth::user()->tipo == TipoUsuario::MASTER || Auth::user()->tipo == TipoUsuario::MASTERSS)
          <div class="col">
                <a class="btn btn-primary" href="{{URL::to('')}}/gestionMedica/{{base64_encode($caso['id_caso'])}}">Gestión medica</a> 
                <a class="btn btn-primary col-md-offset-1" href="{{URL::to('')}}/gestionEnfermeria/{{$caso['id_caso'] }}">Registro clínico de enfermería</a> 
          </div>
            <br>
            <div>
            <fieldset>
                <legend>Indicaciones</legend>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                      <thead><tr>
                        <th>Fecha</th>
                        <th>Indicación</th>
                        <th>Opciones</th>
                      </tr></thead>
                      <tbody>
                        @if($indicaciones)
                            @foreach ($indicaciones as $indicacion)
                                <tr>
                                  <td id="{{$indicacion['id']}}EIF">{{{ $indicacion['fecha'] }}}</td>
                                  <td id="{{$indicacion['id']}}EIC">{{{ $indicacion['indicacion'] }}}</td>
                                  <td>
                                    <a class="btn btn-xs btn-warning" onclick="modalEIndicaciones({{ $indicacion['id'] }})">Editar</a>
                                    <a class="btn btn-xs btn-danger" onclick="eliminarIndicaciones({{ $indicacion['id'] }})">Eliminar</a>
                                  </td>
                                </tr>
                            @endforeach
                        @endif
                        
                      </tbody>
                    </table>
                </div>
            </fieldset></div>

            <div class="col">
                <a class="btn btn-primary" onclick="modalAIndicaciones({{$caso['id_caso'] }})">+ Indicación</a> 
            </div>
          @endif
          <div>
              <fieldset>
                  <legend>Diagnósticos</legend>
                  <div class="table-responsive">
                      <table class="table table-striped table-bordered table-hover">
                          <thead><tr>
                              <th>Fecha</th>
                              <th>Diagnóstico</th>
                              @if(Auth::user()->tipo == TipoUsuario::MEDICO || Auth::user()->tipo == TipoUsuario::MASTER || Auth::user()->tipo == TipoUsuario::MASTERSS)
                                <th>Comentario</th>
                                <th>Opciones</th>
                              @endif
                          </tr></thead>
                          <tbody>
                          @foreach ($diagnosticos as $diagnostico)
                              <tr>
                                  <td>{{{ $diagnostico['fecha'] }}}</td>
                                  <td>{{{ $diagnostico['diagnostico'] }}}</td>
                                  @if(Auth::user()->tipo == TipoUsuario::MEDICO || Auth::user()->tipo == TipoUsuario::MASTER  || Auth::user()->tipo == TipoUsuario::MASTERSS)
                                    <td id='{{$diagnostico['id']}}'>{{{ $diagnostico['comentario']}}}</td>
                                    <td><a class="btn btn-xs btn-warning" onclick="modificarDiagnosticos({{ $diagnostico['id'] }})">Editar</a></td>
                                  @endif
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
              </fieldset>
          </div>



        <div><fieldset>
          <legend>Categorización</legend>
          <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover tabla_categorizacion">
            <thead><tr>
            <th>Fecha oculta</th>
              <th>Fecha</th>
              <th>Categoría</th>
            </tr></thead>
            <tbody>
              @foreach ($evoluciones as $evolucion)
                  @if($evolucion['categoria'] != '')
                        <tr>
                        <td>{{{
                          \Carbon\Carbon::parse($evolucion['fecha'])->format('m/d/Y H:i')
                          }}}</td>
                          <td>{{{
                          \Carbon\Carbon::parse($evolucion['fecha'])->format('d/m/Y H:i')
                          }}}</td>
                          <td>{{{ $evolucion['categoria'] }}}</td>
                        </tr>
                  @endif
              @endforeach
            </tbody>
          </table>
          </div>
        </fieldset></div>

        {{--prueba--}}
        @if($caso['fecha_termino'] == "Caso cerrado")
        <div><fieldset>
          <legend>Detalle de Alta</legend>
          <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover">
            <thead><tr>
              <th>Fecha</th>
              <th>Diagnostico</th>
              <th>Medico</th>
              <th>Motivo Alta</th>
              <th>Detalle Alta</th>
            </tr></thead>
            <tbody>
              @forelse ($altas as $alta)
                  <tr>
                    <td>{{{ \Carbon\Carbon::parse($alta["fecha"])->format('d/m/Y H:i') }}}</td>
                    <td>{{{ ucwords($alta['diagnostico']) }}}</td>
                    <td>{{{ ucwords($alta['nombre_medico']) }}}</td>
                    <td>{{{ ucwords($alta['motivo_termino']) }}}</td>
                    <td>{{{ ucwords($alta['detalle_termino']) }}}</td>
                  </tr>
              @empty
                <tr>
                  <td> - </td>
                  <td> - </td>
                  <td> - </td>
                  <td> - </td>
                  <td> - </td>
                </tr>
              @endforelse

            </tbody>
          </table>
          </div>
        </fieldset></div>
        @endif
        {{--prueba--}}

        <div><fieldset>
          <legend>Historial de camas</legend>
          <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover">
            <thead><tr>
              <th>Fecha</th>
              <th>Servicio</th>
              <th>Sala</th>
              <th>Cama</th>
            </tr></thead>
            <tbody>
            

              @foreach ($traslados as $traslado)
              <tr>
                <td>{{{ \Carbon\Carbon::parse($traslado['fecha'])->format('d/m/Y H:i') }}}</td>
                <td>{{{ $traslado['serv'] }}}</td>
                <td>{{{ $traslado['sala'] }}}</td>
                <td>{{{ $traslado['cama'] }}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
        </fieldset></div>
        <div><fieldset>
          <legend>Historial de Derivación</legend>
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Fecha Ingreso</th>
                  <th>Fecha Egreso</th>
                  {{-- <th>Usuario Ingresa</th> --}}
                  {{-- <th>Usuario Egresa</th> --}}
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                @if($derivaciones)
                @foreach ($derivaciones as $derivado)
                    <tr>
                      <td>{{{ \Carbon\Carbon::parse($derivado['fecha_ingreso'])->format('d/m/Y H:i') }}}</td>
                      @if($derivado['fecha_egreso'] == null)
                      <td>---</td>
                      @else
                      <td>{{{ \Carbon\Carbon::parse($derivado['fecha_egreso'])->format('d/m/Y H:i') }}}</td>
                      @endif
                      {{-- <td>{{{ $derivado['usuario_ingresa'] }}}</td> --}}
                      {{-- <td>{{{ $derivado['usuario_egresa'] }}}</td> --}}
                      <td>{{{ $derivado['estado'] }}}</td>
                    </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </fieldset></div>
        <div><fieldset>
          <legend>Historial de Exámenes / Estudios / Procedimientos</legend>
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Fecha Ingreso</th>
                  <th>Fecha Modificación</th>
                  <th>Examen</th>
                  <th>Estado</th>
                  <th>Tipo</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody>
                  @foreach ($examenes as $exam)
                      <tr>
                      <td>{{{\Carbon\Carbon::parse($exam['fecha_ingreso'])->format('d/m/Y H:i')}}}</td>
                      <td>{{{\Carbon\Carbon::parse($exam['fecha_modificacion'])->format('d/m/Y H:i')}}}</td>
                      <td>{{$exam['examen']}}</td>
                      <td>{{$exam['pendiente']}}</td>
                      <td>{{$exam['tipo']}}</td>
                      <td>{{$exam['usuario']}}</td>
                      </tr>
                  @endforeach
              </tbody>
            </table>
          </div>
        </fieldset></div>

      </div>
    </div>
  </div>

  {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'formEnviarDerivado', 'autocomplete' => 'off')) }}
	  @include('Gestion/formularioDerivacion')
  {{ Form::close() }}

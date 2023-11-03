@extends("Templates/template")

@section("titulo")
Derivar Paciente
@stop

@section('css')
<link rel="stylesheet" href="../plugins/flexdatalist/jquery.flexdatalist.min.css">

@section("miga")
<li><a href="#">Urgencia</a></li>
<li><a href="#">Derivar Paciente</a></li>
@stop



@section("section")
 

<legend> Formulario de Derivación </legend>
<br>
(*) : Campo obligatorio.
<br>

<fieldset>

<div class="panel-heading"  >
  <h4>Datos Generales:</h4>
</div>
  
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <br>
@endif

@if(session()->has('msj'))
<script>
    $(document).ready(function()
    {

       $("#mostrarModal").modal("show");

       $('.btn.btn-primary').on('click' , function(){
        $('#volverUnidad').trigger('click');
   });
     

    });
 </script>

 
    
@endif
<div class="panel-body">
  <div class="tab-pane" id="settings">
  <form role="form" class="form-horizontal" id="formulario"  data-toggle="validator" method="POST" action="{{url('/derivacionNueva')}}">
        
        @csrf
            <div class="row" >
                <div class="col-xs-3">
                  
                        <label>Fecha Actual</label>
                            <div class="input-group">
                                <input type="text" id="fechaHoy" class="form-control" readonly>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                    </div>
                 
            </div>
            <br>
            <br>
    
            <div class="row form-group">
                <div class="col-xs-3">
                    <label> Nombre </label>
                <input id="nombre" type="text" class="form-control" value="{{$paciente->nombre}} " readonly > 

                <input type="hidden" name="idPaciente" id="idPaciente" value="{{$paciente->id}}" >
                </div>
                <div class="col-xs-3">
                    <label> Apellido Paterno </label>
                    <input type="text" class="form-control" id="apellidoP" value="{{$paciente->apellido_paterno}}" readonly> 
                </div>
                <div class="col-xs-3">
                    <label> Apellido Materno </label>
                    <input type="text" class="form-control" id="apellidoM" value="{{$paciente->apellido_materno}}" readonly> 
                </div>
            </div>

            <br>      
            <div class="row">
                    <div class="col-xs-3">
                        <label> Rut </label>
                    <input type="text" class="form-control" id="inputBusqueda" class="form-control" value="{{$paciente->rut}}-{{$paciente->dv}}"  readonly> 
                    <input type="hidden" name="idCaso" id="idCaso" value="{{$caso->id}}" >
                    <input type="hidden" name="url" id="url" value="{{$url}}" >
           

                    </div>
                   
                    <div class="col-xs-3">
                        <div class="input-group">
                                <label >Etario</label><label></label>
                                  <select class="form-control " name="etario" readonly>
                                      <option selected="selected"></option>
                                   {{--   @foreach ($edades as $edad)
                                        <option>{{$edad}}</option>
                                      @endforeach
                                    --}}
                                  </select>
                            </div>
                    </div>
                    <div class="col-xs-3">
                        <label> Edad </label>
                    <input type="text" class="form-control" id="edad"  readonly > 
                    </div>
                </div>
                <br>
                <div class="row">
                        <div class="col-xs-3">
                                <div class="input-group">
                                        <label >Previsión</label><label></label>
                                        <div >
                                          <input class="form-control " id="prevision" value={{$caso->prevision}} readonly>
                                         
                                           
                                        </div>
                                </div>
                            </div>
                        <div class="col-xs-6">
                            <label> Diagnostico CIE10 </label>
                        <input type="text" class="form-control" name="cieoDesc" id="cieoDesc" value="{{$cieo->nombre}}" readonly> 
                        </div>
                </div>
                <br>
                <div class="row">
                    <div  class="col-xs-5">
                        <label> Diagnóstico Clínico </label>
                    <textarea class="form-control" name="diagnosticoClinico" id="diagnosticoClinico" rows="3" readonly > {{$diagnostico->diagnostico}} </textarea>
                 </div>
                </div>

        <br>
        <br>

              </div>
        <legend>Datos Derivacion</legend>

            @csrf
            
            <div class="row" >
                    <div class="col-xs-3">
                            <label >Fecha hospitalización</label>
                            <div class="from-group input-group date">
                                <input type="text" class="form-control pull-right" name="fechaHospitalizacion" id="fechaHospitalizacion" readonly>
                            </div>
                        </div>
                    <div class="col-xs-3">
                            <label >Fecha solicitud(*)</label>
                            <div class="from-group input-group date">
                              <input type="text" class="form-control pull-right" name="fechaSolicitud" id="fechaSolicitud" required>
                            </div>
                    </div>
                    <div class="col-xs-3">
                      <label >Fecha derivación(*)</label>
                        <div class="from-group input-group date">
                          <input type="text" class="form-control pull-right" name="fechaDeri" id="fechaDeri" required>
                        </div>
                    </div>
            </div>
                <br>
                <br>
                <div class="row">
                        <div class="col-xs-3">
                                <div class="input-group">
                                        <label >Unidad funcional que deriva</label><label></label>
                                      
                                          <select class="form-control " name="unidadQderiva" id="unidadQderiva" >
                                        
                                        @if($unidades)
                                          @foreach ($unidades as $unidad)
                                            <option>{{$unidad->nombre}}</option>
                                          @endforeach
                                        @else 
                                           <option>SIN UNIDAD ASOCIADA</option>
                                        @endif
                                          </select>
                                </div>
                        </div>
                        <div class="col-xs-5">
                                <label> Tipo traslado </label>
                                <select class="form-control " name="tipoTraslado" id="tipoTraslado" >
                                  {{-- @foreach ($unidades as $unidad)
                                 <option>{{$unidad->nombre}}</option>
                                   @endforeach--}}
                            
                                   </select>
                        </div>
                </div>
                <br>
                <br>
                <div class="row">
                        <div class="col-xs-5">
                                  <label >Motivo de derivación</label><label></label>
                                          <select class="form-control" type="text" id="motivoDerivacion" name="motivoDerivacion" >
                                          @foreach ($motivos as $motivo)
                                        <option>{{$motivo->enum_value}}</option>
                                          @endforeach

                                                        <select class="form-control " name="movil" >
                              <option selected="selected"></option>
                           {{--   @foreach ($edades as $edad)
                                <option>{{$edad}}</option>
                              @endforeach
                            --}}
                    </select>
                                   
                                 
                        </div>
                        <div class="col-xs-4">
                                <label> Detalle otro </label>
                                <input type="text" class="form-control" id="detalleOtro" name="detalleOtro"> 
                        </div>
                </div>
                <br>
                <div class="row">
                        <div class="col-xs-6">
                                <label> Medico Derivador </label>

                        @if($medico)
                          <input  placeholder="Ingrese el nombre del médico" data-min-length='1' data-selection-required='true' type="text" class="form-control" id="medicoDerivador" value="{{$medico->nombre_medico}}" required> 
                          <input type="hidden" name="idMedico" id="idMedico" value="{{$medico->id_medico}}" >
                        @else 
                         <input type="text" class="form-control" id="medicoDerivador" value=""> 
                         <input type="hidden" name="idMedico" id="idMedico" value="" >
                         @endif
                        </div>

                        <div class="col-xs-3">
                                <label> Rut Médico </label>
                        @if($medico)
                        <input  placeholder='Ingrese el nombre del médico' data-min-length='1' data-selection-required='true'  type="text" class="form-control" id="rutMedico" name="rutMedico" value="{{$medico->rut_medico}}-{{$medico->dv_medico}}" readonly> 
                        @else 
                        <input type="text" class="form-control" id="rutMedico" value="" readonly> 
                        @endif  
                      </div>
                    
                </div>  
                <br>
                <div class="row">
                        <div class="col-xs-3">
                            <label>GES     </label>
                                <div>
                                  <label class="radio-inline">
                                    <input type="radio" name="optGes" value="true">Sí
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="optGes" value="false" checked>No
                                  </label>
                                </div>
                        </div>
                        <div class="col-xs-3">
                                <label>UGCC     </label>
                                <div id="ugcoptions">
                                    <label class="radio-inline">
                                      <input type="radio" name="optUgcc" id="optUgcc" value="true" >Sí
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="optUgcc" id="optUgcc" value="false" checked>No
                                    </label>
                                  </div>
                                
                        </div>
                        <div class="col-xs-4">
                                <div class="input-group">
                                        <label >Tipo UGCC</label><label></label>
                                          <select class="form-control " name="tipoUgcc" id="tipoUgcc" disabled>
                                          
                                              @foreach ($tiposUgcc as $tipoUg)
                                          <option>{{$tipoUg->enum_value}}</option>
                                              @endforeach
                                          
                                    </select>
                                </div>
                        </div>
                </div> 
            <br>
            <br>

            <div class="row">
                    <div class="col-xs-4">
                            <div class="input-group">
                                    <label >Centro derivación</label>
                                    <select class="form-control" name="centroDer" id="centroDer" >
                                          
                                        @foreach ($establecimientos as $e)
                                    <option value="{{$e->id}}" >{{$e->nombre}}
                               
                                    
                                    </option>
                                        @endforeach
                                    
                              </select> 
                                
                            </div>
                    </div>  
                    <div class="col-xs-3">
                            <label>  Otro centro </label>
                            <input type="text" class="form-control" id="otroCentro" name="otroCentro"> 
                    </div>
            </div>
        <br>
        <div class="row">
                <div class="col-xs-4">
                        <div class="input-group">
                                <label >Tipo de centro (red pública)</label><label></label>
                                  <select class="form-control" name="tipoCentroPublica" >
                                      <option selected="selected"> - </option>
                                   {{--   @foreach ($edades as $edad)
                                        <option>{{$edad}}</option>
                                      @endforeach
                                    --}}
                            </select>
                        </div>
                </div> 

                <div class="col-xs-4">
                        <div class="input-group">
                                <label >Tipo de centro (red privada)</label><label></label>
                                  <select class="form-control " name="tipoCentroPrivada" >
                                      <option selected="selected"> - </option>
                                   {{--   @foreach ($edades as $edad)
                                        <option>{{$edad}}</option>
                                      @endforeach
                                    --}}
                            </select>
                        </div>
                </div> 
        </div>
        <br>
       

        <div class="row">
                <div class="col-xs-4">
                        <div class="input-group">
                                <label >Vía traslado  </label><label></label>
                                  <select class="form-control " name="viaTras" >
                                      <option selected="selected"> - </option>
                                   {{--   @foreach ($edades as $edad)
                                        <option>{{$edad}}</option>
                                      @endforeach
                                    --}}
                            </select>
                        </div>
                </div> 

                <div class="col-xs-4">
                        <div class="input-group">
                                <label >Traslado Terrestre</label><label></label>
                                  <select class="form-control " name="trasladoT" >
                                      <option selected="selected"> - </option>
                                   {{--   @foreach ($edades as $edad)
                                        <option>{{$edad}}</option>
                                      @endforeach
                                    --}}
                            </select>
                        </div>
                </div> 
        </div>
        <br> 
        <div class="row">
                <div class="col-xs-3">
                        <label>  Origen </label>
                        <input type="text" class="form-control" id="origenDerivacion" name="origenDerivacion"> 
                </div>

                <div class="col-xs-3">
                        <label>  Ida </label>
                        <input type="text" class="form-control" id="idaDerivacion" name="idaDerivacion"> 
                </div>
                <div class="col-xs-3">
                        <label >Fecha Ida</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                           </div>
                            <input type="text" class="form-control pull-right" name="fechaIda" id="fechaIda">
                      
                        </div>
                    </div>
                
        </div>
        <br>     
        <div class="row">
                <div class="form-group col-xs-3" style="margin: 0px">
                        <label class="control-label">  Destino (*) </label>
                        <select class="form-control" id="destino" name="destino" required>
                        </select> 
                </div>
             
                <div class="form-group col-xs-3" style="margin: 0px">
                        <label>  Rescate </label>
                        <input type="text" class="form-control" id="rescateDerivacion" name="rescateDerivacion"> 
                </div>
               
                <div class="form-group col-xs-3" style="margin: 0px">
                        <label >Fecha Rescate</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                           </div>
                            <input type="text" class="form-control pull-right" name="fechaResc" id="fechaRescate">
                      
                        </div>
                    </div>     
        </div>
        <br>
        <div class="row">
            <div class="col-xs-4">
                <div class="input-group">
                        <label >Movil</label><label></label>
                          <select class="form-control " name="movil" >
                              <option selected="selected"></option>
                           {{--   @foreach ($edades as $edad)
                                <option>{{$edad}}</option>
                              @endforeach
                            --}}
                    </select>
                </div>  
            </div>
            <div class="col-xs-4">
                <div class="input-group">
                        <label >Compra Servicios</label><label></label>
                          <select class="form-control " name="compraSer" >
                              <option selected="selected"></option>
                           {{--   @foreach ($edades as $edad)++
                                <option>{{$edad}}</option>
                              @endforeach
                            --}}
                    </select>
                </div>
            </div>  
        </div>
        <br>
        <div class="row">
          <div class="col-xs-4">
            <div class="input-group">
                    <label >Comentarios</label><label></label>
                    <textarea class="form-control" name="comentarios" id="comentarios" rows="4" cols="200" ></textarea>
            </div>
          </div>
        </div>  
        <br>
        <br>
        <br>
        
        </form>
        <div class="row">
          
            <div class="form-group col-md-10 btn-group">
         
      
            <button type="submit" form="formulario" id="derivarBoton" class="btn btn-primary">Derivar Paciente
            </button>

              @if($url != "error")
            
                <form action='../unidad/{{$url}}' method='GET'>
                  <input hidden type='text' name='paciente' value='{{$paciente->id}}'>
                  <input hidden type='text' name='id_sala' value='{{$sala}}'>
                  <input hidden type='text' name='id_cama' value='{{$cama}}'>
                  <input hidden type='text' name='caso' value='{{$caso->id}}'>
                
                <button class="btn btn-danger" type="submit" id="volverUnidad">Cancelar</button>
             
                </form>
              @endif
          </div>
          
           
        </div>       
        
      </div>
 

      <div class="modal" role="dialog" id="mostrarModal">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <h4>Se ha ingresado la derivación sin problemas.</h2> <output name="fecax" for="fechaDeri"></output>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal" id="cerrarVentana">Cerrar</button>
      
              </div>
            </div>
          </div>
      </div>
      
 
 

 
</fieldset>

@stop

@section("script")
<script src="../plugins/flexdatalist/jquery.flexdatalist.js" charset="utf-8"></script>

<script>
 
 $(document).ready(function(){


     $("#fechaHoy").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });

    $("#fechaSolicitud").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });
    
    
    $("#fechaHospitalizacion").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });

    $("#fechaDeri").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });

    $("#fechaRescate").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });
    
    $("#fechaIda").datetimepicker({
        locale: "es",
        format: "DD-MM-YYYY",
        defaultDate: moment(),
        sideBySide: true
    });
    $("#fechaHospitalizacion").val("{{$thistorial->fecha_ingreso_real}}");
    /**
    $('#inputBusqueda').flexdatalist({
      minLength: 1,
      requestType: 'GET',
      selectionRequired: true,
      data: "{{url('/data/paciente/lista')}}",
      params: {
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      },
      minLength: 1,
      selectionRequired: true,
      searchByWord: true,
      searchIn: ['rut'],
      visibleProperties: ["rut"],
    }); **/
    var fechaNac = "{{$paciente->fecha_nacimiento}}";
    
    $('#edad').val(getEdad(fechaNac));
  
   
     
    $('#medicoDerivador').flexdatalist({
      minLength: 1,
      requestType: 'GET',
      selectionRequired: true,
      data: "{{url('/data/medico/lista')}}",
  
      minLength: 1,
      selectionRequired: true,
      searchByWord: true,
      searchIn: ['nombre_medico'],
      visibleProperties: ["nombre_medico"],
    });
    $('#medicoDerivador').on('select:flexdatalist',function(event,set,options){
      $('#rutMedico').val(set.rut_medico+"-"+set.dv_medico);
      $('#rutMedico').prop('readonly', true);
      $('#rutMedico').prop('disabled', true);
      $('#rutMedico').prop('required', false);
      $('#idMedico').val(set.id_medico);
    

    
    });
    
  
    /**
    function cargarDatosForm($id){
        var casoPa;
         
    
        $.ajax({
          url: "{{url('data/paciente')}}"+"/"+id,
          method: "GET",
          success: function(data){
            let datosPaciente = JSON.parse(data);
            $('#inputBusqueda').prop('required', true);
      
            $('#nombre').val(datosPaciente.nombre);
            $('#nombre').prop('readonly', true);
            $('#nombre').prop('disabled', true);
            $('#nombre').prop('required', false);
      
            $('#apellidoP').val(datosPaciente.apellido_paterno);
            $('#apellidoP').prop('readonly', true);
            $('#apellidoP').prop('disabled', true);
            $('#apellidoP').prop('required', false);
            $('#apellidoM').val(datosPaciente.apellido_materno);
            $('#apellidoM').prop('readonly', true);
            $('#apellidoM').prop('disabled', true);
            $('#apellidoM').prop('required', false);
            $('#edad').val(getEdad(datosPaciente.fecha_nacimiento));
            $('#edad').prop('readonly', true);
            $('#edad').prop('disabled', true);
            $('#edad').prop('required', false);
            $('#divLoading').hide();
          },
         
        });

        $.ajax({
          url: "{{url('data/caso/lista')}}",
          method: "GET",
          success: function(data){
            let datosCaso = JSON.parse(data); 
            
            var previsiones =  unique(datosCaso);

            $.each(datosCaso,function(key, caso) {
                    if(caso.paciente ==id){
                        casoPa = caso;
                       // cargaDiagnostico(casoPa.id);
                     
                         cargaTHistOc(casoPa.id);
                        //cargaEstablecimiento(casoPa.id);
                        //cargaUnidadEstablecida(casoPa.id);
                        
                        $.ajax({
                          url: "{{url('data/medico/lista')}}",
                          method: "GET",
                          success: function(data){
                          let datosMedicos = JSON.parse(data);
                          let datoMedico;
                          $.each(datosMedicos,function(key, rec) {
                            if(casoPa.id_medico == rec.id_medico){
                              datoMedico = rec;
                            }
                          });   
                         
                          if(datoMedico){
                            console.log(datoMedico.nombre_medico);
                            $('#medicoDerivador').val(datoMedico.nombre_medico,datoMedico.apellido_medico);
                            $('#rutMedico').val(datoMedico.rut_medico);
                            }
                          else{
                            $('#medicoDerivador').val('');
                            $('#rutMedico').val('');
                          }
                          }
                      });
                    }
                    else{

                      $('#diagnosticoClinico').val('');
                      $('#diagnosticoClinico').val('');
                  

                    }
            });

              
             
            $.each(previsiones,function(key, prevision) {  
                $("#prevision").append("<option>"+prevision+"</option>");
            });  
            $("#prevision").val(casoPa.prevision); 
          }, 
        });
      } */
      
      
    function getEdad(dateString)
    {
        var today = new Date(); 
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear(); 
        var m = today.getMonth() - birthDate.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())){
            age--; 
        } 

        return age; 
    }

    function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e.prevision, result) == -1) result.push(e.prevision);
    });
    return result;
    }
    /*
    function cargaDiagnostico(id){
        $.ajax({
          url: "{{url('data/diagnostico/lista')}}",
          method: "GET",
          success: function(data){
            let datosDiag = JSON.parse(data);
            let diagnostico
            $.each(datosDiag,function(key, diag) {
               if(diag.caso == id){
                    diagnostico = diag;
                    cargaCIEO(diagnostico.id_cie_10);
               }
            });   
           
            if(diagnostico){
                $('#diagnosticoClinico').val(diagnostico.diagnostico);
            }
          }
        });

    }


    function cargaCIEO(id){
        $.ajax({
          url: "{{url('data/cieo/lista')}}",
          method: "GET",
          success: function(data){
            let datosCieo = JSON.parse(data);
            let ciao
            $.each(datosCieo,function(key, cie) {
               if(cie.id_cie_10 == id){
                ciao = cie;
    
               }
            });   
           
            if(ciao){
                $('#cieoDesc').val(ciao.nombre);
            }
          }
        });

    }
  
 
    function cargaUnidadEstablecida(id){
        $.ajax({
          url: "{{url('data/uestablecida/lista')}}",
          method: "GET",
          success: function(data){
            let datosDiag = JSON.parse(data);
            let diagnostico
            $.each(datosDiag,function(key, diag) {
               if(diag.caso == id){
                    diagnostico = diag;
               }
            });   
           
            if(diagnostico){
                $('#diagnosticoClinico').val(diagnostico.diagnostico);
            }
          }
        });

    }
    function cargaEstablecimiento(id){
        $.ajax({
          url: "{{url('data/establecimiento/lista')}}",
          method: "GET",
          success: function(data){
            let datosDiag = JSON.parse(data);
            let diagnostico
            $.each(datosDiag,function(key, diag) {
               if(diag.caso == id){
                    diagnostico = diag;
               }
            });   
           
            if(diagnostico){
                $('#diagnosticoClinico').val(diagnostico.diagnostico);
            }
          }
        });

    }
    function cargaTHistOc(id){
        $.ajax({
          url: "{{url('data/thistoric/lista')}}",
          method: "GET",
          success: function(data){
            let datosTH = JSON.parse(data);
            let datoTH
            $.each(datosTH,function(key, rec) {
               if(rec.caso == id){
                  datoTH = rec;
               }
            });   
           
            if(datoTH){
                $('#fechaHospitalizacion').val(datoTH.fecha_ingreso_real);
                $('#fechaHospitalizacion').prop('readonly', true);
                $('#fechaHospitalizacion').prop('disabled', true);
                $('#fechaHospitalizacion').prop('required', false);
            }
            else{
                $('#fechaHospitalizacion').val('');
                $('#fechaHospitalizacion').prop('readonly', false);
                $('#fechaHospitalizacion').prop('disabled', true);
                $('#fechaHospitalizacion').prop('required', true);

            }
          }
        });

    }

    */
 
 
$('#ugcoptions input:radio').change(function(){ 
    var valor = $("#ugcoptions input:radio:checked").val();
    console.log(valor);

    if(valor.localeCompare(true)==0){         

      $('#tipoUgcc').prop('disabled', false);
           
    }
    else{
      $('#tipoUgcc').prop('disabled', true);
    }
});
 
  

cargarUE();
$('#centroDer').on('change',function(){ 
  $("#destino").empty();
   cargarUE();

 
});


 
  

function cargarUE(){
  var idEstablecimiento = $("#centroDer").val();
  $.ajax({
      url: "{{url('/data/uestablecimiento/lista')}}"+"/"+idEstablecimiento,
      method: "GET",
      success: function(data){
        let dataEstable = JSON.parse(data);
 
        $.each(dataEstable,function(key, diag) {
        
          $("#destino").append("<option value="+diag.id+">"+diag.alias+"</option>");
        });   
    
      }
    });
}


      
});
 


   

</script>
@stop
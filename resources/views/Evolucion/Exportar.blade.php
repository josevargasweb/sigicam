@extends("Templates/template")

@section("titulo")
    Categorización
@stop

@section("miga")
<li><a href="#">Categorización</a></li>
<li><a href="#" onclick='location.reload()'>Exportar categorización</a></li>
@stop

@section("script")

    <script>

        $(function(){
            $(".fecha-mm-yyyy").datepicker({
                autoclose: true,
                language: "es",
                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months",
                todayHighlight: true,
                endDate: "+0d"
            }).on("changeDate", function(){
                $('#upload').bootstrapValidator('revalidateField', 'fecha');
            });
            $("#evolucionMenu").collapse();

            $("#form-generar-evolucion").bootstrapValidator({
                excluded: ':disabled',
                fields: {
                    fecha: {
                        validators:{
                            notEmpty: {
                                message: 'Debe especificar la fecha'
                            }
                        }
                    }
                }
            }).on('status.field.bv', function(e, data) {
                $("#form-generar-evolucion input[type='submit']").prop("disabled", false);
            }).on("success.form.bv", function(evt){
                evt.preventDefault(evt);
                $("#resultado").html("");
                var $form = $(evt.target)[0];
                console.log(new FormData($form));
                $("#loading").show();
                $.ajax({
                    url: "cargar",
                    type: "post",
                    dataType: "json",
                    data: new FormData($form),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        if(data.out) {
                            $("#resultado").html(data.contenido);
                        }
                        if(!data.out) {
                            	swalInfo.fire({
                                    title: 'Información',
                                    text:data.msg
                                    });
                            console.log(data.msg);
                        }
                        $("#form-generar-evolucion input[type='submit']").prop("disabled", false);
                        $("#loading").hide();
                    },
                    error: function(error){
                        $("#loading").hide();
                        console.log(error);
                    }
                });
                return false;
            });

        });

    </script>

@stop

@section("section")
    <fieldset>
        <legend>Seleccionar mes </legend>
        <div class="panel panel-default">
            <div class="panel-body">
                {{ Form::open(array('url' => '#', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'form-generar-evolucion', 'onsubmit' => 'return false')) }}
                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                    <label for="fecha" class="col-sm-1 control-label">Fecha: </label>
                    <div class="col-sm-4">
                        {{ Form::text("fecha", \Carbon\Carbon::now()->format("m-Y"), array("class" => "form-control fecha-mm-yyyy")) }}
                    </div>
                    @if(Session::get("idEstablecimiento") == "")
                    <div class="col-sm-4">
                        
                        {{ Form::select('establecimiento', $establecimientos, null, array('class' => 'form-control', 'id' => 'establecimiento')) }}
                    </div>
                    @else
                    {{ Form::hidden('establecimiento', Session::get("idEstablecimiento"), array('id' => 'establecimiento')) }}
                    @endif

                    <div class="col-md-2">
                        {{ Form::submit('Generar datos', array("class" => "btn btn-primary")) }}
                    </div>
                    <div class="col-md-7"></div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </fieldset>
    <div id="resultado">
    </div>



@stop


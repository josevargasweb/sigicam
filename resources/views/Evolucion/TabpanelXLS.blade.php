
@extends('Evolucion/Resultado')

<script>
    $(function(){
        $("#tab-resultados a").click(function(e){
            e.preventDefault();
            $(this).tab("show");
        });

        $("#tab-resultados a:first").tab("show");

        $(".fecha-yyyy").datepicker({
            autoclose: true,
            language: "es",
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            todayHighlight: true,
            endDate: "+0d"
        }).on("changeDate", function(){
            $('#upload').bootstrapValidator('revalidateField', 'fecha');
        });

        $("#form-datos-evolucion").bootstrapValidator({
            fields:{
                @section("validator-fields")
                @show
            }
        }).on('success.form.bv', function(e){
            e.preventDefault();
            var form = $(e.target);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: form.attr('method'),
                dataType: 'json',
                success: function(data){
                    if(data.exito){
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
                    if(data.error) {
                        swalError.fire({
						title: 'Error',
						text:data.error
						});
                    }
                },
                error: function(error){

                }
            });
        });
    });

</script>

@foreach($datos as $info)
@section('tabs')
    <li role="presentation"><a href="#tab-{{$info->nombreId()}}">{{$info->nombre}}</a></li>
    @parent
@stop

@section('contenido')
    <div role="tabpanel" class="tab-pane panel panel-default" id="tab-{{$info->nombreId()}}">
        @if($info->comprobar(Session::get("idEstablecimiento")))
            <div class="panel-heading">{{$info->getNombreHospital()}}</div>
            <div class="panel-body">
                @if(!$info->servicio)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">{{$info->error_unidad}}</div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-3 col-xs-12 col-sm-6">
                        {{ Form::select("servicio-{$info->nombreId()}", [0 => "Seleccionar servicio"] + $info->establecimiento_original->getUnidades(), $info->servicio, array("class" => "form-control")) }}
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-6">
                        {{ Form::select("mes-{$info->nombreId()}", [
                        1 => 'Enero',
                        'Febrero',
                        'Marzo',
                        'Abril',
                        'Mayo',
                        'Junio',
                        'Julio',
                        'Agosto',
                        'Septiembre',
                        'Octubre',
                        'Noviembre',
                        'Diciembre',], $info->getMes(), array("class" => "form-control")) }}
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-6">
                        {{ Form::text("anno-{$info->nombreId()}", $info->anno, array("class" => "form-control fecha-yyyy")) }}
                    </div>
                    <div class="col-md-3 col-xs-12 col-sm-6">
                        {{ Form::text("encargado-{$info->nombreId()}", $info->enfermera, array("class" => "form-control")) }}
                    </div>
                </div>
            </div>
            <div class="panel-body">
            	<div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr>
                        <th>DIA</th>
                        @foreach($info->categorias as $cat)
                            <th>{{$cat}}</th>
                        @endforeach
                    </tr></thead>
                    <tbody>
                    @foreach($info->rangoDias() as $dia)
                        <tr><td>{{$dia}}</td>
                            @foreach($info->categorias as $cat)
                                <td>{{  $info->{$cat}[$dia] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <p>{{$info->error}}</p>
            </div>
        @endif
    </div>
    @parent
@stop

@section('validator-fields')
    "{{ "servicio-{$info->nombreId()}" }}":{
    validators:{
    greaterThan:{
    inclusive: false,
    message: "Debe seleccionar un servicio.",
    value: 0
    }
    }
    },
    "{{ "mes-{$info->nombreId()}" }}":{
    validators:{
    notEmpty:{
    inclusive: false,
    message: "Debe seleccionar un mes.",
    value: 0
    }
    }
    },
    "{{ "anno-{$info->nombreId()}" }}":{
    validators:{
    notEmpty:{
    message: "Debe ingresar el año.",
    }
    }
    },
    "{{ "encargado-{$info->nombreId()}" }}":{
    validators:{
    notEmpty:{
    message: "Debe ingresar el nombre del encargado.",
    }
    }
    },
    @parent
@stop
@endforeach


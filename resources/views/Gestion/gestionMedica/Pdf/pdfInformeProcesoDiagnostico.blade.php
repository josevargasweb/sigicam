<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{ HTML::style('css/bootstrap.css') }}
    {{-- {{ HTML::script('js/bootstrap.js') }} --}}
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="margin-left: auto;">
            <div class="column" style="width: 50%;">
                <label for="num_folio" class="control-label">FOLIO N°</label>
                <p>{{$informe->num_folio}}</p>
            </div>
            <div class="column" style="width: 50%;">
                <label for="nombre">FECHA INFORME</label>
                <p>{{Carbon\Carbon::parse($informe->fecha_informe)->format("d-m-Y H:i:s")}}</p>
            </div>
        </div>
        <br>
        <div class="panel panel-default">
            <div class="row" style="margin-left: auto;">
                <div class="column primera" style="width: 50%;">
                    <label for="">1. Servicio de Salud </label>
                    <p id="nombre_servicio_salud">{{$infoExtraInforme["servicio_salud"]}}</p>
                </div>
                <div class="column" style="width: 50%;">
                    <label for="">2. Establecimiento</label>
                    <p id="establecimiento">{{$infoExtraInforme["nombre_establecimiento"]}}</p>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="column" style="width: 50%;">
                    <label for="">3. Especialidad</label>
                    <p>{{$informe->especialidad}}</p>
                </div>
                <div class="column" style="width: 50%;">
                    {{-- <div class="form-group"> --}}
                        <label for="">4. Unidad</label>
                        <p id="unidad">{{$infoExtraInforme["nombre_unidad"]}}</p>
                    </div>
                </div>
            </div>
        </div>                        
        <br>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4 class="modal-title">DATOS DEL (DE LA) PACIENTE</h4>
            </div>
            <div class="row" style="margin-left: auto;">
                <div class="column" style="width: 50%;">
                    <label for="">5. Nombre</label>
                    <p id="nombre_paciente">{{$infoExtraInforme["nombre_paciente"]}}</p>
                </div>
                <div class="column" style="width: 50%;">
                    <div class="form-group">
                        <label for="">6. Historia clinica</label>
                        <p>{{$informe->historia_clinica}}</p>
                    </div>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="column" style="width: 36%;">
                    <label for="">7. RUT.</label>
                    <p id="rut_paciente">{{$infoExtraInforme["rut_paciente"]}}</p>
                </div>
                <div class="column" style="width: 27%;">
                    <p>8. Si es recien nacido, RUT de padre o madre beneficiario</p>
                </div>
                <div class="column" style="width: 36%;">
                    <label for="rut_beneficiario" class="control-label" title="Rut">Run: </label>
                    <div class="input-group" style="z-index: 0;">
                        @if ($informe->rut_beneficiario)
                            <p>{{$informe->rut_beneficiario}} - {{$dv_beneficiario}}</p>
                        @endif
                    </div>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="column" style="width: 33%;">
                    <label for="fecha_nacimiento">9. Sexo</label>
                    <p>{{$infoExtraInforme["sexo_paciente"]}}</p>
                </div>
                <div class="column" style="width: 33%;">
                    <label for="fecha_nacimiento">10. Fecha de Nacimiento</label>
                    @if ($infoExtraInforme["fecha_nacimiento"])
                        <p id="fecha_nacimiento">{{$infoExtraInforme["fecha_nacimiento"]}}</p>
                    @endif
                </div>
                <div class="column" style="width: 33%;">
                    <label for="edad">11. Edad</label>
                    @if($infoExtraInforme["edad"])
                        <p id="edad">{{$infoExtraInforme["edad"]}} año(s)</p>
                    @endif
                </div>
            </div>
        </div>
        <br>
        <div class="panel panel-default">
            <div class="panel-heading panel-info">
                <h4 class="modal-title">Datos Clinicos</h4>
            </div>
            <div class="row" style="margin-left: auto;">
                <div class="column" style="width: 60%;">
                    <div class="form-group">
                        <label for="">12. Problema de salud AUGE</label>
                        <p>{{$informe->problema_saluda_auge}}</p>
                    </div>
                </div>
                <div class="column" style="width:40%;">
                    <label for="">13. ¿Confirma que el diagnostico pertenece al sistema AUGE?</label><br>
                    <p>aca: {{$informe->confirmacion_auge}}</p>
                    @if($informe->confirmacion_auge == true)
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "no", false, array('id' => 'confirmacion_auge'))}}No</label>
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "si", true, array('id' => 'confirmacion_auge'))}}Sí</label>
                    @elseif($informe->confirmacion_auge == false)
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "no", true, array('id' => 'confirmacion_auge'))}}No</label>
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "si", false, array('id' => 'confirmacion_auge'))}}Sí</label>
                    @elseif($informe->confirmacion_auge == null || $informe->confirmacion_auge == "")
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "no", false, array('id' => 'confirmacion_auge'))}}No</label>
                        <label class="radio-inline">{{Form::radio('confirmacion_auge', "si", false, array('id' => 'confirmacion_auge'))}}Sí</label>
                    @endif
                    
                </div>
            </div>
            <br>
            <div class="row" style="margin-left: auto;">
                <div class="col-md-12">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label for="">14. Subgrupo o subcategoria de salud AUGE</label>
                            <textarea class="form-control" name="subgrupo_salud_auge" id="subgrupo_salud_auge" cols="5" rows="3">{{$informe->subgrupo_salud_auge}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="col-md-12">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label>15. Diagnósticos</label>
                            @foreach($infoExtraInforme["diagnosticos"] as $diagnostico)
                                <li class="list-group-item list-group-item-success" id="diagnostico_informe">{{$diagnostico["diagnostico"]}}</li>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="col-md-12">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label for="">16. Fundamentos del diagnóstico</label>
                            <textarea class="form-control" name="fundamentos_diagnostico" id="fundamentos_diagnostico" cols="5" rows="3">{{$informe->fundamentos_diagnostico}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <hr id="hr_menos_separacion">
            <div class="row" style="margin-left: auto;">
                <div class="col-md-12">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label for="">17. Tratamiento e indicaciones</label>
                            <textarea class="form-control" name="tratamiento_indicaciones" id="tratamiento_indicaciones" cols="5" rows="3">{{$informe->tratamiento_indicaciones}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: auto;">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">18. El tratamiento deberá iniciarse a más tardar el</label>
                            {{Form::text('fecha_inicio_tratamiento', Carbon\Carbon::parse($informe->fecha_informe)->format("d-m-Y H:i:s"), array('id' => 'fecha_inicio_tratamiento', 'class' => 'form-control dtp_fechasInforme'))}}
                            <!-- {{Carbon\Carbon::parse($informe->fecha_informe)->format("d-m-Y H:i:s")}} -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<style>
    #hr_menos_separacion{
        margin-top: 0px;
        margin-bottom: 10px;
    }

    .column {
        float: left;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }
</style>

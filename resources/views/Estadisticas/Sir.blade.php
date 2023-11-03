@extends("Templates/template")

@section("titulo")
Estadísticas
@stop

@section("miga")
<li><a href="#">Estadisticas</a></li>
<li><a href="#" onclick='location.reload()'>Reporte de casos sociales</a></li>
@stop

@section("script")
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
<!-- <script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script> -->
<script>

$(function() {
    $("#estadistica").collapse();
    $("#grafico").hide();

    $("#formSir").bootstrapValidator({
            excluded: ':disabled',
            fields: {
                n: {
                    validators:{
                        notEmpty: {
                            message: 'El campo es obligatorio'
                        },
                        integer: {
                            message: 'Debe ingresar un número'
                        }
                    }
                },
                beta: {
                    validators:{
                        notEmpty: {
                            message: 'El campo es obligatorio'
                        },
                        number: {
                            message: 'Debe ingresar un número'
                        }
                    }
                },
                gama: {
                    validators:{
                        notEmpty: {
                            message: 'El campo es obligatorio'
                        },
                        number: {
                            message: 'Debe ingresar un número'
                        }
                    }
                },
                t: {
                    validators:{
                        notEmpty: {
                            message: 'El campo es obligatorio'
                        },
                        integer: {
                            message: 'Debe ingresar un número'
                        }
                    }
                }
            }
    }).on('status.field.bv', function(e, data) {
        data.bv.disableSubmitButtons(false);
    }).on("success.form.bv", function(evt){
        evt.preventDefault(evt);
        var $form = $(evt.target);
        var S = null;
        var I = null;
        var R = null;
        $.ajax({
            url: $form .prop("action"),
            type: "POST",
            data: $form .serialize(),
            dataType: "json",
            async: false,
            headers: {
 				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
            success: function(data){
                console.log("siri: ", data);
                S = data.S;
                I = data.I;
                R = data.R;
                $("#grafico").show();
                $("#grafico").highcharts({
                    chart: {
                    type: 'line'
                    },
                    title: {
                        text: 'Modelo SIR'
                    },
                    /* subtitle: {
                        text: 'Source: WorldClimate.com'
                    }, */
                    xAxis: {
                        visible: true
                    },
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: false
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [{
                        name: 'Susceptibles',
                        data: S
                    }, {
                        name: 'Infectados',
                        data: I
                    }, {
                        name: 'Recuperados',
                        data: R
                    }]
                });
            },
            error: function(error){
                console.log("malo siri:", error);
            }
        });
    });
    
});

</script>

@stop

@section("section")
	<fieldset>
        <legend>Gráfico SIR</legend>
        <form id="formSir" role="form" method="POST" action="{{asset('getSir')}}">
            <div class="col-md-12">
                <div class="form-group col-sm-4">
                    <label>Población total</label>
                    <input name="n" type="text" class="form-control">
                </div>
                <div class="form-group col-sm-4">
                    <label>Tasa de infección</label>
                    <input name="beta" type="text" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group col-sm-4">
                    <label>Tasa de eliminación</label>
                    <input name="gama" type="text" class="form-control">
                </div>
                <div class="form-group col-sm-4">
                    <label>Cantidad de días</label>
                    <input name="t" type="text" class="form-control">
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group col-sm-4">
                    <button type="submit" class="btn btn-primary">Generar</button>
                </div>
            </div>
        </form>
        
        <div class="col-md-12">
            <div class="col-md-6 col-sm-6" id="grafico" style="margin-top:50;"></div>
        </div>
    </fieldset>

@stop

<script>


    function generarTablaValoracionHerida2(){

        tablavaloracionherida2 = $("#tablavaloracionheridas2").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/buscarValoracionHeridas/{{ $caso }}/"+$("#fechaBusqueda").val()+"" ,
                type: 'GET'
                },

            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            }
            //"initComplete": funcionPrueba
        });

    }

    function generarTablaCuracionesSimples2(){

        tablacuracionessimple2 = $("#tablacuracionessimples2").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/buscarCuracionesSimples/{{ $caso }}/"+$("#fechaBusquedaCuracion").val()+"",
                type: 'GET'
                },

            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            }
        });

    }

    function getFechaBusqueda(fechaBusqueda){
        if(fechaBusqueda == ""){
            mensajeFechaVacia("de busqueda de herida");
        }else{
            if(typeof tablavaloracionherida2 !== 'undefined') {
                tablavaloracionherida2.api().ajax.url( "{{URL::to('/gestionEnfermeria')}}/buscarValoracionHeridas/{{ $caso }}/"+fechaBusqueda+"" ).load();
            }else{
                generarTablaValoracionHerida2();
            }
        }
    }

    function getFechaCuracion(fechaCuracion){
        if(fechaCuracion == ""){
            mensajeFechaVacia("de busqueda de curaciones simple");
        }else{
            if(typeof tablacuracionessimple2 !== 'undefined') {
                tablacuracionessimple2.api().ajax.url( "{{URL::to('/gestionEnfermeria')}}/buscarCuracionesSimples/{{ $caso }}/"+fechaCuracion+"" ).load();
            }else{
                generarTablaCuracionesSimples2();
            }
        }
    }

    function cargarVistaBusqueda(){
        getFechaBusqueda($("#fechaBusqueda").val());
        getFechaCuracion($("#fechaBusquedaCuracion").val());
    }

    function mensajeFechaVacia(texto){
		swalWarning.fire({
			title: 'Información',
			html:"<h4>Debe seleccionar la fecha "+texto+" </h4>"
		});
	}

    $( document ).ready(function() {
        $("#hojaDeCuracion").click(function(){
            var tabsHojaCuraciones = $("#tabsHojaCuraciones").tabs().find(".active");
            tabHC = tabsHojaCuraciones[0].id;

            if(tabHC == "2ch"){
                console.log("tabHC busqueda: ", tabHC);
                cargarVistaBusqueda();
            }
            
        });

        $("#2hc").click(function(){
            cargarVistaBusqueda();
        });

        $('#btnBuscarHCuracion').click(function(){
            var fechaBusqueda = $("#fechaBusqueda").val();
            if(fechaBusqueda == ""){
                mensajeFechaVacia("de busqueda de herida");
            }else{
                getFechaBusqueda(fechaBusqueda);
            }
        });

        $('#btnBuscarCuracionSimple').click(function(){
            var fechaCuracion = $("#fechaBusquedaCuracion").val();
            if(fechaCuracion == ""){
                mensajeFechaVacia("de busqueda de curaciones simple");
            }else{
                getFechaCuracion(fechaCuracion);
            }
        });

        $(".fecha-sel").datetimepicker({
    		format: "DD-MM-YYYY",
			locale: 'es'
    	});

        $(".fecha-sel").on("dp.change keyup", function() {
			if($(this).val() == ""){
                $(this).focus($(this).css({
                    'border': '1px solid #a94442'
                }));
                $(this).next().html('Debe ingresar una fecha').css({
				'color': '#a94442'
				});
            }else{
                $(this).focus($(this).css({
                    'border': '1px solid #ccc'
                }));
                $(this).next().html('');
            }
        });

    })

</script>

<div class="formulario">

    {{ Form::hidden('caso', $caso) }}
    <br>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Busqueda de Herida</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-2">
                <div class="col-md-10">
                    <div class="form-group">
                        {{Form::label('lbl_fecha', "Fecha", array('class' => ''))}}
                        {{Form::text('fechaBusqueda', \Carbon\Carbon::now()->format('d-m-Y'), array('id' => 'fechaBusqueda', 'class' => 'form-control fecha-sel','required'))}}
                        <span class="errorFechaBusqueda"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6" style="margin-top: 22px; margin-left: -80px;">
                <div class="col-md-2">
                    {{ Form::submit("Buscar", array("id" => "btnBuscarHCuracion", "class" => "btn btn-primary")) }}
                    <br>
                </div>
            </div>

            <br><br>
            <div class="col-md-12">
                <br><br>
                <legend>Registros de valoración de herida</legend>
                <table id="tablavaloracionheridas2" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 12%">HERIDAS VALORADAS</th>
                            <th style="width: 30%">ASPECTO / MAYOR EXTENSIÓN / PROFUNDIDAD / EXUDADO CANTIDAD / EXUDADO CALIDAD</th>
                            <th style="width: 30%">TEJIDO ESFACELADO O NECRÓTICO / TEJIDO GRANULATORIO / EDEMA / DOLOR /PIEL CIRCUNDANTE / TOTAL</th>
                            <th style="width: 10%">USUARIO</th>

                        </tr>
                    </thead>
                    <tbody id="cargaRes"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<br>
<div class="formulario">

    {{ Form::hidden('caso', $caso) }}
    <br>
    <div class="panel panel-default">
        <div class="panel-heading panel-info">
            <h4>Busqueda de Curaciones Simple</h4>
        </div>
        <div class="panel-body">
            <div class="col-md-2">
                <div class="col-md-10">
                    <div class="form-group">
                        {{Form::label('lbl_fecha', "Fecha", array('class' => ''))}}
                        {{Form::text('fechaBusquedaCuracion', \Carbon\Carbon::now()->format('d-m-Y'), array('id' => 'fechaBusquedaCuracion', 'class' => 'form-control fecha-sel','required'))}}
                        <span class="errorFechaCuracion"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6" style="margin-top: 22px; margin-left: -80px;">
                <div class="col-md-2">
                    {{ Form::submit("Buscar", array("id" => "btnBuscarCuracionSimple", "class" => "btn btn-primary")) }}
                    <br>
                </div>
            </div>

            <br><br>
            <div class="col-md-12">
                <br><br>
                <legend>Registros de valoración de herida</legend>
                <table id="tablacuracionessimples2" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 20%">Fecha Creasion</th>
                            <th style="width: 30%">OBSERVACIONES</th>
                            <th style="width: 30%">FECHA PROXIMA CURACION</th>
                            <th style="width: 20%">USUARIO</th>

                        </tr>
                    </thead>
                    <tbody id="cargaResCuracion"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function generarTablaHistoricaGeneral(){
        tHistoricoGeneral = $("#tablaHistoricoGeneral").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "order": [[0, 'desc']],
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerHistoricoGeneral/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    $(document).ready(function(){
        // $( "#hR" ).click(function() {
        //     if (typeof tHistoricoGeneral !== 'undefined') {
        //         tHistoricoGeneral.api().ajax.reload();
        //     }else{
        //         generarTablaHistoricaGeneral();
        //     }
        // });
        $( "#IeHg" ).click(function() {
            if (typeof tHistoricoGeneral !== 'undefined') {
                tHistoricoGeneral.api().ajax.reload();
            }else{
                generarTablaHistoricaGeneral();
            }
        });
    });
</script>

<div class="container">
    <legend>Histórico Examen Físico General</legend>
    {{-- <br> --}}
    {{ HTML::link("gestionEnfermeria/$caso/obtenerHistorialIngresoEnfermeria", 'Histórico', ['class' => 'btn btn-danger']) }}
    <br><br> 
    <table id="tablaHistoricoGeneral" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>FECHA CREACIÓN</th>
                <th>FECHA MODIFICACIÓN</th>
                {{-- <th>TIPO MODIFICACIÓN</th> --}}
                <th>RESULTADO IMC</th>
                <th>SIGNOS VITALES</th>
                <th>ESTADO NUTRICIONAL</th>
                <th>ESTADO CONCIENCIA</th>
                <th>FUNCIÓN RESPIRATORIA</th>
                <th>HIGIENE</th>
                <th>ESCALAS</th>
                <th>USUARIO</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
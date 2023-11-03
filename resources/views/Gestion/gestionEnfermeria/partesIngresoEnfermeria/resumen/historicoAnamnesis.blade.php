<script>
    function generarTablaHistoricoAnamnesis(){
        tHistoricoAnamnesis = $("#tablaHistoricoAnamnesis").dataTable({
            "iDisplayLength": 5,
            "ordering": true,
            "order": [[0, 'desc']],
            "searching": true,
            "ajax": {
                url: "{{URL::to('/gestionEnfermeria')}}/obtenerHistoricoAnamnesis/{{ $caso }}" ,
                type: 'GET'
            },
            "oLanguage": {
                "sUrl": "{{ URL::to('/') }}/js/spanish.txt"
            },
        });
    }

    $(document).ready(function(){
        $( "#hR" ).click(function() {
            if (typeof tHistoricoAnamnesis !== 'undefined') {
                tHistoricoAnamnesis.api().ajax.reload();
            }else{
                generarTablaHistoricoAnamnesis();
            }
        });
        $( "#IeHa" ).click(function() {
            if (typeof tHistoricoAnamnesis !== 'undefined') {
                tHistoricoAnamnesis.api().ajax.reload();
            }else{
                generarTablaHistoricoAnamnesis();
            }
        });
    });
</script>
<div class="container">
    <legend>Histórico Anamnesis</legend>
    {{-- <br> --}}
    {{ HTML::link("gestionEnfermeria/$caso/obtenerHistorialIngresoEnfermeria", 'Histórico', ['class' => 'btn btn-danger']) }}
    <br><br> 
    <table id="tablaHistoricoAnamnesis" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>FECHA CREACIÓN</th>
                <th>FECHA MODIFICACIÓN</th>
                {{-- <th>TIPO MODIFICACIÓN</th> --}}
                <th>ANTECEDENTES MORBIDOS</th>
                <th>ANTECEDENTES QUIRURGICOS</th>
                <th>ANTECEDENTES ALERGICOS</th>
                <th>HÁBITOS</th>
                <th>DIAGNÓSTICOS MÉDICOS</th>
                <th>ANAMNESIS ACTUAL</th>
                <th>USUARIO</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

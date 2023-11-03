<script>
    function mostrarCategorizadorPorServicio(){
       
    }
</script>

<fieldset>
    <legend>Categorizados por servicio</legend>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Area Funcional</th>
                    <th>Servicio</th>
                    <th>Sin categorización</th>
                    {{-- <th>No habilitado para categorizar</th> --}}
                    <th>A1</th>
                    <th>A2</th>
                    <th>A3</th>
                    <th>B1</th>
                    <th>B2</th>
                    <th>B3</th>
                    <th>C1</th>
                    <th>C2</th>
                    <th>C3</th>
                    <th>D1</th>
                    <th>D2</th>
                    <th>D3</th>
                    <th>% Pacientes categorizados</th> 
                    <th>TOTAL</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($categorizacion as $categoriza)
                <tr>
                    <th>{{$categoriza[16]}}</th>
                    <th>{{$categoriza[14]}}</th>
                    <th>{{$categoriza[13]}}</th>
                    {{-- <th>{{$categoriza[15]}}</th> --}}
                    <th>{{$categoriza[0]}}</th>
                    <th>{{$categoriza[1]}}</th>
                    <th>{{$categoriza[2]}}</th>
                    <th>{{$categoriza[3]}}</th>
                    <th>{{$categoriza[4]}}</th>
                    <th>{{$categoriza[5]}}</th>
                    <th>{{$categoriza[6]}}</th>
                    <th>{{$categoriza[7]}}</th>
                    <th>{{$categoriza[8]}}</th>
                    <th>{{$categoriza[9]}}</th>
                    <th>{{$categoriza[10]}}</th>
                    <th>{{$categoriza[11]}}</th>
                    @if($categoriza[12] == $categoriza[13])
                    <th>0 %</th>
                    @else
                    <th>{{round(($categoriza[12]-$categoriza[13])*100/$categoriza[12],2)}} %</th>
                    @endif
                    <th>{{$categoriza[12]}}</th>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</fieldset>
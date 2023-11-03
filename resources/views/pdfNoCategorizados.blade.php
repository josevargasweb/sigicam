<html>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>



        <div class="row">
            <h3 style="text-align: center;">Informe Diario</h3>
            <p style="font-size: 14px; text-align: center;">Pacientes sin categorizar.</p>

            <div class="row" style="text-align: left">
                Fecha: <label style="font-weight: bold;">{{ Carbon\Carbon::now()->format("d-m-Y") }}</label>
            </div>
            <br>
        
            <table>
            
                <thead>
                    <tr>
                        <th>Rut</th>
                        <th>Nombre</th>
                        <th>Fecha nacimiento</th>
                        <th>Area funcional</th>
                        <th>Unidad funcional</th>
                        <th>sala</th>
                        <th>cama</th>
                        {{-- <th>Tiempo sin categorizar</th> --}}
                        
                    </tr>
                </thead>
                <tbody>
                @foreach($resultado as $r)
                    <tr>
                        <td>{{$r['rut']}}</td>
                        <td>{{$r['nombre']}}</td>
                        <td>{{$r['fecha_nacimiento']}}</td>
                        <td>{{$r["area_funcional"]}}</td>
                        <td>{{$r["unidad_funcional"]}}</td>
                        <td>{{$r["sala"]}}</td>
                        <td>{{$r["cama"]}}</td>
                        {{-- <td>{{$r["estadia"]}} días</td>                         --}}
                    </tr>
                    
                @endforeach
                
                </tbody>
            </table>
    </div>
</html>


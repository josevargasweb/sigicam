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
                <p style="font-size: 14px; text-align: center;">Pacientes de larga estadia (más de 6 días).</p>

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
                            <th>Días de Hospitalización</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($resultado2 as $r2)
                        <tr>
                            <td>{{$r2['rut']}}</td>
                            <td>{{$r2['nombre']}}</td>
                            <td>{{$r2['fecha_nacimiento']}}</td>
                            <td>{{$r2["area_funcional"]}}</td>
                            <td>{{$r2["unidad_funcional"]}}</td>
                            <td>{{$r2["sala"]}}</td>
                            <td>{{$r2["cama"]}}</td>
                            <td>{{$r2["estadia"]}} días</td>                        
                        </tr>
                        
                    @endforeach
                    
                    </tbody>
                </table>
        </div>
</html>


<tr>
    <td>{{$informacion["nombre"]}}</td>
    @for($i = 1; $i <= $dias_del_mes; $i++)
    {{--si dia existe en dias de inicio o si dia existe en dias de fin--}}
        @if(in_array((int) $i, json_decode($informacion["inicio"])) || in_array((int) $i, json_decode($informacion["fin"])))
            {{-- si dia existe en dias de inicio --}}
            @if (in_array((int) $i, json_decode($informacion["inicio"])))
                <td style="background-color: green;">{{$i}}</td>                                    
            @endif

            {{-- si dia existe en dias de fin --}}
            @if (in_array((int) $i, json_decode($informacion["fin"])))
                {{-- si dia que existe en fin y existe en inicio --}}
                @if(in_array($i, json_decode($informacion["inicio"])))
                    {{-- no hacer nada --}}
                @else
                    {{-- si solo existe en fin --}}
                <td style="background-color: red;">{{$i}}</td>
                @endif                                    
            @endif 
        {{-- si dia no existe en dias inicio o fin --}}
        @else
            {{-- esto rellenara los dias sin nada para que se ubiquen bien los dias con info --}}
            <td style="background-color: white;">{{$i}}</td>  
        @endif
    @endfor
</tr>
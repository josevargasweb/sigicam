@extends("Templates/template")



@section("titulo")
RESTRINGIDO
@stop

@section('section')

<div class="col-md-12" style="text-align:center;padding-top: 30%;">
    <h3> <img src="{{asset('img/bloquear.png')}}" height="20px"> {{$error}}</h3>  <br>
    
</div>
  
@endsection
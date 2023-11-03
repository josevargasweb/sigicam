@extends("Templates/template")

@section("titulo")
Editar Ambulancias
@stop

@section("script")

@stop

@section("miga")
@stop

@section("section")
<legend>Editar Ambulancia {{ $ambulancia->patente }}</legend>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">

                @if(count($errors)>0)
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {!! Form::open(['route' => ['ambulancias.update', $ambulancia->id], 'method' => 'PUT', 'class' => 'form-horizontal']) !!}
                 
                <fieldset>
                 
	                <!-- patente -->
	                <div class="form-group">
	                    {!! Form::label('patente', 'Patente:', ['class' => 'col-lg-3 control-label']) !!}
	                    <div class="col-lg-8 col-md-offset-1">
	                        {!! Form::text('patente',$ambulancia->patente,['class' => 'form-control']) !!}
	                    </div>
	                </div>

	                <!-- tipo -->
	                <div class="form-group">
	                    {!! Form::label('tipo_id', 'Tipo:', ['class' => 'col-lg-3 control-label']) !!}
	                    <div class="col-lg-8 col-md-offset-1">
	                        {!! Form::select('tipo_id',$tipos ,$ambulancia->tipo_id,['class' => 'form-control']) !!}
	                    </div>
	                </div>


	                <!-- estado -->
	                <div class="form-group">
	                    {!! Form::label('estadoa_id', 'Estado:', ['class' => 'col-lg-3 control-label']) !!}
	                    <div class="col-lg-8 col-md-offset-1">
	                        {!! Form::select('estadoa_id', $estados,$ambulancia->estadoa_id,['class' => 'form-control']) !!}
	                    </div>
	                </div>
	                 
	                <!-- establecimiento -->
	                <div class="form-group">
	                    {!! Form::label('establecimiento_id', 'Establecimiento:', ['class' => 'col-lg-3 control-label'] )  !!}
	                    <div class="col-lg-8 col-md-offset-1">
	                        {!! Form::select('establecimiento_id', $establecimientos, $ambulancia->establecimiento_id, ['class' => 'form-control']) !!}
	                    </div>
	                </div>

	                <!-- establecimiento -->
	                <div class="form-group">
	                    {!! Form::label('ubicacion', 'Ubicación:', ['class' => 'col-lg-3 control-label'] )  !!}
	                    <div class="col-lg-8 col-md-offset-1">
	                        {!! Form::select('ubicacion', $establecimientos, $ambulancia->establecimiento_id, ['class' => 'form-control']) !!}
	                    </div>
	                </div>

	                <div >
	                	<a class="btn btn-lg btn-danger" href="{{ route('ambulancias.index') }}">Volver</a>
	                

	                	<!-- Submit Button -->
	                    {!! Form::submit('Actualizar', ['class' => 'btn btn-lg btn-primary pull-right'] ) !!}
	                   
	                </div>
	                


                 
                </fieldset>
                 
                {!! Form::close()  !!}
                 
		</div>
	</div>
</div>

@stop
<div class="container">
    <div class="col">
        <div class="col-md-3">
        <b>Sala</b>   
        <img class="img-responsive" src="{{asset('img/paciente_postrado.png')}}" >
        </div>
        <div class="col-md-3">
        <label for="fecha" class="col-md-12" title="Fecha">Fecha: </label>
        {{Form::text('fecha', null, array('id' => 'fecha', 'class' => 'form-control','required'))}}
        </div>
        <div class="col-md-3">
        <label for="sitio" class="col-md-12" title="Sitio">Sitio: </label>
        {{Form::text('sitio', null, array('id' => 'sitio', 'class' => 'form-control','required'))}}
        </div>
    </div>
</div>
<input id="btnpostrados" type="submit" name="" class="btn btn-primary" value="Ingresar Información">
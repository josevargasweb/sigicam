@extends("Templates/template")

@section("titulo")
SIGICAM
@stop

@section("script")
  @include('Gestion.gestionEnfermeria.partials.scriptMacdems')
  <script>
     $(".fechaPC").inputmask({ alias: "datetime", inputFormat:"dd-mm-yyyy"});
  </script>
  <meta name="csrf-token" content="{{{ Session::token() }}}">
@stop

@section("section")
<style>

.formulario > .panel-default > .panel-heading {
	background-color: #bce8f1 !important;
}

.tt-input{
	width:100%;
}
.tt-query {
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	 -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
  color: #999
}

.tt-menu {    /* used to be tt-dropdown-menu in older versions */
  /*width: 430px;*/
  margin-top: 4px;
 /* padding: 4px 0;*/
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
	 -moz-border-radius: 4px;
		  border-radius: 4px;
  -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	 -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
		  box-shadow: 0 5px 10px rgba(0,0,0,.2);
	overflow-y: scroll;
	max-height: 350px;
}

.tt-suggestion {
 /* padding: 3px 20px;*/
  line-height: 24px;
}

.tt-suggestion.tt-cursor,.tt-suggestion:hover {
  color: #fff;
  background-color: #1E9966;

}

.tt-suggestion p {
  margin: 0;
}
.twitter-typeahead{
	width:100%;
}

#modalCamas {
	overflow-y:scroll
}

/*
.dropdown-menu > li > a {
	color:#399865 !important;
}

.btn-default {
	color:#399865 !important;
}
*/



.tt-input{
	width:100%;
}
.tt-query {
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	 -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
  color: #999
}

.tt-menu {    /* used to be tt-dropdown-menu in older versions */
  /*width: 430px;*/
  margin-top: 4px;
 /* padding: 4px 0;*/
  background-color: #fff;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
	 -moz-border-radius: 4px;
		  border-radius: 4px;
  -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	 -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
		  box-shadow: 0 5px 10px rgba(0,0,0,.2);
	overflow-y: scroll;
	max-height: 350px;
}

.tt-suggestion {
 /* padding: 3px 20px;*/
  line-height: 24px;
}

.tt-suggestion.tt-cursor,.tt-suggestion:hover {
  color: #fff;
  background-color: #1E9966;

}

.tt-suggestion p {
  margin: 0;
}
.twitter-typeahead{
	width:100%;
}

#modalCamas {
	overflow-y:scroll
}

.primerNav{
  background-color:#1E9966;
}
  .primerNav > li > a{
    color: #fff !important;
  }

    .primerNav > li.active > a, .primerNav > li.active > a:hover,.primerNav > li.active > a:focus,.primerNav > li > a:hover, .primerNav > li > a:focus{
      background-color:#c35c6b;
    }

.segundoNav{
  background-color:#c35c6b
}

  .segundoNav > li > a{
    color: #fff !important;
  }

    .segundoNav > li.active > a, .segundoNav > li.active > a:hover,.segundoNav > li.active > a:focus, .segundoNav > li > a:hover, .segundoNav > li > a:focus{
      background-color:#bce8f1;
      color: #1e9966 !important;
    }
/*
.dropdown-menu > li > a {
	color:#399865 !important;
}

.btn-default {
	color:#399865 !important;
}
*/

</style>

<a href="javascript:history.back()" class="btn btn-primary">Volver</a>
<br> <br>
<fieldset>
  <legend>Formularios</legend>
  <div class="row">
    <div class="col-md-5">
      <b>NOMBRE PACIENTE:</b>  {{ $infoPaciente->nombre }} {{ $infoPaciente->apellido_paterno }} {{ $infoPaciente->apellido_materno }}
    </div>
    <div class="col-md-2">
      <b>RUT:</b>  {{ $infoPaciente->rut }}-{{ ($infoPaciente->dv == 10)?'K':$infoPaciente->dv }} 
    </div>
    <div class="col-md-5">
      <b>FECHA DE NACIMIENTO:</b>  {{ $infoPaciente->fecha_nacimiento }} ({{ $edad = Carbon\Carbon::now()->diffInYears($infoPaciente->fecha_nacimiento) }} AÑOS)
    </div>

  </div>

  <br>

  <div class="row">
    <div id="exTab1" class="" >
      <ul  class="nav nav-pills primerNav">
        <li id="riesgoCaida" class="active">
            <a href="#1f" data-toggle="tab">Riesgo Caída</a>
        </li>
      </ul>
      <div class="tab-content clearfix">
          <div class="tab-pane active" id="1f">
            <br>
            {{ HTML::link("gestionEnfermeria/$caso/historialEscalaMacdems", 'Ver Historial', ['class' => 'btn btn-default', "id" => "btnHistorial"]) }}
            {{-- @include('Gestion.gestionEnfermeria.macdems2') --}}
            {{ Form::open(array('method' => 'post', 'class' => 'form-horizontal',  'id' => 'escalaMacdemsform')) }}
            {{ Form::hidden('idCaso', $caso, array('id' => 'idCasoMacdems')) }}
            <input type="hidden" value="En Curso" name="tipoFormMacdems" id="tipoFormMacdems">
                @include('Gestion.gestionEnfermeria.partials.FormMacdems') 
            {{ Form::close()}}   
          </div>
      </div>
    </div>
  </div>


</fieldset>

@stop

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;
use TipoUsuario;
use DB;
use Log;
use Carbon\Carbon;;
use App\Models\HojaCuraciones;
use App\Models\HojaCuracionesSimple;
use View;
use Form;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HojaCuracionController extends Controller
{

    public function buscarValoracionHeridas($caso, $fechaBusqueda){

        $caso = strip_tags($caso);
        //Esto le da formato Año-mes-dia
        Carbon::createFromFormat('d-m-Y',$fechaBusqueda)->format('Y-m-d');

        $fechaini = Carbon::parse(strip_tags($fechaBusqueda))->startOfDay();
        $fechafin = Carbon::parse(strip_tags($fechaBusqueda))->endOfDay();

        $resultado = [];

        $heridas = DB::select(DB::raw("select i.id, i.caso, i.visible, i.usuario, i.fecha_valoracion, i.fecha_creacion, i.aspecto, i.exudado_cantidad, i.exudado_calidad, i.edema,
            i.piel_circundante, i.exudado_calidad, i.mayor_extension, i.profundidad, i.tejido_esfacelado, i.dolor, i.tejido_granulatorio,
            u.nombres,
            u.apellido_paterno,
            u.apellido_materno
            from formulario_hoja_curaciones_valoracion_herida as i
            inner join usuarios as u on u.id = i.usuario
            where i.caso = $caso and i.visible and i.fecha_creacion >= '$fechaini' and i.fecha_creacion <= '$fechafin'
            "));

        foreach ($heridas as $h) {
            $total = HojaCuraciones::calcular($h);

            $row = "<div>";
            $end = "</div>";
            $valorAspecto = $h->aspecto;

            $valorExtension = $h->mayor_extension;

            $valorProfundidad = $h->profundidad;

            $valorCalidad = $h->exudado_calidad;

            $valorCantidad = $h->exudado_cantidad;

            $valorEsfacelado = $h->tejido_esfacelado;

            $valorGranulatorio = $h->tejido_granulatorio;

            $valorEdema = $h->edema;

            $valorDolor = $h->dolor;

            $valorPiel = $h->piel_circundante;

            $htmlhorav = "";

            $resultado [] = [
                $htmlhorav."<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($h->fecha_creacion)->format("d-m-Y H:i")."</b> </p> </div>",
                "<div class='col-md-12'>
                    <div class='col-md-7'><label>Aspecto</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorAspecto."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Mayor Extension</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorExtension." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Profundidad</label> </div>
                    <div class='col-md-5' style='text-align:left'> ".$valorProfundidad."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Exudado Calidad</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorCalidad."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Exudado Cantidad</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorCantidad."</div>
                </div>",
                "<div class='col-md-12'>
                    <div class='col-md-7'><label>Tejido Esfacelado</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorEsfacelado."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Tejido Granulatorio</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorGranulatorio." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Edema</label> </div>
                    <div class='col-md-5' style='text-align:left'> ".$valorEdema."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Dolor</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorDolor."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label>Piel Circundante</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$valorPiel." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-7'><label id='totalGrado'>Total</label> </div>
                    <div class='col-md-5' style='text-align:left'>".$total."</div>
                </div>",
                $h->nombres." ".$h->apellido_paterno." ".$h->apellido_materno
            ];
        }

        return response()->json(["aaData" => $resultado]);
   }

   public function buscarCuracionesSimples($caso, $fechaBusqueda){

       $caso = strip_tags($caso);
       //Esto le da formato Año-mes-dia
       Carbon::createFromFormat('d-m-Y',$fechaBusqueda)->format('Y-m-d');

       $fechaini = Carbon::parse(strip_tags($fechaBusqueda))->startOfDay();
       $fechafin = Carbon::parse(strip_tags($fechaBusqueda))->endOfDay();

       $resultado = [];

       $curaciones = DB::select(DB::raw("select c.id, c.caso, c.visible, c.usuario, c.fecha_creacion, c.observaciones, c.proxima_curacion,
           u.nombres,
           u.apellido_paterno,
           u.apellido_materno
           from formulario_hoja_curaciones_curaciones as c
           inner join usuarios as u on u.id = c.usuario
           where c.caso = $caso and c.visible and c.fecha_creacion >= '$fechaini' and c.fecha_creacion <= '$fechafin'
           "));

       foreach ($curaciones as $c) {

           $row = "<div>";
           $end = "</div>";

           $proximaCuracion = Carbon::parse($c->proxima_curacion)->format("d-m-Y");
           $observacion = $c->observaciones;

           $resultado [] = [
               "<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($c->fecha_creacion)->format("d-m-Y H:i")."</b> </p> </div>",
               "<div class='col-md-12'>
                   <div class='col-md-5' style='text-align:left; margin-top:5%;'>".$observacion."</div>
               </div>",
               "<div class='col-md-12'>
                   <div class='col-md-5' style='text-align:left; margin-top:5%;'>".$proximaCuracion." </div>
               </div>",
               $c->nombres." ".$c->apellido_paterno." ".$c->apellido_materno
           ];
       }
       return response()->json(["aaData" => $resultado]);
   }

    public function obtenerValoracionHeridas($caso){

        /* esta mal falta revisar. Poner ejemplo de que hoy son las 12 de las noche o 00:00 del dia... */
        /* $hoy = Carbon::createFromTime(8,0,0);
        $mañana = Carbon::createFromTime(7,59,59)->addDays(1); */

        $inicio = Carbon::now()->startOfDay();
        $fin = Carbon::now()->endOfDay();


        $resultado = [];

        $heridas = DB::select(DB::raw("select i.id, i.caso, i.visible, i.usuario, i.fecha_valoracion, i.fecha_creacion, i.aspecto, i.exudado_cantidad, i.exudado_calidad, i.edema,
        i.piel_circundante, i.exudado_calidad, i.mayor_extension, i.profundidad, i.proxima_curacion, i.tejido_esfacelado, i.dolor, i.tejido_granulatorio, i.observaciones,
        u.nombres,
        u.apellido_paterno,
        u.apellido_materno
        from formulario_hoja_curaciones_valoracion_herida as i
        inner join usuarios as u on u.id = i.usuario
        where i.caso = $caso and i.visible and i.fecha_creacion > '$inicio' and i.fecha_creacion < '$fin'
        "));

        foreach ($heridas as $key => $h) {
            $opciones = "<div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-warning' onclick='modificarCuracion(".$h->id.",".$key.")'>Modificar</button>
            </div>
            <br><br>
            <div class='btn-group' role='group' aria-label='...'>
            <button type='button' class='btn-xs btn-danger' onclick='eliminarCuracion(".$h->id.")'>Eliminar</button>
            </div>";

            $fechaValoracion = "";
            if($h->fecha_valoracion){
                $fechaValoracion = Carbon::parse($h->fecha_valoracion)->format("H:i");
            }
            $htmlhorav = "<div class='form-group'>
            <div class='col-md-10'>
            <input class='dPValoracion form-control' id='thorariov-".$key."' type='text' value='".$fechaValoracion."'>
            </div>";

            $row = "<div>";
            $end = "</div>";
            $valorAspecto = 0;
            if($h->aspecto == 'Eritematoso'){
                $valorAspecto = 1;
            }elseif($h->aspecto == 'Enrojecido'){
                $valorAspecto = 2;
            }elseif($h->aspecto == 'Amarillo pálido'){
                $valorAspecto = 3;
            }else{
                $valorAspecto = 4;
            }
            $aspecto = $row.Form::select("taspecto", array("1" => "Eritematoso", "2" => "Enrojecido","3" => "Amarillo pálido","4" => "Necrótico"), $valorAspecto, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "taspecto-$key")).$end;

            $valorExtension = 0;
            if($h->mayor_extension == '0 - 1 cm'){
                $valorExtension = 1;
            }elseif($h->mayor_extension == '> 1 - 3 cm'){
                $valorExtension = 2;
            }elseif($h->mayor_extension == '> 3 - 6 cm'){
                $valorExtension = 3;
            }else{
                $valorExtension = 4;
            }
            $mayorExtension = $row.Form::select("tmayorextension", array("1" => "0 - 1 cm", "2" => "> 1 - 3 cm","3" => "> 3 - 6 cm","4" => "> 6 cm"), $valorExtension, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tmayorextension-$key")).$end;

            $valorProfundidad = 0;
            if($h->profundidad == '0'){
                $valorProfundidad = 1;
            }elseif($h->profundidad == '< 1 cm'){
                $valorProfundidad = 2;
            }elseif($h->profundidad == '1 - 3 cm'){
                $valorProfundidad = 3;
            }else{
                $valorProfundidad = 4;
            }
            $profundidad = $row.Form::select("tprofundidad", array("1" => "0 cm", "2" => "< 1 cm","3" => "1 - 3 cm","4" => "> 3 cm"), $valorProfundidad, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tprofundidad-$key")).$end;

            $valorCalidad = 0;
            if($h->exudado_calidad == 'Sin exudado'){
                $valorCalidad = 1;
            }elseif($h->exudado_calidad == 'Seroso'){
                $valorCalidad = 2;
            }elseif($h->exudado_calidad == 'Turbio'){
                $valorCalidad = 3;
            }else{
                $valorCalidad = 4;
            }
            $calidad = $row.Form::select("tcalidad", array("1" => "Sin exudado", "2" => "Seroso","3" => "Turbio","4" => "Purulento"), $valorCalidad, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tcalidad-$key")).$end;

            $valorCantidad = 0;
            if($h->exudado_cantidad == 'Ausente'){
                $valorCantidad = 1;
            }elseif($h->exudado_cantidad == 'Escaso'){
                $valorCantidad = 2;
            }elseif($h->exudado_cantidad == 'Moderado'){
                $valorCantidad = 3;
            }else{
                $valorCantidad = 4;
            }
            $cantidad = $row.Form::select("tcantidad", array("1" => "Ausente", "2" => "Escaso","3" => "Moderado","4" => "Abundante"), $valorCantidad, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tcantidad-$key")).$end;

            $valorEsfacelado = 0;
            if($h->tejido_esfacelado == 'Ausente'){
                $valorEsfacelado = 1;
            }elseif($h->tejido_esfacelado == '< 25%'){
                $valorEsfacelado = 2;
            }elseif($h->tejido_esfacelado == '25 - 50%'){
                $valorEsfacelado = 3;
            }else{
                $valorEsfacelado = 4;
            }
            $esfacelado = $row.Form::select("tesfacelado", array("1" => "Ausente", "2" => "< 25%","3" => "25 - 50%","4" => "> 50%"), $valorEsfacelado, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tesfacelado-$key")).$end;

            $valorGranulatorio = 0;
            if($h->tejido_granulatorio  == '100 - 75%'){
                $valorGranulatorio = 1;
            }elseif($h->tejido_granulatorio  == '< 75 - 50%'){
                $valorGranulatorio = 2;
            }elseif($h->tejido_granulatorio  == '< 50 - 25%'){
                $valorGranulatorio = 3;
            }else{
                $valorGranulatorio = 4;
            }
            $granulatorio = $row.Form::select("tgranulatorio", array("1" => "100 - 75%", "2" => "< 75 - 50%","3" => "< 50 - 25%","4" => "< 25%"), $valorGranulatorio, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tgranulatorio-$key")).$end;

            $valorEdema = 0;
            if($h->edema  == 'Ausente'){
                $valorEdema = 1;
            }elseif($h->edema  == '+'){
                $valorEdema = 2;
            }elseif($h->edema  == '++'){
                $valorEdema = 3;
            }else{
                $valorEdema = 4;
            }
            $edema = $row.Form::select("tedema", array("1" => "Ausente", "2" => "+","3" => "++","4" => "+++"), $valorEdema, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tedema-$key")).$end;

            $valorDolor = 0;
            if($h->dolor == '0 - 1'){
                $valorDolor = 1;
            }elseif($h->dolor  == '2 - 3'){
                $valorDolor = 2;
            }elseif($h->dolor  == '4 - 6'){
                $valorDolor = 3;
            }else{
                $valorDolor = 4;
            }
            $dolor = $row.Form::select("tdolor", array("1" => "0 - 1", "2" => "2 - 3","3" => "4 - 6","4" => "7 - 10"), $valorDolor, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tdolor-$key")).$end;

            $valorPiel = 0;
            if($h->piel_circundante == 'Sana'){
                $valorPiel = 1;
            }elseif($h->piel_circundante  == 'Descamada'){
                $valorPiel = 2;
            }elseif($h->piel_circundante  == 'Eritematosa'){
                $valorPiel = 3;
            }else{
                $valorPiel = 4;
            }
            $piel = $row.Form::select("tpiel", array("1" => "Sana", "2" => "Descamada","3" => "Eritematosa","4" => "Macerada"), $valorPiel, array("class" => "form-control calculartValoracion", "data-id" => $key, "id" => "tpiel-$key")).$end;

            $valorTotal = $valorAspecto + $valorExtension + $valorProfundidad + $valorCalidad + $valorCantidad + $valorEsfacelado + $valorGranulatorio + $valorEdema + $valorDolor + $valorPiel;

            if($valorTotal <= 15 ){$valorTotal = $valorTotal." (Grado 1)";}
            if($valorTotal > 15 && $valorTotal <= 21 ){$valorTotal = $valorTotal." (Grado 2)";}
            if($valorTotal > 21 && $valorTotal <= 27 ){$valorTotal = $valorTotal." (Grado 3)";}
            if($valorTotal > 27 && $valorTotal <= 40 ){$valorTotal = $valorTotal." (Grado 4)";}

            $total = "<input class='form-control' id='totalValoracion".$key."' type='text' value='".$valorTotal."' disabled>";

            $observaciones = $row.Form::textArea("tobservaciones", $h->observaciones, array("id" => "tobservaciones-$key", "class" => "form-control", "rows" => 4, "style" => "resize:none")).$end;

            $proxima_curacion = "";
            if($h->proxima_curacion){
                $proxima_curacion = Carbon::parse($h->proxima_curacion)->format("d-m-Y");
            }
            $htmlproximacuracion = "<div class='form-group'>
            <div class='col-md-12'>
            <input class='form-control dPtPcuracion' id='tproximacuracion-".$key."' type='text' value='".$proxima_curacion."'>
            </div></div>";

            $resultado [] = [
                $htmlhorav."<br> <div class='col-md-12'> <p> <b>  Creado el: ".Carbon::parse($h->fecha_creacion)->format("d-m-Y H:i")."</b> </p> </div>",
                "<div class='col-md-12'>
                    <div class='col-md-4'><label>Aspecto</label> </div>
                    <div class='col-md-8'>".$aspecto."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Mayor Extension</label> </div>
                    <div class='col-md-8'>".$mayorExtension." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Profundidad</label> </div>
                    <div class='col-md-8'> ".$profundidad."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Exudado Calidad</label> </div>
                    <div class='col-md-8'>".$calidad."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Exudado Cantidad</label> </div>
                    <div class='col-md-8'>".$cantidad." </div>
                </div>",
                "<div class='col-md-12'>
                    <div class='col-md-4'><label>Tejido Esfacelado</label> </div>
                    <div class='col-md-8'>".$esfacelado."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Tejido Granulatorio</label> </div>
                    <div class='col-md-8'>".$granulatorio." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Edema</label> </div>
                    <div class='col-md-8'> ".$edema."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Dolor</label> </div>
                    <div class='col-md-8'>".$dolor."</div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Piel Circundante</label> </div>
                    <div class='col-md-8'>".$piel." </div>
                </div>
                <div class='col-md-12'>
                    <div class='col-md-4'><label>Total</label> </div>
                    <div class='col-md-8'>".$total." </div>
                </div>",
                $h->nombres." ".$h->apellido_paterno." ".$h->apellido_materno,
                $opciones
            ];
        }

        return response()->json(["aaData" => $resultado]);
    }

    public function obtenerCuracionSimple($caso){
      $inicio = Carbon::now()->startOfDay();
      $fin = Carbon::now()->endOfDay();

      $resultado = [];

      $curaciones = DB::select(DB::raw("select c.id, c.caso, c.visible, c.usuario, c.fecha_creacion, c.proxima_curacion, c.observaciones, c.tipo_curacion,
      u.nombres,
      u.apellido_paterno,
      u.apellido_materno
      from formulario_hoja_curaciones_curaciones as c
      inner join usuarios as u on u.id = c.usuario
      where c.caso = $caso and c.visible and c.fecha_creacion > '$inicio' and c.fecha_creacion < '$fin'
      "));

      foreach ($curaciones as $key => $c) {

        $tipo_curacion = "Curación {$c->tipo_curacion}";
        
        $opciones = "<div class='btn-group' role='group' aria-label='...'>
        <button type='button' class='btn-xs btn-warning' onclick='modificarCuracionSimple(".$c->id.",".$key.")'>Modificar</button>
        </div>
        <div class='btn-group' role='group' aria-label='...'>
        <button type='button' class='btn-xs btn-danger' onclick='eliminarCuracionSimple(".$c->id.")'>Eliminar</button>
        </div>";

        $resultado [] = [
            "<div class='col-md-12'> <p> <b>{$tipo_curacion}</b> <br> Creado el: ".Carbon::parse($c->fecha_creacion)->format("d-m-Y H:i")." </p> </div>",
            "<div class='col-md-12'>
                <input class='form-control' id='observaciones-".$key."' type='textArea' value='".$c->observaciones."'>
            </div>",
            "<div class='col-md-12'>
                <input class='curacionSimple form-control' autocomplete='off' id='proximaCuracion-".$key."' type='text' value='".Carbon::parse($c->proxima_curacion)->format("d-m-Y")."'>
            </div>",
            "<div class='col-md-12'>
              ".$c->nombres." ".$c->apellido_paterno." ".$c->apellido_materno."
            </div>",
            "<div class='col-md-12'>
                ".$opciones."
            </div>"
            ];
      }

        return response()->json(["aaData" => $resultado]);
    }

    public function modificarCuracion(Request $request){
        try {
            DB::beginTransaction();
            $editado = HojaCuraciones::where("id", $request->id)->first();
            //continuacion del editado
            $editado->visible = false;
            $editado->usuario_modifica = Auth::user()->id;
            $editado->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $editado->tipo_modificacion = 'Editado';
            $editado->save();

            $nuevoRegistro = new HojaCuraciones;
            $nuevoRegistro->caso = $editado->caso;
            $nuevoRegistro->id_anterior = $editado->id;
            $nuevoRegistro->usuario = Auth::user()->id;
            $nuevoRegistro->fecha_valoracion = Carbon::parse(strip_tags($request->thorariov))->format("Y-m-d H:i:s");
            $nuevoRegistro->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            //homologaciones
            $nuevoRegistro->aspecto = HojaCuraciones::homologarAspecto($request->taspecto);
            $nuevoRegistro->mayor_extension = HojaCuraciones::homologarMayorExtension($request->tmayorextension);
            $nuevoRegistro->profundidad = HojaCuraciones::homologarProfundidad($request->tprofundidad);
            $nuevoRegistro->exudado_cantidad = HojaCuraciones::homologarExudadoCantidad($request->tcantidad);
            $nuevoRegistro->exudado_calidad = HojaCuraciones::homologarExudadoCalidad($request->tcalidad);
            $nuevoRegistro->tejido_esfacelado = HojaCuraciones::homologarEscafelado($request->tesfacelado);
            $nuevoRegistro->tejido_granulatorio = HojaCuraciones::homologarGranulatorio($request->tgranulatorio);
            $nuevoRegistro->edema = HojaCuraciones::homologarEdema($request->tedema);
            $nuevoRegistro->dolor = HojaCuraciones::homologarDolor($request->tdolor);
            $nuevoRegistro->piel_circundante = HojaCuraciones::homologarPielCircundante($request->tpiel);            

            $nuevoRegistro->visible = true;
            $nuevoRegistro->save();
            DB::commit();
            return response()->json(["exito" => "Se ha actualizado la informacion de valoración de herida"]);
        } catch (Exception $ex) {
            log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar la informacion de valoración de herida"]);
        }
    }

    public function modificarCuracionSimple(Request $request){
      
        try {
            DB::beginTransaction();
            $editado = HojaCuracionesSimple::where("id", $request->id)->first();
            $editado->visible = false;
            $editado->usuario_modifica = Auth::user()->id;
            $editado->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $editado->tipo_modificacion = 'Editado';
            $editado->save();

            $nuevoRegistro = new HojaCuracionesSimple;
            $nuevoRegistro->caso = $editado->caso;
            $nuevoRegistro->id_anterior = $editado->id;
            $nuevoRegistro->usuario = Auth::user()->id;
            $nuevoRegistro->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $nuevoRegistro->tipo_curacion = $editado->tipo_curacion;
            $nuevoRegistro->observaciones = strip_tags($request->tobservaciones);
            $nuevoRegistro->proxima_curacion =  Carbon::parse(strip_tags($request->tproximacuracion))->format("Y-m-d H:i:s");
            $nuevoRegistro->visible = true;
            $nuevoRegistro->save();
            
            DB::commit();
            return response()->json(["exito" => "Se ha actualizado la informacion de valoración de herida"]);
        } catch (Exception $ex) {
            log::info($ex);
            DB::rollback();
            return response()->json(["error" => "Error al actualizar la informacion de valoración de herida"]);
        }
    }

    public function ingresoHojaCuracion(Request $request){
        try {
            DB::beginTransaction();
            $hojaCuracion = new HojaCuraciones;
            $hojaCuracion->caso = $request->idCaso;
            $hojaCuracion->usuario = Auth::user()->id;
            $hojaCuracion->fecha_valoracion = Carbon::parse(strip_tags($request->horariov));
            $hojaCuracion->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $hojaCuracion->visible = true;

            //homologaciones
            $hojaCuracion->aspecto = HojaCuraciones::homologarAspecto($request->aspecto);
            $hojaCuracion->mayor_extension = HojaCuraciones::homologarMayorExtension($request->mayorExtension);
            $hojaCuracion->profundidad = HojaCuraciones::homologarProfundidad($request->profundidad);
            $hojaCuracion->exudado_cantidad = HojaCuraciones::homologarExudadoCantidad($request->cantidad);
            $hojaCuracion->exudado_calidad = HojaCuraciones::homologarExudadoCalidad($request->calidad);
            $hojaCuracion->tejido_esfacelado = HojaCuraciones::homologarEscafelado($request->esfacelado);
            $hojaCuracion->tejido_granulatorio = HojaCuraciones::homologarGranulatorio($request->granulatorio);
            $hojaCuracion->edema = HojaCuraciones::homologarEdema($request->edema);
            $hojaCuracion->dolor = HojaCuraciones::homologarDolor($request->dolor);
            $hojaCuracion->piel_circundante = HojaCuraciones::homologarPielCircundante($request->pielC);

            //$hojaCuracion->observaciones = strip_tags($request->observaciones);
            //$hojaCuracion->proxima_curacion = Carbon::parse($request->proximaCuracion)->format("Y-m-d");
            $hojaCuracion->save();
            DB::commit();
            return response()->json(["exito" => "El ingreso se ha realizado exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            log::info("error: ",$ex);
            return response()->json(["error" => "No se ha podido realizar el ingreso"]);
        }
    }

    public function ingresoHojaCuracionSimple(Request $request){
        
        try {

            DB::beginTransaction();
            $hojaCuracionSimple = new HojaCuracionesSimple;
            $hojaCuracionSimple->caso = $request->idCaso;
            $hojaCuracionSimple->usuario = Auth::user()->id;
            $hojaCuracionSimple->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $hojaCuracionSimple->visible = true;
            $hojaCuracionSimple->tipo_curacion = ($request->tipo_curacion == 1) ? 'Simple' : 'Avanzada';
            $hojaCuracionSimple->observaciones = strip_tags($request->observaciones);
            $hojaCuracionSimple->proxima_curacion = Carbon::parse($request->proximaCuracion)->format("Y-m-d");

            $hojaCuracionSimple->save();
            DB::commit();
            return response()->json(["exito" => "El ingreso se ha realizado exitosamente"]);
        } catch (Exception $ex) {
            DB::rollback();
            log::info("error: ",$ex);
            return response()->json(["error" => "No se ha podido realizar el ingreso"]);
        }
    }

    public function validar_tipo_curacion(Request $request){
        $this->tipos_validos = ['1'=>'1','2'];
        $validador = Validator::make($request->all(), [
            'tipo_curacion' => Rule::in($this->tipos_validos)
        ]);

        if($validador->fails()){
            return response()->json(["valid" => false, "message" => "Debe seleccionar un tipo de curación existente"]);
        }else{
            return response()->json(["valid" => true]);
        }
    }

    public function eliminarCuracion(Request $request){
        try {
            DB::beginTransaction();
            $fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");
            $eliminado = HojaCuraciones::where("id", $request->id)->first();
            $eliminado->usuario_modifica = Auth::user()->id;
            $eliminado->fecha_eliminacion = $fecha_eliminacion;
            $eliminado->tipo_modificacion = 'Eliminado';
            $eliminado->visible = false;
            $eliminado->save();
            DB::commit();
            return response()->json(["exito" => "Se ha eliminado la valoración de herida"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar la valoración de herida"]);
        }
    }

    public function eliminarCuracionSimple(Request $request){
        try {
            DB::beginTransaction();
            $fecha_eliminacion = Carbon::now()->format("Y-m-d H:i:s");
            $eliminado = HojaCuracionesSimple::where("id", $request->id)->first();
            $eliminado->usuario_modifica = Auth::user()->id;
            $eliminado->fecha_modificacion = $fecha_eliminacion;
            $eliminado->tipo_modificacion = 'Eliminado';
            $eliminado->visible = false;
            $eliminado->save();
            DB::commit();
            return response()->json(["exito" => "Se ha eliminado la valoración de herida"]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(["error" => "Error al eliminar la valoración de herida"]);
        }
    }
}

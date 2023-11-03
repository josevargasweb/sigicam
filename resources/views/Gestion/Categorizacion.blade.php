@for($i = 0; $i < $dias; $i++)

    <div class="form-group col-md-12">
        <label class="col-sm-6 control-label" for="cat-{{$i}}">
            Categorización para {{$desde->copy()->addDays($i+1)->format('d-m-Y')}}
        </label>
        <div class="col-sm-6">
            {{Form::select("cat-$i", ["Sin riesgo"] + Riesgo::getRiesgos(), null, ["class" => "form-control"])}}

        </div>
    </div>
@endfor
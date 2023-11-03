<div class="form-group col-md-12" id="divParto" hidden>
    {{-- <div class="col-md-2"> --}}
        <label class="col-sm-2 control-label">Parto: </label>
    {{-- </div> --}}
    <div class="col-sm-10">
        <label class="radio-inline">{{Form::radio('parto', "no", false)}}No</label>
        <label class="radio-inline">{{Form::radio('parto', "si", false)}}Si</label>
    </div>
</div>
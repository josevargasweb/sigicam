<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Nova;

class validarNovaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'estado_mental' => 'required|integer',
            'incontinencia' => 'required|integer',
            'movilidad' => 'required|integer',
            'nutricion_ingesta' => 'required|integer',
            'actividad' => 'required|integer',
            'total' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'estado_mental.required'   => 'El :attribute es obligatorio.',
            'estado_mental.integer'   => 'El :attribute debe ser diferente a seleccionar.',
    
            'incontinencia.required'   => 'La :attribute es obligatorio.',
            'incontinencia.integer'   => 'La :attribute debe ser diferente a seleccionar.',
    
            'movilidad.required'   => 'La :attribute es obligatorio.',
            'movilidad.integer'   => 'La :attribute debe ser diferente a seleccionar.',
    
            'nutricion_ingesta.required'   => 'La :attribute es obligatorio.',
            'nutricion_ingesta.integer'   => 'La :attribute debe ser diferente a seleccionar.',

            'actividad.required'    => 'La :attribute es obligatorio.',
            'actividad.integer'   => 'La :attribute debe ser diferente a seleccionar.',
    
            'total.required'   => 'El :attribute es obligatorio.',
            'total.integer'   => 'El :attribute debe ser diferente a seleccionar.',            
        ];
    }
}

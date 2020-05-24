<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmmunitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // #TODO #SECURITY Not currently checking this.
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
            // required fields for all types
            'manufacturer'            => 'required',
            'name'                    => 'required',
            'purpose_id'              => 'integer',
            // common fields for all types
            'weight'                  => 'string',
            // Shotgun specific fields
            'shell_length_id'         => 'integer',
            'shell_type_id'           => 'integer',
            'shot_material_id'        => 'integer',
            // Bullet specific fields
            'ammunition_casing_id'    => 'integer',
            'ammunition_condition_id' => 'integer',
            'bullet_type_id'          => 'integer',
            'primer_type_id'          => 'integer',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmmunitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // #TODO #SECURITY Not currently checking this.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // required fields for all types
            'manufacturer'            => 'required',
            'label'                   => 'required',
            'purpose_id'              => 'required|integer',
            // common fields for all types
            'weight'                  => 'integer|nullable',
            // Shotgun specific fields
            'shell_length_id'         => 'integer|nullable',
            'shell_type_id'           => 'integer|nullable',
            'shot_material_id'        => 'integer|nullable',
            // Bullet specific fields
            'ammunition_casing_id'    => 'integer|nullable',
            'ammunition_condition_id' => 'integer|nullable',
            'bullet_type_id'          => 'integer|nullable',
            'primer_type_id'          => 'integer|nullable',
        ];
    }
}

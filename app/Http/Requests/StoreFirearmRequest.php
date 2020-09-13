<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFirearmRequest extends FormRequest
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
            'label'        => 'required',
            'manufacturer' => 'required',
            'model'        => 'required',
            'caliber_id'   => 'array',
            'caliber_id.*' => 'integer',
        ];
    }
}

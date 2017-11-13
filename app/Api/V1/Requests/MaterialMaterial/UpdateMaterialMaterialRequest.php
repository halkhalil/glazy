<?php

namespace App\Api\V1\Requests\MaterialMaterial;

//use App\Http\Requests\Request;

//use Illuminate\Foundation\Http\FormRequest;
use App\Api\V1\Requests\ApiFormRequest;

class UpdateMaterialMaterialRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        $rules['materialComponents.*.componentMaterialId'] = 'required|integer';
        $rules['materialComponents.*.percentageAmount'] = 'required|numeric';

        return $rules;
    }

    public function attributes()
    {
        return [
            'materialComponents.*.componentMaterialId' => 'the recipe ingredient material id',
            'materialComponents.*.percentageAmount' => 'the percentage amount of the recipe ingredient',
            'materialComponents.*.isAdditional' => 'true if this ingredient is added in addition to 100%',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'materialComponents.*.componentMaterialId' => 'A recipe component material id is required',
            'materialComponents.*.percentageAmount' => 'The percentage amount of the recipe ingredient is required',
            'materialComponents.*.isAdditional' => 'Required to specifiy whether or not the recipe ingredient is additional',
        ];
    }
}

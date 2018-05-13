<?php

namespace App\Api\V1\Requests\MaterialComment;

//use Illuminate\Foundation\Http\FormRequest;
use App\Api\V1\Requests\ApiFormRequest;

class CreateMaterialCommentRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributes()
    {
        return [
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
        ];
    }
}

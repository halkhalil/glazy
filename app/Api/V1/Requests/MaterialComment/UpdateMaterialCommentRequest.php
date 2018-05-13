<?php

namespace App\Api\V1\Requests\MaterialComment;

//use App\Http\Requests\Request;
use App\Api\V1\Requests\ApiFormRequest;

class UpdateMaterialCommentRequest extends ApiFormRequest
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
}

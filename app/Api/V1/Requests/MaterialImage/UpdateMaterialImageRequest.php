<?php

namespace App\Api\V1\Requests\MaterialImage;

//use App\Http\Requests\Request;
use App\Api\V1\Requests\ApiFormRequest;

class UpdateMaterialImageRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }
/*
    public function authorize()
    {
        $recipeRepository = new MaterialImageRepository();
        $recipe = $recipeRepository->get($this->route('recipe'));

        return $recipe && $this->user()->can('update', $recipe);
    }
*/
    public function rules()
    {
        return [
//            'title' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
//            'title' => 'the material\'s name',
        ];
    }
}

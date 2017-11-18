<?php

namespace App\Api\V1\Requests\MaterialReview;

//use App\Http\Requests\Request;
use App\Api\V1\Requests\ApiFormRequest;

class UpdateMaterialReviewRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }
/*
    public function authorize()
    {
        $recipeRepository = new MaterialReviewRepository();
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

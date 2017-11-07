<?php

namespace App\Api\V1\Requests\Recipe;

//use App\Http\Requests\Request;

//use Illuminate\Foundation\Http\FormRequest;
use App\Api\V1\Requests\ApiFormRequest;

class UpdateRecipeRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }
/*
    public function authorize()
    {
        $recipeRepository = new RecipeRepository();
        $recipe = $recipeRepository->get($this->route('recipe'));

        return $recipe && $this->user()->can('update', $recipe);
    }
*/
    public function rules()
    {
        return [
//            'material' => 'array|required',
//            'material.name' => 'required|string',
            'name' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
//            'material.name' => 'the material\'s name',
            'name' => 'the recipe\'s name',
        ];
    }
}

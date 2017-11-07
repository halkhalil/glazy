<?php

namespace App\Api\V1\Controllers\Glazy;

//use App\Api\V1\Controllers\ApiController;

use App\Api\V1\Transformers\Material\MaterialTransformer;

use App\Api\V1\Repositories\Material\RecipeRepository;

use App\Models\Glazy\Material\Material;

use App\Api\V1\Requests\Recipe\CreateRecipeRequest;
use App\Api\V1\Requests\Recipe\UpdateRecipeRequest;

use App\Models\Glazy\Material\MaterialImage;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class RecipeController extends ApiBaseController
{

    /**
     * @var RecipeRepository
     */
	protected $recipeRepository;

    public function __construct(
        RecipeRepository $recipeRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->recipeRepository = $recipeRepository;

//        $this->middleware('auth.basic', ['only' => 'store']);
    }

    public function show($id)
    {
        $recipe = $this->recipeRepository->getWithDetails($id);

        if (!$recipe)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($recipe->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $recipe)) {
                return $this->respondUnauthorized('Recipe is private.'.Auth::guard()->user());
            }
        }

        $resource = new FractalItem($recipe, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function thumbnail($materialId, $imageId)
    {
        // TODO: just get material from image?
        $material = Material::find($materialId);
        $image = MaterialImage::find($imageId);

        if (!$material || !$image)
        {
            return $this->respondNotFound('Material or Image does not exist');
        }

        $this->authorize('update', $material);

        $material->thumbnail_id = $image->id;
        $material->save();

        return $this->respondUpdated('Thumbnail set');
    }

    public function store(CreateRecipeRequest $request)
    {
        $this->recipeRepository->create($request->all());

        return $this->respondCreated('Recipe successfully created');
    }

    public function update($recipeId, UpdateRecipeRequest $request)
    {
        //$data = $request->get('form', []);
        $data = $request->all();

        $material = $this->recipeRepository->get($recipeId);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        $this->authorize('update', $material);

        $recipe = $this->recipeRepository->update($material, $data);

        return $this->respond([
            'data' => [
                'material' => $this->materialTransformer->transform($recipe)
            ]
        ]);


    }

    public function destroy($recipeId)
    {
        $material = $this->recipeRepository->get($recipeId);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        $this->authorize('delete', $material);

        $result = $this->recipeRepository->destroy($recipeId);

        return $this->respondDeleted('Recipe deleted');
    }


    public function copy($recipeId)
    {
        $material = $this->recipeRepository->get($recipeId);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            $this->authorize('view', $material);
        }

        $copiedMaterial = $this->recipeRepository->copy($material);

        return $this->respond([
            'data' => [
                'material' => $this->materialTransformer->transform($copiedMaterial)
            ]
        ]);

    }


    public function publish($recipeId)
    {
        $material = $this->recipeRepository->getWithDetails($recipeId);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        $this->authorize('update', $material);

        $material->is_private = false;
        $material->save();

        return $this->respond([
            'data' => [
                'material' => $this->materialTransformer->transform($material)
            ]
        ]);
    }

    public function unpublish($recipeId)
    {
        $material = $this->recipeRepository->getWithDetails($recipeId);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        $this->authorize('update', $material);

        $material->is_private = true;
        $material->save();

        return $this->respond([
            'data' => [
                'material' => $this->materialTransformer->transform($material)
            ]
        ]);
    }



}

<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Transformers\Material\MaterialTransformer;
use App\Api\V1\Transformers\Material\ShallowMaterialTransformer;

use App\Api\V1\Repositories\PrimitiveMaterialRepository;

use App\Models\Material;

use App\Api\V1\Requests\Recipe\CreateRecipeRequest;
use App\Api\V1\Requests\Recipe\UpdateRecipeRequest;

use App\Models\MaterialImage;

use Illuminate\Support\Facades\Log;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class PrimitiveMaterialController extends ApiBaseController
{
    /**
     * @var PrimitiveMaterialRepository
     */
	protected $primitiveMaterialRepository;

    public function __construct(
        PrimitiveMaterialRepository $primitiveMaterialRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->primitiveMaterialRepository = $primitiveMaterialRepository;

//        $this->middleware('auth.basic', ['only' => 'store']);
    }

    public function userlist($user_id = null)
    {
        $materials = $this->primitiveMaterialRepository->userlist($user_id);
        $resource = new FractalCollection($materials, new ShallowMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }

    public function editRecipeMaterialList($recipe_id = null)
    {
        $materials = $this->primitiveMaterialRepository->editRecipeMaterialList($recipe_id);
        $resource = new FractalCollection($materials, new ShallowMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }
}

<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Controllers\ApiController;
use App\Api\V1\Repositories\MaterialRepository;
use App\Api\V1\Transformers\Material\MaterialTransformer;

use App\Api\V1\Repositories\MaterialMaterialRepository;

use App\Api\V1\Requests\MaterialMaterial\UpdateMaterialMaterialRequest;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialMaterialController extends ApiBaseController
{

    /**
     * @var MaterialMaterialRepository
     */
	protected $materialMaterialRepository;

    public function __construct(
        MaterialMaterialRepository $materialMaterialRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialMaterialRepository = $materialMaterialRepository;
    }

    public function store(UpdateMaterialMaterialRequest $request)
    {
        $data = $request->all();

        // Create a new Material
        $materialRepository = new MaterialRepository();
        $material_data = [
            'name' => 'Please give this recipe a name!',
            'is_analysis' => false,
            'is_primitive' => false,
        ];
        $material = $materialRepository->create($material_data);
        // Add materials to new recipe
        $material = $this->materialMaterialRepository->updateAll($material->id, $data);

        $resource = new FractalItem($material, new MaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }

    public function update($recipeId, UpdateMaterialMaterialRequest $request)
    {
//        return $this->respondNotFound('Recipe does not exist');

        $data = $request->all();

        $material = $this->materialMaterialRepository->updateAll($recipeId, $data);

        $resource = new FractalItem($material, new MaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }

}

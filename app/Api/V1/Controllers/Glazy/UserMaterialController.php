<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Repositories\UserMaterialRepository;
use App\Api\V1\Transformers\UserMaterial\UserMaterialTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;
use App\Api\V1\Serializers\GlazySerializer;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class UserMaterialController extends ApiBaseController
{

    /**
     * @var UserMaterialRepository
     */
	protected $userMaterialRepository;

    public function __construct(
        UserMaterialRepository $userMaterialRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->userMaterialRepository = $userMaterialRepository;
    }

    public function index(Request $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to do this.');
        }

        $userMaterials = $this->userMaterialRepository->getByUserId();

        $resource = new FractalCollection($userMaterials, new UserMaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function show($id)
    {
        $userMaterial = $this->userMaterialRepository->getWithDetails($id);

        if (!Auth::guard()->user() || !Auth::guard()->user()->can('view', $userMaterial)) {
            return $this->respondUnauthorized('This is private.');
        }

        if (!$userMaterial)
        {
            return $this->respondNotFound('Item does not exist');
        }

        $resource = new FractalItem($userMaterial, new UserMaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function store(Request $request)
    {
        $this->userMaterialRepository->create($request->all());

        return $this->respondCreated('Recipe successfully created');
    }

    public function update($id, Request $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to do this.');
        }

        $data = $request->all();

        $userMaterial = $this->userMaterialRepository->get($id);

        if (! $userMaterial)
        {
            return $this->respondNotFound('Item does not exist');
        }

        if (!Auth::guard()->user()->can('update', $userMaterial)) {
            return $this->respondUnauthorized('This item does not belong to you.');
        }

        $userMaterial = $this->userMaterialRepository->update($userMaterial, $data);

        $resource = new FractalItem($userMaterial, new UserMaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function destroy($id)
    {
        $userMaterial = $this->userMaterialRepository->get($id);

        if (! $userMaterial)
        {
            return $this->respondNotFound('Item does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $userMaterial)) {
            return $this->respondUnauthorized('This item does not belong to you.');
        }

        $result = $this->userMaterialRepository->destroy($id);

        return $this->respondDeleted('Item deleted');
    }
}

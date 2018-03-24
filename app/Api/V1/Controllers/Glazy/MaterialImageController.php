<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Controllers\ApiController;
//use App\Api\V1\Transformers\MaterialImageTransformer;

use App\Api\V1\Controllers\Glazy\ApiBaseController;
use App\Api\V1\Repositories\MaterialImageRepository;

use App\Api\V1\Repositories\MaterialRepository;
use App\Api\V1\Transformers\MaterialImage\MaterialImageTransformer;
use App\Models\Material;
use App\Models\MaterialImage;

use App\Api\V1\Requests\MaterialImage\CreateMaterialImageRequest;
use App\Api\V1\Requests\MaterialImage\UpdateMaterialImageRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection as FractalCollection;

use App\Api\V1\Serializers\GlazySerializer;


/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialImageController extends ApiBaseController
{
    const MAX_ITEMS_PER_PAGE = 50;
    const DEFAULT_ITEMS_PER_PAGE = 48;

    /**
     * @var MaterialImageRepository
     */
    protected $materialImageRepository;

    public function __construct(
        MaterialImageRepository $materialImageRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialImageRepository = $materialImageRepository;
    }

    /*
    public function index(Request $request)
    {
        $page = (int)$request->input('p');
        if (!$page || $page <= 0) {
            // if page not specified, start w/ page 1
            $page = 1;
        }

        $search_user_id = (int)$request->input('u');
        $keywords = $request->input('keywords');
        $sort_order_id = $request->input('sort_order');
        $view_option = $request->input('view_option');
        $view_option_paginate = $request->input('pag');

        $count = $request->input('count');

        $query = MaterialImage::query();

        $user = null;
        if (Auth::check()) {
            $user = Auth::guard('api')->user();
        }

        if ($search_user_id) {
            if ($user) {
                $query->ofCreatedByUserId($search_user_id, $user->id);
            } else {
                $query->ofCreatedByUserId($search_user_id);
            }
        } elseif ($user) {
            $query->ofNotPrivate($user->id);
        } else {
            $query->ofPublic($query);
        }

        $query->ofKeywords($keywords);

        $query->with('material');
        $query->with('material.from_orton_cone');
        $query->with('material.to_orton_cone');
        $query->with('material.analysis');
        $query->with('material.atmospheres');
        $query->with('material.thumbnail');
        $query->with('material.created_by_user');

        $query->orderBy('updated_at', 'DESC');

        if ($count && $count < self::MAX_ITEMS_PER_PAGE) {
            $images = $query->paginate($count, ['*'], 'page', $page);
        } else {
            $images = $query->paginate(self::DEFAULT_ITEMS_PER_PAGE, ['*'], 'page', $page);
        }

        if (!$images || $images->total() == 0) {
            return $this->respondNotFound('No images found.');
        }

        $transformer = new MaterialImageTransformer();

        return $this->respond($transformer->transformPaginatedCollection($images));
    }

    public function show($id)
    {
        $image = $this->materialImageRepository->getWithDetails($id);

        if (! $image)
        {
            return $this->respondNotFound('Image does not exist');
        }

        return $this->respond([
            'data' => [
                'image' => $this->materialImageTransformer->transform($image)
            ]
        ]);

    }
    */

    public function userList($user_id)
    {
        $images = $this->materialImageRepository->userList($user_id);

        if (! $images)
        {
            return $this->respondNotFound('No Images Found for that User');
        }

        $resource = new FractalCollection($images, new MaterialImageTransformer());
        return $this->manager->createData($resource)->toArray();
    }

    public function store(CreateMaterialImageRequest $request)
    {
        $data = $request->all();

        $materialRepository = new MaterialRepository();

        // TODO: remove and put in request object
        if (!isset($data['materialId']) || !$data['materialId'])
        {
            return $this->respondNotFound('Material id not sent.');
        }

        $material = Material::find($data['materialId']);

        if (! $material)
        {
            return $this->respondNotFound('Material does not exist');
        }

        $this->materialImageRepository->create($material, $data);

        return $this->respondCreated('Material Image successfully created');
    }

    public function update($imageId, UpdateMaterialImageRequest $request)
    {
        $data = $request->all();

        $materialImage = $this->materialImageRepository->get($imageId);

        if (! $materialImage)
        {
            return $this->respondNotFound('Image does not exist');
        }

        if (!Auth::guard()->user()->can('update', $materialImage)) {
            return $this->respondUnauthorized('This image does not belong to you.');
        }

        $this->materialImageRepository->update($materialImage, $data);

        return $this->respondUpdated('Material Image successfully updated');
    }

    public function destroy($materialImageId)
    {
        $materialImage = $this->materialImageRepository->get($materialImageId);

        if (! $materialImage)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $materialImage)) {
            return $this->respondUnauthorized('This image does not belong to you.');
        }

        $this->materialImageRepository->destroy($materialImage);

        return $this->respondDeleted('Image deleted');
    }

}

<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Controllers\Glazy\ApiBaseController;
use App\Api\V1\Repositories\MaterialReviewRepository;

use App\Api\V1\Repositories\MaterialRepository;
use App\Models\Material;

use App\Api\V1\Requests\MaterialReview\CreateMaterialReviewRequest;
use App\Api\V1\Requests\MaterialReview\UpdateMaterialReviewRequest;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Illuminate\Http\Request;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialReviewController extends ApiBaseController
{
    /**
     * @var MaterialReviewRepository
     */
    protected $materialReviewRepository;

    public function __construct(
        MaterialReviewRepository $materialReviewRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialReviewRepository = $materialReviewRepository;
    }

    public function store(CreateMaterialReviewRequest $request)
    {
        $data = $request->all();

        $this->materialReviewRepository->create($data);

        return $this->respondCreated('Material Review successfully created');
    }

    public function update($reviewId, UpdateMaterialReviewRequest $request)
    {
        $data = $request->all();

        $materialReview = $this->materialReviewRepository->get($reviewId);

        if (! $materialReview) {
            return $this->respondNotFound('Review does not exist');
        }

        if (!Auth::guard()->user()->can('update', $materialReview)) {
            return $this->respondUnauthorized('This review does not belong to you.');
        }

        $this->materialReviewRepository->update($materialReview, $data);

        return $this->respondUpdated('Material Review successfully updated');
    }

    public function destroy($materialReviewId)
    {
        $materialReview = $this->materialReviewRepository->get($materialReviewId);

        if (! $materialReview) {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $materialReview)) {
            return $this->respondUnauthorized('This review does not belong to you.');
        }

        $this->authorize('delete', $materialReview);

        $this->materialReviewRepository->destroy($materialReview);

        return $this->respondDeleted('Review deleted');
    }


}

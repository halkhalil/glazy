<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Controllers\Glazy\ApiBaseController;
use App\Api\V1\Repositories\MaterialCommentRepository;

use App\Api\V1\Repositories\MaterialRepository;
use App\Models\Material;

use App\Api\V1\Requests\MaterialComment\CreateMaterialCommentRequest;
use App\Api\V1\Requests\MaterialComment\UpdateMaterialCommentRequest;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Illuminate\Http\Request;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialCommentController extends ApiBaseController
{
    /**
     * @var MaterialCommentRepository
     */
    protected $materialCommentRepository;

    public function __construct(
        MaterialCommentRepository $materialCommentRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialCommentRepository = $materialCommentRepository;
    }

    public function store(CreateMaterialCommentRequest $request)
    {
        $data = $request->all();

        $this->materialCommentRepository->create($data);

        return $this->respondCreated('Material Comment successfully created');
    }

    public function update($commentId, UpdateMaterialCommentRequest $request)
    {
        $data = $request->all();

        $materialComment = $this->materialCommentRepository->get($commentId);

        if (! $materialComment) {
            return $this->respondNotFound('Comment does not exist');
        }

        if (!Auth::guard()->user()->can('update', $materialComment)) {
            return $this->respondUnauthorized('This comment does not belong to you.');
        }

        $this->materialCommentRepository->update($materialComment, $data);

        return $this->respondUpdated('Material Comment successfully updated');
    }

    public function destroy($materialCommentId)
    {
        $materialComment = $this->materialCommentRepository->get($materialCommentId);

        if (! $materialComment) {
            return $this->respondNotFound('Comment does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $materialComment)) {
            return $this->respondUnauthorized('This comment does not belong to you.');
        }

        $this->materialCommentRepository->destroy($materialComment);

        return $this->respondDeleted('Comment deleted');
    }

}

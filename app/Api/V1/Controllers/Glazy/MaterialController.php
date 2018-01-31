<?php

namespace App\Api\V1\Controllers\Glazy;

//use App\Api\V1\Controllers\ApiController;

use App\Api\V1\Transformers\Material\MaterialTransformer;

use App\Api\V1\Repositories\MaterialRepository;

use App\Models\Material;

use App\Api\V1\Requests\Recipe\CreateRecipeRequest;
use App\Api\V1\Requests\Recipe\UpdateRecipeRequest;

use App\Models\MaterialImage;

use Illuminate\Support\Facades\Log;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialController extends ApiBaseController
{

    /**
     * @var MaterialRepository
     */
	protected $materialRepository;

    public function __construct(
        MaterialRepository $materialRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialRepository = $materialRepository;

//        $this->middleware('auth.basic', ['only' => 'store']);
    }

    public function show($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (!$material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $material)) {
                return $this->respondUnauthorized('Recipe is private.');
            }
        }

        $resource = new FractalItem($material, new MaterialTransformer());

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

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        $material->thumbnail_id = $image->id;
        $material->save();

        return $this->respondUpdated('Thumbnail set');
    }

    public function store(CreateRecipeRequest $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to create materials.');
        }

        $data = $request->all();
        $material = new Material();
        $material->created_by_user_id = Auth::guard()->user()->id;
        // TODO: Properly use store & update methods
        $material = $this->materialRepository->createOrUpdate($material, $data);

        if (!$material) {
            // Error updating the material
            return $this->respondInternalError('Internal Error creating the material');
        }

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
        //        return $this->respondCreated('Recipe successfully created');
    }

    public function update($id, UpdateRecipeRequest $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to edit recipes.');
        }

        Log::info('Arrived update method');
        //$data = $request->get('form', []);
        $data = $request->all();

        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        $material = $this->materialRepository->update($material, $data);

        if (!$material) {
            // Error updating the material
            return $this->respondInternalError('Internal Error updating the recipe');
        }

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function destroy($id)
    {
        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        if ($material->is_archived) {
            return $this->respondUnauthorized('Archived recipes cannot be deleted.');
        }

        $result = $this->materialRepository->destroy($id);

        return $this->respondDeleted('Recipe deleted');
    }


    public function copy($id)
    {
        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $material)) {
                return $this->respondUnauthorized('Recipe is private.');
            }
        }

        $copiedMaterial = $this->materialRepository->copy($material);

        $resource = new FractalItem($copiedMaterial, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }


    public function publish($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        $material->is_private = false;
        $material->save();

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function unpublish($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        if ($material->is_archived) {
            return $this->respondUnauthorized('Archived recipes cannot be deleted.');
        }

        $material->is_private = true;
        $material->save();

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    /**
     * @param $id
     * @return Response
     *
     */
    public function export($id)
    {
        $export_type = $request->input('type');

        if ($export_type === 'Card')
        {
            // Temporarily just redirect to the recipe page
            return redirect()->route('profile', ['id' => 1]);
        }
    }

}

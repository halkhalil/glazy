<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Repositories\CollectionRepository;
use App\Models\Collection;
use App\Models\Material;
use Illuminate\Http\Request;

use Auth;

class CollectionController extends ApiBaseController
{
    protected $collectionRepository;

    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }

    public function destroy(Request $request, $collection_id)
    {
        $collection = Collection::find($collection_id);

        if (! $collection) {
            return $this->respondNotFound('Collection does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $collection)) {
            return $this->respondUnauthorized('This collection does not belong to you.');
        }

        $this->collectionRepository->destroy($collection_id);

        return $this->respondDeleted('Collection deleted');
    }
}

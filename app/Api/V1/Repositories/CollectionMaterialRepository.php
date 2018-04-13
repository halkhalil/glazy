<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\CollectionMaterial;

use Illuminate\Database\Eloquent\Model;

class CollectionMaterialRepository extends Repository
{
    public function getModel()
    {
        return new CollectionMaterial();
    }

    /*
     * Obtain a material's collections via the MaterialRepository getWithDetails function
    public function getByMaterialId($material_id)
    {
        return CollectionMaterial::with('collection')
            ->where('material_id', $material_id)
            ->get();
    }
    */

    public function create(array $data)
    {
        $collection_id = $data['collection_id'];
        $material_id = $data['material_id'];

        $collectionMaterial = CollectionMaterial::where('collection_id', $collection_id)
            ->where('material_id', $material_id)->first();

        if ($collectionMaterial) {
            // This collection/material is already in the db
            return;
        }

        $collectionMaterial = $this->getModel();
        $collectionMaterial->collection_id = $collection_id;
        $collectionMaterial->material_id = $material_id;
        $collectionMaterial->save();

        $collection = $collectionMaterial->collection;
        $collection->incrementCount();
        $collection->save();

        return $collectionMaterial;
    }

    public function destroy($collection_id, $material_id)
    {
        $collectionMaterial =
            CollectionMaterial::where('collection_id', $collection_id)
            ->where('material_id', $material_id)->first();

        if (!$collectionMaterial) {
            return false;
        }

        $collection = $collectionMaterial->collection;
        $collectionMaterial->delete();
        $collection->decrementCount();
        $collection->save();

        return true;
    }

}

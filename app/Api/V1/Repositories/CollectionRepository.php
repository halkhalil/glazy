<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CollectionRepository extends Repository
{
    const USER_COLLECTION_TYPE_ID = 1;

    public function getModel()
    {
        return new Collection();
    }

    public function get($id)
    {
        return Collection::find($id);
    }

    public function userlist($id)
    {
        return Collection::where('created_by_user_id', $id)->with('thumbnail')->get();
    }

    public function getByUser($id)
    {
        return Collection::where('created_by_user_id', $id)->get();
    }

    public function getWithDetails($id)
    {
        return Collection::with('materials')
            ->with('parent')
            ->with('thumbnail')
            ->with('created_by_user')
            ->find($id);
    }

    public function getAll()
    {
        return Collection::get();
    }

    public function create(array $data)
    {
        $collection = $this->getModel();
        
        $collection->fill($data);

        $collection->collection_type_id = self::USER_COLLECTION_TYPE_ID;

        $collection->created_by_user_id = Auth::user()->id;
//TODO: ???        $collection->created_by_user_id = 1;

        $collection->save();

        return $collection;
    }

    public function update(Model $collection, array $data)
    {
        $collection->fill($data);
        $collection->save();
        return $collection;
    }

    public function destroy($id)
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return false;
        }

        $collection->delete();

        return true;
    }

}

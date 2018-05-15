<?php

namespace App\Api\V1\Repositories;

use App\Models\Event;
use App\Models\Material;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Notifications\MaterialCommentAdded;

use App\Models\MaterialComment;

class MaterialCommentRepository extends Repository
{

    public function getModel()
    {
        return new MaterialComment();
    }

    public function get($id)
    {
        return MaterialComment::find($id);
    }

    public function getWithDetails($id)
    {
        return MaterialComment::with('material')->with('user')->find($id);
    }

    public function getByUserId($user_id)
    {
        return MaterialComment::where('user_id', $user_id)->get();
    }

    public function getByMaterialId($material_id)
    {
        return MaterialComment::where('material_id', $material_id)->get();
    }

    public function create(array $data)
    {
        $material_id = $data['material_id'];
        $user_id =  Auth::user()->id;

        $material = Material::with('created_by_user')->find($material_id);

        if (!$material) {
            return false;
        }

        // TODO Authorize?
        $materialComment = $this->getModel();

        $materialComment->content = $data['content'];
        $materialComment->material_id = $material->id;
        $materialComment->user_id = $user_id;

        $materialComment->save();

        // Set the material as updated
        $material->touch();

        // Notify the creator of this material that a new comment has
        // been added
        $material->created_by_user->notify(new MaterialCommentAdded(
            $material,
            Auth::user(),
            $materialComment
        ));

        return $materialComment;
    }

    public function update(Model $materialComment, array $data)
    {
        $material = Material::with('created_by_user')->find($materialComment->material_id);

        $user_id =  Auth::user()->id;

        if (!$material || $user_id !== $materialComment->user_id) {
            return false;
        }

        $materialComment->content = $data['content'];
        $materialComment->save();

        $material->touch();

        // Notify the creator of this material that a new comment has
        // been added
        $material->created_by_user->notify(new MaterialCommentAdded(
            $material,
            Auth::user(),
            $materialComment
        ));

        return $materialComment;
    }

    public function destroy(Model $materialComment)
    {
        $material = Material::find($materialComment->material_id);

        if (!$material) {
            return false;
        }

        $materialComment->delete();
    }

}

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

        $material = Material::with('created_by_user')
            ->with('reviews')
            ->with('reviews.user')
            ->with('comments')
            ->with('comments.user')
            ->find($material_id);

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

        $this->sendCommentAddedNotifications($materialComment, $material);

        return $materialComment;
    }

    public function update(Model $materialComment, array $data)
    {
        $material = Material::with('created_by_user')
            ->with('reviews')
            ->with('reviews.user')
            ->with('comments')
            ->with('comments.user')
            ->find($materialComment->material_id);

        $user_id =  Auth::user()->id;

        if (!$material || $user_id !== $materialComment->user_id) {
            return false;
        }

        $materialComment->content = $data['content'];
        $materialComment->save();

        $material->touch();

        $this->sendCommentAddedNotifications($materialComment, $material);

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

    protected function sendCommentAddedNotifications(MaterialComment $materialComment, Material $material) {
        $currentUser = Auth::user();

        $notifiedUsers = [ $material->created_by_user->id => true ];

        foreach($material->comments as $comment) {
            if ($comment->user &&
                !array_key_exists($comment->user->id, $notifiedUsers)) {
                if ($currentUser->id !== $comment->user->id) {
                    // Notifiy all other users who've commented in this material
                    // (But don't notify the person who made the comment.)
                    $comment->user->notify(new MaterialCommentAdded(
                        $material,
                        $currentUser,
                        $materialComment
                    ));
                }
                $notifiedUsers[$comment->user->id] = true;
            }
        }

        foreach($material->reviews as $review) {
            if ($review->user &&
                !array_key_exists($review->user->id, $notifiedUsers)) {
                if ($currentUser->id !== $review->user->id) {
                    // Notifiy all other users who've reviewed in this material
                    // (But don't notify the person who made the review.)
                    $review->user->notify(new MaterialCommentAdded(
                        $material,
                        $currentUser,
                        $materialComment
                    ));
                }
                $notifiedUsers[$review->user->id] = true;
            }
        }

        // Notify the creator of this material that a new comment has been added
        if ($currentUser->id !== $material->created_by_user->id) {
            // But don't notify if the creator is the one who made the comment
            $material->created_by_user->notify(new MaterialCommentAdded(
                $material,
                $currentUser,
                $materialComment
            ));
        }
    }
}

<?php

namespace App\Api\V1\Repositories;

use App\Models\Material;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Api\V1\Repositories\Repository;

use App\Models\MaterialReview;
use App\Notifications\MaterialReviewAdded;

class MaterialReviewRepository extends Repository
{

    public function getModel()
    {
        return new MaterialReview();
    }

    public function get($id)
    {
        return MaterialReview::find($id);
    }

    public function getWithDetails($id)
    {
        return MaterialReview::with('material')->with('user')->find($id);
    }

    public function getByUserId($user_id)
    {
        return MaterialReview::where('user_id', $user_id)->get();
    }

    public function all()
    {
        return MaterialReview::all();
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
        $materialReview = $this->getModel();

        $materialReview->fill($data);

        $materialReview->material_id = $material->id;
        $materialReview->user_id = $user_id;

        $materialReview->save();

        // Update total rating info for material
        $material->rating_total += $materialReview->rating;
        $material->rating_number += 1;
        $material->rating_average = $material->rating_total / $material->rating_number;
        $material->save();

        $this->sendReviewAddedNotifications($materialReview, $material);

        return $materialReview;
    }

    public function update(Model $materialReview, array $data)
    {
        $material = Material::find($materialReview->material_id);

        if (!$material) {
            return false;
        }

        $old_rating = $materialReview->rating;

        $materialReview->fill($data);

        $materialReview->save();

        // Update total rating info for material
        $material->rating_total -= $old_rating;
        $material->rating_total += $materialReview->rating;
        $material->rating_average = $material->rating_total / $material->rating_number;
        $material->save();

        return $materialReview;
    }

    public function destroy(Model $materialReview)
    {
        $material = Material::find($materialReview->material_id);

        if (!$material) {
            return false;
        }

        // Update total rating info for material
        $material->rating_total -= $materialReview->rating;
        $material->rating_number -= 1;
        if ($material->rating_number) {
            // Re-calculate average if there are still ratings
            $material->rating_average = $material->rating_total / $material->rating_number;
        } else {
            // No other ratings for this material
            $material->rating_average = 0;
        }
        $material->save();

        $materialReview->delete();
    }

    protected function sendReviewAddedNotifications(MaterialReview $materialReview, Material $material) {
        $currentUser = Auth::user();

        $notifiedUsers = [ $material->created_by_user->id => true ];

        foreach($material->reviews as $review) {
            if ($review->user &&
                !array_key_exists($review->user->id, $notifiedUsers)) {
                if ($currentUser->id !== $review->user->id) {
                    // Notifiy all other users who've reviewed in this material
                    // (But don't notify the person who made the review.)
                    $review->user->notify(new MaterialReviewAdded(
                        $material,
                        $currentUser,
                        $materialReview
                    ));
                }
                $notifiedUsers[$review->user->id] = true;
            }
        }

        foreach($material->comments as $comment) {
            if ($comment->user &&
                !array_key_exists($comment->user->id, $notifiedUsers)) {
                if ($currentUser->id !== $comment->user->id) {
                    // Notifiy all other users who've commented in this material
                    // (But don't notify the person who made the comment.)
                    $comment->user->notify(new MaterialReviewAdded(
                        $material,
                        $currentUser,
                        $materialReview
                    ));
                }
                $notifiedUsers[$comment->user->id] = true;
            }
        }

        // Notify the creator of this material that a new review has been added
        if ($currentUser->id !== $material->created_by_user->id) {
            // But don't notify if the creator is the one who made the review
            $material->created_by_user->notify(new MaterialReviewAdded(
                $material,
                $currentUser,
                $materialReview
            ));
        }
    }
}

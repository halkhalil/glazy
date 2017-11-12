<?php

namespace App\Api\V1\Transformers\MaterialReview;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\MaterialReview;
use League\Fractal;

class MaterialReviewTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'user'
    ];

    public function transform($materialReview)
    {
        $review_data = array();

        if ($materialReview)
        {
            $review_data['id'] = $materialReview['id'];
            $review_data['material_id'] = $materialReview['material_id'];
            $review_data['user_id'] = $materialReview['user_id'];
            $review_data['title'] = $materialReview['title'];
            $review_data['description'] = $materialReview['description'];
            $review_data['rating'] = $materialReview['rating'];
            $review_data['created_at'] = $materialReview['created_at'];
            $review_data['updated_at'] = $materialReview['updated_at'];
        }

        return $review_data;
    }

    public function includeUser(MaterialReview $materialReview)
    {
        if ($materialReview->user) {
            return $this->item($materialReview->user, new UserTransformer());
        }
    }

}
<?php

namespace App\Api\V1\Transformers\MaterialReview;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\MaterialReview;
use League\Fractal;
use App\Api\V1\Transformers\JsonDateTransformer;

class MaterialReviewTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    protected $defaultIncludes = [
        'user'
    ];

    public function transform($materialReview)
    {
        $review_data = array();

        if ($materialReview)
        {
            $review_data['id'] = $materialReview['id'];
            $review_data['materialId'] = $materialReview['material_id'];
            $review_data['userId'] = $materialReview['user_id'];
            $review_data['title'] = $materialReview['title'];
            $review_data['description'] = $materialReview['description'];
            $review_data['rating'] = $materialReview['rating'];
            $review_data['createdAt'] = $this->jsonDate($materialReview['created_at']);
            $review_data['updatedAt'] = $this->jsonDate($materialReview['updated_at']);
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
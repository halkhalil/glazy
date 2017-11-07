<?php

namespace App\Api\V1\Transformers\MaterialReview;

use League\Fractal;

class MaterialReviewTransformer extends Fractal\TransformerAbstract
{
    public function transform($materialReview)
    {
        $review_data = array();

        $userTransformer = new ShallowUserTransformer();

        if ($materialReview)
        {
            $review_data['id'] = $materialReview['id'];
            $review_data['material_id'] = $materialReview['material_id'];
            $review_data['user_id'] = $materialReview['user_id'];
            if ($materialReview->user) {
                $review_data['user'] = $userTransformer->transform($materialReview->user);
            }
            $review_data['title'] = $materialReview['title'];
            $review_data['description'] = $materialReview['description'];
            $review_data['rating'] = $materialReview['rating'];
            $review_data['created_at'] = $materialReview['created_at'];
            $review_data['updated_at'] = $materialReview['updated_at'];
        }

        return $review_data;
    }
}
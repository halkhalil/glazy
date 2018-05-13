<?php

namespace App\Api\V1\Transformers\MaterialComment;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\MaterialComment;
use League\Fractal;
use App\Api\V1\Transformers\JsonDateTransformer;

class MaterialCommentTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    protected $defaultIncludes = [
        'user'
    ];

    public function transform($materialComment)
    {
        $comment_data = array();

        if ($materialComment)
        {
            $comment_data['id'] = $materialComment['id'];
            $comment_data['parentId'] = $materialComment['parent_id'];
            $comment_data['materialId'] = $materialComment['material_id'];
            $comment_data['userId'] = $materialComment['user_id'];
            $comment_data['content'] = $materialComment['content'];
            $comment_data['createdAt'] = $this->jsonDate($materialComment['created_at']);
            $comment_data['updatedAt'] = $this->jsonDate($materialComment['updated_at']);
        }

        return $comment_data;
    }

    public function includeUser(MaterialComment $materialComment)
    {
        if ($materialComment->user) {
            return $this->item($materialComment->user, new UserTransformer());
        }
    }

}
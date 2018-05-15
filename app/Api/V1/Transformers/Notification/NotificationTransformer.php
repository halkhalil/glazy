<?php

namespace App\Api\V1\Transformers\Notification;

use App\Api\V1\Transformers\JsonDateTransformer;

use Illuminate\Support\Facades\Notification;

use League\Fractal;

class NotificationTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [];

    protected $defaultIncludes = [];

    public function transform(Notification $notification)
    {
        $notification_data = [];
        $notification_data['type'] = $notification['type'];
        $notification_data['data'] = $notification['data'];
        $notification_data['title'] = $notification['title'];
        $notification_data['message'] = $notification['message'];
        $notification_data['link'] = $notification['link'];
        $notification_data['createdAt'] = $notification['created_at'];

        return $notification_data;
    }
}


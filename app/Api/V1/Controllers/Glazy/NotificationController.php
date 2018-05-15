<?php

namespace App\Api\V1\Controllers\Glazy;

use League\Fractal\Manager as FractalManager;
use App\Api\V1\Serializers\GlazySerializer;

use Auth;

class NotificationController extends ApiBaseController
{

    public function markAsRead()
    {
        $user = Auth::guard('api')->user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return $this->respondUpdated('Notifications marked as read');
    }

}

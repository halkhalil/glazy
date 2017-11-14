<?php

namespace App\Api\V1\Transformers;

use Carbon\Carbon;

/**
 * Class DateTransformer
 */
trait JsonDateTransformer {

    public function jsonDate($date) {
        return Carbon::parse($date)->format('Y-m-d\TH:i:s');
    }
}
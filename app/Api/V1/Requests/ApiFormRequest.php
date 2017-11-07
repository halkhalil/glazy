<?php

namespace App\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiFormRequest extends FormRequest
{
    public function wantsJson()
    {
        return true;
    }
}
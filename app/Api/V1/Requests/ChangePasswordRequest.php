<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'old_password' => 'required',
            'password' => 'required|min:3|confirmed',
            'password_confirmation' => 'required|min:3|same:password'
        ];
    }

    public function authorize()
    {
        return true;
    }
}

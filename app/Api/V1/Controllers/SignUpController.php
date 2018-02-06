<?php

namespace App\Api\V1\Controllers;

use App\Mail\UserRegistered;
use Config;
use App\User;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        $user = new User($request->all());
        if(!$user->save()) {
            throw new HttpException(500);
        }

        Mail::send(new UserRegistered($user));

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }
        $token = $JWTAuth->fromUser($user);

        /*
        return response()->json([
            'status' => 'ok',
            'token' => $token
        ], 201);
        */
        return response([
            'status' => 'success',
            //'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60,
            'data' => $user
        ])->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Authorization', 'Bearer '.$token);
    }
}

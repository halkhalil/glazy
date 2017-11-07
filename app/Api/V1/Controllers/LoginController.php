<?php

namespace App\Api\V1\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;

class LoginController extends Controller
{

    /**
     * DAU
     * @var JWTAuth
     */
    protected $jwt;

    /**
     * DAU
     * LoginController constructor.
     * @param JWTAuth $jwt
     * @param UserController $userController
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * DAU
     * @param $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        Log::info('MMMMMM Redirect to Provider: '.$provider);
        Log::info('MMMMMM GOOGLE: '.config('services.google.client_id'));
        Log::info('MMMMMM MAILGUN: '.config('services.mailgun.domain'));

        if ($provider !== 'google' && $provider !== 'facebook') {
            throw new AccessDeniedHttpException();
        }

        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    /**
     *
     * DAU
     *
     * @param $provider
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    /**
    also see https://isaacearl.com/blog/stateless-socialite
     **/
    public function handleProviderCallback(Request $request, $provider)
    {
        if (!$request->has('code')) {
            return response([
                'status' => 'success',
                'msg' => 'Successfully fetched token url.',
                'data' => [
                    'url' => Socialite::with($provider)->stateless()->redirect()->getTargetUrl()
                ]
            ], 200);
        }

        Log::info('MMMMMM Callback: '.$provider);

        Log::info('xxxxxxxx');

        $providerUser = Socialite::driver($provider)
            ->stateless()
            ->user();

        Log::info('yyyyyyyy');

        Log::info('MMMMMM Callback Provider Email: '.$providerUser->getEmail());
        Log::info('MMMMMM Callback Provider Email d: '.$providerUser->getEmail());
        Log::info('MMMMMM Callback Provider Name d: '.$providerUser->getName());

        $user = User::query()->firstOrNew(['email' => $providerUser->getEmail()]);

        if(!$user->exists) {

            Log::info('MMMMMM Callback Provider USER NO EXIST');

            /**
            $table->string('provider')->nullable();
            $table->string('provider_id',60)->unique()->nullable();
            $table->string('avatar_url')->nullable();
             */
            $user->email = $providerUser->getEmail();
            $user->name = $providerUser->getName();

            // All Providers
            //$user->getId();
            //$user->getNickname();
            //$user->getAvatar();

            $user->remember_token = str_random(10);
            $user->save();

            Log::info('MMMMMM Callback Created User: '.$user->name);
        }

        if ( ! $token = $this->jwt->fromUser($user)) {
            throw new AuthorizationException;
        }

        Log::info('MMMMMM Callback Created JWT TOKEN: '.$token);

        //return redirect('/search');
        //return response()->json(compact('token'));

        return response([
            'status' => 'success',
            'msg' => 'Successfully logged in via ' . $provider . '.'
        ])->header('Authorization', $token);
    }


    /**
     * Log the user in
     *
     * @param LoginRequest $request
     * @param JWTAuth $JWTAuth
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = Auth::guard()->attempt($credentials);

            if(!$token) {
                throw new AccessDeniedHttpException();
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response()
            ->json([
                'status' => 'success',
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60,
                'data' => Auth::guard()->user()
            ]);
    }
}

<?php

namespace App\Api\V1\Controllers;

use App\User;
use App\Models\UserProfile;
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
    public function loginSocial(Request $request, $provider)
    {
        if ($provider !== 'facebook' && $provider !== 'google') {
            // Only facebook & google supported
            if (!$request->has('code')) {
                return response([
                    'status' => 'error',
                    'msg' => 'Only Facebook & Google login supported',
                ], 401);
            }
        }

        if (!$request->has('code')) {
            return response([
                'status' => 'success',
                'msg' => 'Successfully fetched token url.',
                'data' => [
                    'url' => Socialite::with($provider)->stateless()->redirect()->getTargetUrl()
                ]
            ], 200);
        }

        $providerUser = Socialite::driver($provider)
            ->stateless()
            ->user();

        $user = User::query()->firstOrNew(['email' => $providerUser->getEmail()]);

        if($user->exists) {
            // User with this email already exists
            // Check if various profile fields already set
            $userProfile = UserProfile::query()->firstOrNew(['user_id' => $user->id]);
            if ($userProfile) {
                $userProfile->user_id = $user->id;
                if ($providerUser->getAvatar()) {
                    $userProfile[$provider.'_avatar'] =
                        $this->getOriginalAvatar($provider, $providerUser->getAvatar());
                }
                if ($providerUser->getId()) {
                    $userProfile[$provider.'_id'] = $providerUser->getId();
                }
                $userProfile->save();
            }
        } else {
            // This is a "new" user according to Glazy
            // (This person might already have an account,
            // but there's no way to tell if email is different.)
            $user->email = $providerUser->getEmail();
            $user->name = $providerUser->getName();

            $user->remember_token = str_random(10);

            $user->save();

            $userProfile = UserProfile::query()->firstOrNew(['user_id' => $user->id]);
            Log::info('trying create new user profile: id: '.$user->id.' avatar:'.$providerUser->getAvatar());

            if ($userProfile) {
                $userProfile->user_id = $user->id;
                if ($providerUser->getAvatar()) {
                    $userProfile[$provider.'_avatar'] =
                        $this->getOriginalAvatar($provider, $providerUser->getAvatar());
                }
                if ($providerUser->getId()) {
                    $userProfile[$provider.'_id'] = $providerUser->getId();
                }
                $userProfile->save();
            }

            Log::info('MMMMMM Callback Created User: '.$user->name);
        }

        if ( ! $token = $this->jwt->fromUser($user)) {
            throw new AuthorizationException;
        }

        Log::info('MMMMMM Callback Created JWT TOKEN: '.$token);

        return response([
            'status' => 'success',
            'msg' => 'Successfully logged in via ' . $provider . '.'
        ])->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Authorization', 'Bearer '.$token);
    }

    protected function getOriginalAvatar($provider, $avatar) {
        if ($provider === 'google') {
            return str_replace('?sz=50', '', $avatar);
        } elseif ($provider === 'facebook') {
            return str_replace('?type=normal', '?type=large', $avatar);
        }
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

        $user = Auth::guard()->user();
        /*
        $user->load(['collections' => function ($q) {
            $q->orderBy('name', 'asc');
        }]);
        $user->load('profile');
        */
        $user->load(['collections' => function ($q) {
            $q->orderBy('name', 'asc');
        }])->load('user_materials')->load('profile');

        return response([
                'status' => 'success',
                'expires_in' => Auth::guard()->factory()->getTTL() * 60,
                'data' => $user
        ])->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Authorization', 'Bearer '.$token);
    }
}

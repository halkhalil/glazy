<?php

namespace App\Api\V1\Controllers;

use App\User;
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
    public function handleProviderCallback($provider)
    {
        try {

            /**
             * get user infos with callback provider token
             */

            $user = Socialite::driver($provider)
                ->stateless()
                ->user();

            /**
             * check if user email exists in database
             */
            $existsUser = User::where('email',$user->email)->first();

            if(!$existsUser) {

                /**
                 * create user array infos to save in database
                 */
                /**
                $table->string('provider')->nullable();
                $table->string('provider_id',60)->unique()->nullable();
                $table->string('avatar_url')->nullable();
                 */
                $userInfos = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => null,
                    'remember_token' => str_random(10),
                    // TODO 'provider' => $provider,
                    // TODO 'provider_id' => $user->id,
                    // TODO 'avatar_url' => $user->avatar
                ];

                /**
                 * generate a personal token access from this new user
                 */

                //$token = $this->userController->createUserFromProvider($userInfos);

                try {

                    $user = User::create($infos);
                    $token = $this->jwt->fromUser($user);
                    return response()->json(compact('token'));

                } catch (\Exception $ex) {

                    return response($ex->getMessage(),500);

                }

            } else {

                /**
                 * search existent user in database and generate your personal token access
                 */
                // $existsUser = User::where('email',$user->email)->first();
                $token = $this->jwt->fromUser($existsUser);

            }

            return response()->json(compact('token'));
        } catch (\Exception $ex) {
            return response($ex->getMessage(),500);
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

        return response()
            ->json([
                'status' => 'success',
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60,
                'data' => Auth::guard()->user()
            ]);
    }
}

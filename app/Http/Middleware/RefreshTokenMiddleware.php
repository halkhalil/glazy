<?php
/**
 * DAU:
 * https://github.com/tymondesigns/jwt-auth/issues/872
 */
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;
use Auth;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try
        {
            if (! $user = JWTAuth::parseToken()->authenticate() )
            {
                return response()->json([
                    'code'   => 101, // means auth error in the api,
                   'response' => null // nothing to show
                 ]);
            }
        }
        catch (TokenExpiredException $e)
        {
            // If the token is expired, then it will be refreshed and added to the headers
            try
            {
                $token = Auth::guard()->refresh();

                /**
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
                // $response->headers->set('Authorization', 'Bearer '.$refreshed);

                $newtoken = $this->auth->refresh(); // Get new token.
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                Cache::put($key, $newtoken, $expiresAt);


                $token = Auth::guard()->refresh();

                return response()->json([
                    'status' => 'ok',
                    'token' => $token,
                    'expires_in' => Auth::guard()->factory()->getTTL() * 60
                ]);
                 */
            }
            catch (JWTException $e)
            {
                return response()->json([
                    'code'   => 103, // means not refreshable
                   'response' => null // nothing to show
                 ]);
            }
        }
        catch (JWTException $e)
        {
            return response()->json([
                'code'   => 101, // means auth error in the api,
                   'response' => null // nothing to show
            ]);
        }

        // Login the user instance for global usage
        Auth::login($user, false);

        return  $next($request);
    }
}
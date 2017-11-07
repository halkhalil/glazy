<?php
/**
 * DAU:  Unneeded
 */
namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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

        //Intercepts OPTIONS requests
        if($request->isMethod('OPTIONS') || $request->isMethod('HEAD')) {
            $response = response('', 200);
        } else {
            // Pass the request to the next middleware
            $response = $next($request);
        }

        // Adds headers to the response
        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Credentials', 'true'); // DAU

        // Sends it
        return $response;
    }
}
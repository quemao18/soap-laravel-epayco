<?php namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;

class APIKeyMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (env('APP_ENV') === 'testing') {
            return $next($request);
        }

        $authenticated = false;
        
        if (!isset($_SERVER['http-x-site-api-key'])) {
            return response()->json(array('error' => 'This resource requires an API key.'), 403);
        }

        $key = $_SERVER['HTTP_X_SITE_API_KEY'];
        if ($key) {
            $key = ApiKey::where('key', '=', $key)->first();
            if ($key) {
                session(['api_key' => $key->key]);
                $authenticated = true;
            }
        }

        if (!$authenticated) {
            return response()->json(array('error' => 'Invalid API Key'), 403);
        }

        return $next($request);
    }

}

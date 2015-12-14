<?php namespace App\Http\Middleware;

use Closure;
use App\Models\Site;

class SiteMiddleware {

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

        if (!isset($_SERVER['HTTP_X_SITE_NAME'])) {
            return response()->json(array('error' => 'This resource requires a site name.'), 403);
        }

        $key = $_SERVER['HTTP_X_SITE_NAME'];
        if ($key) {
            $key = Site::where('url', '=', $key)->first();
            if ($key) {
                session(['site_id' => $key->id]);
                $authenticated = true;
            }
        }

        if (!$authenticated) {
            return response()->json(array('error' => 'Invalid Site'), 403);
        }

        return $next($request);
    }

}

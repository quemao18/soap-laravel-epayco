<?php 

namespace App\Http\Middleware;

use Closure;
use App\Models\Token;  

class AuthMiddleware {
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authenticated = false;
        if (!isset($_SERVER['HTTP_X_SITE_AUTH_TOKEN'])) {
            return response()->json(array('error' => 'This resource requires an authentication token.'), 403);
        }
        $token = $_SERVER['HTTP_X_SITE_AUTH_TOKEN'];
        if ($token) {
            $token = Token::where('token', '=', $token)->where('expires', '>', time())->first();
            if ($token) {
                if($token->{'user_id'} != 0) {
                    session(['user_id' => $token->{'user_id'}]);
                }
                $authenticated = true;
                $token->updateExpiration();
            }
        }
        if (!$authenticated) {
            return response()->json(array('error' => 'Invalid token'), 403);
        }
        return $next($request);
    }

}
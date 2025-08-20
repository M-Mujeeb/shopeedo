<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        // Let Laravel/Sanctum do the normal auth first
        $this->authenticate($request, $guards);
      
        // Global ban check
        $user = $request->user();
        if ($user && ($user->banned ?? false)) {
            return response()->json(['result' => false, 'message' => translate('User is banned'), 'user' => null], 401);
        }
        return $next($request);
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        return route('login');
    }
}

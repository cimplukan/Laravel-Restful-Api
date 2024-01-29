<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");
        $authenticate = true;

        /* Parameter token tidak ada */
        if (!$token) {
            $authenticate = false;
        }

        /* Check user */
        $user = User::where('token', $token)->first();
        if (!$user) {
            $authenticate = false;
        } else {
            /* Set login user */
            Auth::login($user);
        }

        /* Check Login */
        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => ["Unauthorized"]
                ]
            ])->setStatusCode(401);
        }
    }
}

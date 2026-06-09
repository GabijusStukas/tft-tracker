<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithJwt
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $cookieToken = $request->cookie('jwt_token')) {
            return $this->unauthenticatedResponse($request);
        }

        $request->headers->set('Authorization', 'Bearer '.$cookieToken);

        try {
            if (Auth::guard('api')->check()) {
                $user = Auth::guard('api')->user();

                if ($user) {
                    Auth::setUser($user);
                } else {
                    return $this->unauthenticatedResponse($request);
                }
            } else {
                return $this->unauthenticatedResponse($request);
            }
        } catch (Exception $e) {
            return $this->unauthenticatedResponse($request);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function unauthenticatedResponse(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->route('login');
    }
}

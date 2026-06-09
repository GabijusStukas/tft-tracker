<?php

namespace App\Http\Middleware;

use Closure;
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
        if (Auth::guard('api')->check()) {
            $this->setUser();
            return $next($request);
        }

        if (! $request->headers->get('Authorization')) {

            if (! $cookieToken = $request->cookie('jwt_token')) {
                return $this->unauthenticatedResponse($request);
            }

            $request->headers->set('Authorization', 'Bearer '.$cookieToken);
        }

        if (Auth::guard('api')->check()) {
            $this->setUser();
            return $next($request);
        }

        return $this->unauthenticatedResponse($request);
    }

    /**
     * @return void
     */
    private function setUser(): void
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            Auth::setUser($user);
        }
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

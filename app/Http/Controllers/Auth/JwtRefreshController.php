<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class JwtRefreshController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $token = Auth::guard('api')->refresh();
        $ttlMinutes = Auth::guard('api')->factory()->getTTL();

        return response()
            ->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $ttlMinutes * 60,
            ])
            ->cookie(
                'jwt_token',
                $token,
                $ttlMinutes,
                '/',
                null,
                request()->isSecure(),
                false,
                false,
                'lax'
            );
    }
}


<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        // Hash and validate token
        $hashedToken = hash('sha256', $token);
        $apiToken = ApiToken::where('token', $hashedToken)->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido'
            ], 401);
        }

        if ($apiToken->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Token expirado'
            ], 401);
        }

        if (!$apiToken->user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario desactivado'
            ], 401);
        }

        // Authenticate the user
        auth()->login($apiToken->user, false);

        // Update last used timestamp
        $apiToken->update(['last_used_at' => now()]);

        return $next($request);
    }
}

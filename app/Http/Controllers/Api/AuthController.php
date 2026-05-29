<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return authentication token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta ha sido desactivada.'],
            ]);
        }

        // Create and store API token
        $tokenValue = Str::random(80);
        $token = $user->apiTokens()->create([
            'token' => hash('sha256', $tokenValue),
            'name' => 'Login Token',
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $tokenValue,
            'user' => $this->formatUserData($user),
        ], 200);
    }

    /**
     * Get current authenticated user (requires token in header)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        // Find user by token
        $user = $this->validateToken($token);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario autenticado',
            'user' => $this->formatUserData($user)
        ], 200);
    }

    /**
     * Validate API token and return associated user
     *
     * @param string $token
     * @return User|null
     */
    private function validateToken(string $token): ?User
    {
        $hashedToken = hash('sha256', $token);
        $apiToken = ApiToken::where('token', $hashedToken)->first();

        if (!$apiToken || $apiToken->isExpired()) {
            return null;
        }

        // Update last used timestamp
        $apiToken->update(['last_used_at' => now()]);

        return $apiToken->user;
    }

    /**
     * Logout user (frontend handles token removal)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Frontend will remove the token from localStorage
        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso',
        ], 200);
    }

    /**
     * Format user data to return to frontend
     *
     * @param User $user
     * @return array
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames()->toArray(),
            'permissions' => $user->getPermissionNames()->toArray(),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }
}

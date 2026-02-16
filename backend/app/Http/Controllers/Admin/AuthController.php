<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Check IP restriction
        if ($user->ip_restriction_enabled && !empty($user->allowed_ips)) {
            if (!in_array($request->ip(), $user->allowed_ips, true)) {
                throw ValidationException::withMessages([
                    'email' => ['Access denied from your IP address.'],
                ]);
            }
        }

        // If 2FA is enabled, return partial response
        if ($user->two_factor_enabled) {
            $partialToken = encrypt($user->id . '|' . now()->addMinutes(5)->timestamp);

            return response()->json([
                'data' => [
                    'requires_2fa' => true,
                    'partial_token' => $partialToken,
                ],
            ]);
        }

        return $this->issueToken($user, $request);
    }

    public function verifyTwoFactor(Request $request): JsonResponse
    {
        $request->validate([
            'partial_token' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        try {
            $decrypted = decrypt($request->input('partial_token'));
            [$userId, $expiry] = explode('|', $decrypted);

            if (now()->timestamp > (int) $expiry) {
                return response()->json(['message' => 'Verification session expired.'], 422);
            }

            $user = User::findOrFail($userId);

            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $request->input('code'));

            if (!$valid) {
                return response()->json(['message' => 'Invalid verification code.'], 422);
            }

            return $this->issueToken($user, $request);
        } catch (\Throwable) {
            return response()->json(['message' => 'Invalid or expired token.'], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'two_factor_enabled' => $user->two_factor_enabled,
                'permissions' => $user->permissions ?? [],
                'created_at' => $user->created_at,
            ],
        ]);
    }

    private function issueToken(User $user, Request $request): JsonResponse
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $user->createToken('admin-api')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'permissions' => $user->permissions ?? [],
                ],
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json(['message' => '2FA is already enabled.'], 422);
        }

        $secret = $this->google2fa->generateSecretKey();

        $user->update(['two_factor_secret' => $secret]);

        $qrUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'CMS Admin'),
            $user->email,
            $secret
        );

        return response()->json([
            'data' => [
                'secret' => $secret,
                'qr_url' => $qrUrl,
            ],
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'Please enable 2FA first.'], 422);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->input('code'));

        if (!$valid) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        $user->update(['two_factor_enabled' => true]);

        return response()->json([
            'message' => '2FA has been enabled successfully.',
        ]);
    }

    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['message' => '2FA is not enabled.'], 422);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->input('code'));

        if (!$valid) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);

        return response()->json([
            'message' => '2FA has been disabled.',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('sites:id,name,domain')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 50));

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:landlord.users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', 'in:master,admin'],
            'is_active' => ['sometimes', 'boolean'],
            'ip_restriction_enabled' => ['sometimes', 'boolean'],
            'allowed_ips' => ['nullable', 'array'],
            'allowed_ips.*' => ['ip'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:sponsor_manage,redirect_manage,content_generate,analytics_view,site_manage,user_manage'],
            'site_ids' => ['nullable', 'array'],
            'site_ids.*' => ['integer', 'exists:landlord.sites,id'],
        ]);

        $siteIds = $validated['site_ids'] ?? [];
        unset($validated['site_ids']);

        $user = User::create($validated);

        if (!empty($siteIds)) {
            $user->sites()->sync($siteIds);
        }

        return response()->json([
            'data' => $user->load('sites:id,name,domain'),
            'message' => 'Admin created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('sites:id,name,domain')->findOrFail($id);

        return response()->json([
            'data' => $user,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:landlord.users,email,' . $user->id],
            'role' => ['sometimes', 'in:master,admin'],
            'is_active' => ['sometimes', 'boolean'],
            'ip_restriction_enabled' => ['sometimes', 'boolean'],
            'allowed_ips' => ['nullable', 'array'],
            'allowed_ips.*' => ['ip'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:sponsor_manage,redirect_manage,content_generate,analytics_view,site_manage,user_manage'],
            'site_ids' => ['nullable', 'array'],
            'site_ids.*' => ['integer', 'exists:landlord.sites,id'],
        ]);

        $siteIds = $validated['site_ids'] ?? null;
        unset($validated['site_ids']);

        $user->update($validated);

        if ($siteIds !== null) {
            $user->sites()->sync($siteIds);
        }

        return response()->json([
            'data' => $user->fresh()->load('sites:id,name,domain'),
            'message' => 'Admin updated successfully.',
        ]);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete yourself.'], 422);
        }

        $user->sites()->detach();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Admin deleted successfully.',
        ]);
    }

    public function updatePassword(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function resetTwoFactor(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);

        return response()->json([
            'message' => '2FA has been reset for this user.',
        ]);
    }
}

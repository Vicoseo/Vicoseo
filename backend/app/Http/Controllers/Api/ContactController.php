<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Rate limiting: 3 messages per hour per IP
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Çok fazla mesaj gönderdiniz. Lütfen daha sonra tekrar deneyin.',
                'retry_after' => $seconds,
            ], 429);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|in:Genel,Teknik,Bonus,Şikayet',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // Get site from tenant middleware context
        $site = $request->attributes->get('site');
        $siteId = $site->id ?? null;

        DB::connection('landlord')->table('contact_messages')->insert([
            'site_id' => $siteId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        RateLimiter::hit($key, 3600); // 1 hour window

        return response()->json([
            'message' => 'Mesajınız başarıyla gönderildi.',
        ], 201);
    }
}

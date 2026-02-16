<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageGenerationService
{
    /**
     * Generate a featured image for a blog post using DALL-E 3.
     */
    public function generateFeaturedImage(string $topic, string $brandName, ?string $imagePrompt = null): ?string
    {
        $apiKey = config('ai.openai.api_key');
        if (empty($apiKey)) {
            Log::warning('ImageGenerationService: OPENAI_API_KEY not configured, skipping image generation.');
            return null;
        }

        try {
            $prompt = $imagePrompt ?: $this->buildImagePrompt($topic, $brandName);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(120)->post('https://api.openai.com/v1/images/generations', [
                'model' => config('ai.dalle.model', 'dall-e-3'),
                'prompt' => $prompt,
                'n' => 1,
                'size' => config('ai.dalle.size', '1792x1024'),
                'quality' => config('ai.dalle.quality', 'standard'),
                'response_format' => 'url',
            ]);

            $response->throw();

            $imageUrl = $response->json('data.0.url');
            if (empty($imageUrl)) {
                Log::warning('ImageGenerationService: No image URL in DALL-E response.');
                return null;
            }

            return $this->downloadAndConvert($imageUrl, $brandName);
        } catch (\Throwable $e) {
            Log::error('ImageGenerationService: Failed to generate image.', [
                'error' => $e->getMessage(),
                'topic' => $topic,
            ]);
            return null;
        }
    }

    /**
     * Build a DALL-E prompt for a topic.
     */
    private function buildImagePrompt(string $topic, string $brandName): string
    {
        $cleanTopic = str_replace($brandName, 'online platform', $topic);

        return "Professional digital illustration for a blog article about: {$cleanTopic}. "
            . 'Modern, clean design with abstract geometric elements. '
            . 'Technology and digital theme. No text, no words, no letters, no watermarks. '
            . 'Vibrant colors with blue and purple gradient tones. '
            . 'Suitable as a blog featured image, landscape orientation.';
    }

    /**
     * Download image from URL and convert to WebP.
     */
    private function downloadAndConvert(string $imageUrl, string $brandName): ?string
    {
        try {
            $imageData = Http::timeout(60)->get($imageUrl)->body();

            if (empty($imageData)) {
                Log::warning('ImageGenerationService: Downloaded image is empty.');
                return null;
            }

            $slug = Str::slug($brandName);
            $filename = $slug . '-' . time() . '-' . Str::random(6) . '.webp';
            $directory = 'public/images/posts';
            $path = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::makeDirectory($directory);

            // Convert to WebP using GD
            $image = @imagecreatefromstring($imageData);
            if ($image === false) {
                // If GD can't process, save original as-is with png extension
                $filename = $slug . '-' . time() . '-' . Str::random(6) . '.png';
                $path = $directory . '/' . $filename;
                Storage::put($path, $imageData);

                return '/storage/images/posts/' . $filename;
            }

            // Save as WebP
            ob_start();
            imagewebp($image, null, 85);
            $webpData = ob_get_clean();
            imagedestroy($image);

            if (empty($webpData)) {
                Storage::put($path, $imageData);
            } else {
                Storage::put($path, $webpData);
            }

            return '/storage/images/posts/' . $filename;
        } catch (\Throwable $e) {
            Log::error('ImageGenerationService: Failed to download/convert image.', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,gif,webp,svg'],
            'directory' => ['sometimes', 'string', 'in:sponsors,logos,backgrounds,site-logos,earnings,promotions,hero-backgrounds'],
        ]);

        $file = $request->file('file');
        $directory = $request->input('directory', 'sponsors');
        $diskPath = "uploads/{$directory}";

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString();

        // Ensure directory exists (use public disk explicitly)
        Storage::disk('public')->makeDirectory($diskPath);

        if ($extension === 'gif') {
            // GIF stays as-is (animated)
            $finalName = $filename . '.gif';
            $file->storeAs($diskPath, $finalName, 'public');
        } elseif ($extension === 'svg') {
            // SVG stays as-is (vector)
            $finalName = $filename . '.svg';
            $file->storeAs($diskPath, $finalName, 'public');
        } else {
            // For JPG/PNG/WebP: save optimized original format + WebP version
            $image = Image::read($file->getContent());

            if ($image->width() > 1200 || $image->height() > 800) {
                $image->scaleDown(1200, 800);
            }

            // Determine original output format
            $origExt = in_array($extension, ['jpg', 'jpeg']) ? 'jpg' : $extension;
            $finalName = $filename . '.' . $origExt;
            $absDir = storage_path("app/public/{$diskPath}");
            $origPath = "{$absDir}/{$finalName}";

            // Save in original format (for broad compatibility)
            if (in_array($origExt, ['jpg', 'jpeg'])) {
                $image->toJpeg(85)->save($origPath);
            } elseif ($origExt === 'png') {
                $image->toPng()->save($origPath);
            } else {
                $image->toWebp(85)->save($origPath);
            }

            // Also save WebP version for modern browsers
            if ($origExt !== 'webp') {
                $webpPath = "{$absDir}/{$filename}.webp";
                $image->toWebp(85)->save($webpPath);
            }
        }

        $url = "/storage/{$diskPath}/{$finalName}";

        // Include WebP URL for <picture> fallback
        $webpUrl = null;
        if (!in_array($extension, ['gif', 'svg', 'webp'])) {
            $webpUrl = "/storage/{$diskPath}/{$filename}.webp";
        }

        return response()->json([
            'url' => $url,
            'webp_url' => $webpUrl,
            'filename' => $finalName,
        ]);
    }
}

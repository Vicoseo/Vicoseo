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
            'directory' => ['sometimes', 'string', 'in:sponsors,logos,backgrounds'],
        ]);

        $file = $request->file('file');
        $directory = $request->input('directory', 'sponsors');
        $storagePath = "public/uploads/{$directory}";

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString();

        // Ensure directory exists
        Storage::makeDirectory($storagePath);

        if ($extension === 'gif') {
            // GIF stays as-is (animated)
            $finalName = $filename . '.gif';
            $file->storeAs($storagePath, $finalName);
        } elseif ($extension === 'svg') {
            // SVG stays as-is (vector)
            $finalName = $filename . '.svg';
            $file->storeAs($storagePath, $finalName);
        } else {
            // For JPG/PNG/WebP: save optimized original format + WebP version
            $image = Image::read($file->getRealPath());

            if ($image->width() > 1200 || $image->height() > 800) {
                $image->scaleDown(1200, 800);
            }

            // Determine original output format
            $origExt = in_array($extension, ['jpg', 'jpeg']) ? 'jpg' : $extension;
            $finalName = $filename . '.' . $origExt;
            $origPath = storage_path("app/{$storagePath}/{$finalName}");

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
                $webpPath = storage_path("app/{$storagePath}/{$filename}.webp");
                $image->toWebp(85)->save($webpPath);
            }
        }

        $url = "/storage/uploads/{$directory}/{$finalName}";

        // Include WebP URL for <picture> fallback
        $webpUrl = null;
        if (!in_array($extension, ['gif', 'svg', 'webp'])) {
            $webpUrl = "/storage/uploads/{$directory}/{$filename}.webp";
        }

        return response()->json([
            'url' => $url,
            'webp_url' => $webpUrl,
            'filename' => $finalName,
        ]);
    }
}

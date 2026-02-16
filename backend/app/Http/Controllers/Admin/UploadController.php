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

        // GIF stays as-is, others convert to WebP
        if ($extension === 'gif') {
            $finalName = $filename . '.gif';
            $file->storeAs($storagePath, $finalName);
        } elseif ($extension === 'svg') {
            $finalName = $filename . '.svg';
            $file->storeAs($storagePath, $finalName);
        } else {
            $finalName = $filename . '.webp';
            $fullPath = storage_path("app/{$storagePath}/{$finalName}");

            // Ensure directory exists
            Storage::makeDirectory($storagePath);

            // Resize if over 1200x800 and convert to WebP
            $image = Image::read($file->getRealPath());
            $width = $image->width();
            $height = $image->height();

            if ($width > 1200 || $height > 800) {
                $image->scaleDown(1200, 800);
            }

            $image->toWebp(85)->save($fullPath);
        }

        $url = "/storage/uploads/{$directory}/{$finalName}";

        return response()->json([
            'url' => $url,
            'filename' => $finalName,
        ]);
    }
}

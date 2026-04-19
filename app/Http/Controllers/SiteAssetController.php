<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiteAssetController extends Controller
{
    public function show(string $type): BinaryFileResponse
    {
        $settingKey = match ($type) {
            'logo' => 'site_logo',
            'favicon' => 'site_favicon',
            'og-image' => 'og_image',
            default => null,
        };

        if ($settingKey === null) {
            abort(404);
        }

        $path = Setting::get($settingKey);
        if (! $path) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        $absolutePath = $disk->path($path);
        $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

        try {
            return response()->file($absolutePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        } catch (FileNotFoundException) {
            abort(404);
        }
    }
}

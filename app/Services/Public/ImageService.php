<?php

namespace App\Services\Public;

use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Memoized existence checks per request to avoid N× Storage::exists I/O.
     *
     * @var array<string, bool>
     */
    private array $existsCache = [];

    /**
     * Resolve public image URL preferring thumb (400w) if exists, for card performance.
     */
    public function url(?string $path, string $fallback = 'assets/images/hero/hero-1.webp'): string
    {
        if (! $path) {
            return asset($fallback);
        }

        $thumb = ImageOptimizer::thumbPath($path);

        if ($thumb !== $path && $this->exists($thumb)) {
            return asset('storage/'.$thumb);
        }

        return asset('storage/'.$path);
    }

    public function srcSet(?string $path): ?string
    {
        if (! $path || ! $this->exists($path)) {
            return null;
        }

        $thumb = ImageOptimizer::thumbPath($path);
        $hasThumb = $thumb !== $path && $this->exists($thumb);

        $mainUrl = asset('storage/'.$path);

        if ($hasThumb) {
            $thumbUrl = asset('storage/'.$thumb);

            return $thumbUrl.' 400w, '.$mainUrl.' 800w';
        }

        return $mainUrl.' 800w';
    }

    private function exists(string $path): bool
    {
        if (array_key_exists($path, $this->existsCache)) {
            return $this->existsCache[$path];
        }

        return $this->existsCache[$path] = Storage::disk('public')->exists($path);
    }
}

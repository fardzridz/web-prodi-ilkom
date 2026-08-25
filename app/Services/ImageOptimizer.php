<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

class ImageOptimizer
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new GdDriver);
    }

    /**
     * Optimize an uploaded image to webp plus a 400w thumbnail.
     *
     * Animated GIFs are stored as-is to preserve animation. Any decode or write
     * failure is surfaced as a RuntimeException after cleaning up partial files,
     * so callers never persist a path pointing at missing storage.
     *
     * @return array{path: string, thumb: string|null}
     *
     * @throws RuntimeException
     */
    public function optimize(UploadedFile $file, string $directory, int $maxWidth = 1280, int $thumbWidth = 400, int $quality = 80): array
    {
        $directory = trim($directory, '/');

        if (strtolower((string) $file->getMimeType()) === 'image/gif') {
            return $this->storeAnimatedGif($file, $directory);
        }

        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: Str::random(8);
        $path = $directory.'/'.$basename.'-'.Str::random(6).'.webp';
        $thumbPath = $directory.'/'.$basename.'-'.Str::random(6).'-thumb.webp';

        $written = [];

        try {
            $image = $this->manager->decode($file->getRealPath());

            $this->write($path, (string) (clone $image)->scaleDown(width: $maxWidth)->encode(new WebpEncoder(quality: $quality)));
            $written[] = $path;

            $this->write($thumbPath, (string) $image->scaleDown(width: $thumbWidth)->encode(new WebpEncoder(quality: $quality)));
            $written[] = $thumbPath;
        } catch (Throwable $exception) {
            $this->delete($written);

            throw new RuntimeException(
                'Gagal memproses gambar yang diunggah: '.$exception->getMessage(),
                previous: $exception,
            );
        }

        return ['path' => $path, 'thumb' => $thumbPath];
    }

    /**
     * Delete given paths from the public disk.
     *
     * @param  array<int, string|null>  $paths
     */
    public function delete(array $paths): void
    {
        $paths = array_values(array_unique(array_filter($paths)));

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }

    /**
     * Resolve the thumbnail path for a stored webp path by convention.
     */
    public static function thumbPath(string $webpPath): string
    {
        return preg_replace('/\.webp$/i', '-thumb.webp', $webpPath) ?? $webpPath;
    }

    /**
     * @return array{path: string, thumb: string|null}
     *
     * @throws RuntimeException
     */
    private function storeAnimatedGif(UploadedFile $file, string $directory): array
    {
        $path = $file->storeAs($directory, Str::uuid().'.gif', 'public');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Gagal menyimpan berkas GIF ke penyimpanan publik.');
        }

        return ['path' => $path, 'thumb' => null];
    }

    /**
     * The public disk is configured with `throw => false`, so a failed write
     * returns false instead of raising. Convert that into an exception.
     *
     * @throws RuntimeException
     */
    private function write(string $path, string $contents): void
    {
        if (Storage::disk('public')->put($path, $contents) === false) {
            throw new RuntimeException("Gagal menulis berkas gambar ke penyimpanan publik: {$path}");
        }
    }
}

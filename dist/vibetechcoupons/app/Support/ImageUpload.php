<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageUpload
{
    public function store(UploadedFile $file, string $group, string $label, string $errorField): string
    {
        if (! in_array($group, ['stores', 'reviews'], true)) {
            throw new \InvalidArgumentException('Invalid image group.');
        }
        if (config('media.driver') === 'local') {
            return $this->storeLocalWebp($file, $group, $errorField);
        }

        $cloud = (string) config('media.cloudinary.cloud_name');
        if (config('media.driver') !== 'cloudinary' || ! preg_match('/^[a-zA-Z0-9_-]{1,64}$/D', $cloud)
            || ! filled(config('media.cloudinary.api_key')) || ! filled(config('media.cloudinary.api_secret'))) {
            throw ValidationException::withMessages([$errorField => 'Cloudinary is not configured. Please ask the site administrator to connect the image storage account.']);
        }

        $slug = substr(Str::slug($label), 0, 50) ?: 'image';
        $publicId = 'couponhub/'.$group.'/'.$slug.'-'.Str::lower(Str::random(12));
        $size = $group === 'stores' ? 512 : 1600;
        $stream = fopen($file->getRealPath(), 'rb');
        try {
            $response = Http::withBasicAuth(config('media.cloudinary.api_key'), config('media.cloudinary.api_secret'))
                ->connectTimeout(5)->timeout(45)->attach('file', $stream, $file->getClientOriginalName())
                ->post('https://api.cloudinary.com/v1_1/'.$cloud.'/image/upload', [
                    'public_id' => $publicId,
                    'overwrite' => 'false',
                    'format' => 'webp',
                    'transformation' => 'c_limit,w_'.$size.',h_'.$size.',q_auto',
                ]);
            $version = $response->json('version');
            if (! $response->successful() || $response->json('public_id') !== $publicId
                || $response->json('format') !== 'webp' || ! is_numeric($version) || (int) $version < 1) {
                throw new \RuntimeException('Upload failed.');
            }

            return 'https://res.cloudinary.com/'.$cloud.'/image/upload/v'.(int) $version.'/'.$publicId.'.webp';
        } catch (\Throwable) {
            // Do not expose credentials or provider response bodies in validation errors.
            throw ValidationException::withMessages([$errorField => 'Cloudinary could not save the image. Check the connection and account quota, then try again.']);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    private function storeLocalWebp(UploadedFile $file, string $group, string $errorField): string
    {
        $source = $output = null;
        $bufferLevel = ob_get_level();
        try {
            $source = imagecreatefromstring(file_get_contents($file->getRealPath()));
            if (! $source) {
                throw new \RuntimeException('Invalid image.');
            }
            if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
                $orientation = (@exif_read_data($file->getRealPath()) ?: [])['Orientation'] ?? 1;
                if (in_array($orientation, [2, 4, 5, 7], true)) {
                    imageflip($source, IMG_FLIP_HORIZONTAL);
                }
                $angle = match ($orientation) {
                    3, 4 => 180, 5, 6 => -90, 7, 8 => 90, default => 0
                };
                if ($angle) {
                    $rotated = imagerotate($source, $angle, 0);
                    imagedestroy($source);
                    $source = $rotated;
                }
            }
            $limit = $group === 'stores' ? 512 : 1600;
            $scale = min(1, $limit / max(imagesx($source), imagesy($source)));
            $width = max(1, (int) round(imagesx($source) * $scale));
            $height = max(1, (int) round(imagesy($source) * $scale));
            $output = imagecreatetruecolor($width, $height);
            imagealphablending($output, false);
            imagesavealpha($output, true);
            imagecopyresampled($output, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));
            ob_start();
            $encoded = imagewebp($output, null, 82);
            $bytes = ob_get_clean();
            $path = 'uploads/'.$group.'/'.Str::random(40).'.webp';
            if (! $encoded || ! $bytes || ! Storage::disk('public')->put($path, $bytes)) {
                throw new \RuntimeException('Could not save WebP.');
            }

            return $path;
        } catch (\Throwable) {
            throw ValidationException::withMessages([$errorField => 'The image could not be optimized and saved. Please check that GD with WebP support is enabled and try again.']);
        } finally {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }
            if ($source instanceof \GdImage) {
                imagedestroy($source);
            }
            if ($output instanceof \GdImage) {
                imagedestroy($output);
            }
        }
    }

    public function discardNewUpload(string $value): void
    {
        // Only called for a newly uploaded file when saving its database record fails.
        if (str_starts_with($value, 'uploads/')) {
            Storage::disk('public')->delete($value);

            return;
        }
        $cloud = (string) config('media.cloudinary.cloud_name');
        if (! $cloud || ! preg_match('~^https://res\.cloudinary\.com/'.preg_quote($cloud, '~').'/image/upload/v[0-9]+/(couponhub/(?:stores|reviews)/[a-z0-9-]+)\.webp$~D', $value, $matches)) {
            return;
        }
        try {
            $response = Http::withBasicAuth(config('media.cloudinary.api_key'), config('media.cloudinary.api_secret'))
                ->asForm()->connectTimeout(5)->timeout(15)
                ->post('https://api.cloudinary.com/v1_1/'.$cloud.'/image/destroy', ['public_id' => $matches[1], 'invalidate' => 'true']);
            if (! $response->successful()) {
                Log::warning('Could not clean up a new Cloudinary image after a failed database save.');
            }
        } catch (\Throwable) {
            Log::warning('Could not clean up a new Cloudinary image after a failed database save.');
        }
    }
}

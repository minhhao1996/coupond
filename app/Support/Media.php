<?php

namespace App\Support;

class Media
{
    public static function url(?string $value, int $width = 1200): ?string
    {
        if (! $value) {
            return null;
        }
        if (preg_match('~^uploads/(stores|reviews)/[a-zA-Z0-9]+\.(jpg|jpeg|png|webp)$~', $value)) {
            return url('/storage/'.$value);
        }

        if (! filter_var($value, FILTER_VALIDATE_URL) || ! in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'])) {
            return null;
        }
        $parts = self::cloudinaryParts($value);
        if ($parts) {
            $width = max(1, min(1600, $width));

            return $parts[1].'f_webp,q_auto,c_limit,w_'.$width.'/'.$parts[2];
        }

        return $value;
    }

    public static function srcset(?string $value): ?string
    {
        if (! $value || ! self::cloudinaryParts($value)) {
            return null;
        }

        return implode(', ', array_map(fn ($width) => self::url($value, $width).' '.$width.'w', [480, 720, 1200, 1600]));
    }

    private static function cloudinaryParts(string $value): ?array
    {
        $cloud = config('media.cloudinary.cloud_name');
        if (! is_string($cloud) || $cloud === '') {
            return null;
        }

        return preg_match('~^(https://res\.cloudinary\.com/'.preg_quote($cloud, '~').'/image/upload/)(v[0-9]+/[^?\s]+)$~D', $value, $parts) ? $parts : null;
    }
}

<?php

namespace App\Support;

use App\Models\AnalyticsEvent;
use MaxMind\Db\Reader;

class AnalyticsCountry
{
    public function lookup(?string $ip): array
    {
        if (! config('analytics.country_lookup_enabled') || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return [];
        }

        $path = config('analytics.country_database');
        if (! is_string($path) || ! is_readable($path)) {
            return [];
        }

        try {
            $reader = new Reader($path);
            try {
                $record = $reader->get($ip);
            } finally {
                $reader->close();
            }
            $code = $record['country']['iso_code'] ?? null;
            $name = $record['country']['names']['en'] ?? $code;

            return is_string($code) && preg_match('/^[A-Z]{2}$/D', $code) && is_string($name)
                ? ['country_code' => $code, 'country_name' => mb_substr($name, 0, 100)] : [];
        } catch (\Throwable $error) {
            report($error);

            return [];
        }
    }

    public function enrich(int $eventId, ?string $ip): void
    {
        try {
            $country = $this->lookup($ip);
            if ($country) {
                AnalyticsEvent::whereKey($eventId)->where('ip', $ip)->whereNull('country_code')->update($country);
            }
        } catch (\Throwable $error) {
            report($error);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AnalyticsEvent;
use App\Support\AnalyticsCountry;
use Illuminate\Console\Command;

class EnrichAnalyticsCountries extends Command
{
    protected $signature = 'analytics:enrich-countries {--limit=500 : Maximum events to process per run}';

    protected $description = 'Fill missing analytics countries for events with retained public IP addresses';

    public function handle(AnalyticsCountry $countries): int
    {
        $limit = max(1, min(5000, (int) $this->option('limit')));
        $events = AnalyticsEvent::whereNull('country_code')->whereNotNull('ip')->latest('id')->limit($limit)->get(['id', 'ip']);
        foreach ($events as $event) {
            $countries->enrich($event->id, $event->ip);
        }

        $this->info('Processed '.$events->count().' events. Unresolvable IPs remain Unknown.');

        return self::SUCCESS;
    }
}

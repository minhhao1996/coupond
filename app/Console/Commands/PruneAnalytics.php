<?php
namespace App\Console\Commands;
use App\Models\AnalyticsEvent;
use Illuminate\Console\Command;
class PruneAnalytics extends Command {
 protected $signature='analytics:prune';
 protected $description='Remove old analytics events and anonymize older IP addresses';
 public function handle(): int {
  AnalyticsEvent::where('created_at','<',now()->subDays(config('analytics.ip_retention_days')))->update(['ip'=>null,'user_agent'=>null]);
  $count=AnalyticsEvent::where('created_at','<',now()->subDays(config('analytics.retention_days')))->delete();
  $this->info("Removed {$count} expired analytics events."); return self::SUCCESS;
 }
}

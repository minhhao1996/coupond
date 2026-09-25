<?php
namespace App\Support;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Analytics {
 public static function record(Request $request, string $event, ?int $id, string $title, string $path, ?int $storeId = null): void {
  if (!config('analytics.enabled') || $request->user()?->is_admin) return;
  $agent = (string) $request->userAgent();
  if (preg_match('/bot|spider|crawler|headless|preview|facebookexternalhit/i', $agent)) return;
  $visitor = $request->session()->get('analytics_visitor');
  if (!$visitor) { $visitor=Str::uuid()->toString(); $request->session()->put('analytics_visitor',$visitor); }
  $visitor=hash_hmac('sha256',$visitor,config('app.key'));
  $bucket=(int) floor(now()->timestamp / ($event === 'coupon_copy' ? 10 : 30));
  $dedupe=hash('sha256',implode('|',[$visitor,$event,$id ?? $path,$bucket]));
  try {
   $record = AnalyticsEvent::firstOrCreate(['dedupe_key'=>$dedupe],[
    'event'=>$event,'subject_id'=>$id,'store_id'=>$storeId,'title'=>mb_substr($title,0,255),'path'=>mb_substr($path,0,1000),
    'visitor'=>$visitor,'ip'=>$request->ip(),'user_agent'=>mb_substr($agent,0,500),'created_at'=>now(),
   ]);
   if ($record->wasRecentlyCreated) {
    $eventId = $record->id;
    $ip = $record->ip;
    \Illuminate\Support\defer(fn () => app(AnalyticsCountry::class)->enrich($eventId, $ip));
   }
  } catch (\Throwable $error) { report($error); }
 }
}

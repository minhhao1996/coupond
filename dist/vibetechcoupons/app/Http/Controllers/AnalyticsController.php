<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Coupon;
use App\Models\Store;
use App\Support\Analytics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnalyticsController extends Controller
{
    public function copy(Request $request, Coupon $coupon)
    {
        abort_unless($coupon->is_active && $coupon->type === 'code' && filled($coupon->code), 404);
        Analytics::record($request, 'coupon_copy', $coupon->id, $coupon->title, route('stores.show', $coupon->store, false), $coupon->store_id);

        return response()->noContent();
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'event' => ['nullable', Rule::in(['page_view', 'review_view', 'coupon_copy'])],
            'ip' => ['nullable', 'ip'],
            'store_id' => ['nullable', 'integer', Rule::exists('stores', 'id')],
            'country' => ['nullable', 'string', 'regex:/^(?:[A-Z]{2}|unknown)$/D'],
        ]);
        $from = Carbon::parse($data['from'] ?? now()->subDays(29)->toDateString())->startOfDay();
        $to = Carbon::parse($data['to'] ?? now()->toDateString())->endOfDay();
        $base = AnalyticsEvent::whereBetween('created_at', [$from, $to])
            ->when($data['store_id'] ?? null, fn ($q, $store) => $q->where('store_id', $store))
            ->when($data['country'] ?? null, fn ($q, $country) => $country === 'unknown' ? $q->whereNull('country_code') : $q->where('country_code', $country));
        $stats = [
            'Page views' => (clone $base)->whereIn('event', ['page_view', 'review_view'])->count(),
            'Article views' => (clone $base)->where('event', 'review_view')->count(),
            'Successful copies' => (clone $base)->where('event', 'coupon_copy')->count(),
            'Visitor sessions' => (clone $base)->distinct()->count('visitor'),
        ];
        $top = fn ($event) => (clone $base)->where('event', $event)->select('subject_id')
            ->selectRaw('MAX(title) as title, COUNT(*) as total')->groupBy('subject_id')->orderByDesc('total')->limit(10)->get();
        $logs = (clone $base)->with('store:id,name')
            ->when($data['event'] ?? null, fn ($q, $event) => $q->where('event', $event))
            ->when($data['ip'] ?? null, fn ($q, $ip) => $q->where('ip', $ip))
            ->latest('id')->paginate(30)->withQueryString();
        $countries = AnalyticsEvent::whereNotNull('country_code')->select('country_code')
            ->selectRaw('MAX(country_name) as country_name')->groupBy('country_code')->orderBy('country_name')->get();

        return view('admin.analytics', compact('stats', 'logs', 'countries') + [
            'stores' => Store::orderBy('name')->get(['id', 'name']),
            'topReviews' => $top('review_view'),
            'topCoupons' => $top('coupon_copy'),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }
}

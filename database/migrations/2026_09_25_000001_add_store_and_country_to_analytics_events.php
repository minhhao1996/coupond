<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->index(['store_id', 'created_at']);
            $table->index(['country_code', 'created_at']);
        });

        // Recover store associations for events recorded before these fields existed.
        DB::table('analytics_events')->orderBy('id')->chunkById(500, function ($events) {
            $coupons = DB::table('coupons')->whereIn('id', $events->where('event', 'coupon_copy')->pluck('subject_id'))->pluck('store_id', 'id');
            $reviews = DB::table('reviews')->whereIn('id', $events->where('event', 'review_view')->pluck('subject_id'))->pluck('store_id', 'id');
            $stores = DB::table('stores')->whereIn('id', $events->where('event', 'page_view')->pluck('subject_id'))->pluck('slug', 'id');
            foreach ($events as $event) {
                $storeId = match ($event->event) {
                    'coupon_copy' => $coupons[$event->subject_id] ?? null,
                    'review_view' => $reviews[$event->subject_id] ?? null,
                    'page_view' => isset($stores[$event->subject_id]) && $event->path === '/store/'.$stores[$event->subject_id] ? $event->subject_id : null,
                    default => null,
                };
                if ($storeId) {
                    DB::table('analytics_events')->where('id', $event->id)->update(['store_id' => $storeId]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropIndex(['store_id', 'created_at']);
            $table->dropIndex(['country_code', 'created_at']);
            $table->dropColumn(['store_id', 'country_code', 'country_name']);
        });
    }
};

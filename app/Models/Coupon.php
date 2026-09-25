<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function getRouteKeyName(): string { return 'slug'; }
}

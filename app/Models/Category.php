<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function stores(): BelongsToMany { return $this->belongsToMany(Store::class); }
    public function coupons(): HasMany { return $this->hasMany(Coupon::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function getRouteKeyName(): string { return 'slug'; }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AnalyticsEvent extends Model {
 public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Store::class); }
 public $timestamps = false;
 protected $guarded = [];
 protected function casts(): array { return ['created_at'=>'datetime']; }
}

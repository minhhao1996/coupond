<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('analytics_events', function(Blueprint $table) {
  $table->id(); $table->string('event', 30); $table->unsignedBigInteger('subject_id')->nullable();
  $table->string('title'); $table->string('path', 1000); $table->char('visitor',64);
  $table->ipAddress('ip')->nullable(); $table->string('user_agent',500)->nullable();
  $table->char('dedupe_key',64)->unique(); $table->timestamp('created_at')->index();
  $table->index(['event','subject_id','created_at']); $table->index(['visitor','created_at']);
 }); }
 public function down(): void { Schema::dropIfExists('analytics_events'); }
};

<?php
namespace App\Http\Middleware;
use App\Support\Analytics;
use Closure;
use Illuminate\Http\Request;
class TrackPublicViews {
 public function handle(Request $request, Closure $next) {
  $response=$next($request);
  $name=$request->route()?->getName();
  if ($request->isMethod('GET') && $response->getStatusCode()===200 && in_array($name,['home','stores.index','stores.show','categories.index','categories.show','reviews.index','reviews.show'])) {
   $review=$request->route('review');
   $subject=$review ?? $request->route('store') ?? $request->route('category');
   Analytics::record($request,$review ? 'review_view' : 'page_view',$subject?->id,$subject?->title ?? $subject?->name ?? ($name === 'home' ? 'Home' : ucfirst(explode('.',$name)[0])), '/'.ltrim($request->path(),'/'), $review?->store_id ?? $request->route('store')?->id);
  }
  return $response;
 }
}

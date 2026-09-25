<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponRedirectController;
use App\Http\Controllers\EditorImageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::post('/media/images', EditorImageController::class)->middleware('throttle:30,1')->name('images.store');
    Route::get('/reviews/{id}/preview', [AdminController::class, 'previewReview'])->whereNumber('id')->name('reviews.preview');
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::view('/account', 'admin.account')->name('account');
    Route::put('/account/password', [AuthController::class, 'password'])->middleware('throttle:6,1')->name('password');
    Route::get('/{resource}', [AdminController::class, 'index'])->name('index');
    Route::get('/{resource}/create', [AdminController::class, 'create'])->name('create');
    Route::post('/{resource}', [AdminController::class, 'store'])->name('store');
    Route::get('/{resource}/{id}/edit', [AdminController::class, 'edit'])->whereNumber('id')->name('edit');
    Route::put('/{resource}/{id}', [AdminController::class, 'update'])->whereNumber('id')->name('update');
    Route::get('/{resource}/{id}/delete', [AdminController::class, 'confirmDelete'])->whereNumber('id')->name('confirm-delete');
    Route::delete('/{resource}/{id}', [AdminController::class, 'destroy'])->whereNumber('id')->name('destroy');
});

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemaps/{type}-{page}.xml', [SitemapController::class, 'pages'])->where('type', 'pages|stores|categories|reviews')->whereNumber('page')->name('sitemap.pages');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/search', SearchController::class)->name('search');
Route::get('/go/coupon/{coupon}', CouponRedirectController::class)->name('coupons.go');
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/store/{store}', [StoreController::class, 'show'])->name('stores.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');

Route::post('/events/coupon/{coupon:id}/copy', [\App\Http\Controllers\AnalyticsController::class, 'copy'])->middleware('throttle:60,1')->name('analytics.copy');

<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\LocaleController as AdminLocaleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SiteAssetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect root to default locale
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $locale = session('locale', config('app.locale'));
    if (!in_array($locale, ['en', 'de'], true)) {
        $locale = 'en';
    }
    return redirect()->route('home', ['locale' => $locale]);
})->name('root');

Route::get('/site-assets/{type}', [SiteAssetController::class, 'show'])
    ->whereIn('type', ['logo', 'favicon', 'og-image'])
    ->name('site-assets.show');

/*
|--------------------------------------------------------------------------
| Customer Routes (with locale prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('{locale}')
    ->where(['locale' => 'en|de'])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/menu', [MenuController::class, 'index'])->name('menu');
        Route::get('/offers', [OfferController::class, 'index'])->name('offers');
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/track', [OrderController::class, 'trackForm'])->name('orders.track');
        Route::get('/orders/track/{token}', [OrderController::class, 'track'])->name('orders.track.show');

        // API routes for products (used by product modal)
        Route::get('/api/products/{product}', [MenuController::class, 'show'])->name('api.products.show');

        // AI Chat routes
        Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware('admin.locale')->name('admin.')->group(function () {
    Route::post('/locale', [AdminLocaleController::class, 'update'])->name('locale');

    // Auth routes
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        
        // Categories
        Route::resource('categories', CategoryController::class);
        Route::post('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
        
        // Products
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

        // Offers (bundles)
        Route::resource('offers', AdminOfferController::class)->except(['show']);
        Route::post('/offers/{offer}/toggle', [AdminOfferController::class, 'toggle'])->name('offers.toggle');
        
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    });
});

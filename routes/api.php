<?php
use App\Http\Controllers\Api\PublicInventoryController;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/public/listings', [PublicInventoryController::class, 'listings']);
    Route::get('/public/listings/{slug}', [PublicInventoryController::class, 'listing']);

    Route::get('/public/developments', [PublicInventoryController::class, 'developments']);
    Route::get('/public/developments/{slug}', [PublicInventoryController::class, 'development']);

    Route::get('/public/development-units', [PublicInventoryController::class, 'developmentUnits']);
    Route::get('/public/development-units/{slug}', [PublicInventoryController::class, 'developmentUnit']);
});

Route::get('/public/manifest', [PublicInventoryController::class, 'manifest']);
Route::get('/public/sitemap', [PublicInventoryController::class, 'sitemap']);
Route::get('/public/ai-context', [PublicInventoryController::class, 'aiContext']);

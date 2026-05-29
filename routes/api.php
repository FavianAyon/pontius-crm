<?php
use App\Http\Controllers\Api\PublicInventoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadsController;
use App\Http\Controllers\PublicLeadController;

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/me', [AuthController::class, 'me']);

// Protected routes - require authentication
Route::middleware('api.token')->group(function () {
    // Leads routes - specific routes before parameter-based routes
    Route::get('/leads/my-leads', [LeadsController::class, 'myLeads']);
    Route::get('/leads/statistics', [LeadsController::class, 'statistics']);
    Route::get('/leads/available-agents', [LeadsController::class, 'getAvailableAgents']);

    // Leads main CRUD
    Route::get('/leads', [LeadsController::class, 'index']);
    Route::post('/leads', [LeadsController::class, 'store']);
    Route::get('/leads/{lead}', [LeadsController::class, 'show']);
    Route::put('/leads/{lead}', [LeadsController::class, 'update']);
    Route::delete('/leads/{lead}', [LeadsController::class, 'destroy']);

    // Lead activities routes
    Route::post('/leads/{lead}/activities', [LeadsController::class, 'storeActivity']);
    Route::get('/leads/{lead}/activities', [LeadsController::class, 'getActivities']);

    // Lead assignment routes
    Route::post('/leads/{lead}/assign', [LeadsController::class, 'reassign']);

    // Follow-ups routes
    Route::get('/leads/follow-ups', [LeadsController::class, 'getFollowUps']);
    Route::post('/leads/follow-ups/{followUpId}/complete', [LeadsController::class, 'completeFollowUp']);

    // Tasks routes
    Route::get('/leads/tasks', [LeadsController::class, 'getTasks']);
    Route::post('/leads/tasks/{taskId}/complete', [LeadsController::class, 'completeTask']);

    // Developments routes
    Route::get('/developments/statistics', [\App\Http\Controllers\Api\DevelopmentsController::class, 'statistics']);
    Route::get('/developments', [\App\Http\Controllers\Api\DevelopmentsController::class, 'index']);
    Route::post('/developments', [\App\Http\Controllers\Api\DevelopmentsController::class, 'store']);
    Route::get('/developments/{development}', [\App\Http\Controllers\Api\DevelopmentsController::class, 'show']);
    Route::put('/developments/{development}', [\App\Http\Controllers\Api\DevelopmentsController::class, 'update']);
    Route::delete('/developments/{development}', [\App\Http\Controllers\Api\DevelopmentsController::class, 'destroy']);

    // Listings routes
    Route::get('/listings/statistics', [\App\Http\Controllers\Api\ListingsController::class, 'statistics']);
    Route::get('/listings', [\App\Http\Controllers\Api\ListingsController::class, 'index']);
    Route::post('/listings', [\App\Http\Controllers\Api\ListingsController::class, 'store']);
    Route::get('/listings/{listing}', [\App\Http\Controllers\Api\ListingsController::class, 'show']);
    Route::put('/listings/{listing}', [\App\Http\Controllers\Api\ListingsController::class, 'update']);
    Route::delete('/listings/{listing}', [\App\Http\Controllers\Api\ListingsController::class, 'destroy']);
    Route::get('/developments/{development}/listings', [\App\Http\Controllers\Api\ListingsController::class, 'byDevelopment']);
});

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

// Public Developments and Listings (no authentication required)
Route::get('/public/developments', [\App\Http\Controllers\Api\DevelopmentsController::class, 'publicIndex']);
Route::get('/public/listings', [\App\Http\Controllers\Api\ListingsController::class, 'publicIndex']);

Route::post('/public/leads', [PublicLeadController::class, 'store']);

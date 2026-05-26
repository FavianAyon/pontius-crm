<?php

use App\Http\Controllers\PublicLeadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/lead-capture', [PublicLeadController::class, 'create'])->name('public.leads.create');

Route::post('/lead-capture', [PublicLeadController::class, 'store'])->name('public.leads.store');

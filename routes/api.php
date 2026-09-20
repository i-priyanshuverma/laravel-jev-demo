<?php

use App\Http\Controllers\TriageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])->group(function (): void {
    Route::prefix('triage')->group(function (): void {
        Route::post('/analyze', [TriageController::class, 'analyze']);
        Route::post('/sandbox', [TriageController::class, 'sandbox']);
        Route::get('/presets', [TriageController::class, 'presets']);
    });

    Route::post('/feedback/submit', [TriageController::class, 'submitFeedback']);
});

<?php

use App\Http\Controllers\TriageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])->name('api.')->group(function (): void {
    Route::prefix('triage')->name('triage.')->group(function (): void {
        Route::post('/analyze', [TriageController::class, 'analyze'])->name('analyze');
        Route::post('/sandbox', [TriageController::class, 'sandbox'])->name('sandbox');
        Route::get('/presets', [TriageController::class, 'presets'])->name('presets');
    });

    Route::post('/feedback/submit', [TriageController::class, 'submitFeedback'])->name('feedback.submit');
});

<?php
// routes/api.php

use App\Http\Controllers\Api\PartnerHandoffController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    
    // Partner handoff
    Route::post('/partner/find', [PartnerHandoffController::class, 'findPartner']);
    Route::post('/partner/outcome', [PartnerHandoffController::class, 'trackOutcome']);
    Route::get('/partners', [PartnerHandoffController::class, 'listPartners']);
    
});
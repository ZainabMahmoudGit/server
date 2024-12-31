<?php

use App\Http\Controllers\OTPController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


# Route::post('/send-otp', [OTPController::class, 'sendOtp']);

# Route::post('/verify-otp', [OTPController::class, 'verifyOtp']);




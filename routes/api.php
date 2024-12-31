<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;


Route::middleware('api')->group(function () {
    
  

  Route::post('/send-otp', [OTPController::class, 'sendOtp']);
  Route::post('/verify-otp', [OTPController::class, 'verifyOtp']);
  Route::post('/update-profile', [OTPController::class, 'updateProfile']);


  Route::get('/users/{id}', [OTPController::class, 'indexUser']);
  Route::put('/users/{id}', [OTPController::class, 'updateUser']);
  Route::delete('/users/{id}', [OTPController::class, 'deleteUser']);

});



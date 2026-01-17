<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AparmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/apartments/filterByCity', [AparmentController::class, 'filterByCity']);
Route::get('/apartments/filterByPrice', [AparmentController::class, 'filterByPrice']);
Route::get('/apartments/filterByFeatures', [AparmentController::class, 'filterByFeatures']);

Route::middleware('auth:sanctum')->group(function () {
    // جلب المحادثة بين مستخدمين
    Route::get('/messages/{receiver_id}', [MessageController::class, 'getMessages']);
    // إرسال رسالة جديدة
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
});
Route::middleware('auth:sanctum')->post('/apartments/rate', [ReviewController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {


    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::post('logout', [AuthController::class, 'logout']);


    Route::get('/apartments', [AparmentController::class, 'index']);


    Route::middleware('is_landlord')->group(function () {
        Route::post('/apartments', [AparmentController::class, 'store']);
        Route::put('/apartments/{id}', [AparmentController::class, 'update']);
        Route::delete('/apartments/{id}', [AparmentController::class, 'destroy']);
    });


    Route::middleware('admin')->group(function () {
        Route::get('/admin/pending-users', [AuthController::class, 'getPendingUsers']);
        Route::post('/admin/approve/{id}', [AuthController::class, 'approveUser']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/bookings', action: [BookingController::class, 'store']);
        Route::post('/bookings/{id}/cancel', action: [BookingController::class, 'cancelBooking']);
        Route::put(uri: '/bookings/{id}', action: [BookingController::class, 'updateBooking']);

        Route::post('/bookings/{id}/approve', [BookingController::class, 'approveBooking']);


        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    });

});

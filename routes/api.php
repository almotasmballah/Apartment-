<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AparmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes (المسارات العامة)
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/apartments/filterByCity', [AparmentController::class, 'filterByCity']);
Route::get('/apartments/filterByPrice', [AparmentController::class, 'filterByPrice']);
Route::get('/apartments/filterByFeatures', [AparmentController::class, 'filterByFeatures']);
/*
|--------------------------------------------------------------------------
| Protected Routes (المسارات المحمية لجميع المستخدمين)
|--------------------------------------------------------------------------
*/ 


Route::middleware('auth:sanctum')->post('/apartments/rate', [ReviewController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {

    // جلب بيانات المستخدم الحالي
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // تسجيل الخروج (للجميع)
    Route::post('logout', [AuthController::class, 'logout']);

    // الشقق (عرض فقط للجميع)
    Route::get('/apartments', [AparmentController::class, 'index']);

    /*
    |--- Landlord Routes (صلاحيات صاحب العقار) ---
    */
    Route::middleware('is_landlord')->group(function () {
        Route::post('/apartments', [AparmentController::class, 'store']);
        Route::put('/apartments/{id}', [AparmentController::class, 'update']);
        Route::delete('/apartments/{id}', [AparmentController::class, 'destroy']);
    });

    /*
     Admin Routes
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin/pending-users', [AuthController::class, 'getPendingUsers']);
        Route::post('/admin/approve/{id}', [AuthController::class, 'approveUser']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        // للمستأجر: إرسال طلب حجز
        Route::post('/bookings', action: [BookingController::class, 'store']);
        Route::post('/bookings/{id}/cancel', action: [BookingController::class, 'cancelBooking']);
        Route::put(uri: '/bookings/{id}', action: [BookingController::class, 'updateBooking']);

        // لصاحب الشقة: الموافقة على الحجز
        Route::post('/bookings/{id}/approve', [BookingController::class, 'approveBooking']);


        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    });

});

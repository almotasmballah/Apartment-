<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'phone' => 'required|numeric',
            'role' => 'required|in:landlord,tenant' 
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => $fields['role'],
            'phone' => $fields['phone'],
            'is_approved' => false, 
        ]);

        // $this->sendOtp($user); 

        return response()->json([
            'message' => 'تم التسجيل بنجاح. يرجى تأكيد الـ OTP وانتظار موافقة الإدارة.'
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required|numeric',
            'otp_code' => 'nullable' 
        ]);

        $user = User::where('email', $fields['email'])->first();
        // $user = User::where('phone', $fields['phone'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        if (!$user->is_approved) {
            return response()->json(['message' => 'حسابك بانتظار موافقة الإدارة'], 403);
        }

        if (!$request->has('otp_code') || empty($fields['otp_code'])) {
            $this->sendOtp($user);
            return response()->json([
                'message' => 'تم إرسال كود التحقق إلى إيميلك، يرجى إدخاله لإتمام الدخول',
                'requires_otp' => true
            ], 200);
        }

        if ($user->otp_code != $fields['otp_code'] || now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'كود OTP غير صالح أو منتهي'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update(['otp_code' => null, 'otp_expires_at' => null]);

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function getPendingUsers()
    {
        return User::where('is_approved', false)->get();
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);
        return response()->json(['message' => 'تمت الموافقة على المستخدم وتفعيل حسابه.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function resendOtp(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $this->sendOtp($user);

        return response()->json([
            'message' => 'تم إعادة إرسال كود جديد إلى بريدك الإلكتروني.'
        ], 200);
    }

    private function sendOtp($user)
    {
        $code = rand(100000, 999999);
        $user->update([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($code));
        } catch (\Exception $e) {
        }


    }
}

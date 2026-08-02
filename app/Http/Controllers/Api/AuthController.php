<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'patient',
        ]);

        // توليد OTP وتحديث حقول التحقق
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // إرسال OTP عبر البريد الإلكتروني
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message'  => 'Registration successful. OTP sent to your email.',
            'email'    => $user->email,
            'verified' => false,
        ], 201);
    }

    // تسجيل الدخول - توليد OTP وإرساله للتحقق
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // توليد OTP وإرساله للتحقق لكل عملية دخول
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message'  => 'OTP sent to your email. Please verify to complete login.',
            'email'    => $user->email,
            'verified' => false,
        ]);
    }

    // التحقق من OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // التحقق من انتهاء صلاحية OTP
        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired. Please login again.'], 422);
        }

        // التحقق من صحة OTP
        if (!Hash::check($request->otp, $user->otp_code)) {
            return response()->json(['message' => 'Invalid OTP code.'], 422);
        }

        // مسح OTP بعد التحقق وتفعيل الحساب
        $user->update([
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'email_verified_at' => Carbon::now(),
        ]);

        // إعطاء الـ token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'  => 'Login successful',
            'user'     => $user,
            'token'    => $token,
            'verified' => true,
        ]);
    }

    // إعادة إرسال OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // توليد OTP جديد
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message' => 'OTP resent successfully.',
            'email'   => $user->email,
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // بيانات المستخدم الحالي
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // إنشاء حساب staff (الأدمن فقط)
    public function createStaff(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:doctor,receptionist,admin',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => $request->role,
        ]);

        return response()->json([
            'message' => 'Staff account created successfully',
            'user'    => $user,
        ], 201);
    }

    // تحديث FCM Token
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'message' => 'FCM token updated successfully',
        ]);
    }

    // تحديث الملف الشخصي
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'phone'            => 'sometimes|nullable|string|max:20',
            'profile_picture'  => 'sometimes|nullable',
            'current_password' => 'required_with:password|string',
            'password'         => 'sometimes|string|min:6|confirmed',
        ];

        if ($user->role === 'patient' || $user->role === 'admin') {
            $rules['name'] = 'sometimes|string|max:255';
        }

        $request->validate($rules);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                ], 422);
            }
            $user->password = Hash::make($request->password);
        }

        if ($request->has('name') && ($user->role === 'patient' || $user->role === 'admin')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ]);
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        } elseif ($request->filled('profile_picture') && is_string($request->profile_picture)) {
            $user->profile_picture = $request->profile_picture;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh(),
        ]);
    }

    // نسيت كلمة السر - إرسال OTP
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message' => 'OTP for password reset sent to your email.',
            'email'   => $user->email,
        ]);
    }

    // التحقق من OTP لاستعادة كلمة السر
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired. Please request a new one.'], 422);
        }

        if (!Hash::check($request->otp, $user->otp_code)) {
            return response()->json(['message' => 'Invalid OTP code.'], 422);
        }

        return response()->json([
            'message' => 'OTP verified successfully. You may now reset your password.',
            'email'   => $user->email,
        ]);
    }

    // تعيين كلمة سر جديدة بعد التحقق من OTP
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired. Please request a new one.'], 422);
        }

        if (!Hash::check($request->otp, $user->otp_code)) {
            return response()->json(['message' => 'Invalid OTP code.'], 422);
        }

        $user->update([
            'password'       => Hash::make($request->password),
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        // إرسال OTP عبر البريد الإلكتروني وتسجيله محلياً للاختبار
        Log::info("OTP generated for {$user->email}: {$otp}");
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

        Log::info("OTP generated for {$user->email}: {$otp}");
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

        Log::info("OTP generated for {$user->email}: {$otp}");
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return response()->json([
            'message' => 'OTP resent successfully.',
            'email'   => $user->email,
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->update(['fcm_token' => null]);
            $user->currentAccessToken()->delete();
        }

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

    // قائمة الموظفين الاستقبال (الأدمن فقط)
    public function listReceptionists()
    {
        $receptionists = User::where('role', 'receptionist')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'email'               => $user->email,
                    'phone'               => $user->phone ?? '',
                    'profile_picture'     => $user->profile_picture,
                    'profile_picture_url' => $user->profile_picture_url,
                    'created_at'          => $user->created_at?->toDateString(),
                ];
            });

        return response()->json([
            'total'         => $receptionists->count(),
            'receptionists' => $receptionists,
        ]);
    }

    // حذف حساب موظف (الأدمن فقط)
    public function deleteStaff(Request $request, $id)
    {
        $staff = User::whereIn('role', ['receptionist', 'doctor'])->find($id);

        if (!$staff) {
            return response()->json(['message' => 'Staff member not found'], 404);
        }

        if ($staff->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 422);
        }

        $staff->delete();

        return response()->json(['message' => 'Staff account deleted successfully']);
    }

    // تحديث FCM Token واللغة المفضلة للإشعارات
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'locale'    => 'nullable|string|in:ar,en',
        ]);

        $user = $request->user();

        // 1. إزالة Token من أي حساب آخر على نفس الجهاز
        User::where('fcm_token', $request->fcm_token)
            ->where('id', '!=', $user->id)
            ->update(['fcm_token' => null]);

        $localeHeader = strtolower($request->header('Accept-Language', 'en'));
        $requestedLocale = $request->locale ?? (str_contains($localeHeader, 'ar') ? 'ar' : 'en');

        // 2. تحديث Token واللغة للمستخدم الحالي
        $user->update([
            'fcm_token' => $request->fcm_token,
            'locale'    => $requestedLocale,
        ]);

        return response()->json([
            'message' => 'FCM token and language preference updated successfully',
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

        Log::info("OTP generated for {$user->email}: {$otp}");
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

    // تسجيل مريض جديد من قبل الموظف أو الأدمن
    public function registerPatient(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'phone'          => 'nullable|string|max:20',
            'staff_override' => 'nullable|boolean',
        ]);

        $isOverride = $request->boolean('staff_override');

        if ($isOverride) {
            $patient = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'phone'             => $request->phone ?? '',
                'password'          => Hash::make($request->password),
                'role'              => 'patient',
                'email_verified_at' => now(),
            ]);

            AuditLog::create([
                'performed_by_id' => $request->user()->id,
                'target_user_id'  => $patient->id,
                'action'          => 'patient_register_staff_override',
                'old_value'       => null,
                'new_value'       => $patient->email,
                'notes'           => 'Staff override in-person identity verification during registration',
            ]);

            return response()->json([
                'message'      => 'Patient registered and verified successfully.',
                'requires_otp' => false,
                'patient'      => $patient,
            ], 201);
        }

        $patient = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone ?? '',
            'password'          => Hash::make($request->password),
            'role'              => 'patient',
            'email_verified_at' => null,
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $patient->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Log::info("Registration OTP generated for {$patient->email}: {$otp}");
        Mail::to($patient->email)->send(new OtpMail($otp, $patient->name));

        return response()->json([
            'message'      => 'Patient account created. OTP sent to patient email.',
            'requires_otp' => true,
            'patient_id'   => $patient->id,
            'email'        => $patient->email,
        ], 201);
    }

    // تعديل بيانات المريض من قبل الموظف أو الأدمن
    public function updatePatient(Request $request, $id)
    {
        $patient = User::where('role', 'patient')->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $id,
            'phone'           => 'nullable|string|max:20',
            'staff_override'  => 'nullable|boolean',
            'otp'             => 'nullable|string|size:6',
            'profile_picture' => 'sometimes|nullable',
        ]);

        $emailChanged = strtolower($request->email) !== strtolower($patient->email);

        if ($emailChanged) {
            $isOverride = $request->boolean('staff_override');
            $hasOtp = $request->filled('otp');

            if ($isOverride) {
                // Log Audit Trail for Staff Override
                AuditLog::create([
                    'performed_by_id' => $request->user()->id,
                    'target_user_id'  => $patient->id,
                    'action'          => 'patient_email_staff_override',
                    'old_value'       => $patient->email,
                    'new_value'       => $request->email,
                    'notes'           => 'Staff override in-person identity verification',
                ]);

                $patient->email = $request->email;
                $patient->email_verified_at = now();
            } elseif ($hasOtp) {
                if (!$patient->otp_expires_at || Carbon::now()->isAfter($patient->otp_expires_at) || !Hash::check($request->otp, $patient->otp_code)) {
                    return response()->json(['message' => 'Invalid or expired OTP code.'], 422);
                }

                $patient->email = $request->email;
                $patient->email_verified_at = now();
                $patient->otp_code = null;
            } else {
                // Send OTP to new email address
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $patient->update([
                    'otp_code'       => Hash::make($otp),
                    'otp_expires_at' => Carbon::now()->addMinutes(10),
                ]);

                Log::info("Email update OTP for patient {$patient->id} ({$request->email}): {$otp}");
                try {
                    Mail::to($request->email)->send(new OtpMail($otp, $request->name));
                } catch (\Exception $e) {
                    Log::error("Failed to send email update OTP: " . $e->getMessage());
                }

                return response()->json([
                    'requires_otp' => true,
                    'message'      => 'OTP sent to new email address',
                    'email'        => $request->email,
                ]);
            }
        }

        $patient->name  = $request->name;
        $patient->phone = $request->phone ?? '';

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ]);
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $patient->profile_picture = $path;
        }

        $patient->save();

        return response()->json([
            'message' => 'Patient details updated successfully',
            'patient' => $patient->fresh(),
        ]);
    }

    // تحديث صورة المريض من قبل الموظف أو الأدمن
    public function updatePatientProfilePicture(Request $request, $id)
    {
        $patient = User::where('role', 'patient')->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $patient->profile_picture = $path;
        $patient->save();

        return response()->json([
            'message'             => 'Profile picture updated successfully',
            'profile_picture_url' => $patient->fresh()->profile_picture_url,
        ]);
    }

    // تحديث صورة ملف تعريف الدكتور أو الموظف من قبل الأدمن
    public function updateUserProfilePicture(Request $request, $id)
    {
        $user = User::whereIn('role', ['doctor', 'receptionist'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->profile_picture = $path;
        $user->save();

        return response()->json([
            'message'             => 'Profile picture updated successfully',
            'profile_picture_url' => $user->fresh()->profile_picture_url,
        ]);
    }
}


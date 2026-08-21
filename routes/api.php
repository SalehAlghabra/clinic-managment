<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ReportController;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register',         [AuthController::class, 'register']);
    Route::post('/login',            [AuthController::class, 'login']);
    Route::post('/verify-otp',       [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp',       [AuthController::class, 'resendOtp']);
    Route::post('/forgot-password',  [AuthController::class, 'forgotPassword']);
    Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
    Route::post('/reset-password',   [AuthController::class, 'resetPassword']);
});

// Public routes (للجميع)
Route::get('/doctors',                      [DoctorController::class, 'index']);
Route::get('/doctors/{id}',                 [DoctorController::class, 'show']);
Route::get('/doctors/{doctorId}/schedules', [DoctorScheduleController::class, 'index']);
Route::get('/settings',                     [SettingController::class, 'index']);

// Serve public storage media with CORS headers for Flutter Web CanvasKit
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        return response()->json(['message' => 'File not found'], 404);
    }
    return response()->file($filePath, [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
        'Cross-Origin-Resource-Policy' => 'cross-origin',
    ]);
})->where('path', '.*');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',     [AuthController::class, 'logout']);
    Route::get('/auth/me',          [AuthController::class, 'me']);
    Route::match(['put', 'post'], '/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/fcm-token',  [AuthController::class, 'updateFcmToken']);
    Route::get('/doctors/{doctorId}/available-slots', [AppointmentController::class, 'availableSlots']);

    // Notifications (All authenticated users)
    Route::get('/notifications',      [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);

    // المريض، الأدمن، والموظف (حجز، إلغاء، وإعادة جدولة المواعيد)
    Route::middleware('role:patient,admin,receptionist')->group(function () {
        Route::post('/appointments',                  [AppointmentController::class, 'store']);
        Route::patch('/appointments/{id}/cancel',     [AppointmentController::class, 'cancel']);
        Route::patch('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule']);
    });

    // المريض فقط
    Route::middleware('role:patient')->group(function () {
        Route::post('/appointments/preview',          [AppointmentController::class, 'preview']);
        Route::get('/appointments/my',                [AppointmentController::class, 'patientAppointments']);
        Route::post('/appointments/{id}/pay-remaining',[AppointmentController::class, 'payRemaining']);
        Route::get('/medical-records/my',             [MedicalRecordController::class, 'patientRecords']);
        Route::get('/invoices/my',                    [InvoiceController::class, 'patientInvoices']);

        // Wallet
        Route::get('/wallet/balance',            [WalletController::class, 'balance']);
        Route::get('/wallet/transactions',       [WalletController::class, 'transactions']);
    });

    // الدكتور فقط
    Route::middleware('role:doctor')->group(function () {
        Route::get('/appointments/doctor',                          [AppointmentController::class, 'doctorAppointments']);
        Route::get('/doctor/patients/{patientId}/medical-records',   [MedicalRecordController::class, 'doctorPatientRecords']);
        Route::post('/medical-records',                             [MedicalRecordController::class, 'store']);
        Route::post('/medical-records/{id}/prescriptions',          [MedicalRecordController::class, 'addPrescription']);
        Route::patch('/appointments/cancel-day',                    [AppointmentController::class, 'cancelDayAppointments']);
        Route::patch('/appointments/{id}/status',                   [AppointmentController::class, 'updateStatus']);
    });

    // الأدمن، الدكتور، والمريض
    Route::middleware('role:admin,doctor,patient')->group(function () {
        Route::get('/medical-records/{id}',                [MedicalRecordController::class, 'show']);
    });

    // الأدمن والموظف (Receptionist)
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('/appointments',                        [AppointmentController::class, 'index']);
        Route::get('/invoices',                            [InvoiceController::class, 'index']);
        Route::post('/invoices',                           [InvoiceController::class, 'store']);
        Route::get('/invoices/{appointmentId}',            [InvoiceController::class, 'show']);
        Route::patch('/invoices/{id}/payment',             [InvoiceController::class, 'updatePayment']);
        Route::get('/reports/patients',                    [ReportController::class, 'patientsReport']);
        Route::post('/patients',                           [AuthController::class, 'registerPatient']);
        Route::put('/patients/{id}',                       [AuthController::class, 'updatePatient']);
        Route::post('/patients/{id}/profile-picture',      [AuthController::class, 'updatePatientProfilePicture']);
        Route::post('/wallet/deposit/{userId}',            [WalletController::class, 'deposit']);
        Route::post('/wallet/deduct/{userId}',             [WalletController::class, 'deduct']);
        Route::get('/wallet/transactions/{userId}',        [WalletController::class, 'patientTransactions']);
        Route::get('/reports/doctors',                     [ReportController::class, 'doctorsReport']);
        Route::get('/reports/financial-history',            [ReportController::class, 'financialHistory']);
    });

    // الأدمن فقط
    Route::middleware('role:admin')->group(function () {
        // Reports
        Route::get('/reports/dashboard',            [ReportController::class, 'dashboard']);
        Route::get('/reports/appointments',         [ReportController::class, 'appointmentsReport']);
        Route::get('/reports/revenue',              [ReportController::class, 'revenueReport']);
        Route::get('/reports/violations',           [ReportController::class, 'violationsReport']);
        Route::post('/auth/create-staff',          [AuthController::class, 'createStaff']);
        Route::patch('/settings',                  [SettingController::class, 'update']);

        // Staff management
        Route::get('/staff/receptionists',                  [AuthController::class, 'listReceptionists']);
        Route::put('/staff/{id}',                           [AuthController::class, 'updateStaff']);
        Route::delete('/staff/{id}',                        [AuthController::class, 'deleteStaff']);
        Route::post('/staff/{id}/profile-picture',          [AuthController::class, 'updateUserProfilePicture']);

        // Doctor
        Route::post('/doctors',                    [DoctorController::class, 'store']);
        Route::put('/doctors/{id}',                [DoctorController::class, 'update']);
        Route::delete('/doctors/{id}',             [DoctorController::class, 'destroy']);

        // Schedule
        Route::post('/doctors/{doctorId}/schedules',                [DoctorScheduleController::class, 'store']);
        Route::put('/doctors/{doctorId}/schedules/{scheduleId}',    [DoctorScheduleController::class, 'update']);
        Route::delete('/doctors/{doctorId}/schedules/{scheduleId}', [DoctorScheduleController::class, 'destroy']);
    });
});

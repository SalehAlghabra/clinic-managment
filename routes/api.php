<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ReportController;

// Public routes
// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register',   [AuthController::class, 'register']);
    Route::post('/login',      [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
});

// Public routes (للجميع)
Route::get('/doctors',                              [DoctorController::class, 'index']);
Route::get('/doctors/{id}',                         [DoctorController::class, 'show']);
Route::get('/doctors/{doctorId}/schedules',         [DoctorScheduleController::class, 'index']);
Route::get('/doctors/{doctorId}/services',          [ServiceController::class, 'index']);
Route::get('/settings',                             [SettingController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',     [AuthController::class, 'logout']);
    Route::get('/auth/me',          [AuthController::class, 'me']);
    Route::put('/auth/profile',     [AuthController::class, 'updateProfile']);
    Route::post('/auth/fcm-token',  [AuthController::class, 'updateFcmToken']);

    // المريض فقط
    Route::middleware('role:patient')->group(function () {
        Route::post('/appointments/preview', [AppointmentController::class, 'preview']);
        Route::post('/appointments',                       [AppointmentController::class, 'store']);
        Route::get('/appointments/my',                     [AppointmentController::class, 'patientAppointments']);
        Route::patch('/appointments/{id}/cancel',          [AppointmentController::class, 'cancel']);
        Route::get('/medical-records/my',                  [MedicalRecordController::class, 'patientRecords']);
        Route::get('/invoices/my',                         [InvoiceController::class, 'patientInvoices']);

        // Wallet
        Route::get('/wallet/balance',                      [WalletController::class, 'balance']);
        Route::get('/wallet/transactions',                 [WalletController::class, 'transactions']);
    });

    // الدكتور فقط
    Route::middleware('role:doctor')->group(function () {
        Route::get('/appointments/doctor',                 [AppointmentController::class, 'doctorAppointments']);
        Route::patch('/appointments/{id}/status',          [AppointmentController::class, 'updateStatus']);
        Route::post('/medical-records',                    [MedicalRecordController::class, 'store']);
        Route::post('/medical-records/{id}/prescriptions', [MedicalRecordController::class, 'addPrescription']);

        // إلغاء مواعيد يوم محدد
        Route::patch('/appointments/cancel-day',           [AppointmentController::class, 'cancelDayAppointments']);
    });

    // الأدمن والدكتور
    Route::middleware('role:admin,doctor')->group(function () {
        Route::get('/medical-records/{id}',                [MedicalRecordController::class, 'show']);
        Route::get('/doctors/{doctorId}/available-slots',  [AppointmentController::class, 'availableSlots']);
    });

    // الأدمن والموظف
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::post('/invoices',                           [InvoiceController::class, 'store']);
        Route::get('/invoices/{appointmentId}',            [InvoiceController::class, 'show']);
        Route::patch('/invoices/{id}/payment',             [InvoiceController::class, 'updatePayment']);
    });

    // الأدمن فقط
    Route::middleware('role:admin')->group(function () {
        // Reports
        Route::get('/reports/dashboard',     [ReportController::class, 'dashboard']);
        Route::get('/reports/appointments',  [ReportController::class, 'appointmentsReport']);
        Route::get('/reports/revenue',       [ReportController::class, 'revenueReport']);
        Route::get('/reports/doctors',       [ReportController::class, 'doctorsReport']);
        Route::get('/reports/violations',    [ReportController::class, 'violationsReport']);
        Route::post('/wallet/deposit/{userId}', [WalletController::class, 'deposit']);
        Route::post('/auth/create-staff',                           [AuthController::class, 'createStaff']);
        Route::get('/appointments',                                 [AppointmentController::class, 'index']);
        Route::get('/invoices',                                     [InvoiceController::class, 'index']);
        Route::patch('/settings',                                   [SettingController::class, 'update']);

        // Doctor
        Route::post('/doctors',                                     [DoctorController::class, 'store']);
        Route::put('/doctors/{id}',                                 [DoctorController::class, 'update']);
        Route::delete('/doctors/{id}',                              [DoctorController::class, 'destroy']);

        // Schedule
        Route::post('/doctors/{doctorId}/schedules',                [DoctorScheduleController::class, 'store']);
        Route::put('/doctors/{doctorId}/schedules/{scheduleId}',    [DoctorScheduleController::class, 'update']);
        Route::delete('/doctors/{doctorId}/schedules/{scheduleId}', [DoctorScheduleController::class, 'destroy']);

        // Services
        Route::post('/doctors/{doctorId}/services',                 [ServiceController::class, 'store']);
        Route::put('/doctors/{doctorId}/services/{serviceId}',      [ServiceController::class, 'update']);
        Route::delete('/doctors/{doctorId}/services/{serviceId}',   [ServiceController::class, 'destroy']);
    });
});

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorDetail;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Invoice;
use App\Models\WalletTransaction;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Disable FK constraints and clear tables
        Schema::disableForeignKeyConstraints();
        WalletTransaction::truncate();
        Invoice::truncate();
        Prescription::truncate();
        MedicalRecord::truncate();
        Appointment::truncate();
        Service::truncate();
        DoctorSchedule::truncate();
        DoctorDetail::truncate();
        User::truncate();
        Setting::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Seed Settings
        Setting::create(['key' => 'booking_deposit', 'value' => '50', 'description' => 'Deposit required for booking']);
        Setting::create(['key' => 'max_penalty_percentage', 'value' => '25', 'description' => 'Maximum penalty percentage for cancellation']);
        Setting::create(['key' => 'hours_before_cancellation', 'value' => '24', 'description' => 'Hours before appointment to cancel without penalty']);

        // 3. Seed Admin
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0999999991',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 4. Seed Receptionist
        $receptionist = User::create([
            'name' => 'Receptionist Staff',
            'email' => 'receptionist@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0999999992',
            'role' => 'receptionist',
            'email_verified_at' => now(),
        ]);

        // 5. Seed Doctors
        $doc1User = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor1@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0999999993',
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);
        $doc1 = DoctorDetail::create([
            'user_id' => $doc1User->id,
            'specialization' => 'Cardiology',
            'bio' => 'Experienced cardiologist specializing in heart diseases and heart failures.',
        ]);

        $doc2User = User::create([
            'name' => 'Dr. Clara Oswald',
            'email' => 'doctor2@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0999999994',
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);
        $doc2 = DoctorDetail::create([
            'user_id' => $doc2User->id,
            'specialization' => 'Dermatology',
            'bio' => 'Expert in skin care, acne treatments, and cosmetic dermatology.',
        ]);

        $doc3User = User::create([
            'name' => 'Dr. David Tennant',
            'email' => 'doctor3@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0999999995',
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);
        $doc3 = DoctorDetail::create([
            'user_id' => $doc3User->id,
            'specialization' => 'Pediatrics',
            'bio' => 'Dedicated pediatrician caring for newborn babies and kids.',
        ]);

        // 6. Seed Doctor Schedules
        // Dr 1: monday, wednesday, friday
        DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'duration_per_patient' => 20,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'day_of_week' => 'wednesday',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'duration_per_patient' => 20,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'day_of_week' => 'friday',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'duration_per_patient' => 20,
        ]);

        // Dr 2: tuesday, thursday, saturday
        DoctorSchedule::create([
            'doctor_id' => $doc2->id,
            'day_of_week' => 'tuesday',
            'start_time' => '10:00:00',
            'end_time' => '15:00:00',
            'duration_per_patient' => 15,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc2->id,
            'day_of_week' => 'thursday',
            'start_time' => '10:00:00',
            'end_time' => '15:00:00',
            'duration_per_patient' => 15,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc2->id,
            'day_of_week' => 'saturday',
            'start_time' => '10:00:00',
            'end_time' => '15:00:00',
            'duration_per_patient' => 15,
        ]);

        // Dr 3: sunday, monday, wednesday
        DoctorSchedule::create([
            'doctor_id' => $doc3->id,
            'day_of_week' => 'sunday',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'duration_per_patient' => 30,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc3->id,
            'day_of_week' => 'monday',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'duration_per_patient' => 30,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doc3->id,
            'day_of_week' => 'wednesday',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'duration_per_patient' => 30,
        ]);

        // 7. Seed Services
        // Dr 1 (Cardiology)
        $s1 = Service::create(['doctor_id' => $doc1->id, 'service_name' => 'Cardiology Consultation', 'price' => 150.00]);
        $s2 = Service::create(['doctor_id' => $doc1->id, 'service_name' => 'ECG Heart Monitor', 'price' => 80.00]);

        // Dr 2 (Dermatology)
        $s3 = Service::create(['doctor_id' => $doc2->id, 'service_name' => 'Dermatology Consultation', 'price' => 120.00]);
        $s4 = Service::create(['doctor_id' => $doc2->id, 'service_name' => 'Skin Allergen Test', 'price' => 60.00]);

        // Dr 3 (Pediatrics)
        $s5 = Service::create(['doctor_id' => $doc3->id, 'service_name' => 'Pediatric Consultation', 'price' => 100.00]);
        $s6 = Service::create(['doctor_id' => $doc3->id, 'service_name' => 'Child Vaccination visit', 'price' => 50.00]);

        // 8. Seed 15 Patients
        $patients = [];
        for ($i = 1; $i <= 15; $i++) {
            $patients[] = User::create([
                'name' => "Patient Number $i",
                'email' => "patient$i@clinic.com",
                'password' => Hash::make('password123'),
                'phone' => '09111111' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'role' => 'patient',
                'wallet_balance' => 800.00, // starting balance
                'violation_count' => ($i % 5 == 0) ? 1 : 0, // violation for some
                'email_verified_at' => now(),
            ]);
        }

        // Helper dates
        $today = Carbon::today()->format('yyyy-MM-dd');
        $yesterday = Carbon::yesterday();
        $threeDaysAgo = Carbon::today()->subDays(3);
        $tomorrow = Carbon::tomorrow();
        $nextWeek = Carbon::today()->addDays(7);

        // 9. Seed Appointments & related data
        // App 1: Completed cardiology appointment for Patient 1
        $app1 = Appointment::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doc1->id,
            'service_id' => $s1->id,
            'appointment_date' => $threeDaysAgo->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'status' => 'completed',
            'notes' => 'Feeling short of breath lately.',
        ]);

        // Medical Record for App 1
        $record1 = MedicalRecord::create([
            'appointment_id' => $app1->id,
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doc1->id,
            'visit_date' => $app1->appointment_date,
            'symptoms' => 'Mild heart flutter, shortness of breath on exertion.',
            'diagnosis' => 'Arrhythmia suspect, advised monitor.',
            'doctor_notes' => 'Keep wallet activity low, reduce caffeine.',
        ]);

        Prescription::create([
            'medical_record_id' => $record1->id,
            'medication_name' => 'Metoprolol 50mg',
            'dosage' => 'Once daily',
            'duration' => '30 Days',
            'instructions' => 'Take with breakfast in the morning.',
        ]);

        // Invoice for App 1
        Invoice::create([
            'appointment_id' => $app1->id,
            'total_amount' => 150.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 100.00,
            'payment_status' => 'paid',
            'payment_method' => 'online',
            'issued_at' => $threeDaysAgo,
        ]);

        // Transactions for App 1
        WalletTransaction::create([
            'user_id' => $patients[0]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Card. appointment',
            'appointment_id' => $app1->id,
        ]);
        $patients[0]->update(['wallet_balance' => 750.00]);

        // App 2: Confirmed appointment for Patient 2 (Dr 2 - Dermatology)
        $app2 = Appointment::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doc2->id,
            'service_id' => $s3->id,
            'appointment_date' => $tomorrow->format('Y-m-d'),
            'appointment_time' => '11:00:00',
            'status' => 'confirmed',
            'notes' => 'Severe rash on my hands.',
        ]);

        Invoice::create([
            'appointment_id' => $app2->id,
            'total_amount' => 120.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 70.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'online',
            'issued_at' => now(),
        ]);

        WalletTransaction::create([
            'user_id' => $patients[1]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Derm. appointment',
            'appointment_id' => $app2->id,
        ]);
        $patients[1]->update(['wallet_balance' => 750.00]);

        // App 3: Pending appointment request for Patient 3 (Dr 3 - Pediatrics)
        $app3 = Appointment::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doc3->id,
            'service_id' => $s5->id,
            'appointment_date' => $tomorrow->format('Y-m-d'),
            'appointment_time' => '09:00:00',
            'status' => 'pending',
            'notes' => 'Child regular checkup.',
        ]);

        Invoice::create([
            'appointment_id' => $app3->id,
            'total_amount' => 100.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 50.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'online',
            'issued_at' => now(),
        ]);

        WalletTransaction::create([
            'user_id' => $patients[2]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Pedia. appointment',
            'appointment_id' => $app3->id,
        ]);
        $patients[2]->update(['wallet_balance' => 750.00]);

        // App 4: Rejected appointment for Patient 4 (Dr 1)
        $app4 = Appointment::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doc1->id,
            'service_id' => $s2->id,
            'appointment_date' => $yesterday->format('Y-m-d'),
            'appointment_time' => '12:00:00',
            'status' => 'rejected',
            'notes' => 'Need ECG monitor test.',
        ]);

        // Refund full transaction for rejection
        WalletTransaction::create([
            'user_id' => $patients[3]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for ECG appointment',
            'appointment_id' => $app4->id,
        ]);
        WalletTransaction::create([
            'user_id' => $patients[3]->id,
            'type' => 'refund_full',
            'amount' => 50.00,
            'balance_before' => 750.00,
            'balance_after' => 800.00,
            'description' => 'Refund: appointment rejected by doctor',
            'appointment_id' => $app4->id,
        ]);

        // App 5: Cancelled appointment for Patient 5 (by Patient)
        $app5 = Appointment::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doc2->id,
            'service_id' => $s4->id,
            'appointment_date' => $tomorrow->format('Y-m-d'),
            'appointment_time' => '13:00:00',
            'status' => 'cancelled',
            'notes' => 'Allergy test request.',
            'cancellation_reason' => 'Change of plans.',
            'cancelled_by' => 'patient',
            'cancelled_at' => now(),
        ]);

        // Refund partial after violation penalty
        WalletTransaction::create([
            'user_id' => $patients[4]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Allergy test',
            'appointment_id' => $app5->id,
        ]);
        // Violation count was 1 for Patient 5 (i % 5 == 0), so penalty rate is 5%
        WalletTransaction::create([
            'user_id' => $patients[4]->id,
            'type' => 'refund_partial',
            'amount' => 47.50,
            'balance_before' => 750.00,
            'balance_after' => 797.50,
            'description' => 'Partial refund after 5% penalty (violation #1)',
            'appointment_id' => $app5->id,
        ]);
        WalletTransaction::create([
            'user_id' => $patients[4]->id,
            'type' => 'penalty',
            'amount' => 2.50,
            'balance_before' => 797.50,
            'balance_after' => 797.50,
            'description' => 'Penalty 5% for late cancellation',
            'appointment_id' => $app5->id,
        ]);
        $patients[4]->update(['wallet_balance' => 797.50]);

        // App 6: Confirmed appointment on Today for Doctor 1 to display in Schedule Overview
        $app6 = Appointment::create([
            'patient_id' => $patients[5]->id,
            'doctor_id' => $doc1->id,
            'service_id' => $s1->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'appointment_time' => '09:30:00',
            'status' => 'confirmed',
            'notes' => 'Follow up on my heart pills.',
        ]);

        Invoice::create([
            'appointment_id' => $app6->id,
            'total_amount' => 150.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 100.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'online',
            'issued_at' => now(),
        ]);

        WalletTransaction::create([
            'user_id' => $patients[5]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Card. follow-up',
            'appointment_id' => $app6->id,
        ]);
        $patients[5]->update(['wallet_balance' => 750.00]);

        // App 7: Pending appointment on Today for Doctor 1
        $app7 = Appointment::create([
            'patient_id' => $patients[6]->id,
            'doctor_id' => $doc1->id,
            'service_id' => $s1->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'appointment_time' => '11:00:00',
            'status' => 'pending',
            'notes' => 'Chest pain description.',
        ]);

        Invoice::create([
            'appointment_id' => $app7->id,
            'total_amount' => 150.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 100.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'online',
            'issued_at' => now(),
        ]);

        WalletTransaction::create([
            'user_id' => $patients[6]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for Cardiology chest pain',
            'appointment_id' => $app7->id,
        ]);
        $patients[6]->update(['wallet_balance' => 750.00]);

        // App 8: Completed Dermatology visit for Patient 8
        $app8 = Appointment::create([
            'patient_id' => $patients[7]->id,
            'doctor_id' => $doc2->id,
            'service_id' => $s3->id,
            'appointment_date' => $threeDaysAgo->format('Y-m-d'),
            'appointment_time' => '12:00:00',
            'status' => 'completed',
            'notes' => 'Dry skin problem.',
        ]);

        $record8 = MedicalRecord::create([
            'appointment_id' => $app8->id,
            'patient_id' => $patients[7]->id,
            'doctor_id' => $doc2->id,
            'visit_date' => $app8->appointment_date,
            'symptoms' => 'Severe dryness and irritation.',
            'diagnosis' => 'Eczema.',
            'doctor_notes' => 'Advised mild soaps and daily moisturizing.',
        ]);

        Prescription::create([
            'medical_record_id' => $record8->id,
            'medication_name' => 'Hydrocortisone cream',
            'dosage' => 'Apply twice daily',
            'duration' => '10 Days',
            'instructions' => 'Avoid direct sunlight after application.',
        ]);

        Invoice::create([
            'appointment_id' => $app8->id,
            'total_amount' => 120.00,
            'deposit_amount' => 50.00,
            'remaining_amount' => 70.00,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'issued_at' => $threeDaysAgo,
        ]);

        WalletTransaction::create([
            'user_id' => $patients[7]->id,
            'type' => 'booking_deduct',
            'amount' => 50.00,
            'balance_before' => 800.00,
            'balance_after' => 750.00,
            'description' => 'Deposit deduct for dry skin consultation',
            'appointment_id' => $app8->id,
        ]);
        $patients[7]->update(['wallet_balance' => 750.00]);
    }
}

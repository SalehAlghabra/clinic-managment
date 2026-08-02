<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorDetail;
use App\Models\DoctorSchedule;
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
        // 1. Disable FK constraints and clear tables safely
        Schema::disableForeignKeyConstraints();
        WalletTransaction::truncate();
        Invoice::truncate();
        Prescription::truncate();
        MedicalRecord::truncate();
        Appointment::truncate();
        DoctorSchedule::truncate();
        DoctorDetail::truncate();
        User::truncate();
        Setting::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Seed System Settings
        Setting::create(['key' => 'booking_deposit', 'value' => '50', 'description' => 'المبلغ الإقتطاعي المطلوب كعربون حجز موعد']);
        Setting::create(['key' => 'max_penalty_percentage', 'value' => '25', 'description' => 'النسبة المئوية الحسابية لغرامة الإلغاء المتأخر']);
        Setting::create(['key' => 'cancellation_hours', 'value' => '24', 'description' => 'الحد الأدنى بالساعات للإلغاء بدون غرامة مالیة']);

        // 3. Seed Admin User
        $admin = User::create([
          'name' => 'مدير النظام',
          'email' => 'admin@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999991',
          'role' => 'admin',
          'email_verified_at' => now(),
        ]);

        // 4. Seed Receptionist User
        $receptionist = User::create([
          'name' => 'موظف الاستقبال',
          'email' => 'receptionist@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999992',
          'role' => 'receptionist',
          'email_verified_at' => now(),
        ]);

        // 5. Seed Doctors across distinct specialties
        // Doctor 1: Cardiology
        $doc1User = User::create([
          'name' => 'د. يوسف المنصور',
          'email' => 'doctor1@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999993',
          'role' => 'doctor',
          'email_verified_at' => now(),
        ]);
        $doc1 = DoctorDetail::create([
          'user_id' => $doc1User->id,
          'specialization' => 'أمراض القلب والشرايين',
          'bio' => 'استشاري جراحة وقلب أطفال وكبار، خبرة أكثر من 15 عاماً في قسطرة الشرايين وعلاج ضغط الدم والاضطرابات القلبية.',
          'consultation_fee' => 150.00,
        ]);

        // Doctor 2: Dermatology
        $doc2User = User::create([
          'name' => 'د. فاطمة الزهراء',
          'email' => 'doctor2@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999994',
          'role' => 'doctor',
          'email_verified_at' => now(),
        ]);
        $doc2 = DoctorDetail::create([
          'user_id' => $doc2User->id,
          'specialization' => 'الأمراض الجلدية والتجميل',
          'bio' => 'أخصائية تجميل وجلدية وعلاج بالليزر، متخصصة في علاج مشاكل البشرة وحب الشباب والعناية بالشعر.',
          'consultation_fee' => 120.00,
        ]);

        // Doctor 3: Pediatrics
        $doc3User = User::create([
          'name' => 'د. كريم عبد العزيز',
          'email' => 'doctor3@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999995',
          'role' => 'doctor',
          'email_verified_at' => now(),
        ]);
        $doc3 = DoctorDetail::create([
          'user_id' => $doc3User->id,
          'specialization' => 'طب الأطفال وحديثي الولادة',
          'bio' => 'طبيب أطفال متخصص في متابعة النمو والتطور للأطفال وحديثي الولادة وعلاج الأمراض الصدرية والحساسية.',
          'consultation_fee' => 100.00,
        ]);

        // Doctor 4: Orthopedics
        $doc4User = User::create([
          'name' => 'د. مريم العلي',
          'email' => 'doctor4@clinic.com',
          'password' => Hash::make('password123'),
          'phone' => '0999999996',
          'role' => 'doctor',
          'email_verified_at' => now(),
        ]);
        $doc4 = DoctorDetail::create([
          'user_id' => $doc4User->id,
          'specialization' => 'جراحة العظام والمفاصل',
          'bio' => 'استشارية جراحة العظام والمفاصل، متخصصة في إصابات الملعب والكسور وعلاج آلام العمود الفقري والمفاصل.',
          'consultation_fee' => 140.00,
        ]);

        // 6. Seed Doctor Schedules
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'monday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'friday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);

        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'tuesday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'thursday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'saturday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);

        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'sunday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'monday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'wednesday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);

        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'tuesday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'thursday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);

        // 7. Seed Patients
        $arabicNames = [
          'أحمد علي مصطفى', 'سارة محمود حسن', 'محمد خالد العتيبي', 'أسماء عمر إبراهيم',
          'طارق حسن السيد', 'ريم سليمان الخالد', 'ياسر عبد الله الزهراني', 'هدى عبد الرحمن الشمري',
          'عمر فاروق الغامدي', 'منى يوسف القحطاني', 'خالد إبراهيم الدوسري', 'ليلى عثمان النجار',
          'حمزة صالح البقمي', 'نورة فيصل العنزي', 'زياد طلال المطيري', 'مريم جمال الصالح',
          'عبد العزيز سعيد الحارثي', 'فاطمة بدر المالكي', 'بلال راشد السبيعي', 'رزان وليد الحربي'
        ];

        $patients = [];
        for ($i = 1; $i <= 20; $i++) {
            $patients[] = User::create([
              'name' => $arabicNames[$i - 1],
              'email' => "patient$i@clinic.com",
              'password' => Hash::make('password123'),
              'phone' => '09111111' . str_pad($i, 2, '0', STR_PAD_LEFT),
              'role' => 'patient',
              'wallet_balance' => 500.00 + ($i * 50.00),
              'violation_count' => ($i % 6 == 0) ? 1 : 0,
              'email_verified_at' => now(),
            ]);
        }

        // Helpers
        $threeDaysAgo   = Carbon::today()->subDays(3)->format('Y-m-d');
        $twoDaysAgo     = Carbon::today()->subDays(2)->format('Y-m-d');
        $yesterday      = Carbon::yesterday()->format('Y-m-d');
        $today          = Carbon::today()->format('Y-m-d');
        $tomorrow       = Carbon::tomorrow()->format('Y-m-d');

        // 8. Seed Appointments
        $app1 = Appointment::create([
          'patient_id'       => $patients[0]->id,
          'doctor_id'        => $doc1->id,
          'consultation_fee' => 150.00,
          'appointment_date' => $threeDaysAgo,
          'appointment_time' => '09:30:00',
          'status'           => 'completed',
          'additional_cost'  => 50.00,
          'additional_note'  => 'تخطيط قلب إضافي',
          'notes'            => 'أعاني من تسارع في ضربات القلب وضيق في التنفس.',
        ]);

        $app2 = Appointment::create([
          'patient_id'       => $patients[1]->id,
          'doctor_id'        => $doc2->id,
          'consultation_fee' => 120.00,
          'appointment_date' => $twoDaysAgo,
          'appointment_time' => '10:15:00',
          'status'           => 'completed',
          'additional_cost'  => 0.00,
          'notes'            => 'استشارة بخصوص احمرار وحكة شديدة.',
        ]);

        $app3 = Appointment::create([
          'patient_id'       => $patients[2]->id,
          'doctor_id'        => $doc3->id,
          'consultation_fee' => 100.00,
          'appointment_date' => $today,
          'appointment_time' => '08:30:00',
          'status'           => 'confirmed',
          'notes'            => 'ارتفاع حرارة الطفل وسعلة خفيفة.',
        ]);

        // Seed Invoices
        Invoice::create([
          'appointment_id'   => $app1->id,
          'total_amount'     => 200.00,
          'deposit_amount'   => 150.00,
          'remaining_amount' => 50.00,
          'payment_status'   => 'paid',
          'payment_method'   => 'cash',
          'issued_at'        => now(),
        ]);

        Invoice::create([
          'appointment_id'   => $app2->id,
          'total_amount'     => 120.00,
          'deposit_amount'   => 120.00,
          'remaining_amount' => 0.00,
          'payment_status'   => 'paid',
          'payment_method'   => 'wallet',
          'issued_at'        => now(),
        ]);
    }
}

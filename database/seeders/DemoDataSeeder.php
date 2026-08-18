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
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0938472910',
            'role' => 'admin',
            'profile_picture' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        // 4. Seed Receptionist User
        User::create([
            'name' => 'موظف الاستقبال',
            'email' => 'receptionist@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0984710293',
            'role' => 'receptionist',
            'profile_picture' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        // 5. Seed 5 Doctors across distinct specialties with prices between $8 and $14
        // Doctor 1: Cardiology (price: $8.00)
        $doc1User = User::create([
            'name' => 'د. يوسف المنصور',
            'email' => 'doctor1@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0947201938',
            'role' => 'doctor',
            'profile_picture' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);
        $doc1 = DoctorDetail::create([
            'user_id' => $doc1User->id,
            'specialization' => 'أمراض القلب والشرايين',
            'bio' => 'استشاري أمراض القلب والشرايين، خبرة أكثر من 15 عاماً في قسطرة الشرايين وعلاج ضغط الدم والاضطرابات القلبية.',
            'consultation_fee' => 8.00,
        ]);

        // Doctor 2: Dermatology (price: $10.00)
        $doc2User = User::create([
            'name' => 'د. فاطمة الزهراء',
            'email' => 'doctor2@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0971039482',
            'role' => 'doctor',
            'profile_picture' => 'https://images.unsplash.com/photo-1594824813566-788534778b7c?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);
        $doc2 = DoctorDetail::create([
            'user_id' => $doc2User->id,
            'specialization' => 'الأمراض الجلدية والتجميل',
            'bio' => 'أخصائية الأمراض الجلدية والعناية بالبشرة والليزر، متخصصة في علاج كافة المشاكل الجلدية وحب الشباب وعلاجات التجميل.',
            'consultation_fee' => 10.00,
        ]);

        // Doctor 3: Eye Doctor / Ophthalmology (price: $12.00)
        $doc3User = User::create([
            'name' => 'د. كريم عبد العزيز',
            'email' => 'doctor3@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0958203941',
            'role' => 'doctor',
            'profile_picture' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);
        $doc3 = DoctorDetail::create([
            'user_id' => $doc3User->id,
            'specialization' => 'طب وجراحة العيون',
            'bio' => 'استشاري طب وجراحة العيون، متخصص في فحص النظر، تصحيح الرؤية، وعلاج أمراض الشبكية والقرنية والمياه البيضاء.',
            'consultation_fee' => 12.00,
        ]);

        // Doctor 4: ENT Doctor (price: $14.00)
        $doc4User = User::create([
            'name' => 'د. مريم العلي',
            'email' => 'doctor4@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0918274930',
            'role' => 'doctor',
            'profile_picture' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);
        $doc4 = DoctorDetail::create([
            'user_id' => $doc4User->id,
            'specialization' => 'طب وجراحة الأنف والأذن والحنجرة',
            'bio' => 'استشارية طب وجراحة الأنف والأذن والحنجرة، متخصصة في علاج الجيوب الأنفية، التهابات الأذن، واضطرابات السمع والحبال الصوتية.',
            'consultation_fee' => 14.00,
        ]);

        // Doctor 5: Neurology / Nerve Doctor (price: $11.50)
        $doc5User = User::create([
            'name' => 'د. طارق السعيد',
            'email' => 'doctor5@clinic.com',
            'password' => Hash::make('password123'),
            'phone' => '0963820194',
            'role' => 'doctor',
            'profile_picture' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);
        $doc5 = DoctorDetail::create([
            'user_id' => $doc5User->id,
            'specialization' => 'أمراض المخ والأعصاب',
            'bio' => 'استشاري أمراض المخ والأعصاب (دكتور أعصاب)، متخصص في علاج الاضطرابات العصبية، الصداع المزمن، وأمراض الأعصاب الطرفية والعمود الفقري.',
            'consultation_fee' => 11.50,
        ]);

        // 6. Seed Doctor Schedules
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'monday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'wednesday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'friday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 20]);

        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'tuesday', 'start_time' => '13:00:00', 'end_time' => '17:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'thursday', 'start_time' => '13:00:00', 'end_time' => '17:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'saturday', 'start_time' => '13:00:00', 'end_time' => '17:00:00', 'duration_per_patient' => 15]);

        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'sunday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'tuesday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'thursday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'duration_per_patient' => 30]);

        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'monday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'wednesday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'thursday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);

        DoctorSchedule::create(['doctor_id' => $doc5->id, 'day_of_week' => 'friday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc5->id, 'day_of_week' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 30]);

        // 7. Seed Patients (5 accounts with positive & varied wallet balances and profile pictures)
        $patientsData = [
            [
                'name' => 'أحمد علي مصطفى',
                'email' => 'patient1@clinic.com',
                'phone' => '0928374910',
                'wallet' => 50.00,
                'picture' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'سارة محمود حسن',
                'email' => 'patient2@clinic.com',
                'phone' => '0983720194',
                'wallet' => 100.00,
                'picture' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'محمد خالد العتيبي',
                'email' => 'patient3@clinic.com',
                'phone' => '0937482019',
                'wallet' => 150.00,
                'picture' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'أسماء عمر إبراهيم',
                'email' => 'patient4@clinic.com',
                'phone' => '0957102938',
                'wallet' => 250.00,
                'picture' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'خالد إبراهيم الدوسري',
                'email' => 'patient5@clinic.com',
                'phone' => '0974820193',
                'wallet' => 500.00,
                'picture' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&auto=format&fit=crop&q=80',
            ],
        ];

        $patients = [];
        foreach ($patientsData as $p) {
            $patients[] = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('password123'),
                'phone' => $p['phone'],
                'role' => 'patient',
                'wallet_balance' => $p['wallet'],
                'profile_picture' => $p['picture'],
                'violation_count' => 0,
                'email_verified_at' => now(),
            ]);
        }

        // 8. Seed 5 Historical Visits (3 Completed, 2 Cancelled)
        // Visit 1: Completed (Patient 1 with Doctor 1)
        Appointment::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doc1->id,
            'consultation_fee' => 8.00,
            'appointment_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
            'appointment_time' => '08:00:00',
            'status' => 'completed',
            'additional_cost' => 2.00,
            'additional_note' => 'تخطيط قلب ودعم متابعة',
            'notes' => 'مراجعة دورية لضغط الدم وحالة القلب',
        ]);

        // Visit 2: Completed (Patient 2 with Doctor 2)
        Appointment::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doc2->id,
            'consultation_fee' => 10.00,
            'appointment_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'appointment_time' => '13:00:00',
            'status' => 'completed',
            'additional_cost' => 0.00,
            'notes' => 'استشارة بخصوص تنظيف البشرة وحب الشباب',
        ]);

        // Visit 3: Completed (Patient 3 with Doctor 3)
        Appointment::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doc3->id,
            'consultation_fee' => 12.00,
            'appointment_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
            'appointment_time' => '09:00:00',
            'status' => 'completed',
            'additional_cost' => 3.00,
            'additional_note' => 'فحص قعر العين النظاري',
            'notes' => 'فحص نظر دوري وقياس حدة الإبصار',
        ]);

        // Visit 4: Cancelled by Patient (Patient 4 with Doctor 4)
        Appointment::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doc4->id,
            'consultation_fee' => 14.00,
            'appointment_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'appointment_time' => '14:00:00',
            'status' => 'cancelled',
            'cancelled_by' => 'patient',
            'cancellation_reason' => 'ظرف طارئ للمريض وتم الإلغاء قبل الموعد',
            'cancelled_at' => Carbon::now()->subDays(4),
            'notes' => 'التهاب في الأذن والحنجرة',
        ]);

        // Visit 5: Cancelled by Doctor (Patient 5 with Doctor 5)
        Appointment::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doc5->id,
            'consultation_fee' => 11.50,
            'appointment_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'appointment_time' => '09:00:00',
            'status' => 'cancelled',
            'cancelled_by' => 'doctor',
            'cancellation_reason' => 'إلغاء المواعيد بسبب مشاركة الطبيب في مؤتمر طبي طارئ',
            'cancelled_at' => Carbon::now()->subDays(3),
            'notes' => 'استشارة صداع مزمن وآلام رقبة',
        ]);
    }
}



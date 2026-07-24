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
        // 1. Disable FK constraints and clear tables safely
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

        // 2. Seed System Settings
        Setting::create(['key' => 'booking_deposit', 'value' => '50', 'description' => 'المبلغ الإقتطاعي المطلوب كعربون حجز موعد']);
        Setting::create(['key' => 'max_penalty_percentage', 'value' => '25', 'description' => 'النسبة المئوية الحسابية لغرامة الإلغاء المتأخر']);
        Setting::create(['key' => 'hours_before_cancellation', 'value' => '24', 'description' => 'الحد الأدنى بالساعات للإلغاء بدون غرامة مالیة']);

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

        // 5. Seed 4 Doctors across distinct specialties with Arabic Bios
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
        ]);

        // 6. Seed Doctor Schedules
        // Doctor 1: Monday, Wednesday, Friday
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'monday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc1->id, 'day_of_week' => 'friday', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'duration_per_patient' => 20]);

        // Doctor 2: Tuesday, Thursday, Saturday
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'tuesday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'thursday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);
        DoctorSchedule::create(['doctor_id' => $doc2->id, 'day_of_week' => 'saturday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'duration_per_patient' => 15]);

        // Doctor 3: Sunday, Monday, Wednesday
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'sunday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'monday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);
        DoctorSchedule::create(['doctor_id' => $doc3->id, 'day_of_week' => 'wednesday', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'duration_per_patient' => 30]);

        // Doctor 4: Sunday, Tuesday, Thursday
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'tuesday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);
        DoctorSchedule::create(['doctor_id' => $doc4->id, 'day_of_week' => 'thursday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'duration_per_patient' => 20]);

        // 7. Seed Arabic Services
        // Doctor 1
        $s1 = Service::create(['doctor_id' => $doc1->id, 'service_name' => 'معاينة قلبية متخصصة', 'price' => 150.00]);
        $s2 = Service::create(['doctor_id' => $doc1->id, 'service_name' => 'تخطيط القلب الكهربائي (ECG)', 'price' => 80.00]);
        $s3 = Service::create(['doctor_id' => $doc1->id, 'service_name' => 'فحص إيكو القلب (ECHO)', 'price' => 200.00]);

        // Doctor 2
        $s4 = Service::create(['doctor_id' => $doc2->id, 'service_name' => 'استشارة جلدية وتجميلية', 'price' => 120.00]);
        $s5 = Service::create(['doctor_id' => $doc2->id, 'service_name' => 'تنظيف وتجديد البشرة بالليزر', 'price' => 250.00]);
        $s6 = Service::create(['doctor_id' => $doc2->id, 'service_name' => 'فحص تنظير الجلد والمحيط', 'price' => 90.00]);

        // Doctor 3
        $s7 = Service::create(['doctor_id' => $doc3->id, 'service_name' => 'كشف أطفال دوري', 'price' => 100.00]);
        $s8 = Service::create(['doctor_id' => $doc3->id, 'service_name' => 'برنامج تطعيمات ومتابعة نمو', 'price' => 70.00]);

        // Doctor 4
        $s9 = Service::create(['doctor_id' => $doc4->id, 'service_name' => 'استشارة عظام ومفاصل', 'price' => 140.00]);
        $s10 = Service::create(['doctor_id' => $doc4->id, 'service_name' => 'جلسة علاج طبيعي وتأهيل', 'price' => 110.00]);

        // 8. Seed 20 Patients in Arabic
        $arabicNames = [
          'أحمد علي مصطفى',
          'سارة محمود حسن',
          'محمد خالد العتيبي',
          'أسماء عمر إبراهيم',
          'طارق حسن السيد',
          'ريم سليمان الخالد',
          'ياسر عبد الله الزهراني',
          'هدى عبد الرحمن الشمري',
          'عمر فاروق الغامدي',
          'منى يوسف القحطاني',
          'خالد إبراهيم الدوسري',
          'ليلى عثمان النجار',
          'حمزة صالح البقمي',
          'نورة فيصل العنزي',
          'زياد طلال المطيري',
          'مريم جمال الصالح',
          'عبد العزيز سعيد الحارثي',
          'فاطمة بدر المالكي',
          'بلال راشد السبيعي',
          'رزان وليد الحربي',
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
        $threeDaysAgo = Carbon::today()->subDays(3)->format('Y-m-d');
        $twoDaysAgo = Carbon::today()->subDays(2)->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $threeDaysLater = Carbon::today()->addDays(3)->format('Y-m-d');

        // 9. Seed Appointments across all statuses
        // ----------------------------------------------------
        // COMPLETED APPOINTMENTS
        // App 1: Patient 1 -> Doc 1
        $app1 = Appointment::create([
          'patient_id' => $patients[0]->id,
          'doctor_id' => $doc1->id,
          'service_id' => $s1->id,
          'appointment_date' => $threeDaysAgo,
          'appointment_time' => '09:30:00',
          'status' => 'completed',
          'notes' => 'أعاني من تسارع في ضربات القلب وضيق في التنفس عند الحركة.',
        ]);

        // App 2: Patient 2 -> Doc 2
        $app2 = Appointment::create([
          'patient_id' => $patients[1]->id,
          'doctor_id' => $doc2->id,
          'service_id' => $s4->id,
          'appointment_date' => $twoDaysAgo,
          'appointment_time' => '10:15:00',
          'status' => 'completed',
          'notes' => 'استشارة بخصوص احمرار وحكة شديدة في الجلد.',
        ]);

        // App 3: Patient 3 -> Doc 3
        $app3 = Appointment::create([
          'patient_id' => $patients[2]->id,
          'doctor_id' => $doc3->id,
          'service_id' => $s7->id,
          'appointment_date' => $yesterday,
          'appointment_time' => '08:30:00',
          'status' => 'completed',
          'notes' => 'ارتفاع حرارة الطفل وسعلة خفيفة منذ ليلتين.',
        ]);

        // App 4: Patient 4 -> Doc 4
        $app4 = Appointment::create([
          'patient_id' => $patients[3]->id,
          'doctor_id' => $doc4->id,
          'service_id' => $s9->id,
          'appointment_date' => $threeDaysAgo,
          'appointment_time' => '14:20:00',
          'status' => 'completed',
          'notes' => 'آلام حادة في المفصل وصعوبة في صعود السلالم.',
        ]);

        // ----------------------------------------------------
        // CONFIRMED APPOINTMENTS (Today / Upcoming)
        // App 5: Patient 5 -> Doc 1
        $app5 = Appointment::create([
          'patient_id' => $patients[4]->id,
          'doctor_id' => $doc1->id,
          'service_id' => $s2->id,
          'appointment_date' => $today,
          'appointment_time' => '11:00:00',
          'status' => 'confirmed',
          'notes' => 'مراجعة نتيجة فحص تخطيط القلب وعرض النتائج.',
        ]);

        // App 6: Patient 6 -> Doc 2
        $app6 = Appointment::create([
          'patient_id' => $patients[5]->id,
          'doctor_id' => $doc2->id,
          'service_id' => $s5->id,
          'appointment_date' => $tomorrow,
          'appointment_time' => '11:30:00',
          'status' => 'confirmed',
          'notes' => 'جلسة نضارة وتقشير للبشرة بالليزر.',
        ]);

        // App 7: Patient 7 -> Doc 3
        $app7 = Appointment::create([
          'patient_id' => $patients[6]->id,
          'doctor_id' => $doc3->id,
          'service_id' => $s8->id,
          'appointment_date' => $threeDaysLater,
          'appointment_time' => '09:00:00',
          'status' => 'confirmed',
          'notes' => 'كشف وتطعيم دوري لعمر 6 أشهر.',
        ]);

        // App 8: Patient 8 -> Doc 4
        $app8 = Appointment::create([
          'patient_id' => $patients[7]->id,
          'doctor_id' => $doc4->id,
          'service_id' => $s10->id,
          'appointment_date' => $tomorrow,
          'appointment_time' => '15:00:00',
          'status' => 'confirmed',
          'notes' => 'جلسة تأهيل وعلاج طبيعي للظهر.',
        ]);

        // ----------------------------------------------------
        // PENDING APPOINTMENTS (Awaiting Doctor Confirmation)
        // App 9: Patient 9 -> Doc 1
        $app9 = Appointment::create([
          'patient_id' => $patients[8]->id,
          'doctor_id' => $doc1->id,
          'service_id' => $s3->id,
          'appointment_date' => $threeDaysLater,
          'appointment_time' => '12:00:00',
          'status' => 'pending',
          'notes' => 'استشارة حول آلام في الصدر بعد التمارين الرياضية.',
        ]);

        // App 10: Patient 10 -> Doc 2
        $app10 = Appointment::create([
          'patient_id' => $patients[9]->id,
          'doctor_id' => $doc2->id,
          'service_id' => $s6->id,
          'appointment_date' => $threeDaysLater,
          'appointment_time' => '13:00:00',
          'status' => 'pending',
          'notes' => 'فحص الشامات والبقع الجلدية المفاجئة.',
        ]);

        // ----------------------------------------------------
        // CANCELLED APPOINTMENTS
        // App 11: Patient 11 -> Doc 1 (Cancelled)
        $app11 = Appointment::create([
          'patient_id' => $patients[10]->id,
          'doctor_id' => $doc1->id,
          'service_id' => $s1->id,
          'appointment_date' => $twoDaysAgo,
          'appointment_time' => '10:00:00',
          'status' => 'cancelled',
          'notes' => 'إلغاء بداعي السفر الطارئ وتغير جدول العمل.',
        ]);

        // App 12: Patient 12 -> Doc 3 (Cancelled)
        $app12 = Appointment::create([
          'patient_id' => $patients[11]->id,
          'doctor_id' => $doc3->id,
          'service_id' => $s7->id,
          'appointment_date' => $yesterday,
          'appointment_time' => '10:30:00',
          'status' => 'cancelled',
          'notes' => 'إلغاء الموعد بسبب تحسن حالة الطفل.',
        ]);

        // ----------------------------------------------------
        // REJECTED APPOINTMENTS
        // App 13: Patient 13 -> Doc 4 (Rejected)
        $app13 = Appointment::create([
          'patient_id' => $patients[12]->id,
          'doctor_id' => $doc4->id,
          'service_id' => $s9->id,
          'appointment_date' => $yesterday,
          'appointment_time' => '16:00:00',
          'status' => 'rejected',
          'notes' => 'اعتذار الطبيبة لعدم التواجد في العيادة بداعي عملية طارئة.',
        ]);


        // 10. Seed Medical Records & Prescriptions for Completed Visits
        // Record 1 (App 1)
        $rec1 = MedicalRecord::create([
          'appointment_id' => $app1->id,
          'patient_id' => $patients[0]->id,
          'doctor_id' => $doc1->id,
          'visit_date' => $app1->appointment_date,
          'symptoms' => 'تسارع نبضات القلب، إجهاد عام، ضيق تنفس عند الصعود.',
          'diagnosis' => 'ارتفاع ضغط الدم الشرياني مع اضطراب نبضات خفيف.',
          'doctor_notes' => 'الالتزام بالنظام الغذائي قليل الملح وممارسة الرياضة الخفيفة يومياً.',
        ]);
        Prescription::create([
          'medical_record_id' => $rec1->id,
          'medication_name' => 'كونكور 5 ملغ (Concor 5mg)',
          'dosage' => 'قرص واحد صباحاً بعد الإفطار',
          'duration' => '30 يوماً',
          'instructions' => 'تناول الدواء مع كوب ماء كامل يومياً في نفس الموعد المحدد',
        ]);
        Prescription::create([
          'medical_record_id' => $rec1->id,
          'medication_name' => 'أسبرين حماية (Aspirin 81mg)',
          'dosage' => 'قرص واحد مساءً',
          'duration' => '30 يوماً',
          'instructions' => 'بعد وجبة العشاء مباشرة',
        ]);

        // Record 2 (App 2)
        $rec2 = MedicalRecord::create([
          'appointment_id' => $app2->id,
          'patient_id' => $patients[1]->id,
          'doctor_id' => $doc2->id,
          'visit_date' => $app2->appointment_date,
          'symptoms' => 'احمرار شديد في البشرة، حكة مستمرة، جفاف في جلد الوجه.',
          'diagnosis' => 'إكزيما تلامسية حادة ناتجة عن استخدام مستحضرات كيميائية.',
          'doctor_notes' => 'تجنب الصابون المعطر واستخدام مرطب طبي خالي من العطور.',
        ]);
        Prescription::create([
          'medical_record_id' => $rec2->id,
          'medication_name' => 'موميتاسون كريم (Mometasone Cream)',
          'dosage' => 'دهان موضعي مرتين يومياً',
          'duration' => '7 أيام',
          'instructions' => 'ضع طبقة رقيقة جداً على المناطق المصابة بالحكة فقط',
        ]);

        // Record 3 (App 3)
        $rec3 = MedicalRecord::create([
          'appointment_id' => $app3->id,
          'patient_id' => $patients[2]->id,
          'doctor_id' => $doc3->id,
          'visit_date' => $app3->appointment_date,
          'symptoms' => 'حرارة 38.5 درجة، سعلة جافة، فقدان الشهية للطفل.',
          'diagnosis' => 'التهاب الحلق والبلعوم الفيروسي الشائع عند الأطفال.',
          'doctor_notes' => 'إكثار من السوائل الدافئة والراحة التامة للطفل وتجنب الأطعمة الباردة.',
        ]);
        Prescription::create([
          'medical_record_id' => $rec3->id,
          'medication_name' => 'أدول شراب أطفال (Adol Syrup)',
          'dosage' => '5 مل كل 6 ساعات عند اللزوم',
          'duration' => '5 أيام',
          'instructions' => 'رج العبوة جيداً قبل الاستعمال لقياس الجرعة بدقة',
        ]);

        // Record 4 (App 4)
        $rec4 = MedicalRecord::create([
          'appointment_id' => $app4->id,
          'patient_id' => $patients[3]->id,
          'doctor_id' => $doc4->id,
          'visit_date' => $app4->appointment_date,
          'symptoms' => 'ألم في مفصل الركبة اليمنى، صعوبة في انحناء الساق.',
          'diagnosis' => 'إجهاد عضلي خفيف مع خشونة مبكرة في مفصل الركبة.',
          'doctor_notes' => 'عمل كمادات دافئة وتجنب حمل الأوزان الثقيلة مع الراحة السريرية.',
        ]);
        Prescription::create([
          'medical_record_id' => $rec4->id,
          'medication_name' => 'فولتارين جيل (Voltaren Emulgel)',
          'dosage' => 'دهان موضعي 3 مرات يومياً',
          'duration' => '10 أيام',
          'instructions' => 'تدليك خفيف للمنطقة حتى امتصاص الجيل بالكامل',
        ]);


        // 11. Seed Invoices for Appointments
        // App 1 Invoice (Paid online)
        Invoice::create([
          'appointment_id' => $app1->id,
          'total_amount' => 150.00,
          'deposit_amount' => 50.00,
          'remaining_amount' => 100.00,
          'payment_status' => 'paid',
          'payment_method' => 'online',
          'issued_at' => Carbon::now()->subDays(3),
        ]);

        // App 2 Invoice (Paid cash)
        Invoice::create([
          'appointment_id' => $app2->id,
          'total_amount' => 120.00,
          'deposit_amount' => 50.00,
          'remaining_amount' => 70.00,
          'payment_status' => 'paid',
          'payment_method' => 'cash',
          'issued_at' => Carbon::now()->subDays(2),
        ]);

        // App 3 Invoice (Paid online)
        Invoice::create([
          'appointment_id' => $app3->id,
          'total_amount' => 100.00,
          'deposit_amount' => 50.00,
          'remaining_amount' => 50.00,
          'payment_status' => 'paid',
          'payment_method' => 'online',
          'issued_at' => Carbon::now()->subDays(1),
        ]);

        // App 4 Invoice (Unpaid remaining)
        Invoice::create([
          'appointment_id' => $app4->id,
          'total_amount' => 140.00,
          'deposit_amount' => 50.00,
          'remaining_amount' => 90.00,
          'payment_status' => 'unpaid',
          'payment_method' => 'cash',
          'issued_at' => Carbon::now()->subDays(3),
        ]);

        // App 5 Invoice (Unpaid confirmed app)
        Invoice::create([
          'appointment_id' => $app5->id,
          'total_amount' => 80.00,
          'deposit_amount' => 50.00,
          'remaining_amount' => 30.00,
          'payment_status' => 'unpaid',
          'payment_method' => 'online',
          'issued_at' => Carbon::now(),
        ]);


        // 12. Seed Wallet Transactions
        // Patient 1
        WalletTransaction::create([
          'user_id' => $patients[0]->id,
          'appointment_id' => null,
          'type' => 'deposit',
          'amount' => 500.00,
          'balance_before' => 0.00,
          'balance_after' => 500.00,
          'description' => 'شحن رصيد المحفظة الإلكترونية',
        ]);
        WalletTransaction::create([
          'user_id' => $patients[0]->id,
          'appointment_id' => $app1->id,
          'type' => 'booking_deduct',
          'amount' => 50.00,
          'balance_before' => 550.00,
          'balance_after' => 500.00,
          'description' => 'خصم تأمين حجز موعد عيادة القلب',
        ]);

        // Patient 2
        WalletTransaction::create([
          'user_id' => $patients[1]->id,
          'appointment_id' => null,
          'type' => 'deposit',
          'amount' => 600.00,
          'balance_before' => 0.00,
          'balance_after' => 600.00,
          'description' => 'شحن رصيد المحفظة الإلكترونية',
        ]);
        WalletTransaction::create([
          'user_id' => $patients[1]->id,
          'appointment_id' => $app2->id,
          'type' => 'booking_deduct',
          'amount' => 50.00,
          'balance_before' => 650.00,
          'balance_after' => 600.00,
          'description' => 'خصم تأمين حجز موعد عيادة الجلدية',
        ]);

        // Patient 11 (Cancelled booking refund)
        WalletTransaction::create([
          'user_id' => $patients[10]->id,
          'appointment_id' => null,
          'type' => 'deposit',
          'amount' => 700.00,
          'balance_before' => 0.00,
          'balance_after' => 700.00,
          'description' => 'شحن رصيد المحفظة الإلكترونية',
        ]);
        WalletTransaction::create([
          'user_id' => $patients[10]->id,
          'appointment_id' => $app11->id,
          'type' => 'booking_deduct',
          'amount' => 50.00,
          'balance_before' => 1050.00,
          'balance_after' => 1000.00,
          'description' => 'خصم تأمين حجز موعد',
        ]);
        WalletTransaction::create([
          'user_id' => $patients[10]->id,
          'appointment_id' => $app11->id,
          'type' => 'refund_full',
          'amount' => 50.00,
          'balance_before' => 1000.00,
          'balance_after' => 1050.00,
          'description' => 'استرجاع كامل عربون الحجز للإلغاء المسبق',
        ]);
    }
}

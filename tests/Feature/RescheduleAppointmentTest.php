<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\DoctorDetail;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Models\Setting;
use Carbon\Carbon;

class RescheduleAppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;
    protected User $doctorUser;
    protected DoctorDetail $doctor;
    protected User $patientA;
    protected User $patientB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Settings
        Setting::create(['key' => 'cancellation_hours', 'value' => '24', 'description' => 'Min hours']);

        // 2. Staff & Doctor
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->receptionist = User::factory()->create(['role' => 'receptionist']);

        $this->doctorUser = User::factory()->create(['role' => 'doctor']);
        $this->doctor = DoctorDetail::create([
            'user_id' => $this->doctorUser->id,
            'specialization' => 'General Medicine',
            'consultation_fee' => 100.00,
        ]);

        // Weekly schedule: Monday 09:00 - 14:00 (20 min slots)
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'duration_per_patient' => 20,
        ]);

        // 3. Patients
        $this->patientA = User::factory()->create(['role' => 'patient', 'wallet_balance' => 500.00]);
        $this->patientB = User::factory()->create(['role' => 'patient', 'wallet_balance' => 500.00]);
    }

    public function test_patient_can_reschedule_appointment_more_than_24h_in_advance()
    {
        // Future Monday (at least 3 days out)
        $futureMonday = Carbon::now()->next(Carbon::MONDAY);
        if ($futureMonday->diffInHours(Carbon::now()) < 48) {
            $futureMonday->addWeek();
        }
        $dateStr = $futureMonday->format('Y-m-d');

        $appointment = Appointment::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'consultation_fee' => 100.00,
            'appointment_date' => $dateStr,
            'appointment_time' => '09:00:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->patientA, 'sanctum')
            ->patchJson("/api/appointments/{$appointment->id}/reschedule", [
                'appointment_date' => $dateStr,
                'appointment_time' => '09:20',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Appointment rescheduled successfully');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $dateStr,
            'appointment_time' => '09:20',
        ]);
    }

    public function test_patient_cannot_reschedule_appointment_within_24h()
    {
        // Appointment in 2 hours
        $today = Carbon::now();
        $dateStr = $today->format('Y-m-d');

        // Create schedule for today if needed
        $dayOfWeek = strtolower(date('l'));
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => '00:00:00',
            'end_time' => '23:59:00',
            'duration_per_patient' => 20,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'consultation_fee' => 100.00,
            'appointment_date' => $dateStr,
            'appointment_time' => Carbon::now()->addHours(2)->format('H:00'),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->patientA, 'sanctum')
            ->patchJson("/api/appointments/{$appointment->id}/reschedule", [
                'appointment_date' => $dateStr,
                'appointment_time' => Carbon::now()->addHours(3)->format('H:00'),
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Cannot reschedule less than 24 hours before the appointment. Please cancel or contact reception.');
    }

    public function test_patient_cannot_reschedule_another_patients_appointment()
    {
        $futureMonday = Carbon::now()->next(Carbon::MONDAY)->addWeek();
        $dateStr = $futureMonday->format('Y-m-d');

        $appointment = Appointment::create([
            'patient_id' => $this->patientB->id,
            'doctor_id' => $this->doctor->id,
            'consultation_fee' => 100.00,
            'appointment_date' => $dateStr,
            'appointment_time' => '09:00:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->patientA, 'sanctum')
            ->patchJson("/api/appointments/{$appointment->id}/reschedule", [
                'appointment_date' => $dateStr,
                'appointment_time' => '09:20',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_override_24h_restriction()
    {
        $today = Carbon::now();
        $dateStr = $today->format('Y-m-d');

        $dayOfWeek = strtolower(date('l'));
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => '00:00:00',
            'end_time' => '23:59:00',
            'duration_per_patient' => 20,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'consultation_fee' => 100.00,
            'appointment_date' => $dateStr,
            'appointment_time' => Carbon::now()->addHours(2)->format('H:00'),
            'status' => 'pending',
        ]);

        $newTime = Carbon::now()->addHours(4)->format('H:00');

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/appointments/{$appointment->id}/reschedule", [
                'appointment_date' => $dateStr,
                'appointment_time' => $newTime,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Appointment rescheduled successfully');
    }

    public function test_receptionist_can_reschedule_confirmed_and_within_24h_appointment()
    {
        $today = Carbon::now();
        $dateStr = $today->format('Y-m-d');

        $dayOfWeek = strtolower(date('l'));
        DoctorSchedule::firstOrCreate([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => $dayOfWeek,
        ], [
            'start_time' => '00:00:00',
            'end_time' => '23:59:00',
            'duration_per_patient' => 20,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'consultation_fee' => 100.00,
            'appointment_date' => $dateStr,
            'appointment_time' => Carbon::now()->addHours(2)->format('H:00'),
            'status' => 'confirmed',
        ]);

        $newTime = Carbon::now()->addHours(4)->format('H:00');

        $response = $this->actingAs($this->receptionist, 'sanctum')
            ->patchJson("/api/appointments/{$appointment->id}/reschedule", [
                'appointment_date' => $dateStr,
                'appointment_time' => $newTime,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Appointment rescheduled successfully');
    }
}

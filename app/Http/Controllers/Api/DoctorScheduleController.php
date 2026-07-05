<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    // عرض جدول دكتور محدد (للجميع)
    public function index($doctorId)
    {
        $doctor = DoctorDetail::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)->get();

        return response()->json($schedules);
    }

    // إضافة جدول للدكتور (الأدمن فقط)
    public function store(Request $request, $doctorId)
    {
        $doctor = DoctorDetail::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $request->validate([
            'day_of_week'          => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'           => 'required|date_format:H:i',
            'end_time'             => 'required|date_format:H:i|after:start_time',
            'duration_per_patient' => 'required|integer|min:5|max:120',
        ]);

        // التحقق أن اليوم غير موجود مسبقاً
        $exists = DoctorSchedule::where('doctor_id', $doctorId)
                                ->where('day_of_week', $request->day_of_week)
                                ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Schedule for this day already exists'
            ], 422);
        }

        $schedule = DoctorSchedule::create([
            'doctor_id'            => $doctorId,
            'day_of_week'          => $request->day_of_week,
            'start_time'           => $request->start_time,
            'end_time'             => $request->end_time,
            'duration_per_patient' => $request->duration_per_patient,
        ]);

        return response()->json([
            'message'  => 'Schedule added successfully',
            'schedule' => $schedule,
        ], 201);
    }

    // تعديل جدول (الأدمن فقط)
    public function update(Request $request, $doctorId, $scheduleId)
    {
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
                                  ->where('id', $scheduleId)
                                  ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $request->validate([
            'start_time'           => 'sometimes|date_format:H:i',
            'end_time'             => 'sometimes|date_format:H:i|after:start_time',
            'duration_per_patient' => 'sometimes|integer|min:5|max:120',
        ]);

        $schedule->update($request->only([
            'start_time',
            'end_time',
            'duration_per_patient'
        ]));

        return response()->json([
            'message'  => 'Schedule updated successfully',
            'schedule' => $schedule,
        ]);
    }

    // حذف جدول (الأدمن فقط)
    public function destroy($doctorId, $scheduleId)
    {
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
                                  ->where('id', $scheduleId)
                                  ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorDetail;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // عرض قائمة الأطباء (للجميع)
    public function index()
    {
        $doctors = DoctorDetail::with('user')->get()->map(function ($doctor) {
            return [
                'id'                  => $doctor->id,
                'user_id'             => $doctor->user_id,
                'name'                => $doctor->user->name,
                'email'               => $doctor->user->email,
                'phone'               => $doctor->user->phone,
                'profile_picture'     => $doctor->user->profile_picture,
                'profile_picture_url' => $doctor->user->profile_picture_url,
                'specialization'      => $doctor->specialization,
                'bio'                 => $doctor->bio,
                'consultation_fee'    => (float) $doctor->consultation_fee,
            ];
        });

        return response()->json($doctors);
    }

    // عرض دكتور محدد (للجميع)
    public function show($id)
    {
        $doctor = DoctorDetail::with('user')->find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json([
            'id'                  => $doctor->id,
            'user_id'             => $doctor->user_id,
            'name'                => $doctor->user->name,
            'email'               => $doctor->user->email,
            'phone'               => $doctor->user->phone,
            'profile_picture'     => $doctor->user->profile_picture,
            'profile_picture_url' => $doctor->user->profile_picture_url,
            'specialization'      => $doctor->specialization,
            'bio'                 => $doctor->bio,
            'consultation_fee'    => (float) $doctor->consultation_fee,
        ]);
    }

    // إضافة تفاصيل دكتور (الأدمن فقط)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'specialization'   => 'required|string|max:255',
            'bio'              => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        // التحقق أن المستخدم دكتور
        $user = User::find($request->user_id);
        if ($user->role !== 'doctor') {
            return response()->json([
                'message' => 'This user is not a doctor'
            ], 422);
        }

        // التحقق أن الدكتور ليس لديه تفاصيل مسبقاً
        if (DoctorDetail::where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => 'Doctor details already exist'
            ], 422);
        }

        $doctor = DoctorDetail::create([
            'user_id'          => $request->user_id,
            'specialization'   => $request->specialization,
            'bio'              => $request->bio,
            'consultation_fee' => $request->consultation_fee,
        ]);

        return response()->json([
            'message' => 'Doctor details added successfully',
            'doctor'  => $doctor,
        ], 201);
    }

    // تعديل تفاصيل دكتور (الأدمن فقط)
    public function update(Request $request, $id)
    {
        $doctor = DoctorDetail::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $request->validate([
            'specialization'   => 'sometimes|string|max:255',
            'bio'              => 'nullable|string',
            'consultation_fee' => 'sometimes|numeric|min:0',
        ]);

        $doctor->update($request->only(['specialization', 'bio', 'consultation_fee']));

        return response()->json([
            'message' => 'Doctor updated successfully',
            'doctor'  => $doctor,
        ]);
    }


    // حذف دكتور (الأدمن فقط)
    public function destroy($id)
    {
        $doctor = DoctorDetail::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully',
        ]);
    }
}

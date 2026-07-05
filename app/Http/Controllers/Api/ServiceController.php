<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // عرض خدمات دكتور محدد (للجميع)
    public function index($doctorId)
    {
        $doctor = DoctorDetail::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $services = Service::where('doctor_id', $doctorId)->get();

        return response()->json($services);
    }

    // إضافة خدمة للدكتور (الأدمن فقط)
    public function store(Request $request, $doctorId)
    {
        $doctor = DoctorDetail::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $request->validate([
            'service_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
        ]);

        $service = Service::create([
            'doctor_id'    => $doctorId,
            'service_name' => $request->service_name,
            'price'        => $request->price,
        ]);

        return response()->json([
            'message' => 'Service added successfully',
            'service' => $service,
        ], 201);
    }

    // تعديل خدمة (الأدمن فقط)
    public function update(Request $request, $doctorId, $serviceId)
    {
        $service = Service::where('doctor_id', $doctorId)
                          ->where('id', $serviceId)
                          ->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $request->validate([
            'service_name' => 'sometimes|string|max:255',
            'price'        => 'sometimes|numeric|min:0',
        ]);

        $service->update($request->only(['service_name', 'price']));

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    // حذف خدمة (الأدمن فقط)
    public function destroy($doctorId, $serviceId)
    {
        $service = Service::where('doctor_id', $doctorId)
                          ->where('id', $serviceId)
                          ->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }
}

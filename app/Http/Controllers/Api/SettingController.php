<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // عرض الإعدادات
    public function index()
    {
        $settings = [
            'booking_deposit'        => (float) Setting::get('booking_deposit', 50),
            'max_penalty_percentage' => (float) Setting::get('max_penalty_percentage', 25),
            'cancellation_hours'     => (int) Setting::get('cancellation_hours', 24),
        ];

        return response()->json($settings);
    }

    // تحديث الإعدادات (الأدمن فقط)
    public function update(Request $request)
    {
        $request->validate([
            'booking_deposit'        => 'sometimes|numeric|min:0',
            'max_penalty_percentage' => 'sometimes|numeric|min:0|max:100',
            'cancellation_hours'     => 'sometimes|integer|min:1',
        ]);

        if ($request->has('booking_deposit')) {
            Setting::set(
                'booking_deposit',
                $request->booking_deposit,
                'Deposit amount required at booking'
            );
        }

        if ($request->has('max_penalty_percentage')) {
            Setting::set(
                'max_penalty_percentage',
                $request->max_penalty_percentage,
                'Maximum penalty percentage for late cancellation'
            );
        }

        if ($request->has('cancellation_hours')) {
            Setting::set(
                'cancellation_hours',
                $request->cancellation_hours,
                'Hours before appointment to cancel without penalty'
            );
        }

        return response()->json([
            'message'  => 'Settings updated successfully',
            'settings' => [
                'booking_deposit'        => (float) Setting::get('booking_deposit'),
                'max_penalty_percentage' => (float) Setting::get('max_penalty_percentage'),
                'cancellation_hours'     => (int) Setting::get('cancellation_hours'),
            ],
        ]);
    }
}
